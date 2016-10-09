
function generarNombre(element){    
   $.ajax({
        url: BASE_URL+"comisiones/getNombre",
        type: "POST",
        data: {codigo_comision: codigo , nombre: $(element).val()},
        dataType:"JSON",
        cache:false,
        success:function(respuesta){

        }
    });    
};

function validarSelect(){    
    var retorno=true;    
    var a=$('select[name="cod_plan_academico"]').val();    
    var b=$('select[name="periodos"]').val();    
    var c=$('select[name="ciclo"]').val();
    if(!a || !b || !c){        
        retorno=false;
    }
    return retorno;
}

function getPrefijo(){
   $.ajax({
        url: BASE_URL+"comisiones/getPrefijo",
        type: "POST",
        data:  {
            modalidad: $("[name=modalidad]").val(),
            cod_comision: $("[name=cod_comision]").val(),
            cod_plan_academico: $("[name=cod_plan_academico]").val(),
            periodos: $("[name=periodos]").val(),
            comision_descipcion: $("[name=comision_descipcion]").val(),
            ciclo: $("[name=ciclo]").val()            
        },
        dataType: "JSON",
        cache:false,
        success:function(respuesta){
            $('#prefijo').html(respuesta);
            var nombre = $("[name=comision_descipcion]").val();
            if (nombre.length + respuesta.length + 1 >  20){
                $("[name=comision_descipcion]").val("");
            }
        }
    });    
}

$('.fancybox-wrap').ready(function(){
    // modificacion franco ticket 5149-> 
    $(".chosen-control").chosen({ width: "100%"});     
    // <-modificacion franco ticket 5149
    $('#errores').hide();
    $('select[name="cod_plan_academico"]').on('change', function(){    
    var select_plan_academico = $(this);
        $.ajax({
            url:BASE_URL+'comisiones/getPeriodos',
            type:'POST',
            data:"codigo=" + select_plan_academico.val() ,
            dataType:'json',
            async: false,
            cache:false,
            success:function(respuesta){                   
                $('select[name="periodos"]').empty();
                $('select[name="periodos"]').append('<option value=""></option>');
                $(respuesta).each(function(key, periodo){
                    $(periodo.modalidad).each(function(j,modalidad){
                        var nombrePeriodo = modalidad.nombre_periodo+'['+modalidad.modalidad+']';
                        $('select[name="periodos"]').append('<option value="' + modalidad.cod_tipo_periodo + '" data-modalidad="'+modalidad.modalidad+'">' + nombrePeriodo + '</option>');
                    });
                });
                if(respuesta.length == 1){
                    $('select[name="periodos"]').find('option').prop('selected',true);
                    $('select[name="periodos"]').trigger('change');
                }
                $('select[name="periodos"]').trigger("chosen:updated");
            }
        });
    });

   $('select[name="periodos"]').on('change', function(){
        var cod_tipo_periodo = $(this).val();
        var cod_plan_academico = $('#cod_plan_academico').val();
        var modalidad =  $(this).find('option:selected').attr('data-modalidad');
        $('input[name="modalidad"]').val(modalidad);
        data = {
            'cod_tipo_periodo':cod_tipo_periodo,
            'cod_plan_academico':cod_plan_academico,
            'modalidad': modalidad
        };
        $.ajax({
            url: BASE_URL + 'comisiones/getCiclos',
            type: 'POST',
            data: data,
            dataType: 'json',
            cache: false,
            success: function(respuesta){
                var option = '';
                $(respuesta).each(function(key,valor){
                    option +='<option value='+valor.codigo+'>'+valor.ciclo_lectivo+'<option>';
                });
                $('#ciclo_lectivo').html(option);
                $('#ciclo_lectivo').trigger("chosen:updated");
                getPrefijo();
            }
        });
    });

    $('button[type="submit"]').click(function(){
        $('input[name="accion"]').val($(this).val());
    });

    $('.fancybox-wrap').on('submit','#nuevaComision',function(){
        $.ajax({
            url: BASE_URL + 'comisiones/guardar',
            type:'POST',
            data: {
                modalidad: $("[name=modalidad]").val(),
                cod_comision: $("[name=cod_comision]").val(),
                cod_plan_academico: $("[name=cod_plan_academico]").val(),
                periodos: $("[name=periodos]").val(),
                comision_descipcion: $("[name=comision_descipcion]").val(),
                ciclo: $("[name=ciclo]").val(),
                prefijo: $("#prefijo").html()
            },
            dataType:'json',
            cache:false,
            success:function(respuesta){        
                if(respuesta.codigo==='0'){
                    $("#errores").html(respuesta.respuesta).fadeIn();                
                    $.fancybox.update();
                } else {                
                    $.gritter.add({
                        title: lang.BIEN,
                        text: lang.COMISION_GUARDADA ,
                        sticky: false,
                        time: '3000',
                        class_name: 'gritter-success'
                    });
                    $.fancybox.close();
                }
            }
        });
        return false;
    });

    $('.fancybox-wrap').on('change','select',function(){
        $('#prefijo').html('<br>');
        if(validarSelect()){
            getPrefijo();
        };
    });
});

function verificar_caracteres(){
    var prefijo = $("#prefijo").html();
    var nombre = $("[name=comision_descipcion]").val();
    console.log(nombre.length + prefijo.length + 1 );
    return nombre.length + prefijo.length + 1 < 30;    
}