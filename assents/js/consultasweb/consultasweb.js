var lang;
var inlineTab = '';
var instancia = '';
var cod_template_respuesta_libre = 9; // es el id del template
var btnGuardarFunctionOverrided = null;

function enviarTemplate() {
    $("#btnCancelar").attr("disabled", true);
    $("#btnAnterior").attr("disabled", true);
    $("#btnSiguiente").attr("disabled", true);
    var cod_consulta = $("#codigo_consulta_responder").val();
    var arrTemplates = $("[name=select_templates]");
    var templates = new Array();
    for (var i = 0; i < arrTemplates.length; i++) {
        templates.push(arrTemplates[i].value);
    }
    $.ajax({
        url: BASE_URL + 'consultasweb/guardar_respuesta_template',
        type: 'POST',
        dataType: "json",
        data: {
            cod_consulta: cod_consulta,
            templates: templates
        },
        success: function (_json) {
            if (_json.success) {
                $.gritter.add({
                    title: lang.ok,
                    text: lang.validacion_ok,
                    sticky: false,
                    time: '3000',
                    class_name: 'gritter-success'
                });
            } else {
                $.gritter.add({
                    title: lang.upps,
                    text: lang.ocurrio_error,
                    sticky: false,
                    time: '3000',
                    class_name: 'gritter-error'
                });
            }
            cargarRespuestas(cod_consulta);
        }
    });
}

function mostrarVistaPrevia() {
    /*
     $('#BTNGuardar').html('<i class="icon-ok"></i>' + lang.enviar).attr('onclick', 'enviarTemplate();');
     $('#BTNGuardar').unbind('click');
     $('#BTNAnterior').attr('onclick', 'responderConsultaSeleccionaTemplates();');
     $("#area_de_notificacion").html('');
     */

    $("#area_de_notificacion").html('');
    $("#area_de_notificacion").hide();

    var INPUTTEXTREQUIRED = $("[name=INPUTTEXTREQUIRED]");
    var INPUTTEXTCALENDARREQUIRED = $("[name=INPUTTEXTCALENDARREQUIRED]");
    var SELECT = $("[name=SELECT]");
    var TEXTAREAREQUIRED = $("[name=TEXTAREAREQUIRED]");
    var TEXTAREA = $("[name=TEXTAREA]");
    var INPUTTEXT = $("[name=INPUTTEXT]");

    var INPUTTEXTREQUIREDVALUE = new Array();
    var INPUTTEXTCALENDARREQUIREDVALUE = new Array();
    var SELECTVALUE = new Array();
    var TEXTAREAREQUIREDVALUE = new Array();
    var TEXTAREAVALUE = new Array();
    var INPUTTEXTVALUE = new Array();
    var DESCUENTOSVALUE = new Array();
    var CUOTASVALUE = new Array();

    var falta_parametro = false;
    var template_errors = [];

    for (var i = 0; i < INPUTTEXTREQUIRED.length; i++) {
        var id = INPUTTEXTREQUIRED[i].id;
        var temp = id.split("_");
        var template = temp[1];
        var numero_campo = temp[2];
        var value = trim(INPUTTEXTREQUIRED[i].value);
        if (value == '') {
            falta_parametro = true;
        } else {
            INPUTTEXTREQUIREDVALUE.push({
                template: template,
                numero_campo: numero_campo,
                value: value
            });
        }
    }

    // Obtenemos los descuentos para enviarlos via POST.
    $('#div_responder_consulta td[id ^= descuentos_template_]').each(function () {
        current_template_id = $(this).attr('id');
        current_template_id = current_template_id.split('_');
        current_template_id = current_template_id[2];

        presupuesto_vencimiento = $(this).find('input[name=fecha_limite_precupuesto]').last().val();

        descuento_matricula_activado = $(this).find('.checkbox_descuento_matricula').get(0).checked;
        descuento_matricula_porcentaje = $(this).find('input[name=descuento_porcentaje_matricula]').last().val();
        descuento_matricula_vencimiento = $(this).find('input[name=fecha_vencimiento_descuento_matricula]').last().val();

        descuento_curso_activado = $(this).find('.checkbox_descuento_curso').get(0).checked;
        descuento_curso_porcentaje = $(this).find('input[name=descuento_porcentaje_curso]').last().val();
        descuento_curso_vencimiento = $(this).find('input[name=fecha_vencimiento_descuento_curso]').last().val();

        if (
            (descuento_matricula_activado && (descuento_matricula_porcentaje.length == 0 || descuento_matricula_vencimiento.length == 0)) ||
            (descuento_curso_activado && (descuento_curso_porcentaje.length == 0 || descuento_curso_vencimiento.length == 0))
        ) {
            falta_parametro = true;
        }
        else
        {
            if (
                (!descuento_matricula_activado && !descuento_curso_activado) &&
                (presupuesto_vencimiento.length <= 0)
            ) {
                falta_parametro = true;
            }
        }

        descuentos_object = {
            presupuesto: {
                vencimiento: presupuesto_vencimiento
            },
            matricula: {
                activado: descuento_matricula_activado,
                porcentaje: descuento_matricula_porcentaje,
                vencimiento: descuento_matricula_vencimiento
            },
            curso: {
                activado: descuento_curso_activado,
                porcentaje: descuento_curso_porcentaje,
                vencimiento: descuento_curso_vencimiento
            }
        };

        DESCUENTOSVALUE.push({
            template: current_template_id,
            numero_campo: "0",
            value: JSON.stringify(descuentos_object)
        });
    });

    // Obtenemos los planes de pago para enviarlos via POST.
    $('#div_responder_consulta span[id ^= cuotas_container_]').each(function () {
        current_template_id = $(this).attr('id');
        current_template_id = current_template_id.split('_');
        current_template_id = current_template_id[2];

        planes_pagos = [];

        $(this).children('.plan_de_pagos_container').each(function () {
            datos_plan_pagos = [];

            titulo_plan_pagos = $(this).find('.titulo_plan_pagos').first().val();

            datos_plan_pagos = {
                TITULO: titulo_plan_pagos,
                CUOTAS: []
            };

            $(this).children('.cuotas_plan_container').children('.cuotas').each(function () {

                current_cantidad_cuotas = $(this).children('input[name=CANTIDADDECUOTAS]').val();
                current_precio_cuotas = $(this).children('input[name=PRECIODECUOTAS]').val();
                observacion = $(this).children('input[name=OBSERVACION]').val();

                if (current_cantidad_cuotas === '' ||
                    current_precio_cuotas === ''
                ) {
                    falta_parametro = true;
                }

                datos_plan_pagos.CUOTAS.push({
                    CANTIDADDECUOTAS: current_cantidad_cuotas,
                    PRECIODECUOTAS: current_precio_cuotas,
                    OBSERVACION: observacion
                });
            });

            planes_pagos.push(datos_plan_pagos);
        });

        CUOTASVALUE.push({
            template: current_template_id,
            numero_campo: "0",
            value: JSON.stringify(planes_pagos)
        });
    });

    for (var i = 0; i < INPUTTEXTCALENDARREQUIRED.length; i++) {
        var id = INPUTTEXTCALENDARREQUIRED[i].id;
        var temp = id.split("_");
        var template = temp[1];
        var numero_campo = temp[2];
        var value = trim(INPUTTEXTCALENDARREQUIRED[i].value);
        if (value == '') {
            falta_parametro = true;
        } else {
            INPUTTEXTCALENDARREQUIREDVALUE.push({
                template: template,
                numero_campo: numero_campo,
                value: value
            });
        }
    }

    for (var i = 0; i < TEXTAREAREQUIRED.length; i++) {
        var id = TEXTAREAREQUIRED[i].id;
        var temp = id.split("_");
        var template = temp[1];
        var numero_campo = temp[2];
        var value = trim(TEXTAREAREQUIRED[i].value);
        if (value == '') {
            falta_parametro = true;
        } else {
            TEXTAREAREQUIREDVALUE.push({
                template: template,
                numero_campo: numero_campo,
                value: value
            });
        }
    }

    for (var i = 0; i < SELECT.length; i++) {
        var id = SELECT[i].id;
        var temp = id.split("_");
        var template = temp[1];
        var numero_campo = temp[2];
        var value = trim(SELECT[i].value);
        SELECTVALUE.push({
            template: template,
            numero_campo: numero_campo,
            value: value
        });
    }

    for (var i = 0; i < TEXTAREA.length; i++) {
        var id = TEXTAREA[i].id;
        var temp = id.split("_");
        var template = temp[1];
        var numero_campo = temp[2];
        var value = trim(TEXTAREA[i].value);
        TEXTAREAVALUE.push({
            template: template,
            numero_campo: numero_campo,
            value: value
        });
    }

    for (var i = 0; i < INPUTTEXT.length; i++) {
        var id = INPUTTEXT[i].id;
        var temp = id.split("_");
        var template = temp[1];
        var numero_campo = temp[2];
        var value = trim(INPUTTEXT[i].value);
        INPUTTEXTVALUE.push({
            template: template,
            numero_campo: numero_campo,
            value: value
        });
    }

    if (falta_parametro || template_errors.length > 0) {
        $("#area_de_notificacion").html('<div class="alert alert-block alert-danger" style="margin-bottom: 0px; padding: 6px 15px">' + lang.los_campos_en_rojo_son_requeridos + '</div>');
        $("#area_de_notificacion").fadeIn('fast');
        //$("#btnCancelar").attr("disabled", false);
        //$("#btnSiguiente").attr("disabled", false);
        //$("#btnAnterior").attr("disabled", false);
    } else {
        $('#BTNGuardar').html('<i class="icon-ok"></i>' + lang.enviar).attr('onclick', 'enviarTemplate();');
        $('#BTNGuardar').unbind('click');
        $('#BTNAnterior').attr('onclick', 'responderConsultaSeleccionaTemplates();');

        var cod_consulta = $("#codigo_consulta_responder").val();
        var templates_seleccionados = $("[name=select_templates]");
        var templates = new Array();
        for (var i = 0; i < templates_seleccionados.length; i++) {
            templates.push(templates_seleccionados[i].value);
        }

        parametros_ajax = {
            INPUTTEXTREQUIRED: INPUTTEXTREQUIREDVALUE,
            INPUTTEXTCALENDARREQUIRED: INPUTTEXTCALENDARREQUIREDVALUE,
            SELECT: SELECTVALUE,
            TEXTAREAREQUIRED: TEXTAREAREQUIREDVALUE,
            TEXTAREA: TEXTAREAVALUE,
            INPUTTEXT: INPUTTEXTVALUE,
            CUOTAS: CUOTASVALUE,
            DESCUENTOS_VIGENTES: DESCUENTOSVALUE
        };

        $.ajax({
            url: BASE_URL + 'consultasweb/mostrar_vista_previa',
            type: 'POST',
            data: {
                param: parametros_ajax,
                templates: templates,
                cod_consulta: cod_consulta
            },
            success: function (_html) {
                $("#tdContenedorResponder").html(_html);
            }
        });
    }
}

