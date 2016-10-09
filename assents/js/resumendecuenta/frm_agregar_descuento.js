$(".fancybox-wrap").ready(function(){
    $("#ctacteDescuentos").dataTable();
    $(".select_chosen").chosen( { "width": '100%'});
    $("#ctacteDescuentos_filter").hide();
    $("#ctacteDescuentos_length").hide();
});

function guardarDescuentoCondicionado(){
    $("[name=btn_guardar_descuento]").prop("disabled", true);
    var valor_final = _calcular_total();
    var mensaje = '';
    if (valor_final < 0){
        mensaje += langFrm_agregar_descuento.el_descuento_condicionado_es_mayor_que_el_total + "<br>";
    }
    var forma_descuento = $("[name=forma_descuento]").val();
    var dias_vencida = $("[name=dias_vencida]").val();
    var valor = $("[name=valor_descuento]").val();
    var tipo_descuento = $("[name=tipo_descuento]").val();
    if (tipo_descuento == 'condicionado' && dias_vencida == ''){
        mensaje += langFrm_agregar_descuento.dias_de_vencida_es_quererido + "<br>";
    }
    if (parseFloat(valor) == 0){
        mensaje += langFrm_agregar_descuento.valor_no_puede_ser_cero + "<br>";
    }
    if (mensaje != ''){
        gritter(mensaje);
        $("[name=btn_guardar_descuento]").prop("disabled", false);
    } else {
        $.ajax({
            url: BASE_URL + 'ctacte/guardar_descuento',
            type: 'POST',
            dataType: 'json',
            data:{
                forma_descuento: forma_descuento,
                valor: valor,
                tipo_descuento: tipo_descuento,
                dias_vencida: dias_vencida,
                cod_ctacte: cod_ctacte
            },
            success: function(_json){
                if (_json.error){
                    gritter(_json.error);
                } else {
                    gritter(langFrm_agregar_descuento.validacion_ok,true);
                    $.fancybox.close();
                    getCtaCte();
                }
            }
        });
    }
}

function tipo_descuento_change(element){
    if ($(element).val() == "condicionado"){
        $("#div_dias_vencida").show();
    } else {
        $("#div_dias_vencida").hide();
    }
}

function calcular_total(){
    var valor_final = _calcular_total();
    if (valor_final < 0){
        gritter(langFrm_agregar_descuento.el_descuento_condicionado_es_mayor_que_el_total);
        $("#vista_importe_final").html("---");
    } else {
        valor_final = valor_final.toString();
        var valor_mostrar = valor_final.replace(/\./g, ',');
        $("#vista_importe_final").html(valor_mostrar);
    }
}

function _calcular_total(){
    var forma_descuento = $("[name=forma_descuento]").val();
    var valor = $("[name=valor_descuento]").val();
    if (separador_decimal != '.'){
        valor = eval("valor.replace(/" + separador_decimal + "/g, '.')");
    }
    valor = Math.round(valor * 100) / 100;
    $("[name=valor_descuento]").val(valor);
    var valor_final = 0;
    if (forma_descuento == "importe"){
        valor = valor * 100 / importe_total;
        valor = Math.round(valor * 10000) / 10000;
    }
    var valor_final = importe_total - (importe_total * valor / 100);
    valor_final = Math.round(valor_final * 100) / 100;
    return valor_final;
}