var myVarTimeExecute;
var lang1;

var claves1 = Array('notificaciones', 'ver_todas_las_notificaciones', '_idioma', 'ayer', 'hoy', 'maniana',
                    'proxima_semana', 'a_las', 'la_semana_pasada', 'ver_mas', 'ver_todas_las_consultas',
                    "ERROR", "BIEN", "mensajes", "fallo_envio_alertas_alumnos"
                );
        
function graficarMailsConsultas(_json, total_consultas){
    var _html = '';
    var valorTotal = 0;
    if (_json.iTotalRecords){
        valorTotal = _json.iTotalRecords;
    }
    if (valorTotal > 0){
        $("#cantidad_consultas_web").html(valorTotal);
    } else {
        $("#cantidad_consultas_web").html("");
    }
    if (valorTotal > total_consultas){
        $("#icon_envelope").removeClass("icon-animated-vertical");
        $("#icon_envelope").addClass("icon-animated-vertical");
        var originalState = $("#icon_envelope").clone();
        $("#icon_envelope").replaceWith(originalState);
    }
    _html += '<li class="dropdown-header">';
    _html += '<i class="icon-envelope-alt"></i>';
    _html += valorTotal + lang1.mensajes;
    _html += '</li>';
    
    $.each(_json.aaData, function(index, value){
        _html += '<li>';
        _html += '<a href="' + BASE_URL + 'consultasweb/index/ver_consulta/' + value.codigo + '">';
        _html += '<span class="msg-body">';
        _html += '<span class="msg-title">';
        _html += '<span class="blue">';
        _html += value.nombre;
        _html += '</span>';
        _html += '<br>';
        _html += value.asunto;
        _html += '</span>';
        _html += '<span class="msg-time">';
        _html += '<i class="icon-time"></i>';
        _html += '<span>';
        _html += '&nbsp;';
        _html += moment(value.fechahora).lang(lang1._idioma).calendar();
        _html += '</span>';
        _html += '</span>';
        _html += '</span>';
        _html += '</a>';
        _html += '</li>';
    });
    if (_json.aaData.length <  valorTotal){
        _html += "<li>";
        _html += '<a href="' + BASE_URL + 'consultasweb">';
        _html += '<span class="blue">';
        _html += lang1.ver_mas + '...';
        _html += '</span>';
        _html += '</a>';
        _html += '</li>';
    }
    _html += '<li>';
    _html += '<a href="' + BASE_URL + 'consultasweb">';
    _html += lang1.ver_todas_las_consultas;
    _html += '<i class="icon-arrow-right"></i>';
    _html += '</a>';
    _html += '</li>';
    $("[name=descripcion_consultas_web]").html(_html);
}


function graficarAlertas(_json, total_original){
    var _html = '';
    var valorTotal = 0;
    if (_json.generales.total){
        valorTotal = _json.generales.total;
    }
    if (_json.alertas_envios_fallidos){
        valorTotal += 1;
    }
    if (valorTotal > 0){
        $("#cantidad_alertas_container").html(valorTotal);
    } else {
        $("#cantidad_alertas_container").html("");
    }
    if (valorTotal > total_original){
        $("#icon_alerta").removeClass("icon-animated-bell");
        $("#icon_alerta").addClass("icon-animated-bell");
        var originalState = $("#icon_alerta").clone();
        $("#icon_alerta").replaceWith(originalState);
    }
    _html += '<li class="dropdown-header" id="li_container">';
    _html += '<i class="icon-warning-sign"></i>';
    _html += '<span name="cantidad_alertas_container">';
    _html += valorTotal;
    _html += '</span>';
    _html += ' ' + lang1.notificaciones;
    _html += '</li>';                
    if (_json.generales.total){
        $.each(_json.generales.alertas, function(index, value){
            _html += "<li>";
            var url = BASE_URL + 'alertas/notificaciones/' + value.tipo_alerta;
            if (value.url_notificacion){
                url = BASE_URL + value.url_notificacion;
            }
            _html += '<a href="' + url + '">';
            _html += '<div class="clearfix">';
            _html += '<span class="pull-left">';
            _html += '<i class="btn btn-xs no-hover btn-pink icon-comment"></i>';
            _html += value.nombre_alerta;
            _html += '</span>';
            _html += '<span class="pull-right badge badge-info">+';
            _html += value.cantidad;
            _html += '</span>';
            _html += '</div>';
            _html += '</a>';
            _html += '</li>';
        });
    }
    if (_json.alertas_envios_fallidos && _json.alertas_envios_fallidos > 0){
        _html += "<li>";
        _html += '<a href="' + BASE_URL + 'alertas/envios_fallidos">';
        _html += '<div class="clearfix">';
        _html += '<span class="pull-left">';
        _html += '<i class="btn btn-xs no-hover btn-pink icon-comment"></i>';
        _html += lang1.fallo_envio_alertas_alumnos;
        _html += '</span>';
        _html += '<span class="pull-right badge badge-info">+';
        _html += _json.alertas_envios_fallidos;
        _html += '</span>';
        _html += '</div>';
        _html += '</a>';
        _html += '</li>';
    }
    _html += '<li>';
    _html += '<a href="' + BASE_URL + 'alertas/notificaciones/">';
    _html += ' ' + lang1.ver_todas_las_notificaciones;
    _html += '<i class="icon-arrow-right"></i>';
    _html += '</a>';
    _html += '</li>';
    $("[name=descripcion_alertas_container]").html(_html); 
}

