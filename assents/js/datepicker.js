
$(document).ready(function(){
    //EVENTO QUE DESPLIEGA UN MENU CON UN CALENDARIO:
    
               $( "#datepicker" ).datepicker({
                 changeMonth: true,
                 changeYear: true,
                  showOn: "button",
buttonImage: "assents/img/calendario.jpg",
buttonImageOnly: true
                 
            });
         
            
$( "#datepicker" ).change(function() {
 // con esta funcion rescato la fecha para poder formatearla de acuerdo al pais
var fecha=''; 
$( "#datepicker" ).datepicker( "option", "dateFormat",alert($( this ).val()));
$('#datepicker').focus().focusout();
});
 
});
