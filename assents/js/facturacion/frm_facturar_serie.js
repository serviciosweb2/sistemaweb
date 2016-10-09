var thead = [];
var data = '';
var oTable = '';
var aoColumnDefs = columnsFacturacionSerie;
var frmLang = langFrm;
var puntoVentaTipoFactura = new Array();
var puntoVentaPorventaje = new Array();
var ctacte_seleccionados = new Array();
var total_seleccionados = 0;
var importe_total_seleccionados = 0;
function habilitarGuardar(element){
    var value = $(element).prop("checked") ? 1 : 0;
    var importe_seleccion = parseFloat($(element).closest("tr").find("[name=importe_linea]").val());
    if (value == 1){
        total_seleccionados ++;
        importe_total_seleccionados += importe_seleccion;
    } else {
        total_seleccionados --;
        importe_total_seleccionados -= importe_seleccion;
    }
    $("#sp_total_seleccionados").html(total_seleccionados);
    $("#sp_total_importe_seleccionados").html((Math.round(importe_total_seleccionados * 100) / 100).toFixed(2));
    
    ctacte_seleccionados[$(element).val()] = value;
    var deshabilitar=true;
    $(oTable.$('input[type="checkbox"]')).each(function(k,input){
        if($(input).is(":checked")){
            deshabilitar = false;
        }
    });
    $("button[name='guardar_fact_series']").prop("disabled",deshabilitar);
}

$(document).ready(function(){
    $("button[name='guardar_fact_series']").prop("disabled",true);
    init();
});

function redibujar(){
    var mensaje = '';
    if ($("[name=facturante-serie]").val() == '') mensaje += frmLang.facturante_es_requerido + "<br>";
    if ($("[name=tipo-factura]").val() == null){
        mensaje += frmLang.tipo_factura_es_requerido + "<br>";
    } else {
        var tipo_factura = $("[name=tipo-factura]").val();
        var encontrado = false;
        for (var i = 0; i < tipo_factura.length; i++){
            var tipo = puntoVentaTipoFactura[tipo_factura[i]];
            for (var j = i + 1; j < tipo_factura.length; j++){
                if (puntoVentaTipoFactura[tipo_factura[j]] == tipo && typeof(tipo) != "undefined"){
                    encontrado = true;                    
                }
            }
        }
        if (encontrado){
            mensaje += frmLang.ha_seleccionado_tipos_de_datos_de_facturas_repetidos;
        } else {
            var total_facturacion = 0;
            for (var i = 0; i < tipo_factura.length; i++){
                total_facturacion += puntoVentaPorventaje[tipo_factura[i]];
            }
        }
    }    
    if (mensaje != ''){
        gritter(mensaje);
    } else {
        $("[name=div_table_filters]").hide();
        oTable.fnDraw();
    }
}

