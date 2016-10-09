$('.main_content').ready(function() {
    $.ajax({
        url: BASE_URL + 'planespago/getFinanciacionPlan',
        data: "codigo=" + $("[name=plan_original]").val(),
        type: 'POST',
        cache: false,
        async: false,
        dataType: 'json',
        success: function(respuesta) {
            FinanciacionesObj = respuesta;
            Init();
        },
        error: function() {
            $(".fancybox-wrap").remove();
        }
    });
    $( "#title_tooltip" ).tooltip({
        show: null,
        position: {
            my: "left top",
            at: "left bottom"
        },
        open: function( event, ui ) {
            ui.tooltip.animate({ top: ui.tooltip.position().top + 10 }, "fast" );
        }
    });
});

function Init() {
    $('select').chosen({
        width: '100%'
    });
    if ($("#config-periodo").val() !== "1") {
        $('.grupoPeriodo').hide();
    }
    $('select[name="cursos"]').on('change', function() {
        var seleccion = new Array();
        var modalidades = new Array();
        if (actualizar_periodos){
            var chk_periodos = $("[name=periodos]");
            $.each(chk_periodos, function(key, element){
                if ($(element).is(":checked")){
                    seleccion[$(element).val()] = true;
                    var sel_modalidad = $(element).closest("tr").find("[name=periodos_modalidad]").val();
                    modalidades[$(element).val()] = sel_modalidad;
                }
            });
        }
        var curso = $(this).val();
        $("#tbody_periodos_modalidades").find("tr").remove();
        $("#tbody_periodos_modalidades").append("<tr><td colspan='2' style='text-align: center;'>" + "(" + langFrm.recuperando + ")" + "</td></tr>");
        $.ajax({
            url: BASE_URL + 'planespago/getPeriodosCurso',
            type: 'POST',
            data: 'cod_curso=' + curso,
            dataType: 'json',
            cache: false,
            success: function(respuesta) {
                $("#tbody_periodos_modalidades").find("tr").remove();
                var muestra_periodos = respuesta.muestra_peridos == 1;
                var _html = '';
                $.each(respuesta.periodos_plan, function(key, periodos){
                    _html += '<tr>';
                    if (!muestra_periodos){
                        var complemento = !actualizar_periodos || seleccion[periodos.cod_tipo_periodo] ? 'checked="true"' : '';
                        _html += '<td>';
                        _html += '<label>';
                        _html += '<input type="checkbox" class="ace checkperiodo" name="periodos" ' + complemento + ' value="' + periodos.cod_tipo_periodo + '">';
                        _html += '<span class="lbl">';
                        _html += '</span>';
                        _html += '</label>';
                        _html += '</td>';
                    }
                    _html += '<td>';
                    if (muestra_periodos){
                        _html += '<input type="checkbox" class="checkperiodo" name="periodos" checked="" value="' + periodos.cod_tipo_periodo + '" style="display: none;">';
                    }
                    if (periodos.modalidad.length > 1){
                        _html += '<select class="form-control" name="periodos_modalidad" id="periodo_modalidad" data-placeholder="' + langFrm.seleccione_modalidad + '">';
                        $.each(periodos.modalidad, function(key, modalidad){
                            var complemento = actualizar_periodos && seleccion[periodos.cod_tipo_periodo] && modalidad.modalidad == modalidades[periodos.cod_tipo_periodo] ? 'selected="true"' : '';
                            _html += '<option value="' + modalidad.modalidad + '" ' + complemento + '>';
                            _html += modalidad.nombre_periodo + ' [' + eval("langFrm." + modalidad.modalidad) + ']';
                            _html += '</option>';
                        });
                        _html += '</select>';
                    } else {
                        _html += '<span style="padding-left: 12px;">';
                        _html += periodos.modalidad[0].nombre_periodo + ' [' + eval("langFrm." + periodos.modalidad[0].modalidad) + ']';
                        _html += '</span>';
                        _html += '<input type="hidden" name="periodos_modalidad" id="periodos_modalidad" value="' + periodos.modalidad[0].modalidad + '">';
                    }
                    _html += '</td>';
                    _html += '</tr>';
                });
                $("#tbody_periodos_modalidades").append(_html);
            }
        });
    });

    if (FinanciacionesObj !== null) {
        $.each(FinanciacionesObj, function(key, value) {
            $.each(value, function(key, financiacion) {
                //debugger;
                addRowTablePlanes(financiacion.cantcuotas, financiacion.descuento, financiacion.total_neto, financiacion.detalle,
                    financiacion.estado, key, financiacion.concepto, financiacion.interes, financiacion.limite_primer_cuota, financiacion.fecha_limite, financiacion.limite_vigencia, financiacion.fecha_vigencia);
            });
        });
    }

    $("#agregar-mas").click(function() {
        var valor_curso = $("[name=curso_precio_lista]").val();
        addRowTablePlanes(null, null, valor_curso);
    });

    var totalFilas = $('tr:last td', $("#tablaFinanciaciones")).length;
    var dia = parseInt($("[name=hd_dia]").val());
    var mes = parseInt($("[name=hd_mes]").val());
    var anio = parseInt($("[name=hd_anio]").val());

    $("#fecha-inicio").datepicker({
        changeMonth: false,
        changeYear: false,
        dateFormat: 'dd/mm/yy',
        minDate: new Date(anio, mes - 1, dia)
    });

    $("#fecha-fin").datepicker({
        cahngeMonth: false,
        changeYear: false,
        dateFormat: 'dd/mm/yy',
        minDate: new Date(anio, mes - 1, dia)
    });

    if (totalFilas === 0) {
        addRowTablePlanes(null, null, null, null, null, null, 5);
    }

    $("#cambiarFecha").click(function() {
        $(".control-fechas").removeClass("hide");
        $.fancybox.update();
    });

    $("#btn-guardar").click(function() {
        var codigo_plan = $("[name=codigo-plan]").val();
        var nombre_plan = trim($("[name=nombrePlan]").val());
        var descuento_condicionado = $("[name=descuentocond]").is(":checked") ? 1 : 0;
        var fecha_inicio = $("[name=fecha-inicio]").val();
        var fecha_fin = $("[name=fecha-fin]").val();
        var cursos = $("[name=cursos]").val();
        var matricula_precio_lista = $("[name=matricula_precio_lista]").val();
        var curso_precio_lista = $("[name=curso_precio_lista]").val();
        if (separador_decimal == ','){
            matricula_precio_lista = parseFloat(matricula_precio_lista.replace(/,/g, '.'));
            curso_precio_lista = parseFloat(curso_precio_lista.replace(/,/g, '.'));
        }
        var periodicidad = $("[name=periodocidad]").val();
        var periodos = new Array();
        var select_periodos = $("#tbody_periodos_modalidades").find("tr");
        $.each(select_periodos, function(key, value){
            if ($(value).find("[name=periodos]").is(":checked")){
                var cod_periodo = $(value).find("[name=periodos]").val();
                var modalidad = $(value).find("[name=periodos_modalidad]").val();
                periodos.push({
                    codigo_periodo: cod_periodo,
                    modalidad: modalidad
                });
            }
        });
        var select_financiaciones = $("#tablaFinanciaciones").find("tbody").find("tr");
        var financiaciones = new Array();
        var mensaje = '';
        $.each(select_financiaciones, function(key, value){
            var concepto_financiacion = $(value).find("[name=concepto_financiacion]").val();
            var codigo_financiacion = $(value).find("[name=input_financiacion]").val();
            var recargo_financiacion = $(value).find("[name=recargo_financiacion]").val();
            var descuento_financiacion = $(value).find("[name=valordescuento]") .val();
            var tipo_fecha_limite = $(value).find("[name=tipo_fecha_limte]").val();
            var tipo_fecha_financiacion_limite = (concepto_financiacion == 1)?"con_fecha_limite":"sin_fecha_limite";
            var fecha_fecha_limite = $(value).find("[name=fecha_fecha_limite]").val();
            var fecha_financiacion_limite = $(value).find("[name=fecha_financiacion_limite]").val();
            var detalle_cuotas = $(value).find("[name=detalle]").val();
            var valor_neto_concepto = $(value).find("[name=valorcurso]").val();
            if (descuento_financiacion == ''){
                mensaje += langFrm.el_valor_de_descuento_no_debe_ser_vacio + '<br>';
            } else if (tipo_fecha_limite == 'con_fecha_limite' && fecha_fecha_limite == '' && mensaje == ''){
                mensaje += langFrm.fecha_limite_es_obligatorio + "<br>";
            }else if (tipo_fecha_financiacion_limite == 'con_fecha_limite' && fecha_financiacion_limite == '' && mensaje == ''){
                mensaje += langFrm.fecha_limite_es_obligatorio + "<br>";
            } else {
                if (separador_decimal == ','){
                    recargo_financiacion = parseFloat(recargo_financiacion.replace(/,/g, '.'));
                    descuento_financiacion = parseFloat(descuento_financiacion.replace(/,/g, '.'));
                    valor_neto_concepto = parseFloat(valor_neto_concepto.replace(/,/g, '.'));
                }
                financiaciones.push({
                    concepto_financiacion: concepto_financiacion,
                    codigo_financiacion: codigo_financiacion,
                    recargo_financiacion: recargo_financiacion,
                    descuento_financiacion: descuento_financiacion,
                    tipo_fecha_limite: tipo_fecha_limite,
                    fecha_fecha_limite: fecha_fecha_limite,
                    tipo_fecha_financiacion_limite: tipo_fecha_financiacion_limite,
                    fecha_financiacion_limite: fecha_financiacion_limite,
                    detalle: detalle_cuotas,
                    valor_neto_concepto: valor_neto_concepto
                });
            }
        });
        if (nombre_plan == '') mensaje += langFrm.nombre_del_plan_es_requerido + "<br>";
        if (cursos == '') mensaje += langFrm.plan_academico_es_requerido + "<br>";
        if (periodos.length == 0) mensaje += langFrm.periodo_plan_es_requerido + "<br>";
        if (matricula_precio_lista === '') mensaje += langFrm.precio_de_lista_de_matricula_es_requerido + "<br>";
        if (curso_precio_lista == '') mensaje += langFrm.precio_de_lista_del_curso_es_requerido + "<br>";
        if (matricula_precio_lista == 0 && curso_precio_lista == 0) mensaje += langFrm.precio_lista_matricula_o_precio_lista_curso_no_pueden_ser_cero + "<br>";
        if (mensaje != ''){
            gritter(mensaje);
        } else {
            $.ajax({
                url: BASE_URL + 'planespago/guardar',
                type: 'POST',
                dataType: 'json',
                data: {
                    codigo_plan: codigo_plan,
                    nombre_plan: nombre_plan,
                    descuento_condicionado: descuento_condicionado,
                    fecha_inicio: fecha_inicio,
                    fecha_fin: fecha_fin,
                    cursos: cursos,
                    matricula_precio_lista: matricula_precio_lista,
                    curso_precio_lista: curso_precio_lista,
                    periodicidad: periodicidad,
                    periodos: periodos,
                    financiaciones: financiaciones,
                    nuevo: true
                },
                success: function(_json){
                    if (_json.codigo == 1){
                        gritter(langFrm.PLAN_GUARDADO_CORRECTAMENTE, true);
                        $.fancybox.close();
                    } else {
                        gritter(_json.msgerror, false, "Error");
                    }
                }
            });
        }
    });

    $('.fancybox-wrap').on("click", '.checkf', function() {
        if ($(this).is(':checked')) {
            $("#input-check-" + $(this).val()).val(1);
        } else {
            $("#input-check-" + $(this).val()).val(0);
        }
    });

    $('#tablaFinanciaciones').on('click', '.btn-detalles', function() {
        boton = this;
        var tr = $(this).closest("tr");
        var concepto = $(tr).find("[name=concepto_financiacion]").val();
        var descuento = $(tr).find("[name=valordescuento]").val();
        if ($(boton).parent().find('.popover').is(':visible')) {
            $(boton).popover('hide');
        } else {
            var valorJsonDetalle = $(tr).find("[name=detalle]").val();
            var ObjDetalle;
            if (valorJsonDetalle !== "0") {
                ObjDetalle = JSON.parse(valorJsonDetalle);
                generarTablaFinanciacion(ObjDetalle, $(boton).val(), concepto, descuento);
                $('#stack1').modal('show');
            } else {
                var valor_neto = $(tr).find("[name=valorcurso]").val();
                var cuotas_financiacion = $(tr).find("[name=input_financiacion]").val();
                if (separador_decimal == ','){
                    cuotas_financiacion = cuotas_financiacion.replace(/,/g, '.');
                    valor_neto = valor_neto.replace(/,/g, '.');
                    descuento = descuento.replace(/,/g, '.');
                }
                $("[name=btn_financiacion_guardar]").show();
                $.ajax({
                    url: BASE_URL + 'planespago/getDetalleNuevo',
                    data: {
                        cuotas_finaciacion: cuotas_financiacion,
                        valor_neto: valor_neto,
                        valor_descuento: descuento
                    },
                    type: 'POST',
                    cache: false,
                    async: false,
                    dataType: 'json',
                    success: function(respuesta) {
                        if (respuesta.codigo === "0") {
                            $.gritter.add({
                                title: 'Error!',
                                text: respuesta.msgerror,
                                sticky: false,
                                time: '3000',
                                class_name: 'gritter-error'
                            });
                        } else {
                            generarTablaFinanciacion(respuesta, $(boton).val(), concepto, descuento);
                            $('#stack1').modal('show');
                        }
                    }
                });
            }
        }
        return false;
    });
    if (actualizar_periodos){
        $("select[name=cursos]").change();
    }

    cambiaSelectConcepto();
}

