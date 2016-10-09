//claves_punto_baja = Array("validacion_ok", "ERROR", "datos_actualizados_correctamente", "error_al_actualizar_los_puntos_de_venta");
frm_punto_venta = langFrm;

$('[name=frm_punto_venta]').ready(function(){
//    $.ajax({
//        url: BASE_URL + 'entorno/getLang',
//        data: "claves=" + JSON.stringify(claves_punto_baja),
//        type: "POST",
//        dataType: "JSON",
//        async: false,
//        cache: false,
//        success: function(respuesta) {
//            frm_punto_venta = respuesta;
//            init();
//        }
//    });
    
    init();
    
    
});

function init(){
    $("[name=btn_guardar_punto_venta]").on("click", function(){
        var proximo_numero = $("[name=proximo_numero]").val();
        var activo = $("[name=activo]").prop("checked") ? 1 : 0;
        var usuarios = $("[name=usuarios_habilitados]");
        var usuarios_permisos = new Array();
        var codigo = $("[name=codigo_punto_venta]").val();
        for (var i = 0; i < usuarios.length; i++){
            usuarios_permisos.push({
                cod_usuario: $(usuarios[i]).val(),
                usuario_habilitado: $(usuarios[i]).prop("checked") ? 1 : 0
            });
        }
        $.ajax({
            url: BASE_URL + 'configuracion/guardarPuntoVenta',
            type: 'POST',
            dataType: 'json',
            data: {
                codigo:codigo,
                proximo_numero: proximo_numero,
                activo: activo,
                usuarios_permisos: usuarios_permisos
            },
            success: function(_json){
                if (_json.error){
                    gritter(frm_punto_venta.error_al_actualizar_los_puntos_de_venta, false, frm_punto_venta.ERROR);
                } else {
                    $.fancybox.close();
                    tablaPuntosVentas("activo");
                    tablaPuntosVentas("inactivo");                    
                    gritter(frm_punto_venta.datos_actualizados_correctamente, true, frm_punto_venta.validacion_ok);
                }
            }
        });
    });
}