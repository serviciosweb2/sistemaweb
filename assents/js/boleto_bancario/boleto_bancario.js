var oTableBoleto = '';
var oTableGenerar = '';
var lang = BASE_LANG;
var codigo_boleto_imprimir = '';
var totalBoletos = 0;
menu = menuJson;
var claves = Array(
        "validacion_ok", "debe_seleccionar_al_menos_un_items_de_cuenta_corriente", "emitir_boletos", "transferencia_de_archivos",
        "volver", "baja_de_boletos", "debe_seleccionar_algun_boleto_para_dar_de_baja", "validacion_ok",
        "debe_seleccionar_al_menos_un_items_de_cuenta_corriente", "emitir_boletos", "transferencia_de_archivos",
        "volver", "no_tiene_permiso"
    );

$(document).ready(function() {    
    init();   
});

//Memoria de la tabla, para que no se rompa entre distintas paginas.
//La declaro dentro de window, para que no quede undefined.
var ctacte = [];

function checkearCtaCte(id){
    if($('#cb-ctacte-' + id).prop('checked')){
        if(ctacte.indexOf(id) <= -1){
            ctacte.push(id);
        }
    } else {
        if(ctacte.indexOf(id) > -1){
            ctacte = ctacte.filter(function(elemento){
                return elemento != id;
            }); 
        }
    }
}