function guardarFinanciacionModificada(){
    var descuento = $("[name=detalle_financiacion_valor_descuento]").val();
    if (separador_decimal == ','){
        descuento = parseFloat(descuento.replace(/,/g, '.'));
    }
    var element = $("[name=detalle_financiacion_cuota_descuento]");
    var idFinanciacion = $("[name=detalle_financiacion_identificador]").val();
    var arrayResp = new Array();
    var valor_neto_curso = $("#valor-curso-" + idFinanciacion).val();
    if (separador_decimal == ','){
        valor_neto_curso = valor_neto_curso.replace(/,/g, '.');
    }
    valor_neto_curso = parseFloat(valor_neto_curso);
    var valorTotal = 0;
    $.each(element, function(key, value){
        var tr = $(value).closest("tr");
        var id = $(tr).find("[name=detalle_nro_cuota]").val();
        var valor_neto = $(tr).find("[name=detalle_financiacion_cuota_neto]").val();
        if (separador_decimal == ','){
            valor_neto = parseFloat(valor_neto.replace(/,/g, '.'));
        }
        valor_neto = Math.round(valor_neto * 100) / 100;
        var valor = $(tr).find("[name=detalle_financiacion_cuota_descuento]").val();
        if (separador_decimal == ','){
            valor = parseFloat(valor.replace(/,/g, '.'));
        }
        valorTotal = parseFloat(valorTotal) + parseFloat(valor);
        arrayResp.push({
            nrocuota: id,
            valor_neto: valor_neto,
            valor: valor,
            descuento: descuento
        });
    });
    if (valorTotal <= valor_neto_curso){
        var porcentaje_descuento = 100 - (valorTotal * 100 / valor_neto_curso);
        porcentaje_descuento = Math.round(porcentaje_descuento * 100) / 100;
        if (separador_decimal == ','){
            porcentaje_descuento = porcentaje_descuento.toString();
            porcentaje_descuento = porcentaje_descuento.replace(/\./, ',');
        }
        $("#valor-descuento-" + idFinanciacion).val(porcentaje_descuento);
        var json = JSON.stringify(arrayResp);
        $("#tr-plan-" + idFinanciacion).find("[name=detalle]").val(json);
        $('#stack1').modal('hide');
    } else {
        gritter("La suma de los valores con descuento no puede ser mayor al valor neto del curso");
    }
}

