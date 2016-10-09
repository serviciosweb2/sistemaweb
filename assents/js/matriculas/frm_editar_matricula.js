var langFrmMatricula;
langFrmMatricula = frmLang;

$(".fancybox-wrap").ready(function() {
    init();
});

function init() {
    $('[data-rel=popover]').popover({container: 'body', html: true, trigger: 'manual'});
    $('body').not('.popover').on('click', function() {
        $('.popover').hide().fadeIn('fast').remove();
    });
   $('.chosen-select').chosen({
        width: "100%"
    });
    $('.fancybox-wrap').on('submit', '#editarMatricula', function() {        
        var cod_alumno = $('#cod_alumno').val();
        var cod_plan_academico = $('#cod_plan_academico').val();        
        var material = $('#material').val();
        var documentacion = $('#documentacion').val();        
        var observaciones = $('#ob').val();
        var medio_pago_cuotas = $("[name=medio_pago_cuotas]").val();
        $.ajax({
            url: BASE_URL + 'matriculas/guardarEdicionMatricula',
            data: {
                cod_alumno: cod_alumno,
                cod_plan_academico: cod_plan_academico,
                material: material,
                documentacion: documentacion,
                observaciones: observaciones,
                medio_pago_cuotas: medio_pago_cuotas
            },
            type: 'POST',
            dataType: 'json',
            success: function(respuesta){
                if (respuesta.codigo == '0'){
                    $.fancybox.close(true);
                    $.gritter.add({
                        title: langFrmMatricula.BIEN,
                        text: respuesta.respuesta,
                        sticky: false,
                        time: '3000',
                        class_name: 'gritter-success'
                    });
                } else {
                    $.gritter.add({
                        title: langFrmMatricula.ERROR,
                        text: respuesta.respuesta,
                        sticky: false,
                        time: '3000',
                        class_name: 'gritter-error'
                    });
                }
            }
        });
        return false;
    });
}
