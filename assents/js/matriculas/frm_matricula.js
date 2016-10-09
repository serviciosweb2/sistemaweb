var idioma = [];
var nombreSelects = [];
var langFrmMatricula;
langFrmMatricula = frmLang;
var estadosActualesVencimientos = new Array();

$(".fancybox-wrap").ready(function() {
    init();
});

// U238
function setCobroMatricula() {
    var attribute = $("#checkboxCobroMatricula").prop("checked");
    $("#checkboxCobroMatricula").prop("value", attribute);
}

function changeFechasVencimientos(fila_num, periodo_tiempo)
{
    //debugger;
    var fecha_calculada = undefined;
    var fecha_input = $("#id-date-picker-ver-" + fila_num).val();
    var anio  = fecha_input.split('/')[2];
    var mes   = fecha_input.split('/')[1];
    var dia   = fecha_input.split('/')[0];
    var periodo = periodo_tiempo.split(' ')[0];
    var periodo_tipo = periodo_tiempo.split(' ')[1];
    var filas = $('.date-picker-ver');
    var total_filas = filas.length;
    var fecha_calcular = new Date(anio,mes-1, dia);
    var i = 0;

    filas.each(function(index, value)
    {
        if(i > fila_num)
        {
            if(periodo_tipo == 'day')
            {
                //debugger;
                fecha_calcular = new Date(fecha_calcular.setDate(parseInt(("0" + (fecha_calcular.getDate())).slice(-2))+parseInt(periodo)));
                fecha_calculada = ("0" + (fecha_calcular.getDate())).slice(-2)+'/'+("0" + (fecha_calcular.getMonth() + 1)).slice(-2)+'/'+fecha_calcular.getFullYear();
                //console.log(fecha_calculada);
            }
            else if(periodo_tipo == 'month')
            {
                //debugger;
                fecha_calcular = new Date(fecha_calcular.setMonth(parseInt(("0" + (fecha_calcular.getMonth())).slice(-2))+parseInt(periodo)));
                fecha_calculada = ("0" + (fecha_calcular.getDate())).slice(-2)+'/'+("0" + (fecha_calcular.getMonth() + 1)).slice(-2)+'/'+fecha_calcular.getFullYear();
                //console.log(fecha_calculada);
            }
            $("#id-date-picker-ver-" + index).val(fecha_calculada);
        }
        i++;
    });
}

function fechaChange() {
    $('.date-picker-ver').each(function(index, obj)
    {
        //actualiza fecha primer pago en el modal anterior
        if(index == 1)
        {
            $("[name='fechaPrimerPago[]']")[1].value = obj.value;
        }
        estadosActualesVencimientos[index] = obj.value;
    });
}

function getGraphicNodePicker(fila_num, fechavenc, periodo_tiempo) {
    var inputpicker = '<div class="input-group">';
    if(fila_num == 0)
    {
        inputpicker += '<input readonly id="id-date-picker-ver-' + fila_num + '" name="date_picker_ctacte" value="' + fechavenc + '" class="form-control date-picker-ver" type="text" data-date-format="dd/mm/yyyy" style="width: 88px;" disabled>';
    }
    else
    {
        inputpicker += '<input readonly id="id-date-picker-ver-' + fila_num + '" name="date_picker_ctacte" value="' + fechavenc + '" class="form-control date-picker-ver" type="text" data-date-format="dd/mm/yyyy" style="width: 88px;" >';
    }
    inputpicker += '<span class="input-group-addon">';
    inputpicker += '<i class="icon-calendar bigger-110"></i>';
    inputpicker += '</span>';
    if(fila_num > 0)
    {
        inputpicker += '<span class="icon-bolt" style="margin-left: 14px; cursor: pointer" onclick="changeFechasVencimientos(\''+fila_num+'\', \''+periodo_tiempo+'\')"></span>';
    }
    inputpicker += '</div>';
    inputpicker += '<input type="hidden" value="' + fila_num + '" name="picker_ctacte">';
    return inputpicker;
}

function generarTablaFinanciacion(ObjCuotas) {
    if (ObjCuotas.codigo !== "0") {
        var table = "<div class='row'>";
        var i = 0;
        $(ObjCuotas).each(function(key, fila) {
            if (i % 10 == 0) {
                table += "<div class='col-md-4'><table class='table table-striped table-bordered table-condensed table-hover'><tr><th>" + langFrmMatricula.concepto + "</th><th>" + langFrmMatricula.cuota + "</th><th>" + langFrmMatricula.vencimiento + "</th><th>" + langFrmMatricula.valor + "</th></tr>";
            }
            table += "<tr><td>" + fila.concepto + "</td><td>" + fila.nrocuota + "</td><td>" + getGraphicNodePicker(i, fila.fecha, fila.periodo_tiempo) + "</td><td>" + fila.valor + "</td></tr>";
            i++;
            if (i % 10 == 0) {
                table += "</table></div>";
            }
        });
        table += "</div>";
    } else {
        $("#errores").html(ObjCuotas.respuesta);
        $("#errores").show("slow");
        return false;
    }

    return table;
}