function generarTablaFinanciacion(ObjCuotas, idFinanaciacion, concepto, descuento) {
    $(".financiacion-body").html("");
    var table = "";
    table += "<form id='frm-financiacion'><div class='row'>";
    table += "<input type='hidden' name='detalle_financiacion_identificador' value='" + idFinanaciacion + "'>";
    table += "<input type='hidden' name='detalle_financiacion_concepto' value='" + concepto + "'>";
    table += "<input type='hidden' name='detalle_financiacion_valor_descuento' value='" + descuento + "'>";
    var texto_descuento = langFrm.con_XXX_de_descuento;
    texto_descuento = texto_descuento.replace(/XXX/g, descuento + "%");
    var i = 0;
    $(ObjCuotas).each(function(key, fila){
        if (i % 10 == 0) {
            table += "<div class='col-md-4'>";
            table += "<table class='table table-condensed' id='table_detalles_financiaciones'>";
            table += "<thead>";
            table += "<tr>";
            table += "<th>";
            table += "&nbsp;";
            table += "</th>";
            table += "<th>";
            table += "&nbsp;";
            table += "</th>";
            table += "<th>";
            table += langFrm.importe_neto;
            table += "</th>";
            table += "<th>";
            table += texto_descuento;
            table += "</th>";
            table += "</tr>";
            table += "</thead>";
        }
        table += "<tr>";
        table += "<td>";
        table += "<input type='hidden' name='detalle_financiacion_descuento' value='" + fila.descuento + "'>";
        table += "<input type='hidden' name='detalle_nro_cuota' value='" + fila.nrocuota + "' />";
        table += langFrm.formatearcuotas_cuota;
        table += "</td>";
        table += "<td>";
        table += fila.nrocuota;
        table += "</td>";
        table += "<td>";
        var valor_neto = Math.round(fila.valor_neto * 100) / 100;
        if (separador_decimal == ','){
            valor_neto = valor_neto.toString();
            valor_neto = valor_neto.replace(/\./g, ',');
        }
        table += "<input type='text' class='form-control input-sm' index='" + fila.nrocuota + "' name='detalle_financiacion_cuota_neto' value='" + valor_neto + "' readonly='true'/>";
        table += "</td>";
        table += "<td>";
        var valor = Math.round(fila.valor * 100) / 100;
        if (separador_decimal == ','){
            valor = valor.toString();
            valor = valor.replace(/\./g, ',');
        }
        table += "<input type='text' class=form-control input-sm' index='" + fila.nrocuota + "' name='detalle_financiacion_cuota_descuento' value='" + valor + "' onkeypress='return ingresarFloat(this, event, \"" + separador_decimal + "\");'>";
        table += "</td>";
        table += "<td>";
        table += "<span class='icon-bolt' style='cursor: pointer;'>";
        table += "</span>";
        table += "</td>";
        table += "</tr>";
        i++;
        if (i % 10 == 0) {
            table += "</table>";
            table += "</div>";
        }
    });
    table += "</div>";
    table += "</form>";
    $(".financiacion-body").html(table);
    $('#frm-financiacion').on("click", ".icon-bolt", function() {
        var trt = $(this).closest("tr");
        var importe_descuento = $(trt).find("[name=detalle_financiacion_cuota_descuento]").val();
        if (separador_decimal == ','){
            importe_descuento = parseFloat(importe_descuento.replace(/,/g, '.'));
        }
        importe_descuento = Math.round(importe_descuento * 100) / 100;
        if (separador_decimal == ','){
            var importe_descuento_mostrar = importe_descuento.toString();
            importe_descuento_mostrar = importe_descuento_mostrar.replace(/\./, ',');
        } else {
            var importe_descuento_mostrar = importe_descuento;
        }
        var indice = $(trt).find("[name=detalle_financiacion_cuota_neto]").attr("index");
        var seleccion = $("[name=detalle_financiacion_cuota_neto]");
        $.each(seleccion, function(key, value){
            if (parseInt($(value).attr("index")) >= parseInt(indice)){
                var tr = $(value).closest('tr');
                $(tr).find("[name=detalle_financiacion_cuota_descuento]").val(importe_descuento_mostrar);
            }
        });
    });
}

