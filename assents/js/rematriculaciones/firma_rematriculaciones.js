var aoColumnDefs = '';
aoColumnDefs = columns;
var oTable = '';
var dateRange = '';
var drp = '';
var id_filtro_actual = 1;
var thead=[];
var data='';
var codigo='';
var lang = BASE_LANG;

var filtros = {"documento":"documento","nombre":"nombre","apellido":"apellido","matricula":"matricula","comision":"comision", "ciclo":"ciclo","firmo":"firmo","ano":"ano","trimestre":"trimestre","fecha":"fecha"};

$(document).ready(function(){
    init();
    $("#agregar_filtro_busqueda_usuario").on("click", function(){
        agregarRegistro();
    });

    $("#areaTablas").on("click", "[name=remove_filtro_avanzado]", function(){
        id_filtro_actual --;
        console.log("cantidad de cajas de busqueda"+id_filtro_actual+"  valor a borrar  "+$("[name=filtro_avanzado_usuario].filter_" + $(this).attr("id")).val());
        $("[name=filtro_avanzado_usuario].filter_" + $(this).attr("id")).val('-1');
        $("[name=filtro_avanzado_condicion].filter_" + $(this).attr("id")).val('-1');
        $("#"+$(this).attr("id")+" [name=filtro_avanzado_usuario_valor]").val("");
        $("[name=filtro_avanzado_usuario].filter_" + $(this).attr("id")).css("display","none");
        var id = $(this).attr("id");
        $('.tags span[data_group = "filtro_avanzado"]').each(function(k, valor){
            if(id == $(this).attr("id")){
                $(valor).remove();
            }
        });
    });

    function agregarRegistro(strTipoConsulta, selectedCampo){
        if(id_filtro_actual < 5)
            id_filtro_actual ++;
        console.log("cantidad de cajas de busqueda "+id_filtro_actual);
        var id_filtro = id_filtro_actual-1;
        $('#'+id_filtro).css("display", "-webkit-box");
    }
    $("[name=btnBuscar]").on("click", function(){
        console.log($("#0 [name=filtro_avanzado_usuario_condicion]").val());
        listar();
    });
});

