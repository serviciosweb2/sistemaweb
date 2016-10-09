
jQuery.fn.dataTableExt.oSort['uk_date-asc'] = function(a, b) {
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
    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
};

jQuery.fn.dataTableExt.oSort['uk_date-desc'] = function(a, b) {
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
    return ((x < y) ? 1 : ((x > y) ? -1 : 0));
};

var oTableInputaciones, oTableFacturas;

var tabla_nueva_financiacion = $("#tabla_nueva_financiacion").dataTable({
    iDisplayLength: 3,
    bLengthChange: false,
     searching:false,
     bInfo: false
});

function imprimirDetalleCtacte() {
    var cod_alumno = $("[name=cod_alumno]").val();
    var param = new Array();
    param.push(cod_alumno);
    printers_jobs(3, param);
    cerrarVentana();
}

function cerrarVentana() {
    $.fancybox.close(true);
}

function getDetalle(tipo, respuesta) {

    var nomPeticion = tipo == 'getFacturas' ? 'faturas' : 'inputaciones';

    var vistaTabla = '<table id="dCtacte" class="table table-striped table-bordered dataTable" cellspacing="0" cellpadding="0" border="0">';

    if (tipo == 'getFacturas') {

        $('#modalDtalle h4').html('Facturas');
        vistaTabla += '<thead><th>numero</th><th>tipo</th><th>importe</th></thead>';
        vistaTabla += '<tbody>';

        $(respuesta).each(function(k, valores) {

            vistaTabla += '<tr><td>' + valores.nrofact + '</td><td>' + valores.factura + '</td><td>' + valores.importeformateado + '</td></tr>';

        });


    } else {
        $('#modalDtalle h4').html('Inputaciones');
        vistaTabla += '<thead><th>fecha</th><th>medio pago</th><th>importe</th></thead>';
        vistaTabla += '<tbody>';

        $(respuesta).each(function(k, valores) {

            vistaTabla += '<tr><td>' + valores.fecha + '</td><td>' + valores.medio + '</td><td>' + valores.importeformateado + '</td></tr>';

        });
    }

    vistaTabla += '</tbody>';
    vistaTabla += '</table>';

    $('.contenedorTabla').empty().html(vistaTabla);

    $('#dCtacte').dataTable({
        "iDisplayLength": 4,
        "aaSorting": [],
    });


}

function addInputaciones(inputaciones) {

    oTableInputaciones.clear().draw();

    $(inputaciones).each(function(k, I) {

        oTableInputaciones.row.add([I.fechareal, I.medio, I.importeformateado]).draw();

    });



}

function addFacturas(rows) {
    oTableFacturas.clear().draw();
    $(rows).each(function(k, F) {

        oTableFacturas.row.add([F.fecha, F.tipo_numero, F.importeformateado]).draw();

    });



}

function getInputacionesFacturas(codigo) {

    $.ajax({
        url: BASE_URL + "ctacte/getImputaciones_facturas",
        type: "POST",
        data: {'cod_ctacte': codigo},
        dataType: "JSON",
        cache: false,
        success: function(respuesta) {

            addInputaciones(respuesta.imputaciones);

            addFacturas(respuesta.facturas);

            //var ver=oTableInputaciones.row(0).data();
        }
    });

    $('#modalDtalle').modal();

    return false;
}

function filtrar(element) {


    oTableDetalle.search($(element).val()).draw();


}

function frm_nuevactacte() {

    $('#modelNuevacc').modal();

}
function cambioConceptos() {
    disabledInputs(false);
}
function disabledInputs(disabled){
    $("[name=cantidad_cuotas]").attr("disabled", disabled);
    $("[name=enviarForm]").attr("disabled", disabled);
    $("[name=fecha_primer_pago]").attr("disabled", disabled);
    $("#periodicidad").attr("disabled", disabled);
    $("#btn_volver").attr("disabled", disabled);
    $("#btn_guardar").attr("disabled", disabled);
}




function volverAFinanciacion(){
    $("#vista_2").hide();
    $("[name=btn_guardar]").hide();
    $("[name=btn_volver]").hide();
    $("[name=enviarForm]").show();
    $("#vista_1").show();
    $.fancybox.update();
}

