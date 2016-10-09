function guardarValorSugerencia(element) {
    var name = $(element).attr('name');
    var valor = $(element).val();
    $.ajax({
        url: BASE_URL + "configuracion/guardarConfiguracionSugerencia",
        type: "POST",
        data: {'nombre': name, 'valor': valor},
        dataType: "JSON",
        cache: false,
        success: function (respuesta) {
            if (respuesta.codigo) {
                gritter(lang.validacion_ok, true);
            } else {
                gritter(lang.ERROR, false);
            }
        }
    });
}

lang = BASE_LANG;

function tablaImpuestos() {
    var impuestos = '';
    $.ajax({
        url: BASE_URL + "configuracion/getImpuestos",
        type: "POST",
        data: "",
        dataType: "JSON",
        cache: false,
        async: false,
        success: function (respuesta) {
            impuestos = respuesta;
        }
    });

    var tabla = '<table id="tablaImpuestos" class="table table-striped table-bordered"><thead>';
    tabla += '<th>' + lang.codigo + '</th><th>' + lang.nombre + '</th><th>' + lang.valor + '</th><th>' + lang.detalle + '</th></thead>';
    tabla += '<tbody>';

    $(impuestos).each(function (k, item) {
        tabla += '<tr>';
        tabla += '<td>';
        tabla += item.codigo;
        tabla += '</td>';
        tabla += '<td>';
        tabla += item.nombre;
        tabla += '</td>';
        tabla += '<td>';
        tabla += item.valor;
        tabla += '</td>';
        tabla += '<td>';
        tabla += '<span class="label label-success arrowed" data-detalle="' + item.codigo + '">';
        tabla += lang.ver_editar;
        tabla += '</span>';
        tabla += '</td>';
        tabla += '</tr>';
    });
    tabla += '</tbody></tabla>';
    $('#widgetImpuesto .table-responsive').html(tabla);
}

function tablaPuntosVentas(valor) {
    var estado = valor == 'activo' ? '' : '1';
    var puntosVentas = '';
    $.ajax({
        url: BASE_URL + "configuracion/getPuntosVentas",
        type: "POST",
        data: "estado=" + estado,
        dataType: "JSON",
        cache: false,
        async: false,
        success: function (respuesta) {
            puntosVentas = respuesta;
        }
    });

    var tabla = '<table id="tablaPuntosVentas' + valor + '" class="table table-striped table-bordered">';
    tabla += '<thead>';
    tabla += '<th>';
    tabla += lang.puntos_venta;
    tabla += '</th>';
    tabla += '<th>';
    tabla += lang.razon_social;
    tabla += '</th>';
    tabla += '<th>';
    tabla += lang.Facturas;
    tabla += '</th>';
    tabla += '<th>';
    tabla += lang.ultimo_numero;
    tabla += '</th>';
    tabla += '<th>';
    tabla += lang.detalle;
    tabla += '</th>';
    tabla += '</thead>';
    tabla += '<tbody>';

    $(puntosVentas).each(function (k, item) {
        var data = {
            'punto_venta': item.prefijo,
            'cod_tipo_factura': item.cod_tipo_factura,
            'cod_facturante': item.cod_facturante
        };
        tabla += "<tr>";
        tabla += "<td>";
        tabla += item.prefijo;
        tabla += "</td>";
        tabla += "<td>";
        tabla += item.razon_social;
        tabla += "</td>";
        tabla += "<td>";
        tabla += item.factura;
        tabla += "</td>";
        tabla += "<td>";
        tabla += item.ultimonumero;
        tabla += "</td>";
        tabla += "<td>";
//        if (item.cantidad_filiales == 1) {
        tabla += "<span class='label label-success arrowed' data-detalle='" + item.punto_venta + "'>";
        tabla += lang.ver_editar;
        tabla += "</span>";
//        } else {
        tabla += "&nbsp;";
//        }
        tabla += "</td>";
        tabla += "</tr>";
    });
    tabla += '</tbody>';
    tabla += '</tabla>';
    $('#widgetPuntosVentas #' + valor + ' .table-responsive').html(tabla);
}

