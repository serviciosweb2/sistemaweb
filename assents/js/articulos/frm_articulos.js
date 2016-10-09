var langFRM = langFrm;
$('.fancybox-wrap').ready(function(){
    initFRM();
});

function initFRM(){
    $('.fancybox-wrap select').chosen({
        width:'100%',
        allow_single_deselect: true
    });
    
    $('#frmArticulo').on('submit',function(){
        $.ajax({
                url: BASE_URL+"articulos/guardar",
                type: "POST",
                data:$(this).serialize(),
                dataType:"JSON",
                cache:false,
                success:function(respuesta){
                    if(respuesta.codigo==1){
                        gritter(langFRM.validacion_ok,true);
                        $.fancybox.close(true);
                        oTableARTICULOS.fnDraw();
                    } else {
                        gritter(respuesta.msgerror);
                    }
                }
            });
        return false;
    });
}