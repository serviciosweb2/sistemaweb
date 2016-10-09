
   var langFRM = langFrm;
$('.fancybox-wrap').ready(function(){
    
//    $.ajax({
//            url:BASE_URL+'entorno/getLang',
//            data:"claves=" + JSON.stringify(clavesFRM),
//            type:"POST",
//            dataType:"JSON",
//            async:false,
//            cache:false,
//            success:function(respuesta){
//                langFRM=respuesta;
//                
//            }
//    });
    
    initFRM();
    
   
});
function initFRM(){
       
       cargarSelect();
        
       $('.fancybox-wrap select').chosen({
        width:'100%',
        allow_single_deselect: true
    });
    
    
        $('#nuevaAlerta').on('submit',function(){

                
                $.ajax({
                    url: BASE_URL+"configuracion/guardarConfiguracionAcademicos",
                    type: "POST",
                    data: $(this).serialize()+'&nombre=ConfiguracionAlertaExamenes&valor=-1',
                    dataType:"JSON",
                    cache:false,
                    success:function(respuesta){
                       if(respuesta.codigo==1){
                           
                            $.gritter.add({
                                     title: '_OK!',
                                     text: langFRM.validacion_ok,
                                     //image: $path_assets+'/avatars/avatar1.png',
                                     sticky: false,
                                     time: '1500',
                                     class_name:'gritter-success'
                         });
                         
                         $.fancybox.close(true);
                         
                         tablaAlertaExamen();
                         
                          
                       }else{
                           
                           $.gritter.add({
                                    title: '_Upps!',
                                    text: respuesta.msgerror,
                                    //image: $path_assets+'/avatars/avatar1.png',
                                    sticky: false,
                                    time: '1500',
                                    class_name:'gritter-error'
                        });
                           
                       }
                    }
                });
                
                 return false;

        });
    
        
     
    }
    
     function cargarSelect(){
        
        var selectPeriodos=JSON.parse($('input[name="selectPeriodos"]').val()); //ES UN HIDDEN
        
        console.log(selectPeriodos);
        
        
        $(selectPeriodos).each(function(k,item){
            
            $('select[name="unidadTiempo"]').append('<option value="'+item.id+'">'+item.nombre+'</option>');
            
            
        });
        
        
    }