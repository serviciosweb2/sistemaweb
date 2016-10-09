function initFRM(){        
        $('.fancybox-wrap select').chosen({
            width:'100%',
            allow_single_deselect: true
        });
    
        $('.fancybox-wrap form').on('submit',function(){
            var motivo = $("[name=motivo]").val();
            console.log("motivo " + motivo);
            if (motivo != ''){
                $.ajax({
                    url:  BASE_URL+'examenes/cambiarEstado',
                    type: "POST",
                    data:$(this).serialize(),
                    dataType:"JSON",
                    cache:false,
                    success:function(respuesta){
                        if(respuesta.codigo==1){
                            $.gritter.add({
                                title:  langFRM.ok,
                                text:   langFRM.validacion_ok,
                                sticky: false,
                                time: '3000',
                                class_name:'gritter-success'
                            });
                            tablaParciales.refresh();
                            tablaFinales.refresh();
                            $.fancybox.close(true);
                        } else {
                            $.gritter.add({
                                title: langFRM.upps,
                                text:   respuesta.msgerror,
                                sticky: false,
                                time: '3000',
                                class_name:'gritter-danger'
                            });
                        }
                    }
                });
            } else {
                gritter(langFRM.debe_seleccionar_un_motivo, false, '');
            }
            return false;            
        });     
    }
 
var langFRM = langFrm;

$('.fancybox-wrap').ready(function(){
    initFRM(); 
});
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
// 
// $('.fancybox-wrap').ready(function(){
//        $('select').chosen({
//            width:'100%'
//        });
//        
//        
//        $('.fancybox-wrap form').on('submit',function(){
//            console.log($(this).serialize());
//            $.ajax({
//                url:  BASE_URL+'examenes/cambiarEstado',
//                type: "POST",
//                data:$(this).serialize(),
//                dataType:"JSON",
//                cache:false,
//                success:function(respuesta){
//                    
//                    if(respuesta.codigo==0){
//                        
//                        $.gritter.add({
//                                    title: '_OK!',
//                                    text: '_Guardado Correctamente',
//                                    sticky: false,
//                                    time: '3000',
//                                    class_name:'gritter-success'
//                        });
//                        
//                        $.fancybox.close(true);
//                      
//                    }else{
//                        
//                        
//                        $.gritter.add({
//                                    title: 'Upps!!',
//                                    text: 'ocurrio un error',
//                                    sticky: false,
//                                    time: '3000',
//                                    class_name:'gritter-danger'
//                        });
//                        
//                        
//                    }
//                  
//                    
//                }
//            });
//            
//            return false;
//            
//        });
//        
//        
//   
//    });
