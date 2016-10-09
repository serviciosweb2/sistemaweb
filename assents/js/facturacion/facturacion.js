var inputsCheckeados = new Array();
var oTable = '';
var oTableExportar = '';
var ordenarylistar = false;
var lang = BASE_LANG;
var menu = menuJson;
var aoColumnDefs = columns;
var envioMail = facturaMail;
var total_facturacion = 0;

$(document).ready(function(){
    var thead = [];
    var data = '';
    init();
    function init() {
        function ejson(string) {
            try {
                JSON.parse(string);
            } catch (e) {
                return false;
            }
            return true;
        }
        var nCloneTd = document.createElement('td');
        nCloneTd.innerHTML = 'link';
        nCloneTd.className = "center";
        var _foot = "<tfoot>";
        _foot += "<tr>";
        _foot += "<td colspan='9' style='vertical-align: middle; text-align: right; font-weight: bold;'>";
        _foot += "Total";
        _foot += "</td>";
        _foot += "<td id='tfoot_total' style='font-weight: bold;'>";        
        _foot += "</td>";
        _foot += "<td>";        
        _foot += "</td>";
        _foot += "</tr>";
        _foot += "</tfoot>";
        $("#administracionFacturacion").append(_foot);
        oTable = $('#administracionFacturacion').dataTable({
            "bServerSide": true,
            "aaSorting": [[0, "desc"]],
            "sAjaxSource": BASE_URL + "facturacion/listar",
            "sServerMethod": "POST",
            "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                var estado = aData[10];
                total_facturacion += Math.round(parseFloat(aData[13]) * 100) / 100;
                var total = Math.round(total_facturacion * 100) / 100;
                var _html = aData[14] + " " + total.toFixed(2);
                $("#administracionFacturacion").find("tfoot").find("#tfoot_total").html(_html);
                var btn = "";
                switch (estado) {
                    case "inhabilitada":
                        btn = "<span style='width: 78px;' class='label label-default arrowed' onclick='verDetalleFactura(" + aData[0] + ")' style='cursor: pointer;'>" + lang.ANULADA + "</span>";
                        break;
                    case "habilitada":
                        btn = "<span style='width: 78px;' class='label label-success arrowed' onclick='verDetalleFactura(" + aData[0] + ")' style='cursor: pointer;'>" + lang.CONFIRMADA + "</span>";
                        break;
                    case "pendiente":
                        btn = "<span style='width: 78px;' class='label label-yellow arrowed' onclick='verDetalleFactura(" + aData[0] + ")' style='cursor: pointer;'>" + lang.pendiente + "</span>";
                        break;
                    case "enviado":
                        btn = "<span style='width: 78px;' class='label label-yellow arrowed' onclick='verDetalleFactura(" + aData[0] + ")' style='cursor: pointer;'>" + lang.enviada + "</span>";
                        break;
                    case "error":
                        btn = "<span style='width: 78px;' class='label label-warning arrowed' onclick='verDetalleFactura(" + aData[0] + ")' style='cursor: pointer;'>" + lang.error + "</span>";
                        break;
                    case "pendiente_cancelar":
                        btn = "<span style='width: 78px;' class='label label-default arrowed' onclick='verDetalleFactura(" + aData[0] + ")' style='cursor: pointer;'>" + lang.pendiente_cancelar + "</span>";
                }
                $('td:last', nRow).html(btn);
                var complemento = "";
                if (inputsCheckeados[aData[0]] && inputsCheckeados[aData[0]] == 1) {
                    complemento += " checked='true'";
                } else {
                    inputsCheckeados[aData[0]] = 0;
                }
                var email = '';
                email += aData[11] + "\n";
                if (aData[11] == null || aData[12]=='0') {
                    complemento += " disabled='true'";
                }
                $('td:first', nRow).html("<input type='checkbox' class='ace' onclick='facturasChecked(this);' value='" + aData[0] + "' name='facturas_enviar_check'" + complemento + "><span class='lbl'></span>");
            },
            "fnServerData": function(sSource, aData, fnCallback) {
                total_facturacion = 0;
                $("#administracionFacturacion").find("tfoot").find("#tfoot_total").html("0.00");
                var estado = $("[name=filtro_estado]").val();
                var fecha_desde = $("[name=filtro_facturas_fecha_desde]").val();
                var fecha_hasta = $("[name=filtro_facturas_fecha_hasta]").val();
                var tipo_factura = $("[name=filtro_facturas_tipo_factura]").val();
                aData.push({name: "estado", value: estado});
                aData.push({name: "fecha_desde", value: fecha_desde});
                aData.push({name: "fecha_hasta", value: fecha_hasta});
                aData.push({name: "tipo_factura", value: tipo_factura});
                $.ajax({
                    "dataType": 'json',
                    "type": "POST",
                    "url": sSource,
                    "data": aData,
                    "async": true,
                    "success": fnCallback
                });
                $('#administracionFacturacion tbody tr .btn').each(function() {
                    $(this).popover({
                        placement: 'left',
                        title: 'Detalle',
                        html: true,
                        trigger: 'manual'
                    });
                });
            },
            "aoColumnDefs": aoColumnDefs
        });        
        
        oTableExportar = $('#tbl_exportar').dataTable({
            oLanguage: {     
                sLengthMenu: lang.mostrar + ' <select>' +
                    '<option value="1000">(Todos)</option>' + // es el que selecciona por defecto
                    '<option value="10" SELECTED>10</option>' +
                    '<option value="20">20</option>' +
                    '<option value="30">30</option>' +
                    '<option value="40">40</option>' +
                    '<option value="50">50</option>' +
                    '<option value="100">100</option>' +
                    '<option value="250">250</option>' +
                    '</select> ' + lang.registros 
            },
            bServerSide: true,
            aaSorting: [[0, "desc"]],
            sAjaxSource: BASE_URL + "facturacion/listar",
            sServerMethod: "POST",
            fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                var btn = "";
                btn = "<span style='width: 78px;' class='label label-success arrowed' onclick='verDetalleFactura(" + aData[0] + ")' style='cursor: pointer;'>" + lang.CONFIRMADA + "</span>";
                $('td:last', nRow).html(btn);
                var complemento = "";
                if (inputsCheckeados[aData[0]] && inputsCheckeados[aData[0]] == 1) {
                    complemento += " checked='true'";
                } else {
                    inputsCheckeados[aData[0]] = 0;
                }
                var email = '';
                email += aData[11] + "\n";
                $('td:first', nRow).html("<input type='checkbox' class='ace' value='" + aData[0] + "' name='chk_facturas_exportar'><span class='lbl'></span>");
            },
            fnServerData: function(sSource, aData, fnCallback) {
                var estado = "habilitada";
                var medio = 'electronico';
                var tipo_factura = $("[name=filtro_tipo_factura]").val();
                var fecha_desde = $("[name=filtro_fecha_desde]").val();
                var fecha_hasta = $("[name=filtro_fecha_hasta]").val();
                aData.push({name: "estado", value: estado});
                aData.push({name: "fecha_desde", value: fecha_desde});
                aData.push({name: "fecha_hasta", value: fecha_hasta});
                aData.push({name: "medio", value: medio});
                aData.push({name: "tipo_factura", value: tipo_factura});
                $.ajax({
                    "dataType": 'json',
                    "type": "POST",
                    "url": sSource,
                    "data": aData,
                    "async": true,
                    "success": fnCallback
                });

                $('#administracionFacturacion tbody tr .btn').each(function() {
                    $(this).popover({
                        placement: 'left',
                        title: 'Detalle',
                        html: true,
                        trigger: 'manual'
                    });
                });
            },
            aoColumnDefs: aoColumnDefs
        });
        
        $("#tbl_exportar_filter").append('<i class="icon-caret-down grey bigger-110 bigger-140" style="margin-right: 3px; cursor: pointer; margin-left: 10px;" name="table_filters" onclick="ver_ocultar_filtros();"></i>');
        var _html = '';
        _html += '<table style="margin-top: 0px; float: right; position: absolute; top: -22px; left: 234px; z-index: 200;">';
        _html += '<tr>';
        _html += '<td style="padding-left: 18px; vertical-align: bottom;">';
        _html += '<button class="btn btn-primary boton-primario" onclick="exportarListado();">' + lang.exportar  + '</button>';
        _html += '</td>';
        _html += '</tr>';
        _html += '</table>';        
        $("#tbl_exportar_length").append(_html);        
        marcarTr();
        $('#administracionFacturacion').wrap('<div class="table-responsive"></div>');
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

        var posicionModificado = [menu.superior[1], menu.superior[0], menu.superior[2]];        
        var _html2 = generarBotonSuperiorMenu(posicionModificado, "btn-primary", "icon-bookmark");        
        $("#area_botones_superiores").html(_html2);
        var oculto = envioMail !='0' ? '' : 'hide';
        $("#area_botones_superiores").append('<button style="margin-left: 36px;" class="btn btn-primary boton-primario ' + oculto + '" onclick="enviarFacturasPorEmail();">' + lang.enviar_facturas_por_email + 'l</button>');
        if (exportar_facturacion){
            var _html = '<button style="margin-left: 36px;" class="btn btn-primary boton-primario " onclick="verExportarNfe();">';
            _html += lang.informe_contable;
            _html += '</button>';
            $("#area_botones_superiores").append(_html);
        }
        var codigo = '';
        var estado = '';
        $('.page-content').on('mousedown', '#administracionFacturacion tbody tr', function(e) {
            var sData = oTable.fnGetData(this);
            estado = sData[columnName(lang['facturacion_estado'])];
            webserviceArg = sData[14];
            codigo = sData[columnName(lang['codigo'])];
            anular = sData[columnName(lang['facturacion_anular'])];
            if (e.button === 2) {
                generalContextMenu(menu.contextual, e);
                switch (estado) {
                    case "habilitada":
                        //Ticket 4555 -mmori- modifico clave deshabilitar-factura por anular_cobro
                        //TICKET 5123 -ag- modifica clave anular_cobro por anular factura
                        $('a[accion="cambiar-estado-facturas"]').text(lang['baja_factura']);
                        break;

                    case "inhabilitada" :
                        if(webserviceArg){
                            $('a[accion="cambiar-estado-facturas"]').parent().hide();
                        }else{
                            $('a[accion="cambiar-estado-facturas"]').parent().show();
                            $('a[accion="cambiar-estado-facturas"]').text(lang['habilitar-factura']);
                        }
                        $('a[accion="reimprimir_facturas"]').parent().hide();
                        break;

                    case "enviado":
                        $('a[accion="cambiar-estado-facturas"]').parent().hide();
                        $('a[accion="reimprimir_facturas"]').parent().hide();
                        $('#menu').hide();
                        break;

                    case "error":
                        $('a[accion="cambiar-estado-facturas"]').text(lang['reenviar_factura']);
                        $('a[accion="reimprimir_facturas"]').parent().hide();
                        break;

                    case "pendiente":
                         if(!webserviceArg){
                            $('a[accion="cambiar-estado-facturas"]').text(lang['baja_factura']);
                        }else{
                            $('a[accion="cambiar-estado-facturas"]').text(lang['reenviar_factura']);
                        }
                        
                        $('a[accion="reimprimir_facturas"]').parent().hide();
                        break;

                    case "pendiente_cancelar":
                        $('a[accion="cambiar-estado-facturas"]').parent().hide();
                        $('a[accion="reimprimir_facturas"]').parent().hide();
                        $('#menu').hide(); // hide de todo el menu (cuando existan otras opciones que pueden habilitarse, quitar esta linea)
                        break;

                }

                if (anular == 0) {
                    $('a[accion="cambiar-estado-facturas"]').addClass('deshabilitado').attr('accion', '');
                }
                return false;
            }
        });

        $('#administracionFacturacion').on('click', '.btn', function() {
            boton = this;
            if ($(boton).parent().find('.popover').is(':visible')) {
                $('.btn').popover('hide');
            } else {
                $.ajax({
                    url: BASE_URL + 'facturacion/getRenglonesDescripcion',
                    type: 'POST',
                    data: 'cod_factura=' + codigo,
                    dataType: 'json',
                    cache: false,
                    success: function(respuesta) {
                        tablaDetalle = '<div class="row"><div class="col-md-12"><table class="table table-striped table-bordered"><thead>';
                        tablaDetalle += '<th>' + lang.descripcion + '</th><th>' + lang.importe + '</th><th>' + lang.iva + '</th>';
                        tablaDetalle += '<tbody>';
                        $(respuesta).each(function(key, fila) {
                            if (fila.habilitado === '2') {
                                icono = "<i class='icon-ok icon-info-sign' title='" + lang.deuda_pasiva + "'></i>";
                            } else {
                                icono = "";
                            }
                            tablaDetalle += '<tr><td>' + fila.descripcion + '</td><td>' + fila.importe + '</td><td>' + fila.iva + '</td></tr>';
                        });
                        tablaDetalle += '<tbody></table></div></div>';
                        $(boton).attr('data-content', tablaDetalle);
                        $('.btn').not(boton).popover('hide');
                        $(boton).popover('show');
                    }
                });
            }
            return false;
        });

        $('body').on('click', '#menu a', function() {
            var accion = $(this).attr('accion');
            $('#menu').remove();
            switch (accion) {
                case 'reimprimir_facturas':
                    var param = new Array();
                    param.push(codigo);
                    printers_jobs(11, param);
                    break;

                case 'cambiar-estado-facturas':
                    switch (estado) {
                        case "pendiente":
                        case "habilitada" :
                            $.ajax({
                                url: BASE_URL + 'facturacion/frm_baja',
                                data: {
                                    cod_factura: codigo
                                },
                                type: 'POST',
                                cache: false,
                                success: function(respuesta) {
                                    $.fancybox.open(respuesta, {
                                        width: 'auto',
                                        height: 'auto',
                                        scrolling: true,
                                        autoSize: false,
                                        autoResize: false,
                                        padding: 0,
                                        openEffect: 'none',
                                        closeEffect: 'none',
                                        helpers: {
                                            overlay: null
                                        }, beforeClose: function() {
                                            oTable.fnStandingRedraw();
                                        }
                                    });
                                }
                            });
                            break;

                        case "inhabilitada":
                        case "error":
                            $.ajax({
                                url: BASE_URL + 'facturacion/habilitar_factura',
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    cod_factura: codigo
                                },
                                success: function(respuesta) {
                                    if (respuesta.codigo == 1) {
                                        gritter("", true, lang.BIEN);
                                        oTable.fnStandingRedraw();
                                    } else {
                                        gritter(respuesta.msgerror, false, lang.ERROR);
                                    }
                                }
                            });
                            break;                        
                    }
                    break
            }
            return false;
        });

        $('body').on('click', '#area_botones_superiores .boton-primario', function() {
            var accion = $(this).attr('accion');
            $('#desplegable').remove();
            switch (accion) {
                case 'factura-cobro':
                    $.ajax({
                        url: BASE_URL + "facturacion/frm_facturar_cobrar",
                        data: '',
                        type: 'POST',
                        cache: false,
                        success: function(respuesta) {
                            $.fancybox.open(respuesta, {
                                width: "60%",
                                height: "auto",
                                scrolling: 'auto',
                                autoSize: false,
                                autoResize: false,
                                padding: '0',
                                openEffect: 'none',
                                closeEffect: 'none',
                                helpers: {
                                    overlay: null
                                },
                                beforeClose: function() {
                                    oTable.fnDraw();
                                }
                            });
                        }
                    });
                    break;
            }
        });

        $('body').on('click', '#areaTablas .dropdown-menu li a', function() {
            $(".btn-group").removeClass("open");
            var accion = $(this).attr('accion');
            switch (accion) {
                case 'nuevo-factura':
                    ordenarylistar = false;
                    $.ajax({
                        url: BASE_URL + "facturacion/frm_facturar",
                        data: '',
                        type: 'POST',
                        cache: false,
                        success: function(respuesta) {
                            $.fancybox.open(respuesta, {
                                width: "60%",
                                height: "auto",
                                scrolling: 'auto',
                                autoSize: false,
                                autoResize: true,
                                padding: '0',
                                openEffect: 'none',
                                closeEffect: 'none',
                                helpers: {
                                    overlay: null
                                },
                                afterClose: function() {
                                    if (ordenarylistar) {
                                        oTable.fnSettings().aaSorting = [0, "desc"];
                                        oTable.fnDraw();
                                    }
                                }
                            });
                        }
                    });
                    break;

                case 'facturar-serie':
                    $.ajax({
                        url: BASE_URL + "facturacion/frm_facturar_serie",
                        data: '',
                        type: 'POST',
                        cache: false,
                        success: function(respuesta) {
                            $.fancybox.open(respuesta, {
                                scrolling: 'auto',
                                autoSize: true,
                                autoResize: true,
                                openEffect: 'none',
                                closeEffect: 'none',
                                padding: '0',
                                helpers: {
                                    overlay: null
                                },
                                beforeClose: function() {
                                    oTable.fnDraw();
                                }
                            });
                        }
                    });
                    break;
            }
            return false;
        });        

        $("#administracionFacturacion").on('click', '.id-toggle-all', function() {
            if ($(this).is(':checked')) {
                checkAll();
            } else {
                checkNone();
            }
        });
    
        $("#tbl_exportar").on('click', '.id-toggle-all', function(){
            if ($(this).is(':checked')){
                checkAllRelatorio();
            } else {
                unCheckAllRelatorio();
            }
        });

        $(".id-select-none").click(function() {
            checkNone();
        });
    }
    $("#administracionFacturacion_filter").find("label").addClass("input-icon input-icon-right");
    $("#administracionFacturacion_filter").find("label").append('<i name="icon_filters" class="icon-caret-down grey" name="table_filters" style="margin-right: 7px; cursor: pointer"></i>');
    $("#administracionFacturacion_filter").append($("[name=container_menu_filters_temp]").html());
    $(".date-picker").datepicker();
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
});

