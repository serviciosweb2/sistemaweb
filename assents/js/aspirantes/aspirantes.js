var claves = Array("codigo");
var thead=[];
var codigo='';
var lang = BASE_LANG;
var aoColumnDefs = columns;
var menu = BASE_MENU_JSON;
var oTable = '';

function columnName(name){
    var retorno='';
    $(thead).each(function(key,columna){
        if(columna === name){
            retorno = key;
        }
    });
    return retorno;
}


function init(){
    oTable = $('#academicoAspirantes').dataTable({
        "oLanguage": {
            "sLengthMenu": 'Mostrar <select>'+
            '<option value="-1">Todos</option>'+ // es el que selecciona por defecto
            '<option value="10" SELECTED>10</option>'+
            '<option value="20">20</option>'+
            '<option value="30">30</option>'+
            '<option value="40">40</option>'+
            '<option value="50">50</option>'+
            '</select> Registros' 
        },
        bServerSide: true,
        sAjaxSource: BASE_URL + 'aspirantes/listar',
        aaSorting: [[ 0, "desc" ]],
        sServerMethod: "POST",
        fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            return nRow;
        },
        aoColumnDefs: aoColumnDefs,
        fnServerData: function(sSource, aData, fnCallback){
            var tipo_contacto = $("[name=filtro_tipo_contacto]").val();
            var medio = $("[name=filtro_medio]").val();
            var curso_interes = $("[name=filtro_curso_interes]").val();
            var turno = $("[name=filtro_turno]").val();
            var es_alumno = $("[name=filtro_es_alumno]").val();
            var fecha_desde = $("[name=filtro_fecha_desde]").val();
            var fecha_hasta = $("[name=filtro_fecha_hasta]").val();
            aData.push({name: 'tipo_contacto', value: tipo_contacto});
            aData.push({name: 'medio', value: medio});
            aData.push({name: 'curso_interes', value: curso_interes});
            aData.push({name: 'turno', value: turno});
            aData.push({name: "es_alumno", value: es_alumno});
            aData.push({name: "fecha_desde", value: fecha_desde});
            aData.push({name: "fecha_hasta", value: fecha_hasta});
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
    
    $('#academicoAspirantes').wrap( "<div class='table-responsive'></div>" );
    
    $("#academicoAspirantes_filter").find("label").addClass("input-icon input-icon-right");
    $("#academicoAspirantes_filter").find("label").append('<i name="icon_filters" class="icon-caret-down grey" style="margin-right: 7px; cursor: pointer"></i>');
    $("#academicoAspirantes_filter").append($("[name=container_menu_filters_temp]").html());
    $(".date-picker").datepicker();
    $(".select_chosen").chosen();
    $("[name=container_menu_filters_temp]").remove();
    $("[name=div_table_filters]").hide();
    
    $("#academicoAspirantes_filter").append('<i id="exportar_informe" class="icon-print grey" onclick="exportar_informe(\'pdf\');" style="cursor: pointer; margin-left: 6px;" data-original-title="" title="exportar"></i>');
    $("#academicoAspirantes_filter").append('<i id="exportar_informe" class="icon-external-link" onclick="exportar_informe(\'csv\');" style="cursor: pointer; margin-left: 6px;" data-original-title="" title="exportar"></i>');
        
    $("[name=icon_filters]").on("click", function() {
        $("[name=contenedorPrincipal]").toggle();
        $("[name=div_table_filters]").toggle(300);
        return false;
    });

    $("[name=contenedorPrincipal]").on("mousedown", function() {
        $("[name=contenedorPrincipal]").hide();
        $("[name=div_table_filters]").hide(300);
    });
    
    
    
    function devolverEstado(estadoA){
        var clase = "";
        var estado = "";
        switch (estadoA) {
            case "1" :
                clase = "label label-success arrowed";
                estado = lang.pasado_alumno + "&nbsp";
                break;
        }
        imgTag = '<span class="' + clase + '">' + estado + '</span>';
        return imgTag;
    }
    
    $(aoColumnDefs).each(function(){
        thead.push(this.sTitle);
    });
    
    var _html = generarBotonSuperiorMenu(menu.superior, "btn-primary", "icon-group");
    $(_html).css("margin-left", "60px");
    $(".dataTables_length").append(_html);
    $('#areaTablas').on('mouseup','#academicoAspirantes tbody tr',function(e){
        var sData = oTable.fnGetData(this);
        if(e.button === 2){
            codigo = sData[0];
//            console.log(sData[0]);
            generalContextMenu(menu.contextual,e);
        }
    });

    $('body').on('click','#menu a',function(){
        var accion=$(this).attr('accion');
        $('#menu').remove();
        switch(accion){
            case 'modificar_aspirante':
                $.ajax({
                    url: BASE_URL + 'aspirantes/form_aspirante',
                    type: 'POST',
                    data: {
                        codigo: codigo
                    },
                    cache: false,
                    success:function(respuesta){
                        $.fancybox.open(respuesta, {
                            width: 'auto',
                            height: '600px',
                            scrolling: 'auto',
                            autoSize: false,
                            padding: 1,
                            openEffect: 'none',
                            closeEffect: 'none',
                            helpers:  {
                                overlay : null
                            }
                        });
                    }
                });
                break;

            case 'presupuestar-aspirante':
                $.ajax({
                    url: BASE_URL + 'aspirantes/presupuestar_aspirante',
                    type: 'POST',
                    data: {
                        codigo: codigo
                    },
                    cache: false,
                    success: function(respuesta){
                        $.fancybox.open(respuesta, {
                            width: '60%',
                            height: 'auto',
                            scrolling: 'auto',
                            autoSize: false,
                            padding: 1,
                            openEffect: 'none',
                            closeEffect: 'none',
                            helpers: {
                                overlay : null
                            }
                        });
                    }
                });
                break;

            case 'ver_presupuesto_aspirante':
                $.ajax({
                    url: BASE_URL + 'aspirantes/ver_presupuestos',
                    type: 'POST',
                    data:{
                        codigo_aspirante: codigo
                    },
                    cache:false,
                    success:function(respuesta){
                        $.fancybox.open(respuesta, {
                            scrolling: 'auto',
                            height:'auto',
                            padding: 0,
                            openEffect:'none',
                            closeEffect:'none',
                            helpers:  {
                                overlay : null
                            }
                        });
                    }
                });
                break;

            default:
                window.location.href = BASE_URL + 'alumnos/index/true/' + codigo;
                break;
        }
        return false;
    });
            
    $('body').on('click','#academicoAspirantes_length .btn',function(){
        $.ajax({
            url:BASE_URL+'aspirantes/form_aspirante',
            cache:false,
            type:'POST',
            data: {
                codigo: "-1"
            },
            success:function(respuesta){
                $.fancybox.open(respuesta,{
                    scrolling: 'auto',
                    width: 'auto',
                    height: 'auto',
                    autoSize: false,
                    padding: 1,
                    openEffect: 'none',
                    closeEffect:'none',
                    helpers:  {
                        overlay : null
                    }
                });
            }
        });
        return false;
    });

    $('#academicoAspirantes').on('click','.btn-detalle',function(){
        boton = $(this);
        if ($(boton).parent().find('.popover').is(':visible')) {
            $('.btn-detalle').popover('hide');
        } else {
            var usuario = $(boton).attr('data-usuario');
            var estado = $(boton).attr('data-estado');
            var tabla = '<table class="table table-striped table-bordered table-hover">';
            tabla += "<tr>";
            tabla += "<th>";
            tabla += lang.usuario;
            tabla += "</th>";
            tabla += "<th>";
            tabla += lang.estado;
            tabla += "</th>";
            tabla += "</tr>";
            tabla += '<tr>';
            tabla += '<td>';
            tabla += usuario;
            tabla += '</td>';
            tabla += '<td>';
            tabla += estado;
            tabla += '</td>';
            tabla += '</tr>';
            tabla += "</table>";
            $(boton).attr('data-content', tabla);
            $('.btn-detalle').not(boton).popover('hide');
            $(boton).popover('show');
        }
    });
}

$(document).ready(function() {
    init();
});

function listar(){
    oTable.fnDraw();
}

function exportar_informe(tipo_reporte){
    var iSortCol_0 = oTable.fnSettings().aaSorting[0][0];
    var sSortDir_0 = oTable.fnSettings().aaSorting[0][1];
    var iDisplayLength = oTable.fnSettings()._iDisplayLength;
    var iDisplayStart = oTable.fnSettings()._iDisplayStart;
    var sSearch = $("#academicoAspirantes_filter").find("input[type=search]").val();
    var tipo_contacto = $("[name=filtro_tipo_contacto]").val();
    var medio = $("[name=filtro_medio]").val();
    var curso_interes = $("[name=filtro_curso_interes]").val();
    var turno = $("[name=filtro_turno]").val();
    var es_alumno = $("[name=filtro_es_alumno]").val();
    var fecha_desde = $("[name=filtro_fecha_desde]").val();
    var fecha_hasta = $("[name=filtro_fecha_hasta]").val();
    $("[name=frm_exportar]").find("[name=iSortCol_0]").val(iSortCol_0);
    $("[name=frm_exportar]").find("[name=sSortDir_0]").val(sSortDir_0);
    $("[name=frm_exportar]").find("[name=iDisplayLength]").val(iDisplayLength);
    $("[name=frm_exportar]").find("[name=iDisplayStart]").val(iDisplayStart);
    $("[name=frm_exportar]").find("[name=sSearch]").val(sSearch);
    $("[name=frm_exportar]").find("[name=tipo_contacto]").val(tipo_contacto);
    $("[name=frm_exportar]").find("[name=medio]").val(medio);
    $("[name=frm_exportar]").find("[name=curso_interes]").val(curso_interes);
    $("[name=frm_exportar]").find("[name=turno]").val(turno);
    $("[name=frm_exportar]").find("[name=es_alumno]").val(es_alumno);
    $("[name=frm_exportar]").find("[name=fecha_desde]").val(fecha_desde);
    $("[name=frm_exportar]").find("[name=fecha_hasta]").val(fecha_hasta);
    $("[name=frm_exportar]").find("[name=tipo_reporte]").val(tipo_reporte);
    $("[name=frm_exportar]").submit();
}