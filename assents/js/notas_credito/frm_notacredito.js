var clavesFRM = Array("validacion_ok", "descripcion", "fecha", "importe", "alumnos_cobro", "NOTA_CREDITO", "BIEN", "ERROR");
//var oTableCtacte;
var oTableFacturas;
//var validacionCheckboxCtaCte = 1;// ESTE PARAMETRO TIENE QUE VENIR DE CONFIGURACION.TERMINAR.
var langFRM = '';

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
                    terms[val.codigo] = val.nombreapellido;

                });

                response(terms);
                ingresoDeUsuario = 0;
            }
        });

    });

//    $('.imputaciones').hide();

//    $('button[name="volver"],button[name="confirmar"]').hide();

    Init();
});

function Init() {
    oTableFacturas = $('#facturasTable').dataTable({
        "aLengthMenu": [[5, 10, 25], [5, 10, 25]],
        "bFilter": true,
        "bLengthChange": false,
        "iDisplayLength": 5,
        "language": {
            "search": ""
        }
    });
    $('#facturasTable_filter').closest('.row').remove();

//    oTableCtacte = $('#detalleCtacte').dataTable({
//        "aLengthMenu": [[5, 10, 25], [5, 10, 25]],
//        "bFilter": true,
//        "bLengthChange": false,
//        "iDisplayLength": 5,
//        "language": {
//            "search": ""
//        }
//    });
//    $('#detalleCtacte_filter').closest('.row').remove();

    $('.fancybox-wrap').on('change', 'select[name="alumnos"]', function() {

        $("#facturasTable").dataTable().fnClearTable();
        cod_alumno = $(this).val();

        $.ajax({
            url: BASE_URL + 'notascredito/getFacturas',
            type: 'POST',
            data: 'cod_alumno=' + cod_alumno,
            dataType: "JSON",
            cache: false,
            success: function(respuesta) {
                cargarTablaFacturas(respuesta);
            }
        });

//        $("#detalleCtacte").dataTable().fnClearTable();
        $('input[name="total_nota"]').val('');

//        $.ajax({
//            url: BASE_URL + 'cobros/getCtaCteImputar',
//            type: 'POST',
//            data: 'cod_alumno=' + cod_alumno,
//            cache: false,
//            success: function(respuestacta) {
//                cargarTablaCtaCte(respuestacta);
//
//            }
//        });
    });

    $('.fancybox-wrap').on('click', '.modal-footer button', function() {


        var boton = $(this).attr('name');

        switch (boton) {

//            case 'imputar':
//
//
//                var valorTotal = $('.fancybox-wrap').find('input[name="total_nota"]').val();
//
//                if (ingresoDeUsuario = 1 && valorTotal == '' || valorTotal == 0) {
//
//                    calTotalFactura();
//                }
//
//                if (ingresoDeUsuario = 0) {
//                    calTotalFactura();
//                }
//
//
//                var msj = '';
//
//                var seguir = true;
//
//                if (!$('select[name="alumnos"]').val()) {
//                    seguir = false;
//                    msj += '<p>' + langFRM.alumnos_cobro + '</p>';
//                }
//
//                if (seguir == false) {
//
//                    gritter(msj);
//
//                } else {
//
//
//                    $('button[name="imputar"]').hide();
//
//                    $('button[name="volver"]').show();
//
//                    $('button[name="confirmar"]').show();
//
//                    $('.imputaciones').fadeIn();
//
//                    $('.facturas').hide();
//
//                    //$('.totales').hide();
//
//                }
//
//
//                $.fancybox.update();
//
//
//                break;
//
//
//            case 'volver':
//
//                $('.principal').show();
//
//                $('.totales').show();
//
//                $('button[name="imputar"]').show();
//
//                $('button[name="confirmar"],button[name="volver"]').hide();
//
//                $('select[name="alumnos"] option').not(':selected').prop('disabled', false);
//
//                $('select[name="alumnos"]').trigger('chosen:updated');
//
//                $('.imputaciones').hide();
//
//                $('.facturas').fadeIn();
//
//                $.fancybox.update();
//
//
//                break;

            case 'confirmar':
                guardarNotaCredito();
//                $('#cobrar').submit();
//                $('button[name="cobrar"]').hide();

                break;

        }

        return false;
    });
}

function cargarTablaFacturas(respuesta) {
    $(respuesta).each(function(key, valor) {
        icono = '';
        if (valor.imputado != 0) {
            icono = "<i class='icon-ok icon-info-sign' title='" + langFRM.NOTA_CREDITO + ' ' + valor.imputado_format + "'></i>";
        }
        $('#facturasTable').dataTable().fnAddData(
                [
                    '<div class=""><label><input name="facturas[' + key + ']" class="checkselect ace facturas" onclick="checkFacturaChecked(this)" value="' + valor.codigo + '" type="checkbox"><span class="lbl"></span></label></div>',
                    valor.tipo + ' ' + valor.numero,
                    valor.fecha,
                    valor.total + ' ' + icono,
                    $('input[name="moneda"]').val()+ ' &nbsp<input id="valor_nc" name="importe[' + key + ']" value="' + valor.importe_nc + '" ></input>'
                ]
                );

    });

    $.fancybox.update();
}

