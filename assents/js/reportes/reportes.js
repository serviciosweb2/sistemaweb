var iSortCol = '';
var iSortDir = '';
var id_filtro_actual = 0;
var lang = BASE_LANG;
var current_page = 1;
var iFieldView = [];
var tag_input  = '';
var cantFiltros = 0;

$(document).ready(function(){
    tag_input = $('#form-field-icon-2');
    if (!( /msie\s*(8|7|6)/.test(navigator.userAgent.toLowerCase()))){
        tag_input.tag({
            placeholder:tag_input.attr('placeholder'),
            source: ace.variable_US_STATES
        });
    } else {
        tag_input.after('<textarea id="'+tag_input.attr('id')+'" name="'+tag_input.attr('name')+'" rows="3">'+tag_input.val()+'</textarea>').remove();
    }

    $('.tags span').each(function(k, valor){    
        $(filtrosReportes).each(function(tipo,filtro){
            var valueSpan = valor.firstChild.data;
            if(valueSpan == filtro.filter_name){
                var string = {"id":filtro.filter_code};
                var objFormateado = {
                   'data_group': 'filtro_personalizado',
                   'data_json': JSON.stringify(string)
                };
                $(valor).attr(objFormateado);
                $(valor).append('<i class="icon-asterisk"></i>');
            }
        });
    });
    
    initialize();
    $('#imprimir_informe').popover({
        html: false,
        placement: 'left',
        trigger: 'hover',
        content: lang.imprimir_informe
    });

    $('#exportar_informe').popover({
        html: false,
        placement: 'left',
        trigger: 'hover',
        content: lang.exportar_informe
    });

    if(nombre_reporte != 'cobros_estimados') {
        $("input[type=checkbox]").click();
        $("input[type=checkbox]").click();
    }
});

function exportarCsv(obj){
    $("input[name='exportar_reporte']").val(obj);
    $('#exportar').submit();
}

