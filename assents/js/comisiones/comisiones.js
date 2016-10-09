var  aoColumnDefs = columns;
var id_filtro_actual = 1;
var thead=[];   
var data='';
var codigo='';
var lang = BASE_LANG;
var menu = menuJson;
var filtros= {"codigo":"Codigo","nombre":"Nombre","curso":"Curso","cant_inscriptos":"Cant. inscriptos","capacidad":"Capacidad", "estado":"Estado"};

$(document).ready(function(){
    init();
    $("#agregar_filtro_busqueda_usuario").on("click", function(){
        agregarRegistro();
        
    });
    
    $("#areaTablas").on("click", "[name=remove_filtro_avanzado]", function(){
        id_filtro_actual --;
        console.log("cantidad de cajas de busqueda"+id_filtro_actual+"  valor a borrar  "+$("[name=filtro_avanzado_usuario].filter_" + $(this).attr("id")).val());
        //$("[name=filtro_avanzado_usuario].filter_" + $(this).attr("id")).remove();
        //esto parece no producir efecto, ver->
        $("[name=filtro_avanzado_usuario].filter_" + $(this).attr("id")).val('-1');
        $("[name=filtro_avanzado_condicion].filter_" + $(this).attr("id")).val('-1');
        $("#"+$(this).attr("id")+" [name=filtro_avanzado_usuario_valor]").val("");
        $("[name=filtro_avanzado_usuario].filter_" + $(this).attr("id")).css("display","none");
        // <-esto parece no producir efecto, ver
        var id = $(this).attr("id");
        $('.tags span[data_group = "filtro_avanzado"]').each(function(k, valor){
            if(id == $(this).attr("id")){
                $(valor).remove();
                //getTable();
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
    
    //FUNCION QUE TOMA LOS CLICK EN EL MENU FIJO EN LA CABEZERA DE LA TABLA:
    $('#top_buttons').on('click','.dataTables_buttons button',function(){                 
    //$('.dataTables_buttons button').on('click',function(){
        var accion = $(this).attr('accion');             
        switch(accion){                  
            case 'nuevaComision':                        
                $.ajax({
                    url: BASE_URL + 'comisiones/frm_comisiones',
                    data: 'cod_comision=-1',
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
                break;  
            
            case 'comisiones_cambios_periodo':
                comisiones_cambios_periodo(-1);
                break;
                
            default:   
                break;
        }
        return false;
    });
    
    // CAPTURA DEL EVENTO CLICK DERECHO:  
    var desactivado  = "";
    $('#areaTablas').on('mousedown','#academicoComisiones tbody tr',function(e){              
        var sData= oTable.fnGetData(this);       
        if( e.button === 2 ) { 
            generalContextMenu(menu.contextual,e);                  
            desactivado = sData[6];
            codigo = sData[0];                   
            if(desactivado == "inhabilitado"){                        
                $('a[accion="cambiarEstado_comisiones"]').text(lang.HABILITAR);
                $('a[accion="cambiarEstado_comisiones"]').closest("li").show();
            } else if (desactivado == "habilitado"){                          
                $('a[accion="cambiarEstado_comisiones"]').text(lang.INHABILITAR);
                $('a[accion="cambiarEstado_comisiones"]').closest("li").show();
            } else {
                $('a[accion="cambiarEstado_comisiones"]').closest("li").hide();
            }
            if (desactivado == "a_pasar" ||desactivado == 'habilitado'){
                $('a[accion="cambio_comision"]').closest("li").show();
                $('a[accion="false"]').closest("li").show();
            } else {
                $('a[accion="cambio_comision"]').closest("li").hide();
                $('a[accion="false"]').closest("li").hide();
            }
            return false; 
        }     
    }); 
});
  
function init(){    
    oTable =$('#academicoComisiones').dataTable({
        bProcessing: false,
        bServerSide: true,
        sAjaxSource: BASE_URL + 'comisiones/listar',
        sServerMethod: "POST",
        aoColumnDefs:aoColumnDefs ,
        aaSorting: [[ 0, "desc" ]],
        fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull){
            var tiene_salon = aData[4];
            var mostrar_tiene_salon = '';
            var baja = aData[6];
            var imgTag = "";
            if(tiene_salon == 'sin_salon'){
                mostrar_tiene_salon = lang.sin_salon;
                mostrar_tiene_salon += ' ';
                mostrar_tiene_salon +='<i class="icon-ok icon-info-sign sin_horarios" title="'+lang.cargue_horarios+'"></i>';
            } else {
                mostrar_tiene_salon = tiene_salon;
            }
            imgTag = devolverEstado(baja);
            $('td:eq(4)',nRow).html(mostrar_tiene_salon);
            $('td:eq(5)', nRow).html(imgTag);
            $(nRow).find('i').tooltip({
                show: null,
                position: {
                    my: "left top",
                    at: "left bottom"
                },
                open: function( event, ui ) {
                    ui.tooltip.animate({ top: ui.tooltip.position().top + 10 }, "fast" );
                }
            });
            return nRow;
        },
        // modificacion franco ticket 5149-> se agregan los campos para enviar los filtros al comisiones/listar
        fnServerData: function(sSource, aData, fnCallback){
            var campo = "";
            //var condiciones = {"codigo":"","nombre":"","curso":"","cant_inscriptos":"","capacidad":""};
            var cond_nombre = "";
            var cond_codigo = "";
            var cond_curso = "";
            var cond_cant_inscriptos = "";
            var cond_capacidad = "";
            var cond_estado = "";
            var codigo = "";
            var nombre = "";
            var curso = "";
            var cant_inscriptos = "";
            var capacidad = "";
            var estado = "";
            for(i = 0; i < id_filtro_actual; i++){
                campo = $("#"+i+" [name=filtro_avanzado_usuario_campo]").val();
                switch(campo){
                    case "codigo":
                        codigo = $("#"+i+" [name=filtro_avanzado_usuario_valor]").val();
                        cond_codigo = $("#"+i+" [name=filtro_avanzado_usuario_condicion]").val();
                    break;
                    case "nombre":
                        nombre = $("#"+i+" [name=filtro_avanzado_usuario_valor]").val();
                        cond_nombre = $("#"+i+" [name=filtro_avanzado_usuario_condicion]").val();
                    break;
                    case "curso":
                        curso = $("#"+i+" [name=filtro_avanzado_usuario_valor]").val();
                        cond_curso = $("#"+i+" [name=filtro_avanzado_usuario_condicion]").val();
                    break;
                    case "cant_inscriptos":
                        cant_inscriptos = $("#"+i+" [name=filtro_avanzado_usuario_valor]").val();
                        cond_cant_inscriptos = $("#"+i+" [name=filtro_avanzado_usuario_condicion]").val();
                    break;
                    case "capacidad":
                        capacidad = $("#"+i+" [name=filtro_avanzado_usuario_valor]").val();
                        cond_capacidad = $("#"+i+" [name=filtro_avanzado_usuario_condicion]").val();
                    break;
                    case "estado":
                        estado = $("#"+i+" [name=filtro_avanzado_usuario_valor]").val();
                        cond_estado = $("#"+i+" [name=filtro_avanzado_usuario_condicion]").val();
                    break;
                }
            }
            //console.log(campo+'    capac::   '+capacidad+'     condic capac    '+cond_capacidad+'      codigo   '+codigo);
            aData.push({name: 'condiciones_cod', value: cond_codigo});
            aData.push({name: 'condiciones_nom', value: cond_nombre});
            aData.push({name: 'condiciones_cur', value: cond_curso});
            aData.push({name: 'condiciones_cant_ins', value: cond_cant_inscriptos});
            aData.push({name: 'condiciones_capac', value: cond_capacidad});
            aData.push({name: 'condiciones_est', value: cond_estado});
            aData.push({name: 'codigo', value: codigo});
            aData.push({name: 'nombre', value: nombre});
            aData.push({name: 'curso', value: curso});
            aData.push({name: 'cant_inscriptos', value: cant_inscriptos});
            aData.push({name: "capacidad", value: capacidad});
            aData.push({name: "estado", value: estado});
            $.ajax({
                dataType: 'json',
                type: "POST",
                url: sSource,
                data: aData,
                async: true,
                success: fnCallback
            });
        }
        
        // <-modificacion franco ticket 5149
    });
    marcarTr();
    
    $('#academicoComisiones').wrap('<div class="table-responsive"></div>');   
    
    $(aoColumnDefs).each(function(){
        thead.push(this.sTitle);
    });     
            
    // modificacion ticket 5149 ->
    
    $("#academicoComisiones_filter").find("label").addClass("input-icon input-icon-right");
    $("#academicoComisiones_filter").find("label").append('<i name="icon_filters" class="icon-caret-down grey" style="margin-right: 7px; cursor: pointer"></i>');
    $("#academicoComisiones_filter").append($("[name=container_menu_filters_temp]").html());
    $(".date-picker").datepicker();
    $(".select_chosen").chosen();
    $("[name=container_menu_filters_temp]").remove();
    $("[name=div_table_filters]").hide();

    $("#academicoComisiones_filter").append('<i id="exportar_informe" class="icon-print grey" onclick="exportar_informe(\'pdf\');" style="cursor: pointer; margin-left: 6px;" data-original-title="" title="exportar"></i>');
    $("#academicoComisiones_filter").append('<i id="exportar_informe" class="icon-external-link" onclick="exportar_informe(\'csv\');" style="cursor: pointer; margin-left: 6px;" data-original-title="" title="exportar"></i>');
        
    $("[name=icon_filters]").on("click", function() {
        $("[name=contenedorPrincipal]").toggle();
        $("[name=div_table_filters]").toggle(300);
        return false;
    });
    $("[name=contenedorPrincipal]").on("mousedown", function() {
        $("[name=contenedorPrincipal]").hide();
        $("[name=div_table_filters]").hide(300);
    });
    
    $("[name=filtro_avanzado_usuario_campo]").on("change", function(){
        var nom_row = $(this).parent().parent().attr('class');
        num_row = nom_row.substr(11);//es el numero
        var nom_row = nom_row.substr(0,11);//es el nombre del row sin el numero
        var num_row = parseInt(num_row);
        var nombreCampo = $(this).val();
        $.ajax({
                type: "POST",
                url: BASE_URL + 'comisiones/condicionesfiltro',
                data: {campo:$(this).val()},
                dataType: 'json',
                cache: false,
                success: function(respuesta){
                    console.log('nombre de columna '+nom_row+'  numero de columna: '+num_row+'         HOLAAAAAA       '+respuesta.a.id);
                    $("#"+num_row+" [name=filtro_avanzado_usuario_condicion]").html('');
                    if(respuesta.cant > 1){
                        $("#"+num_row+" [name=filtro_avanzado_usuario_condicion]").append('<option value="'+respuesta.a.id+'">'+respuesta.a.display+'</option>');
                        $("#"+num_row+" [name=filtro_avanzado_usuario_condicion]").append('<option value="'+respuesta.b.id+'">'+respuesta.b.display+'</option>');
                        $("#"+num_row+" [name=filtro_avanzado_usuario_condicion]").append('<option value="'+respuesta.c.id+'">'+respuesta.c.display+'</option>');
                        $("#"+num_row+" [name=filtro_avanzado_usuario_condicion]").append('<option value="'+respuesta.d.id+'">'+respuesta.d.display+'</option>');
                        $("#"+num_row+" [name=filtro_avanzado_usuario_condicion]").append('<option value="'+respuesta.e.id+'">'+respuesta.e.display+'</option>');
                    }   else{
                        $("#"+num_row+" [name=filtro_avanzado_usuario_condicion]").append('<option value="'+respuesta.a.id+'">'+respuesta.a.display+'</option>');
                    }
                    $("#"+num_row+" [name=filtro_avanzado_div_valores]").html('');
                    if(respuesta.a.id != "-1") {
                        if(nombreCampo === "estado") {
                            $("#" + num_row + " [name=filtro_avanzado_div_valores]").append('<select name="filtro_avanzado_usuario_valor">' +'<option value="habilitado" class="filter_' + num_row + '_' + num_row + '">'+lang.HABILITADA+'</option><option value="inhabilitado" class="filter_' + num_row + '_' + num_row + '">'+lang.INHABILITADA+'</option><option value="desuso" class="filter_' + num_row + '_' + num_row + '">'+lang.desuso+'</option><option value="a_pasar" class="filter_' + num_row + '_' + num_row + '">'+lang.a_pasar+'</option></select>');
                        }else{
                            $("#" + num_row + " [name=filtro_avanzado_div_valores]").append('<input type="text" name="filtro_avanzado_usuario_valor" class="filter_' + num_row + '_' + num_row + '" value="">');
                        }
                    }
                }
            });
    });
}   
     //<- modificacion ticket 5149
    
    function devolverEstado(baja){
        if (baja === "habilitado") {           
            clase = "label label-success arrowed";
            estado = lang.HABILITADA + "&nbsp";
        } else if (baja == "inhabilitado"){                
            clase = "label label-default arrowed";
            estado = lang.INHABILITADA;
        } else if (baja == "desuso"){
            clase = "label label-default arrowed";
            estado = lang.desuso;
        } else {
            clase = "label label-danger arrowed";
            estado = lang.a_pasar;
        }
        imgTag = '<span class="' + clase + '">' + estado + '</span>';
        return imgTag;
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
    var _html = '';
    var complemento = menu.superior[0].habilitado == '1' ? '' : 'disabled="true"';
    _html += '<div class="btn-group">';
    _html += '<button class="btn btn-primary boton-primario" accion="' + menu.superior[0].accion + '" ' + complemento + '>';
    _html += '<i class="icon-comision"></i>';
    _html += menu.superior[0].text;
    _html += '</button>';
    var complemento = menu.superior[1].habilitado == '1' ? '' : 'disabled="true"';
    _html += '<button class="btn btn-primary boton-primario" accion="' + menu.superior[1].accion + '" ' + complemento + ' style="padding-left: 6px; margin-left: 24px;">';
    _html += '<i class="icon-arrow-right icon-on-right" style="margin-right: 4px;"></i>';
    _html += menu.superior[1].text;
    _html += '</button>';
    _html += '</div>';
    $(".dataTables_length").html(_html);
    
    

             
    //FUNCION QUE TOMA CLICK EN EL MENU DESPLEGABLE:
    $('body').on('click','#menu a',function(){
        var accion=$(this).attr('accion');                 
        $('#menu').remove();                 
        switch(accion){                     
            case 'cambiarEstado_comisiones':
                $.ajax({
                    url: BASE_URL + 'comisiones/cambiarEstado',
                    data: {
                        codigo_comision: codigo
                    },
                    type: 'POST',
                    dataType: 'json',
                    cache: false,
                    success: function(respuesta){
                        if (respuesta.error && respuesta.error == 'cambiar_comision'){
                             comisiones_cambios_periodo(codigo);
                        } else if (respuesta.error && respuesta.error == 'falta_fecha_desde_baja'){
                            comisiones_agregar_fecha_desde(codigo);
                        } else {
                            var texto = "";
                            if(desactivado === "inhabilitado"){
                                texto = lang.COMISION_HABILITADA;                                     
                            } else {
                                texto = lang.COMISION_INHABILITADA;                                     
                            }
                            $.gritter.add({
                                title: lang.BIEN,             
                                text: texto ,
                                sticky: false,
                                time: '3000',
                                class_name: 'gritter-success'
                            });      
                        }
                    }
                 });
                 oTable.fnDraw();
                 break;
                    
            case 'asignarPlanes':                        
                $.ajax({
                    url: BASE_URL + 'comisiones/frm_asignarPlanes',
                    data:'cod_comision='+codigo,
                    type:'POST',
                    async:false,
                    cache:false,
                    success:function(respuesta){
                        $.fancybox.open(respuesta,{
                            height: 'auto',
                            width: '800',
                            scrolling: 'auto',
                            autoSize: false,
                            padding: 1,
                            openEffect: 'none',
                            closeEffect: 'none',
                            helpers: {
                                overlay: null
                            }                                                                      
                       });                              
                   }                         
               });
               break;
                        
            case 'modificar-comision':                        
                $.ajax({
                    url: BASE_URL +'comisiones/frm_comisiones',
                    data:'cod_comision='+codigo,
                    type:'POST',
                    cache:false,
                    success:function(respuesta){
                        $.fancybox.open(respuesta,{
                            scrolling       :false,
                            width   	: '50%',
                            height      	: 'auto',
                            autoSize	: false,
                            padding         : 0, 
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
                break;
                        
            case 'enviar_comunicado':                            
               $.ajax({
                   url:BASE_URL+'comisiones/frm_comunicadoEmail',
                   type: "POST",
                   data: "cod_comision="+codigo,
                   dataType:"",
                   cache:false,
                   success:function(respuesta){                                        
                       $.fancybox.open(respuesta,{
                           scrolling       :'auto',
                           width   	: '70%',
                           height      	: 'auto',
                           autoSize	: false,
                           padding         : 0,
                           openEffect      :'none',
                           closeEffect     :'none',
                           helpers:  {
                               overlay :null
                           },
                           beforeClose: function() {
                               oTable.fnDraw();
                           }
                       });                                        
                   }
               });                            
               break;
               
            case 'cambio_comision':
                comisiones_cambios(codigo);
                break;
               
        }                 
        return false;
    });

       


function comisiones_cambios_periodo(cod_comision){
    $.ajax({
        url: BASE_URL + 'comisiones/cambios_periodos',
        type: 'POST',
        cache: false,
        data: {
            cod_comision: cod_comision
        },
        success: function(respuesta){
            $.fancybox.open(respuesta,{
                scrolling: "auto",
                width: '50%',
                height: 'auto',
                autoSize: false,
                padding: 0,
                openEffect: 'none',
                closeEffect: 'none',
                helpers: {
                    overlay: null
                },
                beforeClose: function(){
                    oTable.fnDraw();
                }                
            });
        }
    });
}
//Ticket 4581 -mmori- cambio de comision inicio
function comisiones_cambios(cod_comision){
    $.ajax({
        url: BASE_URL + 'comisiones/cambios_comision',
        type: 'POST',
        cache: false,
        data: {
            cod_comision: cod_comision
        },
        success: function(respuesta){
            $.fancybox.open(respuesta,{
                scrolling: "auto",
                width: '50%',
                height: 'auto',
                autoSize: false,
                padding: 0,
                openEffect: 'none',
                closeEffect: 'none',
                helpers: {
                    overlay: null
                },
                beforeClose: function(){
                    oTable.fnDraw();
                } 
            });
        }
    });
}
//Ticket 4581 -mmori- cambio de comision fin
function comisiones_agregar_fecha_desde(codigo){
    $("[name=div_agregar_horario]").find("[name=codigo_comision_baja]").val(codigo);
    var _html = $("[name=div_agregar_horario]").html();
    $.fancybox.open(_html, {
        scrolling: "auto",
        width: '50%',
        height: 'auto',
        autoSize: false,
        padding: 0,
        openEffect: 'none',
        closeEffect: 'none',
        helpers: {
            overlay: null
        },
        beforeClose: function(){
            oTable.fnDraw();
        },
        beforeShow: function(){
            $('.date-picker').datepicker({autoclose: true, changeMonth: false,
                changeYear: false,
                dateFormat: 'dd/mm/yy'}).next().on(ace.click_event, function() {
                $(this).prev().focus();
            });
        }
    });
}

function bajaComision(){
    var codigo_comision = $(".fancybox-wrap").find("[name=codigo_comision_baja]").val();
    var fecha_desde = $(".fancybox-wrap").find("[name=fechaDesde]").val();
    $.ajax({
        url: BASE_URL + 'comisiones/cambiarEstado',
        type: 'POST',
        dataType: 'json',
        data: {
            codigo_comision: codigo_comision,
            fecha_desde: fecha_desde
        },
        success: function(_json){
            if (_json.error){
                gritter(_json.error);
            } else {
                var texto = '';
                $.gritter.add({
                    title: lang.BIEN,
                    text: texto ,
                    sticky: false,
                    time: '3000',
                    class_name: 'gritter-success'
                });
                $.fancybox.close();
            }
        }
    });
}

function listar(){
    oTable.fnDraw();
}

function exportar_informe(tipo_reporte){
        var iSortCol_0 = oTable.fnSettings().aaSorting[0][0];
        var sSortDir_0 = oTable.fnSettings().aaSorting[0][1];
        var iDisplayLength = oTable.fnSettings()._iDisplayLength;
        var iDisplayStart = oTable.fnSettings()._iDisplayStart;
        var sSearch = $("#academicoComisiones_filter").find("input[type=search]").val();
        var codigo = $("[name=filtro_codigo]").val();
        var nombre = $("[name=filtro_nombre]").val();
        var curso = $("[name=filtro_curso]").val();
        var cant_inscriptos = $("[name=filtro_cant_inscriptos]").val();
        var capacidad = $("[name=filtro_capacidad]").val();
        var estado = $("[name=filtro_estado]").val();
        $("[name=frm_exportar]").find("[name=iSortCol_0]").val(iSortCol_0);
        $("[name=frm_exportar]").find("[name=sSortDir_0]").val(sSortDir_0);
        $("[name=frm_exportar]").find("[name=iDisplayLength]").val(iDisplayLength);
        $("[name=frm_exportar]").find("[name=iDisplayStart]").val(iDisplayStart);
        $("[name=frm_exportar]").find("[name=sSearch]").val(sSearch);        
        $("[name=frm_exportar]").find("[name=filtro_codigo]").val(codigo);
        $("[name=frm_exportar]").find("[name=nombre]").val(nombre);
        $("[name=frm_exportar]").find("[name=curso]").val(curso); 
        $("[name=frm_exportar]").find("[name=cant_inscriptos]").val(cant_inscriptos);
        $("[name=frm_exportar]").find("[name=capacidad]").val(capacidad);
        $("[name=frm_exportar]").find("[name=estado]").val(capacidad);
        $("[name=frm_exportar]").find("[name=tipo_reporte]").val(tipo_reporte);
        $("[name=frm_exportar]").submit();
    }