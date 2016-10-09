var oTable;
var myTimer;

$(document).ready(function() {
    var thead = [];
    var data = '';
    var claves = Array(
            "codigo", "estado", "nueva_nc","observaciones","motivo_nc",
            "descripcion", "importe", "valor", "anulado",
            "pendiente", "confirmado", "Facturas",
            "errores", "error", "imputaciones", "no_tiene_imputaciones",
            "ERROR", "BIEN", "detalle",
            "estados", "validacion_ok", "fecha", "usuario");
    $.ajax({
        url: BASE_URL + 'entorno/getLang',
        data: "claves=" + JSON.stringify(claves),
        dataType: 'JSON',
        type: 'POST',
        cache: false,
        async: false,
        success: function(respuesta) {
            lang = respuesta;
            $.ajax({//MENU'S
                url: BASE_URL + 'entorno/getMenuJSON',
                data: 'seccion=notascredito',
                dataType: 'JSON',
                type: 'POST',
                cache: false,
                async: true,
                success: function(respuesta) {
                    menu = respuesta;
                    $.ajax({//NOMBRE Y ORDEN DE COLUMNAS 
                        url: BASE_URL + 'notascredito/getColumns',
                        data: '',
                        dataType: 'JSON',
                        type: 'POST',
                        cache: false,
                        async: false,
                        success: function(respuesta) {
                            aoColumnDefs = respuesta;
                            init();
                            $("body").on("click", ".boton-primario", function() {
                                var accion = $(this).attr('accion');
                                switch (accion) {
                                    case 'nueva_nota_credito':
                                        nueva_nota_credito();
                                        break;
                                }
                            });

                            $('body').on('click', '.btn-group .dropdown-menu li a', function() {

                                var accion = $(this).attr('accion');

                                var id = $(this).attr('id');
                                $('#menu').remove();
                                switch (accion) {


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
        var boton_nueva_nc = [{habilitado: 1, accion: 'nueva_nota_credito', text: lang['nueva_nc']}];
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


        oTable = $('#administracionNC').dataTable({
            "aaSorting": [[0, "desc"]],
            "bServerSide": true,
            "bLengthChange": true,
            "bFilter": true,
            "sAjaxSource": BASE_URL + "notascredito/listar",
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


                $('#administracionNC tbody tr .verDetalle').each(function() {
                    $(this).popover({
                        placement: 'left',
                        title: lang.detalle,
                        html: true,
                        trigger: 'manual'
                    });
                });

            },
            "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                var imgTag = devolverEstado(aData[5]);

                $(nRow).find('td').eq(5).html(imgTag);
                return nRow;
            },
            "aoColumnDefs": aoColumnDefs
        });
        marcarTr();
        $(".dataTables_length").html(generarBotonSuperiorMenu(boton_nueva_nc, "btn-primary", "icon-credit-card"));


        // CAPTURA DEL EVENTO CLICK DERECHO:
        codigo = '';
        var baja = '';
        $('#areaTablas').on('mousedown', '#administracionNC tbody tr', function(e) {

            var sData = oTable.fnGetData(this);
            var x = e.clientX;//CORDENADAS DEL MOUSE(averiguo la ubicacion para desplegar el menu)
            var y = e.clientY;
            // alert(x+' '+y);
            var nTds = $('td', this);
            var sBrowser = sData[1];
            var sGrade = sData[4];

            codigo = sData[columnName(lang['codigo'])];

            if (e.button === 2 && sData[5] != 'anulado') {

                generalContextMenu(menu.contextual, e);
                if (baja == 1) {
                    $('a[accion="cambiar-estado-facturas"]').text(lang['habilitar-factura']);
                } else {
                    $('a[accion="cambiar-estado-facturas"]').text(lang['deshabilitar-factura']);
                }

                if (sData[5] == 'anulado') {
                    $(menu.contextual).each(function(k, option) {

                        if (option.accion == 'confirmar_nc')
                        {
                            $("#menu a[accion=confirmar_nc]").closest("li").hide();
                        }
                        if (option.accion == 'imputar_nc')
                        {
                            $("#menu a[accion=imputar_nc]").closest("li").hide();
                        }
                        if (option.accion == 'anular_nc')
                        {
                            $("#menu a[accion=anular_nc]").closest("li").hide();
                        }

                    });
                }
                if (sData[5] == 'confirmado') {
                    $(menu.contextual).each(function(k, option) {

                        if (option.accion == 'confirmar_nc')
                        {
                            $("#menu a[accion=confirmar_nc]").closest("li").hide();
                        }

                    });
                }

                return false;
            }
        });

        $('#administracionNC').on('click', '.verDetalleDeudor', function() {
            $('.contenedorTabla').empty();
            botonMedio = this;
            if ($(botonMedio).parent().find('.popover').is(':visible')) {
                $('.btn').popover('hide');
            } else {
                $.ajax({
                    url: BASE_URL + 'notascredito/getDetallesNotaCredito',
                    type: 'POST',
                    data: 'codigo=' + codigo,
                    dataType: 'json',
                    cache: false,
                    success: function(respuesta) {
                        tablaDetalle = '<div class="col-md-12"> <h6 class="blue bigger">' + lang.motivo_nc + '</h6>';
                        tablaDetalle += '<label>' + respuesta.nc.motivo + '</label>'
                        tablaDetalle += '<div class="row"><div class="col-md-12"> <h6 class="blue bigger">' + lang.Facturas + '</h6>';
                        if (respuesta.facturas.length != 0) {
                            tablaDetalle += '<table class="table table-striped table-bordered"><thead>';
                            tablaDetalle += '<th>' + lang.descripcion + '</th><th>' + lang.importe + '</th>';
                            tablaDetalle += '<tbody>';

                            $(respuesta.facturas).each(function(key, fila) {
                                tablaDetalle += '<tr><td>' + fila.descripcion + '</td><td>' + fila.importe + '</td></tr>';
                            });
                            tablaDetalle += '<tbody></table>';
                            tablaDetalle += '</div></div>';
                        }
                        tablaDetalle += '<div class="row"><div class="col-md-12"> <h6 class="blue bigger">' + lang.imputaciones + '</h6>';
                        if (respuesta.imputaciones.length != 0) {
                            tablaDetalle += '<table class="table table-striped table-bordered"><thead>';
                            tablaDetalle += '<th>' + lang.descripcion + '</th><th>' + lang.importe + '</th><th>' + lang.estado + '</th>';
                            tablaDetalle += '<tbody>';
                            $(respuesta.imputaciones).each(function(key, fila) {
                                tablaDetalle += '<tr><td>' + fila.descripcion + '</td><td>' + fila.valorImputacion + '</td><td>' + fila.estado + '</td></tr>';
                            });
                            tablaDetalle += '<tbody></table>';

                        } else {
                            tablaDetalle += '<label>' + lang.no_tiene_imputaciones + '</label>';

                        }
                        tablaDetalle += '</div></div>';
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

        $('body').on('click', '#menu a', function() {
            var accion = $(this).attr('accion');
            // alert(accion);
            $('#menu').remove();
            switch (accion) {

//                case 'imprimir_recibo':
//                    var param = new Array();
//                    param.push(codigo);
//                    printers_jobs(10, param);
//                    break;
//
                case 'anular_nc':
                    $.ajax({
                        url: BASE_URL + 'notascredito/frm_anular',
                        data: 'codigo=' + codigo,
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

                case 'imputar_nc':
                    $.ajax({
                        url: BASE_URL + 'notascredito/frm_imputar',
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

                case 'confirmar_nc':
                    $.ajax({
                        url: BASE_URL + 'notascredito/frm_confirmar',
                        type: 'POST',
                        data: 'codigo=' + codigo,
                        //dataType: 'JSON',
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

function isJson(obj)
{
    try
    {
        var jsonObject = jQuery.parseJSON(obj);
    }
    catch (e)
    {
        return false;
        // handle error
    }

    return true;
}

function nueva_nota_credito() {
    $.ajax({
        url: BASE_URL + "notascredito/frm_notacredito",
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


   