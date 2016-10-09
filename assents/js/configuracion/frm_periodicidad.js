var langFRM = langFrm;
$('.fancybox-wrap').ready(function(){
//    console.log(periodicidad);
//    var clavesFRM=Array("validacion_ok");
//    
//    var langFRM='';
//    
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


 function cargarSelect(codigoEditado){
       
       var selectPeriodos=JSON.parse($('input[name="selectPeriodos"]').val());//ES UN HIDDEN 
       console.log('PERIODOS');
        console.log(selectPeriodos);
       
       $(selectPeriodos).each(function(k,element){
           
           var selected = codigoEditado == k ? 'selected' : '';
           
           $('select[name="unidadTiempo"]').append('<option value="'+element.id+'"  '+selected+'>'+element.nombre+'</option>');
           
       });
        
        
        
    }
    
    function initFRM(){
       
        var codigoEditado=$('input[name="codigo"]').val();
        
        //alert('OBJETO: '+JSON.stringify(periodicidad)+'\nINDICE A EDITAR: '+codigoEditado);
        
       cargarSelect(codigoEditado); 
        
       $('.fancybox-wrap select').chosen({
        width:'100%',
        allow_single_deselect: true
    });
    
    
        $('#frmPeriodicidad').on('submit',function(){
           
            
            
            
            
            $.ajax({
                    url: BASE_URL+"planespago/guardarConfiguracionPeriodicidad",
                    type: "POST",
                    data: $(this).serialize(),
                    dataType:"JSON",
                    cache:false,
                    success:function(respuesta){
                        if(respuesta.codigo==1){
                            
                            $.gritter.add({
                                    title: '_OK!',
                                    text: langFRM.validacion_ok,
                                    //image: $path_assets+'/avatars/avatar1.png',
                                    sticky: false,
                                    time: '3000',
                                    class_name:'gritter-success'
                        });
                        
                            $.fancybox.close(true);
                          crearTablaPeriodicidad();  
                            
                        }else{
                            
                            $.gritter.add({
                                    title: '_Uppss!',
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