function actualizarAlertas(){
    var recuperarConsultasWeb = 0;
    if ($("[name=cantidad_consultas_web]").length > 0){
        recuperarConsultasWeb = 1;
        var total_consultas = parseInt($("cantidad_consultas_web").html());
        if (isNaN(total_consultas)){
            total_consultas = 0;
        }
    }
    var total_original = parseInt($("#cantidad_alertas_container").html());   
    if (isNaN(total_original)){
        total_original = 0;
    }    
    $.ajax({
        url: BASE_URL + "alertas/resumen_alertas_usuario/",
        dataType: "json",
        type: 'POST',
        data: {
            recuperar_consultas_web: recuperarConsultasWeb
        },
        success: function(_json){
            if (_json.alertas_usuarios){
                graficarAlertas(_json.alertas_usuarios, total_original);
            } else {
                var _html = '';
                _html += '<li>';
                _html += '<a href="' + BASE_URL + 'alertas/notificaciones/">';
                _html += ' ' + lang1.ver_todas_las_notificaciones;
                _html += '<i class="icon-arrow-right"></i>';
                _html += '</a>';
                _html += '</li>';
                $("[name=descripcion_alertas_container]").html(_html);     
            }
            if (_json.mails_consultas){
                graficarMailsConsultas(_json.mails_consultas, total_consultas);
            } else {
                var _html = '';
                _html += '<li>';
                _html += '<a href="' + BASE_URL + 'consultasweb">';
                _html += lang1.ver_todas_las_consultas;
                _html += '<i class="icon-arrow-right"></i>';
                _html += '</a>';
                _html += '</li>';
                $("[name=descripcion_consultas_web]").html(_html);
            }
        }
    });
    myVarTimeExecute = window.setTimeout("actualizarAlertas();", 180000);
}

function generalContextMenu(objMenu,e){
    $('#menu').hide().fadeIn('fast').remove();
    var menuDesplegable=$('<ul/>').attr({
            id:'menu',
            oncontextmenu:"return false",
            onkeydown:"return false"
        }).css({
            "position":'fixed',
            "margin-top":e.clientY,
            "z-index":'10000',
            "margin-left":e.clientX
        });                   
                   
    $(objMenu).each(function(){
        var habilitado = this.habilitado === "1" ? '' : 'ui-state-disabled';
        var li = $('<li/>').addClass(habilitado);
        var a = $('<a/>').text(this.text);
        var accion = this.habilitado === "1" ? this.accion : 'false';
        a.attr({accion: accion});
        a.attr({id: this.accion});
        li.append(a);
        menuDesplegable.append(li);
    });                  
                   
    $('body').prepend(menuDesplegable);
    $("#menu").menu();
    $('body').not('table').on('click',function(){
        $('#menu').hide().fadeIn('fast').remove();
    });
}


function generarBotonSuperiorMenu(objMenu,clase,classBtn){
    $(".dataTables_length").parent().addClass("no-margin-left no-padding-left");
    var countObj=objMenu.length;      
       var div = $('<div>').attr({
            class:'btn-group'
        });      
        
        var ul=$('<ul>').attr({
            class:'dropdown-menu dropdown-default'
        });
        
        $(objMenu).each(function(key,fila){        
            switch(key){             
                case 0:
                    div.append(($('<button>').attr({
                        class:'btn ' + clase + " boton-primario" ,
                        accion:fila.accion,
                        disabled: fila.habilitado == 0 ? true : false
                    }).append($('<i>').addClass(classBtn)).append(fila.text)));                            
                            
                    var drop= $('<button>').attr({
                        class:'btn  dropdown-toggle '  + clase ,
                        'data-toggle':"dropdown"
                    });
               
                    var span=$('<span>').attr({
                        class:'icon-caret-down icon-only'
                    });               
               
                    drop.append(span);                       
                    countObj > 1 ? div.append(drop) :'';
                    break;
            
                default:                    
                    var li=$('<li>');
                    var a=$('<a>').attr({
                        accion:fila.accion
                    }).text(fila.text);
                    li.append(a);
                    ul.append(li);                
                break;
            }
        });
        
    div.append(ul);
    return div;    
}

function marcarScreenOffline(){   
    if( BASE_OFFLINE.token == localStorage.getItem('tkoff')){
        $('#indicadorOffline').show();
    } else {
        $('#indicadorOffline').hide();
    }
}