function listar() {
    oTable.fnDraw();
}

function checkAllRelatorio(){
    $("[name=chk_facturas_exportar]:enabled").prop("checked", true);
}

function unCheckAllRelatorio(){
    $("[name=chk_facturas_exportar]:enabled").prop("checked", false);
}

function checkAll() {    
    var seleccion = $("[name=facturas_enviar_check]:enabled");
    for (var i = 0; i < seleccion.length; i++) {
        if (!seleccion[i].checked) {
            var value = seleccion[i].value;
            inputsCheckeados[value] = 1;
            seleccion[i].checked = true;
        }
    }
    $("#option_checked").removeClass("open");
}

function checkNone() {    
    var seleccion = $("[name=facturas_enviar_check]:enabled");
    for (var i = 0; i < seleccion.length; i++) {
        if (seleccion[i].checked) {
            var value = seleccion[i].value;
            inputsCheckeados[value] = 0;
            seleccion[i].checked = false;
        }
    }
    $("#option_checked").removeClass("open");
}

function facturasChecked(element) {
    var value = element.value;
    inputsCheckeados[value] = element.checked ? 1 : 0;
}

function getFacturasSeleccionadas() {
    var facturas = new Array();
    $.each(inputsCheckeados, function(codFactura, value) {
        if (value == 1) {
            facturas.push(codFactura);
        }
    });
    return facturas;
}

