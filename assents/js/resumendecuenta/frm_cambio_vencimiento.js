var detalle;
$('.fancybox-wrap').ready(function(){     
     detalle = $('#detalleCTACTE').DataTable({
        iDisplayLength: 3,
        "aaSorting": [],
        "bLengthChange": false,
        fnDrawCallback: function(oSettings) {
            $(".date-picker").datepicker({
                dateFormat: 'dd/mm/yy'
            });
        }
    });
});

var estadosActuales = new Array();

function cambioConceptos() {
    $("#btn_guardar_fechas").attr("disabled", true);
    var value = $("#conceptos").val().split("|");
    var codigo_concepto = value[0];
    var concepto = value[1];
    var codigo_alumno = $("#codigo_alumno").val();
    $.ajax({
        url: BASE_URL + 'ctacte/getCtaCteCambioVencimiento',
        type: 'POST',
        dataType: "json",
        data: {
            codigo_concepto: codigo_concepto,
            codigo_alumno: codigo_alumno,
            concepto: concepto
        },
        success: function(_json) {
            graphicDataTable(_json);
            $.fancybox.update();
            if (_json.length > 0) {
                $("#periodicidad").trigger("chosen:updated");
            }
            $("#btn_guardar_fechas").attr("disabled", false);
        }
    });
}

function graphicDataTable(_json) {
    detalle.clear();
    estadosActuales = new Array();
    $(_json).each(function() {
        var codigo_ctacte = this.codigo;
        var fechavenc = this.fechavenc;
        var descripcion = this.descripcion;
        var saldo = this.saldoformateado;
        estadosActuales[codigo_ctacte] = fechavenc;
        var inputpicker = getGraphicNodePicker(codigo_ctacte, fechavenc);
        detalle.row.add([
            descripcion,
            saldo,
            inputpicker
        ]).draw();        
    });   

   $(".date-picker").datepicker({
     dateFormat: 'dd/mm/yy'
     });
}

function getGraphicNodePicker(codigo_ctacte, fechavenc) {
    var inputpicker = '<div class="input-group">';
    inputpicker += '<input id="id-date-picker-' + codigo_ctacte + '" name="date_picker_ctacte" value="' + fechavenc + '" class="form-control date-picker" type="text" data-date-format="dd/mm/yyyy" style="width: 88px;" onchange="fechaChange(' + codigo_ctacte + ');">';
    inputpicker += '<span class="input-group-addon">';
    inputpicker += '<i class="icon-calendar bigger-110"></i>';
    inputpicker += '</span><span class="icon-bolt" style="margin-left: 14px; cursor: pointer" onclick="changeFechasVencimientos(' + codigo_ctacte + ')"></span>';
    inputpicker += '</div>';
    inputpicker += '<input type="hidden" value="' + codigo_ctacte + '" name="picker_ctacte">';
    return inputpicker;
}

function changeFechasVencimientos(codigo_ctacte) {
    $("#btn_guardar_fechas").attr("disabled", true);
    var fecha = $("#id-date-picker-" + codigo_ctacte).val();
    var value = $("#conceptos").val().split("|");
    var codigo_concepto = value[0];
    var concepto = value[1];
    var codigo_alumno = $("#codigo_alumno").val();
    var periocidad = $("#periodicidad").val();
    $.ajax({
        url: BASE_URL + 'ctacte/getCtaCteCambioVencimiento',
        type: 'POST',
        dataType: "json",
        data: {
            codigo_concepto: codigo_concepto,
            codigo_alumno: codigo_alumno,
            concepto: concepto,
            codigo_ctacte: codigo_ctacte,
            fecha: fecha,
            periocidad: periocidad,
            cambiar_fechas_vencimiento: 1
        },
        success: function(_json) {
            
            graphicDataTable(_json);
            $.fancybox.update();
            if (_json.length > 0) {
                $("#periodicidad").attr("disabled", false);
                $("#periodicidad").trigger("chosen:updated");
            }
            $("#btn_guardar_fechas").attr("disabled", false);
        }
    });
}

function fechaChange(cod_ctacte) {
    var value = $("#id-date-picker-" + cod_ctacte).val();
    estadosActuales[cod_ctacte] = value;
}

function guardarCambioFecha() {
    $("#btn_guardar_fechas").attr("disabled", true);
    var codigo_ctacte = new Array();
    var fechas = new Array();
    var mensajeError = false;
    $.each(estadosActuales, function(key, value) {
        if (value != undefined) {
            codigo_ctacte.push(key);
            fechas.push(value);
            if (value == '')
                mensajeError = true;
        }
    });
    if (mensajeError) {
        alert("Todas las fechas deben completarse");
        $("#btn_guardar_fechas").attr("disabled", false);
    } else {
        $.ajax({
            url: BASE_URL + 'ctacte/guardarCambioVencimiento',
            type: 'POST',
            dataType: 'json',
            data: {
                fechas: fechas,
                codigo_ctacte: codigo_ctacte
            },
            success: function(_json) {
                if (_json.codigo == 1) {
                    $.gritter.add({
                        title: 'OK!',
                        text: 'Enviado Correctamente',
                        sticky: false,
                        time: '3000',
                        class_name: 'gritter-success'
                    });
                    $.fancybox.close(true);
                } else {
                    $.gritter.add({
                        title: 'Uppss!',
                        text: _json.msgerror,
                        sticky: false,
                        time: '3000',
                        class_name: 'gritter-error'
                    });
                }
                $("#btn_guardar_fechas").attr("disabled", false);
            }
        });
    }
}