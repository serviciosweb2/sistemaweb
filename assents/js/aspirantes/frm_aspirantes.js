tableTelefonos='';
var n=0;
var telefonosBaja=[];
var frm_aspirantes = '';

function tablaTelefonosAspirante(){
    tableTelefonos = $('#tableTelefonos').DataTable();
    $('#tableTelefonos_length').html('<button type="button" name="nuevo"  id="" class="btn btn-primary" onclick="addTelefono(\'\')">'+langFrm.nuevo_tel+'</button>').parent().addClass('no-padding');
}

function selectTipo(telefono){
    var retorno='<select name="telefonos['+n+'][tipo_telefono]"><option></option>';
    $(_tipoTelefonos).each(function(k,tipo){
        var selected='';
        if( telefono.tipo_telefono == tipo.id){
            selected='selected';
        }
        retorno+='<option value="'+tipo.id+'"  '+selected+'>'+tipo.nombre+'</option>';
    });    
    retorno+='</select>';    
    return retorno;
}

function selectEmpresa(telefono){
    var retorno='<select name="telefonos['+n+'][empresa]"><option></option>';
    $(_empresasTel).each(function(k,empresa){
        var selected='';
        if(empresa.codigo == telefono.empresa){
            selected ='selected';
        }
        retorno+='<option value="'+empresa.codigo+'"  '+selected+'>'+empresa.nombre+'</option>';
    });    
    retorno+='</select>';    
    return retorno;
}

function actualizarTelDefault(){    
    $.each(tableTelefonos.$('.tdefault'),function(k,elemento){        
        if($(elemento).val()==1){            
            var cod_area=$(elemento).closest('tr').find('input[name$="[cod_area]"]').val();    
            var numero=$(elemento).closest('tr').find('input[name$="[numero]"]').val();    
            $('span.file-name').attr('data-title',cod_area+' '+numero);                       
        }        
    });    
};

function setTelDefault(elemento){    
    tableTelefonos.$('.tdefault').val(0);    
    $(elemento).closest('td').find('.tdefault').val(1);   
}

function addTelefono(telefono){
    if(telefono==''){       
        telefono={           
            "baja": "0",
            "cod_area": "",
            "numero": "",
            "codigo": "-1",
            "default": "0",
            "empresa": "",
            "tipo_telefono": "",
            "pais": ""
        };
    }
    var telDefault='';
    var primerTel= tableTelefonos.$('input[name="telefonoDefault"]').length;
    if(primerTel==0 || telefono.default==1 ){
        telDefault='checked';
        telefono.default=1;
    }
    
    tableTelefonos.row.add([               
        "<input class='form-control' name='telefonos["+n+"][cod_area]'  value='"+telefono.cod_area+"'><input type='hidden' value='"+telefono.codigo+"' class='form-control' name='telefonos["+n+"][codigo]'>",
        "<input class='form-control' name='telefonos["+n+"][numero]' value='"+telefono.numero+"'>",
        selectEmpresa(telefono),
        selectTipo(telefono),
        '<div class="radio"><label><input name="telefonoDefault" type="radio" class="ace" '+telDefault+' value="'+telefono.codigo+'" onclick="setTelDefault(this);"><span class="lbl"></span></label></div><input type="hidden" class="tdefault" name="telefonos['+n+'][default]" value="'+telefono.default+'">',
        "<a href='javascript:void(0)' data-codigo='"+telefono.codigo+"' name='telefonos["+n+"][baja]' onclick='delteTelefono(this);'><i class='icon-trash'></i></a>",
        ''
    ]).draw();

    if(telDefault=='checked'){
        $('span.file-name').attr('data-title',telefono.cod_area+' '+telefono.numero);
    }    
    
    $("select[name^='telefonos["+n+"]']").chosen({        
        width:'100%'        
    });
    n++;    
    return false;    
}

function delteTelefono(elemento){
    if($(elemento).closest('tr').find('input[name="telefonoDefault"]').is(':checked') ){
        gritter(langFrm.no_se_puede_eliminar_un_telefono_default + ". " + langFrm.cambielo_e_intente_nuevamente, false, langFrm.ERROR);
    } else {
        if($(elemento).attr('data-codigo')!=-1){
            telefonosBaja.push($(elemento).attr('data-codigo'));
        }
       tableTelefonos.rows( $(elemento).closest('tr') ).remove().draw();
    }
}