function tablaFacturantes() {
    var facturantes = '';
    $.ajax({
        url: BASE_URL + "configuracion/getFacturantes",
        type: "POST",
        data: "",
        dataType: "JSON",
        cache: false,
        async: false,
        success: function (respuesta) {
            facturantes = respuesta;
        }
    });

    var pais_argentina = false;
    $(facturantes).each(function (k, item) {
        if (item.cod_pais == '1') {
            pais_argentina = true;
        }
    });

    var tabla = '<table id="tablaFacturantes" class="table table-striped table-bordered"><thead>';
    tabla += '<th>' + lang.documento + '</th>' + '</th><th>' + lang.razon_social + '</th><th>' + lang.inicio_de_actividades + '</th><th>' + 'Certificado Fact. Electrónica' + '</th>';
    if (pais_argentina) {
        tabla += '<th>' + 'Descarga Certificado Afip' + '</th><th>' + 'Subir Certificado Afip' + '</th>'
    }
    tabla += '</thead>';
    tabla += '<tbody>';

    $(facturantes).each(function (k, item) {
        var data = {
            'cod_razon_social': item.codigo,
            'cod_facturante': item.codigofacturante
        };
        tabla += "<tr><td>" + item.documento + "</td><td>" + item.razon_social + "</td><td>" + item.inicio_actividades + "</td>";

        if (item.tiene_certificado) {
            var deshabilitar = '';
            if (item.cod_pais != '1') {
                deshabilitar = 'disabled="true"';
            }
            tabla += "<td>" + '<button ' + deshabilitar + ' class="btn btn-xs" type="button" name="infoCertificado" onClick="infoCertificado(' + item.codigofacturante + ')">Ver Info</button>' + "</td>";
        } else {
            tabla += "<td>" + '-' + "</td>";
        }


        if (item.cod_pais == '1') {
            //descarga certificado afip
            var link_descarga = BASE_URL + "configuracion/descargarCsrFacturante";
            tabla += '<td>';
            tabla += '<FORM action="' + link_descarga + '" method="post">';
            tabla += '<INPUT type="hidden" name="facturante" value="' + item.codigofacturante + '">';
            tabla += '<INPUT class="btn btn-xs" type="submit" value="Descargar">';
            tabla += '</FORM>';
            tabla += '</td>';
            //sube certificado afip
            var link_subir = BASE_URL + "configuracion/subirCrtFacturante";
            tabla += '<td>';
            tabla += '<FORM action="' + link_subir + '" method="post" id="frm_subir_' + item.codigofacturante + '" enctype="multipart/form-data">';
            tabla += '<INPUT type="hidden" name="facturante" value="' + item.codigofacturante + '">';
            tabla += '<INPUT type="file" accept=".crt" id="crtfile_' + item.codigofacturante + '" name="crtfile" style="display: none;" onchange="subircertificado(' + item.codigofacturante + ');">';// $(\'#frm_subir_' + item.codigofacturante + '\').submit();">';
            tabla += '<INPUT class="btn btn-xs" type="button" onClick="$(\'#crtfile_' + item.codigofacturante + '\').click();" value="Examinar">';
            tabla += '</FORM>';
            tabla += '</td>';

        }
        tabla += "</tr>";
    });
    tabla += '</tbody></tabla>';
    $('#widgetFacturantes .table-responsive').html(tabla);


}

function tablaCajas(valor) {
    var estado = valor == 'activo' ? '1' : '0';
    var cajas = '';
    $.ajax({
        url: BASE_URL + "configuracion/getCajas",
        type: "POST",
        data: "estado=" + estado,
        dataType: "JSON",
        cache: false,
        async: true,
        success: function (respuesta) {

            var tabla = '<table id="tablaCaja"' + valor + ' class="table table-striped table-bordered"><thead>';
            tabla += '<th>' + lang.nombre + '</th><th>' + lang.estado + '</th><th>' + lang.detalle + '</th></thead>';
            tabla += '<tbody>';
            $(respuesta).each(function (k, item) {
                var data = {
                    'codigo': item.codigo,
                };
                tabla += "<tr><td>" + item.nombre + "</td><td>" + item.estado + "</td><td><span class='label label-success arrowed'  data-detalle='" + JSON.stringify(data) + "'>" + lang.ver_editar + "</span></td></tr>";
            });
            tabla += '</tbody></tabla>';
            // $('#widgetCajas .table-responsive').html(tabla);
            $('#widgetCajas #' + valor + ' .table-responsive').html(tabla);
        }
    });
}

function tablaMoras() {
    var cajas = '';
    $.ajax({
        url: BASE_URL + "configuracion/getListaMoras",
        type: "POST",
        data: "",
        dataType: "JSON",
        cache: false,
        async: true,
        success: function (respuesta) {
            var tabla = '<table id="tablaMoras" class="table table-striped table-bordered">';
            tabla += '<thead>';
            tabla += '<th>' + lang.dias_despues_de_vencimiento + '</th>';
            tabla += '<th>' + lang.dia_hasta + '</th>'; 
            tabla += '<th>' + lang.MORA + '</th>'; 
            tabla += '<th>' + lang.porcentaje + '</th>'; 
            tabla += '<th>' + lang.diariamente + '</th>'; 
            tabla += '<th>' + lang.tipo + '</th>';
            if (edita_mora){
                tabla += '<th>' + lang.detalle + '</th>';
            }
            tabla += '</thead>';
            tabla += '<tbody>';
            $(respuesta).each(function (k, item) {
                var data = {'codigo': item.codigo};
                var porcentaje = item.es_porcentaje == 1 ? 'icon-check' : 'icon-check-empty';
                var diariamente = item.diariamente == 1 ? 'icon-check' : 'icon-check-empty';
                tabla += "<tr>";
                tabla += "<td>" + item.dia_desde + "</td>";
                tabla += "<td>" + item.dia_hasta + "</td>";
                tabla += "<td>" + item.mora + "</td>";
                tabla += "<td class='text-center'>";
                tabla += "<i class=" + porcentaje + "></i>";
                tabla += "</td>";
                tabla += "<td class='text-center'>";
                tabla += "<i class=" + diariamente + "></i>";
                tabla += "</td>";
                tabla += "<td>" + item.tipo + "</td>";
                if (edita_mora){
                    tabla += "<td>";
                    tabla += "<span class='label label-success arrowed'  data-detalle='" + JSON.stringify(data) + "'>";
                    tabla += lang.ver_editar;
                    tabla += "</span>";
                    tabla += "</td>"; 
                }
                tabla += "</tr>";
            });
            tabla += '</tbody>';
            tabla += '</tabla>';
            $('#widgetMoras .table-responsive').html(tabla);
        }
    });
}

