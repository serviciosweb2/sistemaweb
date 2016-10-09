  
// variables necesarias solo para la vista de frm_ctacte
var codigo = '';
var commentFile = '';
var FILTRO_CON_SALDO = "";
var FILTRO_HABILITADAS = "habilitada";

jQuery.fn.dataTableExt.oSort['uk_date-asc'] = function(a, b) {
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
    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
};

jQuery.fn.dataTableExt.oSort['uk_date-desc'] = function(a, b) {
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
    return ((x < y) ? 1 : ((x > y) ? -1 : 0));
};

$.fn.dataTable.ext.search.push(
    function(settings, data, dataIndex){
        if (settings.sTableId == 'detalleResumen'){
            var CON_SALDO = data[6];
            var HABILITADA = data[8];
            if (CON_SALDO == FILTRO_CON_SALDO || FILTRO_CON_SALDO == ""){
                if (HABILITADA == FILTRO_HABILITADAS || FILTRO_HABILITADAS == ""){
                    return true;
                }
            }
            return false;
        } else {
            var retorno = true;
            return retorno;
        }
    }
);

var oTableInputaciones, oTableFacturas;
var oTable;
var contexDtalle = [];
var codigoCTACTE = '';
var codigo_ctacte = '';

function ver_comentarios(cod_ctacte){
    $.ajax({
        url: BASE_URL + 'ctacte/frm_comentarios',
        data: 'cod_ctacte=' + cod_ctacte,
        type: 'POST',
        cache: false,
        success: function(respuesta){
            $.fancybox.open(respuesta, {
                width: '50%',
                height: 'auto',
                autoSize: false,
                autoResize: false,
                padding: 0,
                openEffect: 'none',
                closeEffect: 'none',
                helpers: {
                    overlay: null
                }
            });
        }
    });
}

function volver() {
    $('#contenedor_administracioResumenCuenta').show();
    $('#contenedor_detalleResumen').hide();
    $("#area_tablas_3").hide();
    $("#areaTablas").show();
    $('.breadcrumb').find('li').last().remove();
}

function buscarDetalle(element) {
    var valor = $(element).val();
    oTableDetalle
            .search(valor)
            .draw();
}

function imprimirDetalleCtacte() {
    var cod_alumno = codigo;
    var param = new Array();
    var consaldo = $("[name=common_filters]").prop("checked") ? 1 : 0;
    param.push(cod_alumno + "|" + consaldo);
    printers_jobs(3, param);
    $.fancybox.close(true);
}

function getDetalle(tipo, respuesta) {
    var nomPeticion = tipo == 'getFacturas' ? 'faturas' : 'inputaciones';
    var vistaTabla = '<table id="dCtacte" class="table table-striped table-bordered dataTable" cellspacing="0" cellpadding="0" border="0">';
    if (tipo == 'getFacturas') {
        $('#modalDtalle h4').html('Facturas');
        vistaTabla += '<thead><th>numero</th><th>tipo</th><th>importe</th></thead>';
        vistaTabla += '<tbody>';
        $(respuesta).each(function(k, valores) {
            vistaTabla += '<tr><td>' + valores.nrofact + '</td><td>' + valores.factura + '</td><td>' + valores.importeformateado + '</td></tr>';
        });
    } else {
        $('#modalDtalle h4').html('Inputaciones');
        vistaTabla += '<thead><th>fecha</th><th>medio pago</th><th>importe</th><th>estado</th></thead>';
        vistaTabla += '<tbody>';
        $(respuesta).each(function(k, valores) {
            vistaTabla += '<tr><td>' + valores.fechareal + '</td><td>' + valores.medio + '</td><td>' + valores.importeformateado + '</td><td>' + valores.estado + '</td></tr>';
        });
    }
    vistaTabla += '</tbody>';
    vistaTabla += '</table>';
    $('.contenedorTabla').empty().html(vistaTabla);
    $('#dCtacte').dataTable({
        "iDisplayLength": 4,
        "aaSorting": []
    });
}

