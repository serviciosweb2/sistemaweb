oTableInscriptos ='';

var langFRMinscriptos=langFrm;

function initFRM(){
        
       $('.fancybox-wrap select').chosen({
        width:'100%',
        });
    
  oTableInscriptos=$('#tablaInscriptos').DataTable({
        "lengthChange": false,
        "displayLength":5,
        "info":true
        }); 
 
 $('#tablaInscriptos_wrapper').find('.paging_bootstrap').parent().removeClass('col-sm-6').addClass('col-sm-12')
    
    $(".btn-message").click(function(){
        
           $.ajax({
                
                url:BASE_URL +'horarios/frm_excepciones' ,
                type:'POST',
                data: oTableInscriptos.$('input').serialize() + '&' +  $('input[name="codigo_horario"]').serialize(),
                success:function(respuesta){

                    $.fancybox.close();
                    
                    $.fancybox.open(respuesta, {
                                widht:"70%",
                                scrolling: 'auto',
                                padding: 0,
                                openEffect: 'none',
                                closeEffect: 'none',
                                helpers: {
                                    overlay: null,
                                },


                            });
                }
            
            });
        
        
    });
    
    $(".fancybox-wrap").on('change','.asistencia',function(){
       
           var select = this;
           $("[name='cod_matricula_horario[]']").each(function(key, valorObj) {
       
                     
               if($(valorObj).val() ===  $(select).attr("matricula-inscripcion")){
              
         
      
              
              
                if($("#asistencia").val() === ""){
                     $(valorObj).prop("disabled",false);
                }else{
                    
                      $(valorObj).prop("disabled",true);
                    
                }
               
               
                  
          
                   
               }
               
               
               
           });
       


           $.ajax({
            url:BASE_URL +'asistencias/guardarAsistencias' ,
            type:'POST',
            data:"alumnos[0][cod_matricula_horario]=" + $(select).attr("matricula-inscripcion") +"&alumnos[0][estado]="  + $(select).val() ,
                        dataType:'JSON',
               
               
               success:function(respuesta){
                   
                switch (respuesta.codigo){
                    
                    case 1:
                                       $.gritter.add({
                                title: langFRMinscriptos.BIEN,
                                text: langFRMinscriptos.validacion_ok ,
                                sticky: false,
                                time: '3000',
                                class_name: 'gritter-success'
                            });
                             
                        break;
                        
                    default:
                                     $.gritter.add({
                                title: langFRMinscriptos.ERROR,
                                text: respuesta ,
                                sticky: false,
                                time: '3000',
                                class_name: 'gritter-error'
                            });
                             
                        
                        
                        break;
                        
                        
                        
                    
                }
           
            }
            
        });
        
        
    }); 
     
    }

$('.fancybox-wrap').ready(function()
{
    initFRM()
 });