function initialize(){
    id_filtro_actual = $("[name=cantidad_registros_filtros_usuarios]").val();    
    $("[name=filtro_avanzado_usuario] .date-picker").datepicker({
        format: "dd/mm/yyyy"
    });
    
    $("#areaTablas").on("click", "ul.pagination li a.number", function(){
        current_page = this.id;
        changePage(this.id);
    });
    
    $("#areaTablas").on("click", "ul.pagination li a#prev", function(){
        var currentPage = current_page;
        if (currentPage > 1){
            current_page --;
            changePage(current_page);
        }
    });
    
    $("#areaTablas").on("click", "ul.pagination li a#next", function(){
        var currentPage = current_page;
        currentPage ++;
        if ($("ul.pagination li a.page_" + currentPage).length){
            current_page ++;
            changePage(current_page);
        }
    });
    
    $("#areaTablas").on("click", ".tableHead", function(){        
        var orden = $("#table_th_" + $(this).attr("name") + ".tableHead").hasClass("sorting_desc") ? "asc" : "desc";
        $(".tableHead").removeClass("sorting_desc");
        $(".tableHead").removeClass("sorting_asc");
        $(".tableHead").addClass("sorting");
        $("#table_th_" + $(this).attr("name") + ".tableHead").addClass("sorting_" + orden);
        iSortCol = $(this).attr("name");
        iSortDir = orden;        
        getTable();        
    });
    
    $("[name=DataTables_Table_0_length]").on("change", function(){        
        getTable();
    });
    
    $("[name=search_table]").on("keyup", function(){       
        getTable();
    });
    
    $('#form-field-icon-2').on('added', function (e, value) {
        setTimeout(function(){
             getTable(); 
         },250);                        
    });
        
    $('#agregar_tag').on('keyup', function(event){
        if(event.keyCode == 13){
            var hayIcono = $('.tags span:last').find('i');
            if(!hayIcono.length){
                $('.tags span:last').append('<i class="icon-filter"></i>');
            }
        }
    });
    
    $('#form-field-icon-2').on('removed', function (e, element) {
        var data_group = $(element).attr('data_group');
        
        switch(data_group){
            case 'filtro_comun':
                var data_json = JSON.parse($(element).attr('data_json'));
                $('input[value='+data_json.value+']').prop('checked',false);
                break;
            
            case 'filtro_avanzado':                
                var data_json = JSON.parse($(element).attr('data_json'));
                var id = data_json.id;                
                $('#filtro_opciones_busqueda_avanzada_display #'+id+'').remove();
                $('#'+id+'').removeClass('filtros_personales_usuario_selected');
                $("[name=grupo_filtros_avanzados]").html("");
                break;
            
            case 'filtro_personalizado':                
                var data_json = JSON.parse($(element).attr('data_json'));
                $('#'+data_json.id+'').removeClass('filtros_personales_usuario_selected');
                $("[name=grupo_filtros_avanzados]").html("");
                break;
        }        
        getTable();
    });
    
    $("[name=table_filters]").on("click", function(){       
        $("[name=contenedorPrincipal]").toggle();
        $("[name=div_table_filters]").toggle(300);        
        return false;
    });
    
    $("[name=contenedorPrincipal]").on("click", function(){
        $("[name=contenedorPrincipal]").hide();
        $("[name=div_table_filters]").hide(300);
    });
 
    $(".filtro_opciones").on("click", function(){
        var id = $(this).attr("id");
        $("#" + id + "_display").toggle(100);
    });
    
    $("[name=common_filters]").on("click", function(){
        var common_filters = $(this).val();
        if ($(this).is(':checked')){
            var obj = {
                "value":common_filters
            };
            var objFormateado = {
                'data_json':JSON.stringify(obj),
                'data_group': 'filtro_comun'
            };
            var string = formatearStringTag(objFormateado);
            agregarTag(string,objFormateado);
        } else {
            $('.tags span[data_group = "filtro_comun"]').each(function(k, valor){
                var arrJson = JSON.parse($(valor).attr('data_json'));
                if(common_filters == arrJson.value){
                    $(valor).find('.close').trigger('click');
                }
            });           
        }       
    });
    
    $("#filtro_opciones_busqueda_avanzada_display").on("change", "[name=filtro_avanzado_usuario_campo]", function(){
        var select_id = $(this).attr("id");
        $("[name=filtro_avanzado_usuario_condicion].filter_" + select_id).find('option').remove().end().append("<option value=-1>(" + lang.recuperando + ")<option>");
        $("[name=filtro_avanzado_usuario_condicion].filter_" + select_id).attr("disabled", true);
        var report_name = $("[name=report_name]").val();
        var field_name = this.value;
        if (field_name != -1){
            $.ajax({
                url: BASE_URL + 'reportes/getFiltrosCondiciones',
                type: 'POST',
                dataType: 'json',
                data: {
                    report_name: report_name,
                    field_name: field_name
                },
                success: function(_json){
                    $("[name=filtro_avanzado_usuario_condicion].filter_" + select_id).find('option').remove();                    
                    $.each(_json.filters, function(key, value){
                        var str = '<option value="' + value.id + '">';
                        str += value.display;
                        str += '</option>';
                        $("[name=filtro_avanzado_usuario_condicion].filter_" + select_id).append(str);
                    });
                    var dataType = _json.data_type;
                    var value = _json.type == "array" && _json.set ? JSON.stringify(_json.set) : "";   
                    $("[name=filtro_avanzado_set_values].filter_" + select_id).val(value);
                    setInputFilter(dataType, select_id);
                    $("[name=filtro_avanzado_usuario_condicion].filter_" + select_id).attr("disabled", false);
                }
            });
        }
    });
    
    $("#areaTablas").on("change", "[name=filtro_avanzado_usuario_condicion]", function(){
        var select_id = $(this).attr("id");
        var dataType = $("[name=filtro_avanzado_data_type].filter_" + select_id).val();
        setInputFilter(dataType, select_id);
    });
    
    $("[name=btnBuscar]").on("click", function(){
//       var obj = '';
//        $('div[name="filtro_avanzado_usuario"]').each(function(k,div){
//            obj = $(div).find('input, select').serializeJSON();
//            var objFormateado = {
//               'data_group': 'filtro_avanzado',
//               'data_json': JSON.stringify(obj),
//               'id': $(div).attr('id')
//           };
//           var string = formatearStringTag(objFormateado);
//            agregarTag(string,objFormateado);
//        });        
        getTable();
    });
    
    $("#agregar_filtro_busqueda_usuario").on("click", function(){
        agregarRegistro();
    });
    
    $("#areaTablas").on("click", "[name=remove_filtro_avanzado]", function(){
        $("[name=filtro_avanzado_usuario].filter_" + $(this).attr("id")).remove();
        var id = $(this).attr("id");
        $('.tags span[data_group = "filtro_avanzado"]').each(function(k, valor){
            if(id == $(this).attr("id")){
                $(valor).remove();
                getTable();
            }
        });
    });
    
    $("[name=btn_guardar_filtros_usuarios]").on("click", function(){
        var filters = getFiltrosAvnzados();
        var apply_common_filters = getApplyCommonFilters();
        var report_name = $("[name=report_name]").val();
        var filter_save_name = trim($("[name=save_filter_name]").val());
        var compartir = $("[name=guardar_filtro_compartir_con_todos]").is(":checked") ? 1 : 0;
        var usar_defecto = $("[name=guardar_filtro_usar_por_defecto]").is(":checked") ? 1 : 0;
        var mensaje = '';
        var iFieldView = getFieldView();
        if (filter_save_name == '') mensaje += lang.debe_indicar_el_nombre_del_filtro_a_guardar + "<br>";
        if (mensaje == ''){           
            $.ajax({
                url: BASE_URL + 'reportes/guardarFiltros',
                type: 'POST',
                dataType: 'json',
                data: {
                    filters: filters,
                    apply_common_filters: apply_common_filters,
                    report_name: report_name,
                    filter_save_name: filter_save_name,
                    compartir: compartir,
                    usar_defecto: usar_defecto,
                    iFieldView: iFieldView
                },
                success: function(_json){
                    if (_json.error){
                        gritter(_json.error, false);
                    } else {
                        var codigo_filtro = _json.codigo_filtro;
                        agregarFiltroPersonalizado(filter_save_name, codigo_filtro, true);
                        gritter(lang.filtros_guardados_correctamente, true);
                    }
                }
            });
        } else {
            gritter(mensaje, false);
        }
    });
    
    $("[name=div_filtros_personalizados_guardados]").on("click", "[name=filtros_personalizados_usuario]", function(){
        var id = $(this).attr("id");
        if (!$(this).hasClass("filtros_personales_usuario_selected")){
            $("[name=filtros_personalizados_usuario]").removeClass("filtros_personales_usuario_selected");
            $(this).addClass("filtros_personales_usuario_selected");
            cargarFiltroGuardado(id);            
            var string = {"id": id};
            var objFormateado = {
                'data_group': 'filtro_personalizado',
                'data_json': JSON.stringify(string)
            };
            agregarTag($(this).text(),objFormateado);
        } else {
            $("[name=filtros_personalizados_usuario]").removeClass("filtros_personales_usuario_selected");
            $("[name=grupo_filtros_avanzados]").html("");
            $('.tags span[data_group = "filtro_personalizado"]').each(function(k,valor){
                var arrJson = JSON.parse($(valor).attr('data_json'));
                if(id == arrJson.id){
                    $(valor).find('.close').trigger('click');
                }
            });
        }
    });
    
    $("#areaTablas").on("click", "[name=remove_filtro_personalizado]", function(){
        var id_filtro = $(this).attr("id");
        $.ajax({
            url: BASE_URL + 'reportes/eliminarFiltroGuardado',
            type: 'POST',
            dataType: 'json',
            data: {
                id_filtro: id_filtro
            },
            success: function(_json){
                if (_json.error){
                    gritter(_json.error);
                } else {
                    $("#filtros_personalizados_guardados_" + id_filtro).remove();
                     $('.tags span[data_group = "filtro_personalizado"]').each(function(k, valor){
                         var arrJson = JSON.parse($(valor).attr('data_json'));
                         if(id_filtro == arrJson.id){
                             $(valor).remove();
                             getTable();
                         }
                     });
                    gritter(lang.filtro_eliminado_correctamente, true);
                }
            }
        });
    });
}