function guardarFinanciacion(){
    disabledInputs(true);
    var cantidad_cuotas = $("[name=cantidad_cuotas]").val();
    var fecha_primer_pago = $("[name=fecha_primer_pago]").val();
    var periodicidad = $("#periodicidad").val();
    var alumno = $("[name=cod_alumno]").val();
    var valor = $("[name=importe_seleccionado]").val();
    var codconcepto = $("#conceptos").val();   
    $.ajax({
        url: BASE_URL + "ctacte/guardarNuevaCtaCte",
        type: 'POST',
        dataType: 'json',
        data: {
                cuotas: cantidad_cuotas,
                fechaPrimerPago: fecha_primer_pago,
                valor:valor,
                periodicidad: periodicidad,
                alumno: alumno,
                cod_concepto: codconcepto
            },
        success: function(_json){
            if (_json.codigo == 1){
                gritter(lang.registros_guardados_correctamente, true);
                $.fancybox.close();
            } else {
                gritter(_json.msgerror, false);
            }
        }
    });
    
}
//////////////

$('.fancybox-wrap').ready(function() {

    var clavesFRM = Array("validacion_ok", "consaldo", "todas", "sinsaldo", "filtrar", "fecha", "tipo", "importe", "numero", "medio_de_pago");

    var langFRM = '';

    $.ajax({
        url: BASE_URL + 'entorno/getLang',
        data: "claves=" + JSON.stringify(clavesFRM),
        type: "POST",
        dataType: "JSON",
        async: false,
        cache: false,
        success: function(respuesta) {
            langFRM = respuesta;
            initFRM();
        }
    });

    function initFRM() {

        oTableInputaciones = $('#inputaciones').DataTable({
            "lengthChange": false,
            "searching": false,
            "paging": false,
            "info": false,
            "aoColumns": [
                {"sTitle": langFRM.fecha, "sClass": "center", "bVisible": true, "sType": "uk_date"},
                {"sTitle": langFRM.medio_de_pago, "sClass": "center", "bVisible": true},
                {"sTitle": langFRM.importe, "sClass": "center", "bVisible": true}

            ]
        });

        oTableFacturas = $('#facturas').DataTable({
            "lengthChange": false,
            "searching": false,
            "paging": false,
            "info": false,
            "aoColumns": [
                {"sTitle": langFRM.fecha, "sClass": "center", "bVisible": true},
                {"sTitle": langFRM.tipo, "sClass": "center", "bVisible": true},
                {"sTitle": langFRM.importe, "sClass": "center", "bVisible": true}

            ]
        });

        oTableDetalle = $('#detalleResumen').DataTable({
            "sServerMethod": "POST",
            "iDisplayLength": 4,
            "aaSorting": [],
            "aoColumnDefs": [
                {"sType": "uk_date", "aTargets": [3]}
            ]


        });

        oTableDetalle.column(5).visible(false);

        var selector = $('#detalleResumen_wrapper').find('.row').eq(0);

        selector.find('.col-sm-6').removeClass('col-sm-6').addClass('col-md-4');

        var select = '<label style="text-align: right;">' + langFRM.filtrar + ': <select name="filtro" onchange="filtrar(this)" class="form-control"><option value="">' + langFRM.todas + '</option><option value="sinsaldo">' + langFRM.sinsaldo + '</option><option value="consaldo">' + langFRM.consaldo + '</option></select></label>';

        selector.find('.col-md-4').eq(0).after('<div class="col-md-4">' + select + '</div>');





        $('#detalleResumen').on('draw.dt', function() {

            $.fancybox.update();

        });

        $('#facturas_wrapper').find('.row').eq(0).hide();

        $('#inputaciones_wrapper').find('.row').eq(0).hide();

        $('.btn').each(function() {
            var cod_ctacte = $(this).attr('href');
            $(this).popover({
                trigger: 'manual',
                html: true,
                placement: 'left',
                title: 'Descripcion'

            });
        });



    }




});





























//READY!