$(document).ready(function(){    
    $.extend($.gritter.options, { 
        position: 'bottom-right', 
        fade_in_speed: 'medium', 
        fade_out_speed: 2000, 
        time: 6000 
    })  ;     
    BASE_MOMENT_LANG;
    lang1 = BASE_MOMENT_LANG;
    actualizarAlertas();    
    marcarScreenOffline();
      
    $( "#atajo_aspirantes" ).tooltip({
        show: null,
        position: {
                my: "left top",
                at: "left bottom"
        },
        open: function( event, ui ) {
                ui.tooltip.animate({ top: ui.tooltip.position().top + 10 }, "fast" );
        }
    });

    $( "#atajo_nuevo_alumno" ).tooltip({
        show: null,
        position: {
            my: "left top",
            at: "left bottom"
        },
        open: function( event, ui ) {
            ui.tooltip.animate({ top: ui.tooltip.position().top + 10 }, "fast" );
        }
    });
    
    $( "#atajo_facturacion" ).tooltip({
        show: null,
        position: {
            my: "left top",
            at: "left bottom"
        },
        open: function( event, ui ) {
            ui.tooltip.animate({ top: ui.tooltip.position().top + 10 }, "fast" );
        }
    });
    
    $( "#atajo_configuracion" ).tooltip({
        show: null,
        position: {
            my: "left top",
            at: "left bottom"
        },
        open: function( event, ui ) {
            ui.tooltip.animate({ top: ui.tooltip.position().top + 10 }, "fast" );
        }
    });    
});

function ingresarLetras(ctrl,e){
    return validarIngresoCampo(ctrl, e, 'letras');
}

function ingresarNumero(ctrl,e, permitirCopiarYPegar){
    return validarIngresoCampo(ctrl, e, 'numeros', permitirCopiarYPegar);
}

function ingresarNumeroLetra(ctrl,e){
    return validarIngresoCampo(ctrl, e, 'numerosYletras');
}

function ingresarNoNumeros(ctrl,e){
    return validarIngresoCampo(ctrl, e, 'noNumeros');
}

function ingresarFloat(ctrl, e,separador_decimal){
    var separador = '';
    if(separador_decimal != ''){
        separador = separador_decimal;
    }else{
        separador = '.,';
    }
    return validarIngresoCampo(ctrl, e, 'float',' ',separador);
}

function ingresarTelefono(ctrl, e){
    return validarIngresoCampo(ctrl, e, 'telefono');
}

function validarIngresoCampo(ctrl,e, tipoPatron, permitirCopiarYPegar,separador){    
    var tecla = (document.all) ? e.keyCode : e.which;
    if (tecla==8) return true;
    if (tecla==0) return true;
    if (permitirCopiarYPegar && e.ctrlKey && (tecla == 118 || tecla == 86)) return true;
    if (permitirCopiarYPegar && e.ctrlKey && (tecla == 120 || tecla == 88)) return true;
    if (permitirCopiarYPegar && e.ctrlKey && (tecla == 99 || tecla == 67)) return true;    
    var patron = '';
    if (tipoPatron == 'letras'){
        patron =/[A-Za-z]/;
    } else if (tipoPatron == 'numeros'){
        patron = /\d/; 
    } else if (tipoPatron == 'numerosYletras'){
        patron = /[0-9a-zA-Z\s]/;
    } else if (tipoPatron == 'noNumeros'){
        patron = /\D/;
    } else if (tipoPatron == 'float'){
        if (separador == ',')
            patron = /^([0-9])*[,]?[0-9]*$/;  //el patron admite . y ,
        else
            patron = /^([0-9])*[.]?[0-9]*$/;
    } else if (tipoPatron == 'telefono'){
        patron = /^([0-9])*[-]?[0-9]*$/;
    }
    te = String.fromCharCode(tecla);
    return patron.test(te);
}

function validarFecha(fecha){
    var fechaArr = fecha.split('/');
    var aho = fechaArr[2];
    var mes = fechaArr[1];
    var dia = fechaArr[0]; 
    var plantilla = new Date(aho, mes - 1, dia);
    if(!plantilla || plantilla.getFullYear() == aho && plantilla.getMonth() == mes -1 && plantilla.getDate() == dia){
        return true;
    } else {
        return false;
    }
}

function gritter(mensaje, success, titulo){
    var class_name = success ? "gritter-success" : "gritter-error";
    var title = '';
    if (titulo){
        title = titulo;
    } else {
        title = success ? lang.BIEN : lang.ERROR;
    }
    $.gritter.add({
        title: title,
        text: mensaje,
        sticky: false,
        time: '3000',
        class_name: class_name
    });    
}

function trim(str){
    return str.replace(/^\s*|\s*$/g,"");
}  
 
function marcarTr(){        
    $('table').on('mousedown','tr',function(e){
        if(e.button===2){
            $(this).closest('table').find('tr.success').removeClass('success');
            $(this).addClass('success');
        }   
    });
        
    $('.page-content').not('table').on('click',function(){
        $('tr').closest('table').find('tr.success').removeClass('success');
    });        
}
    
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments);},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m);
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-55507530-1', 'auto');
  ga('set', '&uid', '1');
  ga('send', 'pageview');

