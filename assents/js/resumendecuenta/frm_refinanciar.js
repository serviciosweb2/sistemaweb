var arrCtacte = new Array();
var tableIdx = new Array();
var arrCtacteImporte = new Array();


$('select').chosen({
    
    width:'100%'
    
});

var tableDetalle = $('#tableDetalle').dataTable({
    iDisplayLength: 3,
    bLengthChange: false,
     bInfo: false,
      searching:false,
      bSort: false
});

var tabla_nueva_financiacion = $("#tabla_nueva_financiacion").dataTable({
    iDisplayLength: 3,
    bLengthChange: false,
     searching:false,
     bInfo: false
});

function cambioConceptos(){
    disabledInputs(true);
    var arrTemp = $("#conceptos").val().split("|");
    var codigo_concepto = arrTemp[0];
    var concepto = arrTemp[1];
    var codigo_alumno = $("#codigo_alumno").val();
    $.ajax({
        url: BASE_URL + "ctacte/getCtaCteRefinanciar",
        type: 'POST',
        dataType: 'json',
        data:{
            codigo_alumno: codigo_alumno,
            codigo_concepto: codigo_concepto,
            concepto: concepto
        },
        success: function(_json){
            graphicDataTable(_json);
            if (_json.length > 0){
                $.fancybox.update();
                disabledInputs(false);
            }
        }
    });

}

function graphicDataTable(_json){
    arrCtacte = new Array();
    tableIdx = new Array();
    arrCtacteImporte = new Array();
    var i = 0;
    tableDetalle.fnClearTable();
    $(_json).each(function(){
        $("#valor_concepto").val(this.concepto);
        arrCtacte[this.codigo] = 0;
        arrCtacteImporte[this.codigo] = this.saldoformateado;
        var chk = getinputChecked(this.codigo, false);
        tableIdx[i] = this.codigo;
        i++;
        if (this.habilitado === '2') {
            icono = "<i class='icon-ok icon-info-sign' title='" + lang.deuda_pasiva + "'></i>";
        } else {
            icono = "";
        }
        tableDetalle.fnAddData([
            chk,
            this.descripcion +' '+ icono,
            this.simbolo_moneda + this.saldoformateado,
            this.fechavenc
        ]);
    });    
}

function graphicDetalleFinanciacion(_json){
    tabla_nueva_financiacion.fnClearTable();
    var descripcion = $("#conceptos option:selected").text();
    $(_json.cuotas).each(function(){
        tabla_nueva_financiacion.fnAddData([
            this.nrocuota,
            descripcion,
            this.valor,
            this.fecha
        ]);
    });    
}

function getinputChecked(codigo_ctacte, checked){
    var complemento = '';
    if (checked){
        complemento = 'checked="true"';
    }
    return '<input type="checkbox" name="chk_ctacte" value="' + codigo_ctacte + '" onclick="chkCtacteChecked(this);" ' + complemento + '>';
}

function chkCtacteChecked(element){
    $("[name=importe_seleccionado]").val("");
    var nNodes = tableDetalle.fnGetNodes( );
    var codigo_ctacte = element.value;
    var checked = element.checked;
    var nroNodo = 0;
    for (var i = 0; i < tableIdx.length; i++){
        var checkear = tableIdx[i] >= codigo_ctacte;
        arrCtacte[tableIdx[i]] = checkear ? 1 : 0;
        nNodes[i].children[0].innerHTML = getinputChecked(tableIdx[i], checkear);
        if (tableIdx[i] == codigo_ctacte) nroNodo = i;
    }
    if (!checked){
         arrCtacte[codigo_ctacte] = 0;
         nNodes[nroNodo].children[0].innerHTML = getinputChecked(codigo_ctacte, false);
    }
    calcularTotal();
}

function calcularTotal(){
    var codigoImputar = new Array();
    var valorImputar = new Array();
    for (var i = 0; i < tableIdx.length; i++){
        var codigo_ctacte = tableIdx[i];
        if (arrCtacte[codigo_ctacte] == 1){            
            valorImputar.push(arrCtacteImporte[codigo_ctacte]);
            codigoImputar.push(codigo_ctacte);            
        }
    }
    if (codigoImputar.length > 0){
        $.ajax({
             url: BASE_URL + "ctacte/calcularTotal",
             type: 'POST',
             dataType: 'json',
             data: {
                 codigoImputar: codigoImputar,
                 valorImputar: valorImputar,
                 no_verificar_para_nota: 1
             },
             success: function(_json){
                 if (_json.codigo == 1){
                     $("[name=importe_seleccionado]").val(_json.total);
                 } else {
                     gritter(lang.no_se_ha_podido_calcular_el_total, false);
                 }
             }
        });
    }    
}