$('.fancybox-wrap').ready(function(){
    telInputAspirante = $('input[name="telefono_aspirante"]');
    var timerAspirantes='';
    telInputAspirante.intlTelInput({
        utilsScript: BASE_URL+'assents/js/librerias/tel-master/utils.js',
        nationalMode: true,
        onlyCountries: ['ar', 'br', 'uy', 'py', 've', 'bo', 'cl', 'co', 'pa', 'us'],
        defaultCountry: paises[BASE_PAIS]
    });
    telInputAspirante.on('keyup',function(){        
        clearTimeout(timerAspirantes);         
        if ($.trim(telInputAspirante.val())){
            if (paises[BASE_PAIS] == "bo" || telInputAspirante.intlTelInput("isValidNumber")){
                telInputAspirante.removeClass('inputError');
            } else {
                timerAspirantes = setTimeout(function(){
                    telInputAspirante.addClass('inputError');                 
                },1000);
            }
        }
    });
    
    setTimeout(function(){
        $('input[name="telefono_aspirante"]').attr('placeholder',"");
    },300);
    
    $('input[name="telefono_aspirante"]').intlTelInput("setNumber",numeroDefault);  
    init();
});



function eliminar_curso(obj){
    var $div = obj.closest('[id^="opciones_curso_aspirante"]');
    var id = parseInt( $div.prop("id").match(/\d+/g), 10 );
    $('#opciones_curso_aspirante'+id).remove();
}