function init(){
    $('.fancybox-wrap select.select_chosen').chosen({
        width:'100%'
    });
    
    var _html = '<tfoot>';
    _html += '<tr>';
    _html += '<td colspan="5">';
    _html += 'Total Seleccionados ';
    _html += '<span id="sp_total_seleccionados" style="font-weight: bold;">';
    _html += total_seleccionados;
    _html += '</span>';
    _html += '</td>';
    _html += '<td colspan="4" style="text-align: right;">';
    _html += 'Importe Seleccion';
    _html += '</td>';
    _html += '<td colspan="2">';
    _html += '<span id="sp_total_importe_seleccionados" style="font-weight: bold;">';
    _html += parseFloat(importe_total_seleccionados).toFixed(2);
    _html += '</span>';
    _html += '</td>';
    _html += '</tr>';
    _html += '</tfoot>';
    $("#facturacionSerie").append(_html);
    
    var aoData = Array();
    oTable = $('#facturacionSerie').dataTable({
        "bProcessing": false,
        "bServerSide": true,
        "bAutoWidth": false,
        "bScrollCollapse": true,
        "sAjaxSource": BASE_URL + 'facturacion/getCtaCteFacturar',
        "sServerMethod": "POST",
        'aoColumnDefs': aoColumnDefs,
         "oLanguage": {
   	 "sLengthMenu": 'Mostrar <select>' +
   	 '<option value="10000" SELECTED>Todos</option>' +
   	 '<option value="10">10</option>' +
    	 '<option value="20">20</option>' +
    	 '<option value="30">30</option>' +
    	 '<option value="40">40</option>' +
    	 '<option value="50">50</option>' +
    	 '</select> Registros' },
        "aaSorting": [[ 5, "desc" ]],
        "aoColumns" : [
            null, 
            null,
            null,
            null,
            {"sType": "uk_date"},
            null,
            null,
            null
        ],
        "fnCreatedRow": function( nRow, aData, iDataIndex ){
            $(nRow).attr('id', aData[1]);
            $(nRow).attr('razon-social', aData[10]);
        },
        "fnServerParams": function ( aoData ) {
            aoData.push({
                name: "fecha-inicio",
                value:  $("#fecha-inicio").val()
            });
            aoData.push({
                name: "fecha-fin",
                value:  $("#fecha-fin").val()
            });
            aoData.push({
                name: "medio_pago",
                value: $("#medio-pago").val()
            });
            if ($("#tipo-factura").val() != ''){
                var tipos_facturas = $("[name=tipo-factura]").val();
                if ($("[name=tipo-factura]").prop("multiple")){
                    if (tipos_facturas != null){                        
                        for (var i = 0; i < tipos_facturas.length; i++){
                            aoData.push({name: "tipo_factura[]", value: puntoVentaTipoFactura[tipos_facturas[i]]});
                        }
                    }
                } else {
                    aoData.push({name: "tipo_factura[]", value: puntoVentaTipoFactura[tipos_facturas]});
                }
            }
             if ($("#facturante-serie").val() != ''){
                aoData.push({
                    name: "facturante",
                    value: $("#facturante-serie").val()
                });
            }
            aoData.push(filtrar_facturacion_serie());
        },
        "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            var _html = '<input type="hidden" name="importe_linea" value="' + aData[9] + '">';
            _html += aData[9];
            $(nRow).find('td').eq(6).html(_html);
            return nRow;
        },
        "fnServerData": function ( sSource, aoData, fnCallback){
            if ($("[name=tipo-factura]").val() != null){
                $("[name=chk_sel_unsel_all]").prop("checked", false);
                $.ajax({
                    "dataType": 'json',
                    "type": "POST",
                    "url": sSource,
                    "data": aoData,
                    "async": false,
                    "success": fnCallback
                });
                $('#facturacionSerie tbody tr').each(function(){
                    if ($(this).attr('id')){
                        var selected = ctacte_seleccionados[$(this).attr('id')] && ctacte_seleccionados[$(this).attr('id')] == "1" ? "checked='true'" : "";
                        $(this).find('td:last').empty();
                        var _html = '';
                        _html += '<input type="checkbox" onclick="habilitarGuardar(this)" name="ctacteID[]" value="' 
                                + $(this).attr('id') + '"' + selected + '/>';
                        $(this).find('td:first').append(_html);
                        _html = '';
                        _html += '<button type="button" class="btn-razon-social btn btn-default btn-xs" razon-social="'
                                + $(this).attr('razon-social') + '">';
                        _html += '<i class="icon-user"></i>';
                        _html += '</button>';
                        _html += '<button type="button" class="btn btn-default btn-xs btn-detalle-ctacte" id-ctacte="'
                                + $(this).attr('id') + '" data-content="">';
                        _html += '<i class="icon-list"></i>';
                        _html += '</button>';
                        $(this).find('td:last').addClass('text-center').append(_html);
                    }
                });
                $('#facturacionSerie tbody tr .btn-razon-social').each(function(){
                    $(this).popover({
                        placement: 'left',
                        html: true,
                        trigger: 'manual',
                        content: $(this).attr("razon-social")
                    });
                });
                $('#facturacionSerie tbody tr .btn-detalle-ctacte').each(function(){
                    $(this).popover({
                        placement:'left',
                        html: true,
                        title:'Detalle',
                        trigger:'manual'
                    });
                });
                $.fancybox.update();
            }
        }
    });
    
    var _html = '<input name="chk_sel_unsel_all" type="checkbox" class="ace id-toggle-all" title="Seleccionar todos" onclick="seleccionar_todas_las_series(this);">';
    _html += '<span class="lbl">';
    _html += '</span>';
    $("#facturacionSerie").find("thead").find("th:first").html(_html);
    $('#facturacionSerie').wrap('<div class="table-responsive"></div>');
    $(aoColumnDefs).each(function(){
        thead.push(this.sTitle);
    });
    $("#facturacionSerie_length").change(function(){
        $.fancybox.update();
    });
    
    $('#facturante-serie').change(function(){
        $('#tipo-factura').empty();
        if ($(this).val() != ''){
            $.ajax({
                url: BASE_URL + 'facturacion/getTiposFacturaFacturante',
                data: 'cod_facturante=' + $(this).val(),
                type: 'POST',
                dataType: 'json',
                cache: false,
                success: function(respuesta){
                    var selected = true;
                    $(respuesta).each(function() {
                        var complemento = selected ? 'selected="true"' : '';
                        $('#tipo-factura').append('<option value="' + this.codigo + '" ' + complemento + '>' + this.factura + '</option>');
                        puntoVentaPorventaje[this.codigo] = parseFloat(this.porcentaje);
                        puntoVentaTipoFactura[this.codigo] = parseInt(this.tipo);
                        selected = false;
                    });
                    redibujar();
                    $("#tipo-factura").trigger('chosen:updated');
                }
            });
        }
    });
    
    $('#facturacionSerie').on('click','.btn-razon-social',function(){
        if($(this).parent().find('.popover').is(':visible')){
            $('.btn-razon-social').popover('hide');
        } else {
            $('.btn-razon-social').not(this).popover('hide');
            $(this).popover('show');
        }
    });
    
    $('#facturacionSerie').on('click','.btn-detalle-ctacte',function(){
        var vistaTabla="";
        boton = this;
        if($(this).parent().find('.popover').is(':visible')){
            $('.btn-detalle-ctacte').popover('hide');
        } else {
            $.ajax({
                url: BASE_URL + 'ctacte/getFacturas',
                data: {
                    cod_ctacte: $(this).attr('id-ctacte')
                },
                type: 'POST',
                dataType: 'JSON',
                cache: false,
                success:function(respuesta){
                    var vistaTabla = '';
                    if(respuesta.codigo==0){
                        vistaTabla += respuesta.msgerrors;
                    } else {
                        vistaTabla += '<table class="table table-striped table-bordered dataTable" cellspacing="0" cellpadding="0" border="0">';
                        vistaTabla += '<thead>'; 
                        vistaTabla += '<th>';
                        vistaTabla += frmLang.numero;
                        vistaTabla += '</th>';
                        vistaTabla += '<th>';
                        vistaTabla += 'tipo';
                        vistaTabla += '</th>';
                        vistaTabla += '<th>';
                        vistaTabla += frmLang.importe;
                        vistaTabla += '</th>';
                        vistaTabla += '</thead>';
                        vistaTabla+='<tbody>';
                        $(respuesta.facturas).each(function(k,valores){
                            vistaTabla += '<tr>';
                            vistaTabla += '<td>';
                            vistaTabla += valores.nrofact;
                            vistaTabla += '</td>';
                            vistaTabla += '<td>';
                            vistaTabla += valores.factura;
                            vistaTabla += '</td>'; 
                            vistaTabla += '<td>';
                            vistaTabla += valores.importe;
                            vistaTabla += '</td>';
                            vistaTabla += '</tr>';
                        });
                        vistaTabla+='</tbody>';
                        vistaTabla+='</table>';
                    }
                    $(boton).attr('data-content',vistaTabla);
                    $('.btn-detalle-ctacte').not(boton).popover('hide');
                    $(boton).popover('show');
                }
            });
        }
    });
    
    $("#frm-facturar-serie").on('submit',function(){
        var mensaje = '';
        var seleccion = new Array();
        $.each(ctacte_seleccionados, function(key, value){
            if (value == "1"){
                seleccion.push(key);
            }
        });
        
        if ($("[name=facturante-serie]").val() == '') mensaje += frmLang.facturante_es_requerido + "<br>";
        if ($("[name=tipo-factura]").val() == null) mensaje += frmLang.tipo_factura_es_requerido + "<br>";
        if (seleccion.length == 0) mensaje += frmLang.linea_de_cuenta_corriente_es_requerido + "<br>";
        var total_facturacion = 0;
        var tipo_factura = $("[name=tipo-factura]").val();
        if (tipo_factura != null){
            var encontrado = false;
            for (var i = 0; i < tipo_factura.length; i++){
                var tipo = puntoVentaTipoFactura[tipo_factura[i]];
                for (var j = i + 1; j < tipo_factura.length; j++){
                    if (puntoVentaTipoFactura[tipo_factura[j]] == tipo && typeof(tipo) != "undefined"){
                        encontrado = true;
                    }
                }
            }
            if (encontrado){
                mensaje = frmLang.ha_seleccionado_tipos_de_datos_de_facturas_repetidos;
            } else {
                for (var i = 0; i < tipo_factura.length; i++){
                    total_facturacion += puntoVentaPorventaje[tipo_factura[i]];
                }
            }
        } else {
            mensaje += frmLang.indique_el_tipo_de_factura + "<br>";
        }
        if (mensaje != ''){
            gritter(mensaje);
        } else {
            var data = new Array();
            for (var i = 0; i < seleccion.length; i++){
                data.push({name: "ctacteID[]", value: seleccion[i]});
            }
            for (var i = 0; i < tipo_factura.length; i++){
                data.push({name: "puntos_venta[]", value: tipo_factura[i]});
            }
            data.push({name: "tipo-factura", value: $("[name=tipo-factura]").val()});
            $.ajax({
                url: BASE_URL + 'facturacion/guardarFacturaSerie',
                data: data,
                type: 'POST',
                dataType: 'json',
                cache: false,
                success: function(respuesta) {
                    if(respuesta.codigo === 1){
                        gritter("Facturado correctamente", true);
                        $.fancybox.close();
                        if (respuesta.custom.imprimir == "1"){
                            var param = new Array();
                            $.each(respuesta.custom.factura, function(index, value){
                                param.push(value);
                            });
                            printers_jobs(11, param.join("-"));
                        }
                    } else {
                        gritter(respuesta.msgerror);
                    }
                }
            });
        }
        return false;
    });
    
    $(".fancybox-wrap #facturacionSerie_filter").find("label").addClass("input-icon input-icon-right");
    $(".fancybox-wrap #facturacionSerie_filter").find("label").append('<i name="icon_filters" class="icon-caret-down grey" name="table_filters" style="margin-right: 7px; cursor: pointer"></i>');
    $(".fancybox-wrap #facturacionSerie_filter").append($("[name=container_menu_filters_temp]").html());
    $("[name=container_menu_filters_temp]").remove();
    $("[name=icon_filters]").on("click", function(){
        $('.fancybox-wrap').find("[name=contenedor_filtros]").toggle();
        $('.fancybox-wrap').find("[name=div_table_filters]").toggle(300);
        return false;
    });
    
    $("[name=contenedor_filtros]").on("mousedown", function(){
        $('.fancybox-wrap').find("[name=contenedor_filtros]").hide();
        $('.fancybox-wrap').find("[name=div_table_filters]").hide(300);
    });
    
    $(".fancybox-wrap .fecha_filtro_factura").datepicker({
        changeMonth: false,
        changeYear: false
    });
}

