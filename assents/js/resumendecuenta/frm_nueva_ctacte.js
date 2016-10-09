
var tabla_nueva_financiacion;
$('.fancybox-wrap').ready(function() {
    init();
});

function init() {
    $("[name=fecha_primer_pago_concepto]").datepicker({
    });
    tabla_nueva_financiacion = $("#tabla_nueva_financiacion").dataTable({
        iDisplayLength: 15,
        bLengthChange: false,
        searching: false,
        bInfo: false
    });

    if($('#matriculas > option').length === 0){
       $("#plan-de-pagos-radio").addClass("hide");
    }else{
        $("#plan-de-pagos-radio").removeClass("hide");
    }

    $("[name=accion]").on("change", function() {
        switch ($(this).val()) {
            case "linea" :
                $("#vista_1").removeClass("hide");
                $("#vista_2").addClass("hide");
                break;

            case "plan":
                $("#vista_2").removeClass("hide");
                $("#vista_1").addClass("hide");
                break;
        }
        $('.chosen-select').chosen({
            width: "100%"
        });
        $.fancybox.update();
    });

    $('select[name="planes"]').on('change', function() {
        var idPlan_POST = $(this).val();
        $.ajax({
            url: BASE_URL + 'matriculas/getCuotasPlan',
            type: 'POST',
            data: 'codigo=' + idPlan_POST,
            dataType: 'json',
            cache: false,
            success: function(respuesta) {
                var obj = respuesta;
                if (obj.length > 0) {
                    $("#esquema").removeClass("hide");
                } else {
                    ("#esquema").addClass("hide");
                }
                $("#esquema").empty();
                $("#esquema").append("<tr><th style='width: 194px;'>" + lang.concepto + "</th><th>" + lang.financiacion + "</th><th>" + lang.fecha_primer_pago + "</th></tr>");
                var hoy = new Date();
                var dd = hoy.getDate();
                var mm = hoy.getMonth() + 1;
                var yyyy = hoy.getFullYear();
                if(dd < 10) {
                    dd = '0' + dd;
                }
                if(mm < 10) {
                    mm = '0' + mm;
                }
                hoy = dd + '/' + mm + '/' + yyyy;
                $.each(obj, function(key, value) {
                    $.each(value, function(key, value) {
                        var option = "";
                        option += "<option></option>";
                        $.each(value.financiaciones, function(key2, row) {
                            option += "<option value='" + key2 + "' calendario='" + row.limite_primer_cuota + "' fecha_limite='" + row.fecha_limite + "' fecha_hoy='" + row.fecha_hoy + "'>" + row.nombre + "</option>";
                        });
                        var tr = "<tr>";
                        tr += "<input type='hidden' name='plan-concepto[]' value='" + key + "'>";
                        tr += "<td>" + value.concepto + "</td>";
                        tr += "<td>";
                        tr += "<select id='codigo-financiacion-" + key + "' posicion='" + key + "' name='codigo-financiacion[]'  class='select-financiaciones chosen-select'  data-placeholder='" + lang.seleccione_financiacion + "'  style='width: 250px;'>" + option + "</select>";
                        tr += "</td>";
                        tr += '<td>';
                        tr += '<div class="input-group "> ';
                        tr += '<span class=" hide fecha-hoy-financiacion-' + key + '">' + lang.paga_al_momento + '</span>';
                        tr += '<input class="form-control hide date-picker calendario-financiacion-' + key + '  input-mask-date" id="id-date-picker-' + key + '" value="' + hoy + '" type="text" data-date-format="dd-mm-yyyy" name="fechaPrimerPago[]">';
                        tr += '<span class="input-group-addon hide calendario-financiacion-' + key + '">';
                        tr += '<i class="icon-calendar bigger-110"></i>';
                        tr += '</span>';
                        tr += '</div>';
                        tr += '</td>';
                        tr += "</tr>";
                        $("#esquema").append(tr);
                        $(".chosen-select").chosen();
                    });
                });

                $('.ver-planes').click(function() {
                    $("#errores").hide();
                    $.ajax({
                        url: BASE_URL + 'matriculas/getDetallePlan',
                        data: $("#nuevaMatricula").serialize(),
                        dataType: 'JSON',
                        type: 'POST',
                        cache: false,
                        async: false,
                        success: function(respuesta) {
                            var retorno = generarTablaFinanciacion(respuesta);
                            if (retorno !== false) {
                                $("#detalle-plan").html(retorno);
                                $('#stack1').modal('show');
                            }
                        }
                    });
                });
                $.fancybox.update();
                $.fancybox.reposition();
            }
        });
    });
}
$(".fancybox-wrap").on('change', '.select-financiaciones', function() {
    disabledInputs(false);
    var optionseleccionado = $("#" + $(this).attr("id") + " option:selected");
    switch (optionseleccionado.attr("calendario")) {

        case "al_momento":
            $(".calendario-financiacion-" + $(this).attr("posicion")).addClass("hide");
            $(".fecha-hoy-financiacion-" + $(this).attr("posicion")).removeClass("hide");
            break;

        case "con_fecha_limite":
            $("#id-date-picker-" + $(this).attr("posicion")).datepicker({
                changeMonth: false,
                changeYear: false,
                dateFormat: 'dd/mm/yy',
                minDate: optionseleccionado.attr("fecha_hoy"),
                maxDate: optionseleccionado.attr("fecha_limite")
            });
            $(".fecha-hoy-financiacion-" + $(this).attr("posicion")).addClass("hide");
            $(".calendario-financiacion-" + $(this).attr("posicion")).removeClass("hide");
            $("#id-date-picker-" + $(this).attr("posicion")).val(optionseleccionado.attr("fecha_limite"));
            break;

        case "sin_fecha_limite":
            $(".fecha-hoy-financiacion-" + $(this).attr("posicion")).addClass("hide");
            $(".calendario-financiacion-" + $(this).attr("posicion")).removeClass("hide");
            $("#id-date-picker-" + $(this).attr("posicion")).val();
            $("#id-date-picker-" + $(this).attr("posicion")).datepicker({
                changeMonth: false,
                changeYear: false,
                dateFormat: 'dd/mm/yy'
            });
            break;
    }
    $.fancybox.update();
    $.fancybox.reposition();
});
$('.fancybox-wrap').on('change', '#matriculas', function() {
    getPlanesPago();
});