function addInputaciones(inputaciones) {
    oTableInputaciones.clear().draw();
    $(inputaciones).each(function(k, I) {
        oTableInputaciones.row.add([I.fechareal, I.medio, I.importeformateado, I.estado]).draw();
    });
}

function addFacturas(rows) {
    oTableFacturas.clear().draw();
    $(rows).each(function(k, F) {
        oTableFacturas.row.add([F.fecha, F.tipo_numero, F.importeformateado]).draw();
    });
}

function getInputacionesFacturas(codigo) {
    $('.popover').hide();
    $.ajax({
        url: BASE_URL + "ctacte/getImputaciones_facturas",
        type: "POST",
        data: {'cod_ctacte': codigo},
        dataType: "JSON",
        cache: false,
        success: function(respuesta) {
            addInputaciones(respuesta.imputaciones);
            addFacturas(respuesta.facturas);
        }
    });
    $('#modalDtalle').modal();
    return false;
}

function addCtaCte(ctacte) {
    $(ctacte).each(function(k, valor) {
        var icon = valor.tinecomentarios == 1 ? '' : 'hide';
        if (valor.tiene_descuentos) {
            iconoFecha = "&nbsp<i class='icon-ok icon-info-sign btn-detalle' name='icon_ver_detalle' id='" + valor.codigo + "' style='cursor: pointer;'></i>";
        } else {
            if (valor.dto_perdido){
                iconoFecha = "&nbsp<i class='icon-ok icon-info-sign red' title='" + lang.perdio_descuento + "'></i>";
            }else{
                iconoFecha = "";
            }
        }

        if (valor.habilitado === '2') {
            icono = "<i class='icon-ok icon-info-sign' title='" + lang.deuda_pasiva + "'></i>";
        } else {
            icono = "";
        }
        if (valor.habilitado === '0') {
            inhabilita = "<label style='color: red'>" + lang.inhabilitada + "</label>";
        } else {
            inhabilita = "";
        }

        oTableDetalle.row.add([
            valor.codigo,
            valor.descripcion + '   <i style ="cursor:pointer;" class="icon-comment grey ' + icon + '" onclick="ver_comentarios(' + valor.codigo + ')"></i>' + icono + inhabilita,
            valor.importeformateado + iconoFecha,
            valor.saldoformateado,
            valor.fechavenc,
            '<button  class="btn btn-info btn-xs" onClick="getInputacionesFacturas(' + valor.codigo + ');">ver</button>',
            valor.filtro,
            valor.cod_concepto,
            valor.filtro2
        ]).draw();
        
        $('#detalleResumen tbody tr .btn-detalle').each(function() {
            $(this).popover({
                title: '',
                html: true,
                trigger: 'click',
                placement: 'left'
            });
        });
        
    });
}

function filtrar() {
    if ($('#con_saldo').is(':checked')) {
        FILTRO_CON_SALDO = 'consaldo';
    } else {
        FILTRO_CON_SALDO = '';
    }
    if ($('#habilitadas').is(':checked')) {
        FILTRO_HABILITADAS = 'habilitada';
    } else {
        FILTRO_HABILITADAS = '';
    }
    oTableDetalle.draw();
}

function frm_nuevactacte() {
    $.ajax({
        url: BASE_URL + "ctacte/frm_nueva_ctacte",
        type: "POST",
        data: {codigo: codigo},
        dataType: "",
        cache: false,
        success: function(respuesta) {
            $.fancybox.open(respuesta, {
                scrolling: 'auto',
                padding: 0,
                openEffect: 'none',
                closeEffect: 'none',
                helpers: {
                    overlay: null
                }
            });

        }
    });
}

function cambioConceptos() {
    disabledInputs(false);
}

function volverAFinanciacion() {
    $("#paso_2").addClass("hide");
    $("[name=btn_guardar]").hide();
    $("[name=btn_volver]").hide();
    $("[name=enviarForm]").show();
    $("#paso_1").removeClass("hide");
  $.fancybox.update();
}