function agregarFiltroPersonalizado(nombreFiltro, codigoFiltro, selected){
    var complemento = '';
    if (selected){
        $("[name=filtros_personalizados_usuario]").removeClass("filtros_personales_usuario_selected");
        complemento = " filtros_personales_usuario_selected";
    }
    var str = '';
    str += '<div class="row" style="padding: 0px; background-color: white; border-style: none;" id="filtros_personalizados_guardados_' + codigoFiltro + '">';
    str += '<div class="col-md-11 option_content">';
    str += '<span id="' + codigoFiltro + '" class="filtros_personale_usuario' + complemento + '" name="filtros_personalizados_usuario">';
    str += '<i class="icon-filter"></i>';
    str += nombreFiltro;
    str += '</span>';
    str += '</div>';
    str += '<div class="col-md-1 option_content">';
    str += '<i id="' + codigoFiltro + '" class="icon-remove red" name="remove_filtro_personalizado" style="cursor: pointer"></i>';
    str += '</div>';
    str += '</div>';
    $("[name=div_filtros_personalizados_guardados]").append(str);
}

function agregarRegistro(strTipoConsulta, selectedCampo){
    id_filtro_actual ++; 
    if (!selectedCampo) selectedCampo = -1;
    var str = '';
    str += '<div class="row filter_' + id_filtro_actual + '" id="' + id_filtro_actual + '" name="filtro_avanzado_usuario" style="border-style: none; background-color: white; padding: 0px;">';
    str += '<input type="hidden" class="filter_' + id_filtro_actual + '" name="filtro_avanzado_data_type" value="">';
    str += '<input type="hidden" class="filter_' + id_filtro_actual + '" name="filtro_avanzado_set_values" value="">';
    str += '<div class="form-group col-md-3" id="campo_1">';
    str += '<select name="filtro_avanzado_usuario_campo" id="' + id_filtro_actual + '" style="width: 100%" class="filter_' + id_filtro_actual + '">';
    $("[name=filtro_avanzado_usuario_campo_original] option").each(function(){
        var complemento = $(this).attr("value") == selectedCampo ? ' selected="true"' : '';
        str += '<option value="' + $(this).attr("value") + '"' + complemento + '>';
        str += $(this).text();
        str += "</option>";
    });                                                    
    str += '</select>';
    str += '</div>';    
    str += '<div class="form-group col-md-3" id="condicion_1">';
    if (strTipoConsulta){
        str += strTipoConsulta;
    } else {
        str += '<select name="filtro_avanzado_usuario_condicion" id="' + id_filtro_actual + '" class="filter_' + id_filtro_actual + '" style="width: 100%">';
        str += '<option value="-1">(' + lang.SELECCIONE_UNA_OPCION + ')</option>';
        str += '</select>';
    }
    str += '</div>';    
    str += '<div class="form-group col-md-5 filter_' + id_filtro_actual + '" id="valores_1" name="filtro_avanzado_div_valores">';
    str += '</div>';
    str += '<div class="form-group col-md-1">';
    str += '<i class="icon-remove red" style="cursor: pointer" name="remove_filtro_avanzado" id="' + id_filtro_actual + '" class="filter_' + id_filtro_actual + '"></i>';
    str += '</div>';
    str += '</div>';
    $("[name=grupo_filtros_avanzados]").append(str);
}