function init() {
    var _tfoot = "<tfoot>";
    _tfoot += '<tr>';
    _tfoot += '<td colspan="5" style="text-align: right;">';
    _tfoot += 'Total';
    _tfoot += '</td>';
    _tfoot += '<td id="foot_totales">';
    _tfoot += '0.00';
    _tfoot += '</td>';
    _tfoot += '</tr>';
    _tfoot += '</table>';
    $("#boletosBancarios").append(_tfoot);
    if(typeof emitir === "undefined"){
        oTableBoleto = $('#boletosBancarios').dataTable({
            "bServerSide": true,
            "aaSorting": [[0, "desc"]],
            "sAjaxSource": BASE_URL + "boletos/getBoletosDataTable",
            "sServerMethod": "POST",
             "aoColumns": [{"bSortable": false},
                    null, 
                    null,
                    null,
                    {"bSortable": false},
                    null,
                    null,
                    null,
                    null],
            "fnServerData": function(sSource, aoData, fnCallback) {
                totalBoletos = 0;
                $("#boletosBancarios").find("tfoot").find("td#foot_totales").html("0.00");
                var fecha_vencimiento_desde = $(".filtro_opciones").find("[name=filtro_vencimiento_fecha_desde]").val();
                var fecha_vencimiento_hasta = $(".filtro_opciones").find("[name=filtro_vencimiento_fecha_hasta]").val();
                var fecha_emision_desde = $(".filtro_opciones").find("[name=filtro_emision_fecha_desde]").val();
                var fecha_emision_hasta = $(".filtro_opciones").find("[name=filtro_emision_fecha_hasta]").val();
                var estado = $(".filtro_opciones").find("[name=filtro_estado]").val();
                aoData.push({name: "fecha_vencimiento_desde", value: fecha_vencimiento_desde});
                aoData.push({name: "fecha_vencimiento_hasta", value: fecha_vencimiento_hasta});
                aoData.push({name: "fecha_emision_desde", value: fecha_emision_desde});
                aoData.push({name: "fecha_emision_hasta", value: fecha_emision_hasta});
                aoData.push({name: "estado", value: estado});
                $.ajax({
                    "dataType": 'json',
                    "type": "POST",
                    "url": sSource,
                    "data": aoData,
                    "async": true,
                    "success": fnCallback
                });
            },
            "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                totalBoletos += Math.round(aData[9] * 100) / 100;
                
                var _html = parseFloat(totalBoletos).toFixed(2);
                $("#boletosBancarios").find("tfoot").find("td#foot_totales").html(_html);
                var estado = aData[8];
                var disabled = estado != 'Entrada Confirmada' ? " disabled='true'" : '';
                $(nRow).find('td').eq(0).html("<label><input type='checkbox' name='boleto_bancario' class='ace' value ='" 
                                                + aData[0] + "'" + disabled + "/><span class='lbl'></span></label>");
                var _html = '';
                switch (estado) {
                    case "Pendente":
                        _html = "<span style='width: 134px;' class='label label-yellow arrowed'>" + estado + "</span>";
                        break;
    
                    case "Entrada Confirmada":
                        _html = "<span style='width: 134px;' class='label label-success arrowed'>" + estado + "</span>";
                        break;
    
                    case "Entrada rechazada":
                        _html = "<span style='width: 134px;' class='label label-danger arrowed'>" + estado + "</span>";
                        break;
    
                    case "Liquidado":
                        _html = "<span style='width: 134px;' class='label label-info arrowed'>" + estado + "</span>";
                        break;
    
                    case "Baja":
                        _html = "<span style='width: 134px;' class='label label-pink arrowed'>" + estado + "</span>";
                        break;
    
                    case "Baja Solicitada":
                        _html = "<span style='width: 134px;' class='label label-warning arrowed'>" + estado + "</span>";
                        break;
    
                    default:
                        _html = estado;
                        break;
                }
                $(nRow).find('td').eq(8).html(_html);
                return nRow;
            }
        });
        marcarTr();
    }
    $('#boletosBancarios_wrapper').on('mousedown', '#boletosBancarios tbody tr', function(e) {
    var sData = oTableBoleto.fnGetData( this );    
        if (e.button === 2) {
            codigo_boleto_imprimir = sData[0];
            generalContextMenu(menu.contextual, e);
            return false;
        }
    });
    
    $('body').on('click', '#menu a', function() {
        var accion = $(this).attr('accion');
        $('#menu').remove();
        switch (accion) {
            case 'imprimir_boleto':
                var param = new Array();
                param.push(codigo_boleto_imprimir);
                printers_jobs(14, param);
                break;
            case 'ver_detalle_boleto':                
                 verDetalleBoleto(codigo_boleto_imprimir);
                break;
            }
    });
    
    var _html = '';
    _html += '<div class="btn-group" name="btn_emitir_boleto">';
    _html += '<button class="btn btn-info boton-primario" data-original-title="" title="' + lang.emitir_boletos + '" onclick="showFacturantes();">';
    _html += lang.emitir_boletos;
    _html += '</button>';
    _html += '<button class="btn btn-info dropdown-toggle btn-primary" data-toggle="dropdown">';
    _html += '<span class="icon-caret-down icon-only"></span>';
    _html += '</button>';
    _html += '<ul class="dropdown-menu dropdown-default">';
    _html += '<li>';
    _html += '<a accion="baja_de_boletos" onclick="baja_de_boletos();">';
    _html += lang.baja_de_boletos;
    _html += '</a>';
    _html += '</li>';
    _html += '</ul>';
    _html += '</div>';
    _html += '&nbsp;&nbsp;&nbsp;&nbsp;';
    _html += '<div class="btn-group" name="btn_emitir_boleto">';
    _html += '<div class="col-md-2 no-padding">';
    _html += '<button class="btn btn-info boton-primario" data-original-title="" title="' + lang.transferencia_de_archivos + '" onclick="TransferenciaArchivos();">';
    _html += lang.transferencia_de_archivos;
    _html += '</button>';
    _html += '</div>';    
    $("[name=area_botones_accion]").html(_html);    
    $("#boletosBancarios_filter").find("label").addClass("input-icon input-icon-right");
    $("#boletosBancarios_filter").find("label").append('<i class="icon-caret-down grey bigger-110 bigger-140" style="margin-right: 3px; cursor: pointer; margin-left: -16px;" name="table_filters" onclick="ver_ocultar_filtros();"></i>');
    $("#boletosBancarios_filter").append($("[name=container_menu_filters_temp]").html());
    $(".date-picker").datepicker();
    $("[name=container_menu_filters_temp]").remove();    
    $("#boletosBancarios_filter").append('<i id="imprimir_informe" class="icon-print grey" onclick="exportar(\'pdf\');" style="cursor: pointer" data-original-title="" title=""></i>');
    $("#boletosBancarios_filter").append('<i id="exportar_informe" class="icon-external-link" onclick="exportar(\'csv\');" style="cursor: pointer; margin-left: 6px;" data-original-title="" title="exportar"></i>');
    
    if (cod_alumno != -1 && cod_matricula != -1){
        showFacturantes();
    }
    
}

