var arrCtacte = new Array();
var langFrmCobro = Array();
var oTableCtaCte = '';
var validacionCheckboxCtaCte = 0;// ESTE PARAMETRO TIENE QUE VENIR DE CONFIGURACION.TERMINAR.SE QUITA LA RESTRICCION DE CTACTE CHECKEADO (pedido de AGUSTINA)
var puntoVentaPorventaje = new Array();
var puntoVentaTipoFactura = new Array();
Array.prototype.contains = function(element) {
    for (var i = 0; i < this.length; i++) {
        if (this[i] == element) {
            return true;
        }
    }
    return false;
};
var _toPrecision = function(number, precision) {
    var prec = Math.pow(10, precision);
    return Math.round(number * prec) / prec;
};
var valorTotalFacturar = 0.0;
var langFrmCobro = langFrm;
$('.fancybox-wrap').ready(function(){
    Init();
});

function Init() {
    $('.fancybox-wrap').on('click', '.input-group-addon', function() {
        $(this).closest('.input-group').find('input[type="text"]').focus();
    });
    getTableCtacte();
    $("#alumnos").ajaxChosen({
        minLength: 0,
        queryLimit: 10,
        delay: 300,
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

    $("[name=razones_sociales]").on("change", function() {
        getTiposFacturas();
    });
    
    //Para que quede seleccionado por defecto medio de pago efectivo
    $('.form select[name="medio_pago"]').val("1");

    $('.form  select').chosen({
        width: "100%"
    });
    $(".previous-factura").click(function() {
        $(".factura").show();
        $(".cobro").hide();
        $(".btn-factura").show();
        $("#btn-facturar").hide();
        $("#btn-volver").hide();
        $("#btn-cobrar").show();
        $.fancybox.reposition();
    });
    $("#btn-facturar").click(function() {
        $("#btn-facturar").prop('disabled', true);
        facturar();
    });
    $("#btn-cobrar").click(function() {
        cobrar();
    });
    //CAMBIO EN SELECT DE RAZONES
    $('.form select[name="alumnos"]').change(function() {
        $('select[name="razones_sociales"]').empty();
        $('select[name="razones_sociales"]').trigger('chosen:updated');
        $("#ctacteTable").dataTable().fnClearTable();
        valorTotalFacturar = 0.0;
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
    //CAMBIO EN SELECT facturante
    $('.form select[name="facturante"]').change(function() {
        getTiposFacturas();
    });

    // Cuando se clickean los checkboxes
    var orden_actual = 0;
    $('.fancybox-wrap').on('change', '.checkselect', function() {
        $("#total-general").val(0);

        if ($("input[name='codigo']").val() == '') {
            $("input[name='total_cobrar']").val(0); 
        }

        if (this.checked) {
            orden_actual++;
            $(this).attr('data-orden-cobro', orden_actual);
        }
        else
        {
            $(this).attr('data-orden-cobro', 0);
        }

        /*console.log("Selected: " + $(this).attr('data-orden-cobro') + ": " + $(this).val());
        console.log(this);*/
    });

    var day = new Date();
    $("#fecha-factura").datepicker({
//        changeMonth: false,
//        changeYear: false,
        dateFormat: 'dd/mm/yy'
//        minDate: "-" + (day.getDate() - 1) + "d",
//        maxDate: "+" + (ultimoDiaMes - day.getDate()) + "D"
    });
    //SELECT MEDIO PAGO
    $('.form select[name="medio_pago"]').change(function() {
        var htmlDetalle = "";
        htmlDetalle += ' <div class="form-group"><label>' + langFrmCobro.caja + '</label>';
        htmlDetalle += "<select id='medio-caja' name='medio-caja' class='form-control' data-placeholder=" + langFrmCobro.seleccione_caja + ">";
        htmlDetalle += "</select>";
        htmlDetalle += "</div>";
        $(".caja").html(htmlDetalle);
        
        $.ajax({
            url: BASE_URL + 'facturacion/getCajasCobrar',
            type: 'POST',
            data: 'cod_medio=' + $(this).val(),
            dataType: 'json',
            cache: false,
            success: function(respuesta) {
                $("#medio-caja").empty();
                $("#medio-caja").append("<option ></option>");
                $(respuesta).each(function() {
                    if (this.estado == 'abierta' || this.con_automatica == 0){
                        $("#medio-caja").append("<option value='" + this.codigo + "'>" + this.nombre + "</option>");
                    } else {
                        $("#medio-caja").append("<option value='" + this.codigo + "' disabled='true'>" + this.nombre + "&nbsp;(" + langFrmCobro.caja_cerrada + ")" + "</option>");
                    }
                });
                $("#medio-caja").val("1");
                $("#medio-caja").trigger('chosen:updated');
                $(".medios").empty();
                if (respuesta.length < 1) {
                    htmlDetalle += '<label class="red">' + langFrmCobro.cajas_habilitadas_para_este_medio + '</label>';
                    $(".caja").html(htmlDetalle);
                } else {
                    var html = '';
                    switch ($('.form select[name="medio_pago"]').val()) {
                        case  "1" :
                            $(".medios").html(html);
                            break;

                        case  "2" :
                            $(".medios").html(html);
                            break;

                        case  "3" :
                            html += '<div class="form-group"><label>' + langFrmCobro.terminal + "</label>";
                            html += '<select name="medios_terminales" class="form-control" data-placeholder="' + langFrmCobro.seleccione_terminal + '" onchange="actualizarTarjetas();">';
                            html += '</select>';
                            html += '</div>';
                            html += ' <div class="form-group"><label>' + langFrmCobro.TARJETA + '</label>';
                            html += "<select name='medio-tajeta-tipo' id='medio-tajeta-tipo' class='form-control' data-placeholder=" + langFrmCobro.seleccione_tarjeta + ">";
                            html += "</select>";
                            html += "</div>";
                            html += ' <div class="form-group"><label>' + langFrmCobro.codigo_cupon + '</label>';
                            html += '<input type="text" name="medio-tajeta-cupon" id="medio-tajeta-cupon" class="form-control" placeholder="' + langFrmCobro.escriba + langFrmCobro.codigo_cupon + '">';
                            html += '</div>';
                            html += '<div class="from-group"></label>' + langFrmCobro.codigo_autorizacion + "</label>";
                            html += '<input type="text" name="medio_tarjeta_autorizacion" class="form-control" placeholder="' + langFrmCobro.escriba + langFrmCobro.codigo_autorizacion + '">';
                            html += '</div>';
                            $(".medios").html(html);
                            actualizarTerminales();
                            break;

                        case "4" :
                            html += '<div class="form-group"><label>' + langFrmCobro.banco + '</label>';
                            html += "<select name='medio-cheque-banco' id='medio-cheque-banco' class='form-control' data-placeholder=" + langFrmCobro.seleccione_banco + ">";
                            html += "</select>";
                            html += "</div>";
                            html += '<div class="form-group"><label>' + langFrmCobro.tipo + '</label>';
                            html += "<select name='medio-cheque-tipo' id='medio-cheque-tipo' class='form-control' data-placeholder=" + langFrmCobro.seleccione_cheque + ">";
                            html += "</select>";
                            html += "</div>";
                            html += '<div class="form-group">';
                            html += '<label>' + langFrmCobro.fecha + '</label>';
                            html += '<div class="input-group">';
                            html += '<input type="text" placeholder="" name="medio-cheque-fecha" id="medio-cheque-fecha" class="form-control date-picker" data-placeholder="' + langFrmCobro.seleccione_fecha + '">';
                            html += '<span class="input-group-addon ">';
                            html += '<i class="icon-calendar"></i>';
                            html += '</span>';
                            html += "</div>";
                            html += "</div>";
                            html += '<div class="form-group"><label>' + langFrmCobro.numero + '</label>';
                            html += '<input type="text" placeholder="" name="medio-cheque-numero" id="medio-cheque-numero" class="form-control" placeholder="' + langFrmCobro.escriba_numero_cheque + '">';
                            html += "</div>";
                            html += '<div class="form-group"><label>' + langFrmCobro.emisor + '</label>';
                            html += '<input type="text" placeholder="" name="medio-cheque-emisor" id="medio-cheque-emisor" class="form-control" placeholder="' + langFrmCobro.escriba_emisor + '">';
                            html += "</div>";
                            $(".medios").html(html);
                            $("#medio-cheque-fecha ").datepicker({
                                dateFormat: 'dd/mm/yy'
                            });
                            getBancos("#medio-cheque-banco");
                            getTiposCheque("#medio-cheque-tipo");
                            break;

                        case "6" :
                            html += ' <div class="form-group"><label>' + langFrmCobro.banco + '</label>';
                            html += "<select name='medio-deposito-banco' id='medio-deposito-banco' class='form-control'>";
                            html += "</select>";
                            html += "</div>";
                            html += ' <div class="form-group"><label>' + langFrmCobro.fecha + '</label><div class="input-group">';
                            html += '<input type="text" placeholder="" name="medio-deposito-fecha" id="medio-deposito-fecha" class="form-control"><span class="input-group-addon "><i class="icon-calendar"></i></span>';
                            html += "</select>";
                            html += "</div>";
                            html += "</div>";
                            html += '  <div class="form-group"><label>' + langFrmCobro.numero_transaccion + '</label>';
                            html += '<input type="text" placeholder="" name="medio-deposito-transaccion" id="medio-deposito-transaccion" class="form-control">';
                            html += "</select>";
                            html += "</div>";
                            html += ' <div class="form-group"><label>' + langFrmCobro.nombre_de_cuenta + '</label>';
                            html += '<input type="text" placeholder="" name="medio-deposito-cuenta" id="medio-deposito-cuenta" class="form-control"/>';
                            html += "</select>";
                            html += "</div>";
                            $(".medios").html(html);
                            $("#medio-deposito-fecha").datepicker({
                                dateFormat: 'dd/mm/yy'
                            });
                            getBancos("#medio-deposito-banco");
                            break;

                        case "7" :
                            html += '    <div class="form-group"><label>' + langFrmCobro.banco + '</label>';
                            html += "<select name='medio-tranferencia-banco' id='medio-tranferencia-banco' class='form-control'>";
                            html += "</select>";
                            html += "</div>";
                            html += ' <div class="form-group"><label>' + langFrmCobro.fecha + '</label><div class="input-group">';
                            html += '<input type="text" placeholder="" name="medio-tranferencia-fecha" id="medio-tranferencia-fecha" class="form-control"><span class="input-group-addon "><i class="icon-calendar"></i></span>';
                            html += "</div>";
                            html += "</div>";
                            html += ' <div class="form-group"><label>' + langFrmCobro.numero_transaccion + '</label>';
                            html += '<input type="text" placeholder="" name="medio-tranferencia-nro-transaccion"  class="form-control"/>';
                            html += "</select>";
                            html += "</div>";
                            html += ' <div class="form-group"><label>' + langFrmCobro.nombre_de_cuenta + '</label>';
                            html += '<input type="text" placeholder="" name="medio-tranferencia-cuenta" id="medio-tranferencia-cuenta" class="form-control">';
                            html += "</select>";
                            html += "</div>";

                            $(".medios").html(html);
                            getBancos("#medio-tranferencia-banco");
                            $("#medio-tranferencia-fecha").datepicker({
                                dateFormat: 'dd/mm/yy'
                            });
                            break;
                        case  "8" :
                            html += '<div class="form-group"><label>' + langFrmCobro.terminal + "</label>";
                            html += '<select name="medios_terminales" class="form-control" data-placeholder="' + langFrmCobro.seleccione_terminal + '" onchange="actualizarTarjetas(true);">';
                            html += '</select>';
                            html += '</div>';
                            html += ' <div class="form-group"><label>' + langFrmCobro.TDEBITO + '</label>';
                            html += "<select name='medio-tajeta-tipo' id='medio-tajeta-tipo' class='form-control' data-placeholder=" + langFrmCobro.seleccione_tarjeta + ">";
                            html += "</select>";
                            html += "</div>";
                            html += ' <div class="form-group"><label>' + langFrmCobro.codigo_cupon + '</label>';
                            html += '<input type="text" name="medio-tajeta-cupon" id="medio-tajeta-cupon" class="form-control" placeholder="' + langFrmCobro.escriba + langFrmCobro.codigo_cupon + '">';
                            html += '</div>';
                            html += '<div class="from-group"></label>' + langFrmCobro.codigo_autorizacion + "</label>";
                            html += '<input type="text" name="medio_tarjeta_autorizacion" class="form-control" placeholder="' + langFrmCobro.escriba + langFrmCobro.codigo_autorizacion + '">';
                            html += '</div>';
                            $(".medios").html(html);
                            actualizarTerminales();
                            break;
                            
                        default:
                            break;
                    }
                }
            }
        });
        $.fancybox.update();
        $('.form select').chosen({
            width: "100%"
        });
    });
    $('.form select[name="medio_pago"]').change();
}

function actualizarTarjetas(debito){
    $("[name=medio-tajeta-tipo]").find("option").remove();
    $("[name=medio-tajeta-tipo]").append("<option value=''>(" + langFrmCobro.recuperando + "...)</option>");
    $("[name=medio-tajeta-tipo]").trigger("chosen:updated");
    var terminal = $("[name=medios_terminales]").val();
    var medio_pago = $("[name=medio_pagos]").val();

    var URL='';

console.log(medio_pago);
    // Pattch medio de pago debito
    if (debito){
        var URL = BASE_URL + 'facturacion/get_tarjetas_debito';
    }else{
        var URL = BASE_URL + 'facturacion/get_tarjetas';
    }
    if (terminal != '') {
        $.ajax({
            url: URL,
            type: 'POST',
            dataType: 'json',
            data: {
                terminal: terminal
            },
            success: function(_json) {
                $("[name=medio-tajeta-tipo]").find("option").remove();
                if (_json.length > 0) {
                    var _html = _json.length > 1 ? '<option value=""></option>' : '';
                    $.each(_json, function(key, value) {
                        _html += '<option value="' + value.codigo + '">';
                        _html += value.nombre;
                        _html += "</option>";
                    });
                    $("[name=medio-tajeta-tipo]").append(_html);
                } else {
                    $("[name=medio-tajeta-tipo]").append("<option value=''>(" + langFrmCobro.sin_registros + ")</option>");
                }
                $("[name=medio-tajeta-tipo]").trigger("chosen:updated");
            }
        });
    } else {
        $("[name=medio-tajeta-tipo]").find("option").remove();
        $("[name=medio-tajeta-tipo]").append("<option value=''>(" + langFrmCobro.sin_registros + ")</option>");
        $("[name=medio-tajeta-tipo]").trigger("chosen:updated");
    }
}

function actualizarTerminales(){
    $.ajax({
        url: BASE_URL + 'facturacion/get_terminales',
        type: 'POST',
        dataType: 'json',
        success: function(_json) {
            var _html = '<option></option>';
            $.each(_json, function(key, value) {
                _html += '<option value="' + value.codigo + '">';
                _html += value.detalle;
                _html += '</option>';
            });
            $("[name=medios_terminales]").append(_html);
            $("[name=medios_terminales]").trigger("chosen:updated");
        }
    });
}

function checkCtacteChecked(element){
    arrCtacte[element.value]['checked'] = element.checked ? 1 : 0;
    var atributo = $(element).attr('data-codigos');
    var numerador = $(element).attr('numerador');
    if (validacionCheckboxCtaCte == 1){
        if (element.checked){
            numerador++;
            $('#ctacteTable').dataTable().$('input[data-codigos="' + atributo + '"][numerador="' + numerador + '"]').prop('disabled', false);
        } else {
            var bloquiado = false;
            $('#ctacteTable').dataTable().$('input[data-codigos="' + atributo + '"]').not(':disabled').each(function(k, elemento) {
                if ($(elemento).attr('numerador') > numerador){
                    bloquiado = true;
                }
                if ($(elemento).attr('numerador') != 0){
                    if ($(elemento).is(':checked') && $(elemento).attr('numerador') > numerador){
                        $(elemento).prop('checked', false);
                    }
                    $(elemento).prop('disabled', bloquiado);
                    bloquiado = false;
                }
            });
        }
    }
}

function addRows(respuesta) {
    $.ajax({
        url: BASE_URL + 'facturacion/getCtacteFacturaCobroAlumno',
        data: 'cod_alumno=' + $('form select[name="alumnos"]').val(),
        type: 'POST',
        dataType: 'json',
        cache: false,
        success: function(respuesta)
        {
            arrCtacte = new Array();
            var debe = [];
            var num = [];
            $(respuesta).each(function(key, ctacte)
            {
                var k = ctacte.concepto + '-' + ctacte.cod_concepto;
                var disabled = '';
                if (validacionCheckboxCtaCte == 1){
                    if (debe.contains(k)){
                        disabled = 'disabled';
                    } else {
                        num[k] = 0;
                    }
                }
                if (ctacte.habilitado === '2') {
                    icono = "<i class='icon-ok icon-info-sign' title='" + lang.deuda_pasiva + "'></i>";
                } else {
                    icono = "";
                }
                arrCtacte[ctacte.codigo] = {'order': key, 'checked': 0};
                $('#ctacteTable').dataTable().fnAddData([
                    "<input type='checkbox' data-orden-cobro='0' numerador='" + num[k] + "'  data-codigos=" + k + " onclick='checkCtacteChecked(this)' name='ctacte_selected[]' class='checkselect ace' saldo='" + ctacte.saldofaccob + "' value='" + ctacte.codigo + "' " + disabled + "/><span class='lbl'></span>",
                    ctacte.descripcion + ' ' + icono,
                    ctacte.fechavenc,
                    ctacte.saldofaccobformateado
                ]);
                if ((ctacte.saldocobrar - ctacte.pagado) != 0){
                    debe.push(k);
                }
                num[k]++;
            });
            $.fancybox.update();
        }
    });
}

function mySearch(element){
    var text = $(element).val();
    $('#ctacteTable').dataTable().fnFilter(text);
}

function getTableCtacte(){
    var trtable = "";
    trtable += '<table id="ctacteTable" cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" >';
    trtable += '<thead>';
    trtable += '<th></th>';
    trtable += '<th>' + langFrmCobro.descripcion + '</th>';
    trtable += '<th>' + langFrmCobro.fecha + '</th>';
    trtable += '<th>' + langFrmCobro.importe + '</th>';
    trtable += '</thead>';
    var debe = [];
    var num = [];
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
		"order": [[ 2, 'asc' ]], // por defecto se ordena de forma descendente por fecha de vencimiento
        "aoColumns" : [
            null , 
            null,
           { "sType": "uk_date"},
           null
        ]
    });
    $('#ctacteTable_filter').closest('.row').remove();
    $.fancybox.update();
    $.fancybox.reposition();
    $("#total-general-read").val(valorTotalFacturar);
    $("#total-general").val(valorTotalFacturar);
}

function getBancos(selectBancos) {
    $.ajax({
        url: BASE_URL + 'facturacion/getBancos',
        type: 'POST',
        dataType: 'json',
        cache: false,
        success: function(respuesta) {
            $(selectBancos).empty();
            $(selectBancos).append("<option ></option>");
            $(respuesta).each(function() {
                $(selectBancos).append("<option value='" + this.codigo + "'>" + this.nombre + "</option>");
            });
            $(selectBancos).trigger('chosen:updated');
        }
    });
}

function facturar() {
    var cantidad_maxima_renglones = 9; // se puede leer este valor desde una configuracion
    var mensaje = '';
    var alumnos = $("[name=alumnos]").val();
    var facturante = $("[name=facturante]").val();
    var fecha_factura = $("[name=fecha-factura]").val();
    var razones_sociales = $("[name=razones_sociales]").val();
    var tipo_factura = $("[name=tipo_factura]").val();
    var chk_ctacte_selected = new Array();

    oTableCtacte = $("#ctacteTable").dataTable();
    var arrCtacte = new Array();
    oTableCtacte.$('input').each(function () {
        if ( $(this).prop('checked') ) {
            //console.log("\nActual: ", $(this).prop('checked'));
            
            arrCtacte[$(this).attr('data-orden-cobro')] = $(this).val();
        }
    });
    //console.log(arrCtacte);

    $.each(arrCtacte, function( index, value ) {
        if (arrCtacte[index]) {
            /*console.log(index + ": " + value);
            lineasCtaCte += '&' + encodeURIComponent('checkctacte[]') + '=' + value;*/
            chk_ctacte_selected.push(value);
        }
    });

    if (chk_ctacte_selected.length == 0)
        mensaje += langFrmCobro.debe_seleccionar_al_menos_un_items_de_cuenta_corriente + "<br>";
    if (!validarFecha(fecha_factura))
        mensaje += langFrmCobro.fecha_de_factura_invalida + "<br>";
    if (razones_sociales == null)
        mensaje += langFrmCobro.no_se_seleccionado_razon_social + "<br>";
    if (alumnos == null)
        mensaje += langFrmCobro.debe_seleccionar_un_alumno + "<br>";
    if (tipo_factura == null)
        mensaje += langFrmCobro.indique_el_tipo_de_factura + "<br>";
    if (facturante == null)
        mensaje += langFrmCobro.no_se_selecciono_un_facturante + "<br>";
    if (mensaje != '') {
        gritter(mensaje);
    } else {
        var data = $(".form").serializeArray();
        var tipo_factura = $("[name=tipo_factura]").val();
        for (var i = 0; i < tipo_factura.length; i++) {
            data.push({name: "puntos_venta[]", value: tipo_factura[i]});
        }

        var cantidad_seleccionado = 0;
        var counterIncremental = 0;
        
        datatmp = new Array();
        for (x in chk_ctacte_selected) {
            cantidad_seleccionado ++;
            var name_valor = 'chk_ctacte_selected[' + counterIncremental + ']';
            data.push({
                name: name_valor,
                value: chk_ctacte_selected[x]
            });
            counterIncremental++;
        }

        data.pop();


        if (cantidad_seleccionado > cantidad_maxima_renglones){
            gritter(langFrmCobro.el_maximo_de_renglones_a_dacturar_no_debe_ser_mayor_a + " " + cantidad_maxima_renglones);
            $("#btn-facturar").prop("disabled", false);
        } else {
            /*console.log('data: ');
            console.log(data);
            return false;*/
            $.ajax({
                url: BASE_URL + 'facturacion/guardarFacturaCobro',
                data: data,
                type: 'POST',
                dataType: 'json',
                cache: false,
                success: function(respuesta) {
                    $("#btn-facturar").prop('disabled', false);
                    switch (respuesta.codigo) {
                        case 1:
                            $.gritter.add({
                                title: langFrmCobro.BIEN,
                                text: langFrmCobro.FACTURA_EMITIDA_CORRECTAMENTE,
                                sticky: false,
                                time: '3000',
                                class_name: 'gritter-success'
                            });
                            $.fancybox.close();
                            if (respuesta.custom.imprimir == "1") {
                                var factura = respuesta.custom.factura.join("-");
                                var cobro = respuesta.custom.cobro;
                                $.ajax({
                                    url: BASE_URL + 'facturacion/preguntar_imprimir_facturacion_cobro',
                                    type: 'POST',
                                    data: {
                                        factura: factura,
                                        cobro: cobro
                                    },
                                    success: function(_html) {
                                        $.fancybox.open(_html, {
                                            scrolling: true,
                                            width: 'auto',
                                            height: 'auto',
                                            autoSize: false,
                                            padding: 0,
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
                            break;

                        case 0:
                            $(".errores").html(respuesta.msgerror);
                        default :
                            $.gritter.add({
                                title: langFrmCobro.ERROR,
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
}

function cobrar() {
    var cantidad_maxima_renglones = 9;
    var mensaje = '';
    var alumnos = $("[name=alumnos]").val();
    var facturante = $("[name=facturante]").val();
    var fecha_factura = $("[name=fecha-factura]").val();
    var razones_sociales = $("[name=razones_sociales]").val();
    var tipo_factura = $("[name=tipo_factura]").val();
    var chk_ctacte_selected = new Array();
    for (x in arrCtacte) {
        if (arrCtacte[x].checked == 1) {
            chk_ctacte_selected.push(x);
        }
    }
    var total_facturacion = 0;
    if (tipo_factura != null){
        var encontrado = false;
        for (var i = 0; i < tipo_factura.length; i++){
            var tipo = puntoVentaTipoFactura[tipo_factura[i]];
            for (var j = i + 1; j < tipo_factura.length; j++) {
                if (puntoVentaTipoFactura[tipo_factura[j]] == tipo) {
                    encontrado = true;
                }
            }
        }
        if (encontrado) {
            gritter(langFrmCobro.ha_seleccionado_tipos_de_datos_de_facturas_repetidos);
        } else {
//            for (var i = 0; i < tipo_factura.length; i++){
//                total_facturacion += puntoVentaPorventaje[tipo_factura[i]];
//            }
//            if (total_facturacion != 100) {
//                mensaje += langFrmCobro.la_configuracion_de_porcentajes_de_facturacion_sobre_los_puntos_de_venta_seleccionados_debe_ser_del_100 + "<br>";
//            }
        }
    } else {
        mensaje += langFrmCobro.indique_el_tipo_de_factura + "<br>";
    }
    if (chk_ctacte_selected.length == 0)
        mensaje += langFrmCobro.debe_seleccionar_al_menos_un_items_de_cuenta_corriente + "<br>";
    if (!validarFecha(fecha_factura))
        mensaje += langFrmCobro.fecha_de_factura_invalida + "<br>";
    if (razones_sociales == null)
        mensaje += langFrmCobro.no_se_seleccionado_razon_social + "<br>";
    if (alumnos == null)
        mensaje += langFrmCobro.debe_seleccionar_un_alumno + "<br>";
    if (facturante == null)
        mensaje += langFrmCobro.no_se_selecciono_un_facturante + "<br>";
    if (mensaje != '') {
        gritter(mensaje);
    } else {
        var data = $(".form").serializeArray();
        $.each(arrCtacte, function(index, value){
            if (value == 1){
                data.push({name: 'chk_ctacte_selected[]', value: index});
            }
        });
        var cantidad_seleccionado = 0;
        for (x in arrCtacte){
            if (arrCtacte[x].checked == 1){
                data.push({name: 'chk_ctacte_selected[]', value: x});
                cantidad_seleccionado ++;
            }
        }
        if (cantidad_seleccionado > cantidad_maxima_renglones){
            gritter(langFrmCobro.el_maximo_de_renglones_a_dacturar_no_debe_ser_mayor_a + " " + cantidad_maxima_renglones);
        } else {
            for (var i = 0; i < tipo_factura.length; i++){
                data.push({name: 'puntos_venta[]', value: tipo_factura[i]});
            }
            $.ajax({
                url: BASE_URL + 'facturacion/validarDatosCobrar',
                data: data,
                type: 'POST',
                dataType: 'json',
                cache: false,
                success: function(respuesta) {
                    switch (respuesta.codigo) {
                        case "0":
                            gritter(respuesta.msgerror);
                            break;

                        case "1" :
                            $("[name=total_general]").val(respuesta.saldo_facturacion);
                            $(".factura").hide();
                            $(".cobro").show();
                            $('.cobro select').chosen();
                            $("#btn-cobrar").hide();
                            $("#btn-facturar").show();
                            $("#btn-volver").show();
                            $.fancybox.update();
                            $.fancybox.reposition();
                            break;
                    }
                }
            });
        }
    }
}

function calcularTotalFacturacion() {
    var mensaje = '';
    var chk_ctacte_selected = new Array();
    for (x in arrCtacte) {
        if (arrCtacte[x].checked == 1) {
            chk_ctacte_selected.push(x);
        }
    }
    if (chk_ctacte_selected.length == 0)
        mensaje += langFrmCobro.debe_seleccionar_al_menos_un_items_de_cuenta_corriente + "<br>";
    if (mensaje != '') {
        gritter(mensaje);
    } else {
        $.ajax({
            url: BASE_URL + 'facturacion/getSaldo',
            type: 'POST',
            dataType: "json",
            data: {
                chk_ctacte_selected: chk_ctacte_selected
            },
            success: function(_json) {
                if (_json.codigo == 1) {
                    $("[name=total_general]").val(_json.saldo_facturacion);
                } else {
                    gritter(_json.msgerror);
                }
            }
        });
    }
}

function getTiposCheque(selecttipocheque) {
    $.ajax({
        url: BASE_URL + 'facturacion/getTiposCheque',
        type: 'POST',
        dataType: 'json',
        cache: false,
        success: function(respuesta) {
            $(selecttipocheque).empty();
            $(selecttipocheque).append("<option ></option>");
            $(respuesta).each(function() {
                $(selecttipocheque).append("<option value='" + this.codigo + "'>" + this.nombre + "</option>");
            });
            $(selecttipocheque).trigger('chosen:updated');
        }
    });
}

function getTiposFacturas(){
    var facturante = $("#facturante").val();
    var razon_social = $("#razones_sociales").val();
    if (razon_social == null) {
        $("[name=tipo_factura]").empty();
        $("[name=tipo_factura]").trigger('chosen:updated');
    } else {
        var punto_venta = $("#facturante option:selected").attr("punto-venta");
        $.ajax({
            url: BASE_URL + 'facturacion/getTiposFacturaFacturante',
            data: 'cod_facturante=' + facturante + "&cod_razon_social=" + razon_social + "&punto-venta=" + punto_venta,
            type: 'POST',
            dataType: 'json',
            cache: false,
            success: function(respuesta){
                $('select[name="tipo_factura"]').empty();
                $(respuesta).each(function(){
                    var select = '';
                    if (respuesta.length == 1){
                        select = 'selected';
                    }
                    $('select[name="tipo_factura"]').append('<option value="' + this.codigo + '" ' + select + '>' + this.factura + '</option>');
                    puntoVentaPorventaje[this.codigo] = parseFloat(this.porcentaje);
                    puntoVentaTipoFactura[this.codigo] = parseInt(this.tipo);
                });
                $('select[name="tipo_factura"]').trigger('chosen:updated');
            }
        });
    }
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
