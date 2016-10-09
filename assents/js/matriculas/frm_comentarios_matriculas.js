var clavesFRM=Array("validacion_ok","ERROR","eliminacion_ok","sin_comentarios");
var langFRM = langFrm;

function Hay_o_No_Comentarios(){
    var SIN_COMEN = '<div class="widget-main no-padding" style="overflow: hidden; width: auto; height: 300px;">';
    SIN_COMEN += '<div class="row fotoMSJ">';
    SIN_COMEN += '<div class="col-md-12 col-xs-12 text-center">';
    SIN_COMEN += ' <img src="' + BASE_URL + 'assents/img/dashboard/icono_comunicados.png"></div>';
    SIN_COMEN += '</div>';
    SIN_COMEN += '<div class="row">';
    SIN_COMEN += '<div class="col-md-12 col-xs-12 text-center textoMSJ">' + langFRM.sin_comentarios + '</div>';
    SIN_COMEN += '</div>';
    SIN_COMEN += '</div>';
    var selector = $('.dialogs').find('.dialogdiv');
    if(selector.length == 0){
        $(commentFile).find('i').addClass('hide');
        $('.dialogs').html(SIN_COMEN);
    } else {
        $(commentFile).find('i').removeClass('hide');
        $('.fotoMSJ').parent().remove();        
    };
}

function borrarComentario(id,element){
    $.ajax({
        url: BASE_URL + "matriculas/bajaComentario",
        type: "POST",
        data:{
            codigo: id
        },
        dataType:"JSON",
        cache:false,
        success:function(respuesta){
            if(respuesta.codigo == 1){
                gritter(langFRM.eliminacion_ok,true);
                $(element).closest('.dialogdiv').fadeOut(200).remove();
                Hay_o_No_Comentarios();
            } else {
                gritter(langFRM.ERROR);
            }
        }
    });
}

function addComentarios(comentarios){
    var HTML ='';
    var selector = $('.dialogs');
    $(comentarios).each(function(k,comentario){
        HTML+= '<div class="itemdiv dialogdiv">';
        HTML+='<div class="user"><img alt="Johns Avatar" src="' + BASE_URL + 'assents/theme/assets/avatars/profile-pic.jpg"></div>';
        HTML+='<div class="body">';
        HTML+='<div class="time"><i class="icon-time"></i><span class="blue">' + moment(comentario['fecha_hora'], "YYYY-MM-DD h:mm:ss").lang(idioma).calendar() + '</span></div>';
        HTML+='<div class="name"><a href="#"></a></div>';
        HTML+='<div class="text">'+comentario.comentario+'</div>';
        HTML+='<div class="tools"><a href="javascript:void(0)" onclick="borrarComentario(' + comentario.codigo + ',this)" class="btn btn-minier btn-danger"><i class="icon-only icon-trash"></i></a>';
        HTML+='</div></div></div>';                                
        selector.append(HTML);         
        selector.slimScroll({scrollBy: selector.height()});
        $('input[name="comentario"]').val('');        
        HTML = '';
    });    
    Hay_o_No_Comentarios();
}

function guardarComentario(element){
    $.ajax({
        url: BASE_URL+"matriculas/guardarComentario",
        type: "POSt",
        data: $(element).serialize(),
        dataType:"JSON",
        cache:false,
        success:function(respuesta){
            if(respuesta.codigo == 1){
                gritter(langFRM.validacion_ok,true);
                addComentarios([respuesta.custom.obj]);
            } else {
                gritter(langFRM.msgerror,false);
            }
        }
    });
}

 function initFRM(){
    addComentarios(DATACOMENTARIOS);       
    $('.dialogs').slimScroll({height: '400px'});    
    $('#nuevo_comentario').on('submit',function(){
        guardarComentario(this);          
        return false;
    });      
}

$('.fancybox-wrap').ready(function(){
    initFRM();
});