function filtrar(route){
    if(route){
        var primer_dia_mes = moment(1, "DD").lang(idioma).format('L');
        var final_dia = moment().endOf('months').lang(idioma).format('L');
        $("#fecha_desde").datepicker( "setDate", primer_dia_mes );
        $("#fecha_hasta").datepicker( "setDate",final_dia);
    }    
    oTableGenerar.fnDraw();
}

function TransferenciaArchivos() {
    if (transferir_archivos == 1) {
        window.location.href = BASE_URL + "boletos/archivosBoletoBancario";
    } else {
        gritter(lang.no_tiene_permiso);
    }
}

function showFacturantes(matriculas) {
    initForm();
    $("#fuelux-wizard").removeClass("hide");
    switch ($(".facturates").length) {
        case 0:
            $("#no-hay-cuentas").removeClass("hide");
            break;
            
        case 1:
            showGenerarBoleto($(".facturates").attr("codigo"));
            break;
            
        default :
            $("#facturanteSeleccion").removeClass("hide");
            break;
    }
    $("#areaTablasBoletosConfiormarBaja").hide();
    $("#areaTablasBoletos").hide();
    setStep(1);
}

function showGenerarBoleto(facturante) {
    $("#facturanteSeleccion").addClass("hide");
    $("#areaTablasGenerarBoletos").show();
    var tableDef = {
        "bServerSide": true,
        "aaSorting": [[3, "asc"]],
        "sAjaxSource": BASE_URL + "boletos/getCtactePendienteEmision",
        "sServerMethod": "POST",
        "fnServerData": function(sSource, aoData, fnCallback) {
            aoData.push({"name": "facturante", "value": facturante});
            aoData.push({name: "cod_alumno", value: cod_alumno});
            aoData.push({name: "cod_matricula", value: cod_matricula});
            $.ajax({
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": aoData,
                "async": true,
                "success": fnCallback
            });
        },
        "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            $(nRow).find('td').eq(0).html("<label><input type='checkbox' name='ctacte[]' class='ace' value ='" 
                + aData[0] 
                + "' id='cb-ctacte-" 
                + aData[0] 
                + "' onclick='checkearCtaCte(" 
                + '"' 
                + aData[0] 
                + '"' 
                + ")'"
                + ((ctacte.indexOf(aData[0]) >= 0)?"checked":"")
                + "/><span class='lbl'></span></label>");
            $("[name=btn_generar_boletos]").prop("disabled", false);
            return nRow;
        },
        "fnServerParams": function ( aoData ) {
            aoData.push({
                name: "fecha_desde",
                value:  $("#fecha_desde").val()
            });
            aoData.push({
                name: "fecha_hasta",
                value:  $("#fecha_hasta").val() 
            });            
        }
    };
    /*
        Se puede hacer mas prolijo. Si sobra tiempo dejarlo lindo.
    */
    if(typeof emitir !== 'undefined'){
        tableDef.pageLength = 6;
        tableDef.sAjaxSource = BASE_URL + "boletos/getCtaCteRematriculaciones",
        tableDef.fnServerData =  function(sSource, aoData, fnCallback) {
            aoData.push({name: "facturante", value: facturante});
            aoData.push({name: "cod_alumno", value: cod_alumno});
            aoData.push({name: "cod_matricula", value: cod_matricula});
            aoData.push({name: "alumnosRematricular", value: matriculasEmitir});
            aoData.push({name: "desde", value: rangoFechas().desde});
            aoData.push({name: "hasta", value: rangoFechas().hasta});
            $.ajax({
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": aoData,
                "async": true,
                "success": fnCallback
            });
        };
        $.ajax({
            url:BASE_URL + 'boletos/getIdsRematriculaciones',
            dataType:'json',
            type:'POST',
            data:{alumnosRematricular:matriculasEmitir, desde:rangoFechas().desde, hasta:rangoFechas().hasta},
            success: function(data){
                ctacte = data;
                oTableGenerar = $('#boletosBancariosGenerar').dataTable(tableDef);
            }
        });
    } else {
        oTableGenerar = $('#boletosBancariosGenerar').dataTable(tableDef);
    }

    $("#boletosBancariosGenerar_filter").find("label").addClass("input-icon input-icon-right");
    $("#boletosBancariosGenerar_filter").find("label").append('<i name="icon_filters" class="icon-caret-down grey" name="table_filters" style="margin-right: 7px; cursor: pointer"></i>');
    $("#boletosBancariosGenerar_filter").append($("[name=container_menu_filters_temp]").html());
    $("[name=container_menu_filters_temp]").remove();    
    $("[name=icon_filters]").on("click", function(){
        $("[name=contenedorPrincipal]").toggle();
        $("[name=div_table_filters]").toggle(300);        
        return false;
    });
   
    $("[name=contenedorPrincipal]").on("mousedown", function(){
        $("[name=contenedorPrincipal]").hide();
        $("[name=div_table_filters]").hide(300);
    });
    
    $('.date-picker').datepicker({autoclose:true}).next().on(ace.click_event, function(){
        $(this).prev().focus();
    });
     
    var fecha_inicial = moment();
    var fecha_final = moment(fecha_inicial).add(2, 'months').lang(idioma).format('L');
    $("#fecha_desde").datepicker( "setDate",fecha_final);
}