// Conteo exacto de la cantidad de caracteres en un textarea, soluciona
// el problema de conteo por los dos caracteres '/r/n' para newlines
function count_total_characters(stringParam) {
    count_total = 0;
    newLines = stringParam.match(/(\r\n|\n|\r)/g);

    if (newLines != null) {
        count_total = newLines.length;
    }

    return stringParam.length + count_total;
}

/* Funcion para activar el conteo de caracteres en un textarea.
 *
 * Parametros:
 *		textAreaSelector: Selector del textarea, por ej. "#id_textarea"
 *		charactersCountResultContainer: Selector del contenedor que muestra el conteo de caracteres, por ej. "#text_input_characters_count"
 *		characters_limit: Limite de caracteres para el textarea. Por ej. 600.
 *		charactersLimitExceededCallback: Funcion que llamaria cada vez que el usuario introduzca una cantidad de caracteres superior al limite para el textarea. Por ej. function () {alert("Limite de caracteres superado.")}
 */
function textareaLimitedCharactersCounter(textAreaSelector, charactersCountResultContainer, characters_limit, onCharactersLimitExceededCallback, onCharactersLimitNotExceededCallback) {
    counting = false;
    show_warning = true;

    $(textAreaSelector).on('keyup', function () {
        if (!counting) {
            counting = true;

            characters_count = count_total_characters($(textAreaSelector).val());
            if (characters_count > characters_limit && show_warning) {
                show_warning = false;
                onCharactersLimitExceededCallback();
            }
            else
            {
                if (characters_count <= characters_limit && !show_warning) {
                    onCharactersLimitNotExceededCallback();
                    show_warning = true;
                }
            }

            $(charactersCountResultContainer).html(characters_limit - characters_count);

            counting = false;
        }
    });

    $(textAreaSelector).trigger('keyup'); // dispara el evento keyup en el input
}

// Obtiene el ultimo id para el campo del template especificado. El id del
// elemento debe tener el formato <nombre_campo>_<id_template>_<id_elmento>
function getLastIdForTemplateField(field_name) {
    last_element_id = $("."+field_name).last();

    if (last_element_id) {
        if (last_element_id[0]) {
            last_element_id = last_element_id[0].id;

            if (last_element_id.length > 0) {
                last_element_id = last_element_id.split('_');
                return parseInt(last_element_id[2]);
            }
        }
    }

    return 0;
}

function getAddCuotasButton() {
    return '<button class="button_add_cuotas btn btn-xs btn-success" style="margin-bottom: 5px;"><i class="icon-plus"></i> '+lang.agregar_cuotas+'</button>';
}

function getDeleteCuotasButton() {
    return '<button class="btn btn-xs btn-danger boton_delete_cuotas" style="margin: 0 0 5px 5px; font-size: 8px; border-radius: 15%; line-height: 1.3;"><i class="icon-minus"></i></button>';
}

function getEliminarPlanDePagosButton() {
    return '<a href="javascript:;" class="delete_plan_pagos_button badge badge-danger" style="float: right; margin-bottom: 15px;">'+lang.eliminar_plan_de_pagos+'</a>';
}

function getAgregarPlanDePagosButton() {
    return '<button class="add_plan_pagos_button btn btn-xs btn-danger" style="margin-bottom: 5px;"><i class="icon-plus"></i> '+lang.agregar_plan_de_pagos+'</button>';
}

// Agrega inputs de cuotas a un plan de pagos.
function addInputCuotas(sender) {
    cuotas_plan_container = sender.parent().children('.cuotas_plan_container').last();
    input_copy = cuotas_plan_container.children("span.cuotas").last().clone();

    if (input_copy.find('.boton_delete_cuotas').length == 0) {
        input_copy.append(getDeleteCuotasButton());
    }

    cuotas_plan_container.append(input_copy);
}

// Inicializa el formulario de planes de pago para un template
function initFormCuotasForTemplate(template_id) {
    template_form_container = $('#cuotas_container_' + template_id);

    // Modificamos el css para la edicion de planes de pago.
    template_form_container.css('width', '100%');
    template_form_container.css('text-align', 'center');

    // Agregamos los botones para agregar cuotas a los planes de pagos.
    template_form_container.find( ".cuotas_plan_container" ).after(getAddCuotasButton());

    // Recorremos todos contenedores de cuotas de los planes de pagos.
    //$('.cuotas_plan_container').each(function (index_container) {
    template_form_container.find('.cuotas_plan_container').each(function (index_container) {
        // Agregamos los botones para eliminar planes de pagos.
        if (index_container !== 0) {
            $(this).before(getEliminarPlanDePagosButton());
        }

        // Agregamos los botones para eliminar cuotas del plan de pagos.
        $(this).children('.cuotas').each(function (index_cuota) {
            if (index_cuota !== 0) {
                $(this).append(getDeleteCuotasButton());
            }
        });
    });

    // Creamos el boton para agregar planes de pagos.
    template_form_container.find('.plan_de_pagos_container').last().after(getAgregarPlanDePagosButton());

    // Evento click en los botones para agregar cuotas a los planes de pagos.
    template_form_container.on("click", ".button_add_cuotas", function () {
        addInputCuotas($(this));
    });

    // Evento click en los botones para eliminar cuotas.
    template_form_container.on("click", ".boton_delete_cuotas", function () {
        $(this).parent().remove();
    });

    // Evento click en los botones para eliminar planes de pagos.
    template_form_container.on("click", ".delete_plan_pagos_button", function () {
        plan_de_pagos = $(this).parent();

        plan_de_pagos.fadeOut('fast', function () {
            plan_de_pagos.remove();
        });
    });

    // Evento click en el boton para agregar un plan de pagos.
    template_form_container.on("click", ".add_plan_pagos_button", function () {
        last_plan_pagos = $(this).parent().children('.plan_de_pagos_container').last();
        last_plan_pagos_copy = last_plan_pagos.clone();

        if (last_plan_pagos_copy.find('.delete_plan_pagos_button').length === 0) {
            last_plan_pagos_copy.prepend(getEliminarPlanDePagosButton());
        }

        last_plan_pagos_copy.hide();

        last_plan_pagos.after(last_plan_pagos_copy);
        last_plan_pagos_copy.fadeIn();
    });
}

