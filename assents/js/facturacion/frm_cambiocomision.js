
    $(document).ready(function(){
        $('#cambioComision').submit(function(){
            
            var dataPOST=$(this).serialize();
            //alert (dataPOST);
            $.ajax({
                url:BASE_URL+'matriculas/guardarCambioComision',
                type:'POST',
                dataType:'json',
                data:dataPOST,
                success:function(respuesta){
                   //alert (respuesta);
                 
                   if(respuesta.codigo==1){
                        $('.fancybox-inner').html('<div class="row"><div class="col-md-12"><h1><small>Guardado correctamente</small></h1></div></div>');
                        setTimeout(function(){$.fancybox.close(true);},1100);
                        oTable.fnDraw();
                        
                   }else{
                       $('#errores').html('<p>error</p>').show();
                         $.fancybox.reposition();
                         //$.fancybox.resize;
                   }
                   
                  
                   
                   
                }
                
            });
            return false;
        });
    });

