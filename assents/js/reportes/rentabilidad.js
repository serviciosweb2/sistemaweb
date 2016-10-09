var myChart = null;
var gastos = null;
var ingresos = null;
var myFormatters;
var table = null;
var tableIngreso = null;
var myChartFechas;
var interval;
var cod_movimiento_caja = 0;
var rubro;
var gastoEdit;
var fecha_des;
var fecha_has;
var modal = null;
var tablaRent = null;
var tablaRentFechas = null;


$(document).ready(function() {


    switch (idioma){
        case 'sp':
            myFormatters = d3.locale({
            "decimal": ".",
            "thousands": ",",
            "grouping": [3],
            "currency": ["$", ""],
            "dateTime": "%a %b %e %X %Y",
            "date": "%m/%d/%Y",
            "time": "%H:%M:%S",
            "periods": ["AM", "PM"],
            "days": ["Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sábado"],
            "shortDays": ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
            "months": ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
            "shortMonths": ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"]
            });
            $('#reportrange').daterangepicker({
                locale: { cancelLabel: 'Cancelar',
                          applyLabel: 'Aceptar',
                          customRangeLabel: 'Rango perzonalizado'
                            },

                ranges: {
                    'Hoy': [moment(), moment()],
                    'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Ultima semana': [moment().subtract(6, 'days'), moment()],
                    'Ultimos 30 días': [moment().subtract(29, 'days'), moment()],
                    'Este mes': [moment().startOf('month'), moment().endOf('month')],
                    'Ultimo mes': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb);
            d3.time.format = myFormatters.timeFormat;
            break;

        case 'pt-br':
            myFormatters = d3.locale({
                "decimal": ",",
                "thousands": ".",
                "grouping": [3],
                "currency": ["R$", ""],
                "dateTime": "%d/%m/%Y %H:%M:%S",
                "date": "%d/%m/%Y",
                "time": "%H:%M:%S",
                "periods": ["AM", "PM"],
                "days": ["Domingo", "Segunda", "Terça", "Quarta", "Quinta", "Sexta", "Sábado"],
                "shortDays": ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sáb"],
                "months": ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"],
                "shortMonths": ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez"]
            });
            $('#reportrange').daterangepicker({
                locale: { cancelLabel: 'Cancelar',
                    applyLabel: 'Aceitar',
                    customRangeLabel: 'Intervalo perzonalizado'
                },

                ranges: {
                    'Hoje': [moment(), moment()],
                    'Ontem': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Semana passada': [moment().subtract(6, 'days'), moment()],
                    'Últimos 30 dias': [moment().subtract(29, 'days'), moment()],
                    'Este mês': [moment().startOf('month'), moment().endOf('month')],
                    'Último mês': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb);
            d3.time.format = myFormatters.timeFormat;
            break;
    }









    /** Llamadas **/
    
    $('#reportrange').on('apply.daterangepicker', function (ev, picker) {
        if(myChart) {
            myChart.setType('bar');
            clearInterval(interval);

        }
        getReporteGastosEingresos(picker.startDate.format('YYYY-MM-DD'), picker.endDate.format('YYYY-MM-DD'));

        $('#etiquetaGasto').hide();
        $('#etiquetaIngreso').hide();
        $('#dataTableIngreso').hide();
        $('#dataTableGasto').hide();
        $('#botonera').hide();
        $('#botoneraAtras').show();

    });
    
    getReporteRentabilidad();

    $('#back').click(function () {
        getReporteRentabilidad();
        $('#botoneraAtras').hide();
        $('#etiquetaGasto').hide();
        $('#etiquetaIngreso').hide();
        $('#dataTableIngreso').hide();
        $('#dataTableGasto').hide();
        $('#tableRentabilidadFechas').hide();
        $('#tableDivFechas').hide();

    });

        

    

    function cb(start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }
    
    function getReporteGastosEingresos(start, end) {

        $.ajax({
            type: "POST",
            url: BASE_URL + 'reportes/getReporteRentabiliadGastosEingresos',
            data: {
                fecha_desde: start,
                fecha_hasta: end
            },
            dataType: "json",

            success: function (_json) {
                var tt = document.createElement('div'),
                    leftOffset = -(~~$('html').css('padding-left') + ~~$('body').css('margin-left')),
                    topOffset = -32;
                tt.className = 'ex-tooltip';
                document.body.appendChild(tt);
                var opts = {
                    "dataFormatX": function (x) {
                        return start + ' - ' + end;
                    },
                    "tickFormatX": function (x) {
                        return x;
                    },
                    "mouseover": function (d, i) {
                        var pos = $(this).offset();
                        $(tt).text(d.z + ": $" + (d.y).formatMoney(2, ',', '.'))
                            .css({top: topOffset + pos.top, left: pos.left + leftOffset})
                            .show();
                    },
                    "mouseout": function (data, i) {
                       
                       
                        $(tt).hide();
                    },
                    "click": function (data, i) {
                        console.log(data);
                        if(data.z != 'Rentabilidad') {
                            if (data.z == 'EGRESOS') {
                                getReporteRentabiliadGastos(null, start, end);


                            } else {
                                getReporteRentabiliadIngresos(null, start, end);
                            }
                        }
                    }
                };
                $('#tableRentabilidad').hide();
                $('#tableDivInicial').hide();

                myChartFechas = new xChart('bar', _json.principal, '#myChart', opts);
                
                var arr = $.map(_json.tableR, function(el) { return el });
                if(tablaRentFechas) {

                    tablaRentFechas.clear();
                    tablaRentFechas.rows.add(arr);
                    tablaRentFechas.draw();
                }else {
                    tablaRentFechas = $('#tableRentabilidadFechas').DataTable({
                        "data": arr,
                        "bSort": false
                    });
                }
                $('#tableRentabilidadFechas').show();
                $('#tableDivFechas').show();
            }
        });
    }
    

    function getReporteRentabilidad() {
        $.ajax({
            type: "POST",
            url: BASE_URL + 'reportes/getReporteRentabilidad',
            data: {periodo: null},
            dataType: 'json',

            success: function (_json) {
                $('#botonera').show();
                var tt = document.createElement('div'),
                    leftOffset = -(~~$('html').css('padding-left') + ~~$('body').css('margin-left')),
                    topOffset = -32;
                tt.className = 'ex-tooltip';
                document.body.appendChild(tt);
                var opts = {
                    "dataFormatX": function (x) {
                        return d3.time.format('%m').parse(x);
                    },
                    "tickFormatX": function (x) {
                        return d3.time.format('%b')(x);
                    },
                    "mouseover": function (d, i) {
                        var pos = $(this).offset();
                        $(tt).text(d.z + ": $" + (d.y).formatMoney(2, ',', '.'))
                            .css({top: topOffset + pos.top, left: pos.left + leftOffset})
                            .show();
                    },
                    "mouseout": function (data, i) {
                        $(tt).hide();
                    },
                    "click": function (data, i) {
                        if(data.gasOing != 'r') {
                            if (data.gasOing == 'g') {
                                getReporteRentabiliadGastos(d3.time.format('%m')(data.x), null, null);


                            } else {
                                getReporteRentabiliadIngresos(d3.time.format('%m')(data.x), null, null);
                            }
                        }
                    }

                };
                myChart = new xChart('bar', _json.graph, '#myChart', opts);
                $('#barras').click(function () {
                        myChart.setType('bar');
                        clearInterval(interval);
                    
                });
                $('#lineas').click(function () {
                        myChart.setType('line-dotted');
                        clearInterval(interval);
                });
                $('#acum').click(function () {
                        myChart.setType('cumulative');
                        clearInterval(interval);
                });
                typeChart = 'bar';
                if(!tablaRent){
                var arr = $.map(_json.tableRen, function(el) { return el });
                tablaRent = $('#tableRentabilidad').DataTable({
                    "data": arr,
                    "bSort": false
                });
                }

                $('#tableRentabilidad').show();
                $('#tableDivInicial').show();
            }


            });

        }





        function getReporteRentabiliadGastos(mes, fecha_desde, fecha_hasta) {
            $.ajax({
                type: "POST",
                url: BASE_URL + 'reportes/getReporteRentabilidadGastos',
                data: {
                    fecha_desde: fecha_desde,
                    fecha_hasta: fecha_hasta,
                    periodo: mes
                },
                dataType: 'json',
                success: function (_json) {
                    console.log(_json);
                    var tt = document.createElement('div'),
                        leftOffset = -(~~$('html').css('padding-left') + ~~$('body').css('margin-left')),
                        topOffset = -32;
                    tt.className = 'ex-tooltip';
                    document.body.appendChild(tt);

                    var opts = {
                        "mouseover": function (d, i) {
                            var pos = $(this).offset();
                            $(tt).text(d.x + ": $" + (d.y).formatMoney(2, ',', '.'))
                                .css({top: topOffset + pos.top, left: pos.left + leftOffset})
                                .show();
                        },
                        "mouseout": function (data, i) {
                            $(tt).hide();
                        },
                        "click": function (data, i) {
                            $('#etiquetaIngreso').hide();
                            console.log(data);
                          getTablasGastos(data.z, data.d, data.h, data.s);
                        }

                    };
                    $('#etiquetaGasto').show();
                    if(mes) {
                        $('#fechaGasto').text(d3.time.format('%b')(d3.time.format('%m').parse(mes)));
                    } else {
                        $('#fechaGasto').text(fecha_desde + ' - ' + fecha_hasta);
                    }
                    $('#etiquetaGasto').ScrollTo();
                    if(gastos){
                        gastos.setType('bar');
                    }
                    gastos = new xChart('bar', _json, '#gastosChart', opts);
                    $('#barrasGas').click(function () {
                        gastos.setType('bar');
                        clearInterval(interval);

                    });

                    $('#lineasGas').click(function () {
                        gastos.setType('line-dotted');
                        clearInterval(interval);
                    });


                    opts = null;

                }
            });

        }

        function getReporteRentabiliadIngresos(mes, fecha_desde, fecha_hasta)
        {
            $.ajax({
                type: "POST",
                url: BASE_URL + 'reportes/getReporteRentabiliadIngresos',
                data: {fecha_desde: fecha_desde,
                    fecha_hasta: fecha_hasta ,
                    periodo: mes
                },
                dataType: 'json',
                success: function(_json) {
                    var tt = document.createElement('div'),
                        leftOffset = -(~~$('html').css('padding-left') + ~~$('body').css('margin-left')),
                        topOffset = -32;
                    tt.className = 'ex-tooltip';
                    document.body.appendChild(tt);

                    var opts = {
                        "mouseover": function (d, i) {
                            var pos = $(this).offset();
                            $(tt).text(d.x +": $" + (d.y).formatMoney(2,',','.'))
                                .css({top: topOffset + pos.top, left: pos.left + leftOffset})
                                .show();
                        },
                        "mouseout": function (data , i) {
                            $(tt).hide();
                        },
                        "click": function (data, i) {
                            $('#etiquetaGasto').hide();

                            getTablasIngresos(data.z, data.d, data.h);
                        }

                    };
                    $('#etiquetaIngreso').show();
                    if(mes) {
                        $('#fechaIngreso').text(d3.time.format('%b')(d3.time.format('%m').parse(mes)));
                    } else {
                        $('#fechaIngreso').text(fecha_desde + ' - ' + fecha_hasta);
                    }
                    $('#etiquetaIngreso').ScrollTo();
                    if(ingresos){
                        ingresos.setType('bar');
                    }
                    ingresos = new xChart('bar', _json, '#ingresosChart', opts);

                    $('#barrasIng').click(function () {
                        ingresos.setType('bar');
                        clearInterval(interval);

                    });

                    $('#lineasIng').click(function () {
                        ingresos.setType('line-dotted');
                        clearInterval(interval);
                    });

                }
            });

        }
    
    function getTablasGastos(gasto, fecha_desde, fecha_hasta,cod_sub) {
        $.ajax({
            type: "POST",
            url: BASE_URL + 'reportes/getReporteGasto',
            data: {
                gasto: gasto,
                cod_sub: cod_sub,
                fecha_desde: fecha_desde,
                fecha_hasta: fecha_hasta
            },
            dataType: 'json',
            success: function (_json) {
                $('#dataTableIngreso').hide();
                var arr = $.map(_json.datos, function(el) { return el });
                if(table) {

                    table.clear();
                    table.rows.add(arr);
                    table.draw();
                }else{
                table = $('#tableGasto').DataTable( {
                    "data" : arr,
                    "fnRowCallback": function(nRow, aData) {
                        var codigo = aData[0];
                        var _html = '<input type="hidden" name="hd_cod_caja" value="' + codigo + '">';
                        rubro = aData[3];
                         fecha_des = fecha_desde;
                         fecha_has = fecha_hasta;
                         gastoEdit = gasto;
                        $('td:eq(0)', nRow).html(_html + aData[0]);

                        $(nRow).on('mousedown', function(e){
                            if( e.button == 2 ) {
                                generalContextMenu(_json.menu.contextual, e);
                                cod_movimiento_caja = $(this).find("[name=hd_cod_caja]").val();
                                return false;
                            }
                            return true;
                        });
                }});

                }

                $('#dataTableGasto').show();
                $('#dataTableGasto').ScrollTo();

            }
        });
    }
    
    function getTablasIngresos(ingreso, fecha_desde, fecha_hasta) {
        $.ajax({
            type: "POST",
            url: BASE_URL + 'reportes/getReporteIngreso',
            data: {
                ingreso: ingreso,
                fecha_desde: fecha_desde,
                fecha_hasta: fecha_hasta
            },
            dataType: 'json',
            success: function (_json) {
                $('#dataTableGasto').hide();
                var arr = $.map(_json, function(el) { return el });
                if(tableIngreso) {

                    tableIngreso.clear();
                    tableIngreso.rows.add(arr);
                    tableIngreso.draw();
                }else{
                    tableIngreso = $('#tableIngreso').DataTable( {
                        "data" : arr
                    });
                }
                $('#dataTableIngreso').show();
                $('#dataTableIngreso').ScrollTo();

            }
        });
    }


        Number.prototype.formatMoney = function(c, d, t){
            var n = this,
                c = isNaN(c = Math.abs(c)) ? 2 : c,
                d = d == undefined ? "." : d,
                t = t == undefined ? "," : t,
                s = n < 0 ? "-" : "",
                i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
                j = (j = i.length) > 3 ? j % 3 : 0;
            return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
        };

   
    $('body').on('click', '#menu a', function(){
        var accion = $(this).attr('accion');
        $('#menu').remove();
        switch (accion) {
            case "editar_movimiento_caja_subrubro":
                $.ajax({
                    url: BASE_URL + 'caja/getSubRubros',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        rubro: 'EGRESOS'
                    },
                    success: function (_json) {
                        console.log(_json);
                        if(!modal){
                        $.each(_json, function (i, json) {
                            $('#mySelect').append($('<option>', {
                                value: json.codigo,
                                text : json.nombre
                            }));
                        });
                        $('#cod_mov').text("Cod. Mov: " + cod_movimiento_caja + ". " + rubro);
                        modal = $('#responsive').modal();
                        modal.modal('show');
                    }else {
                            $('#cod_mov').text("Cod. Mov: " + cod_movimiento_caja + ". " + rubro);
                            modal = $('#responsive').modal();
                            modal.modal('show');
                        }
                    }
                });
                break;
        }
    });

    $('#aceptarCambioSub').click(function () {
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
                    $('#responsive').modal('hide');
                    getTablasGastos(rubro,fecha_des, fecha_has);
                }

            }
        })
    })

});