function init(){
    $('#turnos').trigger("chosen:updated");
    tablaTelefonosAspirante();
    $(_tel).each(function(k,obj){    
        addTelefono(obj);    
    });
    
    var campo = $('.opciones_curso_aspirante').last();//.html();
    campo.find('select').parent().find('div').remove();
    //campo.find('select option:selected').remove();
    //campo.find('select option:selected').remove();
    campo = campo.html();
    //alert(campo);
    
    $('.fancybox-wrap input').addClass('input-sm');
    $('.modal-content select').chosen({
        width:'100%'
    });
    
    $('.modal-content .chosen-results').css('max-height','100px');
    $('.fancybox-wrap input[name="fechanaci"]').datepicker({
        yearRange: "1920:2014",
        changeMonth: true,
        changeYear: true
    });

    //$('.fancybox-wrap').on('change', 'select', function () {
    $('#prov_muni').change(function () {
        var select = $(this).attr('name');
        var provincia = $(this).val();
        switch (select) {
        case 'prov_muni':
            $.ajax({
                url: BASE_URL + 'aspirantes/getlocalidades',
                data: 'provincia=' + provincia,
                type: 'POST',
                cache: false,
                dataType: 'json',
                success: function (respuesta) {
                    var tipo = eval(respuesta);
                    $('select[name="cod_localidad"]').empty();
                    $(respuesta).each(function (index, value) {
                        $('select[name="cod_localidad"]').append('<option value="' + value['id'] + '">' + value['nombre'] + '</option>');
                    });
                    $('select[name="cod_localidad"]').trigger("chosen:updated");
                }
            });
            break;
        }
    });

    var accion = '';    
    $('.fancybox-wrap').on('click', '.submit', function (){
        accion = $(this).attr('name');       
        $('input[name="accion"]').val(accion);
        $('.fancybox-wrap #nuevo_aspirante').submit();           
        return false;        
    });
    
   
    
    //siwakawa
  
    /*$("#nuevo_aspirante").find("select").change(function () {
        var next = 0;
        var obj = $(this);
        $("#nuevo_aspirante").find("select").each(function () {
            alert("hola");
        });
    });*/
    

    $('#agregar_curso').unbind('click').click(function(){
        var $div = $('[id^="opciones_curso_aspirante"]:last');
        var num = parseInt( $div.prop("id").match(/\d+/g), 10 ) +1;
	var $klon = $div.clone().prop('id', 'opciones_curso_aspirante'+num );
        
        $klon.html(campo);
        $klon.find("option").prop("selected", false);
        $('#opciones_cursos_aspirante').append($klon);
        $('.fancybox-wrap input').addClass('input-sm');
        $('.modal-content select').chosen({
            width:'100%'
        });
        
        $('.eliminar_curso_interes').click(function(e){
            e.preventDefault();
            eliminar_curso($(this));
        });
        /*$('.fancybox-inner').css('height','auto');*/
    });
    
    $('.eliminar_curso_interes').click(function(e){
        e.preventDefault();
        eliminar_curso($(this));
    }); 
    
    $('.fancybox-wrap').on('submit', '#nuevo_aspirante', function (e){

        //telefonos[0][tipo_telefono]
        if($('input[name="telefono_aspirante"]').val() == ''){
            gritter(respuesta.msgerror, false, langFrm.ERROR);
        }

        var telDefaultInvalido = $('.fancybox-wrap .inputError').serializeArray();
        if(telDefaultInvalido.length > 0){
            $.gritter.add({
                text: langFrm.tel_default_invalido,
                sticky: false,
                time: '3000',
                class_name:'gritter-error'
            });            
            return false;
        }
/* Comentado
        var telefonos =  limpiarTelefono($('#id_tipo_telefono').val(),$('input[name="telefono_aspirante"]').val());       
        if($('input[name="telefono_aspirante"]').val() == ''){
            var objTel = {};
        } else {
            var telefonos =  limpiarTelefono($('#id_tipo_telefono').val(),$('input[name="telefono_aspirante"]').val());
            var objTel = {
               'tipo_telefono': $('#id_tipo_telefono').val(),
               'cod_area': telefonos[0],
               'numero': telefonos[1]
            };
        }
*/
        $('#accion').attr('value', accion);

        var disabledFields = $('#nuevo_aspirante').find(':input:disabled').removeAttr('disabled');
        var datos = $("#nuevo_aspirante :input")/*.filter(function() {
            var nombre = $(this).attr('name');   
            return (nombre !== 'cursos_interes' && nombre !== 'modalidad');
        })*/.serialize();
        disabledFields.attr('disabled','disabled');
        
        var cursos = [];
        $("#nuevo_aspirante :input[name='cursos_interes']").each(function(){
            if($(this).val() !== '')
                cursos.push($(this).val());
        });

//telefonos[0][tipo_telefono]
        if($('input[name="telefono_aspirante"]').val() == ''){
            gritter(respuesta.msgerror, false, langFrm.ERROR);
        }
        //var cursos_interes = "&cursos_interes = ["+cursos+"]";
        //var telefonos =  "&telefonos="+JSON.stringify(objTel);
        //datos  += cursos_interes + telefonos;
        $.ajax({
            url: BASE_URL + 'aspirantes/guardar ',
            type: 'POST',
            data: datos,
            cache: false,
            dataType: 'json',
            error: function (respuesta){
            },
            success: function (respuesta){                
                var codigo = String(respuesta.codigo);
                switch (codigo) {
                    case '0':
                        gritter(respuesta.msgerror, false, langFrm.ERROR);
                        break;

                    case '1':
                        oTable.fnDraw(); 
                        var cod_aspirante = respuesta.custom.cod_aspirante;                
                        if (respuesta.accion == 'guardarYpresupuestar') {                        
                            gritter(langFrm.validacion_ok, true, langFrm.BIEN);
                            setTimeout(function () {
                            $.fancybox.close(true);                         
                            $.ajax({
                                url: BASE_URL + 'aspirantes/presupuestar_aspirante',
                                type: 'POST',
                                data: 'codigo=' + cod_aspirante,
                                cache: false,
                                success: function (respuesta){                                    
                                    $.fancybox.open(respuesta, {
                                        scrolling: 'auto',
                                        width: 'auto',
                                        height: 'auto',
                                        autoSize: false,
                                        padding: 1
                                    });
                                }
                            });
                        }, 1200);
                    } else {                        
                        gritter(langFrm.validacion_ok, true, langFrm.BIEN);
                        setTimeout(function () {
                            $.fancybox.close(true);
                        }, 1200);
                    }
                    break;
                }
            }
        });
        return false;
    });
    
    /*$('.chosen-single').change(function(){
       alert($(this).closest('.opciones_curso_aspirante').find('option:selected').html());
    });*/
    
    /*siwkawa :: Quedo obsoleta porque ahora se requiere q independientemnte del curso siempre aparezcan modalidades normal e intensiva
    $('select').change(function(){
        if($(this).attr('name') != 'cursos_interes[]'){return false}
        var datos = "cod_curso="+$(this).val();
        var select = $(this).closest('.opciones_curso_aspirante').find('select:last');
        $.ajax({
            url: BASE_URL + 'aspirantes/getmodalidades ',
            type: 'POST',
            data: datos,
            cache: false,
            //dataType: 'json',
            success: function (respuesta){
                if(respuesta != "[]"){
                    select.empty();
                    html = '';
                    $.each(JSON.parse(respuesta), function(idx, obj) {
                        html += '<option value="'+obj.modalidad +'"> '+obj.nombre +' </option>';
                    });
                    select.html(html);
                    select.trigger("chosen:updated");
                }
            }
        });
       var select = $(this).closest('.opciones_curso_aspirante').find('select:last');
       select.empty();
       var datos = "cod_curso="+$(this).val();
    });*/
}
