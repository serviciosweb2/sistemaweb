var aoColumnDefs = columns;
var codigo = -1;
var lang = BASE_LANG;
var menu = menuJson;
var thead = [];
var data = '';
var oTable = '';

$(document).ready(function() {
    init();
});

/* Table initialisation */
function init() {
    var TablaGeneral = '#administracionPlanesPago';
    oTable = $(TablaGeneral).dataTable({
        bProcessing: false,
        bServerSide: true,
        sAjaxSource: BASE_URL + "planespago/listar",
        sServerMethod: "POST",
        aaSorting: [[0, "desc"]],
        fnServerData: function(sSource, aoData, fnCallback) {
            var fecha_inicio_desde = $("#div_table_filters").find("[name=fecha_inicio_desde]").val();
            var fecha_inicio_hasta = $("#div_table_filters").find("[name=fecha_inicio_hasta]").val();
            var fecha_vigencia_desde = $("#div_table_filters").find("[name=fecha_vigencia_desde]").val();
            var fecha_vigencia_hasta = $("#div_table_filters").find("[name=fecha_vigencia_hasta]").val();
            var plan_academico = $("#div_table_filters").find("[name=filtro_plan_academico]").val();
            var modalidad = $("#div_table_filters").find("[name=filtro_modalidad]").val();
            var periodo = $("#div_table_filters").find("[name=filtro_periodo]").val();
            var baja = $("#div_table_filters").find("[name=filtro_baja]").val();
            aoData.push({name: "fecha_inicio_desde", value: fecha_inicio_desde});
            aoData.push({name: "fecha_inicio_hasta", value: fecha_inicio_hasta});
            aoData.push({name: "fecha_vigencia_desde", value: fecha_vigencia_desde});
            aoData.push({name: "fecha_vigencia_hasta", value: fecha_vigencia_hasta});
            aoData.push({name: "plan_academico", value: plan_academico});
            aoData.push({name: "modalidad", value: modalidad});
            aoData.push({name: "periodo", value: periodo});
            aoData.push({name: "baja", value: baja});
            $.ajax({
                dataType: 'json',
                type: "POST",
                url: sSource,
                data: aoData,
                async: false,
                success: fnCallback
            });
        },
        "aoColumnDefs": aoColumnDefs,
        "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            var baja = aData[6];
            var clase = "";
            var estado = "";
            if (baja === "1") {
                clase = "label label-default arrowed";
                estado = lang.INHABILITADO;
            } else {
                clase = "label label-success arrowed";
                estado = lang.HABILITADO + "&nbsp";
            }
            var imgTag = '<span class="' + clase + '">' + estado + '</span>';
            $('td:eq(6)', nRow).html(imgTag);
            return nRow;
        }
    });
 
    $("#administracionPlanesPago_filter").find("label").addClass("input-icon input-icon-right");
    $("#administracionPlanesPago_filter").find("label").append('<i name="icon_filters" class="icon-caret-down grey" name="table_filters" style="margin-right: 7px; cursor: pointer"></i>');
    $("#administracionPlanesPago_filter").append($("[name=container_menu_filters_temp]").html());
    $(".date-picker").datepicker();
    $("[name=div_table_filters]").show();
    $(".select_chosen").chosen();
    $("[name=div_table_filters]").hide();
    $("[name=container_menu_filters_temp]").remove();    
    $("[name=icon_filters]").on("click", function() {
        $("[name=contenedorPrincipal]").toggle();
        $("[name=div_table_filters]").toggle(300);
        return false;
    });
    
    $("[name=contenedorPrincipal]").on("mousedown", function() {
        $("[name=contenedorPrincipal]").hide();
        $("[name=div_table_filters]").hide(300);
    });
    
    $("#administracionPlanesPago_filter").append('<i id="exportar_informe" class="icon-print grey" onclick="exportar_informe(\'pdf\');" style="cursor: pointer; margin-left: 6px;" data-original-title="" title="exportar"></i>');
    $("#administracionPlanesPago_filter").append('<i id="exportar_informe" class="icon-external-link" onclick="exportar_informe(\'csv\');" style="cursor: pointer; margin-left: 6px;" data-original-title="" title="exportar"></i>');
    
    marcarTr();
    $(aoColumnDefs).each(function() {
        thead.push(this.sTitle);
    });

    function columnName(name) {
        var retorno = '';
        $(thead).each(function(key, valor) {
            if (valor == name) {
                retorno = key;
            }
        });
        return retorno;
    }

    $(".dataTables_length").html(generarBotonSuperiorMenu(menu.superior, "btn-primary", " icon-asterisk"));
    var codigo;
    var desactivado;
    $('#areaTablas').on('mousedown', TablaGeneral + ' tbody tr', function(e) {
        var sData = oTable.fnGetData(this);
        codigo = sData[columnName(lang.codigo)];
        desactivado = sData[6];
        if (e.button === 2) {
            generalContextMenu(menu.contextual, e);
            if (desactivado === "1") {
                $('a[accion="baja-plan"]').text(lang.HABILITAR);
            } else {
                $('a[accion="baja-plan"]').text(lang.INHABILITAR);
            }
            return false;
        }
    });
    function despliegaMenu(x, y, codigo) {
        $('#desplegable').remove();
        var contenido = '<div id="desplegable" class="row" oncontextmenu="return false" onkeydown="return false">' + menu.contextual + '</div>';
        $('body').prepend(contenido);
        $('#desplegable').css({
            "margin-top": y, "margin-left": x
        });
    }

    $('body').on('click', '#menu a', function() {
        var accion = $(this).attr('accion');
        var id = $(this).attr('id');
        $('#menu').remove();
        switch (accion) {
            
            case 'modificar-planes-pago':
                modificar_plan_pago(codigo);
                break;
                
            case 'duplicar_plan':                
                $.ajax({
                    url: BASE_URL + "planespago/frm_plan_pago",
                    data: {
                        codigo: codigo,
                        clonar: codigo
                    },
                    type: 'POST',
                    cache: false,
                    success: function(respuesta) {
                        $.fancybox.open(respuesta, {
                            scrolling: 'auto',
                            autoSize: false,
                            width: '80%',
                            height: 'auto',
                            autoResize: true,
                            padding: 0,
                            openEffect: 'none',
                            closeEffect: 'none',
                            beforeClose: function() {
                                oTable.fnDraw();
                            },
                            helpers: {
                                overlay: null
                            }
                        });
                    }
                });
                break;

            case 'baja-plan':
                var htmldesactivado = "";
                if (desactivado === "0") {
                    htmldesactivado = lang.INHABILITA_PLAN;
                } else {
                    htmldesactivado = lang.HABILITA_PLAN;
                }
                $(".modal-confirmacion").html(htmldesactivado);
                $("#confirmaEliminar").modal('show');
                $("#btn-ok-cambio-estado").unbind("click");
                $("#btn-ok-cambio-estado").on("click", function() {
                    $.ajax({
                        url: BASE_URL + "planespago/cambiarEstado",
                        data: 'codigo=' + codigo,
                        type: 'POST',
                        cache: false,
                        success: function(respuesta) {
                            var correcto;
                            if (desactivado === "0") {
                                correcto = lang.INHABILITACORRECTO_PLAN;
                            } else {
                                correcto = lang.HABILITACORRECTO_PLAN;
                            }
                            $.gritter.add({
                                title: '',
                                text: correcto,
                                sticky: false,
                                time: '3000',
                                class_name: 'gritter-success'
                            });
                            oTable.fnDraw();
                        }
                    });
                });
                break;
        }
        return false;
    });

    $('body').on('click', '.dataTables_length button', function() {
        var accion = $(this).attr('accion');
        $('#desplegable').remove();
        switch (accion) {
            case 'nuevo-plan':
                nuevo_plan_pago(-1);
                break;
        }
        return false;
    });
}    
    
