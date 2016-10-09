var valorTotalFacturar = 0.00;
var puntoVentaPorventaje = new Array();
var puntoVentaTipoFactura = new Array();
var langFRM = langFrm;
var montos_seleccionados = new Array();
$('.fancybox-wrap').ready(function() {
    Init();
});

function Init() {
    getTableCtacte();
    $("#alumnos").ajaxChosen({
        minLength: 0,
        queryLimit: 10,
        delay: 100,
        chosenOptions: {width: '100%', max_selected_options: 1},
        searchingText: "Buscando...",
        noresultsText: "No hay resultados.",
        initialQuery: true
    },
    function(options, response) {
        $.ajax({
            url: BASE_URL + 'alumnos/getAlumnosHabilitadosSelect',
            type: "POST",
            data: {buscar: options.term, estado: 'inhabilitada'},
            dataType: "JSON",
            cache: false,
            success: function(respuesta) {
                var terms = {};
                $.each(respuesta, function(i, val) {
                    terms[val.codigo] = val.nombreapellido + " (" + val.codigo + ", " + val.documento_completo + ")";
                });
                response(terms);
            }
        });
    });

    $(".fechafactura").click(function() {
        $("#fecha-factura").trigger("focus");
    });

    $("#btn-facturar").click(function() {       
        var tipo_factura = $("[name=tipo_factura]").val();
        var mensaje = '';
        var cantidad_maxima_renglones = 9; // se puede leer este valor desde una configuracion
        if (tipo_factura != null) {
            var encontrado = false;
            var total_facturacion = 0;
            for (var i = 0; i < tipo_factura.length; i++) {
                var tipo = puntoVentaTipoFactura[tipo_factura[i]];
                for (var j = i + 1; j < tipo_factura.length; j++) {                 
                    if (puntoVentaTipoFactura[tipo_factura[j]] == tipo) {
                        encontrado = true;
                    }
                }
            }
            if (encontrado) {
                mensaje = langFRM.ha_seleccionado_tipos_de_datos_de_facturas_repetidos;
            } else {
//                for (var i = 0; i < tipo_factura.length; i++) {
//                    total_facturacion += puntoVentaPorventaje[tipo_factura[i]];
//                }
//                if (total_facturacion != 100) {
//                    mensaje += langFRM.la_configuracion_de_porcentajes_de_facturacion_sobre_los_puntos_de_venta_seleccionados_debe_ser_del_100 + "<br>";
//                }
            }
        } else {
            mensaje += langFRM.indique_el_tipo_de_factura + "<br>";
        }
        if (mensaje != '') {
            gritter(mensaje);
        } else {
            var tablaSerialize = $('#ctacteTable').dataTable().$('input').serializeArray();
            var cantidad_seleccionado = 0;
            $.each(tablaSerialize, function(key, value){
                if (value.name == 'checkctacte[]'){
                    cantidad_seleccionado ++;
                }
            });
            if (cantidad_seleccionado > cantidad_maxima_renglones){
                gritter(langFRM.el_maximo_de_renglones_a_dacturar_no_debe_ser_mayor_a + " " + cantidad_maxima_renglones);
            } else {
                var formSerialize = $('.form :not(input[name="checkctacte[]"],input[name^="val"])').serializeArray();
                var data = tablaSerialize;
                data = data.concat(formSerialize);
                for (var i = 0; i < tipo_factura.length; i++) {
                    data.push({name: 'puntos_venta[]', value: tipo_factura[i]});
                }
                $.ajax({
                    url: BASE_URL + 'facturacion/guardarFactura',
                    data: data,
                    type: 'POST',
                    dataType: 'json',
                    cache: false,
                    success: function(respuesta) {
                        switch (respuesta.codigo) {
                            case 1 :
                                $.gritter.add({
                                    title: langFRM.BIEN,
                                    text: langFRM.FACTURA_EMITIDA_CORRECTAMENTE,
                                    sticky: false,
                                    time: '3000',
                                    class_name: 'gritter-success'
                                });
                                ordenarylistar = true;
                                $.fancybox.close();
                                if (respuesta.custom.imprimir == "1") {
                                    var cod_factura = respuesta.custom.factura.join("-");
                                    var param = new Array();
                                    param.push(cod_factura);
                                    printers_jobs(11, param);
                                }
                                break;
                            case 0:
                                $(".errores").html(respuesta.msgerror);
                            default :
                                $.gritter.add({
                                    title: langFRM.ERROR,
                                    text: respuesta.msgerror,
                                    sticky: false,
                                    time: '3000',
                                    class_name: 'gritter-error'
                                });
                                break;
                        }
                    }
                });
            }
        }
    });

    $('.fancybox-wrap select').chosen({
        width: "100%"
    });

    $("#fecha-factura").datepicker({
        changeMonth: false,
        changeYear: false,
        dateFormat: 'dd/mm/yy'
    });

    $('.form select[name="alumnos"]').on('chosen:activate', function() {

    });

    //CAMBIO EN SELECT facturante
    $('.form select[name="facturante"]').change(function() {
        if ($("[name=alumnos]").val())
            getTiposFacturas();
    });

    $('.form select[name="razones_sociales"]').change(function() {
        getTiposFacturas();
    });

    //CAMBIO EN SELECT DE RAZONES
    $('.form select[name="alumnos"]').change(function() {
        valorTotalFacturar = 0.0;
        $('select[name="razones_sociales"]').empty();
        $('select[name="razones_sociales"]').trigger('chosen:updated');
        $("#ctacteTable").dataTable().fnClearTable();
        if ($(this).val() != null) {
            $.ajax({
                url: BASE_URL + 'facturacion/getRazonesAlumno',
                data: 'cod_alumno=' + $(this).val(),
                type: 'POST',
                dataType: 'json',
                cache: false,
                success: function(respuesta) {
                    //debugger;
                    if(respuesta.error)
                    {
                        var mensaje = langFRM.validacion_cpf_invalida;
                        gritter(mensaje);
                    }
                    else
                    {
                        $('select[name="razones_sociales"]').empty();
                        $(respuesta.razon_alumno).each(function() {
                            $('select[name="razones_sociales"]').append('<option value="' + this.codigo + '">' + this.razon_social + '</option>');
                        });
                        $('select[name="razones_sociales"]').trigger('chosen:updated');
                        $('#ctacteTable').dataTable().fnClearTable();
                        addRows();
                        getTiposFacturas();
                    }
                }
            });
        }
    });

    $('.fancybox-wrap').on('change', '.checkselect', function() {
        var saldo = 0;
        saldo = parseFloat($(this).attr('saldo')), 2;
        var id_ctacte = $(this).val();
        if ($(this).is(':checked')) {
            valorTotalFacturar = parseFloat(valorTotalFacturar + saldo, 2);
            montos_seleccionados[id_ctacte] = $(this).closest("tr").find("td:eq(3)").text();
        } else {
            valorTotalFacturar = parseFloat(valorTotalFacturar - saldo, 2);
            montos_seleccionados[id_ctacte] = '';
        }
        $("#total-label").html(valorTotalFacturar);
    });

    $('.fancybox-wrap').on('click', 'span[name="calcularTotal"]', function(){
        calTotal();
        return false;
    });
}