function tablaMorasCursosCortos() {
    var cajas = '';
    $.ajax({
        url: BASE_URL + "configuracion/getListaMorasCursosCortos",
        type: "POST",
        data: "",
        dataType: "JSON",
        cache: false,
        async: true,
        success: function (respuesta) {
            var tabla = '<table id="tablaMoras" class="table table-striped table-bordered">';
            tabla += '<thead>';
            tabla += '<th>' + lang.dias_despues_de_vencimiento + '</th>';
            tabla += '<th>' + lang.dia_hasta + '</th>';
            tabla += '<th>' + lang.MORA + '</th>';
            tabla += '<th>' + lang.porcentaje + '</th>';
            tabla += '<th>' + lang.diariamente + '</th>';
            tabla += '<th>' + lang.tipo + '</th>';
            if (edita_mora){
                tabla += '<th>' + lang.detalle + '</th>';
            }
            tabla += '</thead>';
            tabla += '<tbody>';
            $(respuesta).each(function (k, item) {
                var data = {'codigo': item.codigo};
                var porcentaje = item.es_porcentaje == 1 ? 'icon-check' : 'icon-check-empty';
                var diariamente = item.diariamente == 1 ? 'icon-check' : 'icon-check-empty';
                tabla += "<tr>";
                tabla += "<td>" + item.dia_desde + "</td>";
                tabla += "<td>" + item.dia_hasta + "</td>";
                tabla += "<td>" + item.mora + "</td>";
                tabla += "<td class='text-center'>";
                tabla += "<i class=" + porcentaje + "></i>";
                tabla += "</td>";
                tabla += "<td class='text-center'>";
                tabla += "<i class=" + diariamente + "></i>";
                tabla += "</td>";
                tabla += "<td>" + item.tipo + "</td>";
                if (edita_mora){
                    tabla += "<td>";
                    tabla += "<span class='label label-success arrowed'  data-detalle='" + JSON.stringify(data) + "'>";
                    tabla += lang.ver_editar;
                    tabla += "</span>";
                    tabla += "</td>";
                }
                tabla += "</tr>";
            });
            tabla += '</tbody>';
            tabla += '</tabla>';
            $('#widgetMorasCursosCortos .table-responsive').html(tabla);
        }
    });
}

function tablaConceptos() {
    var conceptos = '';
    $.ajax({
        url: BASE_URL + "configuracion/getConceptosCtaCte",
        type: "POST",
        data: "",
        dataType: "JSON",
        cache: false,
        async: false,
        success: function (respuesta) {
            conceptos = respuesta;
        }
    });

    var tabla = '<table id="tablaConceptos" class="table table-striped table-bordered"><thead>';
    tabla += '<th>' + lang.codigo + '</th><th>' + lang.concepto + '</th><th>' + lang.detalle + '</th></thead>';
    tabla += '<tbody>';

    $(conceptos).each(function (k, item) {
        var data = {
            'cod_concepto': item.codigo
        };
        tabla += "<tr><td>" + item.codigo + "</td><td>" + item.nombre + "</td><td><span class='label label-success arrowed' data-detalle='" + JSON.stringify(data) + "'>" + lang.ver_editar + "</span></td></tr>";
    });
    tabla += '</tbody></tabla>';
    $('#widgetConceptos .table-responsive').html(tabla);
}