function enviarFacturasPorEmail() {
    var facturas = getFacturasSeleccionadas();
    if (facturas.length > 0) {
        $.ajax({
            url: BASE_URL + 'facturacion/enviar_por_mail_confirmar',
            type: 'POST',
            data: {
                facturas: facturas
            },
            success: function(_html) {
                $.fancybox.open(_html, {
                    scrolling: 'auto',
                    autoSize: true,
                    autoResize: true,
                    openEffect: 'none',
                    closeEffect: 'none',
                    helpers: {
                        overlay: null
                    },
                    beforeClose: function() {
                    }
                });
            }
        });
    } else {
        alert(lang.debe_seleccionar_facturas_para_enviar);
    }
}

function cancelarEnvioFacturas() {
    $.fancybox.close();
}

function enviarFacturas() {
    var facturas = getFacturasSeleccionadas();
    if (facturas.length > 0) {
        $.ajax({
            url: BASE_URL + 'facturacion/enviar_facturas',
            type: 'POST',
            dataType: 'json',
            data: {
                facturas: facturas
            },
            success: function(_json) {
                if (_json.error) {
                    var listaCorreo = '';
                    $(_json.failue).each(function(key, valor) {
                        listaCorreo += valor + "<br>";
                    });
                    $.gritter.add({
                        title: lang.ERROR,
                        text: lang.los_siguientes_destinatarios_han_fallado + ":<br>" + listaCorreo,
                        sticky: false,
                        time: '3000',
                        class_name: 'gritter-error'
                    });
                } else {
                    $.gritter.add({
                        title: lang.BIEN,
                        text: lang.facturas_enviadas_correctamente,
                        sticky: false,
                        time: '3000',
                        class_name: 'gritter-success'
                    });
                }
                $.fancybox.close();
            }
        });
    } else {
        alert(lang.debe_seleccionar_facturas_para_enviar);
    }
}