function setInputFilter(dataType, select_id){ 
    var filter_type = $('[name=filtro_avanzado_usuario_condicion].filter_' + select_id).val();
    var input = getInputString(dataType, select_id, filter_type);
    $("[name=filtro_avanzado_div_valores].filter_" + select_id).html(input);
    $("[name=filtro_avanzado_data_type].filter_" + select_id).val(dataType);
    $("[name=filtro_avanzado_usuario] .date-picker").datepicker({
        format: "dd/mm/yyyy"
    });
    $(".select-chosen").chosen();
}

function getInputString(dataType, select_id, filterType, valorFiltros1, valorFiltros2){
    if (!valorFiltros1) valorFiltros1 = '';
    if (!valorFiltros2) valorFiltros2 = '';
    var strSet = $("[name=filtro_avanzado_set_values].filter_" + select_id).val();    
    var str = '';
    if (strSet != ''){
        var jsonSet = $.parseJSON(strSet);
        str = '<select name="filtro_avanzado_usuario_valor[]" class="select-chosen filter_' + select_id + '_0" style="max-width: 236px;">';
        $.each(jsonSet, function(key, value){
            var complemento = value.id == valorFiltros1 ? 'selected="true"' : '';
            if (complemento == '' && value.selected){
                complemento = "selected='true'";
            }
            str += '<option value="' + value.id + '"' + complemento + '>';
            str += value.value;
            str += "</option>";
        });
        str += "</select>";
    } else {    
        switch (dataType){
            case "integer":
                str = '<input type="text" style="width: 90px;" value="' + valorFiltros1 + '" name="filtro_avanzado_usuario_valor[]" class="filter_' + select_id + '_0" onkeypress="return ingresarNumero(this, event);">';
                if (filterType == "entre"){
                    str += '<input type="text" style="width: 90px;" value="' + valorFiltros2 + '" name="filtro_avanzado_usuario_valor[]" class="filter_' + select_id + '_1" onkeypress="return ingresarNumero(this, event);">';
                }
                break;
                
            case "float":
                str = '<input type="text" style="width: 90px;" value="' + valorFiltros1 + '" name="filtro_avanzado_usuario_valor[]" class="filter_' + select_id + '_0">';
                if (filterType == "entre"){
                    str += '<input type="text" style="width: 90px;" value="' + valorFiltros2 + '" name="filtro_avanzado_usuario_valor[]" class="filter_' + select_id + '_1">';
                }
                break;

            case "string":
                str = '<input type="text" value="' + valorFiltros1 + '" name="filtro_avanzado_usuario_valor[]" class="filter_' + select_id + '_0">';
                if (filterType == "entre"){
                    str += '<input type="text" value="' + valorFiltros2 + '" name="filtro_avanzado_usuario_valor[]" class="filter_' + select_id + '_1">';
                }
                break;

            case "date":
                str = '<div class="input-group">';
                str += '<input name="filtro_avanzado_usuario_valor[]" value="' + valorFiltros1 + '" class="estilo_date_picker form-control date-picker filter_' + select_id + '_0" type="text" readyonly="true">';
                str += '<span class="input-group-addon" style="padding: 3px 6px;">';
                str += '<i class="icon-calendar bigger-110"></i>';
                str += '</span>';                
                if (filterType == "entre"){
                    str += '<input name="filtro_avanzado_usuario_valor[]"  value="' + valorFiltros2 + '" class="estilo_date_picker form-control date-picker filter_' + select_id + '_1" type="text" style="margin-right: 0px; width: 74px;" readyonly="true">';
                    str += '<span class="input-group-addon" style="padding: 3px 6px;">';
                    str += '<i class="icon-calendar bigger-110"></i>';
                    str += '</span>';
                }
                str += '</div>';
                break;

            case "boolean":
                var complementoTrue = valorFiltros1 == "true" ? ' selected="true"' : '';
                var complementoFalse = valorFiltros2 == "false" ? ' selected="true"' : '';
                str = '<select name="filtro_avanzado_usuario_valor" class="filter_' + select_id + '_0">';
                str += '<option value="true"' + complementoTrue + '>Verdadero</option>';
                str += '<option value="false"' + complementoFalse + '>Falso</option>';
                str += "</select>";
                break;
        }
    }
    return str;
}

