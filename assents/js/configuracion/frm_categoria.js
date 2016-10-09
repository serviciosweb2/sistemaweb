$('.fancybox-wrap').ready(function(){
    
    var clavesFRM=Array("validacion_ok");
    
    var langFRM='';
    
    $.ajax({
            url:BASE_URL+'entorno/getLang',
            data:"claves=" + JSON.stringify(clavesFRM),
            type:"POST",
            dataType:"JSON",
            async:false,
            cache:false,
            success:function(respuesta){
                langFRM=respuesta;
                initFRM();
            }
    });
    
    function initFRM(){
        
        $('.fancybox-wrap').on('submit','#frmCategoria',function(){
            
       
            
        var codigo=$('input[name="cod_padre"]').val();
        
        console.log(codigo);
         
        var dataURL='';
        
        switch(_accion){
            
            case 'nueva' :
                
               dataURL=BASE_URL+"articulos/agregarCategoriaSubcategoria"; 
                
              break;
              
            default:
                    
                    
               dataURL=BASE_URL+"articulos/modificarCategoria";   
              
           break;
            
        }
            
            
            var dataPOST=$(this).serialize();
            
            $.ajax({
                    url: dataURL,
                    type: "POST",
                    data: dataPOST,
                    dataType:"JSON",
                    cache:false,
                    success:function(respuesta){
                        
                        if(respuesta.codigo==1){
                            
                            $("#tree").dynatree("getTree").reload();
                            $.gritter.add({
                                    title: 'OK!',
                                    text: langFRM.validacion_ok,
                                   
                                    sticky: false,
                                    time: '3000',
                                    class_name:'gritter-success'
                        });
                            $.fancybox.close(true);
                        
                        }else{
                            
                            $.gritter.add({
                                    
                                    text: 'no se puedo guardar correctamente',
                                    sticky: false,
                                    time: '3000',
                                    class_name:'gritter-error'
                            });
                            
                        }
                        
                        
                    }
                });
            
            
            return false;
        });
     
    }
    
   
    
    
});