var thead = [];
var data = '';
var oTable = '';
var aoColumnDefs = columns;
var lang = BASE_LANG;
var menu = menuJson;
var claves = Array(
        "habilitado_curso", "quitar_descuento_condicionado", "reactivar_descuento_condicionado",
        "codigo", "debe_ctacte", "estadoctacte", "estado", "fechas_cambiadas_correctamente",
        "no_se_ha_podido_calcular_el_total", "refinanciacion_guardada_correctamente", "debe_indicar_la_cantidad_de_cuotas",
        "debe_indicar_un_porcentaje_valido", "debe_indicar_por_lo_menos_un_items_para_financiar",
        "la_fecha_para_el_primer_pago_no_es_valida", 'habilitadas', 'deuda_pasiva', 'inhabilitada', 'no_debe_ctacte',
        'consaldo', 'todas', "sinsaldo", "filtrar", "fecha", "validacion_ok", "tipo", "importe", "numero", "medio_de_pago"        
    );


function getCtaCte() {
    oTableDetalle.clear().draw();
    $.ajax({
        url: BASE_URL + 'ctacte/frm_ctacte',
        data: 'codigo=' + codigo,
        type: 'POST',
        cache: false,
        dataType: 'JSON',
        success: function(respuesta){
            if (respuesta.codigo == 1){
                $('#habilitadas').prop("checked", true);
                FILTRO_HABILITADAS = "habilitada";
                addCtaCte(respuesta.ctacte);
                $('#contenedor_administracioResumenCuenta').hide();
                $('#contenedor_detalleResumen').show();
                var Nombre = '<li><a href="javascript:void(0)">' + respuesta.nombre_apellido + '</a></li>';
                $('.breadcrumb').append(Nombre);
            } else {
                gritter(respuesta.msgerror, false);
            }
        }
    });
}

$(document).ready(function(){    
    $(menu.contextual).each(function(k, option) {        
        if (option.accion == 'agregar_comentario'){
            contexDtalle.push(option);
            menu['contextual'].splice(k);
        }
        if (option.accion == 'baja_ctacte'){
            contexDtalle.push(option);
            menu['contextual'].splice(k);
        }        
        if (option.accion == 'agregar_descuento'){
            contexDtalle.push(option);
            menu['contextual'].splice(k);
        }
    });
    $('#div_table_filters').hide();
    $('#contenedor_detalleResumen').hide();
    init();
});

