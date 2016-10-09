var oTableCaja = new Array();
var aoData = '';
var lang = BASE_LANG;
var cod_movimiento_caja = 0;

$(document).ready(function(){
    init();
});

function cargarTabla(llamado){
    if(!oTableCaja[cajaEnFocus]){
        oTableCaja[cajaEnFocus] = $('#tabla-'+cajaEnFocus).DataTable({
            "bServerSide": true,
            "aaSorting": [[ 0, "desc" ]],
            "sAjaxSource": BASE_URL + 'caja/listar',
            "sServerMethod": "POST",
            'aoColumnDefs':columns,
            "fnServerData": function(sSource, aoData, fnCallback) {
                aoData.push({
                    name: "codigo_caja",
                    value:  cajaEnFocus
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
                        var str = '';
                        $("[name=div_detalle_saldos]").html("");
                        if (_json.caja.estado == 'abierta'){
                            var str = '<table>';
                            str += '<thead>';
                            str += '<tr>';
                            str += '<th>&nbsp;</th>';
                            str += '<th style="text-align: center;">';
                            str += lang.APERTURA;
                            str += '</th>';
                            str += '<th style="text-align: center;">';
                            str += lang.saldo;
                            str += '</th>';
                            str += '</tr>';
                            str += '</thead>';
                            $.each(_json.saldos, function(index, value){
                                str += '<tr>';
                                str += '<td style="font-weight: bold">';
                                str += '<b>';
                                str += value.nombre_medio;
                                str += '</b>';
                                str += '</td>';
                                str += '<td style="padding-left: 12px; padding-right: 12px; text-align: center;">';
                                str += value.simbolo_moneda;
                                str += value.saldo_apertura;
                                str += '</td>';
                                str += '<td style="padding-left: 12px; padding-right:12px; text-align: center;">';
                                str += value.simbolo_moneda;
                                str += value.saldo_concepto_formateado;
                                str += '</td>';
                                str += '</tr>';
                            });
                        } else {
                            str += lang.caja_cerrada;
                        }
                        $("#totales-" + cajaEnFocus).html(str);
                        fnCallback(_json);
                    }
                });
            },
            fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                var codigo = aData[6];
                var _html = '<input type="hidden" name="hd_cod_caja" value="' + codigo + '">';
                $('td:eq(0)', nRow).html(_html + aData[0]);
                return nRow;
            }
        });        
        $('#tabla-'+cajaEnFocus).wrap('<div class="table-responsive"></div>');
        //$('#tabla-'+cajaEnFocus)
        $('.tabbable').on('mousedown', '#tabla-'+cajaEnFocus + ' tbody tr', function(e) {
            if (e.button === 2) {
                generalContextMenu(menu.contextual, e);
                cod_movimiento_caja = $(this).find("[name=hd_cod_caja]").val();
                return false;
            }
        });
    } else {
        oTableCaja[cajaEnFocus].draw();
    }
    
    if(llamado){
        llamado();
    }    
    return true;
}

function mostrarTab(myTab){
    $(myTab).tab('show');
}

function init(){
    if(getCajasTotales() == 0){
        $('#msgSinCajasAsignadas').show();
    } else {        
        $('#myTab a').click(function (e){
            var element = this;
            cajaEnFocus = $(this).attr('data-tabla');
            cargarTabla(function(){ 
                mostrarTab(element);
            });
            return false;
        });    
        cargarTabla();
        $(".select-chosen").chosen();
        $(document).on("click", ".transferencia_enabled", function(){
            transferirCaja();
        });
        checkButtons();        
        if(cajaEnFocus == -1){           
            $('#myTab a').eq(0).trigger('click');
        }
    }
}

function getCajasAbiertas(){
   var cajasAbiertas = $('#myTab').find('li[estado="abierta"]');   
    return cajasAbiertas.length;
}

function getCajasTotales(){
   var cajasTotales = $('#myTab').find('li'); 
   return cajasTotales.length;   
}

function checkButtons(){
    var cajas_abiertas = getCajasAbiertas();    
    var cajas = $('#myTab').find('li');     
    $(cajas).each(function(k,caja){
        var href = $(caja).find('a').attr('href');      
        var estado = $(caja).attr('estado');
        if(estado == 'cerrada'){
            $(href).find('.boton-primario').prop('disabled',true);
            $(href).find("[name=li_transferencia]").addClass("transferencia_disabled");
            $(href).find("[name=a_transferencia]").addClass("transferencia_disabled");
        } else {
            $(href).find('.boton-primario').prop('disabled',false);
            $(href).find('[name=li_transferencia]').removeClass().addClass("transferencia_disabled");
            $(href).find("[name=a_transferencia]").removeClass().addClass("transferencia_disabled");
            if(cajas_abiertas > 0){
                $(href).find('[name=li_transferencia]').removeClass().addClass("transferencia_enabled");
                $(href).find("[name=a_transferencia]").removeClass().addClass("transferencia_enabled");
            }             
        }
    });
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
                    oTableCaja[cajaEnFocus].draw();                   
                }
            });
        }
    });
}

function frmCerrarCajas(){
    var codigo_caja = cajaEnFocus;
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
                scrolling: 'auto',
                padding: 0,
                openEffect: 'none',
                closeEffect: 'none',
                helpers: {
                    overlay: null
                }, beforeClose: function() {
                    oTableCaja[cajaEnFocus].draw();
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
    var codigo_caja = cajaEnFocus;
    var nombre_caja = $('#myTab .active a').text();
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
                    oTableCaja[cajaEnFocus].draw();
                }
            });
        }
    });
}

function transferirCaja(){
    var caja_salida = cajaEnFocus;
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
                    oTableCaja[cajaEnFocus].draw();
                }
            });
        }
    });
}

function recargarTabla(){
    oTableCaja[cajaEnFocus].draw();
}

function cambiarVista_Abierta_Cerrada(caja,cambiar_a){
    var aTabContent = $('a[href="#caja_'+caja+'"]');
    var tabContent = $('#caja_'+caja);    
    if(cambiar_a == 'abierta'){         
        tabContent.find('.btnAbrir').hide();
        tabContent.find('.btnCerrar').show();
        aTabContent.closest('li').find('i').removeClass().addClass('icon-unlock green');
        aTabContent.closest('li').attr('estado','abierta');
        tabContent.find('.boton-primario').prop('disabled',true);         
    } else {        
        tabContent.find('.btnAbrir').show();
        tabContent.find('.btnCerrar').hide();
        aTabContent.closest('li').find('i').removeClass().addClass('icon-lock');
        aTabContent.closest('li').attr('estado','cerrada');
        tabContent.find('.boton-primario').prop('disabled',false);
    }
    checkButtons();
}

 $('body').on('click', '#menu a', function(){
    var accion = $(this).attr('accion');
    $('#menu').remove();
    switch (accion) {
        case "editar_movimiento_caja":
//            alert("se va a modificar el registro de caja " + cod_movimiento_caja);
            $.ajax({
                url: BASE_URL + 'caja/editar_movimiento',
                type: "POST",
                data: {
                    cod_movimiento_caja: cod_movimiento_caja
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
                            oTableCaja[cajaEnFocus].draw();
                        }
                    });
                }
            });
            break;
    }
});