function changePage(page){
    $("ul.pagination li").removeClass("active");
    $("ul.pagination li#" + page).addClass("active");   
    getTable();
}

function hideShowColumns(element){
    var id = element.id;
    if (element.checked){
        $("[name=tabla_reportes] .table_col_" + id).show();
    } else {
        $("[name=tabla_reportes] .table_col_" + id).hide();
    }
}

function graficarTabla(aoData){    
    var arrColAcumulable = [];
    var indice_acumulable = aoData.indice_acumulable;
    $.each(indice_acumulable, function(col,val){
        arrColAcumulable[val] = 0;
    });

    $("#limit_min").html(aoData['iLimitMin']);
    $("#limit-top").html(parseInt(aoData['iLimitMin']) + aoData.aaData.length - 1);
    $("#rows_total").html(aoData['iTotalRecords']);
    $("#table_body").empty();
    if (aoData.iTotalRecords > 0){
        $.each(aoData.aaData, function(key, row){
            var str = '<tr>';
            var i = 0;
            $.each(row, function(idx, value){
                $.each(indice_acumulable,function(k,valor){
                    if(idx == valor){
                        arrColAcumulable[valor] = parseFloat(arrColAcumulable[valor]) + parseFloat(row[valor]);
                    }
                });                
                var display = $("#table_th_" + idx).is(":visible") ? "table-cell;" : "none;";
                str += '<td class="table_col_' + idx + '" style="display: ' + display + '">'; 
                str += value == null ? '' : value;
                str += "</td>";
                i++;
            });
            str += '</tr>';
            $("#table_body").append(str);            
        });
        $.each(iFieldView, function(k, colum){
            var x = 0;
            for(var col in arrColAcumulable){             
                if(colum == col){
                  $("[name=valor_acumulable]").eq(x).html(parseFloat(arrColAcumulable[col]).toFixed(2));
                }
                x++;    
            }              
        });
    } else {
        var cantidadColumnas = $("#table_head").find("tr:first th").length;
        str = '<tr class="odd">';
        str += '<td class="dataTables_empty" valign="top" colspan="' + cantidadColumnas + '">';
        str += lang.no_hay_datos_disponivles_pata_mostrar;
        str += '</td>';
        str += '</tr>';
        $("#table_body").append(str);
        var tfoot = $("#table_body").closest("table").find("tfoot").find("[name=valor_acumulable]");
        $.each(tfoot, function(key, element){
            $(element).html("");
        });
    }    
    $("ul.pagination").empty();
    var complemento = aoData.iPagesCount == 1 ? "disabled" : "";
    var str = '';
    str += '<li class="prev ' + complemento + '">';
    str += '<a id="prev">';
    str += '<i class="icon-double-angle-left"></i>';
    str += '</a>';
    str += '</li>';
    $("ul.pagination").append(str);
    str = '';
    var imprimirSalto = aoData.iPagesCount > 15;
    var initPagination = imprimirSalto ? aoData.iCurrentPage - 3 : 1;
    var initSalto = aoData.iCurrentPage + 3;
    var endSalto = aoData.iPagesCount - 3;
    if (initPagination < 1){
        initPagination = 1;
    }
        
    for (var i = initPagination; i <= aoData.iPagesCount; i++){        
        if (imprimirSalto && i >= initSalto && i < endSalto){
            if (i == initSalto){
                str += '<li class="step disabled">';
                str += '<a id="step">...</a>';
                str += '</li>';
            }
        } else {
            var active = i == aoData.iCurrentPage ? 'class="active"' : '';
            str += '<li id="' + i + '" ' + active + '>';
            str += '<a class="number page_' + i + '" id="' + i + '">' + i + '</a>';
            str += '</li>';
        }        
    }
    $("ul.pagination").append(str);    
    str = '';
    str += '<li class="next ' + complemento + '">';
    str += '<a id="next">';
    str += '<i class="icon-double-angle-right"></i>';
    str += '</a>';
    str += '</li>';
    $("ul.pagination").append(str);
    var checkeds = $('[name=columnas_visibles]');
    for (var i = 0; i < checkeds.length; i++){
        if ($(checkeds[i]).prop("checked")){
            $("[name=tabla_reportes] .table_col_" + $(checkeds[i]).attr("id")).show();
        } else {
            $("[name=tabla_reportes] .table_col_" + $(checkeds[i]).attr("id")).hide();
        }
    }    
}

