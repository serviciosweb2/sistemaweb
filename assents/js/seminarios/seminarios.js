var oTable = '';
$(document).ready(function(){
    oTable = $('#academicoAlumnos').dataTable({
        "bServerSide": true,
        "sAjaxSource": BASE_URL + 'seminarios/listarSeminariosDataTable',
        "aaSorting": [[0, "desc"]],
        "sServerMethod": "POST",
        fnServerData: function(sSource, aoData, fnCallback){
            aoData.push({name: "horario", value: $("[name=filtro_horario]").val()});
            $.ajax({
                dataType: 'json',
                type: "POST",
                url: sSource,
                data: aoData,
                success: fnCallback
            });
        }
    });
    $("#academicoAlumnos_filter").append('&nbsp;&nbsp;&nbsp;<i id="imprimir_informe" class="icon-print grey" style="cursor: pointer" onclick="imprimirInscriptos();" data-original-title="" title=""></i>');
});

function buscarInscriptos(){
    oTable.api().ajax.reload(); 
}

function imprimirInscriptos(){
    var horario = $("[name=filtro_horario]").val();
    var param = new Array();
        param.push(horario, 1, 1);
        printers_jobs(15, param);
}