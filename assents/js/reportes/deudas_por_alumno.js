var aoColumnDefs = '';
var oTable = '';
var todos = '';
var todas = '';

a_idioma = idioma;
aoColumnDefs = columns;
todos = langTodos;
todas = langTodas;
asubtotal = subtotal;
atotal = total;
asimboloPesos = simboloPesos;
atotal_general = total_general;
atotal_de_esta_pagina = total_de_esta_pagina;
total_total = 0;

function init() {
    oTable = $('#reporteDeudasPorAlumno').DataTable({
        "bServerSide": true,
        "lengthMenu": [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, todos]],
        "ajax": {
                    url:BASE_URL + 'reportes/listarDeudasPorAlumnos', 
                    data: function(params)
                          {
                            params.cant_cuotas = $("#cant_cuotas").val();
                            params.ultimo_pago_select = $("#ultimo_pago_select").val();
                            params.fecha_pago_desde = $("#fecha_pago_desde").val();
                            params.fecha_pago_hasta = $("#fecha_pago_hasta").val();
                            params.saldo_acumulado = $("#saldo_acumulado").val();
                            params.desd = $("#desd").val();
                            params.hast = $("#hast").val();
                            params.cursos = $("#cursos").val();
                            params.periodo = $("#periodo").val();
                            params.anio = $("#anio").val();
                            params.comision = $("#comision").val();
                            params.turno = $("#turno").val();
                            params.tipo_deuda = $("#tipo_deuda").val();
                          }
                },
        "aaSorting": [[0, "desc"]],
        "sServerMethod": "POST",
        'aoColumnDefs': aoColumnDefs,
        "drawCallback": function ( settings ) {
            
            var api = this.api();
            var rows = api.rows( {page:'current'} ).nodes();
            var last=null;
            var total = api.column(9, {page:'current'} ).data().length;
            var primero = true;
            var e = 0;
            var subTotal = 0;
            var etotal = 0;
            
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
            
            var etotal = api.column( 5 ).data().reduce( function (a, b) {return intVal(a) + intVal(b);},0);
            
            api.column(9, {page:'current'} ).data().each( function ( group, i ) 
            {
                subTotal += intVal(api.column(5, {page:'current'} ).data()[i-1]);
                
                if(last !== group) 
                {
                    if(!primero)
                    {
                        $(rows).eq( i ).before('<tr><td colspan="9" style="font-weight: bold;">'+asubtotal+': '+asimboloPesos+''+(Math.round(subTotal * 100) / 100)+'</td></tr>');
                        subTotal = 0;
                    }
                                        
                    $(rows).eq( i ).before('<tr class="group"><td colspan="9" style="font-weight: bold;">'+group+'</td></tr>');
                    
                    last = group;
                    primero = false;
                }
                if((i + 1) === total)
                {   subTotal += intVal(api.column(5, {page:'current'} ).data()[i]);
                    $(rows).eq( i ).after('<tr><td colspan="7" style="font-weight: bold;">'+atotal_de_esta_pagina+': '+asimboloPesos+''+etotal.toFixed(2)+'</td><td id="totalGeneral" colspan="2" style="font-weight: bold;">'+atotal_general+': '+asimboloPesos+total_total+'</td></tr>');
                    $(rows).eq( i ).after('<tr><td colspan="9" style="font-weight: bold;">'+asubtotal+': '+asimboloPesos+''+subTotal.toFixed(2)+'</td></tr>');
                    subTotal = 0;
                    
                }
            } );
            
        },
        "initComplete": function(settings, json) 
        {
            var select = $('#cursos');
            var cursos = json['cursos'];
            //console.log(cursos);
            option = document.createElement( 'option' );
            option.value = 0;
            option.textContent = todos;
            select.append( option );
            
            cursos.forEach(function( item ) 
            {
                option = document.createElement( 'option' );
                option.value = option.textContent = item['nombre_'+a_idioma];
                select.append( option );
                $('#cursos').trigger('chosen:updated');
            });
            
            var select = $('#comision');
            var comisiones = json['comisiones'];
            //console.log(comisiones);
            option = document.createElement( 'option' );
            option.value = 0;
            option.textContent = todas;
            select.append( option );
            
            comisiones.forEach(function( item ) 
            {
                option = document.createElement( 'option' );
                option.value = option.textContent = item['nombre'];
                select.append( option );
                $('#comision').trigger('chosen:updated');
            });
            
            $("#totalGeneral").html(atotal_general+': '+asimboloPesos + json['totalAcumulado'].toFixed(2));
            total_total = json['totalAcumulado'];
        }
    });
    
        // Order by the grouping
        $('#reporteDeudasPorAlumno tbody').on( 'click', 'tr.group', function () {
        
        var currentOrder = oTable.order()[0];
        
        if ( currentOrder[0] === 9 && currentOrder[1] === 'asc' ) 
        {
            oTable.order( [ 9, 'desc' ] ).draw();
        }
        else 
        {
            oTable.order( [ 9, 'asc' ] ).draw();
        }
    } );
    
    
    $("#reporteDeudasPorAlumno_filter").find("label").addClass("input-icon input-icon-right");
    $("#reporteDeudasPorAlumno_filter").find("label").append('<i name="icon_filters" class="icon-caret-down grey" name="table_filters" style="margin-right: 7px; cursor: pointer"></i>');
    $("#reporteDeudasPorAlumno_filter").append($("[name=container_menu_filters_deudas_por_alumno]").html());
    $("#reporteDeudasPorAlumno_filter").find(".select_chosen").chosen();
    
    $("#reporteDeudasPorAlumno_filter").find(".chosen-container").css({"width": "302px"});
    
    $("#fecha_pago_desde").datepicker();
    $("#fecha_pago_hasta").datepicker();
        
    $("#reporteDeudasPorAlumno_filter").append('<i id="exportar_informe" class="icon-print grey" onclick="exportar_informe(\'pdf\');" style="cursor: pointer; margin-left: 6px;" data-original-title="" title="exportar pdf"></i>');
    $("#reporteDeudasPorAlumno_filter").append('<i id="exportar_informe" class="icon-external-link" onclick="exportar_informe(\'csv\');" style="cursor: pointer; margin-left: 6px;" data-original-title="" title="exportar csv"></i>');
    
    $("[name=container_menu_filters_deudas_por_alumno]").remove();
    
    $("[name=icon_filters]").on("click", function(){
    $("[name=contenedorPrincipal]").toggle();
    $("[name=div_table_filters_deudas_por_alumno]").toggle(300);
        return false;
        });
    
    $("#reporteDeudasPorAlumno_filter").find(".select_chosen").chosen();
    //$("[name=div_table_filters_deudas_por_alumno]").hide();
    $("[name=contenedorPrincipal]").on("mousedown", function() {
        $("[name=contenedorPrincipal]").hide();
        $("[name=div_table_filters_deudas_por_alumno]").hide(300);
        
    });
    
    
    $("#saldo_acumulado").on("change", function()
    {
        switch ($("#saldo_acumulado").val())
        {
            case "-1":
                $('#hast').hide().val('');
                $('#desd').hide().val('');
                $('#span_y').hide();
                break;
            case "1":
                $('#hast').show();
                $('#desd').hide().val('');
                $('#span_y').hide();
                break;
            case "2":
                $('#hast').show();
                $('#desd').hide().val('');
                $('#span_y').hide().val('');
                break;
            case "3":
                $('#hast').show();
                $('#desd').show();
                $('#span_y').show();
                break;
            case "4":
                $('#hast').show();
                $('#desd').hide().val('');
                $('#span_y').hide().val('');
                break;
            default:
                $('#hast').hide().val('');
                $('#desd').hide().val('');
                $('#span_y').hide();
                break;
        }
    });
    
    $("#ultimo_pago_select").on("change", function()
    {
        switch ($("#ultimo_pago_select").val())
        {
            case "-1":
                $('#fecha_pago_hasta').hide().val('');
                $('#fecha_pago_desde').hide().val('');
                $('#span_y_').hide();
                break;
            case "1":
                $('#fecha_pago_hasta').hide().val('');
                $('#fecha_pago_desde').show();
                $('#span_y_').hide();
                break;
            case "2":
                $('#fecha_pago_hasta').hide().val('');
                $('#fecha_pago_desde').show();
                $('#span_y_').hide();
                break;
            case "3":
                $('#fecha_pago_hasta').show();
                $('#fecha_pago_desde').show();
                $('#span_y_').show();
                break;
            case "4":
                $('#fecha_pago_hasta').hide().val('');
                $('#fecha_pago_desde').show();
                $('#span_y_').hide();
                break;
            default:
                $('#fecha_pago_hasta').hide().val('');
                $('#fecha_pago_desde').hide().val('');
                $('#span_y_').hide();
                break;
        }
    });
    
    $("#periodo").on("change", function()
    {
        switch ($("#ultimo_pago_select").val())
        {
            case "0":
                $('#anio').hide().val(0);
                break;
            default:
                $('#anio').show();
                break;
        }
    });
}

