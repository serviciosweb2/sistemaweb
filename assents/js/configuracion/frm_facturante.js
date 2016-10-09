$('.fancybox-wrap').ready(function(){
    
    var clavesFRM=Array("validacion_ok");
    
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
    
     codLocalidad=$('input[name="codLocalidad"]').val();// Es un hidden
    
     codProvincia=$('select[name="provincia"]').val();
    
   
    
    
    
    function getLocalidades(dataPOST,valor){
        
        //alert(valor);
        
        if( dataPOST !=''){
        
        $.ajax({
                    url: BASE_URL+"configuracion/getLocalidades",
                    type: "POST",
                    data: "idprovincia="+dataPOST,
                    dataType:"JSON",
                    cache:false,
                    success:function(respuesta){
                        
                        $('select[name="localidad"]').empty().append('<option></option>');
                        
                        $(respuesta).each(function(k,localidad){
                            
                            var selected = valor == localidad.id ? 'selected' : '';
                            
                            $('select[name="localidad"]').append('<option value="'+localidad.id+'"  '+selected+'>'+localidad.nombre+'</option>');
                            
                        });
                        
                        $('select[name="localidad"]').trigger('chosen:updated');
                    }
                });
        }
    }
    
    getLocalidades(codProvincia,codLocalidad);
    
    function initFRM(){
        
       $('.fancybox-wrap select').chosen({
        width:'100%',
        allow_single_deselect: true
    });
        
        $('input[name="inicioActividad"]').datepicker({
            format:"dd/mm/yyyy",
            language: "es",
            autoclose: true
        });
        
        
        
        $('select[name="provincia"]').on('change',function(){
            
            var dataPOST=$(this).val();
            
            getLocalidades(dataPOST,'');
            
        });
        
        
        $('#frmFacturante').on('submit',function(){
            
            
            
            $.ajax({
                    url: BASE_URL+"facturantes/guardarFacturante",
                    type: "POST",
                    data: $(this).serialize(),
                    dataType:"JSON",
                    cache:false,
                    success:function(respuesta){
                        
                        
                        if(respuesta.codigo==1){
                            
                                $.gritter.add({
                                        title: 'Ok',
                                        text: langFRM.validacion_ok,
                           
                                        sticky: false,
                                        time: '3000',
                                        class_name:'gritter-success'
                                });
                                
                                
                                $.fancybox.close(true);
                                
                                tablaFacturantes();
                            
                        }else{
                            
                            
                                $.gritter.add({
                                        title: 'Upss!',
                                        text: respuesta.msgerror,
                                        //image: $path_assets+'/avatars/avatar1.png',
                                        sticky: false,
                                        time: '3000',
                                        class_name:'gritter-error'
                                });
                            
                            
                        }
                    }
            });
            
            return false;
        });
    
     
    }
    
   
    
    
});


