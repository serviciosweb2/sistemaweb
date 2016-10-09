/**
 * Created by braulio on 06/06/16.
 */
/*******
 * INIT *
 *******/
var dateRange;
var iTable;
var iInnerTable;
var iTableCounter = 1;
var eTable;
var eInnerTable;
var eTableCounter = 1;
var modal = null;
var cod_movimiento_caja = 0;
var langvr = BASE_LANG;

$(document).ready(function () {

    /***************************
     * DATE RANGE PICKER [DRP] *
     **************************/
    var ranges = {};
    //ranges[langvr.hoy] = [moment(), moment()];
    //ranges[langvr.ayer] = [moment().subtract(1, 'days'), moment().subtract(1, 'days')];
    //ranges[langvr.ultima_semana] = [moment().subtract(6, 'days'), moment()];
    //ranges[langvr.ultimos_30_dias] = [moment().subtract(29, 'days'), moment()];
    ranges[langvr.este_mes] = [moment().startOf('month'), moment().endOf(39,'month')];
    ranges[langvr.ultimo_mes] = [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')];


    
    dateRange = $('input[name=date-range-picker]').daterangepicker({
        opens: 'left',
        startDate: moment(),
        endDate: moment(),
        maxDate: moment(),
        locale: {
            format: 'DD/MM/YYYY',
            cancelLabel: langvr.cancelar,
            applyLabel: langvr.aceptar,
            customRangeLabel: langvr.rango_perzonalizado
        },
        ranges: ranges,
        cancelClass: 'btn-danger'
    }, cb);

    /**
     * Callback del DRP esta comentado no estaría haciendo nada ahora
     * */
    function cb(start, end, label) {
        //$('input[name=date-range-picker] span').html(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
    }

    /**
     * Abre el DRP tambien al clickear en el icono del calendario que no viene por defecto
     * */
    $('.fecha').on('click', 'span', function () {
        if(!dateRange.is(":focus")) {
            dateRange.focus();
        }
    });

    /**
     * Llama getData() al seleccionar una fecha en el DRP
     * */
    $(dateRange).on('apply.daterangepicker', function(ev, picker) {
        start = picker.startDate.format('YYYY-MM-DD');
        end = picker.endDate.format('YYYY-MM-DD');
        getData(start, end);
    });

    /**
     * Devuelve la fecha del DRP de DD/MM/YYYY a YYYY-MM-DD para la consulta a la DB
     * */
    function formatearFecha(fecha) {
        var arrFecha = fecha.split("/");
        var fecha_nueva = arrFecha[2] + "-" + arrFecha[1] + "-" + arrFecha[0];

        return fecha_nueva;
    }

    /***********************
     * ARMA TABLAS INTERNAS *
     ***********************/
    function fnFormatDetails(table_id, tipo) {
        var sOut = '<table id="tabla_' + tipo + '_' + table_id + '" width="100%" class="table table-striped table-condensed table-hover tabla-interna"></table>';
        return sOut;
    }

    /**
     * Obtengo las fechas del dateRangePicker cuando carga la vista
     * */
    fechas = dateRange[0].value;
    start = fechas.substring(0,10);
    end = fechas.substring(fechas.length - 10, fechas.length);

    start = formatearFecha(start);
    end = formatearFecha(end);

    /**
     * Obtengo data cuando carga la vista
     * */
    getData(start, end);

    /**
     * Retorna el reporte de rentabilidad
     * @param String start [fecha_desde]
     * @param String end [fecha_hasta]
     * @return deTodo
     */
    function getData(start, end) {
        
        $.ajax({
            url: BASE_URL+"reportes/getReporteRentabilidad2",
            type: "POST",
            data: {
                fecha_desde: start,
                fecha_hasta: end
            },
            dataType: "JSON",
            cache:false,
            success: function(data_ajax){

                /* Envía datos principales [data_ajax.main] a la vista */
                $('#porc_total').text(data_ajax.main.porc_total + ' %');
                $('#total').text(data_ajax.main.utilidad);
                $('#ingreso').text(data_ajax.main.ingreso);
                $('#egreso').text(data_ajax.main.egreso);

                /* Agrega celda vacia en encabezado de las tablas principales para luego insertar
                 * los botones desplegables y la columna de nro de registro */
                data_ajax.colIngreso = [
                    {
                        sTitle: '',
                        width: '20px',
                        bSortable: false
                    },
                    {
                        sTitle: '#',
                        width: '20px',
                        bSortable: false
                    },
                    {
                        sTitle: langvr.concepto,
                        bSortable: false
                    },
                    {
                        sTitle: langvr.valor,
                        width: '80px',
                        sClass: 'text-right',
                        bSortable: false
                    }
                ];

                data_ajax.colEgreso = [
                    {
                        sTitle: '',
                        width: '20px',
                        bSortable: false
                    },
                    {
                        sTitle: '#',
                        width: '20px',
                        bSortable: false
                    },
                    {
                        sTitle: langvr.concepto,
                        bSortable: false
                    },
                    {
                        sTitle: langvr.valor,
                        width: '80px',
                        sClass: 'text-right',
                        bSortable: false
                    }
                ];

                /* Agrega nro de registro e iconos (>) en cada fila (ambas tablas) */
                if(data_ajax.detIngreso.length != 0) {
                    $(data_ajax.detIngreso).each(function (index, element) {
                        this.unshift(index+1);
                        this.unshift('<a href="" class="green" data-key-concepto="' + this[3] + '" ><i class="icon-chevron-right"></i></a>');
                    });
                }

                if(data_ajax.detEgreso.length != 0){
                    $(data_ajax.detEgreso).each(function (index, element) {
                        this.unshift(index+1);
                        this.unshift('<a href="" class="red" data-key-concepto="' + this[3] + '" data-id-subrubro="' + this[4] + '" ><i class="icon-chevron-right"></i></a>');
                    });
                }

                /******************
                 * TABLA INGRESOS *
                 *****************/
                if(iTable){
                    iTable.fnClearTable();
                    if(data_ajax.detIngreso.length != 0)
                        iTable.fnAddData(data_ajax.detIngreso);
                    else
                        gritter(langvr.no_ingresos_para_rango_fechas, false, "Error");
                    iTable.fnDraw();
                } else {
                    iTable = $('#tabla_ingresos').dataTable({
                        aaData: data_ajax.detIngreso,
                        aoColumns: data_ajax.colIngreso,
                        iDisplayLength: 10,
                        ordering: false,
                        bAutoWidth: false, /* for better responsiveness :P */
                        fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                            /* Estilos para la columna nro de registro */
                            $('td:eq(1)', nRow).addClass('disabled active');
                        }
                    });
                }
                /**************************************************
                 * CLICK EN EL AMPLIAR DE UNA FILA(+) DE INGRESOS *
                 *************************************************/
                $('#tabla_ingresos tbody').off().on('click', 'tr td:first-child a', function (e) {
                    e.preventDefault();
                    var nTr = $(this).parents('tr')[0];
                    var concepto = $(this).attr('data-key-concepto');
                    var fechas = dateRange[0].value;
                    var start = fechas.substring(0,10);
                    var end = fechas.substring(fechas.length - 10, fechas.length);

                    start = formatearFecha(start);
                    end = formatearFecha(end);

                    /* Si esta abierta, cerrar fila, sino la abre */
                    if (iTable.fnIsOpen(nTr)) {
                        $(this).find('i').removeClass('icon-rotate-90');
                        iTable.fnClose(nTr);
                    }
                    else {
                        $(this).find('i').addClass('icon-rotate-90');

                        /********
                         * AJAX *
                         *******/
                        $.ajax({
                            url: BASE_URL+"reportes/getDetallesIngreso",
                            type: "POST",
                            data: {
                                concepto: concepto,
                                fecha_desde: start,
                                fecha_hasta: end
                            },
                            dataType: "JSON",
                            cache:false,
                            success: function(data_interna_I){

                                /* Agrega encabezados sub-tabla al obj devuelto */
                                data_interna_I.colIngreso = [
                                    {
                                        sTitle: '#',
                                        width: '20px',
                                        sClass: 'disabled active',
                                        sortable: false
                                    },
                                    {
                                        sTitle: langvr.descripcion
                                    },
                                    {
                                        sTitle: langvr.imputado,
                                        width: '90px',
                                        sClass: 'text-right'
                                    }
                                ];

                                //Abre TR
                                iTable.fnOpen(nTr, fnFormatDetails(iTableCounter, 'ingresos'), 'details');

                                /* Comentar o sacar esta linea cuando no se necesite mas el footer con el total
                                 y la fnFooterCallback de la inicializacion de la tabla
                                $("#tabla_ingresos_" + iTableCounter).append('<tfoot><tr><th></th><th></th></tr></tfoot>'); */

                                /* Agrega la columna del nro de fila a cada fila */
                                if(data_interna_I.detIngreso.length != 0){
                                    $(data_interna_I.detIngreso).each(function () {
                                        this.unshift('nro_fila');
                                    });
                                }

                                //Inicia sub-tabla ingresos
                                iInnerTable = $("#tabla_ingresos_" + iTableCounter).dataTable({
                                    aaData: data_interna_I.detIngreso,
                                    aoColumns: data_interna_I.colIngreso,
                                    bFilter: true,
                                    bInfo: true,
                                    bPaginate: true,
                                    bAutoWidth: false, //for better responsiveness :P
                                    bRetrieve: true, //reqired to show mutiple subtables at once
                                    ordering: false,
                                    fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                                        /* Imprime nro de fila en la tabla */
                                        var index = iDisplayIndexFull +1;
                                        $('td:eq(0)', nRow).html(index);
                                        return nRow;
                                    }/*,
                                    fnFooterCallback: function ( nRow, aaData, iStart, iEnd, aiDisplay ) {

                                        var iTotal = 0;
                                        for ( var i=0 ; i<aaData.length ; i++ )
                                        {

                                            while(aaData[i][1].charAt(0) === '$')
                                                aaData[i][1] = aaData[i][1].substr(1);

                                            iTotal += parseInt(aaData[i][1]);
                                        }

                                        var iPage = 0;
                                        for ( var i=iStart ; i<iEnd ; i++ )
                                        {

                                            while(aaData[aiDisplay[i]][1].charAt(0) === '$')
                                                aaData[aiDisplay[i]][1] = aaData[aiDisplay[i]][1].substr(1);

                                            iPage += parseInt(aaData[ aiDisplay[i] ][1]);
                                        }

                                        var nCells = nRow.getElementsByTagName('th');
                                        nCells[1].innerHTML = '$' + iPage +' [ Total: $'+ iTotal +' ]';
                                    }*/
                                });

                                iTableCounter = iTableCounter + 1;

                                //Fix - Devuelve el focus al input de los datatables internos al tipear
                                var iInput = $(iInnerTable.parent().find('input[type="search"]'));
                                $(document).delegate(iInput, 'keyup', function () {
                                    $(iInput).focus();
                                });

                            }
                        });

                    }
                });

                /*****************
                 * TABLA EGRESOS *
                 ****************/
                if(eTable){
                    eTable.fnClearTable();
                    if(data_ajax.detEgreso.length != 0)
                        eTable.fnAddData(data_ajax.detEgreso);
                    else
                        gritter(langvr.no_egresos_para_rango_fechas, false, "Error");
                    eTable.fnDraw();
                } else {
                    eTable = $('#tabla_egresos').dataTable({
                        aaData: data_ajax.detEgreso,
                        aoColumns: data_ajax.colEgreso,
                        iDisplayLength: 10,
                        bAutoWidth: false, //for better responsiveness :P
                        ordering: false,
                        fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                            /* Estilos para la columna nro de registro */
                            $('td:eq(1)', nRow).addClass('disabled active');
                        }
                    });
                }
                /*************************************************
                 * CLICK EN EL AMPLIAR DE UNA FILA(+) DE EGRESOS *
                 ************************************************/
                $('#tabla_egresos tbody').off().on('click', 'tr td:first-child a', function (e) {
                    e.preventDefault();
                    var nTr = $(this).parents('tr')[0];
                    var gasto = $(this).attr('data-key-concepto');
                    var cod_sub = $(this).attr('data-id-subrubro');
                    var fechas = dateRange[0].value;
                    var start = fechas.substring(0,10);
                    var end = fechas.substring(fechas.length - 10, fechas.length);

                    start = formatearFecha(start);
                    end = formatearFecha(end);

                    /* Si esta abierta, cerrar fila, sino la abre */
                    if (eTable.fnIsOpen(nTr)) {
                        $(this).find('i').removeClass('icon-rotate-90');
                        eTable.fnClose(nTr);
                    }
                    else {
                        $(this).find('i').addClass('icon-rotate-90');

                        /********
                         * AJAX *
                         *******/
                        $.ajax({
                            url: BASE_URL+"reportes/getDetalleGasto",
                            type: "POST",
                            data: {
                                gasto: gasto,
                                fecha_desde: start,
                                fecha_hasta: end,
                                cod_sub: cod_sub
                            },
                            dataType: "JSON",
                            cache:false,
                            success: function(data_interna_E){

                                var menu = data_interna_E.menu

                                /* Agrega encabezados sub-tabla al obj devuelto */
                                data_interna_E.colEgreso = [
                                    {
                                        sTitle: '#',
                                        width: '20px',
                                        sClass: 'disabled active',
                                        bSortable: false
                                    },
                                    {
                                        sTitle: langvr.descripcion,
                                        /* Agrega un input[type='hidden'] con el id del movimiento de caja para tomarlo
                                         * al hacer click derecho en cada row y enviarlo a modar a traves del contextMenu */
                                        mRender: function ( data, type, full ) {
                                            var value = data + '<input type="hidden" name="id" id="id" value="'+full[3]+'" />';
                                            return value;
                                        }
                                    },
                                    {
                                        sTitle: langvr.imputado,
                                        width: '90px',
                                        sClass: 'text-right'
                                    }
                                ];

                                //Abre TR
                                eTable.fnOpen(nTr, fnFormatDetails(eTableCounter, 'egresos'), 'details');

                                /* Comentar o sacar esta linea cuando no se necesite mas el footer con el total
                                 y la fnFooterCallback de la inicializacion de la tabla
                                $("#tabla_egresos_" + eTableCounter).append('<tfoot><tr><th></th><th></th></tr></tfoot>'); */

                                /* Agrega la columna del nro de fila a cada fila */
                                if(data_interna_E.detEgreso.length != 0){
                                    $(data_interna_E.detEgreso).each(function () {
                                        this.unshift('nro_fila');
                                    });
                                }

                                //Inicia sub-tabla
                                eInnerTable = $("#tabla_egresos_" + eTableCounter).dataTable({
                                    aaData: data_interna_E.detEgreso,
                                    aoColumns: data_interna_E.colEgreso,
                                    bFilter: true,
                                    bInfo: false,
                                    bPaginate: true,
                                    bAutoWidth: false, //for better responsiveness :P
                                    bRetrieve: true, //reqired to show mutiple subtables at once
                                    ordering: false,
                                    fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                                        /* Imprime nro de fila en la tabla */
                                        var index = iDisplayIndexFull +1;
                                        $('td:eq(0)', nRow).html(index);
                                        return nRow;
                                    }/*,
                                    fnFooterCallback: function ( nRow, aaData, iStart, iEnd, aiDisplay ) {

                                        var eTotal = 0;
                                        for ( var i=0 ; i<aaData.length ; i++ )
                                        {

                                            while(aaData[i][1].charAt(0) === '$')
                                                aaData[i][1] = aaData[i][1].substr(1);

                                            eTotal += parseInt(aaData[i][1]);
                                        }

                                        var ePage = 0;
                                        for ( var i=iStart ; i<iEnd ; i++ )
                                        {

                                            while(aaData[aiDisplay[i]][1].charAt(0) === '$')
                                                aaData[aiDisplay[i]][1] = aaData[aiDisplay[i]][1].substr(1);

                                            ePage += parseInt(aaData[ aiDisplay[i] ][1]);
                                        }

                                        var nCells = nRow.getElementsByTagName('th');

                                        nCells[1].innerHTML = '$' + parseInt(ePage) +' [ Total: $'+ parseInt(eTotal) +' ]';
                                    }*/
                                });

                                eTableCounter = eTableCounter + 1;

                                /* CAPTURA DEL EVENTO CLICK DERECHO */
                                $(eInnerTable).on('mousedown','tbody tr', function(e){

                                    if( e.button === 2 ) {
                                        /* Toma el id del mov de caja del DOM en la tabla */
                                        var id = e.target.lastChild.value;

                                        generalContextMenu(menu.contextual, e);

                                        /* Pasa el id del movimiento de caja al attr ID del <a> del menu contextual */
                                        $('a#' + menu.contextual.accion).attr('id', id);

                                        return false;

                                    }

                                });

                                //Fix - Devuelve el focus al input de los datatables internos al tipear
                                var eInput = $(eInnerTable.parent().find('input[type="search"]'));
                                $(document).delegate(eInput, 'keyup', function () {
                                    $(eInput).focus();
                                });

                            }

                        });

                    }

                });

            }

        });

    }

    /* TOMA CLICK DEL MENU DESPLEGABLE */
    $('body').on('click', '#menu a', function() {
        var accion = $(this).attr('accion');
        cod_movimiento_caja = $(this).attr('id');

        $('#menu').remove();

        switch (accion) {
            case 'editar_movimiento_caja_subrubro':
                if(cod_movimiento_caja != ''){
                    $.ajax({
                        url: BASE_URL + 'caja/getSubRubros',
                        data: {
                            rubro: 'EGRESOS'
                        },
                        type: 'POST',
                        dataType: 'JSON',
                        cache: false,
                        success: function (_json) {

                            if(!modal){
                                $.each(_json, function (i, json) {
                                    $('#mySelect').append($('<option>', {
                                        value: json.codigo,
                                        text : json.nombre
                                    }));
                                });
                                /* console.log(cod_movimiento_caja); */
                                $('#cod_mov').text(langvr.cod_mov + ": " + cod_movimiento_caja /*+ ". " + rubro*/);

                                modal = $('#responsive').modal();
                                modal.modal('show');

                                $("html, body").animate({ scrollTop: 0 }, "slow");

                            } else {
                                $('#cod_mov').text("Cod. Mov: " + cod_movimiento_caja /*+ ". " + rubro*/);

                                modal = $('#responsive').modal();
                                modal.modal('show');

                                $("html, body").animate({ scrollTop: 0 }, "slow");

                            }
                        }
                    });
                } else {
                    gritter(langvr.id_mov_caja_vacio, false, langvr.error);
                }
                break;

            default:
                $.gritter.add({
                    title: 'Upps',
                    text: langvr.no_tiene_permiso + '!',
                    sticky: false,
                    time: '3000',
                    class_name: 'gritter-error'
                });
                break;
        }

        return false;

    });

    /* Guarda con el nuevo subrubro */
    $('#aceptarCambioSub').on('click', function () {
        console.log('Guardando...');
        $.ajax({
            url: BASE_URL + 'caja/updateSubRubros',
            type: 'POST',
            dataType: 'json',
            data: {
                cod_mov_ca: cod_movimiento_caja,
                nuevo_sub: $( "#mySelect option:selected" ).val()
            },
            success: function (_json) {
                if(_json){

                    /* Obtengo las fechas del dateRangePicker actuales para actualizar la vista luego del cambio */
                    fechas = dateRange[0].value;
                    start = fechas.substring(0,10);
                    end = fechas.substring(fechas.length - 10, fechas.length);

                    start = formatearFecha(start);
                    end = formatearFecha(end);

                    /* Oculta modal y actualiza las tablas [ Lo ideal seria que solo actualice la de Egresos
                    pero no hay tiempo para separar getData() en dos métodos ] xD */
                    $('#responsive').modal('hide');

                    getData(start, end);

                }
            }
        });
    });

    /* Full Screen para mejor visualizacion xD
    * Queda desactivado en la vista hasta que resuelva el conflicto con el boton de imprimir estando
    * o luego de haber estado en fullscreen*/
    $('#fullscreen').on('click', function() {

        var posicion =  $('.page-content').position();

        if(posicion.left != 0){

            $('.page-content').css({
                position: 'fixed',
                top: 45,
                right: 0,
                bottom: 0,
                left: 0,
                'z-index': 1,
                overflow: 'auto',
                opacity: 1,
                width: '100%'
            });

            $(this).removeClass('btn-white').addClass('btn-xs');
            $(this).find('i').removeClass('icon-fullscreen').addClass('icon-resize-small');
            $('#print_btn').attr('disabled', true);

        } else {

            $('.page-content').css({
                position: 'relative',
                top: posicion.top - 45,
                right: posicion.right,
                bottom: posicion.bottom,
                left: posicion.left,
                'z-index': 0
            });

            $(this).addClass('btn-white').removeClass('btn-xs');
            $(this).find('i').removeClass('icon-resize-small').addClass('icon-fullscreen');
            $('#print_btn').attr('disabled', false);

        }
    });

    /* Mueve el modal fuera del .page-content cuando carga la vista para que no quede atras del fondo en fullscreen */
    $('#responsive').appendTo("body");

    /* Algun capo agrego el jQuery UI a la estructura del template y sobreescribe muchos elementos
    que traía por default el template por lo que para poder modificarlos hay que reiniciarlizarlos en cada vista */
    $('[data-rel=tooltip]').tooltip({
        position: {
            my: "left+20 center"
        }
    });

    /**
     * BOTON IMPRIMIR
     */
    /* PARA FIREFOX*/
    $('html').attr('moznomarginboxes', '');
    $('html').attr('mozdisallowselectionprint','');

    $('#print_btn').on('click', function() {
        window.print();
    });

});