function refrescarSelects(nombre) {
        if (nombre == 'cod_alumno') {
            $('select[name="cod_plan_academico"]').val('').trigger('chosen:updated');
        }
        $(nombreSelects).each(function(key, nombrePosicion) {
            if (nombre === nombrePosicion) {
                var selectsTotal = nombreSelects.length;
                for (var i = key + 2; i < selectsTotal; i++) {
                    var select = nombreSelects[i];
                    //actualizarSelect(select, '');
                }
            }
        });
    }
    
    function cargarTablaPeriodos(respuesta) {
        $(".periodos").removeClass("hide");
        var datos = '';
        var ocultar = '';
        if (todosPeriodos == '1') {
            ocultar = 'type="hidden"';
        }

        $(respuesta).each(function(key, value) {
            datos += '<tr><td class="center checksPeriodos"><label><input type="checkbox" class="ace checkperiodo" name="periodos[' + value.cod_tipo_periodo + '][seleccionado]" checked onclick="checkPeriodoChecked(this)" padre="' + value.padre + '" value="' + value.cod_tipo_periodo + '"></input><span class="lbl"></span></label></td>';
            var modalidad = "";
            if (value.modalidad.length > 1) {
                datos += '<td><select name="periodos[' + value.cod_tipo_periodo + '][modalidad]"  id="modalidad" data-placeholder="' + langFrmMatricula.seleccione_modalidad + '" class="select-modalidad" periodo="' + value.cod_tipo_periodo + '" salo="' + value.solo + '">';
                datos += '<option ></option>';
                $(value.modalidad).each(function(k, modal) {
                    var m = '';
                    var descripcion = '';
                    if (modal.modalidad == 'intensiva') {
                        m = 'intensiva';
                        descripcion = langFrmMatricula.intensiva;
                    } else {
                        m = 'normal';
                        descripcion = langFrmMatricula.normal;
                    }
                    datos += '<option value="' + m + '">' + modal.nombre_periodo + ' [' + descripcion + ']' + '</option>';
                });
                datos += '</select></td><td><select disabled="disabled" data-placeholder="  " ></select></td>';
            } else {
                datos += '<td><span>';
                $(value.modalidad).each(function(k, modal) {
                    modalidad = modal.modalidad;
                    var m = '';
                    if (modal.modalidad === 'intensiva') {
                        m = langFrmMatricula.intensiva;
                    } else {
                        m = langFrmMatricula.normal;
                    }
                    datos += modal.nombre_periodo + '[' + m + ']';
                });
                datos += '</span></td>';
            }

            if (modalidad !== "" && (value.solo === true || respuesta.length == 1) && value.cod_tipo_periodo <= 1) {
                $.ajax({
                    data: 'modalidad=' + modalidad + '&cod_plan_academico=' + $("#cod_plan_academico_porfa").val() + "&periodo=" + value.cod_tipo_periodo + "&cod_alumno=" + $("#cod_alumno").val(),
                    url: BASE_URL + "matriculas/getComisiones",
                    type: 'POST',
                    dataType: 'json',
                    cache: false,
                    async: false,
                    success: function(respuesta) {
                        var disabled = respuesta.length === 0 ? "disabled='disabled'" : "";
                        var placeHolder = respuesta.length === 0 ? langFrmMatricula.no_hay_comisiones_disponibles : langFrmMatricula.seleccione_comision;
                        datos += "<td>";
                        datos += '<select '+(value.cod_tipo_periodo>1?'disabled':'')+' class="" name="periodos[' + value.cod_tipo_periodo + '][comision]" id="comisiones" data-placeholder="' + placeHolder + '" data-content="" ' + disabled + '>';
                        datos += "<option></option>";
                        $(respuesta).each(function(k, comisiones) {
                            var disabledo = '';
                            if (comisiones.habilita === false) {
                                disabledo = "disabled='disabled'";
                            }
                            datos += "<option tiene_horarios='" + comisiones.tiene_horarios + "' value='" + comisiones.codigo + "'" + disabledo + ">" + comisiones.nombre + "</option>";
                        });
                        datos += "</select>";
                        datos += '&nbsp <span class="btn btn-warning btn-xs hide" id="pop-horarios" data-rel="popover" onclick="getHorariosComision()" title="' + langFrmMatricula.frm_nuevaMatricula_HorariosDeCursado + '" ><i class="icon-bell-alt  bigger-110 icon-only"></i></span>';
                        datos += "</td>";
                    }
                });
            } else {
                if (value.solo === false) {
                    datos += "<td>";
                    datos += "</td>";
                }
                else if(value.cod_tipo_periodo > 1) {
                    datos += "<td>";
                    datos += "</td>";
                }
            }
            datos += '</tr>';
        });

        $('#tablaPeriodos tbody').html(datos);
        if (todosPeriodos) {
            $('.checksPeriodos').addClass('hide');
        }

        $('.select-modalidad').on('change', function() {
            var selectModalidad = $(this);
            var pediodo = $(this).attr("periodo");
            $.ajax({
                data: 'modalidad=' + $(this).val() + '&cod_plan_academico=' + $("#cod_plan_academico_porfa").val() + "&periodo=" + $(this).attr("periodo") + "&cod_alumno=" + $("#cod_alumno").val(),
                url: BASE_URL + "matriculas/getComisiones",
                type: 'POST',
                dataType: 'json',
                cache: false,
                async: false,
                success: function(respuesta) {
                    var select = "";
                    var placeHolder = respuesta.lenght === 0 ? langFrmMatricula.no_hay_comisiones_disponibles : langFrmMatricula.seleccione_comision;
                    var disabled = respuesta.lenght === 0 ? "disabled" : "";
                    select += '<select'+(pediodo>1?'disabled':'')+' class="" name="periodos[' + pediodo + '][comision]" id="comisiones" data-placeholder="' + placeHolder + '"  data-content="" ' + disabled + '>';
                    select += "<option></option>";
                    $(respuesta).each(function(k, comisiones) {
                        var readonly = '';
                        if (comisiones.tiene_horarios == false) {
                            readonly = 'disabled';
                        }
                        if (comisiones.habilita === false) {
                            readonly = 'disabled';
                        }
                        select += "<option value='" + comisiones.codigo + "' " + readonly + ">" + comisiones.nombre + "</option>";
                    }
                    );
                    select += "</select>";
                    select += "";
                    selectModalidad.parent().next().html(select);
                }
            });
            $('#tablaPeriodos tbody select').chosen({width: '80%'});
        });
        $('#tablaPeriodos tbody select').chosen({width: '80%'});
        $.fancybox.update();
    }



function pedirDocumentacion(cod_plan_academico) {
    if(!cod_plan_academico){
        $('#documentacion').html('');
        $('#documentacion').trigger('chosen:updated');

    }
    $.ajax({
        url:BASE_URL + 'matriculas/documentacionPlan',
        data:{'plan_academico':cod_plan_academico},
        type:'POST',
        dataType:'json',
        success: function(documentaciones){
            options = "";
            $.each(documentaciones, function (indice, documentacion) {
                options += '<option value="' + documentacion.codigo + '">' + documentacion.nombre + '</option>';
            });
            $('#documentacion').html(options);
            $('#documentacion').trigger('chosen:updated');

        }
    });
}

