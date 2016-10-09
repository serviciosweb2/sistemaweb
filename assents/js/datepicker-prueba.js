//function getPais(){
//    
//   
//    var pais="<?php $ci=& get_instance(); $session=$ci->session->userdata('filial'); echo $region=$session['pais'];?>";
//
//    var formato='';            
//
//    switch(pais){
//
//        case '1':
//            formato='dd/mm/yy';
//    }          
//    
//        return formato;
//    
//}
    
 function crearDatepicker(valor){
     
     var input=$('#'+valor);
     

     input.datepicker({
                changeMonth: true,
                changeYear: true
                ////showOn: "button",
               // dateFormat: getPais()
                });
     
     
    $('.input-group-addon').click(function(){
      
        var valor=$(this).parent().find('input').trigger('focus');
         $(this).focusout();
     });
     
      }