function tablaTerminalesPos() {
    var terminales = '';
    $.ajax({
        url: BASE_URL + "configuracion/getTerminalesPos",
        type: "POST",
        data: "",
        dataType: "JSON",
        cache: false,
        async: false,
        success: function (respuesta) {
            terminales = respuesta;
        }
    });

    var tabla = '<table id="tablaTerminalesPos" class="table table-striped table-bordered"><thead>';
    tabla += '<th>' + lang.codigo_interno + '</th><th>' + lang.operador + '</th><th>' + lang.tipo_captura + '</th><th>' + lang.estado + '</th><th>' + lang.detalle + '</th></thead>';
    tabla += '<tbody>';

    $(terminales).each(function (k, item) {
        var data = {
            'codigo': item.codigo
        };
        var estado;
        if (item.estado == 'habilitado') {

            estado = lang.HABILITADO;
        } else {

            estado = lang.INHABILITADO;
        }
        var captura = '';
        switch (item.tipo_captura) {
            case 'otro':
                captura = lang.otro;
                break;
            case 'pos':
                captura = lang.pos;
                break;
            case 'internet':
                captura = lang.internet;
                break;
            case 'manual':
                captura = lang.manual;
                break;

        }
        tabla += "<tr><td>" + item.cod_interno + "</td><td>" + item.nombre + "</td><td>" + captura + "</td><td>" + estado + "</td><td><span class='label label-success arrowed' data-detalle='" + JSON.stringify(data) + "'>" + lang.ver_editar + "</span></td></tr>";
    });
    tabla += '</tbody></table>';

    $('#widgetTerminalesPos .table-responsive').html(tabla);

    $('#tablaTerminalesPos').on('click', '[data-detalle]', function () {

        var dataPOST = JSON.parse($(this).attr('data-detalle'));
        $.ajax({
            url: BASE_URL + "configuracion/frm_terminal",
            type: "POST",
            data: {
                codigo: dataPOST.codigo
            },
            cache: false,
            success: function (respuesta) {
                $.fancybox.open(respuesta, {
                    arrows: false,
                    padding: 0,
                    openEffect: 'none',
                    closeEffect: 'none',
                    helpers: {
                        overlay: null
                    }
                });
            }
        });
        return false;
    });
}

//function tablaMediosCobros() {
//    var medios = '';
//    $.ajax({
//        url: BASE_URL + "configuracion/getMediosCobros",
//        type: "POST",
//        data: "",
//        dataType: "JSON",
//        cache: false,
//        async: false,
//        success: function(respuesta) {
//            medios = respuesta;
//        }
//    });
//
//    var tabla = '<table name="tablaMediosCobro" class="table table-striped table-bordered"><thead>';
//    tabla += '<th>' + lang.medio_pago + '</th><th>' + lang.confirmacion_automatica + '</th></thead>';
//    tabla += '<tbody>';
//
//    $(medios).each(function(k, item) {
//        var checkedconf = '';
//        if (item.conf_auto == '1') {
//            checkedconf = 'checked'
//        }
//        var confirmacion = '<label><input name="confir_auto[]" value="' + item.codigo + '" class="ace ace-switch ace-switch-6" type="checkbox" onchange="guardarConfiguracionCobroMedios();"' + checkedconf + '><span class="lbl"></span></label>';
//
//        tabla += "<tr><td>" + item.medio + "</td><td>" + confirmacion + "</td></tr>";
//    });
//    tabla += '</tbody></table>';
//
//    $('#widgetCobros .table-responsive').html(tabla);
//}

$(document).ready(function ()
{
//    $.ajax({
//        url: BASE_URL + 'entorno/getLang',
//        data: "claves=" + JSON.stringify(claves),
//        type: "POST",
//        dataType: "JSON",
//        async: false,
//        cache: false,
//        success: function(respuesta) {
//            //lang = respuesta;
//            init();
//        }
//    });

    init();

});

