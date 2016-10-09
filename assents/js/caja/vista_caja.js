var oTableCaja = '';
var aoData = '';
var claves = Array("BIEN",
            "ERROR",
            "cerrada"
        );
var lang = BASE_LANG;

$(document).ready(function(){
    init();
});

function init(){
    $(".select-chosen").chosen();
    $(document).on("click", ".transferencia_enabled", function(){
        transferirCaja();
    });
    oTableCaja = $('#detalle_caja').dataTable({
        
        "bServerSide": true,
        "aaSorting": [[ 0, "desc" ]],
        "sAjaxSource": BASE_URL + 'caja/listar',
        "sServerMethod": "POST",
        'aoColumnDefs':columns,
        "fnServerData": function(sSource, aoData, fnCallback) {
            aoData.push({
                name: "codigo_caja",
                value:  $("[name=cajas_abiertas]").val()             
            });
            aoData.push({
                name: "estado_caja",
                value: "abierta"
            });
            $.ajax({
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": aoData,
                "async": false,
                "success": function(_json){
                    $("[name=div_detalle_saldos]").html("");
                    if (_json.iTotalRecords > 0){
                        var str = '';
                        $.each(_json.saldos, function(index, value){
                            str += '<div class="row right">';            
                            str += '<div class="col-md-2 ">';
                            str += '<h4>';
                            str += value.nombre_medio;
                            str += '</h4>';
                            str += '</div>';
                            str += '<div class="col-md-2">';
                            str += '<h4>';
                            str += value.simbolo_moneda;
                            str += value.saldo_concepto_formateado;
                            str += "</h4>";
                            str += '</div>';
                            str += '</div>';
                        });
                        $("[name=div_detalle_saldos]").html(str);
                    }
                    fnCallback(_json);
                }
            });
        }
    });    
}

function checkButtons(){
    var cajas_totales = $("[name=cajas_totales]").val();
    var cajas_abiertas = $('[name=cajas_abiertas] option#abierta').size();
    $("[name=btn_abrir_caja]").attr("disabled", cajas_totales == cajas_abiertas);
    $("[name=btn_cerrar_caja]").attr("disabled", cajas_abiertas == 0);
    if (cajas_abiertas >= 2){
        $("[name=li_transferencia]").removeClass("transferencia_disabled");
        $("[name=li_transferencia]").addClass("transferencia_enabled");
        $("[name=a_transferencia]").removeClass("transferencia_disabled");
    } else {
        $("[name=li_transferencia]").removeClass("transferencia_enabled");
        $("[name=li_transferencia]").addClass("transferencia_disabled");
        $("[name=a_transferencia]").addClass("transferencia_disabled");
    }
    if ($("[name=cajas_abiertas]").find("option:selected").attr("id") == "cerrada"){
        $("[name=btn_abrir_caja]").show();
        $("[name=btn_cerrar_caja]").hide();
        $("[name=btn_agregar_nuevo_movimiento]").attr("disabled", true);
    } else {
        $("[name=btn_abrir_caja]").hide();
        $("[name=btn_cerrar_caja]").show();
        $("[name=btn_agregar_nuevo_movimiento]").attr("disabled", false);
    }
}

function frmAbrirCajas(){
    $.ajax({
        url: BASE_URL + 'caja/frm_abrir_caja',
        success: function(_html){
            $.fancybox.open(_html, {
                width: 'auto',
                height: 'auto',
                scrolling: true,
                padding: 0,
                openEffect: 'none',
                closeEffect: 'none',
                helpers: {
                    overlay: null
                }, beforeClose: function() {
                    oTableCaja.fnDraw();
                }
            });
        }
    });
}

function frmCerrarCajas(){
    checkButtons();
    var codigo_caja = $("[name=cajas_abiertas]").val();
    $.ajax({
        url: BASE_URL + 'caja/frm_cerrar_caja',
        type: 'POST',
        data: {
            codigo_caja: codigo_caja
        },
        success: function(_html){
            $.fancybox.open(_html, {
                width: 'auto',
                height: 'auto',
                scrolling: true,
                padding: 0,
                openEffect: 'none',
                closeEffect: 'none',
                helpers: {
                    overlay: null
                }, beforeClose: function() {
                    oTableCaja.fnDraw();
                }
            });
        }
    });
}

function setSelectCajasAbiertas(_json){
    $("[name=cajas_abiertas]").find('option').remove();
    var caja_abierta = _json.caja_abierta;
    $.each(_json.cajas, function(index, value){
        var complemento = value['codigo'] == caja_abierta ? "selected='true'" : "";
        var str = "<option value='" + value['codigo'] + "' " + complemento + " class='caja_" + value['estado'] + "' id='" + value['estado']  +"'>";
        str += value['nombre'];
        if (value['estado'] == "cerrada"){
            str += "(" + lang.cerrada + ")";
        }
        str += "</option>";
        $("[name=cajas_abiertas]").append(str);
    });
    $("[name=cajas_abiertas]").attr("disabled", false);
    $("[name=cajas_abiertas]").trigger("chosen:updated");
    checkButtons();
}

function nuevoMovimientoCaja(){
    var codigo_caja = $("[name=cajas_abiertas]").val();
    var nombre_caja = $("[name=cajas_abiertas] option:selected" ).text();
    $.ajax({
        url: BASE_URL + 'caja/frm_nuevo_movimiento',
        type: 'POST',
        data: {
            codigo_caja: codigo_caja,
            nombre_caja: nombre_caja
        },
        success: function(_html){
            $.fancybox.open(_html, {
                width: 'auto',
                height: 'auto',
                scrolling: true,
                padding: 0,
                openEffect: 'none',
                closeEffect: 'none',
                helpers: {
                    overlay: null
                }, beforeClose: function() {
                    oTableCaja.fnDraw();
                }
            });
        }
    });
}

function transferirCaja(){
    var caja_salida = $("[name=cajas_abiertas]").val();
    $.ajax({
        url: BASE_URL + 'caja/frm_transferencia_cajas',
        type: 'POST',
        data: {
            caja_salida: caja_salida
        },
        success: function(_html){
            $.fancybox.open(_html, {
                width: 'auto',
                height: 'auto',
                scrolling: true,
                padding: 0,
                openEffect: 'none',
                closeEffect: 'none',
                helpers: {
                    overlay: null
                }, beforeClose: function() {
                    oTableCaja.fnDraw();
                }
            });
        }
    });
}

function recargarTabla(){
    oTableCaja.fnDraw();
}