var oTable;
var oTableBoleto;
var myTimer;
var resumen_ya_iniciado = false;
var total_importe = 0;
var total_imputado = 0;
var total_saldo = 0;
menu = BASE_MENU_JSON;

$(document).ready(function() {
    var thead = [];
    var data = '';
    var claves = Array(
            "codigo", "errores", "advertencias", "mediopago_cobro", "habilitar-factura", "deshabilitar-factura", "facturacion_codigo",
            "facturacion_estado", "facturacion_anular", "cobro_anulado_correctamente", "caja", "estado", "banco", "TARJETA",
            "tipo", "emisor", "nuevo-cobro", "fecha", "cuenta_corriente", "descripcion", "importe", "valor", "cuenta_nombre",
            "numero_transaccion", "respuesta", "no_hay_resultados", "anulado", "codigo_cupon", "codigo_autorizacion", "pendiente",
            "confirmado", "errores", "error", "imputaciones", "no_tiene_imputaciones", "ERROR", "BIEN", "detalle", "estados",
            "validacion_ok", "conta", "agencia", "debe_seleccionar_al_menos_un_items_de_cuenta_corriente", "arrastrar_para_subir",
            "fecha", "usuario", "importar_resumen", "mostrar", "registros", "todos"
        );
    $.ajax({
        url: BASE_URL + 'entorno/getLang',
        data: "claves=" + JSON.stringify(claves),
        dataType: 'JSON',
        type: 'POST',
        cache: false,
        async: false,
        success: function(respuesta) {
            lang = respuesta;
            $.ajax({
                url: BASE_URL + 'entorno/getMenuJSON',
                data: 'seccion=cobros',
                dataType: 'JSON',
                type: 'POST',
                cache: false,
                async: true,
                success: function(respuesta) {
                    menu = respuesta;
                    $.ajax({
                        url: BASE_URL + 'cobros/getColumns',
                        data: '',
                        dataType: 'JSON',
                        type: 'POST',
                        cache: false,
                        async: false,
                        success: function(respuesta) {
                            aoColumnDefs = respuesta;
                            init();
                            $("body").on("click", "#importarResumen", function() {
                                initImportarDatosResumen();
                            });
                            $("body").on("click", ".boton-primario", function() {
                                var accion = $(this).attr('accion');
                                switch (accion) {
                                    case 'nuevo-cobro':
                                        nuevo_cobro();
                                        break;
                                    case 'emitir-boleto':
                                        nuevo_boleto();
                                        break;
                                }
                            });

                            $('body').on('click', '.btn-group .dropdown-menu li a', function() {
                                var accion = $(this).attr('accion');
                                var id = $(this).attr('id');
                                $('#menu').remove();
                                switch (accion) {
                                    case 'cobros':
                                        $(".vista-cobro").removeClass("hide");
                                        break;
                                }
                                return false;
                            });
                        }
                    });
                }
            });
        }
    });

    function init() {
        function ejson(string) {
            try {
                JSON.parse(string);
            } catch (e) {
                return false;
            }
            return true;
        }

        function devolverEstado(valor) {
            var clase = '';
            var texto = '';
            switch (valor) {

                case 'anulado':
                    clase = 'label-inverse verDetalleDeudor';
                    texto = lang.anulado;
                    break;

                case 'pendiente':
                    clase = 'label-warning verDetalleDeudor';
                    texto = lang.pendiente;
                    break;

                case 'confirmado':
                    clase = 'label-success verDetalleDeudor';
                    texto = lang.confirmado;
                    break;

                case 'error':
                    clase = 'label-danger verDetalleDeudor';
                    texto = lang.error;
                    break;
            }

            var retorno = '<span data-alumno="4" class="label ' + clase + '  arrowed editable-click">' + texto + '</span>';
            return retorno;
        }

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

        columnaMedioPago = columnName(lang.mediopago_cobro);
        var _tfoot = '<tfoot>';
        _tfoot += '<tr>';
        _tfoot += '<td colspan="3" style="text-align: right;">';
        _tfoot += 'Total';
        _tfoot += '</td>';
        _tfoot += '<td name="tfoot_importe">';
        _tfoot += '</td>';
        _tfoot += '<td name="tfoot_imputado">';
        _tfoot += '</td>';
        _tfoot += '<td name="tfoot_saldo">';
        _tfoot += '</td>';
        _tfoot += '<td colspan="6">';
        _tfoot += '</td>';
        _tfoot += '</tr>';
        _tfoot += '</tfoot>';
        $("#administracionCobros").append(_tfoot);

        oTable = $('#administracionCobros').dataTable({
            oLanguage: {
                sLengthMenu: lang.mostrar + ' <select>' +
                    '<option value="10">10</option>' +
                    '<option value="20">20</option>' +
                    '<option value="30">30</option>' +
                    '<option value="40">40</option>' +
                    '<option value="50">50</option>' +
                    '<option value="-1">' + lang.todos + '</option>'+
                    '</select> ' + lang.registros
            },
            aaSorting: [[8, "ASC"]],
            bServerSide: true,
            bLengthChange: true,
            bFilter: true,
            sAjaxSource: BASE_URL + "cobros/listar",
            sServerMethod: "POST",
            bStateSave: false,            
            aoColumnDefs: aoColumnDefs,
            pagingType: "full_numbers",
            fnServerData: function(sSource, aoData, fnCallback) {
                $("[name=tfoot_importe]").html("0.00");
                $("[name=tfoot_imputado]").html("0.00");
                $("[name=tfoot_saldo]").html("0.00");
                total_importe = 0;
                total_imputado = 0;
                total_saldo = 0;
                var fecha_desde = $("[name=filtro_fecha_desde]").val();
                var fecha_hasta = $("[name=filtro_fecha_hasta]").val();
                var periodo_mes = $("[name=filtro_periodo_mes]").val();
                var periodo_anio = $("[name=filtro_periodo_anio]").val();
                var estado = $("[name=filtro_estado]").val();
                var medio_pago = $("[name=filtro_medio]").val();
                var caja = $("[name=filtro_caja]").val();
                var saldo = $("[name=filtro_saldo]").val();
                var tipo_filtro_fecha = $("[name=filtro_tipo_fecha]").val();
                if (tipo_filtro_fecha == 'fecha'){
                    aoData.push({name: "fecha_desde_t", value: fecha_desde});
                    aoData.push({name: "fecha_hasta_t", value: fecha_hasta});
                } else if (tipo_filtro_fecha == 'periodo'){
                    aoData.push({name: "periodo_mes", value: periodo_mes});
                    aoData.push({name: "periodo_anio", value: periodo_anio});
                }
                aoData.push({name: "selectEstado", value: estado});
                aoData.push({name: "medio_pago", value: medio_pago});
                aoData.push({name: "caja", value: caja});
                aoData.push({name: "saldo", value: saldo});
                $.ajax({
                    dataType: 'json',
                    type: "POST",
                    url: sSource,
                    data: aoData,
                    async: true,
                    success: function(_json){
                        fnCallback(_json);
                    }
                });

                $('#administracionCobros tbody tr .verDetalle').each(function() {
                    $(this).popover({
                        placement: 'left',
                        title: lang.detalle,
                        html: true,
                        trigger: 'manual'
                    });
                });

                $('#administracionCobros tbody tr .Medios').each(function() {
                    $(this).popover({
                        placement: 'left',
                        title: lang.detalle,
                        html: true,
                        trigger: 'manual'
                    });
                });
            },
            fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                total_importe += parseFloat(aData[13]);
                total_imputado += parseFloat(aData[14]);
                total_saldo += parseFloat(aData[15]);
                $("[name=tfoot_importe]").html(moneda_simbolo + ' ' + total_importe.toFixed(2).replace('.', separador_decimal));
                $("[name=tfoot_imputado]").html(moneda_simbolo + ' ' + total_imputado.toFixed(2).replace('.', separador_decimal));
                $("[name=tfoot_saldo]").html(moneda_simbolo + ' ' + total_saldo.toFixed(2).replace('.', separador_decimal));
                var imgTag = devolverEstado(aData[10]);
                if (aData[12]) {
                    $(nRow).find('td').eq(6).html('<a href="" class="Medios editable-click" data-content="">' + $(nRow).find('td').eq(6).text() + '</a>');
                }
                $(nRow).find('td').eq(10).html(imgTag);
                return nRow;
            }
        });
        marcarTr();
        $(".dataTables_length").append("&nbsp;&nbsp;");
        $(".dataTables_length").append(generarBotonSuperiorMenu(menu.superior, "btn-primary", "icon-credit-card"));
        if (tiene_proveedores){
            $(".dataTables_length").append('<button class="btn btn-link" id="importarResumen">' + lang.importar_resumen + '</button>');
        }
        
        $("#administracionCobros_filter").find("label").addClass("input-icon input-icon-right");
        $("#administracionCobros_filter").find("label").append('<i name="icon_filters" class="icon-caret-down grey" style="margin-right: 7px; cursor: pointer"></i>');
        $("#administracionCobros_filter").append($("[name=container_menu_filters_temp]").html());
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
        
        $("#administracionCobros_filter").append('<i id="exportar_informe" class="icon-print grey" onclick="exportar_informe(\'pdf\');" style="cursor: pointer; margin-left: 6px;" data-original-title="" title="exportar"></i>');
        $("#administracionCobros_filter").append('<i id="exportar_informe" class="icon-external-link" onclick="exportar_informe(\'csv\');" style="cursor: pointer; margin-left: 6px;" data-original-title="" title="exportar"></i>');

        codigo = '';
        var baja = '';
        $('#areaTablas').on('mousedown', '#administracionCobros tbody tr', function(e) {
            var sData = oTable.fnGetData(this);
            estado = sData[10];
            var x = e.clientX;
            var y = e.clientY;
            var nTds = $('td', this);
            var sBrowser = sData[1];
            var sGrade = sData[5];
            codigo = sData[0];
            if (e.button === 2) {
                generalContextMenu(menu.contextual, e);
                switch (estado) {
                    case 'confirmado':
                         $(menu.contextual).each(function(k, option) {
                            if (option.accion == 'confirmar_cobro'){
                                $("#menu a[accion=confirmar_cobro]").closest("li").hide();
                            }
                        });
                        break;
                        
                    case 'anulado':
                        $(menu.contextual).each(function(k, option) {
                            if (option.accion == 'imprimir_recibo'){
                                $("#menu a[accion=imprimir_recibo]").closest("li").hide();
                            }
                            if (option.accion == 'modificar_cobro'){
                                $("#menu a[accion=modificar_cobro]").closest("li").hide();
                            }
                            if (option.accion == 'cambiar-estado-cobro'){
                                $("#menu a[accion=cambiar-estado-cobro]").closest("li").hide();
                            }
                            if (option.accion == 'confirmar_cobro'){
                                $("#menu a[accion=confirmar_cobro]").closest("li").hide();
                            }
                        });
                        $("#menu").hide();
                        break;
                        
                    case 'error':
                        $(menu.contextual).each(function(k, option) {
                            if (option.accion == 'imprimir_recibo'){
                                $("#menu a[accion=imprimir_recibo]").closest("li").hide();
                            }
                            if (option.accion == 'confirmar_cobro'){
                                $("#menu a[accion=confirmar_cobro]").closest("li").hide();
                            }
                        });
                        break;
                        
                    case 'pendiente':
                        $(menu.contextual).each(function(k, option) {
                            if (option.accion == 'imprimir_recibo'){
                                $("#menu a[accion=imprimir_recibo]").closest("li").hide();
                            }
                        });
                        break;
                }            
                return false;
            }
        });
        
        $('#administracionCobros').on('click', '.Medios', function() {
            $.ajax({
                url: BASE_URL + 'cobros/getDetallesMedio',
                type: 'POST',
                data: 'cod_cobro=' + codigo,
                dataType: 'json',
                cache: false,
                success: function(respuesta){
                    tablaDetalle = '<div class="row"><div class="col-md-12"><table class="table table-striped table-bordered"><thead>';
                    switch (respuesta[0].cod_medio) {
                        case '1':
                            tablaDetalle += '<th>' + lang.caja + '</th>';
                            tablaDetalle += '<tbody>';
                            tablaDetalle += '<tr><td>' + respuesta[0].nombreCaja + '</td></tr>';
                            break;

                        case '2':
                            tablaDetalle += '<th>' + lang.fecha + '</th><th>' + lang.conta + '</th><th>' + lang.banco + '</th><th>' + lang.agencia + '</th>';
                            tablaDetalle += '<tbody>';
                            tablaDetalle += '<tr><td>' + respuesta[0].fecha_documento + '</td><td>' + respuesta[0].numero_cuenta + '</td><td>' + respuesta[0].nombre_banco + '</td><td>' + respuesta[0].agencia + '</td></tr>';
                            break;

                        case '3':
                            tablaDetalle += '<th>' + lang.TARJETA + '</th><th>' + lang.codigo_cupon + '</th><th>' + lang.codigo_autorizacion + '</th>';
                            tablaDetalle += '<tbody>';
                            tablaDetalle += '<tr><td>' + respuesta[0].nombreTarj + '</td><td>' + respuesta[0].cupon + '</td><td>' + respuesta[0].autorizacion + '</td></tr>';
                            break;

                        case '4':
                            tablaDetalle += '<th>' + lang.tipo + '</th><th>' + lang.banco + '</th><th>' + lang.emisor + '</th><th>' + lang.fecha + '</th>';
                            tablaDetalle += '<tbody>';
                            tablaDetalle += '<tr><td>' + respuesta[0].nombre_cheque[0]['nombre'] + '</td><td>' + respuesta[0].nombre + '</td><td>' + respuesta[0].emisor + '</td><td>' + respuesta[0].fecha_cobro + '</td></tr>';
                            break;

                        case '5':
                            tablaDetalle += '<th>' + lang.cuenta_corriente + '</th><th>' + lang.descripcion + '</th><th>' + lang.valor + '</th>';
                            tablaDetalle += '<tbody>';
                            tablaDetalle += '<tr><td>' + respuesta[0].cod_cta_cte + '</td><td>' + respuesta[0].descripcion + '</td><td>' + respuesta[0].importeformateado + '</td></tr>';
                            break;

                        case '6':
                            tablaDetalle += '<th>' + lang.cuenta_nombre + '</th><th>' + lang.banco + '</th><th>' + lang.numero_transaccion + '</th>';
                            tablaDetalle += '<tbody>';
                            tablaDetalle += '<tr><td>' + respuesta[0].cuenta_nombre + '</td><td>' + respuesta[0].nombre + '</td><td>' + respuesta[0].nro_transaccion + '</td></tr>';
                            break;

                        case '7':
                            tablaDetalle += '<th>' + lang.cuenta_nombre + '</th><th>' + lang.banco + '</th><th>' + lang.numero_transaccion + '</th>';
                            tablaDetalle += '<tbody>';
                            tablaDetalle += '<tr><td>' + respuesta[0].cuenta_nombre + '</td><td>' + respuesta[0].nombre + '</td><td>' + respuesta[0].nro_transaccion + '</td></tr>';
                            break;
                            
                        case '8':
                            tablaDetalle += '<th>' + lang.debito + '</th><th>' + lang.codigo_cupon + '</th><th>' + lang.codigo_autorizacion + '</th>';
                            tablaDetalle += '<tbody>';
                            tablaDetalle += '<tr><td>' + respuesta[0].nombreTarj + '</td><td>' + respuesta[0].cupon + '</td><td>' + respuesta[0].autorizacion + '</td></tr>';
                            break;

                        default:
                            tablaDetalle += '<th>' + lang.respuesta + '</th><tbody><tr><td>' + lang.no_hay_resultados + '</td></tr>';
                            break;
                    }
                    tablaDetalle += '<tbody></table></div></div>';
                    $('.contenedorTabla').html(tablaDetalle);
                    $('#modalDtalle').modal();
                }
            });
            return false;
        });
        $('#administracionCobros').on('click', '.verDetalleDeudor', function(){
            botonMedio = this;
            if ($(botonMedio).parent().find('.popover').is(':visible')){
                $('.btn').popover('hide');
            } else {
                $.ajax({
                    url: BASE_URL + 'cobros/getDetallesCobro',
                    type: 'POST',
                    data: 'codigo=' + codigo,
                    dataType: 'json',
                    cache: false,
                    success: function(respuesta) {
                        tablaDetalle = '<div class="row"><div class="col-md-12"> <h6 class="blue bigger">' + lang.imputaciones + '</h6>';
                        if (respuesta.imputacionesCobro.length != 0) {
                            tablaDetalle += '<table class="table table-striped table-bordered"><thead>';
                            tablaDetalle += '<th>' + lang.descripcion + '</th><th>' + lang.importe + '</th><th>' + lang.estado + '</th>';
                            tablaDetalle += '<tbody>';
                            $(respuesta.imputacionesCobro).each(function(key, fila) {
                                tablaDetalle += '<tr><td>' + fila.descripcion + '</td><td>' + fila.valorImputacion + '</td><td>' + fila.estado + '</td></tr>';
                            });
                            tablaDetalle += '<tbody></table>';
                        } else {
                            tablaDetalle += '<label>' + lang.no_tiene_imputaciones + '</label>';
                        }
                        tablaDetalle += '</div></div>';
                        if (respuesta.errores.length != 0) {
                            tablaDetalle += '<div class="row"><div class="col-md-12"> <h6 class="blue bigger">' + lang.errores + '</h6>';
                            tablaDetalle += '<table class="table table-striped table-bordered"><thead>';
                            tablaDetalle += '<th>' + lang.descripcion + '</th>';
                            tablaDetalle += '<tbody>';
                            $(respuesta.errores).each(function(key, fila) {
                                tablaDetalle += '<tr><td>' + fila.error + '</td></tr>';
                            });
                            tablaDetalle += '<tbody></table>';
                            tablaDetalle += '</div></div>';
                        }
                        if (respuesta.historico.length != 0) {
                            tablaDetalle += '<div class="row"><div class="col-md-12"> <h6 class="blue bigger">' + lang.estados + '</h6>';
                            tablaDetalle += '<table class="table table-striped table-bordered"><thead>';
                            tablaDetalle += '<th>' + lang.fecha + '</th><th>' + lang.estado + '</th><th>' + lang.usuario + '</th>';
                            tablaDetalle += '<tbody>';
                            $(respuesta.historico).each(function(key, fila) {
                                tablaDetalle += '<tr><td>' + fila.fecha + '</td><td>' + fila.estado + '</td><td>' + fila.usuario + '</td></tr>';
                            });
                            tablaDetalle += '<tbody></table>';
                            tablaDetalle += '</div></div>';
                        }
                        $('.contenedorTabla').html(tablaDetalle);
                        $('#modalDtalle').modal();
                    }
                });
            }
            return false;
        });
        $('body').on('click', '#menu a', function(){
            var accion = $(this).attr('accion');
            $('#menu').remove();
            switch (accion) {
                
                case 'imprimir_recibo':
                    var param = new Array();
                    param.push(codigo);
                    printers_jobs(10, param);
                    break;
                    
                case 'cambiar-estado-cobro':
                    $.ajax({
                        url: BASE_URL + 'cobros/frm_baja',
                        data: 'cod_cobro=' + codigo,
                        type: 'POST',
                        cache: false,
                        success: function(respuesta) {
                            if (isJson(respuesta)) {
                                var res = JSON.parse(respuesta);
                                $.gritter.add({
                                    title: lang.ERROR,
                                    text: res.errors,
                                    sticky: false,
                                    time: '3000',
                                    class_name: 'gritter-error'
                                });
                            } else {
                                $.fancybox.open(respuesta, {
                                    width: '50%',
                                    height: 'auto',
                                    scrolling: 'auto',
                                    autoSize: false,
                                    autoResize: false,
                                    padding: 0,
                                    openEffect: 'none',
                                    closeEffect: 'none',
                                    helpers: {
                                        overlay: null
                                    }
                                });
                            }
                        }
                    });
                    break;

                case 'modificar_cobro':
                    $.ajax({
                        url: BASE_URL + 'cobros/frm_cobros',
                        type: 'POST',
                        data: 'codigo=' + codigo,
                        cache: false,
                        success: function(respuesta) {
                            if (isJson(respuesta)) {
                                var res = JSON.parse(respuesta);
                                $.gritter.add({
                                    title: lang.ERROR,
                                    text: res.errors,
                                    sticky: false,
                                    time: '3000',
                                    class_name: 'gritter-error'
                                });
                            } else {
                                $.fancybox.open(respuesta, {
                                    autoSize: false,
                                    width: '70%',
                                    height: 'auto',
                                    scrolling: 'auto',
                                    openEffect: 'none',
                                    closeEffect: 'none',
                                    padding: 1,
                                    helpers: {
                                        overlay: null
                                    }
                                });
                            }
                        }
                    });
                    break;

                case 'confirmar_cobro':
                    $.ajax({
                        url: BASE_URL + 'cobros/frm_confirmar_cobro',
                        type: 'POST',
                        data: 'codigo=' + codigo,
                        cache: false,
                        success: function(respuesta) {
                            if (isJson(respuesta)) {
                                var res = JSON.parse(respuesta);
                                $.gritter.add({
                                    title: lang.ERROR,
                                    text: res.errors,
                                    sticky: false,
                                    time: '3000',
                                    class_name: 'gritter-error'
                                });
                            } else {
                                $.fancybox.open(respuesta, {
                                    autoSize: false,
                                    width: '80%',
                                    height: 'auto',
                                    scrolling: 'auto',
                                    padding: 0,
                                    openEffect: 'none',
                                    closeEffect: 'none',
                                    helpers: {
                                        overlay: null
                                    }
                                });
                            }
                        }
                    });
                    break;
            }
            return false;
        });
    }
});