function init() {
    $('select').chosen({
        width: '100%',
        allow_single_deselect: true
    });
    initForm();
    tablaImpuestos();
    tablaPuntosVentas('activo');
    tablaPuntosVentas('inactivo');
    tablaFacturantes();
    tablaConceptos();
    tablaCajas('activo');
    tablaCajas('inactivo');
    tablaMoras();
    tablaMorasCursosCortos();

    tablaTerminalesPos();
    // tablaMediosCobros();

    $('.widget-toolbar').on('click', '[data-reload=widgetImpuesto]', function () {
        tablaImpuestos();
        return false;
    });
    $('.widget-toolbar').on('click', '[data-reload=widgetFacturantes]', function () {
        tablaFacturantes();
        return false;
    });
    $('.widget-toolbar').on('click', '[data-reload=widgetCajas]', function () {
        tablaCajas('activo');
        tablaCajas('inactivo');
        return false;
    });

    $('.widget-toolbar').on('click', '[data-reload=widgetConceptos]', function () {
        tablaConceptos();
        return false;
    });

    $('.widget-toolbar').on('click', '[data-reload=widgetMoras]', function () {
        tablaMoras();
        return false;
    });

    $('.widget-toolbar').on('click', '[data-reload=widgetMorasCursosCortos]', function () {
        tablaMorasCursosCortos();
        return false;
    });


    $('.widget-toolbar').on('click', '[data-reload=widgetPuntosVentas]', function () {
        tablaPuntosVentas('activo');
        tablaPuntosVentas('inactivo');
        return false;
    });

    $('.widget-toolbar').on('click', '[data-reload=widgetTerminalesPos]', function () {
        tablaTerminalesPos();
        return false;
    });

    $('#widgetImpuesto').on('click', '[data-detalle]', function () {
        $.ajax({
            url: BASE_URL + "configuracion/frm_nuevoImpuesto",
            type: "POST",
            data: 'cod_impuesto=' + $(this).attr('data-detalle'),
            cache: false,
            success: function (respuesta) {
                $.fancybox.open(respuesta, {
                    padding: 0,
                    width: 'auto',
                    openEffect: 'none',
                    closeEffect: 'none',
                    helpers: {
                        overlay: null
                    }
                });
            }
        });
        return false;
    });

    $('#widgetImpuesto').on('click', '[data-nuevoImpuesto]', function () {
        $.ajax({
            url: BASE_URL + "configuracion/frm_nuevoImpuesto",
            type: "POST",
            data: "cod_impuesto=-1",
            dataType: "",
            cache: false,
            success: function (respuesta) {
                $.fancybox.open(respuesta, {
                    width: 'auto',
                    padding: 0,
                    openEffect: 'none',
                    closeEffect: 'none',
                    helpers: {
                        overlay: null
                    }
                });
            }
        });
        return false;
    });

    $('#widgetPuntosVentas').on('click', '[data-detalle]', function () {
        $.ajax({
            url: BASE_URL + "configuracion/frm_puntoDeVenta",
            type: "POST",
            data: {
                codigo: $(this).attr('data-detalle')
            },
            dataType: "",
            cache: false,
            success: function (respuesta) {
                $.fancybox.open(respuesta, {
                    width: 'auto',
                    padding: 0,
                    openEffect: 'none',
                    closeEffect: 'none',
                    helpers: {
                        overlay: null
                    }

                });
            }
        });
        return false;
    });


    ////////////////////////////////Facturante////////////////////////////
    $('#widgetFacturantes').on('click', '[data-nuevoFacturante]', function () {
        $.ajax({
            url: BASE_URL + "configuracion/frm_facturante",
            type: "POST",
            data: "codigo_facturante=-1&cod_razon_social=-1",
            dataType: "",
            cache: false,
            success: function (respuesta) {
                $.fancybox.open(respuesta, {
                    width: 'auto',
                    padding: 0,
                    openEffect: 'none',
                    closeEffect: 'none',
                    helpers: {
                        overlay: null
                    }
                });
            }
        });
        return false;
    });

    $('#widgetFacturantes').on('click', '[data-detalle]', function () {
        $.ajax({
            url: BASE_URL + "configuracion/frm_facturante",
            type: "POST",
            data: "codigos=" + $(this).attr('data-detalle'),
            dataType: "",
            cache: false,
            success: function (respuesta) {
                $.fancybox.open(respuesta, {
                    width: 'auto',
                    padding: 0,
                    openEffect: 'none',
                    closeEffect: 'none',
                    helpers: {
                        overlay: null
                    }

                });
            }
        });
        return false;
    });

    ////////////////////////////////Cajas////////////////////////////   
    $('#widgetCajas').on('click', '[data-detalle]', function () {
        $.ajax({
            url: BASE_URL + "configuracion/frm_caja",
            type: "POST",
            data: JSON.parse($(this).attr('data-detalle')),
            dataType: "",
            cache: false,
            success: function (respuesta) {
                $.fancybox.open(respuesta, {
                    width: 'auto',
                    padding: 0,
                    openEffect: 'none',
                    closeEffect: 'none',
                    helpers: {
                        overlay: null
                    }
                });
            }
        });
        return false;
    });

    $('#widgetCajas').on('click', '[data-nuevaCaja]', function () {
        $.ajax({
            url: BASE_URL + "configuracion/frm_caja",
            type: "POST",
            data: JSON.parse($(this).attr('data-nuevaCaja')),
            dataType: "",
            cache: false,
            success: function (respuesta) {
                $.fancybox.open(respuesta, {
                    width: 'auto',
                    padding: 0,
                    openEffect: 'none',
                    closeEffect: 'none',
                    helpers: {
                        overlay: null
                    }
                });
            }
        });
        return false;
    });

    ////////////////////////////////Moras//////////////////////////// 
    $('button[name="nuevaMora"]').on('click', function () {
        $.ajax({
            url: BASE_URL + "configuracion/frm_moras",
            type: "POST",
            data: "codigo=-1",
            dataType: "",
            cache: false,
            success: function (respuesta) {
                $.fancybox.open(respuesta, {
                    arrows: false,
                    padding: 0,
                    openEffect: 'none',
                    closeEffect: 'none',
                    helpers: {
                        overlay: null
                    }
                });
            }
        });
        return false;
    });

    $('#widgetMoras').on('click', '[data-detalle]', function () {
        var dataPOST = JSON.parse($(this).attr('data-detalle'));
        $.ajax({
            url: BASE_URL + "configuracion/frm_moras",
            type: "POST",
            data: "codigo=" + dataPOST.codigo,
            dataType: "",
            cache: false,
            success: function (respuesta) {
                $.fancybox.open(respuesta, {
                    arrows: false,
                    padding: 0,
                    openEffect: 'none',
                    closeEffect: 'none',
                    helpers: {
                        overlay: null
                    }
                });
            }
        });
        return false;
    });

    ////////////////////////////////Moras - Cursos Cortos//////////////////////////// 
    $('button[name="nuevaMoraCursosCortos"]').on('click', function () {

        $.ajax({
            url: BASE_URL + "configuracion/frm_moras_cursos_cortos",
            type: "POST",
            data: "codigo=-1",
            dataType: "",
            cache: false,
            success: function (respuesta) {
                $.fancybox.open(respuesta, {
                    arrows: false,
                    padding: 0,
                    openEffect: 'none',
                    closeEffect: 'none',
                    helpers: {
                        overlay: null
                    }
                });
            }
        });
        return false;
    });

    $('#widgetMorasCursosCortos').on('click', '[data-detalle]', function () {

        var dataPOST = JSON.parse($(this).attr('data-detalle'));
        $.ajax({
            url: BASE_URL + "configuracion/frm_moras_cursos_cortos",
            type: "POST",
            data: "codigo=" + dataPOST.codigo,
            dataType: "",
            cache: false,
            success: function (respuesta) {
                $.fancybox.open(respuesta, {
                    arrows: false,
                    padding: 0,
                    openEffect: 'none',
                    closeEffect: 'none',
                    helpers: {
                        overlay: null
                    }
                });
            }
        });
        return false;
    });

    ////////////////////////////////CONCEPTOS//////////////////////////// 
    $('button[name="nuevoConcepto"]').on('click', function () {
        $.ajax({
            url: BASE_URL + "configuracion/frm_conceptos",
            type: "POST",
            data: "cod_concepto=-1",
            dataType: "",
            cache: false,
            success: function (respuesta) {
                $.fancybox.open(respuesta, {
                    width: 'auto',
                    padding: 0,
                    openEffect: 'none',
                    closeEffect: 'none',
                    helpers: {
                        overlay: null
                    }
                });
            }
        });
        return false;
    });

    $('#widgetConceptos').on('click', '[data-detalle]', function () {
        var dataPOST = JSON.parse($(this).attr('data-detalle'));
        $.ajax({
            url: BASE_URL + "configuracion/frm_conceptos",
            type: "POST",
            data: "cod_concepto=" + dataPOST.cod_concepto,
            dataType: "",
            cache: false,
            success: function (respuesta) {
                $.fancybox.open(respuesta, {
                    arrows: false,
                    padding: 0,
                    openEffect: 'none',
                    closeEffect: 'none',
                    helpers: {
                        overlay: null
                    }
                });
            }
        });
        return false;
    });

    ////////////////////////////////Proveedores Pos//////////////////////////// 
    $('button[name="nuevaTerminal"]').on('click', function () {
        $.ajax({
            url: BASE_URL + "configuracion/frm_terminal",
            type: "POST",
            data: {
                codigo: -1,
            },
            cache: false,
            success: function (respuesta) {
                $.fancybox.open(respuesta, {
                    width: 'auto',
                    padding: 0,
                    openEffect: 'none',
                    closeEffect: 'none',
                    helpers: {
                        overlay: null
                    }
                });
            }
        });
        return false;
    });

}

