clavesFrm_cuentas_bancarias = Array("OK","ERROR", "error_al_guardar_la_cuenta_bancaria", "todos_los_datos_son_obligatorios", "validacion_ok",
                    "conta", "carteira");

langFrm_cuentas_bancarias = '';

$("[name=frm_cuentas_bancarias]").ready(function(){
    $.ajax({
        url: BASE_URL + 'entorno/getLang',
        data: "claves=" + JSON.stringify(clavesFrm_cuentas_bancarias),
        type: "POST",
        dataType: "JSON",
        async: false,
        cache: false,
        success: function(respuesta) {
            langFrm_cuentas_bancarias = respuesta;
            init();
        }
    });
});

function trim(str){
    return str.replace(/^\s*|\s*$/g,"");
}    

function init(){
    $("[name=btn_guardar]").on("click", function(){
        var agencia = trim($("#agencia").val());
        var conta = trim($("#conta").val());
        var contrato = trim($("#contrato").val());
        var formato_convenio = trim($("#formatacao_convenio").val());
        var formatacao_nosso_numero = trim($("#formatacao_nosso_numero").val());
        var identificacao = trim($("#identificacao").val());
        var cod_facturante = $("#cod_razon_social").val();
        var cod_configuracion = $("#cod_configuracion").val();
        var numero_secuencia = trim($("#numero_secuencia").val());
        var cantidad_copias = trim($("#cantidad_copias").val());
        var convenios = trim($("#convenio").val());
        var carteira = trim($("#carteira").val());
        var variacao_carteira = trim($("#variacao_carteira").val());
        var demostrativo1 = trim($("#demostrativo1").val());
        var demostrativo2 = trim($("#demostrativo2").val());
        var demostrativo3 = trim($("#demostrativo3").val());
        var instrucciones1 = trim($("#instrucciones1").val());
        var instrucciones2 = trim($("#instrucciones2").val());
        var instrucciones3 = trim($("#instrucciones3").val());
        var instrucciones4 = trim($("#instrucciones4").val());
        var datos_completos = true; // validar
        if (agencia == '') datos_completos = false;
        if (conta == '') datos_completos = false;
        if (contrato == '') datos_completos = false;
        if (formato_convenio == '') datos_completos = false;
        if (formatacao_nosso_numero == '') datos_completos = false;
        if (identificacao == '') datos_completos = false;
        if (!cod_facturante) datos_completos = false;
        if (cod_configuracion == '') datos_completos = false;
        if (numero_secuencia == '') datos_completos = false;
        if (cantidad_copias == '') datos_completos = false;
        if (convenios == '') datos_completos = false;
        if (carteira == '') datos_completos = false;
        if (variacao_carteira == '') datos_completos = false;

        if (datos_completos){
            $.ajax({
                url: BASE_URL + "configuracion/guardar_cuenta_bancaria",
                type: 'POST',
                dataType: 'json',
                data: {
                    agencia: agencia,
                    conta: conta,
                    contrato: contrato,
                    formato_convenio: formato_convenio,
                    formato_nosso_numero: formatacao_nosso_numero,
                    identificacao: identificacao,
                    cod_facturante: cod_facturante,
                    cod_configuracion: cod_configuracion,
                    numero_secuencia: numero_secuencia,
                    cantidad_copias: cantidad_copias,
                    convenio: convenios,
                    carteira: carteira,
                    variacao_carteira: variacao_carteira,
                    cod_banco: 1,
                    demostrativo1: demostrativo1,
                    demostrativo2: demostrativo2,
                    demostrativo3: demostrativo3,
                    instrucciones1: instrucciones1,
                    instrucciones2: instrucciones2,
                    instrucciones3: instrucciones3,
                    instrucciones4: instrucciones4
                },
                success: function(_json){
                    if (_json.error){
                        gritter(langFrm_cuentas_bancarias.error_al_guardar_la_cuenta_bancaria, false, langFrm_cuentas_bancarias.ERROR);
                    } else {
                        gritter(langFrm_cuentas_bancarias.validacion_ok, true);
                        if (cod_configuracion == -1){
                            var _html = '';
                            _html += '<div class="row">';
                            _html += '<div class="col-md-12">';
                            _html += '<a onclick="editarCuentaBancaria(' + _json.codigo_banco + ', ' + _json.codigo_cuenta + ', ' + _json.cartera + ');" style="cursor: pointer;">';
                            _html += _json.nombre_banco + " " + langFrm_cuentas_bancarias.conta + " " + _json.cuenta + " " + langFrm_cuentas_bancarias.carteira + " " + _json.cartera;
                            _html += '</a>';
                            _html += '<label>';
                            _html += '<input class="ace ace-switch ace-switch-6" type="checkbox" onclick="cambiarEstadoCuenta(' + _json.codigo_banco + ', ' + _json.codigo_cuenta + ', this);" name="cuenta_habilitada" checked="">';
                            _html += '<span class="lbl"></span>';
                            _html += '</label>';
                            _html += '</div>';
                            _html += '</div>';
                            $("#div_descripcion_cuentas").append(_html);
                        }
                        $.fancybox.close();                        
                    }
                }
            });
        } else {
            gritter(langFrm_cuentas_bancarias.todos_los_datos_son_obligatorios, false, langFrm_cuentas_bancarias.ERROR);
        }
    });
    
    $("[name=codigo_banco]").on("change", function(){
        var cod_banco = $(this).val();
        j.ajax({
            url: BASE_URL + 'configuracion/cargar_vista_banco',
            type: 'POST',
            data: {
                cod_banco: cod_banco
            },
            success: function(_html){
                $("[name=div_area_cuenta_bancaria]").html(_html);
            }
        });
    });    
}