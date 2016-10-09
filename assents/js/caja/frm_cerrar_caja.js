var frmCerrarCaja = '';

var claves = Array(
            "importe_ingresado_no_valido_para_el_medio",
            "caja_cerrada_correctamente"
        );

$($("[name=frm_cerrar_caja]")).ready(function(){
    frmCerrarCaja =  langFrm;
});

function cerrarCaja(){
    var codigo_caja = cajaEnFocus;
    var valor_medios = $("[name=valor_caja]");
    var dataPost = new Array();
    var mensaje = '';
    var arqueo = false;
    for (var i = 0; i < valor_medios.length; i++){
        var valor_original = valor_medios[i].value.replace(BASE_SEPARADOR,'.');
        var codigo_medio = valor_medios[i].id;
        var valor_ingresado = $("[name=valor_caja_" + codigo_medio + "]").val();
        if (valor_ingresado === '' || isNaN(parseFloat(valor_ingresado))){
            mensaje += frmCerrarCaja.importe_ingresado_no_valido_para_el_medio + " " + codigo_medio + "<br>";
        } else {
            dataPost.push({
                saldo_debe: valor_ingresado,
                codigo_medio: codigo_medio,
                valor_original: valor_original
            });
            var valor_ingresado_formateado = valor_ingresado.replace(',','.');
            if (parseFloat(valor_original) != parseFloat(valor_ingresado_formateado)){
                arqueo = true;
            }
        }
    }
    if (mensaje == ''){
        if (arqueo){
            $("[name=cancelar_preguntar_movimiento]").show();
            $("[name=aceptar_agregar_movimiento]").show();
            $("#div_preguntar_arqueo_caja").show();
            $("#div_cerrar_cajas").hide();
            $("[name=cerrar_caja]").hide();
        } else {
            $.ajax({
                url: BASE_URL + 'caja/cerrarCaja',
                type: 'POST',
                dataType: 'json',
                data: {
                    codigo_caja: codigo_caja,
                    valores_medios: dataPost
                },
                success: function(_json){
                    if (_json.codigo == 0){
                        gritter(_json.msgError, false);
                    } else {
                        gritter(frmCerrarCaja.caja_cerrada_correctamente, true);
                        $.fancybox.close();
                        cambiarVista_Abierta_Cerrada(codigo_caja,'cerrada');
                    }
                }
            });
        }
    } else {
        gritter(mensaje, false);
    }
}

function canclearAgregarMovimiento(){
    $("[name=cancelar_preguntar_movimiento]").hide();
    $("[name=aceptar_agregar_movimiento]").hide();
    $("#div_preguntar_arqueo_caja").hide();
    $("#div_cerrar_cajas").show();
    $("[name=cerrar_caja]").show();
}

function agregarMovimientoCaja(){
    nuevoMovimientoCaja();
}