$(document).ready(function(){
    init();
});

function filtrar(){
      oTable.ajax.reload();
}
function limpiar(){
    $("#cant_cuotas").val(0);
    $('#cant_cuotas').trigger('chosen:updated');
    $("#ultimo_pago_select").val(-1);
    $("#fecha_pago_desde").val('');
    $("#fecha_pago_hasta").val('');
    $("#saldo_acumulado").val(-1);
    $("#desd").val('');
    $("#hast").val('');
    $("#cursos").val(0);
    $('#cursos').trigger('chosen:updated');
    $("#periodo").val(0);
    $("#comision").val(0);
    $('#comision').trigger('chosen:updated');
    $("#turno").val(0);
    $('#turno').trigger('chosen:updated');
    $("#tipo_deuda").val(-1);
    $('#tipo_deuda').trigger('chosen:updated');
    oTable.ajax.reload();
}

function exportar_informe(tipo_reporte)
{
    var info = oTable.page.info();
    var search = {value:$("#reporteDeudasPorAlumno_filter").find("label").find('input').val()};
    var aorder = oTable.order();
    
    
    
    
    var cant_cuotas = $("#cant_cuotas").val();
    var ultimo_pago_select = $("#ultimo_pago_select").val();
    var fecha_pago_desde = $("#fecha_pago_desde").val();
    var fecha_pago_hasta = $("#fecha_pago_hasta").val();
    var saldo_acumulado = $("#saldo_acumulado").val();
    var desd = $("#desd").val();
    var hast = $("#hast").val();
    var cursos = $("#cursos").val();
    var periodo = $("#periodo").val();
    var anio = $("#anio").val();
    var comision = $("#comision").val();
    var turno = $("#turno").val();
    var tipo_deuda = $("#tipo_deuda").val();
    var tipo_reporte = tipo_reporte;
    
    
    $("[name=frm_exportar]").find("[name=length]").val(info.length);
    $("[name=frm_exportar]").find("[name=start]").val(info.start);
    $("[name=frm_exportar]").find("[name=iSortCol_0]").val(aorder[0][0]);
    $("[name=frm_exportar]").find("[name=sSortDir_0]").val(aorder[0][1]);
    $("[name=frm_exportar]").find("[name=cant_cuotas]").val(cant_cuotas);
    $("[name=frm_exportar]").find("[name=ultimo_pago_select]").val(ultimo_pago_select);
    $("[name=frm_exportar]").find("[name=fecha_pago_desde]").val(fecha_pago_desde);
    $("[name=frm_exportar]").find("[name=fecha_pago_hasta]").val(fecha_pago_hasta);
    $("[name=frm_exportar]").find("[name=saldo_acumulado]").val(saldo_acumulado);
    $("[name=frm_exportar]").find("[name=desd]").val(desd);
    $("[name=frm_exportar]").find("[name=hast]").val(hast);
    $("[name=frm_exportar]").find("[name=cursos]").val(cursos);
    $("[name=frm_exportar]").find("[name=periodo]").val(periodo);
    $("[name=frm_exportar]").find("[name=anio]").val(anio);
    $("[name=frm_exportar]").find("[name=comision]").val(comision);
    $("[name=frm_exportar]").find("[name=turno]").val(turno);
    $("[name=frm_exportar]").find("[name=tipo_deuda]").val(tipo_deuda);
    $("[name=frm_exportar]").find("[name=tipo_reporte]").val(tipo_reporte);
    $("[name=frm_exportar]").find("[name=total]").val(total_total);
    $("[name=frm_exportar]").submit();
}