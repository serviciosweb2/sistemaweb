var aoColumnDefs = columns;
var oTable = '';
var fecha_desde = fechaDesde;
var fecha_hasta = fechaHasta;
var cod_curso = codCurso;
var codPlanAcademico = cod_plan_academico;
var cod = codigo;
var tit = titulo;
var tipo_periodo = cod_tipo_periodo;
var desdeInscripcionesY_bajas = desdeInscripcionesYbajas;


function init() {
    
    
    
    oTable = $('#reporteBajas').DataTable({
        "bServerSide": true,
        "lengthMenu": [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, lang.todos]],
        "ajax": {
                    url:BASE_URL + 'reportes/listarReporteBajas', 
                    data: function(params)
                          {
                            params.fechaDesde = $('#fechaDesde').val();
                            params.fechaHasta = $('#fechaHasta').val();
                            params.codCurso = $('#cursos').val();
                            params.cod_plan_academico = $('#cod_plan_academico').val();
                            params.cod_mat_periodo = $('#cod_mat_periodo').val();
                            params.clausulaFechas = $('#fecha_emision').val();
                            params.cod_alumno = $('#cod_alumno').val();
                            params.cod_tipo_periodo = $('#cod_tipo_periodo').val();
                          }
                },
        "aaSorting": [[0, "desc"]],
        "sServerMethod": "POST",
        'aoColumnDefs': aoColumnDefs,
        "initComplete": function(settings, json) 
        {
            var select = $('#cursos');
            var cursos = json['cursos'];
            option = document.createElement( 'option' );
            option.value = 0;
            option.textContent = lang.todos;
            select.append( option );
            
            cursos.forEach(function( item ) 
            {
                option = document.createElement( 'option' );
                option.value = item['codigo'];
                option.textContent = item['nombre_'+lang._idioma];
                select.append( option );
                $('#cursos').trigger('chosen:updated');
                
                if(desdeInscripcionesY_bajas == 1)
                {
                    $('#cursos').val(cod_curso);
                    $('#cursos').trigger('chosen:updated');
                }
            });
        }    
    });
    
       
    $("#reporteBajas_filter").find("label").addClass("input-icon input-icon-right");
    $("#reporteBajas_filter").find("label").append('<i name="icon_filters" class="icon-caret-down grey" name="table_filters" style="margin-right: 7px; cursor: pointer"></i>');
    $("#reporteBajas_filter").append($("[name=container_menu_filters_bajas]").html());
    $("#reporteBajas_filter").find(".select_chosen").chosen();
    
    $("#reporteBajas_filter").find(".chosen-container").css({"width": "302px"});
    
    $("#fecha_pago_desde").datepicker();
    $("#fecha_pago_hasta").datepicker();
        
    $("#reporteBajas_filter").append('<i id="exportar_informe" class="icon-print grey" onclick="exportar_informe(\'pdf\');" style="cursor: pointer; margin-left: 6px;" data-original-title="" title="exportar pdf"></i>');
    $("#reporteBajas_filter").append('<i id="exportar_informe" class="icon-external-link" onclick="exportar_informe(\'csv\');" style="cursor: pointer; margin-left: 6px;" data-original-title="" title="exportar csv"></i>');
    
    $("[name=container_menu_filters_deudas_por_alumno]").remove();
    
    $("[name=icon_filters]").on("click", function(){
    $("[name=contenedorPrincipal]").toggle();
    $("[name=div_table_filters_bajas]").toggle(300);
        return false;
        });
    
    $("#reporteBajas_filter").find(".select_chosen").chosen();
    //$("[name=div_table_filters_deudas_por_alumno]").hide();
    $("[name=contenedorPrincipal]").on("mousedown", function() {
        $("[name=contenedorPrincipal]").hide();
        $("[name=div_table_filters_bajas]").hide(300);
        
    });
    
    $("#fechaDesde").datepicker();
    $("#fechaHasta").datepicker();
    
    $("#fecha_emision").on("change", function()
    {
        switch ($("#fecha_emision").val())
        {
            case "-1":
                $('#fechaHasta').hide().val('');
                $('#fechaDesde').hide().val('');
                $('#span_y_').hide();
                break;
            case "1":
                $('#fechaHasta').hide().val('');
                $('#fechaDesde').show();
                $('#span_y_').hide();
                break;
            case "2":
                $('#fechaHasta').hide().val('');
                $('#fechaDesde').show();
                $('#span_y_').hide();
                break;
            case "3":
                $('#fechaHasta').show();
                $('#fechaDesde').show();
                $('#span_y_').show();
                break;
            case "4":
                $('#fechaHasta').hide().val('');
                $('#fechaDesde').show();
                $('#span_y_').hide();
                break;
            default:
                $('#fechaHasta').hide().val('');
                $('#fechaDesde').hide().val('');
                $('#span_y_').hide();
                break;
        }
    });
    
}