function addRowTablePlanes(cuotas, valorDescuento, valorCurso, detalle, estado, codFinanciacion, conceptoDefaultSelected, interes,
                           tipo_fecha_limite, fecha_limite, tipo_fecha_financiacion_limite, fecha_limite_financiacion) {
    detalle = detalle || 0;
    cuotas = cuotas || 0;
    var puede_modificar = $("[name=puede_modificar]").val() == 1;
    codigofinanciacion = codFinanciacion || -1;
    conceptoDefaultSelected = conceptoDefaultSelected || -1;
    var descuentoVacio = valorDescuento == null;
    valorDescuento = valorDescuento || 0;
    valorCurso = valorCurso || 0;
    tipo_fecha_limite = tipo_fecha_limite || "sin_fecha_limite";
    fecha_limite = fecha_limite || '';
    tipo_fecha_financiacion_limite = tipo_fecha_financiacion_limite || "sin_fecha_limite";
    fecha_limite_financiacion = fecha_limite_financiacion || '';
    estado = estado || "1";
    interes = interes || 0;
    if (detalle !== 0) {
        detalle = JSON.stringify(detalle);
    } else {
        detalle = 0;
    }

    var totalFilas = $('tr', $("#tablaFinanciaciones")).length;
    interes = Math.round(interes * 100) / 100;
    valorDescuento = Math.round(valorDescuento * 100) / 100;
    var complementoEditar = puede_modificar ? "" : "disabled='true'";
    var row = '<tr id="tr-plan-' + totalFilas + '">';
    row += '<td>';
    row += '<select class="form-control" id="concepto_financiacion_' + totalFilas + '" name="concepto_financiacion" data-fila='+totalFilas+' data-placeholder="' + langFrm.seleccione_concepto + '" onchange="conceptoChange(this);" ' + complementoEditar + '>';
    var selected = conceptoDefaultSelected == 1 ? 'selected="true"' : '';
    row += '<option value="1"' + selected + '>';
    row += 'Valor Curso';
    row += '</option>';
    var selected = conceptoDefaultSelected == 5 ? 'selected="true"' : '';
    row += '<option value="5"' + selected + '>';
    row += 'Matricula';
    row += '</option>';
    row += '</select>';
    row += '</td>';
    row += '<td>';
    row += '<input type="hidden" name="codigo_financiacion" value="' + codigofinanciacion + '">';
    row += '<input type="hidden" id="input-check-' + totalFilas + '" name="input-check-financiacion[]" value="' + estado + '">';
    row += '<select class="form-control" id="cuotas-financiacion-' + totalFilas + '" name="input_financiacion" onchange="resetearDetalle(this);" ' + complementoEditar + '>';
    var options = $("[name=template_financiacion]").find("option");
    $.each(options, function(key, value){
        var selected = cuotas == $(value).val() ? 'selected="true"' : '';
        row += '<option value="' + $(value).val() + '"' + selected + '>';
        row += $(value).text();
        row += '</option>';
    });
    row += '</select>';
    row += '</td>';
    row += '<td>';
    row += '<div class="form-group">';
    if (separador_decimal == ','){
        valorCurso = valorCurso.toString();
        valorCurso = valorCurso.replace(/\./g, ',');
    }
    row += '<input type="text"  class="form-control input-sm entero"  name="valorcurso"  value="' + valorCurso + '" placeholder="' + langFrm.valor_curso + '" id="valor-curso-' + totalFilas + '" onkeypress="return ingresarFloat(this, event, \'' + separador_decimal + '\');" onchange="interesChange(this);" ' + complementoEditar + '>';
    row += '</div>';
    row += '<input type="hidden" class="form-control input-sm" name="recargo_financiacion" value="' + interes + '" placeholder="' + langFrm.recargo_financiacion + '" id="recargo-financiacion-' + totalFilas + '">' ;
    row += '</td>';
    row += '<td>';
    row += '<div class="form-group">';
    if (separador_decimal == ','){
        valorDescuento = valorDescuento.toString();
        valorDescuento = valorDescuento.replace(/\./g, ',');
    }
    var valorDescuentoMostrar = descuentoVacio ? '0' : valorDescuento;

    row += '<input class="form-control input-sm entero" type="text"  name="valordescuento" value="' + valorDescuentoMostrar + '" placeholder="' + langFrm.planpago_descuento + '" id="valor-descuento-' + totalFilas + '" onkeypress="return ingresarFloat(this, event, \'' + separador_decimal + '\');" onchange="descuentoChange(this);" ' + complementoEditar + '>';
    row += '</div>';
    row += '</td>';
    row += '<td>';
    row += '<select class="form-control" id="tipo-fecha-limite-' + totalFilas + '" name="tipo_fecha_limte" onclick="validarFechaLimite(' + totalFilas + ');" ' + complementoEditar + '>';
    row += '<option value="sin_fecha_limite"';

    if (tipo_fecha_limite == "sin_fecha_limite"){
        row += ' selected="true" ';
    }
    row += '>';
    row += langFrm.sin_fecha_limite_planpago;
    console.log(langFrm);
    row += '</option>';
    row += '<option value="con_fecha_limite"';
    if (tipo_fecha_limite == "con_fecha_limite") {
        row += ' selected="true" ';
    }
    row += '>';
    row += langFrm.con_fecha_limite_planpago;
    row += '</option>';
    row += '<option value="al_momento"';
    if (tipo_fecha_limite == "al_momento"){
        row += ' selected="true" ';
    }
    row += '>';
    row += langFrm.al_momento_de_matricular;
    row += '</option>';
    row += '</select>';
    var complemento = tipo_fecha_limite == "con_fecha_limite" ? "" : ' style="display: none;"';
    row += '<div class="input-group" id="div_fecha_fecha_limite_' + totalFilas + '"' + complemento + '">';
    row += '<input type="text" class="form-control fecha" id="fecha-fecha-limite-' + totalFilas + '" value="' + fecha_limite + '" name="fecha_fecha_limite" placeholder="' + langFrm.fecha_fin + '" ' + complementoEditar + '>';
    row += '<span class="input-group-addon">';
    row += '<i class="icon-calendar bigger-110"></i>';
    row += '</span>';
    row += '</div>';
    row += '</td>';
    //row += '<td>';
    //row += '</td>';
    //row += '<td>';
    //row += '</td>';
    //debugger;
    row += '<td>';
    row += '<div class="fecha-limite-'+totalFilas+'"';
    row += conceptoDefaultSelected == 5 ? 'style="display:none;"' : '';
    row += '>';
//    row += '<select class="form-control" id="tipo-fecha-financiacion-limite-' + totalFilas + '" name="tipo_fecha_financiacion_limte" onclick="validarFechaLimiteFinanciaciones(' + totalFilas + ');" ' + complementoEditar + '>';
//    row += '<option value="sin_fecha_limite"';
//    if (tipo_fecha_financiacion_limite == "sin_fecha_limite"){
//        row += ' selected="true" ';
//    }
//    row += '>';
    //   row += "Sin fecha límite";
//    row += '</option>';
//    row += '<option value="con_fecha_limite"';
//    if (tipo_fecha_financiacion_limite == "con_fecha_limite") {
    //       row += ' selected="true" ';
//    }
//    row += '>';
//    row += 'Con fecha límite';
//    row += '</option>';

//    row += '</select>';
//    var complemento = tipo_fecha_financiacion_limite == "con_fecha_limite" ? "" : ' style="display: none;"';
    row += '<div class="input-group" id="div_fecha_financiacion_limite_' + totalFilas + '">';
    row += '<div class="input-group" id="div_fecha_financiacion_limite_' + totalFilas + '">';
    row += '<input type="text" class="form-control fecha" id="fecha-financiacion-limite-' + totalFilas + '" value="' + fecha_limite_financiacion + '" name="fecha_financiacion_limite" placeholder="' + langFrm.fecha_fin + '" ' + complementoEditar + '>';
    row += '<span class="input-group-addon">';
    row += '<i class="icon-calendar bigger-110"></i>';
    row += '</span>';
    row += '</div>';
    row += '</div>';
    row += '</td>';

    row += '<td>';
    row += '<div class="btn-group" style="float: right;">';
    row += '<button type="button" class="btn btn-sm btn-default btn-detalles" value="' + totalFilas + '" ' + complementoEditar + '>';
    row += langFrm.detalles;
    row += '</button>';
    row += '<button type="button" class="btn btn-danger btn-sm eliminar" value="' + totalFilas + '" ' + complementoEditar + '>';
    row += '<i class="icon-trash bigger-110 icon-only"></i>';
    row += '</button>';
    row += '</div>';
    row += "<input type='hidden' name='eliminado[]' id='eliminado-" + totalFilas + "' value='0'>";
    row += "<input type='hidden' name='detalle' id='detalle-" + totalFilas + "' value='" + detalle + "' >";
    row += '</td>';
    row += '</tr>';
    $('#tablaFinanciaciones').find("tbody").append(row);
    var dia = parseInt($("[name=hd_dia]").val());
    var mes = parseInt($("[name=hd_mes]").val());
    var anio = parseInt($("[name=hd_anio]").val());
    $("#fecha-fecha-limite-" + totalFilas).datepicker({
        cahngeMonth: false,
        changeYear: false,
        dateFormat: 'dd/mm/yy',
        minDate: new Date(anio, mes - 1, dia)
    });

    $("#fecha-financiacion-limite-" + totalFilas).datepicker({
        cahngeMonth: false,
        changeYear: false,
        dateFormat: 'dd/mm/yy',
        minDate: new Date(anio, mes - 1, dia)
    });

    $('#tablaFinanciaciones .btn-detalles').popover({
        placement: 'left',
        title: 'Detalle',
        html: true,
        trigger: 'manual'
    });

    $(".eliminar").click(function() {
        var totalFilas = $('tr', $("#tablaFinanciaciones")).length;
        if (totalFilas > 2) {
            $("#tr-plan-" + $(this).attr("value")).hide("slow");
            $("#eliminado-" + $(this).attr("value")).val(1);
            $("#cuotas-finaciacion-" + $(this).attr("value")).val("-1");
            $("#tr-plan-" + $(this).attr("value")).remove();
            $.fancybox.update();
        }else{
            gritter('No puede borrar la linea, al menos tiene que haber una financiacion');
        }
    });

    $.fancybox.update();
}