function init(){
    oTable =$('#firmaRematricula-table').dataTable({
        "bServerSide": true,
        "sAjaxSource": BASE_URL + 'rematriculaciones/listar_firmaRematricula',
        "aaSorting": [[0, "desc"]],
        "sServerMethod": "POST",
        "bAutoWidth": false,
        "aoColumns": [
            { "sWidth": "10%" },
            { "sWidth": "15%" },
            { "sWidth": "18%" },
            { "sWidth": "7%" },
            { "sWidth": "15%" },
            { "sWidth": "8%" },
            { "sWidth": "7%" },
            { "sWidth": "5%" },
            { "sWidth": "7%" },
            { "sWidth": "8%" },
        ],
        'aoColumnDefs': aoColumnDefs,
        fnServerData: function(sSource, aData, fnCallback){
            var campo = "";
            var cond_documento = "";
            var cond_nombre = "";
            var cond_apellido = "";
            var cond_matricula = "";
            var cond_comision = "";
            var cond_ciclo = "";
            var cond_firmo = "";
            var cond_ano = "";
            var cond_trimestre = "";
            var cond_fecha = "";
            var documento = "";
            var nombre = "";
            var apellido = "";
            var matricula = "";
            var comision = "";
            var ciclo = "";
            var firmo = "";
            var ano = "";
            var trimestre = "";
            var fecha = "";
            for(i = 0; i < id_filtro_actual; i++){
                campo = $("#"+i+" [name=filtro_avanzado_usuario_campo]").val();
                switch(campo){
                    case "documento":
                        documento = $("#"+i+" [name=filtro_documento]").val();
                        cond_documento = $("#"+i+" [name=filtro_avanzado_usuario_condicion]").val();
                        break;
                    case "nombre":
                        nombre = $("#"+i+" [name=filtro_nombre]").val();
                        cond_nombre = $("#"+i+" [name=filtro_avanzado_usuario_condicion]").val();
                        break;
                    case "apellido":
                        apellido = $("#"+i+" [name=filtro_apellido]").val();
                        cond_apellido = $("#"+i+" [name=filtro_avanzado_usuario_condicion]").val();
                        break;
                    case "matricula":
                        matricula = $("#"+i+" [name=filtro_matricula]").val();
                        cond_matricula = $("#"+i+" [name=filtro_avanzado_usuario_condicion]").val();
                        break;
                    case "comision":
                        comision = $("#"+i+" [name=filtro_comision]").val();
                        cond_comision = $("#"+i+" [name=filtro_avanzado_usuario_condicion]").val();
                        break;
                    case "ciclo":
                        ciclo = $("#"+i+" [name=filtro_ciclo]").val();
                        cond_ciclo = $("#"+i+" [name=filtro_avanzado_usuario_condicion]").val();
                        break;
                    case "firmo":
                        firmo = $("#"+i+" [name=filtro_firmo]").val();
                        cond_firmo = $("#"+i+" [name=filtro_avanzado_usuario_condicion]").val();
                        break;
                    case "ano":
                        ano = $("#"+i+" [name=filtro_ano]").val();
                        cond_ano = $("#"+i+" [name=filtro_avanzado_usuario_condicion]").val();
                        break;
                    case "trimestre":
                        trimestre = $("#"+i+" [name=filtro_trimestre]").val();
                        cond_trimestre = $("#"+i+" [name=filtro_avanzado_usuario_condicion]").val();
                        break;
                    case "fecha":
                        fecha = $("#"+i+" [name=filtro_fecha]").val();
                        cond_fecha = $("#"+i+" [name=filtro_avanzado_usuario_condicion]").val();
                        break;
                }
            }
            aData.push({name: 'condiciones_doc', value: cond_documento});
            aData.push({name: 'condiciones_nom', value: cond_nombre});
            aData.push({name: 'condiciones_ape', value: cond_apellido});
            aData.push({name: 'condiciones_mat', value: cond_matricula});
            aData.push({name: 'condiciones_com', value: cond_comision});
            aData.push({name: 'condiciones_cic', value: cond_ciclo});
            aData.push({name: 'condiciones_fir', value: cond_firmo});
            aData.push({name: 'condiciones_ano', value: cond_ano});
            aData.push({name: 'condiciones_tri', value: cond_trimestre});
            aData.push({name: 'condiciones_fec', value: cond_fecha});

            aData.push({name: 'documento', value: documento});
            aData.push({name: 'nombre', value: nombre});
            aData.push({name: 'apellido', value: apellido});
            aData.push({name: 'matricula', value: matricula});
            aData.push({name: "comision", value: comision});
            aData.push({name: "ciclo", value: ciclo});
            aData.push({name: 'firmo', value: firmo});
            aData.push({name: 'ano', value: ano});
            aData.push({name: "trimestre", value: trimestre});
            aData.push({name: "fecha", value: fecha});

            $.ajax({
                dataType: 'json',
                type: "POST",
                url: sSource,
                data: aData,
                async: true,
                success: fnCallback
            });
        }
    });

    $('#firmaRematricula-table').wrap('<div class="table-responsive"></div>');
    thead = [];
    $(aoColumnDefs).each(function(){
        thead.push(this.sTitle);
    });
    $("#firmaRematricula-table_filter").append('<i id="exportar_informe" class="icon-print grey" onclick="exportar_informe(\'pdf\');" style="cursor: pointer; margin-left: 6px;" data-original-title="" title="exportar"></i>');
    $("#firmaRematricula-table_filter").append('<i id="exportar_informe" class="icon-external-link" onclick="exportar_informe(\'csv\');" style="cursor: pointer; margin-left: 6px;" data-original-title="" title="exportar"></i>');

    $(".select_chosen").chosen({width: "180px"});

    $("#firmaRematricula-table_filter").find("label").addClass("input-icon input-icon-right");
    $("#firmaRematricula-table_filter").find("label").append('<i name="icon_filters" class="icon-caret-down grey" style="margin-right: 7px; cursor: pointer"></i>');
    $("#firmaRematricula-table_filter").append($("[name=container_menu_filters_temp]").html());

    $("[name=container_menu_filters_temp]").remove();
    $("[name=div_table_filters]").hide();

    $("[name=icon_filters]").on("click", function() {
        $("[name=contenedorPrincipal]").toggle();
        $("[name=div_table_filters]").toggle(300);
        return false;
    });
    $("[name=contenedorPrincipal]").on("mousedown", function() {
        $("[name=contenedorPrincipal]").hide();
        $("[name=div_table_filters]").hide(300);
    });

    function cargarComisiones(elemento) {
     document.getElementById(elemento).innerHTML = "";
        $.ajax({
            url: BASE_URL + 'comisiones/getComisionesSelect',
            type: "POST",
            data: {},
            dataType: "JSON",
            cache: false,
            success: function (respuesta) {
                var terms = {};
                $.each(respuesta.rows, function (i, val) {
                    var agregar = document.getElementById(elemento);
                    var option = document.createElement("option");
                    terms[val.nombre] = val.nombre;
                    option.text = val.nombre;
                    agregar.add(option);
                });
            }
        });
    }

    $("[name=filtro_avanzado_usuario_campo]").on("change", function(){
        var nom_row = $(this).parent().parent().attr('class');
        num_row = nom_row.substr(11);//es el numero
        var nom_row = nom_row.substr(0,11);//es el nombre del row sin el numero
        var num_row = parseInt(num_row);
        var nombreCampo = $(this).val();
        $.ajax({
            type: "POST",
            url: BASE_URL + 'rematriculaciones/condicionesfiltro',
            data: {campo:$(this).val()},
            dataType: 'json',
            cache: false,
            success: function(respuesta){
                $("#"+num_row+" [name=filtro_avanzado_usuario_condicion]").html('');
                $("#"+num_row+" [name=filtro_avanzado_usuario_condicion]").append('<option value="'+respuesta.a.id+'">'+respuesta.a.display+'</option>');
                $("#"+num_row+" [name=filtro_avanzado_div_valores]").html('');

                if(respuesta.a.id != "-1") {
                    switch (nombreCampo) {
                        case "documento":
                            $("#" + num_row + " [name=filtro_avanzado_div_valores]").append('<input type="text" name="filtro_documento" class="filter_' + num_row + '_' + num_row + '" value="">');
                            break;
                        case "nombre":
                            $("#" + num_row + " [name=filtro_avanzado_div_valores]").append('<input type="text" name="filtro_nombre" class="filter_' + num_row + '_' + num_row + '" value="">');
                            break;
                        case "apellido":
                            $("#" + num_row + " [name=filtro_avanzado_div_valores]").append('<input type="text" name="filtro_apellido" class="filter_' + num_row + '_' + num_row + '" value="">');
                            break;
                        case "matricula":
                            $("#" + num_row + " [name=filtro_avanzado_div_valores]").append('<input type="text" name="filtro_matricula" class="filter_' + num_row + '_' + num_row + '" value="">');
                            break;
                        case "comision":
                            $("#" + num_row + " [name=filtro_avanzado_div_valores]").append('<select  id ="id_'+num_row+'" name="filtro_comision">' +'<option class="filter_' + num_row + '_' + num_row + '"></option></select>');
                            cargarComisiones("id_"+num_row);
                            break
                        case "ciclo":
                            $("#" + num_row + " [name=filtro_avanzado_div_valores]").append('<input type="text" name="filtro_ciclo" class="filter_' + num_row + '_' + num_row + '" value="">');
                            break;
                        case "firmo":
                            $("#" + num_row + " [name=filtro_avanzado_div_valores]").append('<select name="filtro_firmo">' +'<option value="si" class="filter_' + num_row + '_' + num_row + '">'+lang.SI+'</option><option value="no" class="filter_' + num_row + '_' + num_row + '">'+lang.NO+'</option></select>');
                            break;
                        case "ano":
                            $("#" + num_row + " [name=filtro_avanzado_div_valores]").append('<select name="filtro_ano">' +'<option value="1" class="filter_' + num_row + '_' + num_row + '">'+1+'</option><option value="2" class="filter_' + num_row + '_' + num_row + '">'+2+'</option></select>');
                            break;
                        case "trimestre":
                            $("#" + num_row + " [name=filtro_avanzado_div_valores]").append('<select name="filtro_trimestre">' +'<option value="1" class="filter_' + num_row + '_' + num_row + '">'+'1º Trimestre (Janeiro - Março)'+'</option><option value="2" class="filter_' + num_row + '_' + num_row + '">'+'2º Trimestre (Abril - Junho)'+'</option><option value="3" class="filter_' + num_row + '_' + num_row + '">'+'3º Trimestre (Julho - Setembro)'+'</option><option value="4" class="filter_' + num_row + '_' + num_row + '">'+'4º Trimestre (Outubro - Dezembro)'+'</option></select>');
                            break;
                        case "fecha":
                            $("#" + num_row + " [name=filtro_avanzado_div_valores]").append('<div class="input-group"><input type="text" style="margin: 0px 0px;" name="filtro_fecha" class="date-picker filter_' + num_row + '_' + num_row + '" value=""><span class="input-group-addon" style="padding: 3px 6px;"><i class="icon-calendar bigger-110"></i></span></div>');
                            $(".date-picker").datepicker();
                            break;
                        default:
                            $("#" + num_row + " [name=filtro_avanzado_div_valores]").append('<input type="text" name="filtro_avanzado_usuario_valor" class="filter_' + num_row + '_' + num_row + '" value="">');
                            break;
                    }
                }
            }
        });
    });
}

