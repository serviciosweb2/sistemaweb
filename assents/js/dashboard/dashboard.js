var lang=''; 
var ultimaConsulta=''; 
var ultimoComunicado='';    
        
function guardarTarea(elemento){
    var usuarios = $("[name='usuarios_asignados[]']").val();            
    var nombre_tarea = $("#frmTareas").find("[name=respuesta]").val();
    var mensaje = '';
    if (nombre_tarea == '') mensaje += lang.nombre_de_la_tarea_es_requerido + '<br>';
    if (!usuarios) mensaje += lang.debe_indicar_algun_usuario + "<br>";            
    if (mensaje != ''){
        gritter(mensaje);
    } else {                
        $.ajax({
            url: BASE_URL+"usuarios/guardarTareaUsuario",
            type: "POST",
            data: $(elemento).serialize(),
            dataType:"JSON",
            cache:false,
            success:function(respuesta){                            
                if(respuesta.codigo == 1){
                    $.gritter.add({
                        title: lang.ok,
                        text: lang.validacion_ok,
                        sticky: false,
                        time: '3000',
                        class_name:'gritter-success'
                    });
                    $('[name="usuarios_asignados[]"] option').prop('selected', false).trigger('chosen:updated');
                    listadoTareas('noconcretadas');
                    $('#frmTareas input[name="respuesta"]').val('');
                } else {
                    gritter(respuesta.msgerror);
                }
            }
        });
    }
    return false;            
}

function listadoMailConsultas(codigo){    
    $.ajax({
        url: BASE_URL + "dashboard/getMailsConsultas",
        type: "POST",
        data: "codigo="+codigo,
        dataType:"JSON",
        cache:false,
        async:false,
        success:function(respuesta){
            var ocultar = '';
            
            if(respuesta.length!=0){                    
                $(respuesta).each(function(k, item){                
                    if(k == 0){                    
                        ultimaConsulta=item.codigo;
                    }            
                    if(!tienePermisoConsultasWeb){
                        ocultar = 'hide';
                    }
                    var tablon='<div class="itemdiv dialogdiv">';
                    tablon += '<div class="user">';
                    tablon += '<img alt="Bobs Avatar" src="' + BASE_URL + 'assents/theme/assets/avatars/avatar6.png">';
                    tablon += '</div>';
                    tablon += '<div class="body">';
                    tablon += '<div class="time">';
                    tablon += '<i class="icon-time"></i>';
                    tablon += '<span class="orange">' + moment(item.fechahora).lang(lang._idioma).calendar() + '</span>';
                    tablon += '</div>';
                    tablon += '<div class="name">';
                    tablon += '<a href="' + item.codigo + '">'+item.nombreFormateado+'</a>';
                    tablon += '</div>';                                               
                    tablon += '<div class="text">';                                                    
                    tablon += '<span class="text-muted">' + item.asunto + '</span>';
                    tablon += '</div>';
                    tablon += '<div class="tools">';
                    tablon += '<a href="' + item.codigo + '" class="btn btn-minier btn-info '+ocultar+'">';
                    tablon += '<i class="icon-only icon-share-alt"></i>';
                    tablon += '</a>';
                    tablon += '</div>';
                    tablon += '</div>';
                    tablon += '</div>';                            
                    if(codigo==''){
                        $('#Wconsultasweb .widget-main').append(tablon); 
                    } else {
                        $('#Wconsultasweb .widget-main').find('.itemdiv').eq(0).before(tablon);
                    }
                });
            }          
        }
    });
}  
  
function editarTarea(element,valor){
    var codigo = $(element).attr('data-codigo');
    if(valor == 1){
        $('[data-vista="vista' + codigo + '"]').hide();
        $('#frm'+codigo).removeClass('hide');
    } else {
        $('[data-vista="vista' + codigo + '"]').show();
        $('#frm' + codigo).addClass('hide');
    }
}
   