function init() {
    $("#cod_alumno").ajaxChosen({
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
            data: {buscar: options.term, estado: 'habilitada'},
            dataType: "JSON",
            cache: false,
            success: function(respuesta) {
                reiniciarMatriculacion();
                var terms = {};
                $.each(respuesta, function(i, val) {
                    terms[val.codigo] = val.nombreapellido;
                });
                response(terms);
            }
        });
    });

    $('[data-rel=popover]').popover({container: 'body', html: true, trigger: 'manual'});
    $('body').not('.popover').on('click', function() {
        $('.popover').hide().fadeIn('fast').remove();
    });

    $(".fecha").datepicker({
        changeMonth: false,
        changeYear: false,
        dateFormat: 'dd/mm/yy'
    });

    $.validator.addMethod(
            "Latino",
            function(value, element) {
                return value.match(/^\d\d?\/\d\d?\/\d\d\d\d$/);
            },
            langFrmMatricula.error_fecha
            );

    $('#nuevaMatricula').validate({
        rules: {
            fechaPrimerPago: {
                Latino: true
            }
        }
    });

    $('select').each(function() {
        nombreSelects.push($(this).attr('name'));
    });

    $("#obs").click(function() {
        if ($("textarea[name='observaciones']").hasClass('hide')) {
            $("textarea[name='observaciones']").removeClass('hide');
        } else {
            $("textarea[name='observaciones']").addClass('hide');
        }
    });

    $('.chosen-select').chosen({
        width: "100%"
    });

    $("input[name='todosPeriodos']").click(function() {
        var checkboxes = $("#tablaPeriodos input");
        $(checkboxes).each(function(key, input) {
            if (input.type == 'checkbox') {
                input.checked = $("input[name='todosPeriodos']").is(':checked');
            }
        });
    });

    $('.fancybox-wrap').on('click', '.checkperiodo', function() {
        getPlanesPago();
    });

    function reiniciarMatriculacion() {
        $("#esquema").addClass("hide");
        $("#errores").hide();
        $(".periodos").addClass("hide");
        $('select[name="planes"]').empty();
        $('select[name="planes"]').trigger('chosen:updated');
        $('select[name="cod_plan_academico"]').empty();
        $('select[name="cod_plan_academico"]').trigger('chosen:updated');
        $('select[name="cod_plan_academico"]').on('change', function() { 
            pedirDocumentacion($('#cod_plan_academico_porfa').val());
        });
        $.fancybox.reposition();
    }    

    function dibujarEsquema(idCuota) {
        var vistaDetalle = '';
        $(esquema).each(function() {
            if (this.detalle.codigoMatricula == idCuota) {
                vistaDetalle += this.detalle.matricula + '<br>';
                $(this.detalle.cuotas).each(function() {
                    vistaDetalle += this + '<br>';
                });
                $('#esquema').html(vistaDetalle).hide().fadeIn();
            }
        });
    }

    function elegirCobroMatricula() {
        var vrml = "";
        $("#cobroMatricula").html('');
        vrml += '<input name="" id="checkboxCobroMatricula" value="true" checked type="checkbox" class="ace ace-checkbox-2" onclick="setCobroMatricula()">';
        if (BASE_IDIOMA == 'es') {
            vrml += '<span class="lbl">El estudiante pagará la incripción ahora (desmarcar si no lo hará)</span>';
        }
        if (BASE_IDIOMA == 'pt') {
            vrml += '<span class="lbl">O Aluno vai pagar a matricula agora (desmarcar se não for pagar agora)</span>';
        }
        $("#cobroMatricula").html(vrml);
    }

    function getPlanesPago() {
        $.ajax({
            url: BASE_URL + 'matriculas/getPlanesPago',
            type: 'POST',
            data: $("#nuevaMatricula").serialize(),
            dataType: 'json',
            cache: false,
            success: function(respuesta) {
                if (respuesta.length > 0) {
                    actualizarSelect('planes', respuesta);
                    refrescarSelects('comisiones');
                    elegirCobroMatricula(); // PU239
                } else {
                    gritter(langFrmMatricula.no_hay_planes_de_pago_definidos_para_esta_seleccion, false, '');
                    $("#esquema").empty();
                    $("[name=planes]").empty();
                    $("[name=planes]").trigger("chosen:updated");
                }
            }
        });
    }    
    
    $('select[name="cod_alumno"]').on('change', function() {
        reiniciarMatriculacion();
        $.ajax({
            data: 'cod_alumno=' + $("#cod_alumno").val(),
            url: BASE_URL + "matriculas/getPlanesAcademicos",
            type: 'POST',
            dataType: 'json',
            cache: false,
            success: function(respuesta) {
                var _html = '';
                _html += '<select name="cod_plan_academico" id="cod_plan_academico_porfa" onchange="cod_estado_academico_change()">';                
                _html += '<option value="-1">';
                _html += "";
                _html += '</option>';
                    
                $(respuesta).each(function() {
                    // Inicio Ticket 646
                    if (this.codigo == 1 || this.codigo == 31 || this.codigo == 57) {
                        var habilitado = this.matricular ? "" : "disabled='disabled'";
                        var matriculado = this.matricular ? "" : "[" + langFrmMatricula.matriculado + "]";
                    } else {
                        var habilitado = "";
                        var matriculado = this.matricular ? "" : "[" + langFrmMatricula.matriculado + "]";
                    }
                    // Fin Ticket 646
                    _html += '<option value="' + this.cod_plan_academico + '" '+habilitado+'>';
                    _html += this.nombre + matriculado;
                    _html += '</option>';
                });
                _html += '</select>';
                var element = $("[name=cod_plan_academico]").closest("div");
                $(element).html(_html);
            }
        });
    });

    /**/

    function actualizarSelect(name, option) {
        $('select[name="' + name + '"]').empty();
        switch (name) {
            case'cod_plan_academico':
                $('select[name="' + name + '"]').append('<option value=""></option>');
                $(option).each(function() {
                    if (this.solo == false) {
                        $('select[name="' + name + '"]').append("<option value='" + this.codigo + "'>" + this.nombre_es + "</option>");
                    } else {
                        $('select[name="' + name + '"]').append("<option value='" + this.codigo + "' disabled>" + this.nombre_es + "</option>");
                    }
                });
                break;

            case 'planes':
                if (option.length > 1) {
                    $('select[name="' + name + '"]').append('<option value=""></option>');
                }
                if (option.length !== 0) {
                    $(option).each(function() {
                        $('select[name="' + name + '"]').append('<option value="' + this.codigo + '">' + this.nombre + '</option>');
                    });
                    $('select[name="' + name + '"]').attr('disabled', false);
                    $('select[name="' + name + '"]').attr("data-placeholder", langFrmMatricula.SELECCIONE_UNA_OPCION);
                    if (option.length < 2) {
                        $('select[name="' + name + '"]').change();
                    }
                } else {
                    $('select[name="' + name + '"]').attr('disabled', true);
                    $('select[name="' + name + '"]').attr("data-placeholder", langFrmMatricula.NO_HAY_PLANES);
                }
                $("#esquema").html("");
                $("#ver-plan").addClass("hide");
                break;


            case 'cuotas':
                $('select[name="' + name + '"]').append('<option value=""></option>');
                for (var x in option) {
                    $('select[name="' + name + '"]').append('<option value="' + option[x].detalle.codigoMatricula + '">' + x + ' cuota/s</option>');
                }
                break;
        }

        $('select[name="' + name + '"]').trigger("chosen:updated");
        if (option != '') {
            $('#' + name + '_chosen').effect("shake");
        }
    }

    /**/

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
                var _html = '';
                _html += "<tr>";
                _html += "<th style='width: 194px;'>";
                _html += langFrmMatricula.concepto;
                _html += "</th>";
                _html += "<th>";
                _html += langFrmMatricula.financiacion;
                _html += "</th>";
                _html += "<th>";
                _html += langFrmMatricula.fecha_primer_pago;
                _html += "</th>";
                _html += "</tr>";                
                $("#esquema").append(_html);                
                $.each(obj, function(key, value) {
                    $.each(value, function(key, value) {
                        if(value.hasOwnProperty('financiaciones')) {
                            var option = "";
                            option += "<option></option>";
                            $.each(value.financiaciones, function (key2, row) {
                                option += "<option value='" + key2 + "' calendario='" + row.limite_primer_cuota + "' fecha_limite='" + row.fecha_limite + "' fecha_hoy='" + row.fecha_hoy + "'>" + row.nombre + "</option>";
                            });
                            var tr = "<tr id='" + value.concepto + "'>";
                            tr += "<input type='hidden' name='plan-concepto[]' value='" + key + "'>";
                            tr += "<td>" + value.concepto + "</td>";
                            tr += "<td>";
                            tr += "<select id='codigo-financiacion-" + key + "' posicion='" + key + "' name='codigo-financiacion[]'  class='select-financiaciones chosen-select'  data-placeholder='" + langFrmMatricula.seleccione_financiacion + "'  style='width: 250px;'>" + option + "</select>";
                            tr += "</td>";
                            tr += '<td>';
                            tr += '<div class="input-group "> ';
                            tr += '<span class=" hide fecha-hoy-financiacion-' + key + '">' + langFrmMatricula.paga_al_momento + '</span>';
                            tr += '<input readonly class="form-control hide date-picker calendario-financiacion-' + key + '  input-mask-date" id="id-date-picker-' + key + '" value="" type="text" data-date-format="dd-mm-yyyy" name="fechaPrimerPago[]">';
                            tr += '<span class="input-group-addon hide calendario-financiacion-' + key + '">';
                            tr += '<i class="icon-calendar bigger-110"></i>';
                            tr += '</span>';
                            tr += '<div class="btn btn-link ver-planes">Ver</div>';
                            tr += '</div>';
                            tr += '</td>';
                            tr += "</tr>";
                            $("#esquema").append(tr);
                            $(".chosen-select").chosen();
                        }
                    });
                });
                $('.ver-planes').click(function() {
                    if(!estadosActualesVencimientos.length > 0)
                    {
                        var param = getParametros();
                        $("#errores").hide();
                        $.ajax({
                            url: BASE_URL + 'matriculas/getDetallePlan',
                            data: param,
                            dataType: 'JSON',
                            type: 'POST',
                            cache: false,
                            async: false,
                            success: function(respuesta) {
                                var retorno = generarTablaFinanciacion(respuesta);
                                if (retorno !== false) {
                                    $("#detalle-plan").html(retorno);
                                    //debugger;
                                    $('#stack1').modal('show');
                                    $(".date-picker-ver").datepicker({
                                        dateFormat: 'dd/mm/yy',
                                        onSelect: function(dateText, inst) {
                                            fechaChange();
                                        }
                                    });
                                    $('.date-picker-ver').on('keyup', function(){
                                        fechaChange();
                                    });
                                }
                            }
                        });
                    }
                    else
                    {
                        $('#stack1').modal('show');
                    }
                });
                $.fancybox.reposition();
            }
        });
    });
    $(".fancybox-wrap").on('change', '.select-financiaciones', function() {
        var optionseleccionado = $("#" + $(this).attr("id") + " option:selected");
        var medio_pago = $("[name=medio_pago_matricula]").val();
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
                var tr = $(".calendario-financiacion-" + $(this).attr("posicion")).closest("tr");
                if ($(tr).attr("id") == "MATRICULA" && medio_pago != 2){
                    $(".calendario-financiacion-" + $(this).attr("posicion")).prop("disabled", true);
                    $(".calendario-financiacion-" + $(this).attr("posicion")).val(fecha_hoy);                    
                }
                $("#id-date-picker-" + $(this).attr("posicion")).val(optionseleccionado.attr("fecha_limite"));
                break;

            case "sin_fecha_limite":
                $(".fecha-hoy-financiacion-" + $(this).attr("posicion")).addClass("hide");
                $(".calendario-financiacion-" + $(this).attr("posicion")).removeClass("hide");
                $("#id-date-picker-" + $(this).attr("posicion")).val();
                $("#id-date-picker-" + $(this).attr("posicion")).datepicker({
                    changeMonth: false,
                    changeYear: false,
                    dateFormat: 'dd/mm/yy',
                    minDate: fecha_hoy
                });
                var tr = $("#id-date-picker-" + $(this).attr("posicion")).closest("tr");
                if ($(tr).attr("id") == "MATRICULA" && medio_pago != 2){
                    $("#id-date-picker-" + $(this).attr("posicion")).prop("disabled", true);
                    $("#id-date-picker-" + $(this).attr("posicion")).val(fecha_hoy);
                }
                break;
        }
    });

    $('.fancybox-wrap').on('change', 'select[name="cuotas"]', function() {
        refrescarSelects('cuotas');
        var idcuota = $(this).val();
        dibujarEsquema(idcuota);
    });

    $('.fancybox-wrap').on('change', '#comisiones', function() {
        var optionseleccionado = $("#comisiones option:selected");
        if (optionseleccionado.attr("tiene_horarios") == 'true') {
            $("#pop-horarios").removeClass('hide');
        } else {
            $("#pop-horarios").addClass('hide');
        }
        getPlanesPago();
    });

    $('.fancybox-wrap').on('submit', '#nuevaMatricula', function() {
        
        var param = getParametros();
        $.ajax({
            url: BASE_URL + 'matriculas/guardar',
            data: param,
            type: 'POST',
            dataType: 'json',
            success: function(respuesta) {
                if (respuesta.codigo === '0') {
                    $("#errores").html(respuesta.respuesta).hide().fadeIn('slow');
                } else {
                    var medio_pago = $("[name=medio_pago_matricula]").val();
                    var mensajeOk = '<div class="row-fluid"><h3><p class="text-center"><' + langFrmMatricula.validacion_ok + '<p></h3></div>';
                    $('#contenedorGeneral').parent().html(mensajeOk);
                    $.fancybox.close(true);
                    $.gritter.add({
                        title: langFrmMatricula.BIEN,
                        text: langFrmMatricula.MATRICULA_GUARDADA,
                        sticky: false,
                        time: '3000',
                        class_name: 'gritter-success'
                    });
                    var param = new Array();
                    param.push(respuesta.custom.cod_matricula, 1, 1);
                    var onclose = '';
                    if (medio_pago == "2"){
                        onclose = "redireccionar_a_boleto(" + respuesta.custom.cod_matricula + "," + respuesta.custom.cod_alumno + ")";
                    } else {
                        param.push('imprimir_recibo_cobro_matricula');
                    }
                    printers_jobs(5, param, onclose);                    
                    
                }
            }
        });
        return false;
    });
    
    
    $('.fancybox-wrap').on('submit', '#editarMatricula', function() {

        var param = getParametrosEditar();
        $.ajax({
            url: BASE_URL + 'matriculas/editar',
            data: param,
            type: 'POST',
            dataType: 'json',
            success: function(respuesta) {
                if (respuesta.codigo === '0') {
                    $("#errores").html(respuesta.respuesta).hide().fadeIn('slow');
                } else {
                    var medio_pago = $("[name=medio_pago_matricula]").val();
                    var mensajeOk = '<div class="row-fluid"><h3><p class="text-center"><' + langFrmMatricula.validacion_ok + '<p></h3></div>';
                    $('#contenedorGeneral').parent().html(mensajeOk);
                    $.fancybox.close(true);
                    $.gritter.add({
                        title: langFrmMatricula.BIEN,
                        text: langFrmMatricula.MATRICULA_GUARDADA,
                        sticky: false,
                        time: '3000',
                        class_name: 'gritter-success'
                    });
                    var param = new Array();
                    param.push(respuesta.custom.cod_matricula, 1, 1);
                    var onclose = '';
                    if (medio_pago == "2"){
                        onclose = "redireccionar_a_boleto(" + respuesta.custom.cod_matricula + "," + respuesta.custom.cod_alumno + ")";
                    } else {
                        param.push('imprimir_recibo_cobro_matricula');
                    }
                    printers_jobs(5, param, onclose);                    
                    
                }
            }
        });
        return false;
    });
    
    listar_cajas_cobrar();
    $('select[name="cod_plan_academico"]').on('change', function() {
        pedirDocumentacion($('#cod_plan_academico_porfa').val());
    });
}

