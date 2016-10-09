var fechaDesde = '';
var fechaHasta = '';
function printers_jobs(id_script_impresion, arrParametros, onclose) {
    $.ajax({
        url: BASE_URL + 'impresion/get_metodo_imprimir/' + id_script_impresion,
        type: 'GET',
        dataType: 'json',
        success: function(_json) {
            if (_json.error) {
                alert("Ha ocurrido un error al recuperar los metodos de impresion\ncon el mensaje\n" + _json.error);
            } else {
                if (_json.metodo == "imprimir") {
                    if (_json.impresora == "Cloud") {
                        var param = new Array();
                        param['parametros'] = arrParametros[0];
                        imprimirCloud(id_script_impresion, param, onclose);
                    } else {
                        var param = new Array();
                        param['parametros'] = arrParametros;
                        imprimirNavegador(id_script_impresion, param, onclose);
                    }
                } else if (_json.metodo == "preguntar") {
                    preguntarImprimir(id_script_impresion, arrParametros, onclose);
                } else {
                    if (onclose){
                        eval(onclose);
                    }
                }
            }
        }
    });
}

function imprimirCloud(id_script_impresion, arrParametros, successfunction, errorfunction, onclose) {
    var url = getUrlScriptImpresion(id_script_impresion);
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        data: {
            parametros: arrParametros['parametros'],
            id_impresora: arrParametros['id_impresora'],
            copias: arrParametros['copias'],
            imprimir_reglamento: arrParametros['imprimir_reglamento'],
            imprimir_matricula: arrParametros['imprimir_matricula'],
            imprimir_resumen_cuenta: arrParametros['imprimir_resumen_cuenta'],
            //mmori
            imprimir_observaciones: arrParametros['imprimeObservaciones']
        },
        success: function(_json) {
            if (_json.error) {
                alert(_json.error);
                if (errorfunction) {
                    eval(errorfunction);
                }
            } else {
                if (successfunction) {
                    eval(successfunction);
                }
            }
        }
    });
}

function imprimirNavegador(id_script_impresion, arrParametros, onclose) {
    var url = getUrlScriptImpresion(id_script_impresion);
    var f = new Date();
    var nombre = Math.floor(Math.random() * (10000)) + 1 + "_" + f.getDate() + "_" + f.getHours() + "_" + f.getMinutes() + "_" + f.getSeconds();
    windowOpenPost(url, arrParametros, "new_target_" + nombre);
    if (onclose){
        eval(onclose);
    }
}

function preguntarImprimir(id_script_impresion, arrParametros, onclose) {
    $.ajax({
        url: BASE_URL + 'impresion/preguntar_imprimir/',
        type: 'POST',
        data: {
            id_script_impresion: id_script_impresion,
            parametros: arrParametros
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
                    if (onclose){
                        eval(onclose);
                    }
                }
            });
        }
    });
}

function getUrlScriptImpresion(id_script_impresion) {
    id_script_impresion = parseInt(id_script_impresion);
    var url = '';
    switch (id_script_impresion) {
        case 1:
            url = BASE_URL + "impresion/imprimir_presupuesto/";
            break;

        case 2:
            url = BASE_URL + "impresion/imprimir_formulario_baja/";
            break;

        case 3:
            url = BASE_URL + "impresion/resumen_ctacte_alumno/";
            break;

        case 4:
            url = BASE_URL + "impresion/estado_academico/";
            break;

        case 5:
            url = BASE_URL + "impresion/imprimir_matricula/";
            break;

        case 6:
            url = BASE_URL + "impresion/inscriptos_a_examenes/";
            break;

        case 7:
            url = BASE_URL + "impresion/constancia_examen/";
            break;

        case 8:
            url = BASE_URL + "impresion/acta_volante/";
            break;

        case 9:
            url = BASE_URL + "impresion/asistencias/";
            break;

        case 10:
            url = BASE_URL + "impresion/recibo_cobros/";
            break;

        case 11:
            url = BASE_URL + "impresion/facturacion/";
            break;

        case 12:

            url = BASE_URL + "impresion/imprimir_reporte_facturacion";
            break;

        case 13:
            url = BASE_URL + "impresion/imprimir_remessa_boleto_bancario";
            break;

        case 14:
            url = BASE_URL + "impresion/imprimir_boleto_bancario";
            break;

        case 15:
            url = BASE_URL + 'impresion/imprimir_inscriptos_seminario';
            break;

        case 16:
            url = BASE_URL + 'impresion/imprimir_boletos_bancarios'
        default:
            break;
    }
    return url;
}

