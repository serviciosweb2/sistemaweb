$(document).ready(function() {
    
    
    $("#btn-alta").click(function(){
            $.ajax({
                                url:BASE_URL+"matriculas/cambiarEstado",
                                data:$("#frm-alta").serialize(),
                                type:'POST',
                                cache:false,
                                       dataType: 'json',
                                success:function(resp){
                    
                                    console.log(resp.codigo);
                                    if(resp.codigo=== 1){
                                            
                          $.fancybox.close();
                
                               $.gritter.add({
                                    title: lang.BIEN,
                                   text: lang.validacion_ok ,
                                    sticky: false,
                                    time: '3000',
                                    class_name: 'gritter-success'
                        });
                    
                                                $(".alert").addClass("hide");    
                                        
                                    }else{
                        
                                         $(".alert").removeClass("hide"); 
                                        $(".alert").html(resp);
                                    }
                                        
                                 
                                 
                                 
                                 
                                }
                                
                            });
       
    });
    
    
    
    });