var oTable = '';

$(document).ready(function(){
    
    oTable = $('#tbl_tickets').dataTable({
        "bServerSide": true,
        "sAjaxSource": BASE_URL + 'tickets/listar',
        "aaSorting": [[0, "desc"]],
        "sServerMethod": "POST",
        aoColumnDefs: columns,
        "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            var asunto = aData[1];
            var _html = '<button class="btn btn-link" onclick="ver_ticket(' + aData[7] + ')">';
            _html += asunto;
            _html += '</button>';
            $('td:eq(1)', nRow).html(_html);
            return nRow;
        },
        fnServerData: function(sSource, aData, fnCallback){
            var estado = $("[name=filtro_estado]").val();
            var area = $("[name=filtro_area]").val();
            var prioridad = $("[name=filtro_prioridad]").val();
            var fecha_desde = $("[name=filtro_fecha_desde]").val();
            var fecha_hasta = $("[name=filtro_fecha_hasta]").val();
            aData.push(
                    {name: "estado", value: estado},
                    {name: "area", value: area},
                    {name: "prioridad", value: prioridad},
                    {name: "fecha_desde", value: fecha_desde},
                    {name: "fecha_hasta", value: fecha_hasta}
                );
            $.ajax({
                dataType: 'json',
                type: "POST",
                url: sSource,
                data: aData,
                async: true,
                success: fnCallback
            });
        }
    });
    $("#tbl_tickets_filter").find("label").addClass("input-icon input-icon-right");
    $("#tbl_tickets_filter").find("label").append('<i name="icon_filters" class="icon-caret-down grey" style="margin-right: 7px; cursor: pointer"></i>');
    $("#tbl_tickets_filter").append($("[name=container_menu_filters_temp]").html());
    $(".date-picker").datepicker();
    $(".select_chosen").chosen();
    $("[name=container_menu_filters_temp]").remove();
    $("[name=div_table_filters]").hide();
    $("[name=icon_filters]").on("click", function() {
        $("[name=contenedorPrincipal]").toggle();
        $("[name=div_table_filters]").toggle(300);
        return false;
    });

    $("[name=contenedorPrincipal]").on("mousedown", function() {
        $("[name=contenedorPrincipal]").hide();
        $("[name=div_table_filters]").hide(300);
    });
    
    var _html = '<div class="btn-group" style="margin-left: 60px;">';
    _html += '<button class="btn btn-primary boton-primario" onclick="generar_ticket();">';
    _html += langFrm.reportar_error;
    _html += '</button>';
    _html += '</div>';
    $("#tbl_tickets_length").append(_html);
});

function listar(){
    oTable.fnDraw();
}

function generar_ticket(){
    $.ajax({
        url: BASE_URL + 'tickets/generar_tickets',
        type: 'POST',
        success: function(_html){
            $.fancybox.open(_html, {
                scrolling: 'auto',
                autoSize: false,
                width: '60%',
                height: 'auto',
                padding: 1,
                openEffect: 'none',
                closeEffect: 'none',
                helpers: {
                    overlay: null
                }
            });
        }
    });
}

function ver_ticket(id_ticket){
    $.ajax({
        url: BASE_URL + 'tickets/ver_ticket',
        type: 'POST',
        data: {
            id_ticket: id_ticket
        },
        success: function(_html){
            $.fancybox.open(_html, {
                scrolling: 'auto',
                autoSize: false,
                width: 'auto',
                height: 'auto',
                padding: 1,
                openEffect: 'none',
                closeEffect: 'none',
                helpers: {
                    overlay: null
                }
            });
        }
    });
}