function mySearch(element) {
    var text = $(element).val();
    $('#ctacteTable').dataTable().fnFilter(text);
}

function addRows() {
    $.ajax({
        url: BASE_URL + 'facturacion/getCtacteFacturarAlumno',
        data: 'cod_alumno=' + $('form select[name="alumnos"]').val(),
        type: 'POST',
        dataType: 'json',
        cache: false,
        success: function(respuesta)
        {
            $(respuesta).each(function()
            {
                if (this.habilitado === '2') {
                    icono = "<i class='icon-ok icon-info-sign' title='" + lang.deuda_pasiva + "'></i>";
                } else {
                    icono = "";
                }
                $('#ctacteTable').dataTable().fnAddData(
                        [
                            '<div class=""><label><input name="checkctacte[]" class="checkselect ace ace-checkbox-2" saldo="' + this.saldofacturar + '" value="' + this.codigo + '" type="checkbox"><span class="lbl"></span></label></div>',
                            this.descripcion + ' ' + icono,
                            this.fechavenc,
                            this.saldofacturarformateado + "<input type='hidden' name='val-" + this.codigo + "' value='" + this.saldofacturar + "'/>"
                        ]
                        );

            });

            $.fancybox.update();
        }
    });
}

function resetAlumno() {
    $('.form select[name="razones_sociales"]').val('').trigger("chosen:updated");
    $('.form select[name="alumnos"]').val('').trigger("chosen:updated");
    hideRazon();
}