function getTable(route,exportarImprimir){
    $("ul.pagination li").addClass("disabled");
    var iPaginationLength = $("[name=DataTables_Table_0_length]").val();
    var iCurrentPage = current_page;
    var report_name = $("[name=report_name]").val();
    iFieldView = getFieldView();
    var filters = getFiltrosAvnzados();
    
    if(filters[0] != null)
    {
        if (filters[0].status && filters[0].status == 'filters_error' && filters[0].msg){
        gritter(lang.error_en_filtro_aplicado + "<br>" + filters[0].msg, false, ' ');
        return true;
        }
    }
    
    var data_post =  getPost();
    if(route){
        var obj = {
            iPaginationLength: iPaginationLength,
            iCurrentPage: current_page,
            report_name: report_name,
            sSearch: data_post.sSearch,
            iSortDir: iSortDir,
            iSortCol: iSortCol,
            iFieldView: iFieldView,
            apply_common_filters: data_post.apply_common_filters ,
            filters: filters
        };
        
        if(exportarImprimir){
            exportarCsv(JSON.stringify(obj));
        } else {
            printers_jobs(12,JSON.stringify(obj));
        }
    } else {
        $.ajax({
            url: BASE_URL + 'reportes/getReporte',
            type: 'POST',
            dataType: 'json',
            data: {
                iPaginationLength: iPaginationLength,
                iCurrentPage: iCurrentPage,
                report_name: report_name,
                iSortDir: iSortDir,
                iSortCol: iSortCol,
                sSearch: data_post.sSearch,
                iFieldView: iFieldView,
                apply_common_filters: data_post.apply_common_filters,
                filters: filters
            },
            success: function(_json){
                if(_json.codigo == 1){
                    graficarTabla(_json);
                } else {
                    gritter(_json.msgerror);
                }
            }
        });       
    }
}