function columnName(name){
    var retorno='';
    $(thead).each(function(key,valor){
        if(valor===name){
            retorno=key;
        }
    });
    return retorno;
}

function listar(){
  oTable.fnDraw();
}

function exportar_informe(tipo_reporte){
    var iSortCol_0 = oTable.fnSettings().aaSorting[0][0];
    var sSortDir_0 = oTable.fnSettings().aaSorting[0][1];
    var iDisplayLength = oTable.fnSettings()._iDisplayLength;
    var iDisplayStart = oTable.fnSettings()._iDisplayStart;
    var sSearch = $("#firmaRematricula-table_filter").find("input[type=search]").val();

    var documento = $("[name=filtro_documento]").val();
    var nombre = $("[name=filtro_nombre]").val();
    var apellido = $("[name=filtro_apellido]").val();
    var matricula = $("[name=filtro_matricula]").val();
    var comision = $("[name=filtro_comision]").val();
    var ciclo = $("[name=filtro_ciclo]").val();
    var firmo = $("[name=filtro_firmo]").val();
    var ano = $("[name=filtro_ano]").val();
    var trimestre = $("[name=filtro_trimestre]").val();
    var fecha = $("[name=filtro_fecha]").val();

    $("[name=frm_exportar]").find("[name=iSortCol_0]").val(iSortCol_0);
    $("[name=frm_exportar]").find("[name=sSortDir_0]").val(sSortDir_0);
    $("[name=frm_exportar]").find("[name=iDisplayLength]").val(iDisplayLength);
    $("[name=frm_exportar]").find("[name=iDisplayStart]").val(iDisplayStart);
    $("[name=frm_exportar]").find("[name=sSearch]").val(sSearch);

    $("[name=frm_exportar]").find("[name=documento]").val(documento);
    $("[name=frm_exportar]").find("[name=nombre]").val(nombre);
    $("[name=frm_exportar]").find("[name=apellido]").val(apellido);
    $("[name=frm_exportar]").find("[name=matricula]").val(matricula);
    $("[name=frm_exportar]").find("[name=comision]").val(comision);
    $("[name=frm_exportar]").find("[name=ciclo]").val(ciclo);
    $("[name=frm_exportar]").find("[name=firmo]").val(firmo);
    $("[name=frm_exportar]").find("[name=ano]").val(ano);
    $("[name=frm_exportar]").find("[name=trimestre]").val(trimestre);
    $("[name=frm_exportar]").find("[name=fecha]").val(fecha);

    $("[name=frm_exportar]").find("[name=tipo_reporte]").val(tipo_reporte);
    $("[name=frm_exportar]").submit();
}

$('#nuevaFirma').on('click',function(){
    var accion = $(this).attr('accion');
    if(accion = 'nuevaFirma'){
        $.ajax({
            url: BASE_URL + 'rematriculaciones/frm_firmas',
            data: 'cod_firma=-1',
            type: 'POST',
            cache: false,
            success:function(respuesta){
                $.fancybox.open(respuesta,{
                    scrolling: false,
                    width: '50%',
                    height: 'auto',
                    autoSize: false,
                    padding: 0,
                    openEffect: 'none',
                    closeEffect: 'none',
                    helpers: {
                        overlay: null
                    },
                    beforeClose: function() {
                        oTable.fnDraw();
                    }
                });
            }
        });
    }
    return false;
});