function cambiarEstadoTareas(element,moverA){
    var tipo=$(element).attr('type');
    var codigo='';
    switch(tipo){
        case 'checkbox':         
            codigo= $(element).val();
            moverA=  $(element).is(':checked') ? 'concretadas' : 'noconcretadas';
        break;
         
        default:
            codigo= $(element).attr('data-codigo');
    }
    $.ajax({
        url: BASE_URL+"usuarios/cambiarEstadoTareaUsuario",
        type: "POST",
        data: "codigo="+codigo+"&estado="+moverA,
        dataType:"JSON",
        cache:false,
        success:function(respuesta){            
            if(respuesta.codigo==1){                
                $.gritter.add({
                    title: lang.ok,
                    text: lang.validacion_ok,
                    sticky: false,
                    time: '3000',
                    class_name:'gritter-success'
                });
            }   
            listadoTareas('concretadas');
            listadoTareas('noconcretadas');
            listadoTareas('eliminadas');
        }
    });
 }
 
function getElementTarea(item,estado){     
    var checked ='';
    switch(estado){
        case 'concretadas':
            checked='checked';
            break;

        case 'eliminadas':
            checked='disabled';
            break;        
    }     
    var element = '<li class="item-default clearfix">';
    element += '<label class="inline">';
    element += '<input type="checkbox" class="ace" value="'+item.codigo+'"  '+checked+' onclick="cambiarEstadoTareas(this,\'\');">';
    element += '<span class="lbl" data-vista="vista'+item.codigo+'">'+item.nombre+'</span>';
    element += '<form class="hide frm" id="frm'+item.codigo+'" onsubmit="return guardarTarea(this);"><div class="input-group"><input type="hidden" value="'+item.codigo+'" name="codigo"><input  name="respuesta" class="form-control" value="'+item.nombre+'"><span class="input-group-btn"><button class="btn btn-success btn-sm" type="submit"><i class="icon-ok"></i></button><button class="btn btn-danger btn-sm" type="button" data-codigo="'+item.codigo+'" onclick="editarTarea(this,\'0\')"><i class="icon-reply"></i></button></span></div></form>';
    element += '</label>';                                    
    if(estado == 'noconcretadas'){                                        
        element += '<div class=" pull-right position-relative dropdown-hover" data-vista="vista' + item.codigo+'">';
        element += '<button class="btn btn-minier bigger btn-primary">';
        element += '<i class="icon-cog icon-only bigger-120"></i>';
        element += '</button>';
        element += '<ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-caret dropdown-close pull-right">';
        element += '<li>';
        element += '<a href="javascript:void(0)" data-codigo="' + item.codigo + '" class="tooltip-success" data-rel="tooltip" title="" data-original-title="Mark&nbsp;as&nbsp;done" onclick="editarTarea(this,\'1\')">';
        element += '<span class="green">';
        element += '<i class="icon-pencil bigger-110"></i>';
        element += '</span>';
        element += '</a>';
        element += '</li>';
        element += '<li>';
        element += '<a href="javascript:void(0)" data-codigo="' + item.codigo + '" class="tooltip-error" data-rel="tooltip" title="" data-original-title="Delete" onclick="cambiarEstadoTareas(this,\'eliminadas\')">';
        element += '<span class="red">';
        element += '<i class="icon-trash bigger-110"></i>';
        element += '</span>';
        element += '</a>';
        element += '</li>';
        element += '</ul>';
        element += '</div>';
    }                           
    if(estado == 'concretadas'){                                        
        element += '<div class=" pull-right position-relative dropdown-hover" data-vista="vista' + item.codigo + '">';
        element += '<button class="btn btn-minier bigger btn-primary">';
        element += '<i class="icon-cog icon-only bigger-120"></i>';
        element += '</button>';
        element += '<ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-caret dropdown-close pull-right">';
        element += '<li>';
        element += '<a href="javascript:void(0)" data-codigo="' + item.codigo + '" class="tooltip-error" data-rel="tooltip" title="" data-original-title="Delete" onclick="cambiarEstadoTareas(this,\'eliminadas\')">';
        element += '<span class="red">';
        element += '<i class="icon-trash bigger-110"></i>';
        element += '</span>';
        element += '</a>';
        element += '</li>';
        element += '</ul>';
        element += '</div>';
    }                      
    element += '</li>';
    return element;     
 }
 