function init() {
    var filtro = 0;
    var desactivado = '';
    $(aoColumnDefs).each(function() {
        thead.push(this.sTitle);
    });
    
    $("#table_descuentos_condicionados_perdidos").dataTable();
    $("#table_descuentos_condicionados_perdidos_wrapper").find(".row").first().hide();
    
    function validarCheck() {
        var chekiados = 0;
        $('#areaTablas').find('input[name="alumnos[]"]').each(function() {
            $(this).is(':checked') ? chekiados++ : '';
        });

        var disabled = chekiados == 0 ? true : false;
        $(".dataTables_length").find('button[accion="enviar_aviso_ctacte"]').prop('disabled', disabled);
    }

    function columnName(name) {
        var retorno = '';
        $(thead).each(function(key, valor) {
            if (valor == name) {
                retorno = key;
            }
        });
        return retorno;
    }

    function devolverEstado(baja, valor) {
        var texto = lang.no_debe_ctacte;
        var classEstado = 'label-success';
        if (baja == 1) {
            texto = lang.debe_ctacte;
            var classEstado = 'label-default verDetalleDeudor';
        }
        return '<span data-alumno="' + valor + '" class="label ' + classEstado + ' arrowed">' + texto + '</span>';
    }

    function getCheckbox(row) {
        var x = '<div class="checkbox">';
        x += '<label><input name="alumnos[]" class="ace ace-checkbox-2" type="checkbox" value="' + row + '">';
        x += '<span class="lbl"></span></label></div>';
        return x;
    }

    function getParametro() {
        return filtro;
    }


    var columna = columnName(lang.debe_ctacte);
    var columnaEstado = columnName(lang.estadoctacte);
    var columnaCodigo = columnName(lang.codigo);
    oTable = $('#administracioResumenCuenta').dataTable({
        "bProcessing": false,
        "bServerSide": true,
        "sAjaxSource": BASE_URL + 'ctacte/listar',
        "sServerMethod": "POST",
        "aaSorting": [[0, "desc"]],
        'aoColumnDefs': aoColumnDefs,
        "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            var imgTag = devolverEstado(aData[columna], aData[columnaCodigo]);
            var check = getCheckbox(aData[columnaCodigo]);
            $(nRow).find('td').eq(columnaEstado - 1).html(imgTag);
            return nRow;
        },
        "fnServerParams": function(aoData) {
            aoData.push({"name": 'parametro', "value": getParametro()});
        }
    });

    marcarTr();
    var _html = '';
    $.each(menu.superior, function(key, value){
        _html += '<button class="btn btn-info boton-promario" accion="' + value.accion + '" style="margin-right: 20px;">';
        _html += value.text;
        _html += '</button>';
    });
    $(".dataTables_length").html(_html);
    $('#areaTablas').on('mousedown', '#administracioResumenCuenta tbody tr', function(e) {
        var sData = oTable.fnGetData(this);
        codigo = sData[columnName(lang.codigo)];
        if (e.button === 2) {            
            desactivado = sData[columnName(lang.habilitado_curso)];
            codigo = sData[columnName(lang.codigo)];
            generalContextMenu(menu.contextual, e);
            return false;
        }
    });

    $('body').on('click', '#menu a', function() {              
        var accion = $(this).attr('accion');
        var id = $(this).attr('id');
        $('#menu').remove();
        switch (accion) {
            case 'cambio_vencimiento_ctacte':
                $.ajax({
                    url: BASE_URL + 'ctacte/frm_cambio_vencimiento',
                    type: 'POST',
                    data: {
                        codigo_alumno: codigo
                    },
                    success: function(_html) {
                        $.fancybox.open(_html, {
                            scrolling: 'auto',
                            padding: 0,
                            openEffect: 'none',
                            closeEffect: 'none',
                            helpers: {
                                overlay: null
                            }
                        });
                    }
                });
                break;

            case 'ver-ctacte':
                getCtaCte();
                break;

            case 'refinanciar_ctacte':
                $.ajax({
                    url: BASE_URL + 'ctacte/frm_refinanciar',
                    data: 'codigo_alumno=' + codigo,
                    type: 'POST',
                    cache: false,
                    success: function(respuesta) {
                        $.fancybox.open(respuesta, {
                            scrolling: 'auto',
                            padding: 0,
                            openEffect: 'none',
                            closeEffect: 'none',
                            helpers: {
                                overlay: null
                            }
                        });
                    }
                });

                break;

            case 'notacredito_ctacte':
                $.ajax({
                    url: BASE_URL + 'ctacte/frm_ctactePagas',
                    data: 'codigo_alumno=' + codigo,
                    type: 'POST',
                    cache: false,
                    success: function(respuesta) {
                        $.fancybox.open(respuesta, {
                            scrolling: 'auto',
                            height: 'auto',
                            padding: 0,
                            openEffect: 'none',
                            closeEffect: 'none',
                            helpers: {
                                overlay: null
                            }
                        });
                    }
                });
                break;
                
            case 'agregar_comentario':
                $.ajax({
                    url: BASE_URL + 'ctacte/frm_comentarios',
                    data: 'cod_ctacte=' + codigoCTACTE,
                    type: 'POST',
                    cache: false,
                    success: function(respuesta) {
                        $.fancybox.open(respuesta, {
                            width: '50%',
                            height: 'auto',
                            autoSize: false,
                            autoResize: false,
                            padding: 0,
                            openEffect: 'none',
                            closeEffect: 'none',
                            helpers: {
                                overlay: null
                            }
                        });
                    }
                });
                break;
                
            case 'agregar_descuento':                
                ver_detalle_descuento_condiocionado(codigoCTACTE); 
                break;
                
            case 'baja_ctacte':
                $.ajax({
                    url: BASE_URL + 'ctacte/frm_baja',
                    data: 'cod_ctacte=' + codigoCTACTE,
                    type: 'POST',
                    cache: false,
                    success: function(respuesta) {
                        $.fancybox.open(respuesta, {
                            width: '50%',
                            height: 'auto',
                            autoSize: false,
                            autoResize: false,
                            padding: 0,
                            openEffect: 'none',
                            closeEffect: 'none',
                            helpers: {
                                overlay: null
                            }
                        });
                    }
                });
                break;
        }
        return false;
    });

    $('#areaTablas').on('click', '.verDetalleDeudor', function() {
        var idAlumno = $(this).attr('data-alumno');
        var dataPOST = 'cod_alumno%5B%5D=' + idAlumno + '&mostrar=1';
        $.ajax({
            url: BASE_URL + 'ctacte/frmCtaCteAlumno',
            type: "POST",
            data: dataPOST,
            dataType: "",
            cache: false,
            success: function(respuesta) {
                $.fancybox.open(respuesta, {
                    scrolling: 'auto',
                    padding: 0,
                    openEffect: 'none',
                    closeEffect: 'none',
                    helpers: {
                        overlay: null
                    }
                });
            }
        });
        return false;
    });


    $('#areaTablas').on('click', 'button[accion="enviar_aviso_ctacte"]', function() {
        $.ajax({
            url: BASE_URL + 'ctacte/frmAvisodeudores',
            type: "POST",
            data: "",
            dataType: "",
            cache: false,
            success: function(respuesta) {
                $.fancybox.open(respuesta, {
                    autoSize: false,
                    width: '60%',
                    scrolling: 'auto',
                    height: 'auto',
                    padding: '0',
                    openEffect: 'none',
                    closeEffect: 'none',
                    helpers: {
                        overlay: null
                    }
                });
            }
        });
        return false;
    });

    oTableInputaciones = $('#inputaciones').DataTable({
        "lengthChange": false,
        "searching": false,
        "paging": false,
        "info": false,
        "aoColumns": [
            {"sTitle": lang.fecha, "sClass": "center", "bVisible": true, "sType": "uk_date"},
            {"sTitle": lang.medio_de_pago, "sClass": "center", "bVisible": true},
            {"sTitle": lang.importe, "sClass": "center", "bVisible": true},
            {"sTitle": lang.estado, "sClass": "center", "bVisible": true}
        ]
    });

    oTableFacturas = $('#facturas').DataTable({
        "lengthChange": false,
        "searching": false,
        "paging": false,
        "info": false,
        "aoColumns": [
            {"sTitle": lang.fecha, "sClass": "center", "bVisible": true},
            {"sTitle": lang.tipo, "sClass": "center", "bVisible": true},
            {"sTitle": lang.importe, "sClass": "center", "bVisible": true}

        ]
    });

   oTableDetalle = $('#detalleResumen').DataTable({
        "lengthChange": false,
        "iDisplayLength": 20,
        "aaSorting": [],
        "aoColumnDefs": [
            {"sType": "uk_date", "aTargets": [4]}
        ],
        "order": [[ 4, "asc" ]]
    });

    oTableDetalle.column([0, 6, 7, 8]).visible(false);
    $('#detalleResumen_filter').closest('.row').hide();
    $('#areaTablas_2').on('mousedown', '#detalleResumen tr', function(e){        
        if (e.button === 2){
            $('.popover').hide();
            commentFile = this;
            var aData = oTableDetalle.row(this).data();
            codigoCTACTE = aData[0];
            generalContextMenu(contexDtalle, e);
            if (aData[7] == '1' || aData[7] == '5') {
                $(contexDtalle).each(function(k, option){
                    if (option.accion == 'baja_ctacte'){
                        $("#menu a[accion=baja_ctacte]").closest("li").hide();
                    }
                });
            };
        }
    });

    $('#detalleResumen').on('draw.dt', function(){
        $.fancybox.update();
    });

    $('#facturas_wrapper').find('.row').eq(0).hide();
    $('#inputaciones_wrapper').find('.row').eq(0).hide();
    $('.btn').each(function() {
        var cod_ctacte = $(this).attr('href');
        $(this).popover({
            trigger: 'manual',
            html: true,
            placement: 'left',
            title: 'Descripcion'
        });
    });

    var filter = 0;
    
    function mostrarMenu() {
        $("[name=contenedorPrincipal]").toggle();
        $("[name=div_table_filters]").toggle(300);
    }

    $('[name=table_filters]').on('click', function() {
        mostrarMenu();
    });
    
    $('#areaTablas').on('click', 'button[accion="recuperar_descuentos_condicionados"]', function(){
        $("#areaTablas").hide();
        $("#area_tablas_3").show();        
    });
    
    $('#detalleResumen tbody tr .btn-detalle').each(function() {
        $(this).popover({
            title: '',
            html: true,
            trigger: 'click',
            placement: 'left'
        });
    });
    
    $("#detalleResumen").on("click", "[name=icon_ver_detalle]", function(){
        if ($(this).parent().find('.popover').is(':visible')) {
            $('.popover').popover('hide');
        } else {
            var cod_ctacte = $(this).attr("id");
            var element = $(this);
            $.ajax({
                url: BASE_URL + 'ctacte/getDescuentos',
                type: 'POST',
                dataType: 'json',
                data: {
                    cod_ctacte: cod_ctacte
                },
                success: function(_json){
                    var descuentos_plan_de_pago = false;
                    var _html = '';
                    _html += '<table class="table table-striped table-bordered table-hover">';
                    _html += '<thead>';
                    _html += '<tr>';
                    _html += '<th>';
                    _html += lang.tipo_de_descuento;
                    _html += '</th>';
                    _html += '<th>';
                    _html += lang.porcentaje;
                    _html += '</th>';
                    _html += '<th>';
                    _html += lang.fecha_hasta_;
                    _html += '</th>';
                    _html += '<th>';
                    _html += '&nbsp;';
                    _html += '</th>';
                    _html += '</tr>';
                    _html += '</thead>';
                    _html += '<tbody>';
                    $.each(_json.data.descuentos, function(key, descuento){
                        var complemento = descuento.forma_descuento == 'plan_pago' ? 'style="color: green"' : '';
                        if (descuento.forma_descuento == 'plan_pago'){
                            descuentos_plan_de_pago = true;
                        }
                        _html += '<tr ' + complemento + '>';
                        _html += '<td>';
                        var estado = eval("lang." + descuento.estado);
                        _html += estado;
                        _html += '</td>';
                        _html += '<td>';
                        var importe =  eval("descuento.descuento.replace(/\\./g, '" + _json.data.separador_decimal + "')");
                        _html += importe;
                        _html += '</td>';
                        _html += '<td>';
                        _html += descuento.fecha_perdida_descuento != null ? descuento.fecha_perdida_descuento : "";
                        _html += '</td>';
                        _html += '<td>';
                        if (descuento.activo == '1'){
                            _html += '<i class="icon-trash bigger-140 red" style="cursor: pointer" onclick="eliminar_descuento_condicionado(' + descuento.codigo + ')" title="' + lang.quitar_descuento_condicionado + '"></i>';
                        } else {
                            _html += '<i class="icon-reply icon-only bigger-140 green" style="cursor: pointer" onclick="activar_descuento_condicionado(' + descuento.codigo + ')" title="' + lang.reactivar_descuento_condicionado + '"></i>';
                        }
                        _html += '</td>';
                        _html += '</tr>';
                    });
                    _html += '</tbody>';
                    _html += '</table>';
                    if (descuentos_plan_de_pago){
                        _html += '<label style="color: green; font-size: 11px;">';
                        _html += '<b>';
                        _html += '(*)';
                        _html += '</b>';
                        _html += lang.representan_descuentos_del_plan_de_pago;
                        _html += '</label>';
                        _html += '<br>';
                    }
                    _html += '<span style="cursor: pointer;" onclick="ver_detalle_descuento_condiocionado(' + cod_ctacte + ')">';
                    _html += lang.ver_detalle;
                    _html += '</span>';
                    $(element).attr('data-content', _html);   
                    $(element).popover({
                        html: true
                    });
                    $(element).popover('show');
                }
            });
        }
    });    
}