function cambiaSelectConcepto()
{
    var puede_modificar = $("[name=puede_modificar]").val() == 1;
    var complementoEditar = puede_modificar ? "" : "disabled='true'";
    $('select[name="concepto_financiacion"]').on('change', function()
    {
        //debugger;
        var fila = $(this).attr('data-fila');
        var concepto = $(this).val();

        if(concepto == 1)
        {
            $('.fecha-limite-'+fila).show();
        }
        else
        {
            $('.fecha-limite-'+fila).hide();
        }
    });
}


function validarFechaLimite(id_fila){
    if ($("#tipo-fecha-limite-" + id_fila).val() == "con_fecha_limite"){
        $("#div_fecha_fecha_limite_" + id_fila).show();
    } else {
        $("#div_fecha_fecha_limite_" + id_fila).hide();
    }
}

function validarFechaLimiteFinanciaciones(id_fila){
    if ($("#tipo-fecha-financiacion-limite-" + id_fila).val() == "con_fecha_limite"){
        $("#div_fecha_financiacion_limite_" + id_fila).show();
    } else {
        $("#div_fecha_financiacion_limite_" + id_fila).hide();
    }
}

function mostrarFechasVigencia(){
    $("[name=div_ver_vigencia_desde_hoy]").hide();
    $("[name=div_ver_fechas_vigencias]").show();
}