function hideGenerarBoleto() {
    $("#fuelux-wizard").addClass("hide");
    $("#areaTablasBoletosConfiormarBaja").hide();
    $("#areaTablasGenerarBoletos").hide();
    $("#areaTablasBoletos").show();
}

function cancelarPreviewBoletos() {
    $("#areaTablasBoletosConfiormarBaja").hide();
    $("#areaTablasGenerarBoletosConfirmar").hide();
    $("#areaTablasGenerarBoletos").hide();
    $("#areaFormularioInstruccionesBoleto").show();
    setStep(2);
}

function cancelarInstruccionesBoletos(){
    $("#areaTablasBoletosConfiormarBaja").hide();
    $("#areaTablasGenerarBoletosConfirmar").hide();
    $("#areaFormularioInstruccionesBoleto").hide();
    $("#areaTablasGenerarBoletos").show();
    setStep(1);
}


function generarBoletosInstrucciones(){
    $("#areaTablasGenerarBoletos").hide();
    $("#areaFormularioInstruccionesBoleto").show();
    setStep(2);
}

function setStep(stepNumber){
    for(i = 1;i<=4;i++){
        var selector = '#step' + i;
        if(i == stepNumber){
            $(selector).removeClass("complete");
            $(selector).removeClass("step");
            $(selector).addClass("active");
        } else {
            if(i < stepNumber) {
                $(selector).addClass("complete");
                $(selector).removeClass("step");
                $(selector).removeClass("active");
            } else {
                $(selector).removeClass("complete");
                $(selector).addClass("step");
                $(selector).removeClass("active");
            }
        }
    }
}


function generarBoletosConfirmar() {
    var selected = $("[name='ctacte[]']:checked");

    if (selected.length == 0) {
        gritter(lang.debe_seleccionar_al_menos_un_items_de_cuenta_corriente, false);
    } else {
        $.ajax({
            url: BASE_URL + "boletos/getCtactePendienteEmision",
            data: {ctacte:ctacte},
            type: 'POST',
            cache: false,
            dataType: 'json',
            success: function(_json) {
            /*
                $("#step1").removeClass("step");
                $("#step1").addClass("complete");
                */
                setStep(3);
                if (_json.aaData.length > 0) {
                    $("#areaTablasGenerarBoletos").hide();
                    $("#areaFormularioInstruccionesBoleto").hide();
                    $("#areaTablasGenerarBoletosConfirmar").show();
                    $("[name=tbody_detalles]").find("tr").remove();
                    $.each(_json.aaData, function(key, value) {
                        var _html = '<tr>';
                        _html += "<td>";
                        _html += value[1];
                        _html += "</td>";
                        _html += "<td>";
                        _html += value[2];
                        _html += "</td>";
                        _html += "<td>";
                        _html += value[3];
                        _html += "</td>";
                        _html += '<td>';
                        _html += value[4];
                        _html += '</td>';
                        _html += "</tr>";
                        $("[name=tbody_detalles]").append(_html);
                    });
                }
            }
        });
    }
}

function imprimirRemesa() {
    window.boletosEmitidos = -1;
    var codigo_remesa = $("[name=remesa_creada]").val();
   var param = new Array();
    param.push(codigo_remesa);
    printers_jobs(13, param);
}