function listadoComunicados(codigo){
    $.ajax({
        url: BASE_URL + "dashboard/getComunicadosFilial",
        type: "POST",
        data: "codigo="+codigo,
        dataType: "JSON",
        cache: false,
        async: false,
        success:function(respuesta){           
            if(respuesta.length!=0){
                $(respuesta).each(function(k,item){
                    var comu = '';
                    comu += '<div class="itemdiv">';
                    if(item.url !== null){
                        comu += '<div class="col-md-3" style="float:left; left: 0;">';
                        comu += '<a href="#" onclick="event.preventDefault(); abrir_imagen(\''+item.url+'\');">';
                        comu += '<img alt= "Bobs Avatar" src = "'+item.url+'" width="100%">';
                        comu += '</a>';
                        comu += '</div>';
                        comu += '<div class="col-md-9">';
                    }
                    else {
                        comu += '<div class="col-md-12">';
                    }
                    comu += '<div class="body">';
                    comu += '<div class="time" style="float:right"><i class="icon-time"></i><span class="orange">'+item.fecha+'</span></div>';
                    comu += '<div class="name" style="font-size:28px"><span>'+item.titulo+'</span></div>';
                    comu += '<blockquote>';
                    comu += '<p>'+item.mensaje+'</p>';
                    comu += '</blockquote>';
                    comu += '</div>';
                    comu += '</div>';
                    comu += '</div>';
                    comu += '<div style="clear:both; padding-top:5px"> <hr> </div>';
                    $('#Wcomunicados .widget-main').append(comu);
                });
            }    
        }
    });
}
 
function listadoTareas(estado){     
    $.ajax({
        url: BASE_URL+"usuarios/getTareasUsuario",
        type: "POST",
        data: "estado="+estado,
        dataType:"JSON",
        cache:false,
        async:false,
        success:function(respuesta){                
            var listaTareas='';                
            $(respuesta).each(function(k,item){                    
                listaTareas+=getElementTarea(item,estado);                    
            });                
            if(respuesta.length>0){                    
                $('#'+estado).html('<ul class="item-list ui-sortable">'+listaTareas+'</ul>');                    
            } else {                    
                var msj='<div class="row fotoMSJ">'
                msj+='<div class="col-md-12 col-xs-12 text-center"><img src="'+BASE_URL+'assents/img/dashboard/icono_tareas.png"></div>'
                msj+='</div>';                    
                msj+='<div class="row"><div class="col-md-12 col-xs-12 text-center textoMSJ">'+lang.no_tiene_tareas_pendientes+'</div></div>'
                $('#'+estado).html(msj);                    
            }
        }
    });
    $('#Wtareas').disableSelection();
    $('#Wtareas input:checkbox').each(function(){
        if(this.checked){
            $(this).closest('li').addClass('selected');
        } else {
            $(this).closest('li').removeClass('selected');
        }                
    });
 }
    
