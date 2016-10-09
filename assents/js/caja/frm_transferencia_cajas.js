var frmTransferencia = langFrm;
//var claves = Array(
//            "la_caja_de_origen_debe_diferir_de_la_caja_destino",
//            "debe_ingresar_un_importe_valido",
//            "el_importe_debe_ser_mayor_a_cero",
//            "debe_especificar_la_descripcion_para_la_transferencia",
//            "transferencia_realizada_correctamente"
//        );

$($("[name=frm_transferencia_cajas]")).ready(function(){
//    $.ajax({
//        url: BASE_URL + 'entorno/getLang',
//        data: "claves=" + JSON.stringify(claves),
//        dataType: 'JSON',
//        type: 'POST',
//        cache: false,
//        async: false,
//        success: function(respuesta) {
//            //frmTransferencia = respuesta;
//            $(".select-chosen").chosen();
//        }
//    }); 
   
     $(".select-chosen").chosen();
   
});

function guardarTransferencia(){
    var caja_origen = $("[name=transferencia_caja_origen]").val();
    var caja_destino = $("[name=transferencia_caja_destino]").val();
    var importe = $.trim($("[name=transferencia_importe]").val());
    var descripcion = $.trim($("[name=transferencia_descripcion]").val());
    var medio_pago = $("[name=transferencia_medio_pago]").val();
    var mensaje = '';
    if (caja_origen == caja_destino) mensaje += frmTransferencia.la_caja_de_origen_debe_diferir_de_la_caja_destino + "<br>";
    if (importe == '' || isNaN(parseFloat(importe)))
        mensaje += frmTransferencia.debe_ingresar_un_importe_valido + '<br>';
    else if (parseFloat(importe) == 0)
        mensaje += frmTransferencia.el_importe_debe_ser_mayor_a_cero + "<br>";
    if (descripcion == '') mensaje += frmTransferencia.debe_especificar_la_descripcion_para_la_transferencia + '<br>';
    if (mensaje != ''){
        gritter(mensaje, false);
    } else {
        $.ajax({
            url: BASE_URL + 'caja/guardar_transferencia',
            type: 'POST',
            dataType: 'json',
            data: {
                caja_origen: caja_origen,
                caja_destino: caja_destino,
                importe: importe,
                descripcion: descripcion,
                medio_pago: medio_pago
            },
            success: function(_json){
                if (_json.codigo == 0){
                    gritter(_json.respuesta, false);
                } else {
                    gritter(frmTransferencia.transferencia_realizada_correctamente, true);
                    $.fancybox.close();
                }
            }
        });
    }
}