$('.fancybox-wrap').ready(function(){
    $('.aImputar').hide();
    simbolo='';    
    saldo='';    
    $(".controlTabla").eq(0).animate({ scrollTop: $('#tablaImputaciones').height() }, 1000);
    var validacion='';
    $('.errorSaldo').hide();
    function getSaldo(){
        $.ajax({
            url:BASE_URL+'cobros/getSaldo',
            type:'POST',
            async:false,
            data:'codigo='+codigo,
            success:function(respuesta){
                saldo=respuesta;
                var conPunto=respuesta.replace(BASE_SEPARADOR,".");
                var valorFormatiado=conPunto.replace(BASE_SIMBOLO,"");
                if(parseFloat(valorFormatiado).toFixed(BASE_DECIMALES)<=0){
                    $('.aImputar').hide();
                } else {
                    $('.aImputar').show();
                    $('#saldoRestante').html(respuesta);
                }
            }
        });
        $.fancybox.update();
    }    
    getSaldo();
    function totalImputaciones(){        
        $.ajax({
            url: BASE_URL + 'cobros/getTotalImputaciones',
            type: "POST",
            data: "cod_cobro=" + codigo,
            dataType:"JSON",
            cache:false,
            success:function(respuesta){
                $('p[name="totalImputaciones"]').html(respuesta[0].totImputaciones);
            }
        });
    }
    totalImputaciones();
    function actualizarSaldo(totalImputacion,x){
        var saldoActualizado=parseFloat(saldo-totalImputacion).toFixed(2);
        simbolo=$('.fancybox-wrap .page-header small').text().slice(0,1);
        var saldoVista=saldoActualizado < 0 ? simbolo+'0.00' : simbolo+saldoActualizado;
        $('.fancybox-wrap .page-header small').text(saldoVista);
        x==true ? saldo=saldoActualizado : saldo ;
    }
    
    function resetImput(input){
        var saldoInicial=$(input).parent().parent().find('input[name="valorImputar[]"]').attr('data-importe');
        $(input).parent().parent().removeClass('has-error');
        $(input).parent().parent().find('input[name="valorImputar[]"]').val(saldoInicial);
    }
    
    function validar(){
        validacion=true;
        $('.errorSaldo').hide();
        sum = 0;
        $('input[name="codigoImputar[]"]:checked').each(function(){
            var x=$(this).parent().parent().find('input[name="valorImputar[]"]').val();
            sum+=parseFloat(x) || 0;
            if(!isNaN(x) && parseFloat(x) > 0){
                $(this).parent().parent().removeClass('has-error');
                var importe=$(this).parent().parent().find('input[name="valorImputar[]"]').attr('data-importe');
                if(parseFloat(importe) < parseFloat(x)){
                    $(this).parent().parent().addClass('has-error');
                    validacion=false;
                    if($('.top-right').is(':visible')){
               
                    } else {
                        $('.top-right').notify({
                            type:'danger',
                            message: {
                                text: 'La imputacion supera  el limite del saldo a cobrar!'
                            }
                        }).show();
                    }
                } else {
                    $(this).parent().parent().removeClass('has-error');
                }
            } else {
                $(this).parent().parent().addClass('has-error');
                $('.top-right').notify({
                    type:'danger',
                    message: { text: 'Solo numeros positivos' }
                }).show();
            }
        });
        
        actualizarSaldo(sum,false);
        if(sum >saldo){
            $('.errorSaldo').fadeIn();
            validacion=false;
        }
        sum==0 ? validacion='' : validacion;
    }
    tablaVisible=$('.fancybox-wrap #tablaImputaciones').is(':visible');
    
    function actualizarTablas(){
        $.ajax({
            url:BASE_URL+'cobros/getImputacionesCobro',
            data:'codigo='+codigo,
            type:'POST',
            cache:false,
            dataType:'JSON',
            success:function(respuesta){
                console.log('RESPUESTA TABLA :');
                console.log(respuesta);
                var tr='';
                if(respuesta!=""){
                    $(respuesta).each(function(){
                        $('#imputaciones_cobro').hide();
                        tr += '<tr><td>' + this.descripcion + '</td><td>' + this.valorImputacion + '</td></tr>';
                    });
                    var nuevaTabla='<table id="tablaImputaciones" class="table table-striped table-bordered"><thead><th>Descripción</th><th>Valor</th></thead><tbody>'+tr+'</tbody></table>';
                    $('.controlTabla').eq(0).find("#tablaImputaciones").remove();
                    $('.controlTabla').eq(0).append(nuevaTabla);
                    $.fancybox.update();
                    $(".controlTabla").eq(0).animate({ scrollTop: $('#tablaImputaciones').height() }, 1000);
               }
            }
        });
        
        $('#aImputar tbody tr').find('input[name="valorImputar[]"]:enabled').each(function(){
            var input=$(this);
            var importe=input.attr('data-importe');
            var val=input.val();
            var a=val.replace(BASE_SEPARADOR,".");
            var c=importe.replace(BASE_SEPARADOR,".");
            var nuevoSaldo=  parseFloat(c-a).toFixed(BASE_DECIMALES);
            var b = nuevoSaldo.replace(".",BASE_SEPARADOR);
            input.attr('data-importe',b);
            if(nuevoSaldo==0){
                $(this).parent().parent().parent().remove();
            }else{
                $(this).parent().parent().prev().text(BASE_SIMBOLO+b);
            }
        });
    }
    
    function validarEntrada(elemento){
        var resultado=[];
        resultado.codigo=1;
        resultado.msg='';
        var valorEntrada=$(elemento).val();
        var pattern= new RegExp("^([0-9]{0,10})\\"+BASE_SEPARADOR+"?([0-9]{1,"+BASE_DECIMALES+"})$");
        function msjError(){
            var cadena='Formato esperado '+'XX'+BASE_SEPARADOR;
            for  ( var i=0; i < 2 ; i++) {
                cadena+='X';
            }
            return cadena;
        }

        if(pattern.test(valorEntrada)){
            $(elemento).closest('.input-group').removeClass('has-error');
        } else {
            $(elemento).closest('.input-group').addClass('has-error');
            $.gritter.add({
                title: 'Upps!',
                text: msjError(),
                sticky: false,
                time: '3000',
                class_name:'gritter-error'
            });
        }
        return resultado;
    }
    
    $('.fancybox-wrap').on('click','button[name="calcularSaldo"]',function(){
        var dataPOST=$('#imputaciones').serialize();
        if(dataPOST==''){
            dataPOST='codigoImputar%5B%5D=&valorImputar%5B%5D=';
        }
        
        $.ajax({
            url: BASE_URL+'cobros/actualizarSaldo',
            type: "POST",
            data: dataPOST + '&cod_cobro=' + codigo,
            dataType:"JSON",
            cache:false,
            success:function(respuesta){
                if(respuesta.codigo==1){
                    $('input[name="saldoDisponible"]').val(respuesta.total);
                    $('#saldoRestante').html(respuesta.saldo);
                } else {
                    $.gritter.add({
                        title: 'Upss!',
                        text: respuesta.msgerror,
                        sticky: false,
                        time: '3000',
                        class_name:'gritter-error'
                    });
                }
            }
        });
        return false;
    });
    
    $('.fancybox-wrap').on('click','input[name="codigoImputar[]"]',function(){
        var estado=$(this).is(':checked')? false : true;
        var elemento=$(this).parent().parent().find('input[name="valorImputar[]"]');
        estado == false ? validarEntrada(elemento): resetImput(this);
        $(this).parent().parent().find('input[name="valorImputar[]"]').prop('disabled',estado);
    });
  
    $('.fancybox-wrap').on('keyup','input[name="valorImputar[]"]',function(){
        validarEntrada(this);
    });
    
    $('.fancybox-wrap').on('click','button[type="submit"]',function(){
        var dataPOST=$('#imputaciones').serialize() + '&codigo=' + codigo;
        $.ajax({
            url:BASE_URL+'cobros/guardarImputaciones',
            data:dataPOST,
            type:'POST',
            cache:false,
            dataType:'JSON',
            success:function(respuesta){
                console.log(respuesta);
                if(respuesta.codigo==1){
                    $.gritter.add({
                        title: 'OK!',
                        text: 'Guardado Correctamente',
                        sticky: false,
                        time: '3000',
                        class_name:'gritter-success'
                    });
                    actualizarTablas();
                    totalImputaciones();
                    oTable.fnDraw();
                } else {
                    $.gritter.add({
                        title: 'Uppss!',
                        text: respuesta.msgerror,
                        sticky: false,
                        time: '3000',
                        class_name:'gritter-error'
                    });
                };
            }
        });
    });
});