// Verifica si hay al menos un checkbox marcado, si no es asi, habilita la fecha
// de vencimiento del presupuesto.
function estadoCheckboxesDescuentos(template_id) {
    form_descuentos_container = $('#descuentos_template_'+template_id);

    none_checked = true;
    form_descuentos_container.find('.checkbox_descuento').each(function () {
        none_checked = none_checked && !this.checked;
    });

    form_descuentos_container.find('input[name=fecha_limite_precupuesto]').last().prop('disabled', !none_checked);
}

// Inicializa los campos de descuentos del formulario para un template
function initFormDescuentos(template_id) {
    form_descuentos_container_selector = '#descuentos_template_'+template_id;

    $(form_descuentos_container_selector + ' .checkbox_descuento_matricula').click(function () {
        $(this).parent().parent().find('input[name=descuento_porcentaje_matricula]').last().prop('disabled', !this.checked);
        $(this).parent().parent().find('input[name=fecha_vencimiento_descuento_matricula]').last().prop('disabled', !this.checked);

        estadoCheckboxesDescuentos(template_id);
    });

    $(form_descuentos_container_selector + ' .checkbox_descuento_curso').click(function () {
        $(this).parent().parent().find('input[name=descuento_porcentaje_curso]').last().prop('disabled', !this.checked);
        $(this).parent().parent().find('input[name=fecha_vencimiento_descuento_curso]').last().prop('disabled', !this.checked);

        estadoCheckboxesDescuentos(template_id);
    });
}


function responderConsultaSeleccionaTemplates() {

    $("#area_de_notificacion").html("");
    var arrTemplates = $("[name=select_templates]");
    if (arrTemplates.length == 0) {
        var arrTemplates = $("[name=templates]:checked");
    }
    if (arrTemplates.length > 0) {
        var cod_consulta = $("#codigo_consulta_responder").val();

        //$('#BTNGuardar').html('<i class="icon-ok"></i>' + lang.siguiente).attr('onclick', 'mostrarVistaPrevia();');
        $('#BTNGuardar')
            .html('<i class="icon-ok"></i>' + lang.siguiente)
            .attr('onclick', null);

        $("#BTNGuardar").click(function () { mostrarVistaPrevia(); });

        $('#BTNAnterior').removeClass('hide').attr('onclick', 'responderConsulta(' + cod_consulta + ');');
        var templates = new Array();
        for (var i = 0; i < arrTemplates.length; i++) {
            templates.push(arrTemplates[i].value);
        }
        $.ajax({
            url: BASE_URL + "consultasweb/responder_consulta_completar_valores/",
            type: 'POST',
            data: {
                cod_consulta: cod_consulta,
                templates: templates
            },
            success: function (_html) {
                $("#tdContenedorResponder").html(_html);

                // si templates es array y contiene el codigo de template "cod_template_respuesta_libre"
                if ($.isArray(templates) && ($.inArray(cod_template_respuesta_libre.toString(), templates) > -1))
                {
                    $("#BTNGuardar").unbind('click');
                    $("#BTNGuardar").click(function () {
                        tmp_conteo_caracteres = count_total_characters($('#TEXTAREAREQUIRED_' + cod_template_respuesta_libre + '_0').val());
                        errorNotificationOptions = {
                            title: lang.template_error_titulo,
                            text: '',
                            class_name: 'gritter-error',
                            sticky: false,
                            time: 1500
                        };

                        if (tmp_conteo_caracteres > 600) {
                            errorNotificationOptions.text = lang.template_error_limite_descripcion;
                            $.gritter.add(errorNotificationOptions);
                        }
                        else
                        {
                            if (tmp_conteo_caracteres <= 0) {
                                errorNotificationOptions.text = lang.template_error_vacio_descripcion;
                                $.gritter.add(errorNotificationOptions);
                            }
                            else
                            {
                                // se hace unbind al boton, ya que este se reutiliza en el siguiente paso
                                // se desactiva para que se pueda reenviar el form en caso de errores
                                //$("#BTNGuardar").unbind("click");
                                mostrarVistaPrevia();
                            }
                        }
                    });

                    // Agregamos el badge que va a contener el conteo de caracteres del textarea
                    counter_container_html = '<div class="align-right" style="margin-bottom: 2px;">'+
                        '<span id="text_input_characters_count" class="badge badge-success"></span> '+lang.label_contador_caracteres+
                        '</div>';

                    textarea_libre_selector = '#TEXTAREAREQUIRED_' + cod_template_respuesta_libre + '_0', '#text_input_characters_count';
                    $(textarea_libre_selector).before(counter_container_html);

                    // seteamos el color inicial del borde del textarea en verde
                    $(textarea_libre_selector).css("border", "1px solid #8dd039");

                    // Inicializamos el textarea
                    textareaLimitedCharactersCounter(
                        textarea_libre_selector,
                        '#text_input_characters_count',
                        600,
                        function () {
                            $.gritter.add({
                                title: lang.template_warning_limite_titulo, // title
                                text: lang.template_warning_limite_descripcion, // description
                                sticky: false,
                                time: 1500,
                                before_open: function () {
                                    if ($('.gritter-item-wrapper').length >= 1) {
                                        return false;
                                    }
                                },
                                class_name: 'gritter-warning'
                            });

                            $(textarea_libre_selector).css("border", "1px solid #ff5c59");
                            $("#text_input_characters_count").removeClass("badge-success");
                            $("#text_input_characters_count").addClass("badge-danger");
                        },
                        function () {
                            $(textarea_libre_selector).css("border", "1px solid #8dd039");
                            $("#text_input_characters_count").removeClass("badge-danger");
                            $("#text_input_characters_count").addClass("badge-success");
                        }
                    );

                    // fin agregado
                }
            }
        });
    } else {
        $("#area_de_notificacion").html('<div class="alert alert-block alert-danger" style="margin-bottom: 0px; padding: 6px 15px">' + lang.debe_seleccionar_al_menos_un_template + '</div>');
    }
}






function buscarTemplates(e) {

    if (e.keyCode == 13) {
        var nombre_search = ($("#search_template_name").val());
        $.ajax({
            url: BASE_URL + 'consultasweb/buscar_templates',
            type: 'POST',
            dataType: 'json',
            data: {
                nombre_search: nombre_search
            },
            success: function (_json) {
                if (_json.data) {
                    $("#lista_templates").html();
                    var html = '';
                    html += '<div class="row">';
                    html += '<div class="row">';
                    $.each(_json.data, function (idx, respuesta) {
                        var nombre_campo = respuesta.nombre_mostrar;
//                        if (lang._idioma == "in")
//                            nombre_campo = respuesta.nombre_in;
//                        else if (lang._idioma == "pt")
//                            nombre_campo = respuesta.nombre_pt;
//                        else
//                            nombre_campo = respuesta.nombre_es;
//
////                        if (nombre_campo == '')
//                            nombre_campo = respuesta.nombre;

                        html += '<div class="col-md-3">';
                        html += '<input type="checkbox" style="border: 0 white none" name="templates" value="' + respuesta.cod_template + '">&nbsp;';
                        html += nombre_campo;
                        if (idx < 5 && respuesta.cantidad > 0) {
                            html += '<i class="light-orange icon-asterisk"></i>';
                        }
                        html += '</div>';
                        if ((idx + 1) % 4 == 0) {
                            html += "</div><div class='row'>";
                        }
                    });
                    html += '</div>';
                    html += '</div>';
                    $("#lista_templates").html(html);
                } else {
                    // ver como alertar sobre error en la busqueda
                }
            }
        });
    }
    return false;
}







function cancelarResponderConsulta() {
    $("[name=botones_acciones]").show();
    $("#div_responder_consulta").hide();
}

function responderConsulta(idConsulta) {

    var templates_seleccionados = $("[name=select_templates]");
    var templates = new Array();
    for (var i = 0; i < templates_seleccionados.length; i++) {
        templates.push(templates_seleccionados[i].value);
    }
    $.ajax({
        url: BASE_URL + 'consultasweb/responder_consulta',
        type: 'POST',
        data: {
            id_consulta: idConsulta,
            templates: templates
        },
        success: function (_html) {

            //$('.vistaDetalle').removeClass('hide');
            //$('.message-footer').hide();
            //$('.detalleList').hide();

            $("[name=botones_acciones]").hide();
            $("#div_responder_consulta").html(_html);
            $("#div_responder_consulta").show();
        }
    });
}

function trim(str) {
    return str.replace(/^\s*|\s*$/g, "");
}