function imprimirFacturaYRecibo() {
    $("#btnImprimir").attr("disabled", true);
    var factura = $("#codigo_factura").val();
    var cobro = $("#codigo_cobro").val();
    var impresora_factura = $("#impresora_imprimir_factura").val();
    var impresora_cobros = $("#impresora_imprimir_cobros").val();
    var copias_facturas = $("#impresion_cantidad_copias_factura").val();
    var copias_cobros = $("#impresion_cantidad_copias_cobros").val();
    var puedeCerrar = false;
    if (impresora_factura != -2) {
        var param = new Array();
        param['parametros'] = factura;
        param['id_impresora'] = impresora_factura;
        param['copias'] = copias_facturas;
        if (impresora_factura == -1) {
            puedeCerrar = true;
            imprimirNavegador(11, param);
        } else {
            param['id_impresora'] = impresora_factura;
            param['parametros'] = factura;
            param['copias'] = copias_facturas;
            puedeCerrar = false;
            $("#area_mensajes").html("enviado trabajo de impresion...");
            imprimirCloud(11, param, "cerrarPreguntarImprimir()", "enabledButton()");
        }
    } else {
        puedeCerrar = true;
    }

    if (impresora_cobros != -2) {
        var param = new Array();
        param['parametros'] = cobro;
        param['id_impresora'] = impresora_cobros;
        param['copias'] = copias_cobros;
        if (impresora_cobros == -1) {
            puedeCerrar = puedeCerrar && true;
            imprimirNavegador(10, param);
        } else {
            puedeCerrar = false;
            $("#area_mensajes").html("enviado trabajo de impresion...");
            imprimirCloud(10, param, "cerrarPreguntarImprimir()", "enabledButton()");
        }
    } else {
        puedeCerrar = puedeCerrar && true;
    }
    if (puedeCerrar) {
        $.fancybox.close();
    }
}

