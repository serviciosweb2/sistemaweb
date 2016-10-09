 var langFRM='';
function guardarReceso(event){
   var data_post = $('#nuevo_receso_filial').serialize();
    $.ajax({
                url: BASE_URL + "configuracion/guardarRecesoFilial",
                type: "POST",
                data: data_post,
                dataType:"JSON",
                cache:false,
                success: function(respuesta) {
                    if(respuesta.codigo == 1){
                        gritter(langFRM.validacion_ok,true);
                        dibujarTablaRecesoFilial();
                        $.fancybox.close();
                    }else{
                        gritter(respuesta.msgError);
                    }
                }
            });
            event.preventDefault();
            return false;
}

$('.fancybox-wrap').ready(function(){
   
    var clavesFRM=Array("validacion_ok");

   
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

    function initFRM(){

       $('.fancybox-wrap select').chosen({
        width:'100%',
       });
       
       $('#fecha_desde_receso').datepicker({
           
       });
       
       $('#fecha_hasta_receso').datepicker({
           
       });
       
       $('.inputHora').timepicker({
                        
                        minuteStep: 1,
                        showSeconds: true,
                        showMeridian: false
				}).next().on(ace.click_event, function(){
					$(this).prev().focus();
				});

    }




}); 
