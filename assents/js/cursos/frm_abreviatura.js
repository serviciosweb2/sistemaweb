
var langFRM = langFrm;

function initFRM(){
        
     
        $('#submit').on('click',function(){
            $('form').submit();
            return false;
        });
    
        $('form').on('submit',function(){
            var data_post = $('form').serialize();
        $.ajax({
            url: BASE_URL+'cursos/guardarAbreviatura',
            type: "POST",
            data: data_post,
            dataType:"JSON",
            cache:false,
            success:function(respuesta){
                
                if(respuesta.codigo!=0){
                    
                    gritter(langFRM.validacion_ok,true);
                    
                    oTable.fnDraw();
                    
                    $.fancybox.close(true);
                
                }else{
                    
                    gritter(respuesta.msgerrors);
                }
            }
        });
        return false;
    });
       
       
       
    
    
     
    }

$('.fancybox-wrap').ready(function()
{
    initFRM();
});
    
    
    

