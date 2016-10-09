$(document).ready(function() {
    
    
    $("#btn-alta").click(function(){
           $.ajax({
            url: BASE_URL + 'matriculas/altaMatriculasPeriodos',
            data: $("#frm-alta").serialize(),
            cache: false,
            type: 'POST',
            dataType: 'json',
            success: function(respuesta) {
               if(respuesta.codigo){
                                       $.fancybox.close();                
                    $.gritter.add({
                         title: lang.BIEN,
                        text: lang.validacion_ok ,
                         sticky: false,
                         time: '3000',
                         class_name: 'gritter-success'
                    });
               }else{
                   $.gritter.add({
                        title: lang.ERROR,
                        text: respuesta.respuesta ,
                        sticky: false,
                        time: '3000',
                        class_name: 'gritter-error'
                    });
               }
                
             
            }
        });
        return false;
        

       
    });
    
    
    
    });