function verDetalleFactura(codigoFactura) {
    $.ajax({
        url: BASE_URL + 'facturacion/getDetalleFactura',
        type: 'POST',
        dataType: 'json',
        data: {
            codigoFactura: codigoFactura
        },
        success: function(_json) {
            $("#tbodyDetalle tr").remove();
            var detalle = '';
            $.each(_json.renglones, function(key, value) {
                detalle += '<tr>';
                detalle += '<td>';
                detalle += value.descripcion;
                detalle += '</td>';
                detalle += '<td>';
                detalle += value.simbolo_moneda + " " + value.importe_formateado;
                detalle += '</td>';
                detalle += '</tr>';
            });
            if (_json.web_services) {
                detalle += "<tr>";
                detalle += "<td colspan='2' style='text-align: center;'>";
                detalle += "<table style='width: 100%;'>";
                detalle += "<tr>";
                $.each(_json.web_services, function(key, value) {
                    detalle += "<td>";
                    detalle += value.name;
                    detalle += "<br>";
                    detalle += value.value == null ? "-" : value.value;
                    detalle += "</td>";
                });
                detalle += "</tr>";
                detalle += "</table>";
                detalle += "</td>";
                detalle += "</tr>";
            }
            if (_json.web_services_error) {
                detalle += "<tr>";
                detalle += "<td colspan='2' style='text-align: center;'>";
                detalle += "<table style='width: 100%;'>";
                detalle += "<tr>";
                detalle += "<td>";
                detalle += "<span style='font-weight: bold;'>";
                detalle += "ERROR: &nbsp;&nbsp;&nbsp;&nbsp;";
                detalle += "</span>";
                detalle += _json.web_services_error;
                detalle += "</td>";
                detalle += "</tr>";
                detalle += "</table>";
                detalle += "</td>";
                detalle += "</tr>";
            }
            $("#tbodyDetalle").append(detalle);
            var _htmlVer = $("#detaleFactura").html();
            $.fancybox.open(_htmlVer, {
                height: "auto",
                scrolling: 'auto',
                autoSize: false,
                autoResize: false,
                padding: '0',
                openEffect: 'none',
                closeEffect: 'none',
                helpers: {
                    overlay: null
                },
                beforeClose: function() {
                }
            });
        }
    });
}