function descargarRemessa() {
    var codigo_remesa = $("[name=remesa_creada]").val();
    window.location.href = BASE_URL + 'facturantes/descargarRemessa/' + codigo_remesa;
}

function generarBoletos() {
    var dataserialize = $("#frm_generar_boleto").serialize();
    var postdata = {
                    datosTabla:$("#frm_generar_boleto").serialize(),
                    //Si saco el formulario este se rompe el controller.
                    ctacte:ctacte,
                    datosEtiquetas:$("#frm_etiquetas_boleto").serialize()
                  };
    var enviarRemessa = $('#cb-enviarRemessa').prop('checked');
    if(enviarRemessa)
        postdata['enviarRemessa']= 'true';
    if (dataserialize == '') {
        gritter(lang.debe_seleccionar_al_menos_un_items_de_cuenta_corriente, false);
    } else {
        $.ajax({
            url: BASE_URL + "boletos/generarBoletosBacarios",
            data: postdata,
            type: 'POST',
            cache: false,
            dataType: 'json',
            success: function(_json) {
                if (_json.error) {
                    gritter(_json.error, false);
                } else {
                    window.boletosEmitidos = 1;
                    $("[name=btn_imprimir_remesa]").prop("disabled", false);
                    $("#areaTablasBoletosConfiormarBaja").hide();
                    /*
                    $("#step2").removeClass("step");
                    $("#step2").addClass("complete");
                    $("#step3").removeClass("step");
                    $("#step3").addClass("active");
                    */
                    setStep(4);
                    $("#areaTablasGenerarBoletosConfirmar").hide();
                    $("[name=remesa_creada]").val(_json.codigo_remesa);
                    $("#areaTablasVistaImprimir").show();
                    if(!enviarRemessa){
                        gritter(lang.validacion_ok, true);
                    } else {
                        if(!_json.remesaEnviada){
                            gritter("Boletos creados, REMESA NO ENVIADA", false);
                        } else {
                            gritter(lang_validacion_ok, true);
                        }
                    }
                    if(typeof emitir == undefined){
                        oTableGenerar.fnDraw();
                        oTableBoleto.fnDraw();
                    }
                }
           }
        });
    }
}


function verListadoBoletosEmitidos() {
    /*
    $("#step1").removeClass("complete").addClass("step");
    $("#step2").removeClass("complete").addClass("step");
    $("#step3").removeClass("complete").addClass("step").removeClass("active");
    */
    setStep(4);
    if(typeof emitir !== 'undefined'){
        parent.jQuery.fancybox.close();
        parent.redibujarTabla();
        return;
    }
    $("#fuelux-wizard").addClass("hide");
    $("areaTablasBoletosConfiormarBaja").hide();
    $("#areaTablasVistaImprimir").hide();
    $("#areaTablasGenerarBoletos").hide();
    $("#areaTablasGenerarBoletosConfirmar").hide();
    $("#areaTablasBoletos").show();
}

function baja_de_boletos() {
    var seleccion = $("[name=boleto_bancario]:checked");
    if (seleccion.length == 0) {
        gritter(lang.debe_seleccionar_algun_boleto_para_dar_de_baja, false);
    } else {
        $("#areaTablasBoletos").hide();
        $("#boletosBancariosConfirmarBaja tbody").remove();
        $("#areaTablasBoletosConfiormarBaja").show();
        $("#boletosBancariosConfirmarBaja").append("<tbody>");
        for (var i = 0; i < seleccion.length; i++) {
            var linea = $(seleccion[i]).closest("tr").find("td");
            var facturado = $(linea[1]).html();
            var importe = $(linea[3]).html();
            var nosso_numero = $(linea[5]).html();
            var _html = '';
            _html = '<tr>';
            _html += '<td>';
            _html += facturado;
            _html += '</td>';
            _html += '<td>';
            _html += importe;
            _html += '</td>';
            _html += '<td>';
            _html += nosso_numero;
            _html += '</td>';
            _html += '</tr>';
            $("#boletosBancariosConfirmarBaja tbody").append(_html);
        }
        $("#boletosBancariosConfirmarBaja").append("</tbody>");
    }
}