function initFRM(){        
    $("[name='usuarios_asignados[]']").chosen({width:'100%'});        
    $('#Wsugerencias .widget-main').slimScroll({height: '300px'});
    $("#tablaSugerencias").dataTable({            
        "info": false,
        "lengthChange": false,
        "searching": false,
        "aoColumnDefs": columns,
        "bServerSide":true,
        "sAjaxSource": BASE_URL + 'dashboard/deudoresCtaCte',
        "sServerMethod": "POST",
        "order": [],
        "bSort": false,
//            oaColumns: [{"bSortable": false}, {"bSortable": false}, {"bSortable": false}, {"bSortable": false}, {"bSortable": false}],
        "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            var dataPOST={"cod_matricula":aData[1],'cod_plan_academio':aData[2]};
            $('td:eq(0)', nRow).html("<label><input name='cod_matricula[]' type='checkbox' value='"+aData[1]+"' class='ace' /><span class='lbl'></span></label>");
            var prioridad = aData[5];
            var _html = 0;
            switch (prioridad){
                case "alta":
                    _html = '<span class="badge badge-danger">' + lang.prioridad_alta + '</span>';
                    break;

                case "media":
                    _html = '<span class="badge badge-yellow">' + lang.prioridad_media + '</span>';
                    break;

                case "baja":
                    _html = '<span class="badge badge-success">' + lang.prioridad_baja +'</span>';
                    break;
            }
            $('td:eq(4)', nRow).html(_html);
            return nRow;
        }            
    });

    $('#tablaSugerencias').wrap('<div class="table-responsive"></div>');        
    $('body').find('.pagination').closest('.col-sm-6').removeClass('col-sm-6').addClass('col-sm-12');        
    $('#frmSugerenciaBaja').on('submit',function(){
        $.ajax({
            url: BASE_URL + "dashboard/guardarSugerenciaBaja",
            type: "POST",
            data: $(this).serialize(),
            dataType: "JSON",
            cache: false,
            success:function(respuesta){
                if(respuesta.codigo==1){
                    $.gritter.add({
                        title: lang.ok,
                        text: lang.validacion_ok,
                        sticky: false,
                        time: '3000',
                        class_name:'gritter-success'
                    });
                    $("#tablaSugerencias").dataTable().fnDraw();
                } else {
                    $.gritter.add({
                        title: lang.upps,
                        text: respuesta.msgError,
                        sticky: false,
                        time: '3000',
                        class_name:'gritter-error'
                    }); 
                }
            }
        });
        return false;
    });      

    listadoMailConsultas('');
    setInterval(function(){listadoMailConsultas(ultimaConsulta);},120000);                
    $('#Wconsultasweb .widget-main').slimScroll({height: '300px'});     
    $('#Wconsultasweb .widget-main').on('click','.name a',function(){            
        if(tienePermisoConsultasWeb){
             window.location=BASE_URL+ 'consultasweb/index/ver_consulta/' +$(this).attr('href');        
        }

        return false;
    });

    $('#Wconsultasweb .widget-main').on('click','.tools a',function(){            
        window.location=BASE_URL+ 'consultasweb/index/responder_consulta/' +$(this).attr('href');          

        return false;
    });
    $('#Wtareas .widget-main').slimScroll({height: '206px'});        
    listadoTareas('noconcretadas');
    $('#frmTareas').on('submit',function(){
        guardarTarea(this);
        return false;
    });

    $('.fancybox-wrap select').chosen({
        width:'100%',
        allow_single_deselect: true
    });

    $('#Wcomunicados .widget-main').slimScroll({height: '300px'});
    listadoComunicados('');    
//        setInterval(function(){listadoComunicados(ultimoComunicado);},120000);    
}    
    
$(document).ready(function(){    
    lang = BASE_LANG;    
    moment.lang(lang._idioma, 
    {
        calendar: {
            lastDay: '[' + lang.ayer + ']',
            sameDay: '[' + lang.hoy + ']',
            nextDay: '[' + lang.maniana + ']',
            lastWeek: '[' + lang.la_semana_pasada + '] ( dddd )',
            nextWeek: 'dddd [' + lang.a_las + '] LT',
            sameElse: 'L'
        }
    });    
    initFRM();
});

function abrir_imagen(url){
    console.log("asddsa");
    var html = '<img src="'+url+'"/>';
    $.fancybox.open(html, {
        scrolling: 'auto',
        autoSize: false,
        width: 'auto',
        height: 'auto',
        padding: 1,
        openEffect: 'none',
        closeEffect: 'none',
        helpers: {
            overlay: null
        }                                 
    });
}

function ver_comunicado(id){
    $.ajax({
        url: BASE_URL + 'comunicados/vista_comunicado',
        type: 'POST',
        data: {
            id: id
        },
        success: function(_html){
            $.fancybox.open(_html, {
                scrolling: 'auto',
                autoSize: false,
                width: 'auto',
                height: 'auto',
                padding: 1,
                openEffect: 'none',
                closeEffect: 'none',
                helpers: {
                    overlay: null
                }                                 
            });
        }
    });     
}