function utilizarBoletosBancarios() {
    var utiliza_boleto = $("[name=habilitar_boleto_bancario]").prop("checked") ? 1 : 0;
    $.ajax({
        url: BASE_URL + 'configuracion/guardar_configuracion_boleto_bancario',
        type: 'POST',
        dataType: 'json',
        data: {
            utiliza_boleto: utiliza_boleto
        },
        success: function (_json) {
            if (_json.codigo == 1) {
                if ($("[name=habilitar_boleto_bancario]").prop("checked")) {
                    $("[name=div_configuracion_boleto_bancario]").show();
                } else {
                    $("[name=div_configuracion_boleto_bancario]").hide();
                }
            } else {
                gritter(lang.error_al_deshabilitar_boletos_bancarios, false, lang.ERROR);
            }
        }
    });
}

function cambiarEstadoCuenta(cod_banco, cod_cuenta, element) {
    var estado = ($(element).prop("checked")) ? "habilitar" : "inhabilitar";
    $.ajax({
        url: BASE_URL + 'configuracion/cambiar_estado_banco',
        type: 'POST',
        dataType: 'json',
        data: {
            cod_banco: cod_banco,
            cod_cuenta: cod_cuenta,
            estado: estado
        },
        success: function (_json) {
            if (_json.error) {
                gritter("error al habilitar/inhabilitar cuenta bancaria", false);
            } else {
                gritter("success", true);
            }
        }
    });
}

function editarCuentaBancaria(cod_banco, cod_cuenta, cartera) {
    $.ajax({
        url: BASE_URL + 'configuracion/frm_modificar_cuenta_bancaria',
        type: 'POST',
        data: {
            cod_banco: cod_banco,
            cod_cuenta: cod_cuenta,
            cartera: cartera
        },
        success: function (_html) {
            $.fancybox.open(_html, {
                scrolling: 'auto',
                padding: 1,
                openEffect: 'none',
                closeEffect: 'none',
                width: "auto",
                height: "auto",
                helpers: {
                    overlay: null
                }
            });
        }
    });
}