function getFieldView(){
    var temp = $("[name=columnas_visibles]:checked");
    var iFieldView = new Array();
    for (var i = 0; i < temp.length; i++){
        iFieldView.push(temp[i].value);
    }
    return iFieldView;
}

function getApplyCommonFilters(){
    var tempFilter = $("[name=common_filters]:checked");
    var apply_common_filters = new Array();
    for (var i = 0; i < tempFilter.length; i++){
        apply_common_filters.push(tempFilter[i].value);
    }    
    return apply_common_filters;
}

function getFiltrosAvnzados(){
    var filtersTemp = $("[name=filtro_avanzado_usuario_campo]");
    var filters = new Array();    
    for (var i = 0; i < filtersTemp.length; i++){
        if (filtersTemp[i].value != -1){
            var select_id = $(filtersTemp[i]).attr("id");            
            var field = $("[name=filtro_avanzado_usuario_campo].filter_" + select_id).val();           
            var condicion = $("[name=filtro_avanzado_usuario_condicion].filter_" + select_id).val();
            
            var valor1 = $(".filter_" + select_id + "_0").val();
            var valor2 = $(".filter_" + select_id + "_1").val();
            var dataType = $("[name=filtro_avanzado_data_type].filter_" + select_id).val();
            if (dataType == 'date' && condicion == 'entre'){
                var fecha1 = valor1.split("/");
                var fecha2 = valor2.split("/");
                var fechadesde = '';
                if (fecha1.length == 3){
                    fechadesde = fecha1[2] + fecha1[1] + fecha1[0];
                }
                var fechahasta = '99999999';
                if (fecha2.length == 3){
                    fechahasta = fecha2[2] + fecha2[1] + fecha2[0];
                }
                if (fechadesde > fechahasta){
                    var retorno = new Array();
                    retorno.push({
                        status: "filters_error",
                        msg: lang.fecha_desde_es_mayor_a_fecha_hasta
                    });
                    return retorno;
                }
            }
            filters.push({
                field: field,
                filter: condicion,
                value1: valor1,
                value2: valor2,
                dataType: dataType
            });
        }
    }    
    return filters;
}

function cargarFiltroGuardado(codigo_filtro){    
    var report_name = $("[name=report_name]").val();
    $.ajax({
        url: BASE_URL + "reportes/getFiltroGuardado",
        type: 'POST',
        dataType: 'json',
        data: {
            codigo_filtro: codigo_filtro,
            report_name: report_name
        },
        success: function(_json){            
            if (_json.filters){
                $.each(_json.filters, function(index, filtros){
                    if (filtros.field_view){
                        setVisiblesField(filtros.field_view);
                    }
                    if (filtros.advanced_filters){
                        id_filtro_actual = 0;
                        $("[name=grupo_filtros_avanzados]").html("");
                        $.each(filtros.advanced_filters, function(key, advanced){                            
                            var dataType = advanced.dataType;
                            var filterType = advanced.filter;
                            var selected = advanced.field;
                            var valorFiltros1 = advanced.value1;
                            var valorFiltros2 = advanced.value2 ? advanced.value2 : '';                             
                            if (selected != ''){               
                                var strFilters = '<select id="' + (id_filtro_actual + 1) +'" class="filter_' + (id_filtro_actual + 1) + '" style="width: 100%" name="filtro_avanzado_usuario_condicion">';                                
                                $.each(advanced.data_set.filters, function(idx, filters){
                                    var complemento = filters.id == filterType ? 'selected="true"' : '';
                                    strFilters += '<option value="' + filters.id + '"' + complemento +'>';
                                    strFilters += filters.display;
                                    strFilters += "</option>";
                                });
                                strFilters += "</select>";
                                agregarRegistro(strFilters, selected);
                                $("[name=filtro_avanzado_data_type].filter_" + id_filtro_actual).val(dataType);
                                var value = advanced.data_set.type == "array" && advanced.data_set.set ? JSON.stringify(advanced.data_set.set) : "";
                                $("[name=filtro_avanzado_set_values].filter_" + id_filtro_actual).val(value);
                                var strValues = getInputString(dataType, id_filtro_actual, filterType, valorFiltros1, valorFiltros2);
                                $("[name=filtro_avanzado_div_valores].filter_" + id_filtro_actual).html(strValues); 
                            }
                        });
                    }
                    if(filtros.common_filters){
                        $(filtros.common_filters).each(function(j,valor){
                            $('input[value="'+valor+'"]').trigger('click');
                        });
                    }
                });
                $("[name=filtro_avanzado_usuario] .date-picker").datepicker({
                    format: "dd/mm/yyyy"
                });
                $(".select-chosen").chosen();                
            }
        }
    });
}

