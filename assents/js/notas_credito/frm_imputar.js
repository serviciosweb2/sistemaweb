var clavesFRM = Array("descripcion", "fecha_vencimiento", "importe", "deuda_pasiva", "no_tiene_imputaciones", "BIEN", "deuda_pasiva", "eliminacion_ok", "ERROR");

var langFRM = '';
var imputado = '';
var oTableCtacte;
var oTableImputaciones;
var validacionCheckboxCtaCte = 1;// ESTE PARAMETRO TIENE QUE VENIR DE CONFIGURACION.TERMINAR.
var arrCtacte = new Array();

Array.prototype.contains = function(element) {
        for (var i = 0; i < this.length; i++) {
                if (this[i] == element) {
                        return true;
                }
        }
        return false;
}

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
    getTableCtacte();

    oTableImputaciones = $('#tablaImputaciones').dataTable({
        "bFilter": false,
        "bLengthChange": false,
        "bPaginate": false,
    });

    if ($("input[name='codigo']").val() != '') {
        dibujarTabla(CTACTE_IMPUTAR);
        dibujarTablaImputaciones();
    }

    var day = new Date();

    $('.fancybox-wrap input[name="fecha_nc"]').datepicker({
        changeMonth: false,
        changeYear: false,
        maxDate: "+" + (30 - day.getDate()) + "D"
    });

    moneda = $('input[name="moneda"]').attr('data-simbolo');

//    if (COBRO_ESTADO == 'confirmado') {
//        $('#cobrar').find('input:text', 'select').not('#buscador').prop('disabled', true);
//        $('#cobrar').find('select').trigger('chosen:updated');
//    }
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
    oTableCtacte = $('#detalleCtacte').dataTable({
        "aLengthMenu": [[5, 10, 25], [5, 10, 25]],
        "bFilter": true,
        "bLengthChange": false,
        "iDisplayLength": 3,
        "language": {
            "search": ""
        }
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
                //image: $path_assets+'/avatars/avatar1.png',
                sticky: false,
                time: '3000',
                class_name: 'gritter-error'
            });

        }

    }

    return resultado;

}

