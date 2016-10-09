$('.fancybox-wrap').ready(function(){
    $('#detalleDeudor').DataTable({
        "bLengthChange": false,
        "pageLength": 4
    });
    
   $('.fancybox-wrap').on('click','button[name="submit"]',function(){        
        var dataPOST=$('#enviarAviso').serialize();
        $.ajax({
            url: BASE_URL+'ctacte/guardarAlertasDeudor',
            type: "POST",
            data: dataPOST,
            dataType:"JSON",
            cache:false,
            success:function(respuesta){
                if(respuesta.codigo==1){                    
                    $.gritter.add({
                        title: 'OK!',
                        text: 'Enviado Correctamente',
                        sticky: false,
                        time: '3000',
                        class_name:'gritter-success'
                    });
                        
                    $.fancybox.close(true);                    
                } else {                    
                    $.gritter.add({
                        title: 'Uppss!',
                        text: respuesta.msgerror,
                        sticky: false,
                        time: '3000',
                        class_name:'gritter-success'
                    });
                }
            }
        });        
        return false;
    });    
});