function guardarFinanciacion() {
    disabledInputs(true);
    $.ajax({
        url: BASE_URL + "ctacte/guardarNuevaCtaCte",
        type: 'POST',
        dataType: 'json',
        data: $("#frm-agregar-conceptos").serialize(),
        success: function(_json) {
            if (_json.codigo == 1) {
                gritter(lang.validacion_ok, true);
                getCtaCte();
                $.fancybox.close();
            } else {
                gritter(_json.msgerror, false);
            }
        }
    });
}

function graphicDetalleFinanciacion(_json) {
    tabla_nueva_financiacion.fnClearTable();
    var descripcion = $("#conceptos option:selected").text();
    $(_json).each(function() {
        tabla_nueva_financiacion.fnAddData([
            this.nrocuota,
            this.concepto,
            this.valor,
            this.fecha
        ]);
    });
}

function disabledInputs(disabled) {
    $("[name=cantidad_cuotas]").attr("disabled", disabled);
    $("[name=enviarForm]").attr("disabled", disabled);
    $("[name=fecha_primer_pago]").attr("disabled", disabled);
    $("#periodicidad").attr("disabled", disabled);
    $("#btn_volver").attr("disabled", disabled);
    $("#btn_guardar").attr("disabled", disabled);
}

function previsualizarFinanciacion() {
    disabledInputs(true);
    var cantidad_cuotas = $("[name=cantidad_cuotas]").val();
    var fecha_primer_pago = $(".fancybox-wrap input[name=fecha_primer_pago_concepto]").val();
    var valor = $(".fancybox-wrap input[name=importe_seleccionado]").val();
    var periodicidad = $(".fancybox-wrap #periodicidad").val();
    var mensaje = '';
    $.ajax({
        url: BASE_URL + 'ctacte/getDetalleFinanciacionNuevo',
        type: 'POST',
        dataType: 'json',
        data: $("#frm-agregar-conceptos").serialize(),
        success: function(_json) {
            if (_json.codigo && _json.codigo == 0) {
                gritter(_json.msgerror, false);
            } else {
                graphicDetalleFinanciacion(_json);
                $(".fancybox-wrap [name=enviarForm]").hide();
                $(".fancybox-wrap #paso_1").addClass("hide");
                $(".fancybox-wrap [name=btn_guardar]").show();
                $(".fancybox-wrap [name=btn_volver]").show();
                $(".fancybox-wrap [name=enviarForm]").hide();
                $(".fancybox-wrap #paso_2").removeClass("hide");
                $.fancybox.update();
            }
        }
    });
    disabledInputs(false);
}

function getPlanesPago() {
    $("[name=planes]").empty();
    $.ajax({
        url: BASE_URL + 'ctacte/getPlanesPago',
        type: 'POST',
        data: {cod_matricula: $("#matriculas").val()},
        dataType: 'json',
        cache: false,
        success: function(respuesta) {
            if (respuesta.length > 0) {
                if (respuesta.length > 1) {
                    $('select[name="planes"]').append('<option value=""></option>');
                }
                if (respuesta.length !== 0) {
                    $(respuesta).each(function() {
                        $('select[name="planes"]').append('<option value="' + this.codigo + '">' + this.nombre + '</option>');
                    });
                    $('select[name="planes"]').attr('disabled', false);
                    $('select[name="planes"]').attr("data-placeholder", lang.SELECCIONE_UNA_OPCION);
                    $("[name=planes]").trigger("chosen:updated");
                    if (respuesta.length < 2) {
                        $('select[name="planes"]').change();
                    }
                } else {
                    $('select[name="planes"]').attr('disabled', true);
                    $('select[name="planes"]').attr("data-placeholder", lang.NO_HAY_PLANES);
                }
                $("#esquema").html("");
                $("#ver-plan").addClass("hide");
            } else {
                gritter(lang.no_hay_planes_de_pago_definidos_para_esta_seleccion, false, '');
                $("#esquema").empty();
                $("[name=planes]").empty();
                $("[name=planes]").trigger("chosen:updated");
            }
        }
    });
}