function checkPeriodoChecked(element) {
    if ($(element).is(':checked')) {
        $('input[padre="' + $(element).val() + '"]').each(function(k, elemento) {
            $(elemento).prop('disabled', false);
        });
    } else {
        $('input[padre="' + $(element).val() + '"]').each(function(k, elemento) {
            $(elemento).prop('checked', false);
            $(elemento).prop('disabled', true);
        });
    }
}

function getHorariosComision() {
    $('[data-rel=popover]').popover({container: 'body', html: true, trigger: 'manual'});
    var boton = $("#pop-horarios");
    $.ajax({
        url: BASE_URL + 'matriculas/getHorarioComision',
        data: 'codigo-comision=' + $("#comisiones").val(),
        cache: false,
        type: 'POST',
        dataType: 'json',
        success: function(respuesta) {
            res = respuesta;
            var tabla = "";
            if (res.length === 0) {
                tabla = '<span class="label label-xlg label-primary arrowed arrowed-right">' + langFrmMatricula.NO_HAY_HORARIOS + '</span>';
            } else {
                tabla = "<table class='table table-striped table-bordered table-hover'><tr><th>" + langFrmMatricula.periodos_dia + "</th><th>" + langFrmMatricula.horadesde_horario + "</th><th>" + langFrmMatricula.horaHasta_horario + "</th></tr>";
                $(res).each(function(key, fila2) {
                    var dia = "";
                    for (property in fila2) {
                        dia = property;
                    }
                    tabla += "<tr><td>" + dia + "</td><td>" + fila2[dia].desde + "</td><td>" + fila2[dia].hasta + "</td></tr>";
                });
                tabla += "</table>";
            }
            boton.attr("data-content", tabla);
            boton.popover("show");
        }
    });
}

