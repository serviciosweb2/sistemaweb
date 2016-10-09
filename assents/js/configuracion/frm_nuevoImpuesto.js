var langFRM= langFrm;
function initFRM(){
        $('.fancybox-wrap select').chosen({
        width:'100%',
        allow_single_deselect: true
    });
        
        $('#nuevoImpuesto').on('submit',function(){
        
        
        
        $.ajax({
            url: BASE_URL+"impuestos/guardarImpuesto",
            type: "POST",
            data: $(this).serialize(),
            dataType:"JSON",
            cache:false,
            success:function(respuesta){
                
                var text=langFRM.validacion_ok;
                var title='OK';
                var clase='gritter-success';
                
                if(respuesta.codigo==0){
                    
                 text=respuesta.msgerror;
                 title='Upss!'; 
                 clase='gritter-error';  
                    
                }
                
                $.gritter.add({
                                    title: title,
                                    text: text,
                                    sticky: false,
                                    time: '3000',
                                    class_name:clase
                        });
                        
                    $.fancybox.close(true);  
                    tablaImpuestos();      
            }
        });
        
        return false;
        
    });
    }
$('.fancybox-wrap').ready(function(){
    
//     var clavesFRM=Array("validacion_ok");
    
    
    
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