$(document).ready(function(){
    
    if(desdeInscripcionesY_bajas == 1)
    {
        $('#cod_plan_academico').val(codPlanAcademico);
        $('#cod_tipo_periodo').val(tipo_periodo);
        $('#fechaDesde').val(fecha_desde);
        $('#fechaHasta').val(fecha_hasta);
        $('#cod_tipo_periodo').val(tipo_periodo);
        
        //$('#fecha_emision').val("3");
        newselectedIndex = 3;
        jQuery("#fecha_emision option:selected").removeAttr("selected");
        jQuery("#fecha_emision option[value='"+newselectedIndex +"']").attr('selected', 'selected'); 
        $("#fechaDesde").show();
        $("#fechaHasta").show();
        $("#span_y_").show();
    }
    
    init();
    
});



function filtrar(){
      oTable.ajax.reload();
}
function limpiar(){
    $("#fecha_emision").val(-1);
    $("#fechaDesde").val('');
    $("#fechaHasta").val('');
    $("#cod_alumno").val('');
    $("#cod_mat_periodo").val('');
    $("#cod_plan_academico").val('');
    $("#cod_tipo_periodo").val('');
    $("#cursos").val(0);
    $('#cursos').trigger('chosen:updated');
    
    oTable.ajax.reload();
}

function exportar_informe(tipo_reporte)
{
    var info = oTable.page.info();
    var search = {value:$("#reporteBajas_filter").find("label").find('input').val()};
    var aorder = oTable.order();
    
    var fechaEmision = $("#fecha_emision").val();
    var fecha_desde = $("#fechaDesde").val();
    var fecha_hasta = $("#fechaHasta").val();
    var cod_alumno = $("#cod_alumno").val();
    var cod_curso = $("#cursos").val();
    var codPlanAcademico = $("#cod_plan_academico").val();
    var cod_mat_periodo = $("#cod_mat_periodo").val();
    var cod_tipo_periodo = $('#cod_tipo_periodo').val();
    
    $("[name=frm_exportar]").find("[name=length]").val(info.length);
    $("[name=frm_exportar]").find("[name=start]").val(info.start);
    $("[name=frm_exportar]").find("[name=iSortCol_0]").val(aorder[0][0]);
    $("[name=frm_exportar]").find("[name=sSortDir_0]").val(aorder[0][1]);
    $("[name=frm_exportar]").find("[name=search]").val(search);
    $("[name=frm_exportar]").find("[name=cod_alumno]").val(cod_alumno);
    $("[name=frm_exportar]").find("[name=cod_tipo_periodo]").val(cod_tipo_periodo);
    $("[name=frm_exportar]").find("[name=clausulaFechas]").val(fechaEmision);
    $("[name=frm_exportar]").find("[name=fechaDesde]").val(fecha_desde);
    $("[name=frm_exportar]").find("[name=fechaHasta]").val(fecha_hasta);
    $("[name=frm_exportar]").find("[name=codCurso]").val(cod_curso);
    $("[name=frm_exportar]").find("[name=cod_mat_periodo]").val(cod_mat_periodo);
    $("[name=frm_exportar]").find("[name=cod_plan_academico]").val(codPlanAcademico);
    $("[name=frm_exportar]").find("[name=tipo_reporte]").val(tipo_reporte);

    $("[name=frm_exportar]").submit();
}