function hideRazon() {
    $(".razon-social-group").hide();
}

function showRazon() {
    $(".razon-social-group").show();
}

function getTiposFacturas() {
    var facturante = $("#facturante").val();
    var razon_social = $("#razones_sociales").val();
    var punto_venta = $("#facturante option:selected").attr("punto-venta");
    $.ajax({
        url: BASE_URL + 'facturacion/getTiposFacturaFacturante',
        data: 'cod_facturante=' + facturante + "&cod_razon_social=" + razon_social + "&punto-venta=" + punto_venta,
        type: 'POST',
        dataType: 'json',
        cache: false,
        success: function(respuesta) {
            $('select[name="tipo_factura"]').empty();
            $(respuesta).each(function() {
                $('select[name="tipo_factura"]').append('<option value="' + this.codigo + '">' + this.factura + '</option>');
                puntoVentaPorventaje[this.codigo] = parseFloat(this.porcentaje);
                puntoVentaTipoFactura[this.codigo] = parseInt(this.tipo);
            });
            $('select[name="tipo_factura"]').trigger('chosen:updated');
        }
    });
}

function getTableCtacte() {
    var trtable = "";
    trtable += '<table id="ctacteTable" cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" >';
    trtable += '<thead>';
    trtable += '<th></th>';
    trtable += '<th>' + langFRM.descripcion_ctacte_facturar + '</th>';
    trtable += '<th>' + langFRM.fecha_vencimiento + '</th>';
    trtable += '<th>' + langFRM.saldo + '</th>';
    trtable += '</thead>';
    $('.content-tabla-ctacte').html("");
    $('.content-tabla-ctacte').html(trtable);
    $('#ctacteTable').dataTable({
        "aLengthMenu": [[5, 10, 25], [5, 10, 25]],
        "bFilter": true,
        "bLengthChange": false,
        "iDisplayLength": 5,
        "language": {
            "search": ""
        },
        "aoColumns" : [
            null , 
            null,
           {"sType": "uk_date"},
           null
        ]
    });
    $('#ctacteTable_filter').closest('.row').remove();
    $.fancybox.update();
    $.fancybox.reposition();
}

function calTotal() {
    var totales = new Array();
    $.each(montos_seleccionados, function(key, value){
        if (value && value != ''){
            totales.push(value);
        }
    });
    var dataPOST = $('#ctacteTable').find('input[name="checkctacte[]"]').serialize();
    if (totales.length == 0) {
        dataPOST = 'checkctacte%5B%5D=&importes%5B%5D=';
    } else {
        dataPOST += '&' + $.param({'importes': totales});
    }

    $.ajax({
        url: BASE_URL + 'facturacion/calcularTotal',
        type: "POST",
        data: dataPOST,
        dataType: "JSON",
        cache: false,
        success: function(respuesta) {
            if (respuesta.codigo == 1) {
                $('.fancybox-wrap').find('input[name="total_facturar"]').val(respuesta.total);
            } else {
                $.gritter.add({
                    title: 'Upss!',
                    text: respuesta.msgerror,
                    sticky: false,
                    time: '3000',
                    class_name: 'gritter-danger'
                });
            }
        }
    });
    return false;
}


jQuery.fn.dataTableExt.oSort['uk_date-asc']  = function(a,b) {     
    var ukDatea = a.split('/');
    var ukDateb = b.split('/');                
    if (isNaN(parseInt(ukDatea[0]))) {
        return -1;
    }     
    if (isNaN(parseInt(ukDateb[0]))) {
        return 1;
    }     
    var x = (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
    var y = (ukDateb[2] + ukDateb[1] + ukDateb[0]) * 1;     
    return ((x < y) ? -1 : ((x > y) ?  1 : 0));
};

jQuery.fn.dataTableExt.oSort['uk_date-desc'] = function(a,b) {
    var ukDatea = a.split('/');
    var ukDateb = b.split('/');               
    if (isNaN(parseInt(ukDatea[0]))) {
        return 1;
    }     
    if (isNaN(parseInt(ukDateb[0]))) {
        return -1;
    }     
    var x = (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
    var y = (ukDateb[2] + ukDateb[1] + ukDateb[0]) * 1;     
    return ((x < y) ? 1 : ((x > y) ?  -1 : 0));
};