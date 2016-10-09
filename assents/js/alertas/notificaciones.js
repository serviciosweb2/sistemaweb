var lang;
var claves = Array('_idioma',
        'ayer',
        'hoy',
        'maniana',
        'proxima_semana',
        'a_las',
        'la_semana_pasada',
        'ver_mas'
    );

$(document).ready(function(){    
    
    $.ajax({
        url: BASE_URL + 'entorno/getLang',
        data: "claves=" + JSON.stringify(claves),
        dataType: 'JSON',
        type: 'POST',
        cache: false,
        async: false,
        success: function(respuesta) {
            lang = respuesta;
            cargarNotificaciones();
//            moment.lang(lang._idioma, {
//                calendar : {
//                    lastDay : '[' + lang.ayer + ']',
//                    sameDay : '[' + lang.hoy + ']',
//                    nextDay : '[' + lang.maniana + ']',
//                    lastWeek : '[' + lang.la_semana_pasada + '] ( dddd )',
//                    nextWeek : 'dddd [' + lang.a_las + '] LT',
//                    sameElse : 'L'
//                }
//            });
        }
    });
});

function cargarNotificaciones(limit_min, limit_cant){    
    var tipo_notificacion = $("[name=tipo_alerta]").val();
    if (isNaN(parseInt(limit_min))){
        limit_min = 0;
        limit_cant = tipo_notificacion.length == 0 ? 6 : -1;
    }
    $.ajax({
        url: BASE_URL + 'alertas/listar_notificaciones/' + tipo_notificacion,
        type: 'POST',
        dataType: 'json',
        data: {
            tipo_notificacion: tipo_notificacion,
            limit_min: limit_min,
            limit_cant: limit_cant
        },
        success: function(_json){
            if (_json.error){
                // error en la generacion de la respuesta ¿informar?
            } else if (_json.data){
                var _html = '';
                var codigosAlertas = new Array();
                var last = _json.last;
                var count = _json.count;
                $.each(_json.data, function(fecha, value){
                    var today = new Date();
                    var dd = today.getDate();
                    var mm = today.getMonth()+1;
                    var yyyy = today.getFullYear();
                    if(dd < 10) {
                        dd = '0' + dd;
                    }
                    if(mm < 10) {
                        mm = '0' + mm;
                    } 
                    today = yyyy+'-'+mm+'-'+dd;
                    var currentDay = fecha == today;                    
                    //alert(fecha);
                    _html += '<div class="timeline-container">';
                    _html += '<div class="timeline-label">';
                    _html += '<span class="label label-grey arrowed-in-right label-lg">';
                    _html += '<b>';                    
                    _html += moment(fecha).lang(lang._idioma).format('l');
                    _html += '</b>';
                    _html += '</span>';
                    _html += '</div>';
                    $.each(value, function(tipo_alerta, alertas){
                        $.each(alertas, function(index, alerta){
                            codigosAlertas.push(alerta.codigo);
                            _html += '<div class="timeline-items">';
                            _html += '<div class="timeline-item clearfix">';
                            _html += '<div class="timeline-info">';
                            _html += '<i class="timeline-indicator icon-leaf btn btn-primary no-hover green"></i>';
                            _html += '</div>';
                            _html +=  '<div class="widget-box transparent">';
                            _html += '<div class="widget-header widget-header-small">';
                            _html += '<h5 class="smaller">';
                            _html += alerta.nombre_alerta;
                            _html += '</h5>';
                            _html += '<span class="widget-toolbar no-border">';
                            _html += '<i class="icon-time bigger-110"></i> &nbsp;';
                            if (currentDay)
                                _html += moment(fecha + " " + alerta.hora, "YYYY-MM-DD HH:mm:ss").fromNow();
                            else
                                _html += moment(alerta.hora, "HH:mm:ss").format("HH:mm");
                            _html += '</span>';
                            _html += '<span class="widget-toolbar">';
                            _html += '<a href="#" data-action="collapse">';
                            _html += '<i class="icon-chevron-up"></i>';
                            _html += '</a>';
                            _html += '</span>';
                            _html += '</div>';
                            _html += '<div class="widget-body">';
                            _html += '<div class="widget-main">';
                            _html += alerta.mensaje;
                            _html += '</div>';
                            _html += '</div>';
                            _html += '</div>';
                            _html += '</div>';
                            _html += '</div>';
                        });
                    });                    
                    _html += '</div>';
                });
                if (last < count){
                    var cantidad = 6 + parseInt(last);
                    _html += "<center><button id='hiddenScroll' class='btn btn-link' onclick='cargarNotificaciones(0, " + cantidad + ")'>"+lang.ver_mas+"</button></center>";
                }
                $("#area_de_notificaciones").html(_html);          
                scrollVerMas();
                marcarAlertasComoVistas(codigosAlertas);
            }
        }
    });

    function scrollVerMas(){
        if ($("#hiddenScroll").length > 0){
            var strAncla = $("#hiddenScroll");
            $('body,html').stop(true,true).animate({
                    scrollTop: $(strAncla).offset().top
            },1);
        }
    }
    
    function marcarAlertasComoVistas(codigosAlertas){
        for (var i = 0; i < codigosAlertas.length; i++){
            $.ajax({
                url: BASE_URL + 'alertas/marcar_como_leida',
                type: 'POST',
                dataType: 'json',
                async: false,
                data: {
                    codigo_alerta: codigosAlertas[i]
                },
                success: function(_json){
                    if (_json.error){
                        // ¿alertar sobre error en marcar como leido?
                    }
                }
            });            
        }
        clearTimeout(myVarTimeExecute);
        actualizarAlertas();
    }
}


    
function VerAsistencias(){
  window.location.href = BASE_URL + "matriculas/frm_pasar_a_regular" + "?todos=1";
}