function confirmaBajaBoletos() {
    var seleccion = $("[name=boleto_bancario]:checked");
    if (seleccion.length == 0) {
        gritter(lang.debe_seleccionar_algun_boleto_para_dar_de_baja, false);
    } else {
        var boletos = new Array();
        for (var i = 0; i < seleccion.length; i++) {
            boletos.push($(seleccion[i]).val());
        }
        $.ajax({
            url: BASE_URL + 'boletos/generarBoletosBacarios',
            type: 'POST',
            dataType: 'json',
            data: {
                ctacte: boletos,
                estado: 'baja'
            },
            success: function(_json) {
                if (_json.error) {
                    gritter(_json.error, false);
                } else {
                    $("[name=btn_imprimir_remesa]").prop("disabled", true);
                    $("#areaTablasBoletosConfiormarBaja").hide();
                    $("#areaTablasGenerarBoletosConfirmar").hide();
                    $("[name=remesa_creada]").val(_json.codigo_remesa);
                    $("#areaTablasVistaImprimir").show();
                }
            }
        });
    }
}

function verDetalleBoleto(codigo_boleto) {
    $.ajax({
        url: BASE_URL + 'boletos/ver_detalle_boleto',
        type: 'POST',
        dataType: 'json',
        data: {
            codigo_boleto: codigo_boleto
        },
        success: function(_json) {
            $("[name=tbody_detalle_ctacte_original]").find("tr").remove();
            var _html = '';
            _html += "<tr>";
            _html += "<td>";
            _html += _json.ctacte_original.descripcion;
            _html += "</td>";
            _html += "<td>";
            _html += _json.ctacte_original.fecha_vencimiento;
            _html += "</td>";
            _html += "<td>";
            _html += _json.ctacte_original.importe;
            _html += "</td>";
            _html += "</tr>";
            $("[name=tbody_detalle_ctacte_original]").append(_html);
            if (_json.historico) {
                $("[name=tbody_detalle_movimientos_historicos]").find("tr").remove();
                _html = '';
                $.each(_json.historico, function(key, value) {
                    _html += "<tr>";
                    _html += "<td>";
                    _html += value.estado;
                    _html += "</td>";
                    _html += "<td>";
                    _html += value.fecha;
                    _html += "</td>";
                    _html += "</tr>";
                });
                $("[name=tbody_detalle_movimientos_historicos]").append(_html);
                $("[name=movimientos_historico]").show();
            } else {
                $("[name=movimientos_historico]").hide();
            }
            if (_json.imputaciones) {
                $("[name=tbody_imputaciones_boleto]").find("tr").remove();
                _html = '';
                $.each(_json.imputaciones, function(key, value) {
                    _html += "<tr>";
                    _html += "<td>";
                    _html += value.descripcion;
                    _html += "</td>";
                    _html += "<td>";
                    _html += value.fecha_vencimiento;
                    _html += "</td>";
                    _html += "<td>";
                    _html += value.importe_original;
                    _html += "</td>";
                    _html += "<td>";
                    _html += value.imputado;
                    _html += "</td>";
                    _html += "</tr>";
                });
                $("[name=tbody_imputaciones_boleto]").append(_html);
                $("[name=imputaciones_boleto]").show();
            } else {
                $("[name=imputaciones_boleto]").hide();
            }
            var _htmlVer = $("[name=div_template_detalle_boleto]").html();
            $.fancybox.open(_htmlVer, {
                height: "auto",
                scrolling: 'auto',
                autoSize: false,
                autoResize: false,
                padding: '0',
                openEffect: 'none',
                closeEffect: 'none',
                helpers: {
                    overlay: null
                },
                beforeClose: function() {
                }
            });
        }
    });
    $(".date-picker").datepicker();
}

function test_pagina_impresion() {
    $("#areaTablasBoletos").hide();
    $("#areaTablasBoletosConfiormarBaja").hide();
    $("#areaTablasGenerarBoletosConfirmar").hide();
    $("#areaTablasVistaImprimir").show();
}


function ver_ocultar_filtros(){
    if ($("[name=div_table_filters_exportar]").is(":visible")){
        $("[name=div_table_filters_exportar]").hide(200);
        $("[name=contenedorPrincipalExportar]").hide();
    } else {
        $("[name=div_table_filters_exportar]").show(200);
        $("[name=contenedorPrincipalExportar]").show();
    }
}

