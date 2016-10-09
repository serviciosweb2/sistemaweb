var lang = BASE_LANG;
var langFRM = BASE_LANG;
var temp = '';
function calcularRango(){
    clearTimeout(temp);
    temp = setTimeout(function(){
        $('select[name="nota_aprueba_parcial"]').empty();
        $('select[name="nota_aprueba_final"]').empty();
        $('select[name="nota_aprueba_parcial"]').trigger("chosen:updated");
        $('select[name="nota_aprueba_final"]').trigger("chosen:updated");
        var nota_desde = $('input[name="numero_desde"]').val();
        var nota_hasta = $('input[name="numero_hasta"]').val();
        if(nota_desde === '' || nota_hasta===''){
            gritter(lang.nota_desde_hasta);
        } else {
            for(var i = nota_desde; i <= nota_hasta; i++){
                agregarOptionParciales(i);
                agregarOptionFinales(i);
            }
            $('select[name="nota_aprueba_parcial"]').trigger("chosen:updated");
            $('select[name="nota_aprueba_final"]').trigger("chosen:updated");
        }
    },2500);
}

function agregarOptionParciales(dato){
    var option = '<option value=' + dato + '>' + dato + '</option>';
    $('select[name="nota_aprueba_parcial"]').append(option);
}

function agregarOptionFinales(dato){
    var option = '<option value=' + dato + '>' + dato + '</option>';
    $('select[name="nota_aprueba_final"]').append(option);
}

function guardarValor(element){
    var nombre=$(element).attr('name');
    var valor=$(element).val();
    var type=$(element).attr('type');
    if (!$(element).is(':checked') && type=='checkbox' && $(element).attr('name')!='ConfiguracionAlertaExamenes'){
        valor = 0;
        $(element).val(1);
    } else {
        $(element).val(0);
    }
    $.ajax({
        url: BASE_URL+"configuracion/guardarConfiguracionAcademicos",
        type: "POST",
        data: {
            valor: valor,
            nombre: nombre
        },
        dataType: "JSON",
        cache: false,
        success:function(respuesta){
            if (respuesta.codigo == 1){
                $.gritter.add({
                    title: '_OK!',
                    text: lang.validacion_ok,
                    sticky: false,
                    time: '1500',
                    class_name:'gritter-success'
                });
                nombre == 'ConfiguracionAlertaExamenes' ? tablaAlertaExamen() : '';
            } else {
                $.gritter.add({
                    title: '_Upps!',
                    text: respuesta.msgerror,
                    sticky: false,
                    time: '1500',
                    class_name: 'gritter-error'
                });
            }
        }
    });
}

function tablaAlertaExamen(){
    $('#alertaExamen');
    var objeto = '';
    var tabla = '<table class="table table-condensed">';
    tabla += '<thead>';
    tabla += '<th>';
    tabla += lang.alertar;
    tabla += '</th>';
    tabla += '<th class="text-center">';
    tabla += lang.activo;
    tabla += '</th>';
    tabla += '<tbody>';
    $.ajax({
        url: BASE_URL + "configuracion/getConfiguracionAlertaExamen",
        type: "POST",
        data: "",
        dataType: "JSON",
        cache: false,
        async: false,
        success: function(respuesta){
            objeto = respuesta;
        }
    });
    $(objeto).each(function(k,alerta){
        var selected = alerta.baja == 0 ? 'checked' : '';
        tabla += '<tr>';
        tabla += '<td class="">';
        tabla += alerta.valor + ' ' + alerta.traducido + ' ('+alerta.tipo_traduccion+')';
        tabla += '</td>';
        tabla += '<td class="text-center">';
        tabla += '<label>';
        tabla += '<input name="ConfiguracionAlertaExamenes" value="'
                + alerta.codigo + '"  class="ace ace-switch ace-switch-6" type="checkbox" onchange="guardarValor(this);" ' 
                + selected + '>';
        tabla += '<span class="lbl">';
        tabla += '</span>';
        tabla += '</label>';
        tabla += '</td>';
        tabla += '</tr>';
    });
    tabla+='</tbody></table>';
    $('#alertaExamen .table-responsive').html(tabla);
}

