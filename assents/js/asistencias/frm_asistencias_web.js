var langFRM = langFrm;
var aTable = null;
$('.fancybox-wrap').ready(function(){
   initFRM()
}); 

function initFRM(){
    aTable = $('#asistencias_web').dataTable({
        aoColumnDefs: [
            { 'aTargets': [ 0 ], bSortable: true, 'sClass': 'left' },
            { 'aTargets': [ '_all' ], bSortable: false, 'sClass': 'center' }
        ]
    });

    $("#asistencias_web_filter").append('<i id="exportar_informe" class="icon-print grey" onclick="exportar_informe(\'pdf\');" style="cursor: pointer; margin-left: 6px;" data-original-title="" title="exportar"></i>');
}