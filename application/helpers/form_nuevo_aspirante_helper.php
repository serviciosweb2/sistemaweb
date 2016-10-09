<script>  
$(document).ready(function(){
      $(".chosen-select").chosen({
       width: "100%",
       height:"15px"
     
      });
    
        ///var iconoError="<img src='../assents/img/error.png'>"; 
        $('form').validate({
            
            highlight: function(element) {
               
                 $(element).addClass('test');
                   
             },
            unhighlight: function(element) {
                $(element).removeClass('test');
               
            },
            //errorPlacement: function(error, element) {
            //var position=$(element).position();
            //alert(position.top);
	/*$(element).next().html(error).css({
            'position':'fixed',
            'dispalay':'block',
            'margin-top':(position.top)-25,
            'background':'white'
            
        });*/

                //$('#'+element.attr('id')).parent().next('#prueba').html(error);
              //},
            //success: function(label) {
			//label.remove();         
                        //},                        
            rules:{
                    
                
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
                                date:true
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
                                required:true,
                                maxlength: 11,
                                digits:true
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
                            cp:{
                                required:'<?=lang('error_requerido');?>',
                                maxlength: '<?=lang('error_max_11');?>',
                                digits:'<?=lang('error_max_255');?>'
                            } 
                                
                                            
                            }
                     });
                 });
                 </script>