function isJson(obj){
    try {
        var jsonObject = jQuery.parseJSON(obj);
    } catch (e) {
        return false;
    }
    return true;
}

function nuevo_cobro() {
    $.ajax({
        url: BASE_URL + "cobros/frm_cobros",
        data: '',
        type: 'POST',
        cache: false,
        success: function(respuesta) {
            $.fancybox.open(respuesta, {
                autoSize: false,
                width: '70%',
                height: 'auto',
                scrolling: 'auto',
                openEffect: 'none',
                closeEffect: 'none',
                padding: 1,
                helpers: {
                    overlay: null
                }
            });
        }
    });
}

function initImportarDatosResumen() {
    if (!resumen_ya_iniciado){
        resumen_ya_iniciado = true;
        $('#ArchivoResumenesCargados').dataTable({
            "bFilter": true,
            "aaSorting": [[2, "desc"]],
            "bServerSide": true,
            "sAjaxSource": BASE_URL + "facturantes/getResumenesCobros",
            "sServerMethod": "POST",
            "fnServerData": function(sSource, aoData, fnCallback) {
                $.ajax({
                    "dataType": 'json',
                    "type": "POST",
                    "url": sSource,
                    "data": aoData,
                    "async": true,
                    "success": fnCallback
                });
            },
            "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                return nRow;
            }
        });
    }
    $(".vista-cobro").addClass("hide");
    $(".vista-archivos-consolidar").removeClass("hide");
    jQuery(function($) {
        var $form = $('#subir-resumen');
        var file_input = $form.find('input[type=file]');
        var upload_in_progress = false;
        file_input.ace_file_input({
            style: 'well',
            btn_choose: lang.arrastrar_para_subir,
            btn_change: null,
            droppable: true,
            thumbnail: 'large',
            maxSize: 110000,
            allowExt: ["rem"],
            allowMime: ["text/plain"],
            before_remove: function() {
                if (upload_in_progress)
                    return false; 
                return true;
            },
            preview_error: function(filename, code) {}
        });
        file_input.on('file.error.ace', function(ev, info){});
    });
    $("#subir-resumen").submit(function() {
        return false;
    });
    $('.btn-confirmar-resumen').click(function() {
        var formData = new FormData($("#subir-resumen")[0]);
        $.ajax({
            url: BASE_URL + 'facturantes/sendResumenCobros',
            type: 'POST',
            data: formData,
            mimeType: "multipart/form-data",
            dataType: 'json',
            async: true,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function() {
            },
            success: function(data) {
                $(".subir-archivo").addClass("hide");
                $(".resultado-subir-archivo").removeClass("hide");
                var _htmlCorrectos = '';
                var _htmlErrorAlto = '';
                var _htmlErrorMedio = '';
                var _htmlErrorBajo = '';
                var cantidadErroresMedio = 0;
                var cantidadErroresAlto = 0;
                var cantidadErroresBajo = 0;
                $(data).each(function(key, fila) {
                    if (fila.respuesta == true) {
                        _htmlCorrectos += '<div class="row">'; 
                        _htmlCorrectos += '<div class="col-md-9">'; 
                        _htmlCorrectos += '<i class="icon-circle green"></i>';
                        _htmlCorrectos += '(' + fila.archivo + ') ' + fila.error;
                        _htmlCorrectos += '</div>';
                        _htmlCorrectos += '</div>';
                    } else {
                        if (fila.prioridad == 'alta'){
                            cantidadErroresAlto ++;
                            _htmlErrorAlto += '<tr>';
                            _htmlErrorAlto += '<td>';
                            _htmlErrorAlto += '<i class="icon-remove bigger-110 red"></i>'; 
                            _htmlErrorAlto += '(' + fila.archivo + ') ' + fila.error;
                            _htmlErrorAlto += '</td>';
                            _htmlErrorAlto += '</tr>';
                        } else if (fila.prioridad == 'media'){
                            cantidadErroresMedio ++;
                            _htmlErrorMedio += '<tr>';
                            _htmlErrorMedio += '<td>';
                            _htmlErrorMedio += '<i class="icon-remove bigger-110 yellow" style="color: yellow;"></i>';
                            _htmlErrorMedio += '(' + fila.archivo + ')' + fila.error;
                            _htmlErrorAlto += '</td>';
                            _htmlErrorAlto += '</tr>';
                        } else {
                            cantidadErroresBajo ++;
                            _htmlErrorBajo += '<tr>';
                            _htmlErrorBajo += '<td>';
                            _htmlErrorBajo += '<i class="icon-remove bigger-110 green"></i>';
                            _htmlErrorBajo += '(' + fila.archivo + ')' + fila.error;
                            _htmlErrorBajo += '</td>';
                            _htmlErrorBajo += '</tr>';
                        }                        
                    }
                });
                $(".resultado-subida").html("");
                if (_htmlCorrectos != ''){                   
                    $(".resultado-subida").append(_htmlCorrectos);
                }
                if (_htmlErrorBajo != ''){
                    $(".resultado-subida").append(_htmlErrorBajo);
                }
                if (_htmlErrorMedio != ''){
                    var _html = '<div class="row">';
                    _html += '<div class="col-md-9" style="cursor: pointer;" onclick="ver_ocultar_advertencias();">';
                    _html += '<span style="font-weight: bold; color: grey">Advertencias' + '&nbsp;&nbsp(' + cantidadErroresMedio + ')</span>';
                    _html += '</div>';
                    _html += '</div>';
                    _html += '<div class="row">';
                    _html += '<div class="col-md-9">';
                    _html += '<table style="display: none; margin-left: 14px;" name="div_prioridad_media">';
                    _html += _htmlErrorMedio; 
                    _html += '</table>';
                    _html += '</div>';
                    _html += '</div>';
                    $(".resultado-subida").append(_html);
                }
                if (_htmlErrorAlto != ''){
                    var _html = '<div class="row">';
                    _html += '<div class="col-md-9" style="cursor: pointer" onclick="ver_ocultar_errores();">';
                    _html += '<span style="font-weight: bold; color: grey;">Errores' + '&nbsp;&nbsp;(' + cantidadErroresAlto + ')</span>';
                    _html += '</div>';
                    _html += '</div>';
                    _html += '<div class="row">';
                    _html += '<div class="col-md-9">';
                    _html += '<table style="display: none; margin-left: 14px;" name="div_prioridad_alta">';
                    _html += _htmlErrorAlto;
                    _html += '</table>';
                    _html += '</div>';
                    _html += '</div>';                    
                    $(".resultado-subida").append(_html) ;
                }
            },
            error: function() {
            }
        });
    });

    $('.ok-reset').click(function() {
        resetResumenes();
    });
}

