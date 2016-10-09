
var langFRM = '';
$('.fancybox-wrap').ready(function() {
    var clavesFRM = Array("validacion_ok", "no_existen_comisiones", "no_existen_planes", "seleccione_opcion", "concepto", "cuota", "valor", "dia", "horadesde_horario", "horaHasta_horario",
            "seleccione_modalidad", "intensiva", "normal", "paga_al_momento", "no_hay_comisiones_disponibles", "seleccione_comision", "no_hay_planes_de_pago_definidos_para_esta_seleccion",
            "SELECCIONE_UNA_OPCION", "NO_HAY_PLANES", "financiacion", "fecha_primer_pago", "frm_nuevaMatricula_HorariosDeCursado", "seleccione_financiacion", "ver_detalle");

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
        var planSeleccionado = '';
        $("#errores").hide();
        var idioma = $('input[name="idioma"]').val();
        function fechaFormato(idioma) {
            if (idioma == 'es') {
                return 'dd/mm/yyyy';
            } else {
                return 'mm/dd/yyyy';
            }
        }

        $('select').chosen({
            width: '100%'
        });

        $('.fancybox-wrap .detalleHorario').hide();
        $('.btn-link').hide();
        $('input[name="fechaVigencia"]').datepicker({
            language: idioma,
            format: fechaFormato(idioma)
        });

        function generarTablaFinanciacion(ObjCuotas) {
            if (ObjCuotas.codigo !== "0") {
                var table = "<div class='row'>";
                var i = 0;
                $(ObjCuotas).each(function(key, fila) {
                    if (i % 10 == 0) {
                        table += "<div class='col-md-4'><table class='table table-striped table-bordered table-condensed table-hover'><tr><th>" + langFRM.concepto + "</th><th>" + langFRM.cuota + "</th><th>" + langFRM.valor + "</th></tr>";
                    }
                    table += "<tr><td>" + fila.concepto + "</td><td>" + fila.nrocuota + "</td><td>" + fila.valor + "</td></tr>";
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

        function crearDetalle(detalle) {
            if (detalle.length != '') {
                var table = '<table class="table table-striped table-bordered dataTable"><thead><th>' + langFRM.dia + '</th><th>' + langFRM.horadesde_horario + '</th><th>' + langFRM.horaHasta_horario + '</th></thead><tbody>';
                $(detalle).each(function(k, v) {
                    var dia = "";
                    for (dia in v) {
                        table += '<tr><td>' + dia + '</td><td>' + v[dia].desde + '</td><td>' + v[dia].hasta + '</td></tr>';
                    }
                });
                table += '</tbody></table>';
                $('.btn-warning').attr('data-content', table).popover({container: 'body', html: true, placement: 'left'});
                $('.fancybox-wrap .detalleHorario').fadeIn();
            }
        }

        function cargarTablaPeriodos(respuesta) {
            $(".periodos").removeClass("hide");
            var datos = '';
            $(respuesta).each(function(key, value) {
                datos += '<tr class=""> <td class="center"><label><input type="checkbox" class="ace checkperiodo" name="periodos[' + value.cod_tipo_periodo + '][seleccionado]" checked value="' + value.cod_tipo_periodo + '"></input><span class="lbl"></span></label></td>';
                var modalidad = "";
                if (value.modalidad.length > 1) {
                    datos += '<td><select name="periodos[' + value.cod_tipo_periodo + '][modalidad]"  id="modalidad" data-placeholder="' + langFRM.seleccione_modalidad + '" class="select-modalidad" periodo="' + value.cod_tipo_periodo + '" salo="' + value.solo + '">';
                    datos += '<option ></option>';
                    $(value.modalidad).each(function(k, modal) {
                        var m = '';
                        var descripcion = '';
                        if (modal.modalidad == 'intensiva') {
                            m = 'intensiva';
                            descripcion = langFRM.intensiva;
                        } else {
                            m = 'normal';
                            descripcion = langFRM.normal;
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
                            m = langFRM.intensiva;
                        } else {
                            m = langFRM.normal;
                        }
                        datos += modal.nombre_periodo + '[' + m + ']';
                    });
                    datos += '</span></td>';
                }
                
                if (modalidad !== "" && value.solo === true){
                    $.ajax({
                        data: 'modalidad=' + modalidad + '&cod_plan_academico=' + $("#cod_plan_academico").val() + "&periodo=" + value.cod_tipo_periodo,
                        url: BASE_URL + "aspirantes/listarComisiones",
                        type: 'POST',
                        dataType: 'json',
                        cache: false,
                        async: false,
                        success: function(respuesta) {
                            var disabled = respuesta.length === 0 ? "disabled='disabled'" : "";
                            var placeHolder = respuesta.length === 0 ? langFRM.no_hay_comisiones_disponibles : langFRM.seleccione_comision;

                            datos += "<td>";
                            datos += '<select class="" name="periodos[' + value.cod_tipo_periodo + '][comision]" id="comisiones" data-placeholder="' + placeHolder + '" data-content="" ' + disabled + '>';
                            datos += "<option></option>";

                            $(respuesta).each(function(k, comisiones) {
                                datos += "<option tiene_horarios='" + comisiones.tiene_horarios + "'value='" + comisiones.codigo + "'>" + comisiones.nombre + "</option>";
                            }
                            );
                            datos += "</select>";
                            datos += '&nbsp <span class="btn btn-warning btn-xs hide" id="pop-horarios" data-rel="popover"  title="' + langFRM.frm_nuevaMatricula_HorariosDeCursado + '" ><i class="icon-bell-alt  bigger-110 icon-only"></i></span>';
                            datos += "</td>";
                        }
                    });
                } else {
                    if (value.solo === false){
                        datos += "<td>";
                        datos += "</td>";
                    }
                }
                datos += '</tr>';
            });

            $('#tablaPeriodos tbody').html(datos);
            $('.select-modalidad').on('change', function(){
                var selectModalidad = $(this);
                var pediodo = $(this).attr("periodo");
                $.ajax({
                    data: 'modalidad=' + $(this).val() + '&cod_plan_academico=' + $("#cod_plan_academico").val() + "&periodo=" + $(this).attr("periodo"),
                    url: BASE_URL + "aspirantes/listarComisiones",
                    type: 'POST',
                    dataType: 'json',
                    cache: false,
                    async: false,
                    success: function(respuesta) {
                        var select = "";
                        var placeHolder = respuesta.lenght === 0 ? langFRM.no_hay_comisiones_disponibles : langFRM.seleccione_comision;
                        var disabled = respuesta.lenght === 0 ? "disabled" : "";
                        select += '<select class="" name="periodos[' + pediodo + '][comision]" id="comisiones" data-placeholder="' + placeHolder + '"  data-content="" ' + disabled + '>';
                        select += "<option></option>";
                        $(respuesta).each(function(k, comisiones) {
                            select += "<option tiene_horarios='" + comisiones.tiene_horarios + "'value='" + comisiones.codigo + "'>" + comisiones.nombre + "</option>";
                        });
                        select += "</select>";
                        select += '&nbsp <span class="btn btn-warning btn-xs hide" id="pop-horarios" data-rel="popover" title="' + langFRM.frm_nuevaMatricula_HorariosDeCursado + '" ><i class="icon-bell-alt  bigger-110 icon-only"></i></span>';
                        selectModalidad.parent().next().html(select);
                    }
                });
                $('#tablaPeriodos tbody select').chosen({width: '80%'});
            });
            $('#tablaPeriodos tbody select').chosen({width: '80%'});
            $.fancybox.update();
        }

        function getPlanesPago(){
            $.ajax({
                url: BASE_URL + 'aspirantes/listarPlan',
                type: 'POST',
                data: $("#presupuesto").serialize(),
                dataType: 'json',
                cache: false,
                success: function(respuesta) {
                    if (respuesta.length > 0) {
                        actualizarSelect('plan', respuesta);
                    } else {
                        gritter(langFRM.no_hay_planes_de_pago_definidos_para_esta_seleccion, false, '');
                        $("#financiacion").empty();
                        $("[name=plan]").empty();
                        $("[name=plan]").trigger("chosen:updated");
                    }
                }
            });
        }

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

                case 'plan':
                    if (option.length > 1) {
                        $('select[name="' + name + '"]').append('<option value=""></option>');
                    }
                    if (option.length !== 0) {
                        $(option).each(function() {
                            $('select[name="' + name + '"]').append('<option value="' + this.codigo + '">' + this.nombre + '</option>');
                        });
                        $('select[name="' + name + '"]').attr('disabled', false);
                        $('select[name="' + name + '"]').attr("data-placeholder", langFRM.SELECCIONE_UNA_OPCION);
                        if (option.length < 2) {
                            $('select[name="' + name + '"]').change();
                        }
                    } else {
                        $('select[name="' + name + '"]').attr('disabled', true);
                        $('select[name="' + name + '"]').attr("data-placeholder", langFRM.NO_HAY_PLANES);
                    }
                    $("#esquema").html("");
                    $("#ver-plan").addClass("hide");
                    break

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

        $("#obs").click(function() {
            if ($("textarea[name='observaciones']").hasClass('hide')) {
                $("textarea[name='observaciones']").removeClass('hide');
            } else {
                $("textarea[name='observaciones']").addClass('hide');
            }
        });
        
        $('select[name="cursos"]').on('change', function() {
            var cod_plan_academico = $(this).val();
            $.ajax({
                data: 'cod_plan_academico=' + cod_plan_academico,
                url: BASE_URL + "aspirantes/getPeriodosCurso",
                type: 'POST',
                dataType: 'json',
                cache: false,
                success: function(respuesta) {
                    cargarTablaPeriodos(respuesta);
                }
            });
            return false;
        });

        $('.fancybox-wrap').on('click', '.btn-link', function() {
            $("#errores").hide();
            var plan = $('select[name="plan"]').val();

            $.ajax({
                url: BASE_URL + 'aspirantes/detallesPlanPresupuesto',
                type: "POST",
                data: $('#presupuesto').serialize(),
                dataType: "JSON",
                cache: false,
                success: function(respuesta) {
                    var retorno = generarTablaFinanciacion(respuesta);
                    if (retorno !== false) {
                        $("#detalle-plan").html(retorno);
                        $('#stack1').modal('show');
                    }
                }
            });
            return false;
        });

        $('.fancybox-wrap').on('click', '.btn-success', function() {
            $('#presupuesto').submit();
            return false;
        });

        $('.fancybox-wrap').on('submit', '#presupuesto', function() {
            var imprimir = $("#imprimirPresupuesto").is(":checked");
            $.ajax({
                url: BASE_URL + 'aspirantes/guardar_presupuesto',
                type: "POST",
                data: $(this).serialize(),
                dataType: "JSON",
                cache: false,
                success: function(respuesta) {
                    $.gritter.add({
                        title: respuesta.codigo == 1 ? 'Ok!' : 'Upss',
                        text: respuesta.codigo == 1 ? langFRM.validacion_ok : respuesta.respuesta,
                        sticky: false,
                        time: '3000',
                        class_name: respuesta.codigo == 1 ? 'gritter-success' : 'gritter-error'
                    });

                    respuesta.codigo == 1 ? $.fancybox.close(true) : '';
                    if (respuesta.codigo == 1) {
                        var cod_presupuesto = respuesta.custom;
                        var param = new Array();
                        param.push(cod_presupuesto);
                        printers_jobs(1, param);
                    }
                }
            });
            return false;
        });

        $('.fancybox-wrap').on('click', '.checkperiodo', function() {
            getPlanesPago();
        });

        $('.fancybox-wrap').on('change', '#comisiones', function() {
            var optionseleccionado = $("#comisiones option:selected");
            if (optionseleccionado.attr("tiene_horarios") == 'true') {
                $("#pop-horarios").removeClass('hide');
            } else {
                $("#pop-horarios").addClass('hide');
            }
            var cod_comision = $(this).val();
            $.ajax({
                url: BASE_URL + 'aspirantes/getHorarioComision',
                type: 'POST',
                data: 'cod_comision=' + cod_comision,
                dataType: 'json',
                cache: false,
                success: function(respuesta) {
                    crearDetalle(respuesta);
                    $('.btn-link').show();
                }});
            getPlanesPago();
        });

        $('select[name="plan"]').on('change', function() {
            var idPlan_POST = $(this).val();
            $.ajax({
                url: BASE_URL + 'aspirantes/listarCuotas',
                type: 'POST',
                data: 'codigo=' + idPlan_POST,
                dataType: 'json',
                cache: false,
                success: function(respuesta) {
                    var obj = respuesta;
                    if (obj.length > 0) {
                        $("#ver-plan").removeClass("hide");
                    } else {
                        ("#ver-plan").addClass("hide");
                    }
                    var _html = '';
                    _html += '<center>';
                    _html += '<table>';
                    _html += '<tr>';
                    _html += '<td>';
                    _html += '<table class="table table-striped table-bordered" id="financiacion">';
                    _html += '<thead>';
                    _html += '<tr>';
                    _html += '<th style="width: 194px;">';
                    _html += langFRM.concepto;
                    _html += '</th>';
                    _html += '<th>';
                    _html += langFRM.financiacion;
                    _html += '</th>';
                    _html += '</tr>';
                    _html += '</thead>';
                    _html += '<tbody>';                    
                    $.each(obj, function(key, value){
                        $.each(value, function(key, value){
                            var option = '';
                            option += "<option></option>";
                            if (value.financiaciones.length < 2){
                                option += "<option></option>";
                            }
                            $.each(value.financiaciones, function (key2, row){
                                option += "<option value='" + key2 + "'>" + row.nombre + "</option>";
                            });
                            _html += '<tr>';
                            _html += '<input type="hidden" name=plan-concepto[] value="' + key + '">';
                            _html += '<td style="width: 250px;">';
                            _html += value.concepto;
                            _html += "</td>";
                            _html += '<td style="width: 400px;">';
                            _html += '<select id="codigo-financiacion-' + key + '" posicion="' + key + '" name="codigo-financiacion[]" class="select-financiaciones chosen-select" data-placeholder="' + langFRM.seleccione_financiacion + '" style="width: 350px;">';
                            _html += option;
                            _html += '</select>';
                            _html += '</td>';
                            _html += '</tr>';
                        });        
                    });
                    _html += '</tbody>';
                    _html += '</table>';
                    _html += '</td>';
                    _html += '<td style="vertical-align: bottom; padding-bottom: 14px;">';
                    _html += '<div class="input-group">';
                    _html += '<div class="btn btn-link ver-planes center">';                    
                    _html += langFRM.ver_detalle;                    
                    _html += '</div>';
                    _html += '<div>';
                    _html += '</td>';
                    _html += '</tr>';
                    _html += '</table>';
                    _html += '</center>';
                    $("#div_detalle_financiacion").html(_html);
                    $("#div_detalle_financiacion").find(".chosen-select").chosen();
                    $(".date-picker").datepicker({
                        yearRange: "1920:2014",
                        changeMonth: true,
                        changeYear: true
                    });
                    $.fancybox.reposition();
                }
            });
        });
    }
});