function cerrarFancy() {
    $.fancybox.close();
}

/* Si se la llama con un JSON como parametro, usa el JSON y de lo contrario usa
 * los campos del formulario.
 */
function guardarConsulta(consulta_values_json) {
    var asunto = '';
    var nombre_apellido = '';
    var telefono = '';
    var email = '';
    var consulta = '';
    var como_nos_conocio_codigo = '';

    var mensaje = '';

    if (typeof consulta_values_json !== 'undefined') {
        asunto = consulta_values_json.asunto;
        nombre_apellido = consulta_values_json.nombre_apellido;
        telefono = consulta_values_json.telefono;
        email = consulta_values_json.email;
        consulta = consulta_values_json.consulta;
        como_nos_conocio_codigo = consulta_values_json.como_nos_conocio_codigo;
    }
    else {
        asunto = $("#asunto").val();
        nombre_apellido = trim($("#nombre_apellido").val());
        telefono = trim($("#telefono").val());
        email = trim($("#email").val());
        consulta = trim($("#consulta").val());
        como_nos_conocio_codigo = $("#como_nos_conocio_codigo").val();
    }

    if (asunto == -1)
        mensaje += lang.debe_especificar_el_asunto_del_mensaje + "\n";
    if (nombre_apellido == '')
        mensaje += lang.debe_especificar_el_nombre_y_apellido + "\n";
    if (email == '' || !validarEmail(email))
        mensaje += lang.debe_indicar_un_email_valido + "\n";
    if (consulta.length < 15)
        mensaje += lang.debe_especificar_la_consulta + " (15 " + lang.caracteres_minimo + ")\n";
    if (como_nos_conocio_codigo == -1)
        mensaje += lang.debe_especificar_como_nos_conocio + "\n";

    if (mensaje != '') {
        alert(mensaje);
    } else {
        $.ajax({
            url: BASE_URL + 'consultasweb/guardar_consulta',
            type: 'POST',
            dataType: 'json',
            data: {
                asunto: asunto,
                nombre_apellido: nombre_apellido,
                telefono: telefono,
                email: email,
                consulta: consulta,
                como_nos_conocio_codigo: como_nos_conocio_codigo
            },
            success: function (_json) {
                if (_json.error) {
                    $.gritter.add({
                        title: lang.upps,
                        text: lang.ocurrio_error,
                        sticky: false,
                        time: '3000',
                        class_name: 'gritter-error'
                    });
                } else {
                    $.gritter.add({
                        title: lang.ok,
                        text: lang.validacion_ok,
                        sticky: false,
                        time: '3000',
                        class_name: 'gritter-success'
                    });
                    instancia.listar();

                    if (typeof consulta_values_json !== 'undefined') {
                        $.fancybox.close();
                    }
                }
            }
        });
    }
}
lang = BASE_LANG;
$(document).ready(function ()
{
    init();

});

function nuevaConsulta() { // hacer la llamada por ajax, recuperar la respuesta y copiarla al fancy
    $.ajax({
        url: BASE_URL + 'consultasweb/nueva_consulta',
        type: 'GET',
        success: function (_html) {
            $.fancybox.open(_html, {
                padding: 0,
                width: 650,
                height: 380,
                ajax: {
                    dataType: 'html',
                    headers: {'X-fancyBox': true}
                }
            });
        }
    });
}


function validarEmail(valor) {
    re = /^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,3})$/
    if (!re.exec(valor)) {
        return false;
    } else {
        return true;
    }
}



function cargarEmailInboxExterna(email_uid) {
    $("#id-message-content").html("");
    $.ajax({
        url: BASE_URL + 'consultasweb/ver_email_inbox_externa',
        type: 'POST',
        dataType: 'json',
        data: {
            email_uid: email_uid
        },
        success: function (_json) {
            if (_json.email_uid) {
                // Creamos el form consulta
                var html_form_consulta = '';
                html_form_consulta  = '<form id="form_procesar_email_consulta" style="background-color: #e4e4e4; border: 1px solid #CACACA; padding: 15px; margin-bottom: 15px; display: none;">';
                html_form_consulta +=     '<div class="form-inline">';
                html_form_consulta +=         '<div class="form-group">';
                html_form_consulta +=             '<label for="consulta_nombre">'+lang.nombre+'</label>';
                html_form_consulta +=             '<input type="text" class="form-control" id="consulta_nombre" placeholder="Hernán Melia">';
                html_form_consulta +=         '</div>';

                html_form_consulta +=         '<div class="form-group">';
                html_form_consulta +=             '<label for="consulta_email">'+lang.EMAIL+'</label>';
                html_form_consulta +=             '<input type="email" class="form-control" id="consulta_email" placeholder="hernanmelia@example.com">';
                html_form_consulta +=         '</div>';

                html_form_consulta +=         '<div class="form-group">';
                html_form_consulta +=             '<label for="consulta_phone_number">'+lang.TELEFONO+'</label>';
                html_form_consulta +=             '<input type="text" class="form-control" id="consulta_phone_number" placeholder="(0341) 47852875">';
                html_form_consulta +=         '</div>';

                html_form_consulta +=         '<div class="form-group">';
                html_form_consulta +=             '<label for="consulta_asunto">'+lang.asunto+'</label>';
                html_form_consulta +=             '<select id="consulta_asunto" class="form-control"><option value="-1">(seleccionar)</option></select>';
                //html_form_consulta +=             '<select id="consulta_asunto" class="form-control" style="width: 240px;"><option value="-1">(seleccionar)</option></select>';
                //html_form_consulta +=             '<select id="consulta_asunto" class="chosen-select form-control">';
                //html_form_consulta +=                   '<option value="-1">(seleccionar)</option>';
                //html_form_consulta +=             '</select>';
                html_form_consulta +=         '</div>';
                html_form_consulta +=     '</div>';

                html_form_consulta +=     '<div class="form-group" style="margin-top: 10px;">';
                html_form_consulta +=         '<label for="consulta_content">'+lang.consulta+'</label>';
                html_form_consulta +=         '<textarea class="form-control" rows="5" id="consulta_content"></textarea>';
                html_form_consulta +=     '</div>';

                html_form_consulta +=     '<button type="submit" class="btn btn-success">'+lang.guardar_consulta+'</button>';
                html_form_consulta += '</form>';

                // Creamos el HTML que va a mostrar contenidos del email
                var html = '';
                html += '<div class="message-header clearfix">';
                html += '<div class="pull-left" style="width: 100%">';
                html += '<span id="asuntoMensaje" class="blue bigger-125"> ' + _json.subject + '</span>';
                html += '<div class="action-buttons pull-right" name="botones_acciones">';
                html += '</div>';
                html += '<div id="div_responder_consulta" style="display: none;"></div>';

                html += '<div class="space-4"></div>';
                html += '<a href="#" id="nombreSender" class="sender"> ' + _json.from_name + ' </a>';
                html += '&nbsp;';
                html += '<i class="icon-time bigger-110 orange middle"></i>';
                html += '<span id="timeCabezera2" class="time"> ' + moment(_json.date_time).lang(lang._idioma).calendar() + '</span>';
                html += '</div>';
                html += '<div class="hr hr-double" style="width: 100%; margin-bottom: 18px; margin-top: 0px;"></div>';
                html += '<div class="message-body">';
                html += html_form_consulta;
                html += '<iframe id="message-body-iframe" style="width: 100%; border: 1px solid #CACACA; min-height: 500px; max-height: 1000px;" frameborder="0" scrolling="yes">';
                //html += _json.email_body;
                html += '</iframe>';
                html += '</div>';
                html += '</div>';
                //html += '<div class="hr hr-double" style="margin-top: 20px;"></div>';

                //html += '<div class=" message-footer message-footer-style2 clearfix">';
                //html += '<div class="pull-left"> </div>';
                html += '</div>';

                $("#id-message-content").html(html);
                                              
                $("#id-message-content").ready(function () {
                    $("#message-body-iframe").contents().find('html').html(_json.email_body)

                    /* El seteo del alto no funciona correctamente */
                    $("#message-body-iframe").ready(function () {
                        $("#message-body-iframe").height(
                            $("#message-body-iframe").contents().find("body").height()
                        );
                    });

                    $("#form_procesar_email_consulta").ready(function () {
                        $.ajax({
                            dataType: "json",
                            url: BASE_URL + 'consultasweb/get_cursos_json',
                            type: 'GET',
                            success: function (json_returned) {
                                $.each(json_returned, function (current_index, current_value) {
                                    $('<option value="'+current_value.codigo+'">'+current_value['nombre_' + lang._idioma]+'</option>').appendTo("#consulta_asunto");
                                });

                                //$("#consulta_asunto").chosen();
                            }
                        });

                        $("#form_procesar_email_consulta").submit( function(event) {
                            var form_nueva_consulta_values = {
                                asunto: $("select#consulta_asunto").val(),
                                nombre_apellido: $("input#consulta_nombre").val(),
                                email: $("input#consulta_email").val(),
                                telefono: $("input#consulta_phone_number").val(),
                                consulta: $("textarea#consulta_content").val()
                            };

                            guardarConsulta(form_nueva_consulta_values);

                            event.preventDefault();
                        });

                        if (_json.from_name && _json.from_name !== '') {
                            $("input#consulta_nombre").val(_json.from_name);
                        }

                        if (_json.from_account && _json.from_account !== '') {
                            $("input#consulta_email").val(_json.from_account);
                        }
                    });
                });

                marcarLeidoInboxExterna(_json.email_uid);

                $('.vistaDetalle').removeClass('hide');

                // billete como oculta y muestra
                $('.vistaDetalle').find('#menuMover').hide();
                $('.vistaDetalle').find('.message-toolbar').show();
                $('.vistaDetalle').find('#inbox_externa_buttons').show();
                $('.vistaDetalle').find('a[href="eliminado"]').show();

                if (inlineTab == 'cerradas') {
                    $('.vistaDetalle').find('#menuMover').hide();
                }
                if (inlineTab == 'eliminado') {
                    $('.vistaDetalle').find('a[href="eliminado"]').hide();
                }

                $('#' + instancia.id).find('.detalleList').hide();
            } else
            {
                $("#id-message-content").html(lang.error_al_recuperar_registros); // ver como informar correctamente el error
            }
        }
    });
}