function ocultarCapas(element){
    var dato = $(element).val();
    if (dato == 'alfabetico'){
        $('select[name="nota_aprueba_parcial"]').empty();
        $('select[name="nota_aprueba_final"]').empty();
        $('.tipo_numerico').hide();
        $('.tipo_alfabetico').show();
        llenarSelectParcialesAlfabetico();
        llenarSelectFinalesAlfabetico();
    } else {
        $('select[name="nota_aprueba_parcial"]').empty();
        $('select[name="nota_aprueba_final"]').empty();
        $('.tipo_alfabetico').hide();
        $('.tipo_numerico').show();
        llenarSelectParcialesNumericos();
        llenarSelectFinalesNumericos();
    }
    $('select[name="nota_aprueba_parcial"]').trigger("chosen:updated");
    $('select[name="nota_aprueba_final"]').trigger("chosen:updated");
}

function llenarSelectParcialesAlfabetico(){
    var option = '';
    if(escala_notas.length > 0){
         $(escala_notas).each(function(k,valor){
            var selected = '';
            if(valor == nota_aprueba_parcial){
                selected = 'selected';
            }
            option = '<option value=' + valor + ' ' + selected + '>' + valor + '</option>';
            $('select[name="nota_aprueba_parcial"]').append(option);
        });
    } else {
        option = '<option></option>';
        $('select[name="nota_aprueba_parcial"]').append(option);
    }
    $('select[name="nota_aprueba_parcial"]').trigger("chosen:updated");
}

function llenarSelectFinalesAlfabetico(){
    var option = '';
    if (escala_notas.length > 0){
        $(escala_notas).each(function(k,valor){
            var selected = '';
            if(valor == nota_aprueba_final){
                selected = 'selected';
            }
            option = '<option value=' + valor + ' ' + selected + '>' + valor + '</option>';
            $('select[name="nota_aprueba_final"]').append(option);
        });
    } else {
        option = '<option></option>';
        $('select[name="nota_aprueba_final"]').append(option);
    }
    $('select[name="nota_aprueba_final"]').trigger("chosen:updated");
}

function llenarSelectParcialesNumericos(){
    var option = '';
    $('input[name="numero_desde"]').val(nota_desde_examen);
    $('input[name="numero_hasta"]').val(nota_hasta_examen);
    if(nota_desde_examen != '' && nota_hasta_examen != ''){
        for(var i = nota_desde_examen; i <= nota_hasta_examen; i++){
            var selected = '';
            if (i == nota_aprueba_parcial){
                selected = 'selected';
            }
            option = '<option value='+i+' '+selected+'>'+i+'</option>';
            $('select[name="nota_aprueba_parcial"]').append(option);
        }
    } else {
        option ='<option></option>';
        $('select[name="nota_aprueba_parcial"]').append(option);
    }
    $('select[name="nota_aprueba_parcial"]').trigger("chosen:updated");
 }

function llenarSelectFinalesNumericos(){
    var option = '';
    $('input[name="numero_desde"]').val(nota_desde_examen);
    $('input[name="numero_hasta"]').val(nota_hasta_examen);
    if(nota_desde_examen != '' && nota_hasta_examen != ''){
        for(var i = nota_desde_examen; i <= nota_hasta_examen; i++){
            var selected = '';
            if(i == nota_aprueba_final){
                selected = 'selected';
            }
            option = '<option value=' + i + ' ' + selected + '>' + i + '</option>';
            $('select[name="nota_aprueba_final"]').append(option);
        }
    } else {
        option ='<option></option>';
        $('select[name="nota_aprueba_final"]').append(option);
    }
    $('select[name="nota_aprueba_final"]').trigger("chosen:updated");
}

