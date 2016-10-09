//$('#divBlock').hide();

function cerrarMensajeError()
{
    $('.msjAjaxError').css({'top':'-200'});
}

function detalleAjaxError()
{
    cerrarMensajeError();
    $('#detallesDeErrorAjax').modal('show');
}
    
$(document).ajaxStart(function(event){
      
    // console.log('AJAXSTART:',event);
        
          $('#divBlock').show();
       
            
        $('.ajaxLoad').css({'top':'0'});
            
           
    
}).ajaxSend(function(event, jqXHR, ajaxOptions)
{// mientras que se ejecuta una peticion
             
    var str = ajaxOptions.url;
    var n = str.search('/listar');
    if( n == -1 )
    {// si no encontro un listar


       // console.log('muestro div')
        switch(ajaxOptions.url)
        {


           case BASE_URL+'alertas/resumen_alertas_usuario/':

           $('.ajaxLoad').css({'top':'-200'});
           $('#divBlock').hide();

               break;

           case BASE_URL+'dashboard/getComunicadosFilial':

               $('.ajaxLoad').css({'top':'-200'});
               $('#divBlock').hide();

               break;

           case BASE_URL+'dashboard/getMailsConsultas':


               $('.ajaxLoad').css({'top':'-200'});
               $('#divBlock').hide();

               break;

           case BASE_URL+'offline/ping':


               $('.ajaxLoad').css({'top':'-200'});
               $('#divBlock').hide();

               break;

           case BASE_URL+'offline/sincronizar':

               $('.ajaxLoad').css({'top':'-200'});
               $('#divBlock').hide();

               break;

           default:


             //$('#divBlock').hide();


   }


    }
    else
    {
        //console.log('no muestro div');
        $('#divBlock').hide();
    }
            

    
}).ajaxSuccess(function(event){
         
         
         
         
}).ajaxStop(function(){
        
    $('.ajaxLoad').css({'top':'-200'});

    $('#divBlock').hide();
        
}).ajaxError(function( event, request, settings ){
    
    
    $('.ajaxLoad').css({'top':'-200'});

    //$('.msjAjaxError').css({'top':'0'});
    var errorAJAX = {'event':event,'request':request,'settings':settings};
    
    console.log('REQUEST',request);
    console.log('EVENT',event);
    console.log('SETTINGS',settings);
    
    var error = request.error();
    
    console.error('AJAXERROR : ',errorAJAX);
//    
//    
//    var mensajeParse = request.responseText.split('\n');
//    console.log('parsiado',mensajeParse);
//    var concat = '';
//    var x= true;
//    
//    for(var i in mensajeParse)
//    {
//        
//        if(mensajeParse[i] == '<style type="text/css">')
//        {
//           x= false; 
//        }
//        
//        if(mensajeParse[i] == '</style>')
//        {
//           x= true; 
//        }
//        
//        if(x==true)
//        {
//            concat+=mensajeParse[i];
//        }
//    
//        $('.contenedorDetalle').html(concat);
//    }
    
});