function cerrarPreguntarImprimir() {
    $.fancybox.close(true);
}

function seleccionImpresionImprimir() {
    $("#btnImprimir").attr("disabled", true);
    $("#btnCancelar").attr("disabled", true);
    var id_script_impresion = $("#id_script_inicio").val();
    var param = new Array();
    param['parametros'] = $("#param0").val();
    var imprimeReglamento = new Array();
    if (id_script_impresion == 5) {
        $('input[name="imprimir_reglamento[]"]').each(function(i, val) {
            if ($(val).is(":checked")) {
                imprimeReglamento.push($(val).val());
            }
        });
        var imprimeMatricula = $("#imprimir_matricula").is(":checked") ? 1 : 0;
        var imprimir_resumen_cuenta = $("#imprimir_resumen_cuenta").is(":checked") ? 1 : 0;
        var imprimir_recibo_cobro_matricula = $("[name=imprimir_recibo_cobro]").is(":checked") ? 1 : 0;
        
        param['imprimir_matricula'] = imprimeMatricula;
        param['imprimir_reglamento'] = new Array();
        param['imprimir_reglamento'] = JSON.stringify(imprimeReglamento);
        param['imprimir_resumen_cuenta'] = imprimir_resumen_cuenta;
        param['imprimir_recibo_cobro'] = imprimir_recibo_cobro_matricula;
        
        //imprimir observaciones
        var imprimir_observaciones = $("#imprimir_observaciones").is(":checked") ? 1 : 0;
        param['imprimir_observaciones'] = imprimir_observaciones;
    }
    if (id_script_impresion == 8) {
        var imprimir_notas_parciales = $("#imprimir_notas_parciales").is(":checked") ? 1 : 0;
        param['imprimir_notas_parciales'] = imprimir_notas_parciales;
        var imprimir_estado_deuda = $("#imprimir_estado_deuda").is(":checked") ? 1 : 0;
        param['imprimir_estado_deuda'] = imprimir_estado_deuda;
    }
    if (id_script_impresion == 9) {
        param['fecha_desde'] = $("#contenedor_fechas").find('[name="fecha_desde"]').val();
        param['fecha_hasta'] = $("#contenedor_fechas").find('[name="fecha_hasta"]').val();
    }
    var id_impresora = $("#impresora_imprimir").val();
    var cantidadCopias = $("#impresion_cantidad_copias").val();
    param['id_impresora'] = id_impresora;
    param['copias'] = cantidadCopias;
    if (id_script_impresion == 5 && imprimeMatricula == 0 && param['imprimir_reglamento'].length == 0 && imprimir_resumen_cuenta == 0) {
        alert("Debe elegir imprimir matricula y/o reglamento y/o resumen de cuenta");
    } else {        
        if (id_impresora == -1) {
            imprimirNavegador(id_script_impresion, param);
            cerrarPreguntarImprimir();
        } else {
            $("#area_mensajes").html("enviado trabajo de impresion...");
            imprimirCloud(id_script_impresion, param, "cerrarPreguntarImprimir()", "enabledButton()");
        }
    }
}

function enabledButton() {
    $("#area_mensajes").html("");
    $("#btnImprimir").attr("disabled", false);
    $("#btnCancelar").attr("disabled", false);
}

function windowOpenPost(url, data, target) {
    var formulario = document.createElement("form");
    formulario.action = url;
    formulario.method = "POST";
    formulario.target = target || "_self";
    formulario.id = target + "_printer";
    formulario.style.display = 'none';
    $("#" + target + "_printer").remove();
    document.body.appendChild(formulario);
    if (data) {
        for (var key in data) {
            $('<input>').attr({
                type: 'hidden',
                name: key,
                value: data[key]
            }).appendTo('#' + target + "_printer");
        }
    }
    formulario.submit();
}

function mostrarFiltrosAsistencia(element) {
    var seleccionado = $(element).val();
    if (seleccionado == 2) {
        $('#contenedor_fechas').removeClass('hide');
        $('input[name="fecha_desde"]').datepicker({
            format: 'dd/mm/yyyy'
        });

        $('input[name="fecha_hasta"]').datepicker({
            format: 'dd/mm/yyyy'
        });
    } else {
        $('#contenedor_fechas').addClass('hide');
        $('#contenedor_fechas').find('input').val('');
    }
}