function verDescuentoPerdidoMatricula(cod_matricula){
    $.ajax({
        url: BASE_URL + 'ctacte/frm_recuperar_descuentos_condicionados',
        type: 'POST',
        data: {
            cod_matricula: cod_matricula
        },
        success: function(_html){
            $.fancybox.open(_html, {
                autoSize: false,
                width: '60%',
                scrolling: 'auto',
                height: 'auto',
                padding: '0',
                openEffect: 'none',
                closeEffect: 'none',
                helpers: {
                    overlay: null
                }
            });
        }
    });
}

function ver_detalle_descuento_condiocionado(cod_ctacte){
    $(".popover").hide();
    $.ajax({
        url: BASE_URL + 'ctacte/frm_agregar_descuento',
        data: {
            cod_ctacte: cod_ctacte
        },
        type: 'POST',
        cache: false,
        success: function(_html){
            $.fancybox.open(_html, {
               width: 'auto',
               height: 'auto',
               autoSize: false,
               autoResize: false,
               padding: 0,
               openEffect: 'none',
               closeEffect: 'none',
               helpers: {
                   overlay: null
               }
            });
        }
    });
}

function activar_descuento_condicionado(codigo_descuento_condicionado, element){
    $.ajax({
        url: BASE_URL + 'ctacte/activar_descuento_condicionado',
        type: 'POST',
        dataType: 'json',
        data:{
            codigo_descuento_condicionado: codigo_descuento_condicionado
        },
        success: function(_json){
            if (_json.codigo == 0){
                gritter(_json.error, false, '');
            } else {
                gritter(lang.validacion_ok, true, '');
                if (element){
                    var _html = '<i class="icon-trash bigger-140 red" style="cursor: pointer" onclick="eliminar_descuento_condicionado(' + codigo_descuento_condicionado + ', this)" title="' + lang.quitar_descuento_condicionado + '"></i>';
                    $(element).closest("td").html(_html);
                }
                getCtaCte(); // Ver
                $('#vista_importe_final').html(_json.importe)
            }
        }
    });
}

function eliminar_descuento_condicionado(codigo_descuento_condicionado, element){
    $.ajax({
        url: BASE_URL + 'ctacte/eliminar_descuento_condicionado',
        type: 'POST',
        dataType: 'json',
        data:{
            codigo_descuento_condicionado: codigo_descuento_condicionado
        },
        success: function(_json){
            if (_json.codigo == 0){
                gritter(_json.error, false, '');
            } else {
                gritter(lang.validacion_ok, true, '');
                if (element){
                    var _html = '<i class="icon-reply icon-only bigger-140 green" style="cursor: pointer" onclick="activar_descuento_condicionado(' + codigo_descuento_condicionado + ', this)" title="' + lang.reactivar_descuento_condicionado + '"></i>';
                    $(element).closest("td").html(_html);
                }
                getCtaCte();
                $('#vista_importe_final').html(_json.importe)
            }
        }
    });
}