function setVisiblesField(jsonFiled){
    $("[name=columnas_visibles]").prop("checked", false);
    $.each(jsonFiled, function(idx, field_view){
        $("[name=columnas_visibles]." + field_view).prop("checked", true);
    });    
}

function imprimirReporte(){
    var filtros = getFiltrosAvnzados();
    printers_jobs(12,JSON.stringify(filtros));
}

function agregarTag(filtros,obj){
    $('#agregar_tag').val(filtros);
     tag_input.tag('process');
    $('.tags span:last').attr(obj);
     switch(obj.data_group){
        case 'filtro_comun':
            var hayIcono = $('.tags span:last').find('i');
            if(!hayIcono.length)
                $('.tags span:last').append('<i class="icon-filter"></i>');
             break;
         
        case 'filtro_avanzado':
            var hayIcono = $('.tags span:last').find('i');
            if(!hayIcono.length)
                $('.tags span:last').append('<i class="icon-search"></i>');
            break;
        
        case 'filtro_personalizado':
            var hayIcono = $('.tags span:last').find('i');
            if(!hayIcono.length)
                $('.tags span:last').append('<i class="icon-asterisk"></i>');
            break;         
    }
}

function getPost(){
    var retorno =[];
    retorno['apply_common_filters'] = [];
    retorno['sSearch'] = [];
    retorno['filters'] = [];
    retorno['filtro_personalizado'] = [];
    $('.tags span').each(function(key,span){
        var data_group = $(span).attr('data_group');
        switch(data_group){
            case 'filtro_comun':
                var arrData = JSON.parse($(span).attr('data_json'));
                retorno['apply_common_filters'].push(arrData.value);
                break;
            
            case 'filtro_avanzado':
                var arrData = JSON.parse($(span).attr('data_json'));
                var obj ={
                    "dataType":arrData.filtro_avanzado_data_type,
                    "field": arrData.filtro_avanzado_usuario_campo,
                    "filter": arrData.filtro_avanzado_usuario_condicion
                };
                var i = 1;
                $(arrData.filtro_avanzado_usuario_valor).each(function(k,value){
                    obj["value"+i] = value;
                    i++;
                });
                retorno['filters'].push(obj);
                break;
            
            case 'filtro_personalizado':
                
            break;
            
            default:
                var text = span.firstChild.data;
                retorno['sSearch'].push(text);
                break;
        }
    });
    return retorno;
}

function formatearStringTag(obj){
    var string = '';
    switch (obj.data_group){
        case 'filtro_comun':
            var arrJson = JSON.parse(obj.data_json);
            string = lang[arrJson.value];
            break;
        
        case 'filtro_avanzado':
           var arrJson = JSON.parse(obj.data_json);
           var campo_filtado = '';
            $(arrObjColums).each(function(key,value){
                campo_filtado = value[arrJson.filtro_avanzado_usuario_campo]['display'];
            });
            var valores = '';
            string = campo_filtado+' '+lang[arrJson.filtro_avanzado_usuario_condicion];
            if(arrJson.filtro_avanzado_set_values.length > 0){
                $(JSON.parse(arrJson.filtro_avanzado_set_values)).each(function(id,respuesta){
                    $(arrJson.filtro_avanzado_usuario_valor).each(function(cod,codigo){
                        if(codigo == id+1){
                           valores += respuesta.value;
                        }
                    });
                });
            } else {
                $(arrJson.filtro_avanzado_usuario_valor).each(function(k,valor){
                    valores += valor+' ';
                });
            }
            string += ' ' + valores;
            break;
    }
    return string;
}