function modificar_plan_pago(codigo){
    $.ajax({
        url: BASE_URL + "planespago/frm_modificar_plan_pago",
        data: {
            codigo: codigo
        },
        type: 'POST',
        cache: false,
        success: function(respuesta) {
            $.fancybox.open(respuesta, {
                scrolling: 'auto',
                autoSize: false,
                width: '80%',
                height: 'auto',
                autoResize: true,
                padding: 0,
                openEffect: 'none',
                closeEffect: 'none',
                beforeClose: function() {
                    oTable.fnDraw();
                },
                helpers: {
                    overlay: null
                }
            });
        }
    });
}

function nuevo_plan_pago(codigo){
    $.ajax({
        url: BASE_URL + "planespago/frm_nuevo_plan_pago",
        data: {
            codigo: codigo
        },
        type: 'POST',
        cache: false,
        success: function(respuesta) {
            $.fancybox.open(respuesta, {
                scrolling: 'auto',
                autoSize: false,
                width: '80%',
                height: 'auto',
                autoResize: true,
                padding: 0,
                openEffect: 'none',
                closeEffect: 'none',
                beforeClose: function() {
                    oTable.fnDraw();
                },
                helpers: {
                    overlay: null
                }
            });
        }
    });
}

function listarPlanesAcademicos(){
    oTable.api().ajax.reload(); 
}

