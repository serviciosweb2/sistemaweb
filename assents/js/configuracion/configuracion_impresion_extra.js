function cerrarFancy() {
    $.fancybox.close(true);
}

function guardarConfiguracionImpresionConTexto() {
    $("btnGuardar").attr("disabled", true);
    var cantidadCopias = $("#impresion_cantidad_copias").val();
    var id_script = $("#script_id").val();
    var texto = $("#impresion_pie_matriculas").val();
    var imprimeCurso = $("#imprimeCurso").is(':checked') ? 1 : 0;
    var imprimeTitulo = $("#imprimeTitulo").is(':checked') ? 1 : 0;
    var localidadForo = $("#localidad_foro").val();
    var mostrar_precio_lista_descuento = $("[name=mostrar_precio_lista_descuento]").is(":checked") ? 1 : 0;
    $.ajax({
        url: BASE_URL + "configuracion/guardar_configuracion_impresion_extra",
        type: 'POST',
        dataType: 'json',
        data: {
            cantidad_copias: cantidadCopias,
            id_script: id_script,
            texto: texto,
            imprimeCurso: imprimeCurso,
            imprimeTitulo: imprimeTitulo,
            localidadForo: localidadForo,
            mostrar_precio_lista_descuento: mostrar_precio_lista_descuento
        },
        success: function(_json) {
            if (_json.error) {
                $("#area_mensajes").html("<div class='alert alert-danger' style='margin-bottom: 0px; padding: 6px 15px'>" + _json.error + "</div>");
            } else {
                cerrarFancy();
            }
            $("#btnGuardar").attr("disabled", false);
        }
    });
}

function guardarConfiguracionFacturacion() {
    $("btnGuardar").attr("disabled", true);
    var cantidadCopias = $("#impresion_cantidad_copias").val();
    var id_script = $("#script_id").val();
    var tamanio_papel = $("#impresion_formato_papel").val();
    var imprime_razon_social = $("#impresion_imprimir_razon").is(":checked") ? 1 : 0;
    var muestra_cuotas_total = $("#impresion_muestra_cantidad_total_cuotas").is(":checked") ? 1 : 0;
    var modelo_factura_electronica = $("#modelo_factura_electronica").val();
    var mostrar_ruc = '-1';
    var mostrar_com = '-1';

    if ($("#impresion_muestrar_ruc").length > 0){
        mostrar_ruc = $("#impresion_muestrar_ruc").is(":checked") ? 1 : 0;
    }
    if ($("#impresion_muestrar_com").length > 0){
        mostrar_com = $("#impresion_muestrar_com").is(":checked") ? 1 : 0;
    }
    $.ajax({
        url: BASE_URL + "configuracion/guardar_configuracion_impresion_extra",
        type: 'POST',
        dataType: 'json',
        data: {
            cantidad_copias: cantidadCopias,
            id_script: id_script,
            tamanio_papel: tamanio_papel,
            imprime_razon_social: imprime_razon_social,
            muestra_cuotas_total: muestra_cuotas_total,
            modelo_factura_electronica: modelo_factura_electronica,
            mostrar_ruc: mostrar_ruc,
            mostrar_com: mostrar_com
        },
        success: function(_json) {
            if (_json.error) {
                $("#area_mensajes").html("<div class='alert alert-danger' style='margin-bottom: 0px; padding: 6px 15px'>" + _json.error + "</div>");
            } else {
                cerrarFancy();
            }
            $("#btnGuardar").attr("disabled", false);
        }
    });
}

function guardarConfiguracionExtra() {
    $("#btnGuardar").attr("disabled", true);
    var cantidadCopias = $("#impresion_cantidad_copias").val();
    var id_script = $("#script_id").val();
    var imprime_razon_social = -1;
    if ($("#impresion_imprimir_razon").length == 1){
        imprime_razon_social = $("#impresion_imprimir_razon").is(":checked") ? 1 : 0;
    }    
    $.ajax({
        url: BASE_URL + "configuracion/guardar_configuracion_impresion_extra",
        type: 'POST',
        dataType: 'json',
        data: {
            cantidad_copias: cantidadCopias,
            id_script: id_script,
            imprime_razon_social: imprime_razon_social
        },
        success: function(_json) {
            if (_json.error) {
                $("#area_mensajes").html("<div class='alert alert-danger' style='margin-bottom: 0px; padding: 6px 15px'>" + _json.error + "</div>");
            } else {
                cerrarFancy();
            }
            $("#btnGuardar").attr("disabled", false);
        }
    });
}

$('select[name="provincia_foro"]').on('change', function() {
    var valor = $(this).val();
    $.ajax({
        url: BASE_URL + 'alumnos/getlocalidades',
        data: 'idprovincia=' + valor,
        cache: false,
        type: 'POST',
        dataType: 'json',
        success: function(respuesta) {
            $('select[name="localidad_foro"]').empty();
            $(respuesta).each(function() {
                $('select[name="localidad_foro"]').append('<option value=' + this.id + '>' + this.nombre + '</option>');
            });
            $("select[name='localidad_foro']").trigger("chosen:updated");
        }
    });
});