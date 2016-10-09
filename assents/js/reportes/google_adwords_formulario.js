var aoColumnDefs = '';
var oTable = '';
var lang = BASE_LANG;
var dateRange = '';
var drp = '';
aoColumnDefs = columns;
var cargar = false;

$(function() {

    oTable = $('#adwords-table').dataTable({
        "bServerSide": true,
        "sAjaxSource": BASE_URL + 'reportes/listar_google_adwords_formulario',
        "aaSorting": [[0, "desc"]],
        "sServerMethod": "POST",
        "bAutoWidth": false,
        "bPaginate": false,
        "bLengthChange": false,
        "aoColumns": [
            { "sWidth": "40%" },
            { "sWidth": "20%" },
            { "sWidth": "20%" },
            { "sWidth": "20%" },
        ],
        'aoColumnDefs': aoColumnDefs,
        fnServerData: function(sSource, aData, fnCallback){
            if(cargar) {
                var campana = $("[name=filtro_campana]").val();
                var fecha_desde = drp.startDate.format('YYYY-MM-DD');
                var fecha_hasta = drp.endDate.format('YYYY-MM-DD');

                aData.push({name: 'campana', value: campana});
                aData.push({name: "fecha_desde", value: fecha_desde});
                aData.push({name: "fecha_hasta", value: fecha_hasta});
                $.ajax({
                    dataType: 'json',
                    type: "POST",
                    url: sSource,
                    data: aData,
                    async: true,
                    success: fnCallback
                });
            }
        }
    });

    $('#adwords-table_processing').hide();
    $('#adwords-table').wrap('<div class="table-responsive"></div>');
    thead = [];
    $(aoColumnDefs).each(function() {
        thead.push(this.sTitle);
    });

    $("#adwords-table_filter").find("label").addClass("input-icon input-icon-right");
    $("#adwords-table_filter").find("label").append('<i name="icon_filters" class="icon-caret-down grey" style="margin-right: 7px; cursor: pointer"></i>');
    $("#adwords-table_filter").append($("[name=container_menu_filters_temp]").html());
    $("[name=container_menu_filters_temp]").remove();
    $("[name=div_table_filters]").hide();

    $("#adwords-table_filter").append('<i id="exportar_informe" class="icon-print grey" onclick="exportar_informe(\'pdf\');" style="cursor: pointer; margin-left: 6px;" data-original-title="" title="exportar"></i>');
    $("#adwords-table_filter").append('<i id="exportar_informe" class="icon-external-link" onclick="exportar_informe(\'csv\');" style="cursor: pointer; margin-left: 6px;" data-original-title="" title="exportar"></i>');
    var ranges = {};
    ranges[lang.hoy] = [moment(), moment()];
    ranges[lang.ayer] = [moment().subtract(1, 'days'), moment().subtract(1, 'days')];
    ranges[lang.ultima_semana] = [moment().subtract(6, 'days'), moment()];
    ranges[lang.ultimos_30_dias] = [moment().subtract(29, 'days'), moment()];
    ranges[lang.este_mes] = [moment().startOf('month'), moment().endOf(39,'month')];
    ranges[lang.ultimo_mes] = [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')];

    dateRange = $('input[name=date-range-picker]').daterangepicker({
        opens: 'left',
        startDate: moment(),
        endDate: moment(),
        maxDate: moment(),
        minDate: "01/01/2005",
        locale: {
            format: 'DD/MM/YYYY',
            cancelLabel: lang.cancelar,
            applyLabel: lang.aceptar,
            customRangeLabel: lang.rango_perzonalizado
        },
        ranges: ranges,
        cancelClass: 'btn-danger'
    });

    drp = $('input[name=date-range-picker]').data('daterangepicker');

    $(".select_chosen").chosen({width: "180px"});
    $("[name=icon_filters]").on("click", function() {
        $("[name=contenedorPrincipal]").toggle();
        $("[name=div_table_filters]").toggle(300);
        return false;
    });

    $("[name=contenedorPrincipal]").on("mousedown", function() {
        $("[name=contenedorPrincipal]").hide();
        $("[name=div_table_filters]").hide(300);
    });
    cargar = true;
});

function listar(){
    oTable.fnDraw();
}

function exportar_informe(tipo_reporte){
    if(oTable.fnGetData() == '') {
        $.gritter.add({
            title: lang.alerta,
            text: lang.no_hay_datos_para_imprimir_o_exportar,
            sticky: false,
            time: '3000',
            class_name: 'gritter-warning'
        });
        return false;
    }
    var iSortCol_0 = oTable.fnSettings().aaSorting[0][0];
    var sSortDir_0 = oTable.fnSettings().aaSorting[0][1];
    var iDisplayLength = null;
    var iDisplayStart = null;
    var sSearch = $("#adwords-table_filter").find("input[type=search]").val();
    var fecha_desde = drp.startDate.format('YYYY-MM-DD');
    var fecha_hasta = drp.endDate.format('YYYY-MM-DD');
    var campana = $("[name=filtro_campana]").val();
    $("[name=frm_exportar]").find("[name=iSortCol_0]").val(iSortCol_0);
    $("[name=frm_exportar]").find("[name=sSortDir_0]").val(sSortDir_0);
    $("[name=frm_exportar]").find("[name=iDisplayLength]").val(iDisplayLength);
    $("[name=frm_exportar]").find("[name=iDisplayStart]").val(iDisplayStart);
    $("[name=frm_exportar]").find("[name=sSearch]").val(sSearch);
    $("[name=frm_exportar]").find("[name=campana]").val(campana);
    $("[name=frm_exportar]").find("[name=fecha_desde]").val(fecha_desde);
    $("[name=frm_exportar]").find("[name=fecha_hasta]").val(fecha_hasta);
    $("[name=frm_exportar]").find("[name=tipo_reporte]").val(tipo_reporte);
    $("[name=frm_exportar]").submit();
}