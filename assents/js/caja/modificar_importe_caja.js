function guardar_importe_movimiento(cod_movimiento_caja){
    var importe = $("[name=importe_movimiento_caja]").val();
    if (importe == ''){
        gritter(langFrm.debe_especificar_el_importe_del_movimiento_de_caja, false, " ");
    } else {
        $.ajax({
            url: BASE_URL + 'caja/modificar_importe_movimiento_caja',
            type: 'POST',
            dataType: 'json',
            data:{
                importe: importe,
                cod_movimiento_caja: cod_movimiento_caja
            },
            success: function(_json){
                if (_json.error){
                    gritter(_json.error, false, " ");
                } else {
                    $.fancybox.close();
                }
            }
        });
    }
}