function cargarRespuestas(codConsulta) {
    $("#id-message-content").html("");
    $.ajax({
        url: BASE_URL + 'consultasweb/ver_consulta',
        type: 'POST',
        dataType: 'json',
        data: {
            cod_consulta: codConsulta
        },
        success: function (_json) {
            if (_json.codigo) {
                var html = '';
                html += '<div class="message-header clearfix">';
                html += '<div class="pull-left" style="width: 100%">';
                html += '<span id="asuntoMensaje" class="blue bigger-125"> ' + _json.asunto + '</span>';
                html += '<br><span class="blue">'+lang.como_nos_conocio+': ' + _json.como_nos_conocio + '</span>';
                html += '<div class="action-buttons pull-right" name="botones_acciones">';
                html += '<a href="responder" class="responder">';
                html += '<i class="icon-mail-forward blue icon-only bigger-130"></i>';
                html += '</a>';
                html += '<a href="eliminada" class="eliminar borrarListdesdeDetalle">';
                html += '<i class="icon-trash red icon-only bigger-130"></i>';
                html += '</a>';
                html += '</div>';
                html += '<div id="div_responder_consulta" style="display: none;"></div>';

                $.each(_json.data, function (index, respuesta) {
                    html += '<div class="space-4"></div>';
                    html += '<a href="#" id="nombreSender" class="sender"> ' + respuesta.nombre_contacto + ' </a>';
                    html += '&nbsp;';
                    html += '<i class="icon-time bigger-110 orange middle"></i>';
                    html += '<span id="timeCabezera2" class="time"> ' + moment(respuesta.fecha_hora).lang(lang._idioma).calendar() + '</span>';
                    html += '</div>';
                    html += '<div class="hr hr-double" style="width: 100%; margin-bottom: 18px; margin-top: 0px;"></div>';
                    html += '<div class="message-body">';
                    html += respuesta.html_respuesta;
                    html += '</div>';
                    html += '</div>';
                    html += '<div class="hr hr-double" style="margin-top: 20px;"></div>';
                });

                html += '<div class=" message-footer message-footer-style2 clearfix">';
                html += '<div class="pull-left"> </div>';
                html += '</div>';

                $("#id-message-content").html(html);
                marcarComoLeida(_json.codigo);
                $('.vistaDetalle').removeClass('hide');
                $('.vistaDetalle').find('#inbox_externa_buttons').hide();
                $('.vistaDetalle').find('#menuMover').show();
                $('.vistaDetalle').find('a[href="eliminado"]').show();

                if (inlineTab == 'cerradas') {
                    $('.vistaDetalle').find('#menuMover').hide();
                }
                if (inlineTab == 'eliminado') {
                    $('.vistaDetalle').find('a[href="eliminado"]').hide();
                }
                $('#' + inlineTab).find('.detalleList').hide();
            } else
            {
                $("#id-message-content").html(lang.error_al_recuperar_registros); // ver como informar correctamente el error
            }
        }
    });
}

function marcarComoLeida(codConsulta) {
    $.ajax({
        url: BASE_URL + 'consultasweb/marcar_leida',
        type: 'POST',
        dataType: 'json',
        data: {
            cod_consulta: codConsulta
        },
        success: function (_json) {
            if (_json.error) {
                // ver como alertar marcado como visto fallido
            } else {
                clearTimeout(myVarTimeExecute);
                actualizarAlertas();
            }
        }
    });
}

function marcarLeidoInboxExterna(uid_email) {
    $.ajax({
        url: BASE_URL + 'consultasweb/marcar_leido_inbox_externa',
        type: 'POST',
        dataType: 'json',
        data: {
            uid_email: uid_email
        },
        success: function (_json) {
            if (_json.error) {
                // ver como alertar marcado como visto fallido
            } else {
                clearTimeout(myVarTimeExecute);
                actualizarAlertas();
            }
        }
    });
}

function marcarLeidosInboxExterna(uids_emails) {
    if (typeof uids_emails === 'undefined') {
        var uids_emails = new Array();

        $('.message-item.selected').each( function () {
            uids_emails.push($(this).attr('value'));
        });
    }

    $.ajax({
        url: BASE_URL + 'consultasweb/marcar_leidos_inbox_externa',
        type: 'POST',
        dataType: 'json',
        data: {
            uids_emails: uids_emails
        },
        success: function (_json) {
            //instancia.listar();

            $('.message-item.selected').each( function () {
                $(this).removeClass('message-unread');
            });

            $('#' + instancia.id + ' .message-toolbar').addClass('hide');
            $('#' + instancia.id + ' #id-message-list-navbar .message-infobar').removeClass('hide');

            if (_json.error) {
                // ver como alertar marcado como visto fallido
            } else {
                clearTimeout(myVarTimeExecute);
                actualizarAlertas();
            }
        }
    });
}

/*
 function marcarOcultosInboxExterna(uids_emails) {
 if (typeof uids_emails === 'undefined') {
 var uids_emails = new Array();
 }

 $.ajax({
 url: BASE_URL + 'consultasweb/marcar_leido_inbox_externa',
 type: 'POST',
 dataType: 'json',
 data: {
 uid_email: uid_email
 },
 success: function (_json) {
 if (_json.error) {
 // ver como alertar marcado como visto fallido
 } else {
 clearTimeout(myVarTimeExecute);
 actualizarAlertas();
 }
 }
 });
 }
 */

$(function() {
    $(".date-picker").datepicker();

    $("[name=container_menu_filters_temp]").remove();

    $("[name=table_filters]").on("click", function(){
        $("[name=contenedorPrincipal]").toggle();
        $("[name=div_table_filters]").toggle(300);
        return false;
    });

    $("[name=contenedorPrincipal]").on("mousedown", function() {
        $("[name=contenedorPrincipal]").hide();
        $("[name=div_table_filters]").hide(300);
        return false;
    });
    
    $(".select_chosen").chosen({width: "100%"});
    
    $('.nexPage').on('click', function () {
        if ($(this).attr('class') == "nexPage disabled") {
            return false;
        }
        ;
        instancia.page += 10;
        instancia.numerador++;
        instancia.listar();
        return false;
    });

    $('.prevPage').on('click', function () {
        if ($(this).attr('class') == "prevPage disabled") {
            return false;
        }
        ;
        instancia.page -= 10;
        instancia.numerador--;
        instancia.listar();
        return false;
    });

    $('.lastPage').on('click', function () {
        if ($(this).attr('class') == "lastPage disabled") {
            return false;
        }
        ;

        instancia.numerador = Math.ceil(instancia.totalRecords / 10) - 1;
        instancia.listar();
        return false;
    });

    $('.firthPage').on('click', function () {
        if ($(this).attr('class') == "firthPage disabled") {
            return false;
        }
        ;
        instancia.numerador = 0;
        instancia.listar();
        return false;
    });

    $('.order-by').click(function () {
        $('.order-by .icon-chevron-up').remove();
        $('.order-by .icon-chevron-down').remove();
        $('.order-active').removeClass('order-active');
        $(this).addClass('order-active');

        if($(this).data('order') == 'asc' || $(this).data('order') == '') {
            $(this).data('order', 'desc');
            $(this).append('<i class="icon-chevron-down"></i>');
        }
        else {
            $(this).data('order', 'asc');
            $(this).append('<i class="icon-chevron-up"></i>');
        }
        instancia.listar();
    });
});

