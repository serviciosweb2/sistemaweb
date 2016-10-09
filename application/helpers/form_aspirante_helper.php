<script>  
$(document).ready(function(){
      
        // FUNCION QUE CAPTURA LOS EVENTOS EN LOS SELECT'S
            
             $('body').on('change','select',function(){
                    var select=$(this).attr('id');
                    var provincia=$('#'+select+' option:selected').val();
               
                  switch(select){
                      case 'prov_muni':
                           $.ajax({
                                url:'<?=base_url('aspirantes/getlocalidades')?>',
                                data:'provincia='+provincia,
                                type:'POST',
                                cache:false,
                                dataType:'json',
                                success:function(respuesta){
                                                  
                                        var tipo=eval(respuesta);
                                      // alert(respuesta);
                                            $('#contenedorlocalidad').empty();
                                        $('#contenedorlocalidad').html('<select id="selectLocalidad" name="cod_localidad" class="span12 chosen-select"></select>');
                                        $.each(respuesta , function( index,value ) {
                                            
                                         // alert(index+' '+value['id']+' '+value['nombre']) ;
                                          $('#selectLocalidad').append('<option value="'+value['id']+'">' + value['nombre'] + '</option>');
                                           });
                                        $('.chosen-select').chosen({
                                             width: "100%"
                                        });
                                         
                                } 
                               
                        });
                  
                        break;
                  } 
                 
                    
           });
           
        
     
        
        var accion='';  
			$('body').on('click','input.btn',function(){
           accion=$(this).attr('name');
        }); 
            $('#contenedorGeneral').on('submit','#nuevo_aspirante', function(e) { 
             //alert('submit');
//                if(!$('[name="empresa_fijo_aspirante"]').valid()) {
//        e.preventDefault();
//                }
                    $('#accion').attr('value',accion);
                        var datos=$(this).serialize();
                        alert(datos);
                            $.ajax({
                                url:'<?=base_url('aspirantes/guardar')?>',
                                type:'POST',
                                data:datos,
                                cache:false,
                                dataType:'json',
                                success:function(respuesta){
                                
                                var codigo=String(respuesta.codigo);

                                            switch(codigo){
                                                
            
                                                case '0':// cero es erro de validacion , muestro los errores
                                                    alert('case 0');
                                                    $('.alert').html(respuesta.msgerror);
                                                    $('.alert').fadeIn('slow');
                                                    break;

                                                case '1':// la validacion esta bien 
                                                    alert(respuesta.custom.cod_aspirante+'cod_aspirante retorno');
                                                    var cod_aspirante=respuesta.custom.cod_aspirante;
                                                    alert('case 1');
                                                    var mensajeOk='<div class="row-fluid"><h3><p class="text-center"><?=lang('validacion_ok')?><p></h3></div>';
                                                      if(respuesta.accion=='Guardar y presupuestar'){
                                                   
                                                         $('#contenedorGeneral').html(mensajeOk);
                                                                //custom cod_aspirante
                                                                alert(cod_aspirante);
                                                                setTimeout(function(){
                                                                  $.fancybox.close(true);

                                                                        $.fancybox.showLoading();
                                                                        $.ajax({
                                                                            url:'<?=base_url('aspirantes/presupuestar_aspirante')?>',  
                                                                            type:'POST',
                                                                            data:'codigo='+cod_aspirante,
                                                                            cache:false,
                                                                             success:function(respuesta){
                                                                             alert(respuesta);
                                                                                $.fancybox.open(respuesta,{
                                                                                     maxWidth	: 1000,
                                                                                     maxHeight: 1000,
                                                                                     scrolling:'no',
                                                                                     width   	: '55%',
                                                                                     height   : '58%',
                                                                                     autoSize	: false,
                                                                                     padding  : 4
                                                                                });
                                                                             }

                                                                        });
                                                              },1200);
                                                    }else{                                                              
                                                                // se termina la accion y se cierra el fancy
                                                                //Solo guardar
                                                                $('#contenedorGeneral').parent().html(mensajeOk);
                                                                setTimeout(function(){$.fancybox.close(true);},1200);   
                                                    
                                                         }
                                                    break;

                                        }
                                  
                           
                                }
                          });
                            
                    return false;
                });
        
        $(".chosen-select").chosen({
       width: "100%",
       height:"15px"
     
      });
 $.validator.addMethod(
      "Latino",
      function (value, element) {
        // put your own logic here, this is just a (crappy) example 
        return value.match(/^\d\d?\/\d\d?\/\d\d\d\d$/);
      },
      //"Please enter a date in the format dd/mm/yyyy"
      "<?php echo lang('error_fecha');?>"
    );

     // $.validator.setDefaults({ ignore: ":hidden:not(select)" });
       $('form').validate({
              //submitHandler: function() { alert("Submitted!") },
            highlight: function(element) {
               
                 $(element).addClass('test');
                   
             },
            unhighlight: function(element) {
                $(element).removeClass('test');
               
            },
           
            rules:{
                    
                empresa_fijo_aspirante:{
                   Latino:true 
                },
                    apellido:{
                                required:true,
                                
                                maxlength: 100
                            },
                                    
                      email:{
                                required:true,
                                email:true,
                                maxlength: 50  
                            }, 
                     nombre:{
                                required:true,
                            
                                maxlength: 50        
                            },
              
                   prefijo_fijo_aspirante:{
                                
                                number: true
                            },
        numero_fijo_aspirante:{
                            number:true
                    },
                            
        prefijo_cel_aspirante:{
                            number:true
        },
               tel_cel_aspirante:{
           number:true
               }, 
                
                        documento:{
                                
                                number:true,
                                maxlength: 50
                            }, 
                    tipo:{
                                number:true
                    },
               observaciones:{
                                
                                maxlength: 255
                            },            
                  fechanaci:{
                                Latino:true
                            }, 
                calle:{
                        required:true,
                        maxlength: 255
                                
                },
                calle_numero:{
                            required:true,
                            maxlength: 255
                },
                calle_complemento:{
                           
                            maxlength: 255 
                },
              
                  prov_muni:{
                                maxlength: 35
                            },
                         cp:{
                                //required:true,
                                maxlength: 11
                                
                            } ,
                            cod_localidad:{
                            required:true,
                            maxlength:35,
                            digits:true       
                           
                        }
               },messages: {
                            
                                apellido: {
                                            required: "<?=lang('error_requerido');?>",
                                           
                                            maxlength:"<?=lang('error_max_100')?>"
                                          },
                                nombre:   {
                                            required: "<?=lang('error_requerido');?>",
                                          
                                            maxlength:"<?=lang('error_max_50')?>"
                                          },
    prefijo_fijo_aspirante:{
                                            
                                             number: '<?=lang('error_numeros')?>'
                                                
                                                  },
    numero_fijo_aspirante:{
                                            number:'<?=lang('error_numeros')?>'
                                             },
            prefijo_cel_aspirante:{
                                            number:'<?=lang('error_numeros')?>'
            },
                    tel_cel_aspirante:{
                                     number:'<?=lang('error_numeros')?>'
                                     },
			
                                  email:  {
                                            required:"<?=lang('error_requerido');?>",
                                            email:"<?=lang('error_email');?>",
                                            maxlength:"<?=lang('error_max_50');?>"
                                            },
                            
                            
                            tipo:{
                                            
                                            number:'<?=lang('error_numeros')?>'
                                            
                                    },
                                        
                                   documento:{
                                            
                                            number:"<?=lang('error_numeros')?>",
                                            maxlength:"<?=lang('error_max_50');?>"
                                         },
                            observaciones:{
                                             maxlength:"<?=lang('error_max_50');?>"
                                            },
                                       
                                fechanaci:{
                                      
                                            date:"<?=lang('error_fecha');?>"
                                           },
                                calle:{
                                            required:'<?=lang('error_requerido');?>',
                                            maxlength:"<?=lang('error_max_255');?>"
                                            
                                },
                                calle_numero:{
                                            required:'<?=lang('error_requerido');?>',
                                            maxlength:'<?=lang('error_max_255');?>'
                                            },
                                calle_complemento:{
                                        maxlength:'<?=lang('error_max_255');?>'
                                },
                                    
                               
                                prov_muni: {
                                            maxlength:"<?=lang('error_max_35');?>"
                                            },
                  cod_localidad:{
                                            required:'<?=lang('error_requerido');?>',
                                             maxlength:"<?=lang('error_max_35');?>"
                                          
                                            },
                            codpost:{
                                required:'<?=lang('error_requerido');?>',
                                maxlength: '<?=lang('error_max_11');?>'
                                
                            } 
                                
                                            
                            }
                     });
                 });
                 </script>
