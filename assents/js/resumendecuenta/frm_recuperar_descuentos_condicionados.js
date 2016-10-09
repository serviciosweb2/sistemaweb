$(document).ready(function(){
    $("#table_descuentos_condicionados_recuperar").dataTable({
        bLengthChange: false
    });
});

function recuperarDescuentoCondicionado(cod_matricula){
    var cod_ctacte_inicio = $("[name=recuperar_desde_cuota]").val();
    $.ajax({
        url: BASE_URL + 'ctacte/recuperar_descuento_condicionado',
        type: 'POST',
        dataType: 'json',
        data: {
            cod_matricula: cod_matricula,
            cod_ctacte_inicio: cod_ctacte_inicio
        },
        success: function(_json){
            if (_json.error){
                gritter(_json.error);
            } else {
                gritter(langFrm_recuperar_descuentos_condicionados.validacion_ok, true);
                $("#table_descuentos_condicionados_perdidos").find("#tr_" + cod_matricula).empty();
                $.fancybox.close();
            }
        }
    });
}