function trim(str){
    return str.replace(/^\s*|\s*$/g,"");
}

function precioListaCursoChange(){
    if (trim($("[name=curso_precio_lista]").val()) == ''){
        $("[name=curso_precio_lista]").val("0");
    }
    var valor_curso = $("[name=curso_precio_lista]").val();
    if (separador_decimal == ','){
        valor_curso = valor_curso.replace(/,/g, '.');
    }
    valor_curso = Math.round(valor_curso * 100) / 100;
    if (separador_decimal == ','){
        var valor_curso_mostar = valor_curso.toString() ;
        valor_curso_mostar = valor_curso_mostar.replace(/\./g, ',');
    } else {
        var valor_curso_mostar = valor_curso;
    }
    $("[name=curso_precio_lista]").val(valor_curso_mostar);
    var tr = $("#tablaFinanciaciones").find("tbody").find("tr");
    $.each(tr, function(key, value){
        var concepto = $(value).find("[name=concepto_financiacion]").val();
        if (concepto == 1){
            $(value).find("[name=valorcurso]").val(valor_curso_mostar);
            $(value).find("[name=recargo_financiacion]").val("0");
            var element = $(value).find("[name=recargo_financiacion]");
            resetearDetalle(element);
        }
    });
}

function precioListaMatriculasChange(){
    if (trim($("[name=matricula_precio_lista]").val()) == ''){
        $("[name=matricula_precio_lista]").val("0");
    }
    var valor_curso = $("[name=matricula_precio_lista]").val();
    if (separador_decimal == ','){
        valor_curso = valor_curso.replace(/,/g, '.');
    }
    valor_curso = Math.round(valor_curso * 100) / 100;
    if (separador_decimal == ','){
        var valor_curso_mostar = valor_curso.toString() ;
        valor_curso_mostar = valor_curso_mostar.replace(/\./g, ',');
    } else {
        var valor_curso_mostar = valor_curso;
    }
    $("[name=matricula_precio_lista]").val(valor_curso_mostar);
    var tr = $("#tablaFinanciaciones").find("tbody").find("tr");
    $.each(tr, function(key, value){
        var concepto = $(value).find("[name=concepto_financiacion]").val();
        if (concepto == 5){
            $(value).find("[name=valorcurso]").val(valor_curso_mostar);
            $(value).find("[name=recargo_financiacion]").val("0");
            var element = $(value).find("[name=recargo_financiacion]");
            resetearDetalle(element);
        }
    });
}