function listarExportar(){
    oTableBoleto.api().ajax.reload();
}

$.fn.dataTableExt.oApi.fnStandingRedraw = function(oSettings) {
    if(oSettings.oFeatures.bServerSide === false){
        var before = oSettings._iDisplayStart;
        oSettings.oApi._fnReDraw(oSettings);
        oSettings._iDisplayStart = before;
        oSettings.oApi._fnCalculateEnd(oSettings);
    }
    oSettings.oApi._fnDraw(oSettings);
};

function exportar(formato_exportar){
    var iSortCol_0 = oTableBoleto.fnSettings().aaSorting[0][0];
    var sSortDir_0 = oTableBoleto.fnSettings().aaSorting[0][1];
    var iDisplayLength = oTableBoleto.fnSettings()._iDisplayLength;
    var iDisplayStart = oTableBoleto.fnSettings()._iDisplayStart;
    var sSearch = $("#boletosBancarios_filter").find("input[type=text]").val();
    var fecha_vencimiento_desde = $("[name=filtro_vencimiento_fecha_desde]").val();
    var fecha_vencimiento_hasta = $("[name=filtro_vencimiento_fecha_hasta]").val();
    var fecha_emision_desde = $("[name=filtro_emision_fecha_desde]").val();
    var fecha_emision_hasta = $("[name=filtro_emision_fecha_hasta]").val();
    var estado = $("[name=filtro_estado]").val();
    $("[name=frm_exportar]").find("[name=iSortCol_0]").val(iSortCol_0);
    $("[name=frm_exportar]").find("[name=sSortDir_0]").val(sSortDir_0);
    $("[name=frm_exportar]").find("[name=iDisplayLength]").val(iDisplayLength);
    $("[name=frm_exportar]").find("[name=iDisplayStart]").val(iDisplayStart);
    $("[name=frm_exportar]").find("[name=sSearch]").val(sSearch);
    $("[name=frm_exportar]").find("[name=fecha_vencimiento_desde]").val(fecha_vencimiento_desde);
    $("[name=frm_exportar]").find("[name=fecha_vencimiento_hasta]").val(fecha_vencimiento_hasta);
    $("[name=frm_exportar]").find("[name=fecha_emision_desde]").val(fecha_emision_desde);
    $("[name=frm_exportar]").find("[name=fecha_emision_hasta]").val(fecha_emision_hasta);
    $("[name=frm_exportar]").find("[name=estado]").val(estado);
    $("[name=frm_exportar]").find("[name=exportar]").val(formato_exportar);
    $("[name=frm_exportar]").submit();
}






function checkForm(elemento){
    var selector = '#' + elemento.id;
    switch(elemento.id){
        /*Bloque juros*/
        case 'juros-cobrar':
            if($(selector).prop('checked')){
                $('#juros-tipo-banco').prop('disabled', false);
                $('#juros-tipo-iga').prop('disabled', false);
                if($('#juros-tipo-iga').prop('checked') && !$('#juros-tipo-pelaiga').prop('disabled')){
                    $('#juros-valoriga').prop('disabled', false);
                }
            } else { 
                $('#juros-tipo-banco').prop('disabled', true);
                $('#juros-tipo-iga').prop('disabled', true);
                $('#juros-valoriga').prop('disabled', true);
            }
            break;
        case 'juros-tipo-iga':
            if($('#juros-tipo-iga').prop('checked'))
               $('#juros-valoriga').prop('disabled', false);
            break;
        case 'juros-tipo-banco':
               $('#juros-valoriga').prop('disabled', true);
            break;


        /*Bloque Multa*/
        case 'multa-cobrar':
            if($('#multa-cobrar').prop('checked')){
                $('#multa-dias').prop('disabled', false);
                $('#multa-valor').prop('disabled', false);
            } else {
                $('#multa-dias').prop('disabled', true);
                $('#multa-valor').prop('disabled', true);
            }
            break;

        /*Bloque Aposovencimiento*/
        case 'venc-tipo-nao':
            if($(selector).prop('checked')){
                $('#venc-limite').prop('disabled', true);
                $('#venc-dias').prop('disabled', true);
            }
            break;

        case 'venc-tipo-banco':
            if($(selector).prop('checked')){
               $('#venc-limite').prop('disabled', false);
               if($('#venc-limite').prop('checked'))
                    $('#venc-dias').prop('disabled', false);
            }
            break;

        case 'venc-limite':
            if($(selector).prop('checked')){
                $('#venc-dias').prop('disabled', false);
            } else { 
                $('#venc-dias').prop('disabled', true);
            }
            break;

        /*Bloque atraso */

        case 'inclu-apos':
            if($(selector).prop('checked')){
                $('#inclu-dias').prop('disabled', false);
            } else {
                $('#inclu-dias').prop('disabled', true);
            }
            break;

        /* Bloque default, falta debuggear*/
        default:
            break;
    }

}