$('#tabAcademico').ready(function(){
    llenarSelectParcialesNumericos();
    llenarSelectFinalesNumericos();
    llenarSelectParcialesAlfabetico();
    llenarSelectFinalesAlfabetico();
    $('#form-field-tags').on('added', function (e, value) {
        agregarOptionParciales(value);
        agregarOptionFinales(value);
        $('select[name="nota_aprueba_parcial"]').trigger("chosen:updated");
        $('select[name="nota_aprueba_final"]').trigger("chosen:updated");
    });
    
    $('#form-field-tags').on('removed', function (e, value) {
        $('select[name="nota_aprueba_parcial"]').find('option[value="'+value+'"]').remove();
        $('select[name="nota_aprueba_final"]').find('option[value="'+value+'"]').remove();
        $('select[name="nota_aprueba_parcial"]').trigger("chosen:updated");
        $('select[name="nota_aprueba_final"]').trigger("chosen:updated");
    });
    
    var tag_input = $('#form-field-tags');
        if(!(/msie\s*(8|7|6)/.test(navigator.userAgent.toLowerCase())) ){
            tag_input.tag({
                placeholder: tag_input.attr('placeholder'),
                source: ace.variable_US_STATES
            });
        } else {
            tag_input.after('<textarea id="' + tag_input.attr('id') + '" name="' + tag_input.attr('name') + '" rows="3">' + 
                    tag_input.val() + '</textarea>').remove();
        }
    initFRM();
});

function initFRM(){
    $('#tabAcademico select').chosen({
        width: '100%',
        allow_single_deselect: true
    });

    tablaAlertaExamen();

    $('button[name="nuevaAlerta"]').on('click',function(){
        $.ajax({
            url: BASE_URL+"configuracion/frm_alertaExamen",
            type: "POST",
            data: "",
            dataType: "",
            cache: false,
            success:function(respuesta){
                $.fancybox.open(respuesta,{
                    padding: 0,
                    width: 'auto',
                    openEffect: 'none',
                    closeEffect: 'none',
                    helpers: {
                        overlay : null
                    }
                });
            }
        });
        return false;
    });
}

function guardarConfiguracionNotas(event){
    var data_post = $('#configuracion_notas').serialize();
    $.ajax({
        url: BASE_URL+'configuracion/guardarConfiguracionNotasExamen',
        data: data_post,
        type: "POST",
        dataType: "JSON",
        async: false,
        cache: false,
        success:function(respuesta){
            if (respuesta.codigo == 0){
                gritter(respuesta.msgError);
            } else {
                gritter(lang.validacion_ok,true);
            }
        }
    });
    event.preventDefault();
    return false;
}

function guardarHorasInscripcionesExamen(){
    var horas = $('#horas_cierre_inscripcion').val();
    $.ajax({
        url: BASE_URL + 'configuracion/guardarHorasCierreInscripcionExamen',
        data: 'horas=' + horas,
        type: "POST",
        dataType: "JSON",
        async: false,
        cache: false,
        success: function(respuesta){
            if (respuesta.codigo == 0){
                gritter(respuesta.msgError);
            } else {
                gritter(lang.validacion_ok,true);
            }
        }
    });
}

function agregar_modificar_salon(codigo){
    $.ajax({
        url: BASE_URL + 'horarios/frm_salones',
        type: 'POST',
        cache: false,
        data: {
            codigo: codigo
        },
        success: function(respuesta){
            $.fancybox.open(respuesta, {
                scrolling: 'auto',
                width: '50%',
                height: 'auto',
                minHeight: '300',
                maxWidth: '600',
                autoSize: false,
                autoResize: false,
                openEffect: 'none',
                closeEffect: 'none',
                padding: 1,
                helpers: {
                    overlay: null
                }
            });
        }
    });
}

function guardar_como_nos_conocio(element){
    var id_conocio = $(element).val();
    var accion = $(element).is(":checked") ? "set_como_nos_conocio" : "unset_como_nos_conocio";
    $.ajax({
        url: BASE_URL + "configuracion/" + accion,
        type: "POST",
        dataType: 'json',
        data: {
            id_conocio: id_conocio
        },
        success: function(_json){
            if (_json.error){
                gritter(_json.error, false, ' ');
            }
        }
    });
}