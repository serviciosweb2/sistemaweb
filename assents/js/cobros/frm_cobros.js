var clavesFRM = Array("validacion_ok", "TARJETA", "deuda_pasiva", "banco", "caja", "eliminacion_ok", "DEPOSITO_BANCARIO", "tipo_cheque", "TRANSFERENCIA", "medio_deposito_transaccion_factura", "numero_cupon",
        "validacion_ok", "pos", "fecha", "BIEN", "ERROR", "tipo_tarjeta", "no_tiene_imputaciones", "seleccione_tipo", "codigo_autorizacion", "cajas_habilitadas_para_este_medio", "codigo_cupon", "terminal",
        "alumnos_cobro", "medio_cheque_numero_factura", "medio_cheque_emisor_factura", "medio_deposito_cuenta_factura", "importe", "descripcion", "fecha_vencimiento", "caja_cerrada");

var langFRM = '';
var imputado = '';
var oTableCtacte;
var oTableImputaciones;
var validacionCheckboxCtaCte = 0;// ESTE PARAMETRO TIENE QUE VENIR DE CONFIGURACION.TERMINAR.SE QUITA RESTRICCION POR PEDIDO DE AGUSTINA
var arrCtacte = new Array();
var ingresoDeUsuario = 0;

Array.prototype.contains = function(element) {
    for (var i = 0; i < this.length; i++) {
        if (this[i] == element) {
            return true;
        }
    }
    return false;
};

$('.fancybox-wrap').ready(function() {
    $.ajax({
        url: BASE_URL + 'entorno/getLang',
        data: "claves=" + JSON.stringify(clavesFRM),
        type: "POST",
        dataType: "JSON",
        async: false,
        cache: false,
        success: function(respuesta) {
            langFRM = respuesta;
        }
    });

    $('.datos').hide();
    $("select[name='alumnos']").ajaxChosen({
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
                ingresoDeUsuario = 0;
            }
        });
    });

    $('.pago').hide();
    $('button[name="volver"],button[name="confirmar"],button[name="confirmar_imp"]').hide();
    getTableCtacte();
    oTableImputaciones = $('#tablaImputaciones').dataTable({
        "bFilter": false,
        "bLengthChange": false,
        "bPaginate": false
    });
    if ($("input[name='codigo']").val() != '') {
        dibujarTabla(CTACTE_IMPUTAR);
        dibujarTablaImputaciones();
        datosCobro(true);
    }
    var day = new Date();
    $('.fancybox-wrap input[name="fecha_cobro"]').datepicker({
        changeMonth: false,
        changeYear: false,
        maxDate: "+" + (30 - day.getDate()) + "D"
    });
    moneda = $('input[name="moneda"]').attr('data-simbolo');
    $('.fancybox-wrap select[name="alumnos"],select[name="medio_cobro"]').chosen({
        width: '100%',
        allow_single_deselect: true
    });

    if (COBRO_ESTADO == 'confirmado') {
        $('#cobrar').find('input:text', 'select').not('#buscador').prop('disabled', true);
        $('#cobrar').find('select').trigger('chosen:updated');
    }
});

function mySearch(element) {
    var text = $(element).val();
    $('#detalleCtacte').dataTable().fnFilter(text);
}

