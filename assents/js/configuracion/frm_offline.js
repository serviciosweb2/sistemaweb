function activarOffline(nombreEquipo,pin)
{
    
        $.ajax({
            url: BASE_URL+"configuracion/guardarConfiguracionOffline",
            type: "POST",
            data: {nombre:'modoOffline',valor:1,'nombreEquipo':nombreEquipo,'pin':pin},
            dataType:"JSON",
            cache:false,
            success:function(respuesta)
            {
                
                if(respuesta.codigo==1)
                {
                   if(respuesta.token)
                   {
                        
                        localStorage.tkoff = respuesta.token;
                        localStorage.tkpin = respuesta.pin;
                        localStorage.last_id=0;
                        localStorage.ultimoId_bancos =0;
                        localStorage.ultimoId_Tarjetas = 0;
                        
                        BASE_OFFLINE.token= respuesta.token;
                        BASE_OFFLINE.habilitado=1;
                        
                        $.fancybox.close(true);
                        
                        cierreFancy = 1;
                        
                        advertenciaLOGOUT();
                    }

                    
                     gritter(lang.validacion_ok,true);
                }
                    
                    
                    
                
                
            }
    });
    
    
}


$('.fancybox-wrap').ready(function()
{
   
    var clavesFRM = Array("validacion_ok");
    
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
    
    function initFRM()
    {
        
       $('.fancybox-wrap select').chosen({
        width:'100%',
        allow_single_deselect: true
    });
    
    
     
    }
    
   $('#frmOffline').on('submit',function(){
       
       var nombreEquipo = $('input[name="nombreEquipo"]').val();
       
       var pin = $('input[name="pin"]').val();
       
       var rePin = $('input[name="re-pin"]').val();
       
       if(nombreEquipo != '' &&  pin!=''  &&  rePin!=''){
           
                
                if(pin==rePin)
                {
                    
                    activarOffline(nombreEquipo,pin);
                
                }else{
                    
                    gritter('los pines no coinciden',false);
                    
                }
       
       
       }else{
           
           gritter('complete todos los campos',false);
       
       }
       
       return false;
   });
    
    
});