function filtrar_facturacion_serie(){
    var cobradas_no_facturadas = '';
    if($('#facturadas_no_cobradas').is(':checked')){
        cobradas_no_facturadas = 1;
    }
    var objFiltros = {
        name: "cobradas_nofacturadas",
        value: cobradas_no_facturadas
    };
    return objFiltros;
}

function seleccionar_todas_las_series(element){
    var value = $(element).prop("checked") ? 1 : 0;
    var chk = $("[name='ctacteID[]']");
    $.each(chk, function(key, chk_element){
        var ctacte_id = $(chk_element).val();
        var importe = parseFloat($(chk_element).closest("tr").find("[name=importe_linea]").val());
        if (value == 1){
            if (!$(chk_element).prop("checked")){
                $(chk_element).prop("checked", true);
                total_seleccionados ++;
                importe_total_seleccionados += importe;
            }
        } else {
            if ($(chk_element).prop("checked")){
                $(chk_element).prop("checked", false);
                total_seleccionados --;
                importe_total_seleccionados -= importe;
            }
        }
        ctacte_seleccionados[ctacte_id] = value;
    });
    $("#sp_total_seleccionados").html(total_seleccionados);
    var importeTotal = Math.round(importe_total_seleccionados * 100) / 100;
    importeTotal = importeTotal.toFixed(2);
    $("#sp_total_importe_seleccionados").html(importeTotal);
    var deshabilitar=true;
    $(oTable.$('input[type="checkbox"]')).each(function(k,input){
        if($(input).is(":checked")){
            deshabilitar = false;
        }
    });
    $("button[name='guardar_fact_series']").prop("disabled",deshabilitar);
}


jQuery.fn.dataTableExt.oSort['uk_date-asc']  = function(a,b) {     
    var ukDatea = a.split('/');
    var ukDateb = b.split('/');                
    if (isNaN(parseInt(ukDatea[0]))) {
        return -1;
    }     
    if (isNaN(parseInt(ukDateb[0]))) {
        return 1;
    }     
    var x = (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
    var y = (ukDateb[2] + ukDateb[1] + ukDateb[0]) * 1;     
    return ((x < y) ? -1 : ((x > y) ?  1 : 0));
};

jQuery.fn.dataTableExt.oSort['uk_date-desc'] = function(a,b) {
    var ukDatea = a.split('/');
    var ukDateb = b.split('/');               
    if (isNaN(parseInt(ukDatea[0]))) {
        return 1;
    }     
    if (isNaN(parseInt(ukDateb[0]))) {
        return -1;
    }     
    var x = (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
    var y = (ukDateb[2] + ukDateb[1] + ukDateb[0]) * 1;     
    return ((x < y) ? 1 : ((x > y) ?  -1 : 0));
};