function verExportarNfe(){
    $("#areaTablas").hide();
    $("#areaExportar").show();
}

function volvarAFacturas(){
    $("#areaExportar").hide();
    $("#areaTablas").show();
}

function listarExportar(){
    oTableExportar.api().ajax.reload();
}

function exportarListado(){
    var seleccion = $("[name=chk_facturas_exportar]:checked");
    if (seleccion.length == 0){
        gritter("Seleccione facturas para exportar");
    } else {
        $("[name=frm_exportar]").find("[name='factura_exportar[]']").remove();
        $.each(seleccion, function(key, element){
            $("[name=frm_exportar]").append('<input type="hidden" name="factura_exportar[]" value="' + $(element).val() + '">');
        });
        $("[name=frm_exportar]").submit();
    }
}

function ver_ocultar_filtros(){
    if ($("[name=div_table_filters_exportar]").is(":visible")){
        $("[name=div_table_filters_exportar]").hide(200);
        $("[name=contenedorPrincipalExportar]").hide();
    } else {
        $("[name=div_table_filters_exportar]").show(200);
        $("[name=contenedorPrincipalExportar]").show();
    }
}

function checkExportar(element){
    $("[name=chk_facturas_exportar]").prop("checked", $(element).checked);
}


$.fn.dataTableExt.oApi.fnStandingRedraw = function(oSettings) {
    if(oSettings.oFeatures.bServerSide === false){
        var before = oSettings._iDisplayStart;
        oSettings.oApi._fnReDraw(oSettings);
        oSettings._iDisplayStart = before;
        oSettings.oApi._fnCalculateEnd(oSettings);
    }
    oSettings.oApi._fnDraw(oSettings);
};