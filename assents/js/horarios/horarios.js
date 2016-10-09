var filtro;
var menu = '';
var langFRM = '';
var calendar = '';
var llamada_en_proceso = false;
var collapse=false;
var cod_salon = [];
var langFRM = BASE_LANG;

function imprimir_horario(){
    $("#area_impresion").print({
        globalStyles : true,
        noPrintSelector: '.no_imprimir'
    });
}

function agregarBotonImprimir(){   
    var miBotonImprimir = '<span class="fc-button fc-button-today fc-state-default fc-corner-left fc-corner-right " unselectable="on"><i id="imprimir_informe" class="icon-print grey" style="cursor: pointer" onclick="imprimir_horario();" data-original-title="" title=""></i>'; 
    var boton = '';
    boton+=miBotonImprimir;
    $('.fc-header-left').append(boton);
}

function seleccionarTodos(element){
    var checkeado = $(element).is(':checked') ? true : false;    
    if(checkeado){
        var salones_deschekeados = $('.codSalon').not(':checked');           
        $(salones_deschekeados).each(function(k,valor){
            var id_salon = $(valor).val();
            $(valor).prop('checked',true);
            $(valor).closest('label').removeClass("label_salon_disabled_"+id_salon).addClass('class_label_salon_checked_'+id_salon);
            var button_toggle_check = $('.menu-salon').find('button');
            $(button_toggle_check).each(function(k,valor){
                var valor_botton =  $(valor).attr('data-value');
                if(id_salon == valor_botton){
                    $(valor).removeClass('label_salon_disabled_'+id_salon).addClass('class_label_salon_checked_'+id_salon);
                }
            });
            var external_event = $('.external-event');
            $(external_event).each(function(j,valor_event){
                var cod_evento = $(valor_event).attr('data-value');
                if(id_salon == cod_evento){
                    $(valor_event).removeClass('label_salon_disabled_'+id_salon).removeClass('borde_salon_'+id_salon).addClass('class_label_salon_checked_'+id_salon);
                }
            });
        });
    } else {
        var salones_chekeados = $('.codSalon:checked');            
        $(salones_chekeados).each(function(key,value){                
            var salon = $(value).val();
            $(value).prop('checked',false);
            $(value).closest('label').removeClass('class_label_salon_checked_'+salon).addClass('label_salon_disabled_'+salon);
            var button_toggle_check = $('.menu-salon').find('button');
            $(button_toggle_check).each(function(k,valor){
                var valor_botton =  $(valor).attr('data-value');
                if(salon == valor_botton){
                    $(valor).removeClass('class_label_salon_checked_'+salon).addClass('label_salon_disabled_'+salon);
                }
            });
            var external_event = $('.external-event');
            $(external_event).each(function(j,valor_event){
                var cod_evento = $(valor_event).attr('data-value');
                if(salon == cod_evento){
                    $(valor_event).removeClass('class_label_salon_checked_'+salon).addClass('label_salon_disabled_'+salon).addClass('borde_salon_'+salon);
                }
            });
        });
    }
    reloadCalendarEvents();
}

function collapsarMenu(element){   
    var heightMenu = $('#menuDerecha').height();    
    var miGritter = $.gritter.add({
        text: langFRM.actualizando_grilla_horarios,
        sticky: true,
        time: '3000',
        class_name:'gritter-warning'
    });
    
    if (collapse==false){        
        $(element).closest('#menuDerecha').removeClass('col-sm-3').addClass('col-sm-1');            
        $('.ocultar').hide();       
        $('#areaTablas').removeClass('col-sm-9').addClass('col-sm-11');            
        collapse=true;
        $('#menuDerecha').css({
            height:heightMenu
        });
        $('#menuDerecha').addClass('menuSinColapsar');           
    } else {  
        $('#buscar').hide();            
        $(element).closest('#menuDerecha').removeClass('col-sm-1').addClass('col-sm-3');
        $('.ocultar').show();
        $('#areaTablas').removeClass('col-sm-11').addClass('col-sm-9'); 
        $('#menuDerecha').css({
            height:'auto',
            width:''
        });            
        collapse=false;        
    }    
    setTimeout(function(){
        $('#calendar').fullCalendar('render');        
        $.gritter.remove(miGritter, { 
            fade: true,
            speed: 'fast'
        });    
    },400);
}