function cod_estado_academico_change(){
    var cod_plan_academico = $("[name=cod_plan_academico]").val();
    $('select[name="planes"]').empty();
    $('select[name="planes"]').trigger('chosen:updated');
    $.ajax({
        data: 'cod_plan_academico=' + cod_plan_academico + '&cod_alumno=' + $("#cod_alumno").val(),
        url: BASE_URL + "matriculas/getPeriodosPlanAcademico",
        type: 'POST',
        dataType: 'json',
        cache: false,
        success: function(respuesta) {
            if (respuesta.codigo == 1) {
                cargarTablaPeriodos(respuesta.data);
            }
            else {
                $("[name=periodos]").empty();
                $("[name=periodos]").trigger("chosen:updated");
                gritter(respuesta.msgerror);
            }
        }
    });
    refrescarSelects('planes');
    $("#esquema").empty();
    pedirDocumentacion(cod_plan_academico);
    return false;
}

function select_medio_pago_change(){
    var tr = $("#esquema").find("tr#MATRICULA");
    var medio = $("[name=medio_pago_matricula]").val();
    $.each(tr, function(key, element){
        if (medio != 2){
            $(element).find("[name='fechaPrimerPago[]']").val(fecha_hoy);
            $(element).find("[name='fechaPrimerPago[]']").prop("disabled", true);
        } else {
            $(element).find("[name='fechaPrimerPago[]']").prop("disabled", false);
        }
    });
    listar_cajas_cobrar();
}