function resetResumenes() {
    $('#file-retorno').ace_file_input('reset_input');
    $(".subir-archivo").removeClass("hide");
    $(".resultado-subir-archivo").addClass("hide");
    $(".resultado-subida").html("");
}

function buscarCobro(element, event) {
    clearTimeout(myTimer);
    myTimer = setTimeout(function() {
        oTable.fnFilter($(element).val());
    }, 800);
    event.preventDefault();
}

function volver() {
    $(".vista-cobro").removeClass("hide");
    $(".vista-archivos-consolidar").addClass("hide");
}

function ver_ocultar_advertencias(){
    $("[name=div_prioridad_media]").toggle();
}

function ver_ocultar_errores(){
    $("[name=div_prioridad_alta]").toggle();
}

function listar(){
    oTable.fnDraw();
}

function filtro_tipo_fecha_change(){
    var valor = $("[name=filtro_tipo_fecha]").val();
    if (valor == 'sin_fechas'){
        $("[name=filtro_tipo_fecha_periodo]").hide();
        $("[name=filtro_tipo_fecha_fecha]").hide();
    } else if (valor == 'periodo'){
        $("[name=filtro_tipo_fecha_periodo]").show();
        $("[name=filtro_tipo_fecha_fecha]").hide();
    } else {
        $("[name=filtro_tipo_fecha_periodo]").hide();
        $("[name=filtro_tipo_fecha_fecha]").show();
    }
}