function guardarRefinanciacion(){
    disabledInputs(true);
    var ctactes = new Array();
    for (var i = 0; i < tableIdx.length; i++){
        var codigo_ctacte = tableIdx[i];
        if (arrCtacte[codigo_ctacte] == 1){
            ctactes.push(codigo_ctacte);
        }
    }
    var cantidad_cuotas = $("[name=cantidad_cuotas]").val();
    var porcentaje = $("[name=porcentaje]").val();
    var porcentaje_aplica = $("[name=porcentaje_aplica]").val();
    var fecha_primer_pago = $("[name=fecha_primer_pago]").val();
    var periodicidad = $("#periodicidad").val();
    var alumno = $("#codigo_alumno").val();
         var valor_refinanciar = $("[name=importe_seleccionado]").val();
    var temp = $("#conceptos").val().split("|");
    var codconcepto = temp[0];   
    var concepto = temp[1];
    $.ajax({
        url: BASE_URL + "ctacte/guardarRefinanciacion",
        type: 'POST',
        dataType: 'json',
        data: {
                cuotas: cantidad_cuotas,
                ctacte: ctactes,
                interesporc: porcentaje,
                fechaPrimerPago: fecha_primer_pago,
                valor_refinanciar:valor_refinanciar,
                periodicidad: periodicidad,
                porcentaje_aplica: porcentaje_aplica,
                alumno: alumno,
                concepto: concepto,
                codconcepto: codconcepto
            },
        success: function(_json){
            if (_json.codigo == 1){
                gritter(lang.refinanciacion_guardada_correctamente, true);
                $.fancybox.close();
            } else {
                gritter(_json.msgerror, false);
            }
        }
    });
    
}

function previsualizarRefinanciacion(){
    disabledInputs(true);
    var ctactes = new Array();
    for (var i = 0; i < tableIdx.length; i++){
        var codigo_ctacte = tableIdx[i];
        if (arrCtacte[codigo_ctacte] == 1){
            ctactes.push(codigo_ctacte);
        }
    }
    var cantidad_cuotas = $("[name=cantidad_cuotas]").val();
    var porcentaje = $("[name=porcentaje]").val();
    var porcentaje_aplica = $("[name=porcentaje_aplica]").val();
    var fecha_primer_pago = $("[name=fecha_primer_pago]").val();
     var valor_refinanciar = $("[name=importe_seleccionado]").val();
    var periodicidad = $("#periodicidad").val();    
    var mensaje = '';
    if (isNaN(parseInt(cantidad_cuotas))) mensaje += lang.debe_indicar_la_cantidad_de_cuotas + "<br>";
    if (ctactes.length == 0) mensaje += lang.debe_indicar_por_lo_menos_un_items_para_financiar + "<br>";
    if (fecha_primer_pago == '' || !validarFecha(fecha_primer_pago)) mensaje += lang.la_fecha_para_el_primer_pago_no_es_valida + '<br>';
    if (mensaje != ''){
        gritter(mensaje, false);
    } else {
        $.ajax({
            url: BASE_URL + 'ctacte/getDetalleRefinanciacion',
            type: 'POST',
            dataType: 'json',
            data: {
                cuotas: cantidad_cuotas,
                ctacte: ctactes,
                interesporc: porcentaje,
                fechaPrimerPago: fecha_primer_pago,
                periodicidad: periodicidad,
                valor_refinanciar :valor_refinanciar ,
                porcentaje_aplica: porcentaje_aplica
            },
            success: function(_json){
                if (_json.codigo && _json.codigo == 0){
                    gritter(_json.msgerror, false);
                } else {
                    graphicDetalleFinanciacion(_json);
                    $("[name=enviarForm]").hide();
                    $("#vista_1").hide();
                    $("[name=btn_guardar]").show();
                    $("[name=btn_volver]").show();
                    $("[name=enviarForm]").hide();
                    $("#vista_2").show();
                    $.fancybox.update();
                }
            }
        });
    }
    disabledInputs(false);
}

function disabledInputs(disabled){
    $("[name=cantidad_cuotas]").attr("disabled", disabled);
    $("[name=porcentaje]").attr("disabled", disabled);
    $("[name=porcentaje_aplica]").attr("disabled", disabled);
    $("[name=enviarForm]").attr("disabled", disabled);
    $("[name=fecha_primer_pago]").attr("disabled", disabled);
    $("#periodicidad").attr("disabled", disabled);
    $("#btn_volver").attr("disabled", disabled);
    $("#btn_guardar").attr("disabled", disabled);
}

function volverARefinanciacion(){
    $("#vista_2").hide();
    $("[name=btn_guardar]").hide();
    $("[name=btn_volver]").hide();
    $("[name=enviarForm]").show();
    $("#vista_1").show();
    $.fancybox.update();
}