function getParametros(nuevoPrimerPago){
    if (typeof(nuevoPrimerPago)==='undefined') nuevoPrimerPago = null;
    var param = new Array();
    var medio_pago = $("[name=medio_pago_matricula]").val();
    param.push({name: "cobrarmatricula", value: $("#checkboxCobroMatricula").prop("value")});
    param.push({name: "cod_alumno", value: $("[name=cod_alumno]").val()});
    param.push({name: "cod_plan_academico", value: $("[name=cod_plan_academico]").val()});
    param.push({name: "medio_pago_matricula", value: medio_pago});
    param.push({name: "planes", value: $("[name=planes]").val()});
    param.push({name: "numero_cupon", value: $("[name=numero_cupon]").val()});
    param.push({name: "observaciones", value: $("[name=observaciones]").val()});
    param.push({name: "medio_pago_cuotas", value: $("[name=medio_pago_cuotas]").val()});
    var codigoFinanciacion = $("[name='codigo-financiacion[]']");
    $.each(codigoFinanciacion, function(key, element){
       param.push({name: "codigo-financiacion[]", value: $(element).val()});                       
    });


    var fechaPrimerPago = $("[name='fechaPrimerPago[]']");

    var filas_vencimientos = $('.date-picker-ver').map(function() {
        return $(this).val();
    }).get();

    param.push({name: "filas_vencimientos", value: filas_vencimientos});

    $.each(fechaPrimerPago, function(key, element){
        param.push({name: "fechaPrimerPago[]", value: $(element).val()});
    });
    var tr = $("#tablaPeriodos").find("tbody").find("tr");
    $.each(tr, function(key, linea){
        if ($(linea).find(".checkperiodo").is(":checked")){
            var name = $(linea).find(".checkperiodo").attr("name");
            var valor = $(linea).find(".checkperiodo").val();
            param.push({name: name, value: valor});

            var name1 = $(linea).find("#comisiones").attr("name");
            var valor1 = $(linea).find("#comisiones").val();
            if (valor1){
                param.push({name: name1, value: valor1});
            }
        }
    });
    var esquema = $("[name='plan-concepto[]']");
    $.each(esquema, function(key, element){
        param.push({name: "plan-concepto[]", value: $(element).val()});
    });
    
    var datos = $("[name='documentacion[]']").val();
    if(datos != null){
        $.each(datos, function(key, value){
            param.push({name: 'documentacion[' + key + ']', value: value});
        });
    }  
    var datos = $("[name='material[]']").val();
    if(datos != null){
        $.each(datos, function(key, value){
            param.push({name: 'material[' + key + ']', value: value});
        });  
    }
    param.push({name: "cod_caja", value: $("[name=caja_cobro_matricula]").val()});
    switch (medio_pago){
        case "3":
            param.push({name: "pos_tarjeta", value: $("[name=pos_tarjeta]").val()});
            param.push({name: "tarjetas", value: $("[name=tarjetas]").val()});
            param.push({name: "medio_tarjeta_autorizacion", value: $("[name=medio_tarjeta_autorizacion]").val()});
            param.push({name: "medio_tarjeta_cupon", value: $("[name=medio_tarjeta_cupon]").val()});
            param.push({name: "medio-tajeta-cupon", value: $("[name=medio-tajeta-cupon]").val()});
            param.push({name: "medio_tarjeta_autorizacion", value: $("[name=medio_tarjeta_autorizacion]").val()});
            break;
        
        case "4":
            param.push({name: "medio_cheque_banco", value: $("[name=medio_cheque_banco]").val()});
            param.push({name: "medio_cheque_tipo", value: $("[name=medio_cheque_tipo]").val()});
            param.push({name: "medio_cheque_fecha", value: $("[name=medio_cheque_fecha]").val()});
            param.push({name: "medio_cheque_numero", value: $("[name=medio_cheque_numero]").val()});
            param.push({name: "medio_cheque_emisor", value: $("[name=medio_cheque_emisor]").val()});
            break;
            
        case "6":
            param.push({name: "medio_deposito_banco", value: $("[name=medio_deposito_banco]").val()});
            param.push({name: "medio_deposito_fecha", value: $("[name=medio_deposito_fecha]").val()});
            param.push({name: "medio_deposito_transaccion", value: $("[name=medio_deposito_transaccion]").val()});
            param.push({name: "medio_deposito_cuenta", value: $("[name=medio_deposito_cuenta]").val()});
            break;
            
        case "7":
            param.push({name: "medio_transferencia_banco", value: $("[name=medio_transferencia_banco]").val()});
            param.push({name: "medio_transferencia_fecha", value: $("[name=medio_transferencia_fecha]").val()});
            param.push({name: "medio_transferencia_numero", value: $("[name=medio_transferencia_numero]").val()});
            param.push({name: "medio_transferencia_cuenta", value: $("[name=medio_transferencia_cuenta]").val()});
            break;

        case "8":
            param.push({name: "pos_tarjeta", value: $("[name=pos_tarjeta]").val()});
            param.push({name: "debito", value: $("[name=debito]").val()});
            param.push({name: "medio_tarjeta_autorizacion", value: $("[name=medio_tarjeta_autorizacion]").val()});
            param.push({name: "medio_tarjeta_cupon", value: $("[name=medio_tarjeta_cupon]").val()});
            param.push({name: "medio-tajeta-cupon", value: $("[name=medio-tajeta-cupon]").val()});
            param.push({name: "medio_tarjeta_autorizacion", value: $("[name=medio_tarjeta_autorizacion]").val()});
            break;
    }
    return param;
}

function getParametrosEditar(){
    var param = new Array();
    var medio_pago = $("[name=medio_pago_matricula]").val();
    param.push({name: "cod_alumno", value: $("[name=cod_alumno]").val()});
    param.push({name: "cod_plan_academico", value: $("[name=cod_plan_academico]").val()});
    param.push({name: "medio_pago_matricula", value: medio_pago});
    param.push({name: "planes", value: $("[name=planes]").val()});
    param.push({name: "numero_cupon", value: $("[name=numero_cupon]").val()});
    param.push({name: "observaciones", value: $("[name=observaciones]").val()});
    var codigoFinanciacion = $("[name='codigo-financiacion[]']");
    $.each(codigoFinanciacion, function(key, element){
       param.push({name: "codigo-financiacion[]", value: $(element).val()});                       
    });
    var fechaPrimerPago = $("[name='fechaPrimerPago[]']");
    $.each(fechaPrimerPago, function(key, element){
        param.push({name: "fechaPrimerPago[]", value: $(element).val()});
    });
    var tr = $("#tablaPeriodos").find("tbody").find("tr");
    $.each(tr, function(key, linea){
        var name = $(linea).find(".checkperiodo").attr("name");
        var valor = $(linea).find(".checkperiodo").val();
        param.push({name: name, value: valor});
        var name1 = $(linea).find("#comisiones").attr("name");
        var valor1 = $(linea).find("#comisiones").val();
        if (valor1){
            param.push({name: name1, value: valor1});
        }
    });
    var esquema = $("[name='plan-concepto[]']");
    $.each(esquema, function(key, element){
        param.push({name: "plan-concepto[]", value: $(element).val()});
    });
    
    param.push({name: 'documentacion[]', value: $("[name='documentacion[]']").val()});
    var datos = $("[name='documentacion[]']").val();
    if(datos != null)
        $.each(datos, function(key, value){
            param.push({name: 'documentacion[' + key + ']', value: value});
        });  
    
    param.push({name: 'material[]', value: $("[name='material[]']").val()});    
    var datos = $("[name='material[]']").val();
    if(datos != null)
        $.each(datos, function(key, value){
            param.push({name: 'material[' + key + ']', value: value});
        });  
    
    param.push({name: "cod_caja", value: $("[name=caja_cobro_matricula]").val()});
    switch (medio_pago){
        case "3":
            param.push({name: "pos_tarjeta", value: $("[name=pos_tarjeta]").val()});
            param.push({name: "tarjetas", value: $("[name=tarjetas]").val()});
            param.push({name: "medio_tarjeta_autorizacion", value: $("[name=medio_tarjeta_autorizacion]").val()});
            param.push({name: "medio_tarjeta_cupon", value: $("[name=medio_tarjeta_cupon]").val()});
            param.push({name: "medio-tajeta-cupon", value: $("[name=medio-tajeta-cupon]").val()});
            param.push({name: "medio_tarjeta_autorizacion", value: $("[name=medio_tarjeta_autorizacion]").val()});
            break;
        
        case "4":
            param.push({name: "medio_cheque_banco", value: $("[name=medio_cheque_banco]").val()});
            param.push({name: "medio_cheque_tipo", value: $("[name=medio_cheque_tipo]").val()});
            param.push({name: "medio_cheque_fecha", value: $("[name=medio_cheque_fecha]").val()});
            param.push({name: "medio_cheque_numero", value: $("[name=medio_cheque_numero]").val()});
            param.push({name: "medio_cheque_emisor", value: $("[name=medio_cheque_emisor]").val()});
            break;
            
        case "6":
            param.push({name: "medio_deposito_banco", value: $("[name=medio_deposito_banco]").val()});
            param.push({name: "medio_deposito_fecha", value: $("[name=medio_deposito_fecha]").val()});
            param.push({name: "medio_deposito_transaccion", value: $("[name=medio_deposito_transaccion]").val()});
            param.push({name: "medio_deposito_cuenta", value: $("[name=medio_deposito_cuenta]").val()});
            break;
            
        case "7":
            param.push({name: "medio_transferencia_banco", value: $("[name=medio_transferencia_banco]").val()});
            param.push({name: "medio_transferencia_fecha", value: $("[name=medio_transferencia_fecha]").val()});
            param.push({name: "medio_transferencia_numero", value: $("[name=medio_transferencia_numero]").val()});
            param.push({name: "medio_transferencia_cuenta", value: $("[name=medio_transferencia_cuenta]").val()});
            break;
        case "8":
            param.push({name: "pos_tarjeta", value: $("[name=pos_tarjeta]").val()});
            param.push({name: "debito", value: $("[name=debito]").val()});
            param.push({name: "medio_tarjeta_autorizacion", value: $("[name=medio_tarjeta_autorizacion]").val()});
            param.push({name: "medio_tarjeta_cupon", value: $("[name=medio_tarjeta_cupon]").val()});
            param.push({name: "medio-tajeta-cupon", value: $("[name=medio-tajeta-cupon]").val()});
            param.push({name: "medio_tarjeta_autorizacion", value: $("[name=medio_tarjeta_autorizacion]").val()});
            break;
    }
    return param;
}