function mostrarFiltros(){
    $('.filtro_horario').toggleClass('hide');    
}


$(document).ready(function(){
    $('#buscar').hide();
    init();
});

function init(){
    calendario= $('div[name="calendario-asistencia"]').datepicker({
        dateFormat: 'yy-mm-dd',            
        onSelect : function(dateText, Object){
            $('#calendar').fullCalendar( 'changeView', 'agendaDay' );
            $('#calendar').fullCalendar( 'gotoDate', dateText );
        },
        onChangeMonthYear : function(year, month,object){
            var fechaCambio = year+'-'+month+'-'+'01';            
            $('#calendar').fullCalendar( 'changeView', 'agendaWeek' );
            $('#calendar').fullCalendar( 'gotoDate', fechaCambio);
        }
    });    
        
    $('body').on('click','.fc-day-number',function(event){
        event.stopPropagation();     
    });

    $('.contenedorSalones').append('<div class="col-md-2">' + menu.superior + '</div>');  
    $('body').on('ready', '.fc-cell-overlay', function(){
        $('.fc-cell-overlay').css('background', 'red');
    });

    $('.chosen-select').chosen(); 
    $('.filtro_horario').addClass('hide');
    var idSalon = '';    
    var filtro = ({
        'profesores': [],
        'salones': [],
        'comisiones': [],
        'materias': []
    });
    
    $.ajax({
        url: BASE_URL + 'horarios/getComisiones',
        type: 'POST',
        dataType: 'json',
        cache: false,
        success: function(respuesta){
            $(respuesta).each(function(){
                $('select[name="comisiones"]').append('<option value="' + this.codigo + '">' + this.nombre + '</option>');
            });
            $('select[name="comisiones"]').trigger("chosen:updated");
        }
    });
    
    $.ajax({
        url: BASE_URL + 'horarios/getMaterias',
        type: 'POST',
        dataType: 'json',
        cache: false,
        success: function(respuesta){
            $(respuesta).each(function(){
                $('select[name="materias"]').append('<option value="' + this.codigo + '">' + this.nombre + '</option>');
            });
            $('select[name="materias"]').trigger("chosen:updated");
        }
    });
    
    $.ajax({
        url: BASE_URL + 'horarios/getProfesoresconHorario',
        type: 'POST',
        dataType: 'json',
        cache: false,
        success: function(respuesta){
            $(respuesta).each(function(){
                $('select[name="profesores"]').append('<option value="' + this.codigo + '">' + this.nombre + '</option>');
            });
            $('select[name="profesores"]').trigger("chosen:updated");
        }
    });

    $('.codSalon').click(function(){
        var codigo_salon = $(this).val();
        if ($(this).is(":checked")){
            $("[name=label_salon_" + codigo_salon + "]").removeClass("label_salon_disabled_"+codigo_salon);
            $("[name=label_salon_" + codigo_salon + "]").addClass("class_label_salon_checked_" + codigo_salon);
                 var button_toggle_check = $('.menu-salon').find('button');
              $(button_toggle_check).each(function(k,valor){
                var valor_botton =  $(valor).attr('data-value');
               if(codigo_salon == valor_botton){
                   $(valor).removeClass('label_salon_disabled_'+codigo_salon).addClass('class_label_salon_checked_'+codigo_salon);
               }
              });
              var external_event = $('.external-event');
              $(external_event).each(function(j,valor_event){
                 var cod_evento = $(valor_event).attr('data-value');
                 if(codigo_salon == cod_evento){
                     $(valor_event).removeClass('label_salon_disabled_'+codigo_salon).removeClass('borde_salon_'+codigo_salon).addClass('class_label_salon_checked_'+codigo_salon);
                 }
              });
        } else {
            $("[name=label_salon_" + codigo_salon + "]").removeClass("class_label_salon_checked_" + codigo_salon);
            $("[name=label_salon_" + codigo_salon + "]").addClass("label_salon_disabled_"+codigo_salon);
              var button_toggle_check = $('.menu-salon').find('button');
              $(button_toggle_check).each(function(k,valor){
                var valor_botton =  $(valor).attr('data-value');
               if(codigo_salon == valor_botton){
                   $(valor).removeClass('class_label_salon_checked_'+codigo_salon).addClass('label_salon_disabled_'+codigo_salon);
               }
              });
              var external_event = $('.external-event');
              $(external_event).each(function(j,valor_event){
                 var cod_evento = $(valor_event).attr('data-value');
                 if(codigo_salon == cod_evento){
                     $(valor_event).removeClass('class_label_salon_checked_'+codigo_salon).addClass('label_salon_disabled_'+codigo_salon).addClass('borde_salon_'+codigo_salon);
                 }
              });
             
        }
        reloadCalendarEvents();
    });
    
    $('select[name="comisiones"], select[name="materias"], select[name="profesores"]').change(function(){
        reloadCalendarEvents();
    });
    
    var i = 0;
    var mostrar = '';    
    var viendo = 'month';    
    var pagey = '';    
    var Editable = false;
    
    var setFancy = {
        scrolling: 'auto',
        autoSize: true,
        padding: 0,
        openEffect: 'none',
        closeEffect: 'none',
        helpers:{
            overlay: null
        },
        wrapCSS: 'fancy_custom'
    };

    function ejson(string){
        try {
            JSON.parse(string);
        } catch (e){
            return false;
        }
        return true;
    }

    function resize(){
        if (viendo != 'month'){
            $('#calendar').find('.ui-resizable-handle').show();
        } else {
            $('#calendar').find('.ui-resizable-handle').hide();
        }
    }
    
    var cantidadSalones = $(".codSalon");
    var menuRight = cantidadSalones.length > 4 ? "agendaWeek,agendaDay" : "month,agendaWeek,agendaDay";
    calendar = $('#calendar').fullCalendar({
        lang: BASE_IDIOMA,
        hiddenDays: diasHabilitados, 
        allDayDefault : false,    
        defaultView: "agendaWeek",
        cache: true,
        slotEventOverlap:false,
        selectable: {
            month: false,
            agenda: true
        },
        selectHelper: true,
        select: function(start, end, allDay){
            var fecha = moment(start).format('MM/DD/YYYY'); 
            var horaComienzo =  moment(start).format('HH:mm');            
            var horaFinal = moment(end).format('HH:mm');
            $('.fc-event').popover('hide');
            $.ajax({
                url: BASE_URL + 'horarios/frm_horario',
                data: 'codigo_horario=-1&fechaInicio=' + fecha + '&horaComienzo=' + horaComienzo + '&horaFinal=' + horaFinal,
                type: 'POST',
                cache: false,
                success: function(respuesta){
                    $.fancybox.open(respuesta, setFancy);
                    $('.fancybox-wrap').draggable();
                }
            });
        },
        header:{
            left: 'prev,next, today',
            center: 'title',
            right: menuRight
        },
        viewRender: function(view){
            viendo = view.name;
        },
        eventRender: function(event, element, view){
           if (true){ 
                if(event.tipo == "CURSADO"){
                    var horaC = moment(event.start).format('HH:mm');
                    var horaF = moment(event.end).format('HH:mm');
                    var contenido = '<button type="button" class="close" data-dismiss="alert"  >&times;</button>' +
                            '<div class="row"><div class="col-md-12"><b>'+langFRM.curso+': </b> ' + event.nombre_curso + '<br><b>'+langFRM.materia+':</b> ' + event.nombre_materia + '</div></div>' +
                            '<div class="row"><div class="col-md-12"><b>'+langFRM.comision+':</b> ' +event.nombre_comision+'<br></div></div>'+
                            '<div class="row"><div class="col-md-12"><b>'+langFRM.horario+': </b> ' + horaC + ' - ' + horaF + '</div></div>'+
                            '<div class="row"><div class="col-md-12"><b>'+langFRM.alumnos+':</b> ' +event.inscriptos_comision+' '+'<a class="accion" name="ver-alumnos" href="' + event.id + '">'+langFRM.ver_alumnos+'</a><br></div></div>';
                   
                    contenido  += '<br><div class="row"><div class="col-md-12"><div class="row"><div class="col-md-8">' +
                            '<a class=" accion" name="borrar" href="' + event.id + '">'+langFRM.eliminar+'</a>|' +
                            '</div><div class="col-md-2">' +
                            '|<a class="accion" name="modificar" href="' + event.id + '">'+langFRM.modificar+'</a></div></div></div>';
                            
                    var v = element.popover({
                        trigger: 'manual',
                        placement: 'top',
                        html: true,
                        content:contenido                        
                    });
                    $(element).click(function(e){
                        var clickAltura = e.pageY;
                        $('.fc-event').not(this).popover('hide');
                        if ($(this).next('.popover').is(':visible')){
                            $(this).popover('hide');
                        } else {
                            $(this).popover('show');
                            var position = $(this).next('.popover').position();
                            var anchoContenedor = $('.fc-view').width();
                            var margenPop = position.left;
                            var anchoPop = $(this).next('.popover').outerWidth();
                            var alturaPop = $(this).next('.popover').outerHeight();
                            var topPop = position.top;
                            var diferencia = anchoContenedor - margenPop;
                            var prueba = anchoPop - diferencia;
                            var alturaElement = $(this).outerHeight();
                            if (diferencia < anchoPop){
                                $(this).next('.popover').css({
                                    'left': '-=' + prueba
                                });
                            }
                            if (margenPop < 0){
                                $(this).next('.popover').css({
                                    'left': '0px'
                                });
                            }
                            var test = alturaPop + alturaElement;
                            if (topPop < 0){
                                $(this).next('.popover').css({
                                    'top': '+=' + test
                                });
                                $(this).next('.popover').find('.arrow').css('top', '-11px').addClass('rot180');
                            }
                        }
                    });
                } else if(event.tipo == 'FERIADO'){         
                    $(element).addClass("feriadoEvent");         
                    $(element).find('.fc-event-title').empty().html('<div class="borde">'+event.title+'</div>');
                } else if(event.tipo == 'RECESO_FILIAL'){
                    $(element).addClass("feriadoEvent");     
                    $(element).find('.fc-event-title').empty().html('<div class="borde">'+event.title+'</div>');
                }  
                $(element).find('.fc-event-time').hide();
                if (event.tipo == 'CURSADO'){
                     $(element).find('.fc-event-title').empty().html('<div class="borde">'+event.nombre_comision+'</div>'+'<div class="no-padding" title="'+event.title_tooltip+'" style="position: absolute; bottom: -13px; right: 0px; top:auto"><i style="color:'+event.color_curso_plan+'" class="icon-sort-up triangulo myTooltip"></i></div>'); 
                }
                        
                $( ".myTooltip" ).tooltip({
                    show: null,
                    position: {
                        my: "left top",
                        at: "left bottom"
                    },
                    open: function( event, ui ) {
                        ui.tooltip.animate({ top: ui.tooltip.position().top + 10 }, "fast" );
                    }
                });
            } else {
                $(element).hide();
            }
        },
        axisFormat: 'H:mm',
        minTime: menorHorario,
        maxTime: mayorHorario,

        dayClick: function(date, jsEvent, view){ 
            var divclick = $(jsEvent.target).attr("class");     
            if(divclick === "fc-day-number"){      
              	var toDate = moment(date);
                $('#calendar').fullCalendar( 'changeView', 'agendaDay' );
                $('#calendar').fullCalendar( 'gotoDate', toDate );                
            } else {
                if (!llamada_en_proceso){
                    llamada_en_proceso = true;
                    var fecha =  moment(date).format('MM/DD/YYYY');
                    var horaComienzo =   moment(date).format('HH:mm');
                    $('.fc-event').popover('hide');
                    $.ajax({
                        url: BASE_URL + 'horarios/frm_horario',
                        data: 'codigo_horario=-1&fechaInicio=' + fecha + '&horaComienzo=' + horaComienzo,
                        type: 'POST',
                        cache: false,
                        success: function(respuesta){                           
                            $.fancybox.open(respuesta, setFancy);
                            llamada_en_proceso = false;
                        }
                    });
                }
            }
        },
        editable: true,
        eventClick: function(calEvent, jsEvent, view){
            pagey = jsEvent.pageY;

        },     
        eventDurationEditable:{
            month: false,
            agenda: true
        },
        eventResize: function(event, revertFunc){
            if(event.tipo !=="FERIADO"){
                var FechaDesde =  moment(event.start).format('DD/MM/YYYY');
                var hora =  moment(event.start).format('HH:mm');
                var horaHasta = moment(event.end).format('HH:mm');
                var dataPOST = {
                    'cod_comision': event.cod_comision,
                    'codigo_horario': event.id,
                    'cod_salon': event.cod_salon,
                    'cod_profesor': event.cod_profesor,
                    'cod_materia': event.cod_materia,
                    'fechaDesde': FechaDesde,
                    'finalizacion': FechaDesde,
                    'horaDesde': hora,
                    'horaHasta': horaHasta,
                    'tipoRepeticion': ''
                };
                $.ajax({
                    url: BASE_URL + "horarios/frm_drag",
                    data: dataPOST,
                    type: 'POST',
                    cache: false,
                    success: function(respuesta){
                        if (ejson(respuesta)){
                            $.ajax({
                                url: BASE_URL + 'horarios/guardarHorario',
                                data: JSON.parse(respuesta),
                                type: 'POST',
                                dataType: 'json',
                                cache: false,
                                success: function(respuesta){
                                    switch (respuesta.codigo){
                                        case 2:
                                            var horaC = respuesta.custom.dia.split(' ');
                                            var horaF = respuesta.custom.dia2.split(' ');
                                            var msj = '' + langFRM.no_puede_modificarse + '<br>' + langFRM.superpone_con_evento + '' + horaC[0] + ''+langFRM.que_comienza+'' + horaC[1] + '' +langFRM.y_finaliza+'' + horaF[1];
                                            gritter(msj, false, langFRM.ERROR);
                                            revertFunc();
                                            break;
                                        
                                        case 0:
                                            gritter(respuesta.respuesta, false, langFRM.ERROR);
                                            break;
                                            
                                        case 3:
                                            var msj = langFRM.TIENE_ASISTENCIAS_CARGADAS;
                                            gritter(msj, false, langFRM.ERROR);
                                            revertFunc();
                                            break;
                                            
                                        case 1:
                                            gritter(langFRM.HORARIO_GUARDADO_CORRECTAMENTE, true, langFRM.BIEN);
                                            var unset=respuesta.custom.unset;
                                            $(unset).each(function(){
                                                $('#calendar').fullCalendar( 'removeEvents',this.id);   
                                            });

                                            var nuevo=respuesta.custom.nuevo;
                                            $('#calendar').fullCalendar('addEventSource',nuevo);
                                            $.fancybox.close();
                                            break;
                                    }
                                }
                            });
                        } else {                            
                            $.fancybox.open(respuesta, setFancy);
                        }
                    }
                });
            } else {
                revertFunc();
            }
        },
        slotMinutes: 15,
        eventDrop: function(event,  revertFunc){            
            if(event.tipo!== "FERIADO"){
                var FechaDesde =  moment(event.start).format('MM/DD/YYYY');
                var hora = moment(event.start).format('HH:mm');
                var horaHasta = moment(event.end).format('HH:mm');                
                var dataPOST = {
                    'cod_comision': event.cod_comision,
                    'codigo_horario': event.id,
                    'cod_salon': event.cod_salon,
                    'cod_profesor': event.cod_profesor,
                    'cod_materia': event.cod_materia,
                    'fechaDesde': FechaDesde,
                    'finalizacion': FechaDesde,
                    'horaDesde': hora,
                    'horaHasta': horaHasta,
                    'tipoRepeticion': 0
                };
                $.ajax({
                    url: BASE_URL + "horarios/guardarHorario",
                    data: dataPOST,
                    type: 'POST',
                    dataType: 'json',
                    cache: false,
                    success: function(respuesta){
                        if (respuesta.codigo == 2){
                            var horaC = respuesta.custom.dia.split(' ');
                            var horaF = respuesta.custom.dia2.split(' ');
                            var msj = ''+langFRM.no_puede_modificarse+'<br>'+langFRM.superpone_con_evento+'' + horaC[0] + ''+langFRM.que_comienza+'' + horaC[1] + '' +langFRM.y_finaliza+'' + horaF[1];
                            bootbox.alert(msj, function(){
                                revertFunc();
                            });
                        } else if(respuesta.codigo == 0){
                            gritter(respuesta.respuesta, false, langFRM.ERROR);
                            revertFunc();
                        }
                    }
                });
            } else {           
                revertFunc();
            }
        },
        eventAfterAllRender:function(){
            $('#imprimir_informe').parent().removeClass('fc-state-disabled');
            var height = $('#scrollInterno').find('div').eq(0).height();
            var largo = height + 68;
            $('.fc-view .fc-agenda-days').css('height',largo);        
            setTimeout(function(){
                $('#scrollInterno').css('height',largo);
            },5);
        }
    });   
    agregarBotonImprimir();
    if (viendo == 'month'){        
        $('#areaTablas .fc-cell-overlay').css('background', 'red');
    }    
    var codigo_horario = '';    
    $('#areaTablas').on('click', '.accion', function(){
        $('.fc-event').popover('hide');
        var tipoAccion = $(this).attr('name');
        codigo_horario = $(this).attr('href');
        var baseUrl = '';
        var dataPOST = '';        
        switch (tipoAccion){
            case  "borrar" : 
                $.ajax({
                    url: BASE_URL + 'horarios/frm_baja_horario',
                    data: 'codigo_horario=' + codigo_horario,
                    type: 'POST',
                    cache: false,
                    success: function(respuesta){
                        $.fancybox.open(respuesta, setFancy);                        
                    }
                });
                break;
                
            case "modificar":
                $.ajax({
                    url: BASE_URL + 'horarios/frm_horario',
                    data: 'codigo_horario=' + codigo_horario,
                    type: 'POST',
                    cache: false,
                    success: function(respuesta){
                        $.fancybox.open(respuesta, setFancy);
                        $('.fancybox-wrap').draggable();
                    }
                });
                break;
            
            case "ver-alumnos":
                $.ajax({
                    url: BASE_URL + 'horarios/frm_inscriptos_horario',
                    data: 'codigo_horario=' + codigo_horario,
                    type: 'POST',
                    cache: false,
                    success: function(respuesta){                
                        $.fancybox.open(respuesta,{
                            scrolling       :'auto',
                            autoSize	: false,                                        
                            width   	: '50%',
                            height      	: 'auto',
                            padding         : 1,
                            openEffect      :'none',
                            closeEffect     :'none',
                            afterClose: function(){
                                reloadCalendarEvents();
                            },
                            helpers:  {
                                overlay : null
                            }
                        });
                    }
                });
                break;
        }
        return false;
    });

    $('body').on('click', '.filtro', function(){
        var salon = $(this).attr('href');
        var eventos2 = $('#calendar').fullCalendar('clientEvents');
    });    
    
    $("#btn-feriados").click(function(){
        $.ajax({
            url: BASE_URL + 'horarios/frm_feriados',
            data: 'codigo=-1',
            type: 'POST',
            cache: false,
            success: function(respuesta){
                $.fancybox.open(respuesta, {
                    scrolling: 'auto',
                    width: '50%',
                    height: 'auto',
                    minHeight: '300',
                    maxWidth: '600',
                    autoSize: false,
                    autoResize: false,
                    openEffect: 'none',
                    closeEffect: 'none',
                    padding: 1,
                    helpers: {
                        overlay: null
                    }
                });
            }
        });
    });    

    $('#calendar').on('click', '.close', function(){
        $('.fc-event').popover('hide');
        return false;
    });

    $('.widget-box').on('click', 'a', function(){    
        var element;
        var accion = $(this).attr('accion');
        switch (accion){
            case'modificar-salon':
                $.ajax({
                    url: BASE_URL + 'horarios/frm_salones',
                    data: 'codigo=2',
                    type: 'POST',
                    cache: false,
                    success: function(respuesta){
                        $.fancybox.open(respuesta, {
                            scrolling: 'auto',
                            width: '50%',
                            height: 'auto',
                            minHeight: '300',
                            maxWidth: '600',
                            autoSize: false,
                            autoResize: false,
                            openEffect: 'none',
                            closeEffect: 'none',
                            padding: 1,
                            helpers: {
                                overlay: null
                            }
                        });
                    }
                });

            case 'modificar':
                $.ajax({
                    url: BASE_URL + 'horarios/frm_salones',
                    data: 'codigo=' + $(this).attr('salon'),
                    type: 'POST',
                    cache: false,
                    success: function(respuesta){
                        $.fancybox.open(respuesta, {
                            scrolling: 'auto',
                            width: '50%',
                            height: 'auto',
                            minHeight: '300',
                            maxWidth: '600',
                            autoSize: false,
                            autoResize: false,
                            openEffect: 'none',
                            closeEffect: 'none',
                            padding: 0,
                            helpers: {
                                overlay: null
                            }
                        });
                    }
                });
                break;
                
            case 'bajaSalon':
                $.ajax({
                    url: BASE_URL + 'horarios/cambiarEstadoSalon',
                    data: 'codigo=' + $(this).attr('salon'),
                    type: 'POST',
                    cache: false,
                    dataType: 'JSON',
                    success: function(respuesta){
                        if (respuesta.codigo == 1){
                            $.gritter.add({
                                title: langFRM.BIEN,
                                text: langFRM.FACTURA_EMITIDA_CORRECTAMENTE ,
                                sticky: false,
                                time: '3000',
                                class_name: 'gritter-success'
                            });                    
                            location.reload();
                        }
                    }
                });
                break;
        }        
        $(this).closest('.menu-salon').removeClass('open');        
        $(this).closest('.widget-header').removeClass('open');        
        return false;
    });    
    reloadCalendarEvents();    
    $('.sidebar-collapse').on('click',function(){
        var miGritter = $.gritter.add({
            text: 'Actualizando grilla de horarios...',
            sticky: true,
            time: '3000',
            class_name:'gritter-warning'
        });
        
        setTimeout(function(){
            $('#calendar').fullCalendar('render');
            $.gritter.remove(miGritter, { 
                fade: true,
                speed: 'fast'
            });
        },400);
    });
}