function dibujarTabla(td) {
    oTableCtacte.fnClearTable();

    var tdARRAY = JSON.parse(td);
    //console.log(tdARRAY);
    //contenido='';

    arrCtacte = new Array();
    var debe = [];
    var num = [];
    $(tdARRAY).each(function(key, ctacte) {
        var k = ctacte.concepto + '-' + ctacte.cod_concepto;
        var disabled = '';
        if (validacionCheckboxCtaCte == 1)
        {// si la validacion esta activada

            if (debe.contains(k))
            {// si existe este concepto y este cod_cencepto en  "debe" va disabled el checkbox
                disabled = "disabled";
            }
            else
            {
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
            "<input type='checkbox' numerador='" + num[k] + "'  data-codigos=" + k + " onclick='checkCtacteChecked(this)' name='checkctacte[]' class='checkselect ace' value='" + this.codigo + "' data-saldo='" + this.importe + "'" + disabled + "/><spam class='lbl'></spam>",
            this.descripcion + ' ' + icono,
            this.fechavenc,
            this.simbolo_moneda + this.saldocobrarformateado]);
        if ((ctacte.saldocobrar - ctacte.pagado) != 0) { // si ese concepto y ese codigo de concepto "deben" , los pongo en el array y en la proxima vuelta si es que aparecen saldran bloquiados
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
        url: BASE_URL + 'notascredito/getImputaciones',
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
//function validarInstancia() {
//
//    var msj = '';
//
//    var seguir = true;
//
//    if (!$('select[name="alumnos"]').val()) {
//        seguir = false;
//        msj += '<p>' + langFRM.alumnos_cobro + '</p>';
//    }
//    if (seguir == false) {
//
//        gritter(msj);
//
//    } else {
//
//
//        $('button[name="cobrar"]').hide();
//
//        $('button[name="volver"]').show();
//
//        $('button[name="confirmar"]').show();
//
//        $('button[name="confirmar_imp"]').show();
//
//        $('.pago').fadeIn();
//
//        $('.datos').hide();
//
//        $('.totales').hide();
//
//    }
//
//
//}


function calcularTotImputado() {

    $.ajax({
        url: BASE_URL + 'notascredito/getTotalImputaciones',
        type: "POST",
        data: 'codigo=' + $("input[name='codigo']").val(),
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
        url: BASE_URL + 'notascredito/getRestaImputar',
        type: "POST",
        data: {codigo: $("input[name='codigo']").val()},
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
        //alert($(value).attr('data-saldo'));
        var x = $(value).attr('data-saldo');
        montoFinal = parseFloat(x) + parseFloat(montoFinal);
    });
    $('input[name="total"]').val(moneda + montoFinal);
    $('input[name="total_cobrar"]').val(montoFinal);
    //alert('monto='+ montoFinal);
};

function eliminarImputacion(dato) {
    var codigo = $(dato).attr('imputacion');
    $.ajax({
        url: BASE_URL + 'notascredito/eliminarImputacion',
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

                $.ajax({
                    url: BASE_URL + 'notascredito/getCtaCteImputar',
                    type: 'POST',
                    data: 'codigo=' + $("input[name='codigo']").val(),
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

function guardarImputaciones() {

    var dataPOST = $('input[type!="checkbox"], select').serialize();

    $.ajax({
        url: BASE_URL + 'notascredito/guardarImputaciones',
        data: dataPOST + '&' + oTableCtacte.$('input').serialize(),
        type: 'POST',
        dataType: "JSON",
        cache: false,
        success: function(respuesta) {
            //console.log(respuesta);
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
//                var cod_recibo = respuesta.custom;
//                var param = new Array();
//                param.push(cod_recibo);
//                printers_jobs(10, param);


            } else {

                //console.log(respuesta);
                $.gritter.add({
                    title: langFRM.ERROR,
                    text: respuesta.msgerror,
                    //image: $path_assets+'/avatars/avatar1.png',
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
    if (validacionCheckboxCtaCte == 1)
    {
        if (element.checked)
        {
            numerador++;
            oTableCtacte.$('input[data-codigos="' + atributo + '"][numerador="' + numerador + '"]').prop('disabled', false);
        }
        else
        {
            var bloquiado = false;
            oTableCtacte.$('input[data-codigos="' + atributo + '"]').not(':disabled').each(function(k, elemento) {
                if ($(elemento).attr('numerador') > numerador)
                {// bloqueo todos los check que sean mayores al check seleccionado
                    // alert($(elemento).attr('numerador')+'-'+numerador);
                    bloquiado = true;
                }
                if ($(elemento).attr('numerador') != 0)
                {// si no se trata del primer concepto que el alumno  esta debiendo bloqueo los checkbox
                    if ($(elemento).is(':checked') && $(elemento).attr('numerador') > numerador)
                    {// Si el checkbox es mayor al que fue clikiado y ademas esta chekiado , remuevo el checked
                        $(elemento).prop('checked', false);
                    }
                    $(elemento).prop('disabled', bloquiado);
                    bloquiado = false;// reseteo la variable para la proxima vuelta
                }
            });
        }
    }
}
//MONITOREO DE EVENTOS


$('.fancybox-wrap').on('click', '.modal-footer button', function() {

    // $('.modal-footer button').show();

    var boton = $(this).attr('name');

    switch (boton) {

        case 'confirmar':
            guardarImputaciones();

            break;

    }

    return false;
});

$('.fancybox-wrap').on('click', 'i[name="refresh_resto"]', function() {

    calcularRestaImputar();

    return false;
});


$('#tablaImputaciones').on('click', '.eliminaImputacion', function() {

    eliminarImputacion(this);

    return false;
});



