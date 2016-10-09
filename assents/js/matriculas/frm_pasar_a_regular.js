
var oTable = '';
var langfrm_libres;
var seleccionados = [];
langfrm_libres = frmLang;

function listar(){
    oTable = $('#academicoMatriculas_reagularizar_alumnos').dataTable({
        "bServerSide": true,
        "sAjaxSource": BASE_URL + 'matriculas/listar_pasar_a_regular',
        "sServerMethod": "POST",
        "aaSorting": [[0, "desc"]],
        "bDestroy" : true,
        "aoColumns": [{"bSortable": false},
         null, 
         null,
         null,
         null,
         null
         ],
        fnServerData: function(sSource, aoData, fnCallback) {
            $("#menuMover").hide();
            aoData.push({
                name: "curso",
                value: $("[name=filtro_cursos]").val()
            });
            aoData.push({
                name: "materia",
                value: $("[name=filtro_materias]").val()
            });
            aoData.push({
                name: "fecha_desde",
                value: $("[name=filtro_fecha_desde]").val()
            });
            aoData.push({
                name: "fecha_hasta",
                value: $("[name=filtro_fecha_hasta]").val()
            });
            aoData.push({
                name: "comision",
                value: $("[name=filtro_comision]").val()
            });
            $.ajax({
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": aoData + "&todos="+todos ,
                "async": true,
                "success": fnCallback
            });
        },
        "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {            
            var complemento = "";                
            var idx = seleccionados.indexOf(aData[0]); // Localizamos el indice del elemento en array
            if(idx!=-1){
                complemento ='checked';
            }else{
                $("[name=seleccionar_todos]").prop("checked",false);
            }                
            var disabledCheck = aData[6] != "0" ? 'disabled="disabled" title="Faltan cargar Asistencias"' : "";    
            var _html = '';
            _html += '<label class="inline">';
            _html += '<input class="ace" type="checkbox" value="' + aData[0] + '" name="cod_estado_academico" onclick="desactiverSeleccionarTodos(this);"  '+complemento+' ' + disabledCheck  + '>';
            _html += '<span class="lbl"></span>';
            _html += '</label>';
            $('td:eq(0)', nRow).html(_html);            
            var asistencia = aData[6] != "0" ? "<a href='#' class='ver-asistencias' estadoacademico='" +   aData[0] + "' onclick='cargarAsistenciaEstadoAcademico(" + aData[0] + ")'> (" + langfrm_libres.falta_cargar + ")</a>" : "";
            $('td:eq(5)', nRow).append(asistencia);
            return nRow;
        }
    });
    $('.dataTables_length').parent().addClass('no-padding');    
    var _html = '<div class="btn-group">';
    _html += '<button class="btn btn-primary boton-primario" onclick="document.location.href=\'' + BASE_URL + "matriculas/" + '\'">';
    _html += '<i class="icon-reply"></i>';
    _html += langfrm_libres.volver;
    _html += '</button>';
    _html +='<div class="btn-group padding-left-3"><button id="menuMover" class="btn btn btn-primary" onclick="pasarSeleccionadosARegular();" style="display: none;">'+langfrm_libres.regularizar_alumnos+'</button></div>';
    _html += '</div></div>';
    $("#academicoMatriculas_reagularizar_alumnos_length").html(_html);
    _html = '<i class="icon-caret-down grey bigger-110 bigger-140" style="margin-right: 3px; cursor: pointer; margin-left: 10px;" name="table_filters" onclick="ver_ocultar_filtros();"></i>';
    $("#academicoMatriculas_reagularizar_alumnos_filter").append(_html);
    $("#areaTablas");
}

function init(){
    $(".select_chosen").chosen();
    $(".date-picker").datepicker();
    $("[name=div_table_filters]").hide();
    listar();
    $("[name=contenedorPrincipal]").on("click", function(){
        ver_ocultar_filtros();
    });    
}

function desactiverSeleccionarTodos(element){
   var valor = $(element).val();
    if($(element).is(':checked')){
        seleccionados.push(valor);
    }else{
       var idx = seleccionados.indexOf(valor); // Localizamos el indice del elemento en array
        if(idx!=-1) seleccionados.splice(idx, 1); // Lo borramos definitivamente 
    }
    if (seleccionados.length > 0){
        $("#menuMover").show();
    } else {
        $("#menuMover").hide();
    }
}

function checkAllEstadoAcademico(){
    oTable.$("[name=cod_estado_academico]").trigger('click');
    if (seleccionados.length > 0){
        $("#menuMover").show();
    } else {
        $("#menuMover").hide();
    }
}

function pasarSeleccionadosARegular(){
    var seleccionados = $("[name=cod_estado_academico]:checked");
    if (seleccionados.length > 0){
        var estadosacademicos = new Array();
        for (var i = 0; i < seleccionados.length; i++){
            estadosacademicos.push(seleccionados[i].value);
        }
        $.ajax({
            url: BASE_URL + 'matriculas/pasar_a_regular',
            type: 'POST',
            dataType: 'json',
            data: {
                estadosacademicos: estadosacademicos
            },
            success: function(_json){
                if (_json.success){
                    gritter(langfrm_libres.validacion_ok, true, langfrm_libres.BIEN);
                    oTable.fnDraw();
                } else {
                     gritter("", false, langfrm_libres.ERROR);
                }
            }
        });
    } else {
        gritter(langfrm_libres.debe_seleccionar_un_alumno, false, langfrm_libres.ERROR);
    }    
}

$(document).ready(function(){
    init();
});

function cargarAsistenciaEstadoAcademico(cod_estado_academico){
    $.ajax({
        url: BASE_URL + 'asistencias/frm_asistenciasAlumno',
        type: 'POST',
        data:{
            cod_estado_academico: cod_estado_academico
        },
        success: function(_html){
             $.fancybox.open(_html,{
                autoSize: false,
                width: '70%',
                height: 'auto',
                scrolling: 'auto',
                padding: 1,
                beforeClose: function(){
                    oTable.fnDraw();
                }
            });
        }
    });
}

function ver_ocultar_filtros(){
    if ($("[name=div_table_filters]").is(":visible")){
        $("[name=contenedorPrincipal]").hide();
        $("[name=div_table_filters]").hide(300);
    } else {
        $("[name=div_table_filters]").show(300);
        $("[name=contenedorPrincipal]").show();
    }
}

function get_materias(){
    var cod_curso = $("[name=filtro_cursos]").val();
    var element = $("[name=filtro_materias]");
    $(element).find("option").remove();
    $(element).append("<option value='-1'>(" + langfrm_libres.recuperando + ")</option>");
    $(element).trigger("chosen:updated");
    $.ajax({
        url: BASE_URL + "cursos/get_materias",
        type: 'POST',
        dataType: 'json',
        data: {
            cod_curso: cod_curso
        },
        success: function(_json){            
            $(element).find("option").remove();
            $(element).append("<option value='-1'>(" + langfrm_libres.todas.toLowerCase() + ")</option>");
            $.each(_json, function(key, value){
                $(element).append("<option value='" + value.codigo + "'>" + value.nombre + "</option>");
            });
            $(element).trigger("chosen:updated");
        }
    });
}