function interesChange(element){
    var tr = $(element).closest("tr");
    var concepto = $(tr).find("[name=concepto_financiacion]").val();
    var valor_curso_mostrar = concepto == 1 ? $("[name=curso_precio_lista]").val() : $("[name=matricula_precio_lista]").val();
    if (valor_curso_mostrar == '' || valor_curso_mostrar == "0"){
        gritter("Indique el precio de lista del concepto");
        $(element).val("0");
    } else {
        if (separador_decimal == ','){
            var valor_curso = parseFloat(valor_curso_mostrar.replace(/,/g, '.'));
        } else {
            var valor_curso = parseFloat(valor_curso_mostrar);
        }
        valor_curso = Math.round(valor_curso * 100) / 100;

        var valor_financiacion = $(element).val();
        if (separador_decimal == ','){
            valor_financiacion = parseFloat(valor_financiacion.replace(/,/g, '.'));
        }
        valor_financiacion = Math.round(valor_financiacion * 100) / 100;
        if (valor_financiacion < valor_curso){
            if (separador_decimal == ','){
                valor_curso_mostrar = valor_curso.toString();
                valor_curso_mostrar = valor_curso_mostrar.replace(/\./g, ',');
            }
            $(element).val(valor_curso_mostrar);
        } else {
            if (separador_decimal == ','){
                var valor_financiacion_mostrar = valor_financiacion.toString();
                valor_financiacion_mostrar = valor_financiacion_mostrar.replace(/\./g, ',');
            } else {
                var valor_financiacion_mostrar = valor_financiacion;
            }
            $(element).val(valor_financiacion_mostrar);
            var interes_financiacion = Math.round((valor_financiacion * 100 / valor_curso) * 100) / 100;
            interes_financiacion = interes_financiacion - 100;
            $(tr).find("[name=recargo_financiacion]").val(interes_financiacion);
        }
        resetearDetalle(element);
    }
}