function getTableCtacte() {
    var trtable = "";
    trtable += '<table id="detalleCtacte" cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered">';
    trtable += '<thead>';
    trtable += '<th></th>';
    trtable += '<th>' + langFRM.descripcion + '</th>';
    trtable += '<th style="width: 170px;">' + langFRM.fecha_vencimiento + '</th>';
    trtable += '<th style="width: 170px;">' + langFRM.importe + '</th>';
    trtable += '</thead>';
    $('.content-tabla-ctacte').html("");
    $('.content-tabla-ctacte').html(trtable);

    jQuery.extend(
        jQuery.fn.dataTableExt.oSort,
        {
            "date-uk-pre": function ( a ) {
                var ukDatea = a.split('/');
                return (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
            },
            "date-uk-asc": function ( a, b ) {
                return ((a < b) ? -1 : ((a > b) ? 1 : 0));
            },
            "date-uk-desc": function ( a, b ) {
                return ((a < b) ? 1 : ((a > b) ? -1 : 0));
            }
        }
    );

    oTableCtacte = $('#detalleCtacte').dataTable({
        "aoColumns": [
            null,
            null,
            { "sType": "date-uk" },
            null
        ],
        "aLengthMenu": [[5, 10, 25], [5, 10, 25]],
        "bFilter": true,
        "bLengthChange": false,
        "iDisplayLength": 3,
        "language": {
            "search": ""
        },
        "order": [[ 2, 'asc' ]]
    });
    $('#detalleCtacte_filter').closest('.row').remove();
    $.fancybox.update();
    $.fancybox.reposition();
}

function validarEntrada(elemento) {
    var resultado = [];
    resultado.codigo = 1;
    resultado.msg = '';
    var valorEntrada = $(elemento).val();
    if (valorEntrada.length == 0) {
        $(elemento).closest('.input-group').removeClass('has-error');
    } else {
        var pattern = new RegExp("^([0-9]{0,10})\\" + BASE_SEPARADOR + "?([0-9]{1," + BASE_DECIMALES + "})$");
        function msjError() {
            var cadena = 'Formato esperado ' + 'XX' + BASE_SEPARADOR;
            for (var i = 0; i < 2; i++) {
                cadena += 'X';
            }
            return cadena;
        }
        if (pattern.test(valorEntrada)) {
            $(elemento).closest('.input-group').removeClass('has-error');
        } else {
            $(elemento).closest('.input-group').addClass('has-error');
            $.gritter.add({
                title: 'Upps!',
                text: msjError(),
                sticky: false,
                time: '3000',
                class_name: 'gritter-error'
            });
        }
    }
    return resultado;
}

function cargarSelect(select, opciones, seleccionar) {
    if (!permite_editar_medios){
        select.prop("disabled", true);
    }
    var selected = '';
    var selectName = select.attr('name');
    var opcionesARRAY = JSON.parse(opciones);
    switch (selectName) {
        case 'razones_sociales':
            $(opcionesARRAY).each(function() {
                selected = seleccionar == this.cod_razon_social ? 'selected' : '';
                $(select).append('<option value="' + this.cod_razon_social + '"' + selected + '>' + this.razon_social + '</option>');
            });
            break;

        case 'caja':
            $(opcionesARRAY).each(function() {
                selected = seleccionar == this.codigo ? 'selected' : '';
                if (this.estado == 'abierta' || this.conf_automatica == 0) {
                    $(select).append('<option value="' + this.codigo + '"' + selected + '>' + this.nombre + '</option>');
                } else {
                    $(select).append('<option value="' + this.codigo + '"' + selected + ' disabled="true">' + this.nombre + "&nbsp;(" + langFRM.caja_cerrada + ")" + '</option>');
                }
            });
            $('select[name="caja"]').trigger('chosen:updated');
            break;

        case 'pos_tarjeta':
            $(opcionesARRAY).each(function() {
                selected = seleccionar == this.codigo ? 'selected' : '';
                $(select).append('<option value="' + this.codigo + '"' + selected + '>' + this.detalle + '</option>');
            });
            break;

        case 'tarjetas':
            $(opcionesARRAY).each(function() {
                selected = seleccionar == this.codigo ? 'selected' : '';
                $(select).append('<option value="' + this.codigo + '"' + selected + '>' + this.nombre + '</option>');
            });
            break;

        case 'medio_tarjeta_banco':
            $(opcionesARRAY).each(function() {
                selected = seleccionar == this.codigo ? 'selected' : '';
                $(select).append('<option value="' + this.codigo + '"' + selected + '>' + this.nombre + '</option>');
            });
            break;

        case 'medio_cheque_banco':
            $(opcionesARRAY).each(function() {
                selected = seleccionar == this.codigo ? 'selected' : '';
                $(select).append('<option value="' + this.codigo + '"' + selected + '>' + this.nombre + '</option>');
            });
            break;

        case 'medio_cheque_tipo':
            $(opcionesARRAY).each(function() {
                selected = seleccionar == this.id ? 'selected' : '';
                $(select).append('<option value="' + this.id + '"' + selected + '>' + this.nombre + '</option>');
            });
            break;

        case 'medio_deposito_banco':
            $(opcionesARRAY).each(function() {
                selected = seleccionar == this.codigo ? 'selected' : '';
                $(select).append('<option value="' + this.codigo + '"' + selected + '>' + this.nombre + '</option>');
            });
            break;

        case 'medio_transferencia_banco':
            $(opcionesARRAY).each(function() {
                selected = seleccionar == this.codigo ? 'selected' : '';
                $(select).append('<option value="' + this.codigo + '"' + selected + '>' + this.nombre + '</option>');
            });
            break;
    }

    $(select).chosen({
        width: '100%'
    });
}

function dibujarTabla(td) {
    oTableCtacte.fnClearTable();
    var tdARRAY = JSON.parse(td);
    arrCtacte = new Array();
    var debe = [];
    var num = [];
    $(tdARRAY).each(function(key, ctacte) {
        var k = ctacte.concepto + '-' + ctacte.cod_concepto;
        var disabled = '';
        if (validacionCheckboxCtaCte == 1){
            if (debe.contains(k)){
                disabled = "disabled";
            } else {
                num[k] = 0;
            }
        }
        if (this.habilitado === '2') {
            icono = "<i class='icon-ok icon-info-sign' title='" + langFRM.deuda_pasiva + "'></i>";
        } else {
            icono = "";
        }
        arrCtacte[ctacte.codigo] = {'order': key, 'checked': 0};
        $('#detalleCtacte').dataTable().fnAddData([
            "<input type='checkbox' numerador='" + num[k] + "' data-orden-cobro=0  data-codigos=" + k + " onclick='checkCtacteChecked(this)' name='checkctacte[]' class='checkselect ace' value='" + this.codigo + "' data-saldo='" + this.importe + "'" + disabled + "/><spam class='lbl'></spam>",
            this.descripcion + ' ' + icono,
            this.fechavenc,
            this.simbolo_moneda + this.saldocobrarformateado]);
        if ((ctacte.saldocobrar - ctacte.pagado) != 0) {
            debe.push(k);
        }
        num[k]++;
    });
    $.fancybox.update();
    $('.datos').fadeIn();
    calcularRestaImputar();
}

function dibujarTablaImputaciones() {
    oTableImputaciones.fnClearTable();
    $.ajax({
        url: BASE_URL + 'cobros/getImputacionesCobro',
        type: 'POST',
        data: 'codigo=' + $("input[name='codigo']").val(),
        cache: false,
        dataType: 'json',
        success: function(respuesta) {
            $(respuesta).each(function() {
                oTableImputaciones.fnAddData([
                    '<i class="icon-trash bigger-200 red eliminaImputacion" imputacion="' + this.codigo + '"></i>',
                    this.descripcion,
                    this.vencimiento,
                    this.fecha_imputacion,
                    this.valorImputacion,
                    this.estado
                ]);
            });
            if (respuesta.length < 1) {
                oTableImputaciones.fnAddData([
                    '',
                    langFRM.no_tiene_imputaciones,
                    '',
                    '',
                    '',
                    ''
                ]);
            }
            $.fancybox.update();
            calcularTotImputado();
        }
    });
}

function getBancos() {
    var c = '';
    $.ajax({
        url: BASE_URL + 'cobros/getBancos',
        data: '',
        type: 'POST',
        cache: false,
        async: false,
        success: function(respuesta) {
            c = respuesta;
        }
    });
    return c;
}

function getCajas() {
    var c = '';
    $.ajax({
        url: BASE_URL + 'cobros/getCajasCobrar',
        data: {
            cod_medio: $('select[name="medio_cobro"]').val(),
            codigo: $("input[name='codigo']").val()
        },
        type: 'POST',
        async: false,
        cache: false,
        success: function(respuesta) {
            c = respuesta;
        }
    });
    return c;
}

function getTerminales() {
    var c = '';
    $.ajax({
        url: BASE_URL + 'cobros/getTerminales',
        data: '',
        type: 'POST',
        cache: false,
        async: false,
        success: function(respuesta) {
            c = respuesta;
        }
    });
    return c;
}

function getTarjetas() {
    var cod_terminal = $('select[name="pos_tarjeta"]').val();
    var c = '';
    $.ajax({
        url: BASE_URL + 'cobros/getTarjetas',
        data: {codigo: cod_terminal},
        type: 'POST',
        cache: false,
        async: false,
        success: function(respuesta) {
            c = respuesta;
        }
    });
    return c;
}


function getTarjetasDebito() {
    var cod_terminal = $('select[name="pos_tarjeta"]').val();
    var c = '';
    $.ajax({
        url: BASE_URL + 'cobros/getTarjetasDebito',
        data: {codigo: cod_terminal},
        type: 'POST',
        cache: false,
        async: false,
        success: function(respuesta) {
            c = respuesta;
        }
    });
    return c;
}



function getTiposCheque() {
    var c = '';
    $.ajax({
        url: BASE_URL + 'cobros/getTiposCheque',
        data: '',
        type: 'POST',
        cache: false,
        async: false,
        success: function(respuesta) {
            c = respuesta;
        }
    });
    return c;
}

function crearSelect(nombre) {
    var label = '';
    switch (nombre) {
        case 'pos_tarjeta':
            label = langFRM.terminal;
            break;
        case 'tarjetas':
            label = langFRM.TARJETA;
            break;
        case 'medio_tarjeta_banco':
            label = langFRM.banco;
            break;
        case 'medio_cheque_banco':
            label = langFRM.banco;
            break;
        case 'medio_cheque_tipo':
            label = langFRM.tipo_cheque;
            break
        case 'medio_deposito_banco':
            label = langFRM.DEPOSITO_BANCARIO;
            break;
        case 'medio_transferencia_banco':
            label = langFRM.banco;
            break;
    }
    $('.fancybox-wrap .pago').append('<div class="form-group col-md-4 Mpago dinamico"><label for="exampleInputFile">' + label + '</label><select name="' + nombre + '" class="form-control" title=""></select></div>');
}

function validarInstancia() {
    var msj = '';
    var seguir = true;
    if (!$('select[name="alumnos"]').val()) {
        seguir = false;
        msj += '<p>' + langFRM.alumnos_cobro + '</p>';
    }
    if (seguir == false) {
        gritter(msj);
    } else {
        $('button[name="cobrar"]').hide();
        $('button[name="volver"]').show();
        $('button[name="confirmar"]').show();
        $('button[name="confirmar_imp"]').show();
        $('.pago').fadeIn();
        $('.datos').hide();
        $('.totales').hide();
    }
}

function calTotal() {
    var importes = [];
    var dataPOST = oTableCtacte.$('input[name="checkctacte[]"]').serialize();
    $(oTableCtacte.$('input[name="checkctacte[]"]:checked')).each(function(k, elemento) {
        importes[k] = $(elemento).closest('tr').find('td:eq(3)').text();
    });

    if (dataPOST == '') {
        dataPOST = 'checkctacte%5B%5D=&importes%5B%5D=';
    } else {
        dataPOST += '&' + $.param({'importes': importes});
    }

    $.ajax({
        url: BASE_URL + 'cobros/calcularTotal',
        type: "POST",
        data: dataPOST,
        dataType: "JSON",
        cache: false,
        success: function(respuesta) {
            if (respuesta.codigo == 1) {
                $('.fancybox-wrap').find('input[name="total_cobrar"]').val(respuesta.total);
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
}

function calcularTotImputado() {
    $.ajax({
        url: BASE_URL + 'cobros/getTotalImputaciones',
        type: "POST",
        data: 'cod_cobro=' + $("input[name='codigo']").val(),
        dataType: "JSON",
        cache: false,
        success: function(respuesta) {
            $('#tot_imp').text(respuesta[0].totImputaciones);
        }
    });
    return false;
}

function calcularRestaImputar() {
    $.ajax({
        url: BASE_URL + 'cobros/getRestaImputar',
        type: "POST",
        data: {cod_cobro: $("input[name='codigo']").val(), total: $('.fancybox-wrap').find('input[name="total_cobrar"]').val()},
        dataType: "JSON",
        cache: false,
        success: function(respuesta) {
            $('#res_imp').text(respuesta);
        }
    });
    return false;
}

var monto = function montoTotal() {
    var montoFinal = 0.00;
    $('input[name="checkctacte[]"]:checked').each(function(key, value) {
        var x = $(value).attr('data-saldo');
        montoFinal = parseFloat(x) + parseFloat(montoFinal);
    });
    $('input[name="total"]').val(moneda + montoFinal);
    $('input[name="total_cobrar"]').val(montoFinal);
};

function eliminarImputacion(dato) {
    var codigo = $(dato).attr('imputacion');
    $.ajax({
        url: BASE_URL + 'cobros/eliminarImputacion',
        type: 'POST',
        data: 'codigo=' + codigo,
        dataType: "JSON",
        cache: false,
        success: function(respuesta) {
            if (respuesta.codigo == 1) {
                $.gritter.add({
                    title: langFRM.BIEN,
                    text: langFRM.eliminacion_ok,
                    sticky: false,
                    time: '3000',
                    class_name: 'gritter-success'
                });

                oTableCtacte.fnClearTable();
                oTableImputaciones.fnClearTable();
                var cod_alumno = $('select[name="alumnos"]').val();
                $.ajax({
                    url: BASE_URL + 'cobros/getCtaCteImputar',
                    type: 'POST',
                    data: 'cod_alumno=' + cod_alumno,
                    cache: false,
                    success: function(respuesta) {
                        dibujarTabla(respuesta);
                        $.fancybox.update();
                    }
                });

                dibujarTablaImputaciones();
                $.fancybox.update();
            } else {
                $.gritter.add({
                    title: langFRM.ERROR,
                    text: respuesta.msgerror,
                    sticky: false,
                    time: '3000',
                    class_name: 'gritter-error'
                });
            }
        }
    });
}

function datosCobro(inimodificar) {
    $('.fancybox-wrap .pago #label_caja').empty();
    $('select[name="caja"]').empty();
    $('select[name="caja"]').trigger('chosen:updated');
    var medio_cobro = $('select[name="medio_cobro"]').val();
    $('.dinamico').remove();
    if (medio_cobro != ''){
        var cajas = getCajas();
        if (JSON.parse(cajas).length < 1) {
            var htmlDetalle = '<label id="label_caja" class="red">' + langFRM.cajas_habilitadas_para_este_medio + '</label>';
            $('.fancybox-wrap .pago').append(htmlDetalle);
        } else {
            var disabled = !permite_editar_medios ? 'disabled="true"' : '';
            var select = $('select[name="caja"]');
            var seleccionar = inimodificar ? $("input[name='cajaCobro']").val() : '';
            cargarSelect(select, cajas, seleccionar);
            var arrMedio = MEDIO_COBRO != '' ? JSON.parse(MEDIO_COBRO) : [];
            switch (medio_cobro) {
                case '1':
                    $.fancybox.update();
                    break;

                case '3':
                    var pos = getTerminales();
                    if (jQuery.parseJSON(pos).length > 0) {
                        crearSelect('pos_tarjeta');
                        var select3 = $('select[name="pos_tarjeta"]');
                        var seleccionar = inimodificar ? arrMedio.cod_terminal : '';
                        cargarSelect(select3, pos, seleccionar);
                    }

                    crearSelect('tarjetas');
                    var tarjetas = getTarjetas();
                    $('select[name="tarjetas"]').empty();
                    var select = $('select[name="tarjetas"]');
                    var seleccionar = inimodificar ? arrMedio.cod_tipo : '';
                    cargarSelect(select, tarjetas, seleccionar);
                    var seleccionar = inimodificar ? arrMedio.cupon : '';
                    $('.pago').append('<div class="form-group Mpago dinamico col-xs-4"><label for="exampleInputFile">' + langFRM.codigo_cupon + '</label><input name="medio_tarjeta_cupon" value="' + seleccionar + '" class="form-control" ' + disabled + '></div>');
                    var seleccionar = inimodificar ? arrMedio.cod_autorizacion : '';
                    $('.pago').append('<div class="form-group Mpago dinamico col-xs-4"><label for="exampleInputFile">' + langFRM.codigo_autorizacion + '</label><input name="medio_tarjeta_autorizacion" value="' + seleccionar + '" class="form-control" ' + disabled + '></div>');
                    $.fancybox.update();
                    break;

                case '4':
                    var bancos = getBancos();
                    crearSelect('medio_cheque_banco');
                    var select = $('select[name="medio_cheque_banco"]');
                    var seleccionar = inimodificar ? arrMedio.cod_banco_emisor : '';
                    cargarSelect(select, bancos, seleccionar);
                    var tipos = getTiposCheque();
                    crearSelect('medio_cheque_tipo');
                    var select = $('select[name="medio_cheque_tipo"]');
                    var seleccionar = inimodificar ? arrMedio.tipo_cheque : '';
                    cargarSelect(select, tipos, seleccionar);
                    var seleccionar = inimodificar ? arrMedio.fecha_cobro : '';
                    $('.pago').append('<div class="form-group col-md-4 Mpago dinamico"><label for="exampleInputFile">' + langFRM.fecha + '</label><input id="datepickerCheque" class="form-control date-picker" type="text"  value="' + seleccionar + '" name="medio_cheque_fecha" ' + disabled + '></div>');
                    var seleccionar = inimodificar ? arrMedio.nro_cheque : '';
                    $('.pago').append('<div class="form-group col-md-4 Mpago dinamico"><label for="exampleInputFile">' + langFRM.medio_cheque_numero_factura + '</label><input name="medio_cheque_numero" value="' + seleccionar + '" class="form-control" ' + disabled + '></div>');
                    var seleccionar = inimodificar ? arrMedio.emisor : '';
                    $('.pago').append('<div class="form-group  col-md-4 Mpago dinamico"><label for="exampleInputFile">' + langFRM.medio_cheque_emisor_factura + '</label><input name="medio_cheque_emisor" value="' + seleccionar + '" class="form-control" ' + disabled + '></div>');
                    $('#datepickerCheque').datepicker({
                        format: "dd/mm/yyyy",
                        language: "es",
                        autoclose: true
                    });
                    $.fancybox.update();
                    break;

                case '6':
                    var bancos = getBancos();
                    crearSelect('medio_deposito_banco');
                    var select = $('select[name="medio_deposito_banco"]');
                    var seleccionar = inimodificar ? arrMedio.cod_banco : '';
                    cargarSelect(select, bancos, seleccionar);
                    var seleccionar = inimodificar ? arrMedio.fecha_hora : '';
                    $('.pago').append('<div class="form-group col-md-4 Mpago dinamico"><label for="exampleInputFile">' + langFRM.fecha + '</label><input id="fechaDeposito" name="medio_deposito_fecha" value="' + seleccionar + '" class="form-control" ' + disabled + '></div>');
                    var seleccionar = inimodificar ? arrMedio.nro_transaccion : '';
                    $('.pago').append('<div class="form-group col-md-4 Mpago dinamico"><label for="exampleInputFile">' + langFRM.medio_deposito_transaccion_factura + '</label><input name="medio_deposito_transaccion" value="' + seleccionar + '" class="form-control" ' + disabled + '></div>');
                    var seleccionar = inimodificar ? arrMedio.cuenta_nombre : '';
                    $('.pago').append('<div class="form-group col-md-4 Mpago dinamico"><label for="exampleInputFile">' + langFRM.medio_deposito_cuenta_factura + '</label><input name="medio_deposito_cuenta" value="' + seleccionar + '" class="form-control" ' + disabled + '></div>');
                    $('input[name="medio_deposito_fecha"]').datepicker({
                        format: "dd/mm/yyyy",
                        language: "es",
                        autoclose: true
                    });
                    $.fancybox.update();
                    break;

                case '7':
                    var bancos = getBancos();
                    crearSelect('medio_transferencia_banco');
                    var select = $('select[name="medio_transferencia_banco"]');
                    var seleccionar = inimodificar ? arrMedio.cod_banco : '';
                    cargarSelect(select, bancos, seleccionar);
                    var seleccionar = inimodificar ? arrMedio.fecha_hora : '';
                    $('.pago').append('<div class="form-group col-md-4 Mpago dinamico"><label for="exampleInputFile">' + langFRM.fecha + '</label><input id="fechaTrasferencia" name="medio_transferencia_fecha" value="' + seleccionar + '" class="form-control" ' + disabled + '></div>');
                    var seleccionar = inimodificar ? arrMedio.nro_transaccion : '';
                    $('.pago').append('<div class="form-group col-md-4 Mpago dinamico"><label for="exampleInputFile">' + langFRM.medio_deposito_transaccion_factura + '</label><input name="medio_transferencia_numero" value="' + seleccionar + '" class="form-control" ' + disabled + '></div>');
                    var seleccionar = inimodificar ? arrMedio.cuenta_nombre : '';
                    $('.pago').append('<div class="form-group col-md-4 Mpago dinamico"><label for="exampleInputFile">' + langFRM.medio_deposito_cuenta_factura + '</label><input name="medio_transferencia_cuenta" value="' + seleccionar + '" class="form-control" ' + disabled + '></div>');
                    $('input[name="medio_transferencia_fecha"]').datepicker({
                        format: "dd/mm/yyyy",
                        language: "es",
                        autoclose: true
                    });
                    $.fancybox.update();
                    break;

                case '8':
                    var pos = getTerminales();
                    if (jQuery.parseJSON(pos).length > 0) {
                        crearSelect('pos_tarjeta');
                        var select3 = $('select[name="pos_tarjeta"]');
                        var seleccionar = inimodificar ? arrMedio.cod_terminal : '';
                        cargarSelect(select3, pos, seleccionar);
                    }

                    crearSelect('tarjetas');
                    var tarjetas = getTarjetasDebito();
                    $('select[name="tarjetas"]').empty();
                    var select = $('select[name="tarjetas"]');
                    var seleccionar = inimodificar ? arrMedio.cod_tipo : '';
                    cargarSelect(select, tarjetas, seleccionar);
                    var seleccionar = inimodificar ? arrMedio.cupon : '';
                    $('.pago').append('<div class="form-group Mpago dinamico col-xs-4"><label for="exampleInputFile">' + langFRM.codigo_cupon + '</label><input name="medio_tarjeta_cupon" value="' + seleccionar + '" class="form-control" ' + disabled + '></div>');
                    var seleccionar = inimodificar ? arrMedio.cod_autorizacion : '';
                    $('.pago').append('<div class="form-group Mpago dinamico col-xs-4"><label for="exampleInputFile">' + langFRM.codigo_autorizacion + '</label><input name="medio_tarjeta_autorizacion" value="' + seleccionar + '" class="form-control" ' + disabled + '></div>');
                    $.fancybox.update();
                    break;
            }
        }
    }
}

function guardarCobro() {
    var ajaxParameters = null;
    var dataPOST = $('input[type!="checkbox"], select').serialize();
    var arrayCtaCte = new Array();
    oTableCtacte.$('input').each(function () {
        if ( $(this).prop('checked') ) {
            arrayCtaCte[$(this).attr('data-orden-cobro')] = $(this).val();
        }
    });

    lineasCtaCte = "";
    $.each(arrayCtaCte, function( index, value ) {
        if (arrayCtaCte[index]) {
            lineasCtaCte += '&' + encodeURIComponent('checkctacte[]') + '=' + value;
        }
    });

    ajaxParameters = dataPOST + lineasCtaCte;

    $.ajax({
        url: BASE_URL + 'cobros/guardarCobro',
        data: ajaxParameters,
        type: 'POST',
        dataType: "JSON",
        cache: false,
        success: function(respuesta) {
            if (respuesta.codigo == 1) {
                $.gritter.add({
                    title: langFRM.BIEN,
                    text: langFRM.validacion_ok,
                    sticky: false,
                    time: '3000',
                    class_name: 'gritter-success'
                });
                $.fancybox.close(true);
                oTable.fnDraw();
                var cod_recibo = respuesta.custom;
                var param = new Array();
                param.push(cod_recibo);
                printers_jobs(10, param);
            } else {
                $.gritter.add({
                    title: langFRM.ERROR,
                    text: respuesta.msgerror,
                    sticky: false,
                    time: '3000',
                    class_name: 'gritter-error'
                });
            }
        }
    });
    return false;
}

function guardarImputaciones() {
    var dataPOST = $('input[type!="checkbox"], select').serialize();
    $.ajax({
        url: BASE_URL + 'cobros/guardarImputacionesCobro',
        data: dataPOST + '&' + oTableCtacte.$('input').serialize(),
        type: 'POST',
        dataType: "JSON",
        cache: false,
        success: function(respuesta) {
            if (respuesta.codigo == 1) {
                $.gritter.add({
                    title: langFRM.BIEN,
                    text: langFRM.validacion_ok,
                    sticky: false,
                    time: '3000',
                    class_name: 'gritter-success'
                });
                $.fancybox.close(true);
                oTable.fnDraw();
                var cod_recibo = respuesta.custom;
                var param = new Array();
                param.push(cod_recibo);
                printers_jobs(10, param);
            } else {
                $.gritter.add({
                    title: langFRM.ERROR,
                    text: respuesta.msgerror,
                    sticky: false,
                    time: '3000',
                    class_name: 'gritter-error'
                });
            }
        }
    });
    return false;
}

function checkCtacteChecked(element) {
    arrCtacte[element.value]['checked'] = element.checked ? 1 : 0;
    var atributo = $(element).attr('data-codigos');
    var numerador = $(element).attr('numerador');
    if (validacionCheckboxCtaCte == 1) {
        if (element.checked){
            numerador++;
            oTableCtacte.$('input[data-codigos="' + atributo + '"][numerador="' + numerador + '"]').prop('disabled', false);
        } else {
            var bloquiado = false;
            oTableCtacte.$('input[data-codigos="' + atributo + '"]').not(':disabled').each(function(k, elemento) {
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

$('.fancybox-wrap').on('change', 'select[name="alumnos"]', function(){
    cod_alumno = $(this).val();
    element = $('select[name="razones_sociales"]');
    $.ajax({
        url: BASE_URL + 'cobros/getRazonesAlumno',
        type: 'POST',
        data: 'codigo=' + cod_alumno,
        cache: false,
        success: function(respuesta) {
            $.ajax({
                url: BASE_URL + 'cobros/getCtaCteImputar',
                type: 'POST',
                data: 'cod_alumno=' + cod_alumno,
                cache: false,
                success: function(respuesta) {
                    dibujarTabla(respuesta);
                    get_medio_pago_cuotas_alumno();
                    $.fancybox.update();
                }
            });
        }
    });
});

$('.fancybox-wrap').on('change', 'select[name="medio_cobro"]', function() {
    datosCobro(false);
});

$('.fancybox-wrap').on('click', '.modal-footer button', function() {
    var boton = $(this).attr('name');
    switch (boton) {
        case 'cobrar':
            var valorTotal = $('.fancybox-wrap').find('input[name="total_cobrar"]').val();
            if (ingresoDeUsuario == 1 && (valorTotal == '' || valorTotal == 0)) {
                calTotal();
            }
            if (ingresoDeUsuario == 0) {
                calTotal();
            }
            validarInstancia();
            $.fancybox.update();
            break;

        case 'volver':
            $('.principal').show();
            $('.totales').show();
            $('button[name="cobrar"]').show();
            $('button[name="confirmar"],button[name="volver"],button[name="confirmar_imp"]').hide();
            $('select[name="alumnos"] option').not(':selected').prop('disabled', false);
            $('select[name="alumnos"]').trigger('chosen:updated');
            $('.pago').hide();
            $('.datos').fadeIn();
            $.fancybox.update();
            break;

        case 'confirmar':
            guardarCobro();
            break;

        case 'confirmar_imp':
            guardarImputaciones();
            break;
    }
    return false;
});

$('.fancybox-wrap').on('click', 'span[name="calcularTotal"]', function() {
    calTotal();
    calcularRestaImputar();
    return false;
});

$('.fancybox-wrap').on('click', 'i[name="refresh_resto"]', function() {
    calcularRestaImputar();
    return false;
});

$('.fancybox-wrap').on('keyup', 'input[name="total_cobrar"]', function() {
    ingresoDeUsuario = 1;
    validarEntrada(this);
});

$('#cobrar').on('change', 'select[name="pos_tarjeta"]', function() {
    var tarjetas = getTarjetas();
    $('select[name="tarjetas"]').empty();
    var select = $('select[name="tarjetas"]');
    cargarSelect(select, tarjetas);
    $('select[name="tarjetas"]').trigger('chosen:updated');
});

$('#tablaImputaciones').on('click', '.eliminaImputacion', function() {
    eliminarImputacion(this);
    return false;
});

var orden_actual = 0;
$('.fancybox-wrap').on('change', '.checkselect', function() {
    if ($("input[name='codigo']").val() == '') {
        $("input[name='total_cobrar']").val(0);
    }
    if (this.checked) {
            orden_actual++;
            $(this).attr('data-orden-cobro', orden_actual);
    } else {
        $(this).attr('data-orden-cobro', 0);
    }
});

function get_medio_pago_cuotas_alumno(){
    var cod_alumno = $("[name=alumnos]").val();
    if (cod_alumno != '' && cod_alumno != null && cod_alumno[0]){
        cod_alumno = cod_alumno[0];
        $.ajax({
            url: BASE_URL + 'cobros/get_medio_pago_cuotas',
            type: 'POST',
            dataType: 'json',
            data: {
                cod_alumno: cod_alumno
            },
            success: function(_json){
                var cod_medio = "";
                if (_json.medios_pago_cuotas && _json.medios_pago_cuotas.length > 0){
                    cod_medio = _json.medios_pago_cuotas[0].cod_medio;
                }
                $("[name=medio_cobro]").find("option").prop("selected", false);
                $("[name=medio_cobro] option[value='" + cod_medio + "']").prop("selected", true);
                $("[name=medio_cobro]").trigger("chosen:updated");
                if (cod_medio != ''){
                    $("[name=medio_cobro]").change();
                    $(".row.pago").hide();
                } else {
                    $('.dinamico').remove();
                    $('select[name="caja"]').empty();
                    $('select[name="caja"]').trigger("chosen:updated");
                }
            }
        });
    }
}