function exportar_informe(formato){
    var iSortCol_0 = oTable.fnSettings().aaSorting[0][0];
    var sSortDir_0 = oTable.fnSettings().aaSorting[0][1];
    var iDisplayLength = oTable.fnSettings()._iDisplayLength;
    var iDisplayStart = oTable.fnSettings()._iDisplayStart;
    var sSearch = $("#boletosBancarios_filter").find("input[type=text]").val();
    var fecha_inicio_desde = $("#div_table_filters").find("[name=fecha_inicio_desde]").val();
    var fecha_inicio_hasta = $("#div_table_filters").find("[name=fecha_inicio_hasta]").val();
    var fecha_vigencia_desde = $("#div_table_filters").find("[name=fecha_vigencia_desde]").val();
    var fecha_vigencia_hasta = $("#div_table_filters").find("[name=fecha_vigencia_hasta]").val();
    var plan_academico = $("#div_table_filters").find("[name=filtro_plan_academico]").val();
    var modalidad = $("#div_table_filters").find("[name=filtro_modalidad]").val();
    var periodo = $("#div_table_filters").find("[name=filtro_periodo]").val();
    var baja = $("#div_table_filters").find("[name=filtro_baja]").val();
    $("[name=exportar]").find("[name=iSortCol_0]").val(iSortCol_0);
    $("[name=exportar]").find("[name=sSortDir_0]").val(sSortDir_0);
    $("[name=exportar]").find("[name=iDisplayLength]").val(iDisplayLength);
    $("[name=exportar]").find("[name=iDisplayStart]").val(iDisplayStart);
    $("[name=exportar]").find("[name=sSearch]").val(sSearch);
    $("[name=exportar]").find("[name=fecha_inicio_desde]").val(fecha_inicio_desde);
    $("[name=exportar]").find("[name=fecha_inicio_hasta]").val(fecha_inicio_hasta);
    $("[name=exportar]").find("[name=fecha_vigencia_desde]").val(fecha_vigencia_desde);
    $("[name=exportar]").find("[name=fecha_vigencia_hasta]").val(fecha_vigencia_hasta);
    $("[name=exportar]").find("[name=plan_academico]").val(plan_academico);
    $("[name=exportar]").find("[name=modalidad]").val(modalidad);
    $("[name=exportar]").find("[name=periodo]").val(periodo);
    $("[name=exportar]").find("[name=baja]").val(baja);
    $("[name=exportar]").find("[name=formato]").val(formato);
    $("[name=exportar]").submit();
}