/*
    Para que el selector de etiquetas vuelva siempre al mismo estado al emitir boletos.
*/
function initForm(){
    $.ajax({
        url:BASE_URL + 'configuracion/getEtiquetasBoleto',
        type:'POST',
        dataType:'JSON',
        success:function (respuesta) {
            //Seteo todos los valores:
            $('#juros-valoriga').val(respuesta['juros-valoriga']);
            switch(respuesta['juros-tipo']){
                case 'banco':
                    $('#juros-tipo-banco').prop('checked', true);
                    $('#juros-valoriga').prop('disabled', true);
                    break;
                case 'iga':                    
                    $('#juros-tipo-iga').prop('checked', true);
                    break;
                default:
                    break;
            }
            $('#multa-valor').val(respuesta['multa-valor']);
            $('#multa-dias').val(respuesta['multa-dias']);
            $('#venc-dias').val(respuesta['venc-dias']);
            $('#inclu-dias').val(respuesta['inclu-dias']);
            if(respuesta['descontoFixo'] == 'on'){
               $('#descontoFixo').prop('checked', true); 
            }
            if(respuesta['juros-cobrar'] == 'on'){
                $('#juros-cobrar').prop('checked', true);
            }else {
                $('#juros-cobrar').prop('checked', false);
                $('#juros-tipo-banco').prop('disabled', true);
                $('#juros-tipo-pelaiga').prop('disabled', true);
                $('#juros-valoriga').prop('disabled', true);

            }

            if(respuesta['multa-cobrar'] == 'on'){
                $('#multa-cobrar').prop('checked', true);
            } else {
                $('#multa-valor').prop('disabled', true);
                $('#multa-dias').prop('disabled', true);
            }
            switch(respuesta['venc-tipo']){
                case 'nao':
                    $('#venc-tipo-nao').prop('checked', true);
                    $('#venc-limite').prop('disabled', true);
                    $('#venc-dias').prop('disabled', true);
                    break;
                case 'banco':
                    $('#venc-tipo-banco').prop('checked', true);
                    if(respuesta['venc-limite'] == 'on'){
                        $('#venc-limite').prop('checked', true);
                    } else {
                        $('#venc-dias').prop('disabled', true);
                    }
                    break;
                default:
                    break;
            }
            if(respuesta['inclu-apos'] == 'on'){
                $('#inclu-apos').prop('checked', true);
            } else {
                $('#inclu-dias').prop('disabled', true)
            }



            if(respuesta['cb-enviarRemessa'] == 'on'){
                $('#cb-enviarRemessa').prop('checked', true);
            }

            switch(respuesta['valorBoleto']){
                case 'desconto':
                    $('#valorBoleto-desconto').prop('checked', true);
                    break;
                case 'cheio':
                    $('#valorBoleto-cheio').prop('checked', true);
                    break;
                default:
                    break;
            }

        }
    });
}

function enviarEtiquetas()
{
    var form = $('#frm_etiquetas_boleto');
    var disabled = form.find(':input:disabled').removeAttr('disabled');
    var formData = form.serializeArray();
    disabled.attr('disabled','disabled');

    var preferencias = {};
    formData.forEach(function(elemento){
        preferencias[elemento.name] = elemento.value;
    });
    $.ajax({
        url: BASE_URL + 'configuracion/setEtiquetasBoleto',
        type: 'POST',
        data: {preferencias:JSON.stringify(preferencias)},
        dataType: 'json',
        success: function (success){
            console.log(success);
        }
    });
}