function exportar_informe(formato){
    var iSortCol_0 = oTable.fnSettings().aaSorting[0][0];
    var sSortDir_0 = oTable.fnSettings().aaSorting[0][1];
    var iDisplayLength = oTable.fnSettings()._iDisplayLength;
    var iDisplayStart = oTable.fnSettings()._iDisplayStart;
    var sSearch = $("#administracionCobros_filter").find("input[type=search]").val();
    var fecha_desde = $("[name=filtro_fecha_desde]").val();
    var fecha_hasta = $("[name=filtro_fecha_hasta]").val();
    var periodo_mes = $("[name=filtro_periodo_mes]").val();
    var periodo_anio = $("[name=filtro_periodo_anio]").val();
    var estado = $("[name=filtro_estado]").val();
    var medio_pago = $("[name=filtro_medio]").val();
    var caja = $("[name=filtro_caja]").val();
    var saldo = $("[name=filtro_saldo]").val();
    var tipo_filtro_fecha = $("[name=filtro_tipo_fecha]").val();
    
    if (tipo_filtro_fecha == 'fecha'){
        $("[name=exportar]").find("[name=fecha_desde_t]").val(fecha_desde);
        $("[name=exportar]").find("[name=fecha_hasta_t]").val(fecha_hasta);
    } else if (tipo_filtro_fecha == 'periodo'){
        $("[name=exportar]").find("[name=periodo_mes]").val(periodo_mes);
        $("[name=exportar]").find("[name=periodo_anio]").val(periodo_anio);
    }
    $("[name=exportar]").find("[name=selectEstado]").val(estado);
    $("[name=exportar]").find("[name=medio_pago]").val(medio_pago);
    $("[name=exportar]").find("[name=caja]").val(caja);
    $("[name=exportar]").find("[name=saldo]").val(saldo);    
    $("[name=exportar]").find("[name=tipo_reporte]").val(formato);
    $("[name=exportar]").find("[name=iSortCol_0]").val(iSortCol_0);
    $("[name=exportar]").find("[name=sSortDir_0]").val(sSortDir_0);
    $("[name=exportar]").find("[name=iDisplayLength]").val(iDisplayLength);
    $("[name=exportar]").find("[name=iDisplayStart]").val(iDisplayStart);
    $("[name=exportar]").find("[name=sSearch]").val(sSearch);
    $("[name=exportar]").submit();
}