function init() {
    var prevTab = '';
    var idAsuntoDetalle = '';
    var fecha = new Date();
    $('.messagebar-item-right, .page-header').hide();
    $("[name=contenedorPrincipal]").hide();
    $("[name=div_table_filters]").hide(300);

    function Tab(id) {
        this.id = id;
        this.search = '';

        this.FechaDesde = '';
        this.fecha_hasta = '';
        this.curso = '';

        this.selector = '#' + this.id;
        this.numerador = 0;
        this.cRegistros = 0;
        this.page = 0;


        Tab.prototype.listar = function () {
            var tab = this.selector;
            var a = instancia.numerador * 10 + 10;
            var noLeidos = '';
            $(this.selector + ' .detalleList').show();
            $('.vistaDetalle').addClass('hide');
            $(tab + ' .message-container').append('<div id="loading-spinner-overlay" class="message-loading-overlay"><i class="icon-spin icon-spinner orange2 bigger-160"></i></div>');
            if (instancia.numerador > 0) {
                // estamos + de le primer pagina
                $(tab + ' .message-footer').find('.nexPage').closest('li').removeClass('disabled');
                $(tab + ' .message-footer').find('.prevPage').closest('li').removeClass('disabled');
                $(tab + ' .message-footer').find('.firthPage').closest('li').removeClass('disabled');
                $(tab + ' .message-footer').find('.lastPage').closest('li').removeClass('disabled');
            } else {
                // estamos en la  primer pagina
                $(tab + ' .message-footer').find('.nexPage').closest('li').removeClass('disabled');
                $(tab + ' .message-footer').find('.lastPage').closest('li').removeClass('disabled');
                $(tab + ' .message-footer').find('.prevPage').closest('li').addClass('disabled');
                $(tab + ' .message-footer').find('.firthPage').closest('li').addClass('disabled');
            }
            var iDisplayStart = this.numerador == 0 ? '' : this.numerador * 10;

            var idConsulta = this.id == 'eliminado' ? 'eliminadas' : this.id;

            var Estado      = $("[name=filtro_estado]").val();
            var Curso       = $("[name=filtro_curso]").val();
            var Fecha_Desde = $("[name=FechaDesde]").val();
            var Fecha_Hasta = $("[name=FechaHasta]").val();
            var Fecha_Desde_Consulta = $("[name=FechaDesdeConsulta]").val();
            var Fecha_Hasta_Consulta = $("[name=FechaHastaConsulta]").val();
            var orderField = $('.order-active').data('field') == undefined?'':$('.order-active').data('field');
            var order = $('.order-active').data('order') == undefined?'':$('.order-active').data('order');

            var dataPOST = 'tipoConsulta='   + idConsulta    +
                '&iDisplayStart=' + iDisplayStart +
                '&sSearch='       + this.search   +
                '&Estado='        + Estado        +
                '&Curso='         + Curso         +
                '&FechaDesde='    + Fecha_Desde   +
                '&FechaHasta='    + Fecha_Hasta   +
                '&FechaDesdeConsulta='    + Fecha_Desde_Consulta   +
                '&FechaHastaConsulta='    + Fecha_Hasta_Consulta   +
                '&orderField='    + orderField   +
                '&order='    + order;

            $.ajax({
                url: BASE_URL + 'consultasweb/listar',
                type: "POST",
                data: dataPOST,
                dataType: 'JSON',
                cache: false,
                async: true,
                success: function (respuesta) {

                    $(tab + ' .message-list').empty();

                    Tab.prototype.totalRecords = respuesta.iTotalRecords;
                    if (instancia.numerador + 1 == Math.ceil(respuesta.iTotalRecords / 10)) {
                        $(tab + ' .message-footer').find('.nexPage').closest('li').addClass('disabled');
                        $(tab + ' .message-footer').find('.lastPage').closest('li').addClass('disabled');
                    }

                    var cantRegistros = respuesta.aaData.length;

                    if (respuesta.aaData.length == 0) {
                        $(tab + ' .message-infobar .grey').html('(' + lang.no_tiene_mensajes + ')');
                    } else {
                        $(respuesta.aaData).each(function (k, consulta) {
                            noLeidos = consulta.noLeidos;
                            var estado = '';
                            if (instancia.id == 'cerradas') {
                                estado = consulta.estado == 'cerrado' ? '<span class="label label-success arrowed">' + lang.concretadas + '</span>' : '<span class="label label-info arrowed">' + lang.no_concretadas + '</span>';
                            }
                            //var leido = consulta.notificar == 1 ? 'message-unread' : '';

                            var parametros_row = {
                                notificar: null,
                                codigo: null,
                                leido: null,
                                nombre: null,
                                fechahora: null,
                                asunto: null
                            }

                            if (instancia.id === 'inbox_externa') {
                                //notificar = 0;
                                parametros_row.notificar = 0;
                                parametros_row.codigo = consulta.email_uid;
                                parametros_row.leido = (consulta.readed == 0) ? 'message-unread' : '';
                                parametros_row.nombre = consulta.from_name;
                                parametros_row.fechahora = consulta.date_time;
                                parametros_row.fechahoraconsulta = consulta.date_time;
                                parametros_row.asunto = consulta.subject;
                                parametros_row.oculto = consulta.hidden;
                            }
                            else
                            {
                                parametros_row.notificar = consulta.notificar;
                                parametros_row.codigo = consulta.codigo;
                                parametros_row.leido = (consulta.notificar == 1) ? 'message-unread' : '';
                                parametros_row.nombre = consulta.nombre;
                                parametros_row.fechahora = consulta.fechahora;
                                parametros_row.fechahoraconsulta = consulta.fechahoraconsulta;
                                parametros_row.asunto = consulta.asunto;
                                parametros_row.cantidad = consulta.cantidad_respuestas
                            }


                            var estrellaLeido = consulta.destacar == 1 ? 'icon-star  orange2' : ' icon-star-empty light-grey';
                            var fila = '<div class="message-item ' + parametros_row.leido + '" data-estado="' + parametros_row.notificar + '" value="' + parametros_row.codigo + '">';
                            fila += '<label class="inline">';
                            fila += ' <input name="idAsunto[]" type="checkbox" class="ace" value="' + parametros_row.codigo + '">';
                            fila += '<span class="lbl"></span>';
                            fila += '</label>';

                            if (instancia.id !== 'inbox_externa') {
                                fila += '<i class="message-star ' + estrellaLeido + '"></i>';
                            }

                            fila += '<span class="sender" title="' + parametros_row.nombre + '">' + parametros_row.nombre + '</span>';

                            if (instancia.id === 'inbox_externa') {
                                if (parametros_row.oculto == 1) {
                                    fila += '<i class="light-grey icon-eye-slash icon-only"></i>';
                                }
                            }
                            else {
                                if (parametros_row.cantidad > 0) {
                                    fila += '<i class="purple icon-reply icon-only"></i>';
                                }
                                else {
                                    fila += '<i style="width: 13px" class="purple icon-only"></i>';
                                }
                            }
                            fila += '<span class="summary">';

                            fila += '<span class="text">';
                            fila += parametros_row.asunto;

                            fila += '</span>';
                            fila += '</span>' + estado;
                            fila += '<span class="fecha-consulta" title="' + moment(parametros_row.fechahoraconsulta).lang('es').format('LLL') + '">' + moment(parametros_row.fechahoraconsulta).lang(lang._idioma).calendar() + '</span>';
                            if(parametros_row.cantidad == 0) {
                                fila += '<span class="time" style="float: inherit;">'+lang.no_respondida+'</span>';
                            }
                            else {
                                fila += '<span class="time" style="float: inherit;" title="' + moment(parametros_row.fechahora).lang('es').format('LLL') + '">' + moment(parametros_row.fechahora).lang(lang._idioma).calendar() + '</span>';
                            }
                            fila += '</div>';
                            $(tab + ' .message-list').append(fila);
                            cantRegistros > 10 ? '' : 'disabled';
                        });

                        if (instancia.id === 'inbox_externa') {
                            $('.sender').css('margin-left', '10px');
                        }

                        $(tab + ' .message-infobar .grey').html('(' + noLeidos + ' ' + lang.no_leidos + ')');
                    }

                    if (instancia.id === 'inbox_externa') {
                        if (respuesta.noInboxPassword) {
                            enable_inbox_html = '<div id="activar-inbox-externa-container" class="message-loading-overlay">';
                            enable_inbox_html += '<div style="background-color: #FFF; border: 1px solid #e5e5e5; display: inline-block; opacity: 1; margin-top: 40px; padding: 30px; max-width: 500px;">';
                            enable_inbox_html += '<h2 style="margin: 0 10px 20px 10px;">' + lang.form_password_inbox_externa_titulo + '</h2>';
                            //enable_inbox_html += '<p class="alert alert-warning">Para habilitar la característica <strong>inbox externa</strong>, debe ingresar la contraseña de la cuenta de correo <strong style="text-decoration: underline;">ejemplo@ejemplo.com</strong> por única vez.</p>';
                            enable_inbox_html += '<p class="alert alert-warning">' + lang.form_password_inbox_externa_mensaje_parte_1 + '<strong>' + lang.inbox_externa + '</strong>' + lang.form_password_inbox_externa_mensaje_parte_2 + '<strong style="text-decoration: underline;">' + respuesta.email_filial + '</strong>' + lang.form_password_inbox_externa_mensaje_parte_3 + '</p>';
                            enable_inbox_html += '<form id="set-inbox-password" class="form-inline">';
                            enable_inbox_html += '<input style="text-align: center; font-size: 20px;" type="password" class="input-large" name="inbox-externa-password" placeholder="' + lang.password + '"></input> ';
                            enable_inbox_html += '<br />';
                            enable_inbox_html += '<button type="button" id="button-submit-inbox-password" class="btn btn-sm btn-primary" name="guardar-password-inbox" style="margin-top: 7px;"><i class="icon-ok"></i> ' + lang.enviar_contrasenia + '</button>';
                            enable_inbox_html += '</form>';
                            enable_inbox_html += '</div>';
                            enable_inbox_html += '</div>';

                            $(tab + ' .message-container').append(enable_inbox_html);

                            $("#set-inbox-password").ready(function () {
                                $("#button-submit-inbox-password").unbind('click');
                                $("#button-submit-inbox-password").click(function () {
                                    $.ajax({
                                        url: BASE_URL + 'consultasweb/setInboxExternaPassword',
                                        type: "POST",
                                        data: {inbox_password: $("input[name=inbox-externa-password]").val()},
                                        dataType: "JSON",
                                        success: function (respuesta) {
                                            if (respuesta.success) {
                                                $.gritter.add({
                                                    title: lang.notif_inbox_externa_habilitada_titulo,
                                                    text: lang.notif_inbox_externa_habilitada_mensaje,
                                                    sticky: false,
                                                    time: '3000',
                                                    class_name: 'gritter-success'
                                                });
                                                alert(tab);
                                                $(tab + ' #activar-inbox-externa-container').remove();
                                                instancia.listar();
                                            }
                                            else
                                            {
                                                $.gritter.add({
                                                    title: lang.ocurrio_error, //lang.upps,
                                                    text: respuesta.errors,
                                                    sticky: false,
                                                    time: '3000',
                                                    class_name: 'gritter-error'
                                                });
                                            }
                                        }
                                    });
                                });
                            });
                        }

                        $(tab + ' .message-infobar .grey').html('(' + respuesta.unreadedCount + ' ' + lang.no_leidos + ')');

                        $(tab + ' .message-list').ready(function () {
                            // hacemos unbing para evitar hacer muchos bingings del mismo evento
                            // quizas sea mejor hacer el binding desde otra parte.
                            $("#marcar-como-leidos-boton").unbind('click');
                            $('#marcar-como-leidos-boton').click(function () {
                                marcarLeidosInboxExterna();
                            });
                        });
                    }

                    $(tab + ' #TotalMensajes').html(respuesta.iTotalRecords + ' ' + lang.mensajes);
                    $(tab + ' #loading-spinner-overlay').remove();
                }
            });
            $(this.selector + ' input[name="numeroPagina"]').val(this.numerador + 1);
        };
        Tab.prototype.select_all = function () {
            var count = 0;
            $(this.selector + ' .message-item input[type=checkbox]').each(function () {
                this.checked = true;
                $(this).closest('.message-item').addClass('selected');
                count++;
            });
            $(this.selector + ' .id-toggle-all').get(0).checked = true;
            this.display_bar(count);
        };
        Tab.prototype.display_bar = function (count) {
            if (count == 0) {
                $(this.selector + ' .id-toggle-all').removeAttr('checked');
                $(this.selector + ' #id-message-list-navbar .message-toolbar').addClass('hide');
                $(this.selector + ' #id-message-list-navbar .message-infobar').removeClass('hide');
                if (this.selector === '#inbox_externa') {
                    $(this.selector + ' .message-toolbar').addClass('hide');
                }
            }
            else {
                if (this.selector === '#inbox_externa') {
                    $(this.selector + ' .message-toolbar').removeClass('hide');
                }

                $(this.selector + ' #id-message-list-navbar .message-infobar').addClass('hide');
                $(this.selector + ' #id-message-list-navbar .message-toolbar').removeClass('hide');
            }
        };
        Tab.prototype.select_none = function () {
            $(this.selector + ' .message-item input[type=checkbox]').removeAttr('checked').closest('.message-item').removeClass('selected');
            $(this.selector + ' .id-toggle-all').get(0).checked = false;
            this.display_bar(0);
        };
        Tab.prototype.select_read = function () {
            $(this.selector + ' .message-unread input[type=checkbox]').removeAttr('checked').closest('.message-item').removeClass('selected');
            var count = 0;
            $(this.selector + ' .message-item:not(.message-unread) input[type=checkbox]').each(function () {
                this.checked = true;
                $(this).closest('.message-item').addClass('selected');
                count++;
            });
            this.display_bar(count);
        };
        Tab.prototype.select_unread = function () {
            $(this.selector + ' .message-item:not(.message-unread) input[type=checkbox]').removeAttr('checked').closest('.message-item').removeClass('selected');
            var count = 0;
            $(this.selector + ' .message-unread input[type=checkbox]').each(function () {
                this.checked = true;
                $(this).closest('.message-item').addClass('selected');
                count++;
            });
            this.display_bar(count);
        }
    };


    var inbox = new Tab('inbox');
    instancia = inbox;
    var inbox_externa = new Tab('inbox_externa');

    var cerradas = new Tab('cerradas');
    var eliminado = new Tab('eliminado');
    var accion = $("[name=ver_consulta_web]");
    if (accion.length > 0) {

        //alert(accion.attr('data-accion'));
        // alert(accion.val());

        cargarRespuestas(accion.val());

        accion.attr('data-accion') == 'ver_consulta' ? '' : responderConsulta(accion.val());

    } else {
        inbox.listar();

    }
    inlineTab = inbox.id;



//    Eventos


    $('#inbox-tabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var currentTab = $(this).attr('href');

        if (currentTab === '#inbox_externa') {
            $('ul#inbox-tabs li.active a').css('background-color', '#FFD8D8');
            $('.message-navbar').css('background-color', '#FFD8D8');
        }
        else
        {
            $('ul#inbox-tabs li a').css('background-color', '#F1F5FA');
            $('.message-navbar').css('background-color', '#F1F5FA');
        }

        switch (currentTab) {
            case  '#cerradas':
                inlineTab = cerradas.id;
                cerradas.listar();
                instancia = cerradas;
                break;

            case '#inbox':
                inlineTab = inbox.id;
                inbox.listar();
                instancia = inbox;
                break;

            case '#inbox_externa':
                inlineTab = inbox.id;
                inbox_externa.listar();
                instancia = inbox_externa;

                $("#inbox_externa").removeClass("hide");
                break;

            case '#eliminado':
                inlineTab = eliminado.id;
                instancia = eliminado;
                eliminado.listar();
                break;
        }
    });

    $('.message-list').on('click', '.sender, .summary ', function () {
        idAsuntoDetalle = $(this).closest('.message-item').attr('value');

        if (instancia.id === 'inbox_externa') {
            cargarEmailInboxExterna(idAsuntoDetalle);
        }
        else {
            cargarRespuestas(idAsuntoDetalle);
        }
    });

    $('.btn-back-message-list').on('click', function (e) {
        instancia.listar();
        return false;
    });

    $('#inbox_externa_buttons').on('click', 'a', function () {
        var do_action = $(this).attr('href');

        if (do_action === 'mostrar_form_nueva_consulta') {
            /*html = 'Hola<br />mundo<br />:)<br />!';
             $('.message-body').prepend(html);*/
            $("#form_procesar_email_consulta").show();
        }
    });

    $('#mover').on('click', 'a', function () {
        var cambiarEstado = $(this).attr('href');

        var dataPOST = 'cambiarEstado=' + cambiarEstado + '&idAsunto%5B%5D=' + idAsuntoDetalle;
        $.ajax({
            url: BASE_URL + 'consultasweb/cambiarEstadoAsunto',
            type: "POST",
            data: dataPOST,
            dataType: "JSON",
            cache: false,
            success: function (respuesta) {
                if (respuesta.codigo == 1) {
                    $.gritter.add({
                        title: lang.ok,
                        text: lang.validacion_ok,
                        sticky: false,
                        time: '3000',
                        class_name: 'gritter-success'
                    });
                    instancia.select_none();
                    instancia.listar();
                } else {
                    $.gritter.add({
                        title: lang.upps,
                        text: lang.ocurrio_error,
                        sticky: false,
                        time: '3000',
                        class_name: 'gritter-error'
                    });
                }
            }
        });
        return false;
    });

    $('.eliminar').on('click', function () {
        var cambiarEstado = $(this).attr('href');
        var dataPOST = 'cambiarEstado=' + cambiarEstado + '&idAsunto%5B%5D=' + idAsuntoDetalle;
        $.ajax({
            url: BASE_URL + 'consultasweb/cambiarEstadoAsunto',
            type: "POST",
            data: dataPOST,
            dataType: "JSON",
            cache: false,
            success: function (respuesta) {
                if (respuesta.codigo == 1) {
                    $.gritter.add({
                        title: lang.ok,
                        text: lang.eliminacion_ok,
                        //image: $path_assets+'/avatars/avatar1.png',
                        sticky: false,
                        time: '3000',
                        class_name: 'gritter-success'
                    });
                    instancia.select_none();
                    instancia.listar();
                } else {
                    $.gritter.add({
                        title: lang.upps,
                        text: lang.ocurrio_error,
                        sticky: false,
                        time: '3000',
                        class_name: 'gritter-error'
                    });
                }
            }
        });
        return false;
    });

    $('.id-toggle-all').removeAttr('checked').on('click', function () {
        if (this.checked) {
            instancia.select_all();
        } else
            instancia.select_none();
    });

    //select read
    $('.id-select-message-read').on('click', function (e) {
        e.preventDefault();
        console.log("no voy a seleccionar todos");
        instancia.select_unread();
    });

    //select unread
    $('.id-select-message-unread').on('click', function (e) {
        instancia.select_read();
        e.preventDefault();
    });

    //select none
    $('.id-select-message-none').on('click', function (e) {
        e.preventDefault();
        instancia.select_none();
    });

    //select all
    $('.id-select-message-all').on('click', function (e) {
        instancia.select_all();
        e.preventDefault();
    });

    //click en un chekbox en especial
    $('.message-list').delegate('.message-item input[type=checkbox]', 'click', function () {
        $(this).closest('.message-item').toggleClass('selected');
        if (this.checked) {
            instancia.display_bar(1);
        } else {
            instancia.display_bar($('.message-list input[type=checkbox]:checked').length);
        }
    });

    $('.dropdownList').on('click', 'a', function () {
        var cambiarEstado = $(this).attr('href');
        var idAsunto = $('#frm' + instancia.id).serialize();
        var dataPOST = 'cambiarEstado=' + cambiarEstado + '&' + idAsunto;
        $.ajax({
            url: BASE_URL + 'consultasweb/cambiarEstadoAsunto',
            type: "POST",
            data: dataPOST,
            dataType: "JSON",
            cache: false,
            success: function (respuesta) {
                if (respuesta.codigo == 1) {
                    $.gritter.add({
                        title: lang.ok,
                        text: lang.validacion_ok,
                        //image: $path_assets+'/avatars/avatar1.png',
                        sticky: false,
                        time: '3000',
                        class_name: 'gritter-success'
                    });
                    instancia.select_none();
                    instancia.listar();
                } else {
                    $.gritter.add({
                        title: lang.upps,
                        text: lang.ocurrio_error,
                        sticky: false,
                        time: '3000',
                        class_name: 'gritter-error'
                    });
                }
            }
        });
        return false;
    });

    $('.borrarList').on('click', function () {
        var cambiarEstado = $(this).attr('href');
        var cantEliminados = $('#frm' + instancia.id).serializeArray().length;
        var idAsunto = $('#frm' + instancia.id).serialize();
        var dataPOST = 'cambiarEstado=' + cambiarEstado + '&' + idAsunto;

        $.ajax({
            url: BASE_URL + 'consultasweb/cambiarEstadoAsunto',
            type: "POST",
            data: dataPOST,
            dataType: "JSON",
            cache: false,
            success: function (respuesta) {
                if (respuesta.codigo == 1) {
                    $.gritter.add({
                        title: lang.ok,
                        text: lang.eliminacion_ok,
                        //image: $path_assets+'/avatars/avatar1.png',
                        sticky: false,
                        time: '3000',
                        class_name: 'gritter-success'
                    });
                    cantEliminados == 10 ? instancia.numerador-- : '';
                    instancia.listar();
                    instancia.select_none();
                } else {
                    $.gritter.add({
                        title: lang.upps,
                        text: lang.ocurrio_error,
                        sticky: false,
                        time: '3000',
                        class_name: 'gritter-error'
                    });
                }
            }
        });
        return false;
    });

    $('.message-content').on('click', '.borrarListdesdeDetalle', function () {
        var cambiarEstado = $(this).attr('href');
        var dataPOST = 'cambiarEstado=' + cambiarEstado + '&idAsunto%5B%5D=' + idAsuntoDetalle;
        $.ajax({
            url: BASE_URL + 'consultasweb/cambiarEstadoAsunto',
            type: "POST",
            data: dataPOST,
            dataType: "JSON",
            cache: false,
            success: function (respuesta) {
                if (respuesta.codigo == 1) {
                    $.gritter.add({
                        title: lang.ok,
                        text: lang.eliminacion_ok,
                        //image: $path_assets+'/avatars/avatar1.png',
                        sticky: false,
                        time: '3000',
                        class_name: 'gritter-success'
                    });
                    instancia.select_none();
                    instancia.listar();
                } else {
                    $.gritter.add({
                        title: lang.upps,
                        text: lang.ocurrio_error,
                        sticky: false,
                        time: '3000',
                        class_name: 'gritter-error'
                    });
                }
            }
        });
        return false;
    });

    $('.message-content').on('click', '.responder', function () {

        responderConsulta(idAsuntoDetalle);
        return false;
    });

    $('.message-list').on('click', '.message-star', function () {
        var destacar = 1;
        var element = this;
        var aplicarClase = 'message-star icon-star  orange2';
        var destacarClass = $(this).attr('class');
        var idAsunto = $(this).parent().find('input[name="idAsunto[]"]').val();
        if (destacarClass == 'message-star icon-star  orange2') {
            destacar = 0;
            var aplicarClase = 'message-star  icon-star-empty light-grey';
        }

        $.ajax({
            url: BASE_URL + 'consultasweb/destacarAsunto',
            type: "POST",
            data: "destacar=" + destacar + "&idAsunto=" + idAsunto,
            dataType: "JSON",
            cache: false,
            success: function (respuesta) {
                if (respuesta.codigo == 1) {
                    $(element).attr('class', aplicarClase);

                }
            }
        });
        return false;
    });

    var focus = 0;
    $('.form-search').on('focus', '.nav-search-input', function () {
        focus = 1;
    });

    $('.form-search').on('focusout', '.nav-search-input', function () {
        focus = 0;
    });

    $('.tab-pane').on('keydown', '.form-search', function (e) {
        if (focus == 1 && e.keyCode == 13) {
            instancia.search = $(this).find('input').val();
            instancia.FechaDesde = $(this).find('FechaDesde').val();
            instancia.listar();
            return false;
        }
    });
};




function getTable(){

    var fecha_hasta = $("[name=filtro_fecha_hasta]").val();
    var fecha_desde = $("[name=filtro_fecha_desde]").val();
    var curso       = $("[name=filtro_curso]").val();

    instancia.FechaDesde = $(this).find('input[name=FechaDesde]').last().val();
    instancia.fecha_hasta = $(this).find('input[name=filtro_fecha_desde]').last().val();
    instancia.curso       = $(this).find('input[name=filtro_curso]').last().val();

    instancia.listar();
    return false;

}