//function mySearch(element) {
//    var text = $(element).val();
//    $('#detalleCtacte').dataTable().fnFilter(text);
//}

function mySearchFactura(element) {
    var text = $(element).val();
    $('#facturasTable').dataTable().fnFilter(text);
}

//function cargarTablaCtaCte(td) {
//    oTableCtacte.fnClearTable();
//
//    var tdARRAY = JSON.parse(td);
//
//    arrCtacte = new Array();
//    var debe = [];
//    var num = [];
//    $(tdARRAY).each(function(key, ctacte) {
//
//        var k = ctacte.concepto + '-' + ctacte.cod_concepto;
//        var disabled = '';
//        if (validacionCheckboxCtaCte == 1)
//        {// si la validacion esta activada
//
//            if (debe.contains(k))
//            {// si existe este concepto y este cod_cencepto en  "debe" va disabled el checkbox
//                disabled = "disabled";
//            }
//            else
//            {
//                num[k] = 0;
//            }
//        }
//        if (this.habilitado === '2') {
//            icono = "<i class='icon-ok icon-info-sign' title='" + langFRM.deuda_pasiva + "'></i>";
//        } else {
//            icono = "";
//        }
//        arrCtacte[ctacte.codigo] = {'order': key, 'checked': 0};
//
//        $('#detalleCtacte').dataTable().fnAddData([
//            "<input type='checkbox' numerador='" + num[k] + "'  data-codigos=" + k + " onclick='checkCtacteChecked(this)' name='checkctacte[]' class='checkselect ace' value='" + this.codigo + "' data-saldo='" + this.importe + "'" + disabled + "/><spam class='lbl'></spam>",
//            this.descripcion + ' ' + icono,
//            this.fechavenc,
//            this.simbolo_moneda + this.saldocobrarformateado]);
//        if ((ctacte.saldocobrar - ctacte.pagado) != 0) { // si ese concepto y ese codigo de concepto "deben" , los pongo en el array y en la proxima vuelta si es que aparecen saldran bloquiados
//            debe.push(k);
//        }
//        num[k]++;
//    });
//
//    $.fancybox.update();
//    //$('.imputaciones').fadeIn();
//    //calcularRestaImputar();
//
//}
//
//function checkCtacteChecked(element) {
//    console.log('entra');
//    arrCtacte[element.value]['checked'] = element.checked ? 1 : 0;
//    var atributo = $(element).attr('data-codigos');
//    var numerador = $(element).attr('numerador');
//    if (validacionCheckboxCtaCte == 1)
//    {
//        if (element.checked)
//        {
//            numerador++;
//            oTableCtacte.$('input[data-codigos="' + atributo + '"][numerador="' + numerador + '"]').prop('disabled', false);
//        }
//        else
//        {
//            var bloquiado = false;
//            oTableCtacte.$('input[data-codigos="' + atributo + '"]').not(':disabled').each(function(k, elemento) {
//                if ($(elemento).attr('numerador') > numerador)
//                {// bloqueo todos los check que sean mayores al check seleccionado
//                    // alert($(elemento).attr('numerador')+'-'+numerador);
//                    bloquiado = true;
//                }
//                if ($(elemento).attr('numerador') != 0)
//                {// si no se trata del primer concepto que el alumno  esta debiendo bloqueo los checkbox
//                    if ($(elemento).is(':checked') && $(elemento).attr('numerador') > numerador)
//                    {// Si el checkbox es mayor al que fue clikiado y ademas esta chekiado , remuevo el checked
//                        $(elemento).prop('checked', false);
//                    }
//                    $(elemento).prop('disabled', bloquiado);
//                    bloquiado = false;// reseteo la variable para la proxima vuelta
//                }
//            });
//        }
//    }
//}

function guardarNotaCredito() {

    var dataPOST = $('input[type!="checkbox"][name!="importe"], select').serialize();

    //console.log('TABLA',oTableCtacte.$('input').serialize());

    $.ajax({
        url: BASE_URL + 'notascredito/guardar',
        data: dataPOST + '&' + oTableFacturas.$('input').serialize(),
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
                //var cod_recibo = respuesta.custom;
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

$('.fancybox-wrap').on('click', 'span[name="calcularTotal"]', function() {

    calTotalFactura();

    return false;
});

$('.fancybox-wrap').on('change', '#valor_nc', function() {

    calTotalFactura();

    return false;

});

function checkFacturaChecked() {
    calTotalFactura();

    return false;
}

function calTotalFactura() {

    var importes = [];
    //CODIGOS
    var dataPOST = oTableFacturas.$('input[type="checkbox"]:checked').serialize();
    //IMPORTES

    $(oTableFacturas.$('input[type="checkbox"]:checked')).each(function(k, elemento) {
        //$(elemento).closest('tr').find('td:last').text();
        importes[k] = $(elemento).closest('tr').find('input[type!="checkbox"]').val();

    });


    if (dataPOST == '') {

        dataPOST = 'checkfacturas%5B%5D=&importes%5B%5D=';

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
                $('.fancybox-wrap').find('input[name="total_nota"]').val(respuesta.total);
            } else {
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


}