function agregar_cuenta_bancaria() {
    $.ajax({
        url: BASE_URL + 'configuracion/agregar_cuenta_bancaria',
        type: 'POST',
        success: function (_html) {
            $.fancybox.open(_html, {
                arrows: false,
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

//function guardarConfiguracionCobroMedios() {
//    $.ajax({
//        url: BASE_URL + "configuracion/guardarCobrosMedios",
//        data: $("#tablaMedios").serialize(),
//        type: "POST",
//        dataType: "JSON",
//        async: false,
//        cache: false,
//        success: function(respuesta) {
//            tablaMediosCobros();
//        }
//    });
//}

////////////////////////////////Certificados Afip//////////////////////////// 

function infoCertificado(facturante) {
    $.ajax({
        url: BASE_URL + "configuracion/frm_certificado",
        type: "POST",
        data: "facturante=" + facturante,
        dataType: "",
        cache: false,
        success: function (respuesta) {

            $.fancybox.open(respuesta, {
                padding: 0,
                width: 'auto',
                openEffect: 'none',
                closeEffect: 'none',
                helpers: {
                    overlay: null
                }

            });
        }
    });
}

function actualizarPuntosVenta() {
    $.ajax({
        url: BASE_URL + 'configuracion/actualizarPuntosVenta',
        type: 'POST',
        data: "",
        dataType: "JSON",
        success: function (data) {
         
            
        
        
            gritter(errorPuntodeVenta(data), true);
        }
    });
}
function errorPuntodeVenta(data){
  var respuesta = '';
 $(data).each(function (k, item) {
                 
                if(item.codigo == 1){
                    respuesta += "Actualizado con éxito <br>";
                
                    $(item.custom).each(function (j, facturante){
                        var datos = '';
                        respuesta += facturante.razon + " <br>";
                        if(facturante.resultado.comentarios != ''){
                             datos += "Comentarios: " + facturante.resultado.comentarios + "<br>";
                        }
                        if(facturante.resultado.mod_numero != ''){
                            datos += "Cant. numeraciones modificados: " + facturante.resultado.mod_numero.length + "<br>";
                        }
                        if(facturante.resultado.mod_estado != ''){
                            datos += "Cant. estados modificados: " + facturante.resultado.mod_estado.length + "<br>";
                        }
                        if(facturante.resultado.nuevos != ''){
                            datos += "Cant. nuevos: " + facturante.resultado.nuevos.length + "<br>";
                        }
                        if(datos == ''){
                            datos += "Sin modificaciones <br>";
                        }
                        respuesta += datos;
                    });
                }
            });
        return respuesta;}
        
function subircertificado(facturante){
//    var link_subir = BASE_URL + "configuracion/subirCrtFacturante";
//    $.post(link_subir,.serialize());

    $('#frm_subir_' + facturante).submit(function(e){
      
        $.ajax({
          url: BASE_URL + "configuracion/subirCrtFacturante",
          type: 'POST',
          data: new FormData(this),
          processData: false,
          dataType: "JSON",
          contentType: false,
          success: function (data) {
         
          
        if(data.codigo == 1){
        
            gritter("Subido con éxito" , true);
               tablaFacturantes();
               }else{
                     var comentario= data.custom.comentarios = ''?'':'<br>ADVERTENCIA: '+data.custom.comentarios;
                    gritter(comentario, false); 
                    
               }
        }
        });
    e.preventDefault();
  });
    $('#frm_subir_' + facturante).submit();
}

function resetear_moras(){
    $.ajax({
        url: BASE_URL + 'configuracion/resetear_moras',
        type: 'POST',
        dataType: 'json',
        success: function(_json){
            if (_json.error){
                gritter("Error al reinicar moras", false, '');
            } else {
                gritter("Moras reiniciadas correctamente", true, '');
            }
        }
    });
}

function checkForm(elemento){
    var selector = '#' + elemento.id;
    switch(elemento.id){
        /*Bloque juros*/
        case 'juros-cobrar':
            if($(selector).prop('checked')){
                $('#juros-tipo-banco').prop('disabled', false);
                $('#juros-tipo-iga').prop('disabled', false);
                if($('#juros-tipo-iga').prop('checked') && !$('#juros-tipo-pelaiga').prop('disabled')){
                    $('#juros-valoriga').prop('disabled', false);
                }
            } else { 
                $('#juros-tipo-banco').prop('disabled', true);
                $('#juros-tipo-iga').prop('disabled', true);
                $('#juros-valoriga').prop('disabled', true);
            }
            break;
        case 'juros-tipo-iga':
            if($('#juros-tipo-iga').prop('checked'))
               $('#juros-valoriga').prop('disabled', false);
            break;
        case 'juros-tipo-banco':
               $('#juros-valoriga').prop('disabled', true);
            break;


        /*Bloque Multa*/
        case 'multa-cobrar':
            if($('#multa-cobrar').prop('checked')){
                $('#multa-dias').prop('disabled', false);
                $('#multa-valor').prop('disabled', false);
            } else {
                $('#multa-dias').prop('disabled', true);
                $('#multa-valor').prop('disabled', true);
            }
            break;

        /*Bloque Aposovencimiento*/
        case 'venc-tipo-nao':
            if($(selector).prop('checked')){
                $('#venc-limite').prop('disabled', true);
                $('#venc-dias').prop('disabled', true);
            }
            break;

        case 'venc-tipo-banco':
            if($(selector).prop('checked')){
               $('#venc-limite').prop('disabled', false);
               if($('#venc-limite').prop('checked'))
                    $('#venc-dias').prop('disabled', false);
            }
            break;

        case 'venc-limite':
            if($(selector).prop('checked')){
                $('#venc-dias').prop('disabled', false);
            } else { 
                $('#venc-dias').prop('disabled', true);
            }
            break;

        /*Bloque atraso */

        case 'inclu-apos':
            if($(selector).prop('checked')){
                $('#inclu-dias').prop('disabled', false);
            } else {
                $('#inclu-dias').prop('disabled', true);
            }
            break;

        /* Bloque default, falta debuggear*/
        default:
            break;
    }

}

/*
    Para que el selector de etiquetas vuelva siempre al mismo estado al emitir boletos.
*/
function initForm(){
    $.ajax({
        url:BASE_URL + 'configuracion/getEtiquetasBoleto',
        type:'POST',
        dataType:'JSON',
        success:function (respuesta) {
            //Seteo todos los valores:
            $('#juros-valoriga').val(respuesta['juros-valoriga']);
            switch(respuesta['juros-tipo']){
                case 'banco':
                    $('#juros-tipo-banco').prop('checked', true);
                    $('#juros-valoriga').prop('disabled', true);
                    break;
                case 'iga':                    
                    $('#juros-tipo-iga').prop('checked', true);
                    break;
                default:
                    break;
            }
            $('#multa-valor').val(respuesta['multa-valor']);
            $('#multa-dias').val(respuesta['multa-dias']);
            $('#venc-dias').val(respuesta['venc-dias']);
            $('#inclu-dias').val(respuesta['inclu-dias']);
            if(respuesta['descontoFixo'] == 'on'){
               $('#descontoFixo').prop('checked', true); 
            }
            if(respuesta['juros-cobrar'] == 'on'){
                $('#juros-cobrar').prop('checked', true);
            }else {
                $('#juros-cobrar').prop('checked', false);
                $('#juros-tipo-banco').prop('disabled', true);
                $('#juros-tipo-pelaiga').prop('disabled', true);
                $('#juros-valoriga').prop('disabled', true);

            }

            if(respuesta['multa-cobrar'] == 'on'){
                $('#multa-cobrar').prop('checked', true);
            } else {
                $('#multa-valor').prop('disabled', true);
                $('#multa-dias').prop('disabled', true);
            }
            switch(respuesta['venc-tipo']){
                case 'nao':
                    $('#venc-tipo-nao').prop('checked', true);
                    $('#venc-limite').prop('disabled', true);
                    $('#venc-dias').prop('disabled', true);
                    break;
                case 'banco':
                    $('#venc-tipo-banco').prop('checked', true);
                    if(respuesta['venc-limite'] == 'on'){
                        $('#venc-limite').prop('checked', true);
                    } else {
                        $('#venc-dias').prop('disabled', true);
                    }
                    break;
                default:
                    break;
            }
            if(respuesta['inclu-apos'] == 'on'){
                $('#inclu-apos').prop('checked', true);
            } else {
                $('#inclu-dias').prop('disabled', true)
            }

            if(respuesta['cb-enviarRemessa'] == 'on'){
                $('#cb-enviarRemessa').prop('checked', true);
            }

            switch(respuesta['valorBoleto']){
                case 'desconto':
                    $('#valorBoleto-desconto').prop('checked', true);
                    break;
                case 'cheio':
                    $('#valorBoleto-cheio').prop('checked', true);
                    break;
                default:
                    break;
            }

        }
    });
}

function enviarEtiquetas()
{
    var form = $('#frm_etiquetas_boleto');
    var disabled = form.find(':input:disabled').removeAttr('disabled');
    var formData = form.serializeArray();
    disabled.attr('disabled','disabled');

    var preferencias = {};
    formData.forEach(function(elemento){
        preferencias[elemento.name] = elemento.value;
    });
    $.ajax({
        url: BASE_URL + 'configuracion/setEtiquetasBoleto',
        type: 'POST',
        data: {preferencias:JSON.stringify(preferencias)},
        dataType: 'json',
        success: function (success){
            console.log(success);
        }
    });
}

function guardarConfiguracionFacturacionSegmentada(event){
    var data_post = $('#configuracion_facturacion_segmentada').serialize();
    $.ajax({
        url: BASE_URL+'configuracion/guardarConfiguracionFacturacionSegmentada',
        data: data_post,
        type: "POST",
        dataType: "JSON",
        async: false,
        cache: false,
        success:function(respuesta){
            if (respuesta.codigo == 0){
                gritter(respuesta.msgError);
            } else {
                gritter(lang.validacion_ok,true);
            }
        }
    });
    event.preventDefault();
    return false;
}
