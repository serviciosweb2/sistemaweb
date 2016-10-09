var oTable = '';

$(document).ready(function(){
    oTable = $('#tbl_cupones_landing').dataTable({
        bServerSide: true,
        sAjaxSource: BASE_URL + 'cupones/listar',
        aaSorting: [[ 0, "desc" ]],
        sServerMethod: "POST",
        aoColumnDefs: columns,
        fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            var _html = '';
            _html += '<a class="btn-detalle" href="#" onclick="ver_comentarios(' + aData['6'] + ')">';
            _html += lang_cupones.detalle;
            _html += '</a>';
            $('td:eq(6)', nRow).html(_html);
            return nRow;
        },        
        fnServerData: function(sSource, aData, fnCallback){
            var cod_curso = $("[name=filtro_curso_interes]").val();
            var fecha_desde = $("[name=filtro_fecha_desde]").val();
            var fecha_hasta = $("[name=filtro_fecha_hasta]").val();
            aData.push({name: "cod_curso", value: cod_curso});
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
    });
    
    $("#tbl_cupones_landing_filter").find("label").addClass("input-icon input-icon-right");
    $("#tbl_cupones_landing_filter").find("label").append('<i name="icon_filters" class="icon-caret-down grey" style="margin-right: 7px; cursor: pointer"></i>');
    $("#tbl_cupones_landing_filter").append($("[name=container_menu_filters_temp]").html());
    $(".date-picker").datepicker();
    $(".select_chosen").chosen();
    $("[name=container_menu_filters_temp]").remove();
    $("[name=div_table_filters]").hide();
    
    $("#tbl_cupones_landing_filter").append('<i id="exportar_informe" class="icon-print grey" onclick="exportar_informe(\'pdf\');" style="cursor: pointer; margin-left: 6px;" data-original-title="" title="exportar"></i>');
    $("#tbl_cupones_landing_filter").append('<i id="exportar_informe" class="icon-external-link" onclick="exportar_informe(\'csv\');" style="cursor: pointer; margin-left: 6px;" data-original-title="" title="exportar"></i>');
        
    $("[name=icon_filters]").on("click", function() {
        $("[name=contenedorPrincipal]").toggle();
        $("[name=div_table_filters]").toggle(300);
        return false;
    });

    $("[name=contenedorPrincipal]").on("mousedown", function() {
        $("[name=contenedorPrincipal]").hide();
        $("[name=div_table_filters]").hide(300);
    });
    
});

function listar(){
    oTable.fnDraw();
}

function ver_comentarios(id_cupon){
    $.ajax({
        url: BASE_URL + 'cupones/ver_comentarios',
        type: 'POST',
        data: {
            id_cupon: id_cupon
        },
        success: function(_html){
            $.fancybox.open(_html, {
                scrolling: true,
                width: '50%',
                height: 'auto',
                autoSize: false,
                padding: 0,
                openEffect: 'none',
                closeEffect: 'none',
                helpers: {
                    overlay: null
                }
            });
        }
    });
}

function guardar_comentario(id_cupon){
    var comentario = $("[name=comentario]").val();
    if ($.trim(comentario) == ''){
        gritter(lang_cupones.comentario_es_requerido, false, ' ');
    } else {
        $.ajax({
            url: BASE_URL + "cupones/guardar_comentario",
            type: 'POST',
            dataType: 'json',
            data:{
                id_cupon: id_cupon,
                comentario: comentario
            },
            success: function(_json){
                if (_json.error){
                    gritter(_json.error, false, '');
                } else {
                    gritter(lang_cupones.validacion_ok, true, ' ');
                    $.fancybox.close();
                }
            }
        });
    }
}

function exportar_informe(tipo_reporte){
    var iSortCol_0 = oTable.fnSettings().aaSorting[0][0];
    var sSortDir_0 = oTable.fnSettings().aaSorting[0][1];
    var iDisplayLength = oTable.fnSettings()._iDisplayLength;
    var iDisplayStart = oTable.fnSettings()._iDisplayStart;
    var sSearch = $("#academicoAspirantes_filter").find("input[type=search]").val();
    var fecha_desde = $("[name=filtro_fecha_desde]").val();
    var fecha_hasta = $("[name=filtro_fecha_hasta]").val();
    var cod_curso = $("[name=filtro_curso_interes]").val();
    
    $("[name=frm_exportar]").find("[name=iSortCol_0]").val(iSortCol_0);
    $("[name=frm_exportar]").find("[name=sSortDir_0]").val(sSortDir_0);
    $("[name=frm_exportar]").find("[name=iDisplayLength]").val(iDisplayLength);
    $("[name=frm_exportar]").find("[name=iDisplayStart]").val(iDisplayStart);
    $("[name=frm_exportar]").find("[name=sSearch]").val(sSearch);
    $("[name=frm_exportar]").find("[name=fecha_desde]").val(fecha_desde);
    $("[name=frm_exportar]").find("[name=fecha_hasta]").val(fecha_hasta);
    $("[name=frm_exportar]").find("[name=tipo_reporte]").val(tipo_reporte);
    $("[name=frm_exportar]").find("[name=cod_curso]").val(cod_curso);
    $("[name=frm_exportar]").submit();
}