var max_cupo_examen = 150;

function abrirCalendario(element){
    $(element).find('input[name="fecha"]').focus();
};

function initFRM(){
    $('.fancybox-wrap select').chosen({width:"100%"});
    $('.alert-danger').hide();
    var day = new Date();
    $('.inputHora').timepicker({
        minuteStep: 1,
        showSeconds: true,
        showMeridian: false
                }).next().on(ace.click_event, function(){
                        $(this).prev().focus();
                });

    if (tieneNotasCargadas){
        $('input[name="fecha"]').datepicker({
            autoclose: true,
            maxDate: new Date(day.getUTCFullYear(), day.getUTCMonth(), day.getUTCDate())
        });
    } else {
        $('input[name="fecha"]').datepicker({
            autoclose: true
        });
    }

    var containerCurso=$('select[name="Curso"]').parent().parent();
    var containerComision=$('select[name="Comision"]').parent().parent();
    $('select[name="tipoExamen"]').on('change',function(){
        var valor=$(this).val();
    });

    $('.fancybox-wrap').on('click','button[name="guardar"]',function(){
        $('#examen').submit();
        return false;
    });
    $('.fancybox-wrap').on('change','select[name="profesores[]"]',function(){
        $.fancybox.update();
    });

    $('.fancybox-wrap').on('submit','#examen',function(){
        var dataPOST = $(this).serialize();
        var cupo = $("[name=cupo]").val();
        var validacion = "";
        
        if (cupo > max_cupo_examen){
            var texto = langFRM.solo_se_pueden_inscribir_hasta_un_maximo_de__alumnos_por_examen;
            texto = texto.replace(/###/g, max_cupo_examen);
            validacion += texto + "<br>";            
        } // Se pueden hacer validaciones del lado del cliente aca

        if (validacion != ''){
            gritter(validacion, false, '');
        } else {
            $.ajax({
                url:BASE_URL+'examenes/guardarExamen',
                data:dataPOST,
                type:'POST',
                dataType:'JSON',
                cache:false,
                success:function(respuesta){
                    if(respuesta.codigo==0){
                        $.gritter.add({
                            text: respuesta.msgerror,
                            image: '',
                            sticky: false,
                            time: '3000',
                            class_name:'gritter-error'
                        });
                    } else {
                        $.gritter.add({
                            text: langFRM.validacion_ok,
                            image: '',
                            sticky: false,
                            time: '3000',
                            class_name:'gritter-success'
                        });
                        $.fancybox.close();
                        tablaFinales.refresh();
                    }
                }
            });
        }
        return false;
    });    
}

var langFRM = langFrm;
$('.fancybox-wrap').ready(function(){
    initFRM();
});
