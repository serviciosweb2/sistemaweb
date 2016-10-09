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
        
       $('.fancybox-wrap select').chosen({
        width:'100%',
        allow_single_deselect: true
    });
    
    
    $('#baja').on('submit',function(){
      
       $.ajax({
            url: BASE_URL+"usuarios/cambioEstadoUsuario",
            type: "POST",
            data: $(this).serialize(),
            dataType:"JSON",
            cache:false,
            success:function(respuesta){
                
                if(respuesta.codigo==1){
                    
                    $.gritter.add({
                                    title: 'Ok',
                                    text: langFRM.validacion_ok,
                                    //image: $path_assets+'/avatars/avatar1.png',
                                    sticky: false,
                                    time: '3000',
                                    class_name:'gritter-success'
                        });
                    
                    
                    $.fancybox.close(true);
                    
                    
                    oTableUsuarios.fnDraw();
                    
                    
                }else{
                    
                    
                    $.gritter.add({
                                    title: 'Upps!',
                                    text: respuesta.msgerror,
                                    //image: $path_assets+'/avatars/avatar1.png',
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