function reloadCalendarEvents(){
    $('#referencias td').addClass('no_imprimir');
    var comisiones = $('[name=comisiones]').val();
    var materias = $("[name=materias]").val();
    var profesores = $("[name=profesores]").val();
    var salones = new Array();
    var inputs = $(".codSalon:checked");
    for (var i = 0; i < inputs.length; i++){
        $('#referencias .salon_'+inputs[i].value+'').removeClass('no_imprimir');
        salones.push(inputs[i].value);        
    }
    
    var source = {
        url: BASE_URL + 'horarios/getHorarios',
        type: "POST",
        dataType: 'json',
        data: {
            salones: salones,
            comisiones: comisiones,
            materias: materias,
            profesores: profesores
        }
    };
    
    $('#calendar').fullCalendar('removeEvents');       
    $('#calendar').fullCalendar( 'removeEventSource', source );
    $('#calendar').fullCalendar('addEventSource', source);    
}

function nuevo_salon(){
   $.ajax({
        url: BASE_URL + 'horarios/frm_salones',
        data: 'codigo=-1',
        type: 'POST',
        cache: false,
        success: function(respuesta){
            $.fancybox.open(respuesta, {
                scrolling: 'auto',
                width: '50%',
                height: 'auto',
                minHeight: '300',
                maxWidth: '600',
                autoSize: false,
                autoResize: false,
                openEffect: 'none',
                closeEffect: 'none',
                padding: 1,
                helpers: {
                    overlay: null
                }
            });
        }
    });
}