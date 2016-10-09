var tablaNotas = '';
var langFRM = langFrm ;

function initFRM(){
    tablaNotas = $('#notas').DataTable();
    tablaNotas.$('select').chosen({
       width : '100 %'
    });
    $('.fancybox-wrap #cargarNotas').on('click', 'input[type="checkbox"][name^="alumnos"]', function(){
        if($(this).is(':checked')){
            $(this).closest('tr').find("input[name*='[notas]']").not(this).val('').prop('readonly',true);
        } else {
            $(this).closest('tr').find("input[name*='[notas]']").not(this).prop('readonly',false);
        }
    });

    $('select[name="notas_length"]').on('change',function(){            
        $.fancybox.update();            
    });

    $('.fancybox-wrap #cargarNotas').on('submit',function(){
        var dataPOST = tablaNotas.$('input, select').serialize();
        $.ajax({
            url: BASE_URL + 'examenes/guardarNotaExamen',
            data: dataPOST + '&codigo='+codigo,
            type: 'POST',
            cache: false,
            dataType: 'json',
            success:function(respuesta){
                if (respuesta.codigo == 1){
                    gritter(langFRM.notas_guardadas, true);
                    $.fancybox.close();
                } else {
                    gritter(respuesta.msgerror);
                }
            }
        });
        return false;
    });
}

$('.fancybox-wrap').ready(function(){
    initFRM();    
});