function getTarjetas(debito) {
    var cod_terminal = $('select[name="pos_tarjeta"]').val();
    var c = '';
    var URL = '';
    if (debito){
        var URL = BASE_URL + 'cobros/getTarjetasDebito'
    }else{
        var URL = BASE_URL + 'cobros/getTarjetas'
    }

    $.ajax({
        url: URL,
        data: {codigo: cod_terminal},
        type: 'POST',
        cache: false,
        async: false,
        success: function(respuesta) {
            console.log(respuesta);
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

function crearSelect(nombre) {
    var label = '';
    switch (nombre) {
        case 'pos_tarjeta':
            label = langFrmMatricula.terminal;
            break;
        case 'tarjetas':
            label = langFrmMatricula.TARJETA;
            break;
        case 'debito':
            label = langFrmMatricula.TDEBITO;
            break;
        case 'medio_tarjeta_banco':
            label = langFrmMatricula.banco;
            break;
        case 'medio_cheque_banco':
            label = langFrmMatricula.banco;
            break;
        case 'medio_cheque_tipo':
            label = langFrmMatricula.tipo_cheque;
            break
        case 'medio_deposito_banco':
            label = langFrmMatricula.DEPOSITO_BANCARIO;
            break;
        case 'medio_transferencia_banco':
            label = langFrmMatricula.banco;
            break;
    }
    $('.pago').append('<div class="form-group col-md-4 Mpago dinamico"><label for="exampleInputFile">' + label + '</label><select name="' + nombre + '" class="form-control" title=""></select></div>');
}

function listar_cajas_cobrar(){
    $(".pago").html("");
    var cod_medio = $("[name=medio_pago_matricula]").val();
    $("[name=caja_cobro_matricula]").find('option').remove();
    $("[name=caja_cobro_matricula]").append('<option value="-1">(' + langFrmMatricula.recuperando + '...)</option>');
    $("[name=caja_cobro_matricula]").prop("disabled", true);
    $("[name=caja_cobro_matricula]").trigger("chosen:updated");
    $.ajax({
        url: BASE_URL + 'cobros/getCajasCobrar',
        type: 'POST',
        dataType: 'json',
        data: {
            cod_medio: cod_medio
        },
        success: function(_json){            
            $("[name=caja_cobro_matricula]").find('option').remove();
            if (_json.length > 0){
                var tiene_cajas = false;
                $.each(_json, function(key, value){
                    if (value.estado == 'abierta' || value.conf_automatica == 0){
                        $("[name=caja_cobro_matricula]").append("<option value='" + value.cod_caja + "'>"+ value.nombre + "</option>");
                        tiene_cajas = true;
                    } else {
                        $("[name=caja_cobro_matricula]").append("<option value='" + value.cod_caja + "' disabled='true'>" + value.nombre + "&nbsp;(" + langFrmMatricula.caja_cerrada + ")" + "</option>");
                    }
                });
                if (tiene_cajas){
                    $("[name=caja_cobro_matricula]").prop("disabled", false);
                    $("[name=caja_cobro_matricula]").trigger("chosen:updated");
                    var disabled = '';
                    switch (cod_medio){
                        case "3":
                            var pos = getTerminales();
                            if (jQuery.parseJSON(pos).length > 0) {
                                crearSelect('pos_tarjeta');
                                var select3 = $('select[name="pos_tarjeta"]');
                                var seleccionar = '';
                                cargarSelect(select3, pos, seleccionar);
                            }
                            crearSelect('tarjetas');
                            var tarjetas = getTarjetas();
                            $('select[name="tarjetas"]').empty();
                            var select = $('select[name="tarjetas"]');
                            var seleccionar = '';
                            cargarSelect(select, tarjetas, seleccionar);
                            var seleccionar = '';
                            $('.pago').append('<div class="form-group Mpago dinamico col-xs-4"><label for="exampleInputFile">' + langFrmMatricula.codigo_cupon + '</label><input name="medio_tarjeta_cupon" value="' + seleccionar + '" class="form-control" ' + disabled + '></div>');
                            var seleccionar = '';
                            $('.pago').append('<div class="form-group Mpago dinamico col-xs-4"><label for="exampleInputFile">' + langFrmMatricula.codigo_autorizacion + '</label><input name="medio_tarjeta_autorizacion" value="' + seleccionar + '" class="form-control" ' + disabled + '></div>');
                            $.fancybox.update();
                            break;

                        case "4":
                            var bancos = getBancos();
                            crearSelect('medio_cheque_banco');
                            var select = $('select[name="medio_cheque_banco"]');
                            var seleccionar = '';
                            cargarSelect(select, bancos, seleccionar);
                            var tipos = getTiposCheque();
                            crearSelect('medio_cheque_tipo');
                            var select = $('select[name="medio_cheque_tipo"]');
                            var seleccionar = '';
                            cargarSelect(select, tipos, seleccionar);
                            var seleccionar = '';                
                            $('.pago').append('<div class="form-group col-md-4 Mpago dinamico"><label for="exampleInputFile">' + langFrmMatricula.fecha + '</label><input id="datepickerCheque" class="form-control date-picker" type="text"  value="' + seleccionar + '" name="medio_cheque_fecha" ' + disabled + '></div>');//<span class="input-group-addon"><i class="icon-calendar bigger-115"></i></span></div>');
                            var seleccionar = '';
                            $('.pago').append('<div class="form-group col-md-4 Mpago dinamico"><label for="exampleInputFile">' + langFrmMatricula.medio_cheque_numero_factura + '</label><input name="medio_cheque_numero" value="' + seleccionar + '" class="form-control" ' + disabled + '></div>');
                            var seleccionar = '';
                            $('.pago').append('<div class="form-group  col-md-4 Mpago dinamico"><label for="exampleInputFile">' + 
                                   // langFrmMatricula.medio_cheque_emisor_factura + 
                                    '</label><input type="hidden" name="medio_cheque_emisor" value="' + seleccionar + '" class="form-control" ' + disabled + '></div>');
                            $('#datepickerCheque').datepicker({
                                format: "dd/mm/yyyy",
                                language: "es",
                                autoclose: true
                            });
                            $.fancybox.update();
                            break;

                        case "6":
                            var bancos = getBancos();
                            crearSelect('medio_deposito_banco');
                            var select = $('select[name="medio_deposito_banco"]');
                            var seleccionar = '';
                            cargarSelect(select, bancos, seleccionar);
                            var seleccionar = '';
                            $('.pago').append('<div class="form-group col-md-4 Mpago dinamico"><label for="exampleInputFile">' + langFrmMatricula.fecha + '</label><input id="fechaDeposito" name="medio_deposito_fecha" value="' + seleccionar + '" class="form-control" ' + disabled + '></div>');
                            var seleccionar = '';
                            $('.pago').append('<div class="form-group col-md-4 Mpago dinamico"><label for="exampleInputFile">' + langFrmMatricula.medio_deposito_transaccion_factura + '</label><input name="medio_deposito_transaccion" value="' + seleccionar + '" class="form-control" ' + disabled + '></div>');
                            var seleccionar = '';
                            $('.pago').append('<div class="form-group col-md-4 Mpago dinamico"><label for="exampleInputFile">' + langFrmMatricula.medio_deposito_cuenta_factura + '</label><input name="medio_deposito_cuenta" value="' + seleccionar + '" class="form-control" ' + disabled + '></div>');
                            $('input[name="medio_deposito_fecha"]').datepicker({
                                format: "dd/mm/yyyy",
                                language: "es",
                                autoclose: true
                            });
                            $.fancybox.update();
                            break;

                        case "7":
                            var bancos = getBancos();
                            crearSelect('medio_transferencia_banco');
                            var select = $('select[name="medio_transferencia_banco"]');
                            var seleccionar = '';
                            cargarSelect(select, bancos, seleccionar);
                            var seleccionar = '';
                            $('.pago').append('<div class="form-group col-md-4 Mpago dinamico"><label for="exampleInputFile">' + langFrmMatricula.fecha + '</label><input id="fechaTrasferencia" name="medio_transferencia_fecha" value="' + seleccionar + '" class="form-control" ' + disabled + '></div>');
                            var seleccionar = '';
                            $('.pago').append('<div class="form-group col-md-4 Mpago dinamico"><label for="exampleInputFile">' + langFrmMatricula.medio_deposito_transaccion_factura + '</label><input name="medio_transferencia_numero" value="' + seleccionar + '" class="form-control" ' + disabled + '></div>');
                            var seleccionar = '';
                            $('.pago').append('<div class="form-group col-md-4 Mpago dinamico"><label for="exampleInputFile">' + langFrmMatricula.medio_deposito_cuenta_factura + '</label><input name="medio_transferencia_cuenta" value="' + seleccionar + '" class="form-control" ' + disabled + '></div>');
                            $('input[name="medio_transferencia_fecha"]').datepicker({
                                format: "dd/mm/yyyy",
                                language: "es",
                                autoclose: true
                            });
                            $.fancybox.update();
                            break;
                        case "8":
                            var pos = getTerminales();
                            if (jQuery.parseJSON(pos).length > 0) {
                                crearSelect('pos_tarjeta');
                                var select3 = $('select[name="pos_tarjeta"]');
                                var seleccionar = '';
                                cargarSelect(select3, pos, seleccionar);
                            }
                            crearSelect('debito');
                            var debito = getTarjetas(true);
                            $('select[name="debito"]').empty();
                            var select = $('select[name="debito"]');
                            var seleccionar = '';
                            cargarSelect(select, debito, seleccionar);
                            var seleccionar = '';
                            $('.pago').append('<div class="form-group Mpago dinamico col-xs-4"><label for="exampleInputFile">' + langFrmMatricula.codigo_cupon + '</label><input name="medio_tarjeta_cupon" value="' + seleccionar + '" class="form-control" ' + disabled + '></div>');
                            var seleccionar = '';
                            $('.pago').append('<div class="form-group Mpago dinamico col-xs-4"><label for="exampleInputFile">' + langFrmMatricula.codigo_autorizacion + '</label><input name="medio_tarjeta_autorizacion" value="' + seleccionar + '" class="form-control" ' + disabled + '></div>');
                            $.fancybox.update();
                            break;
                    }
                } else {
                    gritter(langFrmMatricula.no_tiene_cajas_habiertas, false, ' ');
                }
            } else {
                $("[name=caja_cobro_matricula]").find("option").remove();
                $("[name=caja_cobro_matricula]").append("<option value='-1'>(" + langFrmMatricula.sin_registros + ")</option>");
                $("[name=caja_cobro_matricula]").trigger("chosen:updated");
                gritter(langFrmMatricula.cajas_habilitadas_para_este_medio, false, ' ');
            }
        }
    });
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

function cargarSelect(select, opciones, seleccionar) {
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
                    $(select).append('<option value="' + this.codigo + '"' + selected + ' disabled="true">' + this.nombre + "&nbsp;(" + langFrmMatricula.caja_cerrada + ")" + '</option>');
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
        case 'debito':
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

function redireccionar_a_boleto(cod_matricula, cod_alumno){
    window.location.href = BASE_URL + 'boletos/index/' + cod_matricula + "/" + cod_alumno;
}