function conceptoChange(element){
    var tr = $(element).closest("tr");
    var concepto = $(element).val();
    var valor_curso = concepto == 1 ? $("[name=curso_precio_lista]").val() : $("[name=matricula_precio_lista]").val();
    var recargo_financiacion = $(tr).find("[name=recargo_financiacion]").val();
    if(recargo_financiacion == 0){
        var valor = valor_curso;
    }else{
        var valor = Math.round((parseFloat(valor_curso) + (valor_curso * recargo_financiacion / 100)) * 100) / 100;
    }
    $(tr).find("[name=valorcurso]").val(valor);
    resetearDetalle(element);
}

function resetearDetalle(element){
    var tr = $(element).closest("tr");
    $(tr).find("[name=detalle]").val("0");
}

function descuentoChange(element){
    var valor_descuento = $(element).val();
    if (isNaN(parseFloat(valor_descuento))){
        $(element).val('');
    } else {
        if (separador_decimal == ','){
            valor_descuento = parseFloat(valor_descuento.replace(/,/g, '.'));
        }
        valor_descuento = Math.round(valor_descuento * 100) / 100;
        if (separador_decimal == ','){
            var valor_descuento_mostrar = valor_descuento.toString();
            valor_descuento_mostrar = valor_descuento_mostrar.replace(/\./g, ',');
        } else {
            var valor_descuento_mostrar = valor_descuento;
        }
        $(element).val(valor_descuento_mostrar);
        resetearDetalle(element);
    }
}


