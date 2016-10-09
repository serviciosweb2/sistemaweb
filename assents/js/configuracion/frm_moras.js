var langFRM = langFrm ;

function initFRM(){
        
    $('.fancybox-wrap select').chosen({
        width:'100%',
        allow_single_deselect: true
    });
    
    $('#frmMoras').on('submit',function(){
        $.ajax({
                url: BASE_URL+"moras/guardarMora",
                type: "POST",
                data: $(this).serialize(),
                dataType:"JSON",
                cache:false,
                success:function(respuesta){
                    if(respuesta.codigo==1){
                        $.fancybox.close(true);
                        tablaMoras();
                        gritter(langFRM.validacion_ok,true);
                    }else{
                        gritter(respuesta.msgerrors);
                    }
                }
        });
        return false;
    });
}


$('.fancybox-wrap').ready(function(){
    
//    var clavesFRM=Array("validacion_ok");
    
//    $.ajax({
//            url:BASE_URL+'entorno/getLang',
//            data:"claves=" + JSON.stringify(clavesFRM),
//            type:"POST",
//            dataType:"JSON",
//            async:false,
//            cache:false,
//            success:function(respuesta){
//                langFRM=respuesta;
//                initFRM();
//            }
//    });
    initFRM();
    
});