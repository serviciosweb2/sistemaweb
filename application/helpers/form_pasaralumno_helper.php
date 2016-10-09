<script>
    $(document).ready(function(){
        
        var enviarform=[];
      
        $('#errores_alumnos').hide();
        
        var vista=0;
       
         $('#contenedorGeneral').on('click','.popover',function(){
             //alert('click en el pop');
             vista=1;
         });
        
        $('#contenedorGeneral').on('click',function(){
       console.log($(this));
       var obj=$(this);
       console.log(obj[0]['attributes']['0']['baseURI']);
        if(vista!=1){//vista es  igual a 0
            $('#popdatos').popover('hide');
            $('#pop').popover('hide');
        } else{
            vista=0;
        }   
//         if($('.popover').is(':visible')){
//          alert('visible');
//            
//         }else{
//             alert('vista false');
//          }

    
        });
        
       
        
        
//ESTA FUNCION CARGA EL ARRAY QUE ME DICE QUE FORMULARIO TENGO QUE ENVIAR EN CASO QUE ESTE MODIFICANDO UN ALUMNO           
function enviarfrm(x){
   //alert('llamado');
    var form=$('#'+x.id).attr('data-form');
    //alert(form);
    var x=0;
    $(enviarform).each(function(){

        if(this==form){
               x++; 
        }

    });

    if(x==0){  
       
        enviarform.push(form);   

    }

      
}
 //SETEO LOS FORMULARIOS CUANDO TIENEN UN CAMBIO          
$('#contenedorGeneral form').on({
                                                keydown: function() {
                                                         enviarfrm(this);
                                                },
                                                change: function() {
                                                        enviarfrm(this);
                                                }
                                            });
            
        
$("#responsable").validate({
             
rules: {

        nombre: {
                required:true,
                maxlength: 50
        
        },
        apellido:{
                required:true,
                maxlength: 250
        },
        
       
        tipo_doc:{
                required:true,
                maxlength: 4,
                digits: true
                
        },
        
        calle:{
                required: true,
                maxlength:50
        },
        calle_num:{
                required: true,
                maxlength:50,
                digits: true
        },
        complemento:{
                maxlength:250
                
        },
        documento:{
                required: true,
                maxlength:50
        },
       
        email:{
                email:true, 
                maxlength:50
         },
         dni:{
              required: true,
         },
        prefijo:{
             digits: true
         },
         numero:{
            digits: true 
         },
         tipodetel:{
            digits: true 
         },
         empresatel:{
            digits: true  
         }
    },
messages:{
        nombre:{
               required:'<?=lang('error_requerido')?>',
               maxlength:'<?=lang('error_max_50')?>'
        },
        apellido:{
               required:'<?=lang('error_requerido')?>',
               maxlength:'<?=lang('error_max_250')?>'
        },
        
        tipo_doc:{
                required:'<?=lang('error_requerido')?>',
                maxlength:'<?=lang('error_max_4')?>',
                digits:'<?=lang('error_numeros')?>'
                
        },
        observaciones: {
                maxlength:'<?=lang('error_max_255')?>'
        },
        calle:{
                required:'<?=lang('error_requerido')?>',
                maxlength:'<?=lang('error_max_50')?>'
        },
        calle_num:{
                required:'<?=lang('error_requerido')?>',
                maxlength:'<?=lang('error_max_50')?>',
                digits:'<?=lang('error_numeros')?>'
        },
        complemento:{
                maxlength:'<?=lang('error_max_250')?>'
                
        },
        documento:{
                required:'<?=lang('error_requerido')?>',
                maxlength:'<?=lang('error_max_50')?>'
        },
        localidad:{
                required:'<?=lang('error_requerido')?>',
                maxlength:'<?=lang('error_max_11')?>',
                digits:'<?=lang('error_numeros')?>'
        },
        
        email:{
                email:'<?=lang('error_email')?>',
                maxlength:'<?=lang('error_max_50')?>'
         },
         dni:{
             required:'<?=lang('error_requerido')?>',   
         },        
        prefijo:{
             digits:'<?=lang('error_numeros')?>'
         },
         numero:{
            digits:'<?=lang('error_numeros')?>'
         },
        tipodetel:{
            digits:'<?=lang('error_numeros')?>' 
         },
        empresatel:{
            digits:'<?=lang('error_numeros')?>'  
         }
                
        
    }
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
$("#general").validate({
             
rules: {

        nombre: {
                required:true,
                maxlength: 50
        
        },
        apellido:{
                required:true,
                maxlength: 250
        },
        sexo:{
                
                digits: true
        },
        estado_civil:{
                
                maxlength: 2
        },
        tipoDniAlumno:{
                required:true,
                maxlength: 4,
                digits: true
                
        },
        observaciones: {
                maxlength: 255
        },
        calle_alumno:{// este campo debe tener como nosmbre "calle"
                required: true,
                maxlength:50
        },
        calle_num_alumno:{
                required: true,
                maxlength:50,
                digits: true
        },
        complemento_alumno:{
                maxlength:250
                
        },
        documento:{
                required: true,
                maxlength:50
        },
        localidad:{
                required: true,
                maxlength:11,
                digits: true
        },
        codpost:{
                
                maxlength:50
                
        },
        email_alumno:{
                email:true, 
                maxlength:50
         },
         comonosconocio:{
                maxlength:11
         },
         fechanaci:{
             Latino:true
         },
         barrio:{
            maxlength:255 
         },
         prefijo:{
             digits: true
         },
         numero:{
            digits: true 
         },
         tipodetel:{
            digits: true 
         },
         empresatel:{
            digits: true  
         }
    },
messages:{
        nombre:{
               required:'<?=lang('error_requerido')?>',
               maxlength:'<?=lang('error_max_50')?>'
        },
        apellido:{
               required:'<?=lang('error_requerido')?>',
               maxlength:'<?=lang('error_max_250')?>'
        },
        sexo:{
               
               digits:'<?=lang('error_numeros')?>'
        },
        estado_civil:{
               
               digits:'<?=lang('error_max_2')?>'
        },
        tipoDniAlumno:{
                required:'<?=lang('error_requerido')?>',
                maxlength:'<?=lang('error_max_4')?>',
                digits:'<?=lang('error_numeros')?>'
                
        },
        observaciones: {
                maxlength:'<?=lang('error_max_255')?>'
        },
        calle_alumno:{
                required:'<?=lang('error_requerido')?>',
                maxlength:'<?=lang('error_max_50')?>'
        },
        calle_num_alumno:{
                required:'<?=lang('error_requerido')?>',
                maxlength:'<?=lang('error_max_50')?>',
                digits:'<?=lang('error_numeros')?>'
        },
        complemento_alumno:{
                maxlength:'<?=lang('error_max_250')?>'
                
        },
        documento:{
                required:'<?=lang('error_requerido')?>',
                maxlength:'<?=lang('error_max_50')?>'
        },
        localidad:{
                required:'<?=lang('error_requerido')?>',
                maxlength:'<?=lang('error_max_11')?>',
                digits:'<?=lang('error_numeros')?>'
        },
        codpost:{
                maxlength:'<?=lang('error_max_50')?>'
        },
        email_alumno:{
                email:'<?=lang('error_email')?>',
                maxlength:'<?=lang('error_max_50')?>'
         },
        comonosconocio:{
               maxlength:'<?=lang('error_max_11')?>'
         },
        fechanaci:{
             date:'<?=lang('error_fecha')?>'
         },
        barrio:{
            maxlength:'<?=lang('error_max_255')?>'
         },
        prefijo:{
             digits:'<?=lang('error_numeros')?>'
         },
         numero:{
            digits:'<?=lang('error_numeros')?>'
         },
        tipodetel:{
            digits:'<?=lang('error_numeros')?>' 
         },
        empresatel:{
            digits:'<?=lang('error_numeros')?>'  
         }
                
        
    }
});

     
        
                            //SETEO EL CHOSEN EN LOS TAB'S     
       
        $('#tab1 select').chosen({
        width: "100%"
        });// llamo a chosen en la pestaña activa
        
            $('#mitab a').on('click',function (e) {// cada vez que una ventana pasa a activa llamo a chosen
                
              var div=$(this).attr('href');
                 e.preventDefault();
  
                $(this).tab('show');
                 
                 $(div+' select').each( function(){
                     
                     $(this).chosen({
                         width: "100%"
                     });
                 });
    });
 /////////////////////////////FORMULARIO GENERAL/DATOS/////////////////////////////////////
        
//CUANDO HAY UN CAMBIO EN LOS SELEC DE UBICACION ENTRO EN UN SWITCH
       
       $('#contenedorGeneral').on('change','select',function(){
           var select=$(this).attr('name');
           var valor=$(this).val();
           //alert(select +' '+valor);
           
           switch(select){
               case 'id_lugar_nacimiento':
                     $.ajax({
                         url:'<?=base_url('alumnos/getprovincias')?>',
                         data:'lugarnacimiento='+valor,
                         cache:false,
                         type:'POST',
                         dataType:'json',
                         success:function(respuesta){
                              $("#slct_prov").empty();
                              $('#slct_prov').html('<select class="span12" name="prov"></select>');
                             $(respuesta).each(function(){
                                 //alert(this.id);
                                 
                                $('select[name="prov"]').append('<option value'+this.id+'>'+this.nombre+'</option>');
                                
                             });
                             $('select[name="prov"]').chosen({
                                 width: "100%"
                             });
                         }
                        
                     });
                     
                break;
                
                case 'prov':
                            //alert('case');
                            $.ajax({
                                
                                url:'<?=base_url('alumnos/getlocalidades')?>',
                                data:'idprovincia='+valor,
                                cache:false,
                                type:'POST',
                                dataType:'json',
                                success:function(respuesta){
                                   $('#slct_localidades').empty();
                                   $('#slct_localidades').html('<select class="span12" name="localidad"></select>');
                                    $(respuesta).each(function(){
                                        $('select[name="localidad"]').append('<option value='+this.id+'>'+this.nombre+'</option>');
                                        
                                    });
                                    $('select[name="localidad"]').chosen({
                                        width: "100%"
                                    });
                                }
                            });
                break;
                
                case 'domiciProvincia':
                //alert('entro');
                        $.ajax({
                                
                                url:'<?=base_url('alumnos/getlocalidades')?>',
                                data:'idprovincia='+valor,
                                cache:false,
                                type:'POST',
                                dataType:'json',
                                success:function(respuesta){
                                   $('#slct_domicilocalidad').empty();
                                   $('#slct_domicilocalidad').html('<select class="span12" name="domiciLocalidad"></select>');
                                    $(respuesta).each(function(){
                                        $('select[name="domiciLocalidad"]').append('<option value='+this.id+'>'+this.nombre+'</option>');
                                        
                                    });
                                    $('select[name="domiciLocalidad"]').chosen({
                                        width: "100%"
                                    });
                                }
                            });
                
                
                break;
           }
           return false;
       });
       
       
       
       
    
// SETEO EL TAMAÑO Y EL TIPO DE CONTENIDO DE EL POP     
       $('#popdatos').popover({
            html : true,
            placement:'left' 
       
       });
       
//SETEO LAS OPCIONES  EL VALOR DE LOS SELECT QUE LLEGAN
    $('#contenedortel select').each(function(){// opciones seleccionadas de los selec de telefono
                    var slnom=$(this).attr('name');
                    var slval=$(this).val();
                    
    }); 
    var selectempresa=[];
    var selecttipo=[];
    var num='';
    $('#contenedortel select[name="empresatel"] option').each(function(){// values y name de las opciones del selec empresa
                 
                    var optval=$(this).val();
                    var optname=$(this).text();
                   // alert(optval+' '+optname);
                    var r={
                        'id':optval,
                        'nombre':optname
                        };
                    selectempresa.push(r);
                
    }); 
    
     $('#contenedortel select[name="tipodetel"] option').each(function(){// values y name de las opciones del select tipo
                 
                    var optval=$(this).val();
                    var optname=$(this).text();
                   // alert(optval+' '+optname);
                    var r={
                        'id':optval,
                        'nombre':optname
                        };
                    selecttipo.push(r);
                
    }); 
     //...................................................   

    //CONTENIDO HTML DEL POP DETALLES DE LOS TELEFONOS
    var vermas='';      
    vermas+="<div class='row-fluid' id='popovertelefonosdatos'>";
    vermas+="<div class='span12'><form id='editaragregartel'>";
    vermas+="<div class='span2'><small><?=lang('detalleTel_tipo')?></small><div id='cont_tipotel'><select class='span12' name='tipo_tel'></select></div></div>";
    vermas+="<div class='span2'><small><?=lang('detalleTel_empresa')?></small><div id='cont_empresa'><select class='span12' name='empresa'></select></div></div>";
    vermas+="<div class='span2'>";
    vermas+="<small><?=lang('detalleTel_prefijo')?></small><input type='text' class='span12' name='prefijo'>";
    vermas+="</div>";
    vermas+="<div class='span2'><small><?=lang('detalleTel_numero')?></small>";
    vermas+="<input type='text' class='span12' name='numero'></div>";
    vermas+="<input type='submit' class='btn span12' value='agregar' name='agregar'>";
    vermas+="</form><table id='listadoteldatos' class='table table-bordered table-condensed table table-striped'><thead><th><?=lang('detalleTelTabla_tipo')?></th><th><?=lang('detalleTelTabla_empresa')?></th><th><?=lang('detalleTelTabla_numero')?></th><th><?=lang('detalleTelTabla_eliminar')?></th></thead><tbody></tbody></table></div></div>";
    
  $('#popdatos').attr('data-content',vermas);
    
 // FUNCIONES DE AGREGAR MAS TELEFONOS GENERAL/DATOS

 var x='';
 var telGeneral=[];//IMPORTANTE!!!LOS TELEFONOS SE TRABAJAN ACA , Y DESPUES SE ACTUALIZA EL INPUT HIDDEN 
 var modificando='';// BANDERA PARA SABER A QUE TR ESTOY MODIFICANDO
 
 //DECLARACION DE FUNCIONES
function dibujarTelDatos(){
              var empresa='';
              var tipo='';
              var empresavalor='';
              var tipovalor='';
               $('#listadoteldatos tbody').empty();
                $(telGeneral).each(function(){
                 if(this.baja!='1'){
                   
                   empresavalor=this.empresaVal;
                   tipovalor=this.tipoVal;
                   
               
            
                $('select[name="empresa"] option').each(function(){
                       //alert('valor del select-> '+$(this).attr('value'));
                       if(empresavalor==$(this).attr('value')){
                           empresa=$(this).text();
                       }
                   });
                   
                   
                $('select[name="tipo_tel"] option').each(function(){
                       //alert('valor del select Tipo-> '+$(this).attr('value'));
                       
                       
                       if(tipovalor==$(this).attr('value')){
                         tipo=$(this).text(); 
                       }    
                     
                   });
                 
                    //$('#listadoteldatos tbody').append('<tr codigo="'+this.row+'"><td class="tipoTelAlumno">'+this.tipoNom+'</td><td class="empresaTelAlumno">'+this.empresaNom+'</td><td class="numTelAlumno">'+this.prefijo+'-'+this.numero+'</td><td><a name="eliminarTelAlumno" href="#">x<a></td></tr>');
                    $('#listadoteldatos tbody').append('<tr codigo="'+this.row+'"><td class="tipoTelAlumno">'+tipo+'</td><td class="empresaTelAlumno">'+empresa+'</td><td class="numTelAlumno">'+this.prefijo+'-'+this.numero+'</td><td><a name="eliminarTelAlumno" href="#">x<a></td></tr>');               
                    $("#editaragregartel input[type='text']").val('');
                    }else{
                       // alert('este valor no se dibuja-> '+this.baja);
                    }
                });

                
            }   
            
function agregarTelDatos(){
    
                x++;
              
                var registro={
                    'row':x,
                    'codigo':'-1',
                    'empresaVal':$("#cont_empresa select[name='empresa']").val(),
                    'empresaNom':$("#cont_empresa select[name='empresa'] option:selected").text(),
                    'tipoVal':$("#cont_tipotel select[name='tipo_tel']").val(),
                    'tipoNom':$("#cont_tipotel select[name='tipo_tel'] option:selected").text(),
                    'prefijo':$("#editaragregartel input[name='prefijo']").val(),
                    'numero':$("#editaragregartel input[name='numero']").val(),
                    'baja':'0'
                };
                
                telGeneral.push(registro);
                // UNA VEZ QUE HAGO EL PUSH GUARDO EL JSON EN UN INPUT HIDDEN 
                $('#general input[name="telefonosAlumno"]').val(JSON.stringify(telGeneral));
            }
            
function modificarTelDatos(){
               $(telGeneral).each(function(){
                    
                    if(modificando==this.row){
                       
                       this.numero=$("#editaragregartel input[name='numero']").val();
                       this.prefijo=$("#editaragregartel input[name='prefijo']").val();
                       this.tipoNom=$("#cont_tipotel select[name='tipo_tel'] option:selected").text();
                       this.tipoVal=$("#cont_tipotel select[name='tipo_tel']").val();
                       this.empresaNom=$("#cont_empresa select[name='empresa'] option:selected").text();
                       this.empresaVal=$("#cont_tipotel select[name='tipo_tel']").val();
                    }
                });
                $('#general input[name="telefonosAlumno"]').val(JSON.stringify(telGeneral));
            }
        
function borrarTelDatos(){
        //alert('entra');    
        $(telGeneral).each(function(key,value){
                   //alert('modificando->'+modificando+' value->'+value.row);
                    if(modificando==value.row){
                    //alert('pasa al if');
                     // alert('modificando->'+modificando+' value codigo->'+value.codigo);
                    if(value.codigo=='-1'){
                     //   alert('pasa al otro if');
                        telGeneral.splice(key,1);
                        modificando='';
                        }else{
                            value.baja='1';
                            modificando='';
                        }
                }
              });
            $('#editaragregartel input[name="telefonos"]').val(JSON.stringify(telGeneral));
        }
            
function inputTelDatos(){
        
        
        $(telGeneral).each(function(){
                    
                    if(modificando==this.row){
                     
                    $('#editaragregartel input[name="prefijo"]').val(this.prefijo);
                    $('#editaragregartel input[name="numero"]').val(this.numero);
                    
                    }
                });
        
        
        
    }
 //.................................
 
 
 
//RECUPERO TODOS LOS TELEFONOS DEL ASPIRANTE : se generan en un input hidden cuando carga la vista
$('#contenedorGeneral .tel').each(function(){
               var valueJSON=JSON.parse($(this).val());
                x++;
                //alert(valueJSON.baja+' '+valueJSON.codigo);
                var registro={
                    'row':x,
                    'codigo':valueJSON.codigo,
                    'baja':valueJSON.baja,
                    'empresaVal':valueJSON.empresaVal,
                    'empresaNom':valueJSON.empresanom,
                    'tipoVal':valueJSON.tipoid,
                    'tipoNom':valueJSON.tiponombre,
                    'prefijo':valueJSON.prefijo,
                    'numero':valueJSON.numero
                };
               
                telGeneral.push(registro);
            
                
}); 
 
 
   //EVENTO CLIK CUANDO EL USUARIO QUIERE VER EL DETALLE DE TELEFONOS
    $('#popdatos').on('click',function(){
       popVisible='1';
        var form=[];
       form['id']='general';
        enviarfrm(form);
        $('#popovertelefonosdatos').ready(function(){
             $('#editaragregartel').validate({
 rules:{
        prefijo:{
            digits:true,
            required:true
        },
        numero:{
            digits:true,
            required:true
        },
        empresa:{
           digits:true 
        },
        tipo_tel:{
           digits:true 
        }
 },
 messages:{
        prefijo:{
            digits:'<?=lang('error_numeros')?>',
            required:'<?=lang('error_requerido')?>'
        },
        numero:{
            digits:'<?=lang('error_numeros')?>',
            required:'<?=lang('error_requerido')?>'        
        },
        empresa:{
           digits:'<?=lang('error_numeros')?>'
        },
        tipo_tel:{
          digits:'<?=lang('error_numeros')?>'
        }    
 
 }
 });  
            //UNA VEZ QUE LA CAPA ESTA CARGADA CARGO LOS SELECT
            var empresas=$('input[name="nombresDeEmpresas"]').val();
            var tipoTelefonos=$('input[name="tiposDeTelefono"]').val();
            $('#cont_tipotel').empty().html("<select class='span12' name='tipo_tel'></select>");
            $("#cont_tipotel select[name='tipo_tel']").append(tipoTelefonos);
         
          //alert(tipoTelefonos);
            $("#cont_tipotel select[name='tipo_tel']").chosen({
                width: "100%"
            });
         
         
         
            $('#cont_empresa').empty().html("<select class='span12' name='empresa'></select>");
             $("#cont_empresa select[name='empresa']").append(empresas);
//            $(selectempresa).each(function(){
//          
//                $("#cont_empresa select[name='empresa']").append('<option value="'+this.id+'">'+this.nombre+'</option>');
//         
//            });
            $("#cont_empresa select[name='empresa']").chosen({
            width: "100%"    
            });
         
         //........
    
            
            
// PASO A LISTAR TODOS LOS TELEFONOS QUE TENGA EL ALUMNO/ASPIRANTE
   
    $('#popovertelefonosdatos select').chosen({
    width: "100%"    
    });
        dibujarTelDatos();      
           
  //...............         
           
           
       // CLICK EN TR DE  "AGREGAR/EDITAR TELEFONOS" DE DATOS  
        $('#listadoteldatos tbody').on('click','.tipoTelAlumno,.empresaTelAlumno,.numTelAlumno',function(){
        // codigo del tr que voy a editar
      
        var codTr=$(this).parent().attr('codigo');
     
        modificando=codTr;
        inputTelDatos();// rellena los input's con las opciones del seteadas del usuario
        
var optionTipo='';
        $("#cont_tipotel select[name='tipo_tel'] option").each(function(){
           var nombreTel=$(this).text();
           var valorTel=$(this).attr('value');
            
            
             if(telGeneral[modificando-1].tipoNom==nombreTel){
              optionTipo+='<option value="'+valorTel+'" selected>'+nombreTel+'</option>';
              
            }else{
                optionTipo+='<option value="'+valorTel+'">'+nombreTel+'</option>';
                 
            }
         });
     
        /////se arma el select empresa con los valores listados
       
        $('#cont_tipotel').empty().html("<select class='span12' name='tipo_tel'></select>");
         $("#cont_tipotel select[name='tipo_tel']").append(optionTipo);
        $("#cont_tipotel select[name='tipo_tel']").chosen({width: "100%"});
       ///..
        
       var optionEmpresa='';
        $("#cont_empresa select[name='empresa'] option").each(function(){
           var nombreTel=$(this).text();
           var valorTel=$(this).attr('value');
            
            //alert(telGeneral[modificando-1].empresaNom);
             if(telGeneral[modificando-1].empresaNom==nombreTel){
              optionEmpresa+='<option value="'+valorTel+'" selected>'+nombreTel+'</option>';
              
            }else{
                optionEmpresa+='<option value="'+valorTel+'">'+nombreTel+'</option>';
                 
            }
         });
     
        /////se arma el select empresa con los valores listados
       
        $('#cont_empresa').empty().html("<select class='span12' name='empresa'></select>");
         $("#cont_empresa select[name='empresa']").append(optionEmpresa);
        $("#cont_empresa select[name='empresa']").chosen({width: "100%"});
       ///..
    
        //se arma el select tipo con los valores listados
        
//        $('#cont_empresa').empty().html("<select class='span12' name='empresa'></select>");
//        $(selectempresa).each(function(){
//            if(telGeneral[modificando-1].empresaNom==this.nombre){
//             
//               $("#cont_empresa select[name='empresa']").append('<option value="'+this.id+'" selected>'+this.nombre+'</option>');
//            }else{
//                
//                 $("#cont_empresa select[name='empresa']").append('<option value="'+this.id+'">'+this.nombre+'</option>');
//            }
//        
//        });
         $("#cont_empresa select[name='empresa']").chosen({width: "100%"});
        //....
    
    
        $('#editaragregartel input[type="submit"]').val('modificar');
      });
         // submit de editar/agregar telefonos
         $ ('#listadoteldatos tbody').on('click','a[name="eliminarTelAlumno"]',function(){
            //alert($(this).parent().parent().attr('codigo'));
            modificando=$(this).parent().parent().attr('codigo');
            borrarTelDatos();
            dibujarTelDatos();
return false;
});
        $('#contenedorGeneral #popovertelefonosdatos').on('submit','#editaragregartel',function(){
        if(modificando==''){
            num++;
            //entra aca si lo que se esta  insertando un nuevo tel
                agregarTelDatos();
                $("#editaragregartel input[type='text']").val('');
                modificando='';
            }else{
         
                modificarTelDatos();
                $('#editaragregartel input[type="submit"]').val('agregar');
                $("#editaragregartel input[type='text']").val('');

                 modificando='';
               
            }
           
          
             dibujarTelDatos();
            return false;
        });
        
        });
        return false;
    });
       

       
/////////////////////////////////SUBMIT GENERAL/////////  
function mensajeOK(){
    //alert('llama');
       if($("#errores_alumnos" ).is( ":not(':visible')" )){
                                   // alert('cierro');
                            $('#contenedorGeneral').empty().html('<h2>Guardado Correctamente</h2>');
                            
                            setTimeout(function(){$.fancybox.close(true);},1050);   
                        }
    }    

var codigoAspirante='';
var codigoAlumno='';
$('.confirmarenvio').on('click',function(){
//alert('click');
//var errores=['1'/*alumnos*/,'1'/*responsables*/,'1'/*razon*/];

//alert('ok');
$('#errores_alumnos').hide();
$('#errores_alumnos').empty();

   codigoAspirante=$('input[name="codigoAspirante"]').val();

     codigoAlumno=$('input[name="codigo"]').val();
    
 $('.popover').hide();
         $('#general').submit();        

       /* if(codigoAlumno=='-1'){ // si es aspirante o totalmente nuevo  
            $('#general').submit();
            //alert('ok');
            return false;
        
        }else{
                 
            function enviosIndividuales(nombre){
                  // datosenvio=$('#general').serialize();
                    switch(nombre){
                        
                            case 'general':
                               
                              $('#general').submit();
                                
 
                            break;
                        
                        
                        case'listadoResponsables':
                            
                             var datosenvio=$('#listadoResponsables').serialize();
                             alert(datosenvio);
                             $.ajax({
                                 
                                 url:'<?//=base_url('alumnos/guardarResponsable')?>',
                                 data:datosenvio,
                                 type:'POST',
                                 dataType:"json",
                                 cache:false,
                                 success:function(respuesta){
                                     //alert(respuesta.estado);
                                     if(respuesta.estado=='0'){
                                        // alert('ok');
                                         
                                        
                                        //$('#errores_alumnos').hide();
                                        setTimeout(function(){mensajeOK();},300); 
                    }else{
                       
                          if($("#errores_alumnos" ).is( ":visible" )){
                            $('#errores_alumnos').append(respuesta.respuesta).show();  
                        }else{
                            $('#errores_alumnos').fadeIn();
                            $('#errores_alumnos').append(respuesta.respuesta).show();   
                            
                    }
                    
                    }
                    
            
                }
                                 
                });
                            break;
                        
                        
                          default:
                        //listadoRazones
                            var datosenvio=$('#listadoRazones').serialize();
                             $.ajax({
                                 
                                 url:'<?//=base_url('alumnos/guardarRazonSocial')?>',
                                 data:datosenvio,
                                 type:'POST',
                                 dataType:'json',
                                 cache:false,
                                         success:function(respuesta){
                                     //alert(respuesta.estado);
                                     if(respuesta.estado=='0'){
                                        // alert('ok');
                                       setTimeout(function(){mensajeOK();},300);
                                        
                                        //$('#errores_alumnos').hide();
                    }else{
                      
                        
                        alert(respuesta.respuesta);
                          if($("#errores_alumnos" ).is( ":visible" )){
                            $('#errores_alumnos').append(respuesta.respuesta).show();  
                        }else{
                            $('#errores_alumnos').fadeIn();
                            $('#errores_alumnos').append(respuesta.respuesta).show();   
                            
                    }
                }
            }
                                 
        });
        alert('default');
                        
    }// fin del switch
     
     
     
     
}               // fin de la funcion envios individuales
            
            // SI LA PERSONA ES ALUMNO
            alert('envio individuales');
            $(enviarform).each(function(key,value){
                            var valor=String(this);
                            enviosIndividuales(valor);
                            var numUltimoForm=enviarform.length;
            //                     if(key==numUltimoForm-1){
            //                         
            //                         mensajeOK();
            //                    }  

                                }); 


            return false;
        }*/


 

return false;
});

$('.cancelarenvio').on('click',function(){
$.fancybox.close(true);
return false;
});


$("#contenedorGeneral").on('submit','#general',function(){
    
  // rescato los valores del unico telefono que se ve en pantalla de datos
        $('#general input[name="prefijo"]').val();
        $('#general input[name="prefijo"]').attr('id');
        x++;
        

             
        $('#general input[name="telefonosAlumno"]').val(JSON.stringify(telGeneral));
        function altaPrimerTel(){
        //$('#general input[name="prefijo"]').val();
        //$('#general input[name="prefijo"]').attr('id');
        x++;
        if($('#general input[name="prefijo"]').is(':visible')){
            //alert('ok');
            var tipoId=$('#general select[name="tipodetel"]').val();
            var empresa=$('#general select[name="empresatel"]').val();
            var prefijo=$('#general input[name="prefijo"]').val();
            var numero=$('#general input[name="numero"]').val();
             //alert('tipo=>'+tipoId+'\n'+'empresa=>'+empresa+'\n'+'prefijo=>'+prefijo+'\n'+'numero=>'+numero);
            if(tipoId!='' && empresa!='' && prefijo!='' && numero!=''){
               //alert('telefonos completos');
               
                var registro={
                            'row':x,
                            'codigo':'-1', // momentanamente lo puse en -1
                            'empresaVal':$("#general select[name='empresatel']").val(),
                            'tipoVal':$("#general select[name='tipodetel']").val(),
                            'prefijo':$("#general input[name='prefijo']").val(),
                            'numero':$("#general input[name='numero']").val(),
                            'baja':'0'
                         };
       
        //telGeneral.splice(0,0,registro);// pongo el telefono que se ve en pantalla en primer lugar del objeto
            telGeneral[0]=registro;
             $('#general input[name="telefonosAlumno"]').val(JSON.stringify(telGeneral));
        //telGeneral.splice(0,1);
        }
               
               
            }
           
       
        
       
        }
        
        
        //alert('se envia todo ');
                altaPrimerTel();
               var datosenvio=$('#general,#listadoResponsables,#listadoRazones').serialize();
              alert (datosenvio);
                
                $.ajax({
                url:'<?=base_url('alumnos/guardar')?>',
                dataType:'json',
                data:datosenvio,
                type:'POST',
                success:function(respuesta){
                 //alert(respuesta);
                   //alert(respuesta['respuesta']['cod_lugar_nacimiento']);
                    if(respuesta.codigo==1){
                       
                            setTimeout(function(){mensajeOK();},300);
                    }else{
                        $('#errores_alumnos').append(respuesta.respuesta);
                        $('#errores_alumnos').fadeIn();
//                        if($("#errores_alumnos" ).is( ":visible" )){
//                            $('#errores_alumnos').append(respuesta.respuesta);  
//                        }else{
//                            $('#errores_alumnos').append(respuesta.respuesta);   
//                            $('#errores_alumnos').fadeIn();
//                    }
                }
                    
                    // hay que ver que pasa con el array que tiene los datos de los form
                }
                
            });
        
        
        
        
        
        
        
        
        
        /*if(codigoAlumno=='-1'){//si es un nuevo alumno
                //alert('se envia todo ');
                altaPrimerTel();
               var datosenvio=$('#general,#listadoResponsables,#listadoRazones').serialize();
              alert (datosenvio);
                
                $.ajax({
                url:'//=base_url('alumnos/guardar')?>',
//                dataType:'json',
                data:datosenvio,
                type:'POST',
                success:function(respuesta){
                 alert(respuesta);
                   //alert(respuesta['respuesta']['cod_lugar_nacimiento']);
                    if(respuesta.estado==0){
                       
                            setTimeout(function(){mensajeOK();},300);
                    }else{
                        
                        
                        if($("#errores_alumnos" ).is( ":visible" )){
                            $('#errores_alumnos').append(respuesta.respuesta);  
                        }else{
                            $('#errores_alumnos').append(respuesta.respuesta);   
                            $('#errores_alumnos').fadeIn();
                    }
                }
                    
                    // hay que ver que pasa con el array que tiene los datos de los form
                }
                
            });
                
                
           }else{
               //alert('solo general');
               var datosenvio=$('#general').serialize();
               //alert('alertdesde el onSubmit->'+datosenvio);
               $.ajax({
                url:'//=base_url('alumnos/guardarDatosAlumnos')?>',
                dataType:'json',
                data:datosenvio,
                type:'POST',
                success:function(respuesta){
                    //alert(respuesta.estado);
                    if(respuesta.estado=='0'){
                       // alert('ok');
                       
                       setTimeout(function(){mensajeOK();},300);
                       // $('#errores_alumnos').hide();
                    }else{
                        
                        
                          if($("#errores_alumnos" ).is( ":visible" )){
                            $('#errores_alumnos').append(respuesta.respuesta);  
                        }else{
                            $('#errores_alumnos').append(respuesta.respuesta);   
                            $('#errores_alumnos').fadeIn();
                    }
                    }
                    // hay que ver que pasa con el array que tiene los datos de los form
                }
                
            });
           }*/
        
                
               return false;
                
                }); 


















////////////////////////////////////////////////RESPONSABLE//////////////////////////////////////

$('#pop').popover({ // seteo la capa que me muestra el detalle de telefonos
    html : true,
    placement:'left' 
}); 

var n=''; 
var i='';
var numerador=0;
var editando='';

 //LISTADO DE FUNCIONES
 var telRes=[];//telefonos de los responsables
function agregartelResp(editando){
n++;
 telRes=[];//telefonos de los responsables
var listado= $('#'+editando).find('.telefonos input[type="hidden"]').val();//TOMO LOS TELEFONOS DEL INPUT HIDDEN DEL TR("VAR editando")SEGUN CORRESPONDA



if(listado!=''){
    
alert('listadoVacio');
        var listadoArray=JSON.parse(listado);

        var valor=JSON.stringify(telRes);

        var registro={
                'row':n,
                'tr':editando,
                'empresaVal':$("#responsable select[name='empresa']").val(),
                'empresaNom':$("#responsable select[name='empresa'] option:selected").text(),
                'tipoVal':$("#responsable select[name='tipo_tel']").val(),
                'tipoNom':$("#responsable select[name='tipo_tel'] option:selected").text(),
                'prefijo':$("#responsable input[name='prefijo']").val(),
                'numero':$("#responsable input[name='numero']").val()
            };

        listadoArray.push(registro);
                
                

 }else{
    
        var registro={
                'row':n,
                'codigo':'-1',
                'empresaVal':$("#responsable select[name='empresa']").val(),
                'empresaNom':$("#responsable select[name='empresa'] option:selected").text(),
                'tipoVal':$("#responsable select[name='tipo_tel']").val(),
                'tipoNom':$("#responsable select[name='tipo_tel'] option:selected").text(),
                'prefijo':$("#responsable input[name='prefijo']").val(),
                'numero':$("#responsable input[name='numero']").val(),
                'baja':'0'
            };

        telRes.push(registro);
        var valor=JSON.stringify(telRes);
        $('#'+editando).find('.telefonos input[type="hidden"]').val(valor);
        $('#'+editando).find('.vistaTel').html(telRes[0].prefijo+'-'+telRes[0].numero);//muestro siempre el primer tel
         
              
               
    }             
}

function agregartelResp_desp(editando){

var telRes_desp=[];// telefonos de los responsables que se ven el el pop desplegable

var listado= $('#'+editando).find('.telefonos input[type="hidden"]').val();

var listadoArray=JSON.parse(listado);

var valor=JSON.stringify(listadoArray);

  
var registro={
                    'row':n,
                    'codigo':'-1',
                    'empresaVal':$("#editaragregartel select[name='empresa']").val(),
                    'empresaNom':$("#editaragregartel select[name='empresa'] option:selected").text(),
                    'tipoVal':$("#editaragregartel select[name='tipo_tel']").val(),
                    'tipoNom':$("#editaragregartel select[name='tipo_tel'] option:selected").text(),
                    'prefijo':$("#editaragregartel input[name='prefijo']").val(),
                    'numero':$("#editaragregartel input[name='numero']").val(),
                    'baja':'0'
                };

                listadoArray.push(registro);
                
                $('#'+editando).find('.telefonos input[type="hidden"]').val(JSON.stringify(listadoArray));
}

function modificarTelResp(editando){
//MODIFICACION DEL PRIMER TELEFONO
alert('modificar');
var listado=$('#'+editando).find('.telefonos input[type="hidden"]').val();
if(listado!=''){
var obj=JSON.parse(listado);



$(obj[0]).each(function(){
   
                  
        this.empresaVal=$("#responsable select[name='empresa']").val();
        this.empresaNom=$("#responsable select[name='empresa'] option:selected").text();
        this.tipoVal=$("#responsable select[name='tipo_tel']").val();
        this.tipoNom=$("#responsable select[name='tipo_tel'] option:selected").text();
        this.prefijo=$("#responsable input[name='prefijo']").val();
        this.numero=$("#responsable input[name='numero']").val();




        var contenidoJSON=JSON.stringify(obj);
        $('#'+editando).find('.telefonos input[type="hidden"]').val(contenidoJSON);
        $('#'+editando).find('.vistaTel').html(obj[0].prefijo+'-'+obj[0].numero);
});
}else{
    
    
     var registro={
                'row':n,
                'codigo':'-1',
                'empresaVal':$("#responsable select[name='empresa']").val(),
                'empresaNom':$("#responsable select[name='empresa'] option:selected").text(),
                'tipoVal':$("#responsable select[name='tipo_tel']").val(),
                'tipoNom':$("#responsable select[name='tipo_tel'] option:selected").text(),
                'prefijo':$("#responsable input[name='prefijo']").val(),
                'numero':$("#responsable input[name='numero']").val(),
                'baja':'0'
            };

        telRes.push(registro);
        var valor=JSON.stringify(telRes);
        $('#'+editando).find('.telefonos input[type="hidden"]').val(valor);
        $('#'+editando).find('.vistaTel').html(telRes[0].prefijo+'-'+telRes[0].numero);//muestro siempre el primer tel
    
    
}
}

function dibujarDetalleTel(editando){
//DIBUJA EL LISTADO DE TELEFONOS EL EL POP

var valor=$('#'+editando).find('.telefonos input[type="hidden"]').val();
//alert('entro');
var lista=JSON.parse(valor);

$('#listadotel tbody').empty();
$(lista).each(function(){
    
    var tipo='';
    var empresa='';
    
    var empresaValor=this.empresaVal;
    var tipoValor=this.tipoVal;
    
    //alert(empresaValor +' '+tipoValor);
    
    
    //each de tipo:
    $('select[name="tipo_tel"] option').each(function(){
        if(tipoValor==$(this).attr('value')){
            tipo=$(this).text();
        }
    });
    
    // each de empresa
    $('select[name="empresa"] option').each(function(){
        if(empresaValor==$(this).attr('value')){
            empresa=$(this).text();
        }
    });
  
    n=this.row;//
    $('#listadotel tbody').append('<tr id="'+this.row+'"><td class="tipoNomDetalle">'+tipo+'</td><td class="empresaDetalle">'+empresa+'</td><td class="telefonosDetalle">'+this.prefijo+'-'+this.numero+'</td><td class="borrarTelResp"><a href="#">x</a></td></tr>');
});



}

function modificarDetalleTel(tr){


var dattaJSON=$('#'+editando).find('.telefonos input[type="hidden"]').val();

var listadoTel=JSON.parse(dattaJSON);

$(listadoTel).each(function(){
    if(this.row==tr){
       // alert('entro al if-> '+this.row+' = '+tr);
                    this.empresaVal=$("#editaragregartel select[name='empresa']").val();
                    this.empresaNom=$("#editaragregartel select[name='empresa'] option:selected").text();
                    this.tipoVal=$("#editaragregartel select[name='tipo_tel']").val();
                    this.tipoNom=$("#editaragregartel select[name='tipo_tel'] option:selected").text();
                    this.prefijo=$("#editaragregartel input[name='prefijo']").val();
                    this.numero=$("#editaragregartel input[name='numero']").val();
        
        
    }
});

$('#'+editando).find('.telefonos input[type="hidden"]').val(JSON.stringify(listadoTel));
}



$('#pop').hide();


var datos=[];

//SUBMIT RESPONSABLE/////////////////////
$("#contenedorGeneral").on('submit','#responsable',function(){

   //genero un array con los valores de los inputs
    $('#responsable input').each(function(){
    datos[$(this).attr('name')]=$(this).val();
});

        // genero un array con los valores de los select
$('#responsable select').each(function(){
    
    datos[$(this).attr('name')]=$(this).val();
});

$('#responsable input[type="hidden"]').each(function(){
   
    
    datos[$(this).attr('name')]=$(this).val();
  
});

//DESPUES DE OBTENER LOS VALORES SE PASA A DIBUJAR EL TR SEGUN; SI ES UNA MODIFICACION O SE ES UNO NUEVO RESP.
var ultimoTr=$('#tablaresponsables tbody').find('tr:last').attr('id');// busco el id del ultimo tr
if(ultimoTr!=undefined){
 // esta condicion se ejecuta uan sola vez. Cuando la tabla se carga con registros seteo el numerador de tr'S para saber como sigo numerando    
    var partes=ultimoTr.split("o");   
    var z=parseInt(partes[2],8);
    z++;
    numerador=z;
}


  

var nuevoResp='<tr id="codigo'+numerador+'"><td class="nombre">'+datos.nombre+' '+datos.apellido +'<input type="hidden" name="nombreRes[]" value="'+datos.nombre+'|'+datos.apellido+'"><input type="hidden" value="-1" name="codigoRes[]"><input type="hidden" value="0" name="bajaRes[]"></td>';
nuevoResp+='<td class="razon">'+datos.razon+'<input type="hidden" name="razon[]" value="'+datos.razon+'"></td><td class="calle">'+datos.calle+'<input type="hidden" name="calle[]" value="'+datos.calle+'"></td><td class="calle_num">'+datos.calle_num+'<input type="hidden" name="calle_num[]" value="'+datos.calle_num+'"></td><td class="complemento">'+datos.complemento+'<input type="hidden" name="complemento[]" value="'+datos.complemento+'"></td>';
nuevoResp+='<td class="dni">'+datos.dni+'<input type="hidden" name="tipo_doc[]" value="'+datos.tipo_doc+'"><input type="hidden" name="dni[]" value="'+datos.dni+'"></td><td class="email">'+datos.email+'<input type="hidden" value="'+datos.email+'" name="email[]"></td><td id="telefonos" class="telefonos"><input type="hidden" name="telefonos[]" value=""><div class="vistaTel"></div></td><td class="borrar"><a  name="borrarResponsable" href="#">x</a></td></tr>';

if(editando==''){

                editando='codigo'+numerador;
                $('#tablaresponsables').append(nuevoResp);
                agregartelResp(editando);

}else{
        //MODIFICACION

                var nuevosnum='';
                modificarTelResp(editando);
                $('#responsable input[name="guardar"]').attr('value','agregar');
                var nuevo=$('#'+editando+' .telefonos').html();
                
                var modResp='<td class="nombre">'+datos.nombre+' '+datos.apellido +'<input type="hidden" name="nombreRes[]" value="'+datos.nombre+'|'+datos.apellido+'"><input type="hidden" value="'+datos.codigoResponsable+'" name="codigoRes[]"><input type="hidden" value="'+datos.bajaResponsable+'" name="bajaRes[]"></td>';
                modResp+='<td class="razon">'+datos.razon+'<input type="hidden" name="razon[]" value="'+datos.razon+'"></td><td class="calle">'+datos.calle+'<input type="hidden" name="calle[]" value="'+datos.calle+'"></td><td class="calle_num">'+datos.calle_num+'<input type="hidden" name="calle_num[]" value="'+datos.calle_num+'"></td><td class="complemento">'+datos.complemento+'<input type="hidden" name="complemento[]" value="'+datos.complemento+'"></td>';
                modResp+='<td class="dni">'+datos.dni+'<input type="hidden" name="tipo_doc[]" value="'+datos.tipo_doc+'"><input type="hidden" name="dni[]" value="'+datos.dni+'"></td><td class="email">'+datos.email+'<input type="hidden" value="'+datos.email+'" name="email[]"></td><td id="telefonos" class="telefonos">'+nuevo+'</td><td class="borrar"><a href="#" name="borrarResponsable">x</a></td>';
                $('#'+editando).html(modResp);    
                $('#tablaresponsables').css('opacity','1');
     }
    
    
   
    
    $('#responsable input[type="text"]').val('');
        numerador++;
        editando='';
        $('#pop').hide();          
        return false;
    });
       


// CLICK EN TR DE LAS LISTA GENERAL DE RESPONSABLES. OBTENGO LOS VALORES PARA RELLENAR LOS INPUT'S
$('#tablaresponsables tbody ').on('click','tr td:not(".borrar")',function(){
          //alert($(this).parent().attr('id'));
         
             var codigoTR='#'+$(this).parent().attr('id');
            $('#tablaresponsables tbody tr').removeClass('tr');
            $(codigoTR).addClass('tr');
            $('#responsable input[name="guardar"]').attr('value','modificar');
            $('#tablaresponsables').css('opacity','0.4');
            editando=$(codigoTR).attr('id');
            var bajaResponsable=$(codigoTR).find('input[name="bajaRes[]"]').val();
            var codigoResponsable=$(codigoTR).find('input[name="codigoRes[]"]').val();
           // alert('codigo-> '+codigoResponsable+'  baja-> '+bajaResponsable);
            
            var dattaNom=$(codigoTR).find('.nombre input[type="hidden"]').val();
            var nomsplit=dattaNom.split('|');
            
            var numerosTel=$('#'+editando).find('.telefonos input[type="hidden"]').val();
            //alert(numerosTel);
             var arrayTel='';
            var tipotel='';
            var empresatel='';
            if(numerosTel!=''){
               
                 arrayTel=JSON.parse(numerosTel);
                 tipotel=arrayTel[0].tipoVal;
                 empresatel=arrayTel[0].empresaVal;
                $('#responsable input[name="prefijo"]').val(arrayTel[0].prefijo);//INSERTO PREFIJO Y NUMERO DE TEL EN LOS INPUT's
                $('#responsable input[name="numero"]').val(arrayTel[0].numero);
                $('#pop').show();
            }
            
            
            
            var apellido=nomsplit[1];
            var nombre=nomsplit[0];
            var email=$(codigoTR).find('.email').text();
            var calle=$(codigoTR).find('.calle').text();
            var complemento=$(codigoTR).find('.complemento').text();
            var callenum=$(codigoTR).find('.calle_num').text();
            var dni=$(codigoTR).find('.dni').text();
            var razon=$(codigoTR).find('.razon input[name="razon[]"]').val();
            var tipodoc=$(codigoTR).find('.dni input[name="tipo_doc[]"]').val();
            
          
            
            $('#responsable input[name="bajaResponsable"]').val(bajaResponsable);
            $('#responsable input[name="codigoResponsable"]').val(codigoResponsable);
            $('#responsable input[name="email"]').val(email);
            $('#responsable input[name="nombre"]').val(nombre);
            $('#responsable input[name="apellido"]').val(apellido);
            $('#responsable input[name="complemento"]').val(complemento);
            $('#responsable input[name="calle"]').val(calle);
            $('#responsable input[name="complemento"]').val(complemento);
            $('#responsable input[name="calle_num"]').val(callenum);
            $('#responsable input[name="dni"]').val(dni);
            $('#responsable input[name="razon"]').val(razon);
            
        
            
            
           
           if(arrayTel[0].prefijo!=''){
               $('#pop').show();
               //si tiene mas de un telefono muestro el icono de "ver detalle de telefonos"
           }
            
// recupero el estado de los seelct al modificar: 
///////RECUPERO LOS DATOS DEL SELECT TIPO_DOC
            var nuevoSelect='';
            $('select[name="tipo_doc"] option').each(function(){
             //toma las opciones del select
               var idOption=$(this).attr('value');
               var nomOption=$(this).text();
               
               if(idOption==tipodoc){
                   // compara con el valor del usuario
                 nuevoSelect+='<option value="'+idOption+'" selected>'+nomOption+'</option>' ;  
         
        }else{
                   nuevoSelect+='<option value="'+idOption+'">'+nomOption+'</option>';
             
               }
              
            });
            // y ahora actualiza el select
            $('#conten_slct_doc').empty().html('<select class="span12" name="tipo_doc"></select>');
            $('select[name="tipo_doc"]').append(nuevoSelect);
            $('select[name="tipo_doc"]').chosen({width: "100%"});
            nuevoSelect='';
         /////////////// 
          var option_tipo_tel='';
         
////////////RECUPERO LOS DATOS DEL SELECT TIPO TELEFONO
          $('select[name="tipo_tel"] option').each(function(){
               
               var idOption=$(this).attr('value');
               var nomOption=$(this).text();
               option_tipo_tel+='<option value="'+idOption+'">'+nomOption+'</option>';
               if(idOption==tipotel){
                   // compara con el valor del usuario 
                 nuevoSelect+='<option value="'+idOption+'" selected>'+nomOption+'</option>' ;  
         
        }else{
                   nuevoSelect+='<option value="'+idOption+'">'+nomOption+'</option>';
             
               }
              
            });
             // y ahora actualiza el select
          $('#conten_slct_tipo_tel').empty().html('<select class="span12" name="tipo_tel"></select>');
          $('select[name="tipo_tel"]').append(nuevoSelect);
          $('select[name="tipo_tel"]').chosen({width: "100%"});
         
///////RECUPERO LOS DATOS DEL SELECT EMPRESA:_
         nuevoSelect='';
         var option_empresa='';
         
         $('select[name="empresa"] option').each(function(){
             
               var idOption=$(this).attr('value');
               var nomOption=$(this).text();
                option_empresa+='<option value="'+idOption+'">'+nomOption+'</option>';
               if(idOption==empresatel){
                   
                 nuevoSelect+='<option value="'+idOption+'" selected>'+nomOption+'</option>' ;  
         
        }else{
                   nuevoSelect+='<option value="'+idOption+'">'+nomOption+'</option>';
             
               }
              
            });
          $('#conten_slct_empresa').empty().html('<select class="span12" name="empresa"></select>');
          $('select[name="empresa"]').append(nuevoSelect);
          $('select[name="empresa"]').chosen({width: "100%"});
          
          
          //SETEO EL HTML DEL POP DE RESPONSABLE
            var vermas='';      
            vermas+="<div class='row-fluid'id='popovertelefonos'><div class='span12'><form id='editaragregartel'>";
            vermas+="<div class='span2'><small><?=lang('detalleTel_tipo')?></small><div id='cont_tipotel'><select class='span12' name='tipo_tel'>"+option_tipo_tel+"</select></div></div>";
            vermas+="<div class='span2'><small><?=lang('detalleTel_empresa')?></small><div id='cont_empresa'><select class='span12' name='empresa'>"+option_empresa+"</select></div></div>";
            vermas+="<div class='span2'>";
            vermas+="<small><?=lang('detalleTel_prefijo')?></small><input type='text' class='span12' name='prefijo'>";
            vermas+="</div>";
            vermas+="<div class='span2'><small><?=lang('detalleTel_numero')?></small>";
            vermas+="<input type='text' class='span12' name='numero'></div>";
            vermas+="<input type='submit' class='btn span12' value='agregar' name='agregar'>";
            vermas+="</form><table id='listadotel' class='table table-bordered table-condensed table table-striped'><thead><th><?=lang('detalleTelTabla_tipo')?></th><th><?=lang('detalleTelTabla_empresa')?></th><th><?=lang('detalleTelTabla_numero')?></th><th><?=lang('detalleTelTabla_eliminar')?></th></thead><tbody></tbody></table></div></div>";
            $('#pop').attr('data-content',vermas);
         //.....
           
        
           //return false;
       });
       
    $('#tablaresponsables tbody').on('click','a',function(){
       
       var form=[];
       form['id']='responsable';
       enviarfrm(form);
       
        var codigoTR='#'+($(this).parent().parent().attr('id'));
        //($(codigoTR).find('input[name="nombreRes[]"]').val());
        var codigo=$(codigoTR).find('input[name="codigoRes[]"]').val();
        var baja=$(codigoTR).find('input[name="bajaRes[]"]').val();
        var telefonos=$(codigoTR).find('input[name="telefonos[]"]').val();
        //modificando=codigoTR;
        var telefonosARRAY=JSON.stringify(telefonos);

                if(codigo=='-1'){
                        $(codigoTR).remove();   
                }else{
                        $(codigoTR).find('input[name="bajaRes[]"]').val('1');
                        $(codigoTR).hide(); 
                }
        return false;
    }); 
//CLICK EN VER TELEFONOS EN EL FORMULARIO RESPONSABLE
$('#pop').on('click',function(){  
      var form=[];
      form['id']='responsable';
      enviarfrm(form);
    var valorestel=[];
    var editandotel='';
    //LOS EVENTOS DEL POP EMPIEZAN CUANDO EL POP ESTE  READY
    $('#popovertelefonos').ready(function(){
        dibujarDetalleTel(editando);
        $('#popovertelefonos select').chosen({width: "100%"});
       
        
        //SUBMIT EN EL POP
        $('#editaragregartel').on('submit',function(){
       
            $('#editaragregartel input[type="submit"]').val('agregar');
                $('#editaragregartel input[type="text"]').each(function(){
                    var nombre=$(this).attr('name');
                    var valores=$(this).val();
                    valorestel[nombre]=valores;

                });

                $('#editaragregartel select').each(function(){
                    var nombre=$(this).attr('name');
                    var valores=$(this).val();
                    valorestel[nombre]=valores;
                    
                });
                if(editandotel==''){ 
                    n++;
                    //alert(n);
                 
                    agregartelResp_desp(editando);
                    dibujarDetalleTel(editando);
                   
                 }else{
                    
                     
                   modificarDetalleTel(editandotel);
                   dibujarDetalleTel(editando);
                    editandotel='';
                 }

                 return false;
        });
        //FIN DEL SUBMIT
      


//&CLICK EN EL TR DEL POP
$('#listadotel tbody').on('click','tr td:not(".borrarTelResp")',function(){
    var codigoTR='#'+$(this).parent().attr('id');
    //alert(codigoTR);
    alert('click');
        $('#editaragregartel input[type="submit"]').val('modificar');
    editandotel=$(codigoTR).attr('id');
    var telefono=$(codigoTR).find('.telefonosDetalle').text();
    var tipo_tel=$(codigoTR).find('.tipoNomDetalle').text();
    var tipo_empresa=$(codigoTR).find('.empresaDetalle').text();
    var part_telefono=telefono.split('-');
    
    //ARMADO DE SELECT DEL DESPLEGABLE
        var nuevoSelect='';
        $('#editaragregartel select[name="tipo_tel"] option').each(function(){
            var idOption=$(this).attr('value');
            var nomOption=$(this).text();

            if(nomOption==tipo_tel){

                nuevoSelect+='<option value="'+idOption+'" selected>'+nomOption+'</option>' ;  

            }else{
                nuevoSelect+='<option value="'+idOption+'">'+nomOption+'</option>';

            }

        });

        $('#cont_tipotel').empty().html('<select class="span12" name="tipo_tel"></select>');
            $('#cont_tipotel select[name="tipo_tel"]').append(nuevoSelect);
            $('#cont_tipotel select[name="tipo_tel"]').chosen({width: "100%"});
            nuevoSelect='';

            $('#editaragregartel select[name="empresa"] option').each(function(){
                var idOption=$(this).attr('value');
                var nomOption=$(this).text();

                if(nomOption==tipo_empresa){

                    nuevoSelect+='<option value="'+idOption+'" selected>'+nomOption+'</option>' ;  

                }else{
                    nuevoSelect+='<option value="'+idOption+'">'+nomOption+'</option>';

                }

            });

            $('#cont_empresa').empty().html('<select class="span12" name="empresa"></select>');
            $('#cont_empresa select[name="empresa"]').append(nuevoSelect);
            $('#cont_empresa select[name="empresa"]').chosen({width: "100%"});

            $('#editaragregartel input[name="prefijo"]').val(part_telefono[0]);
            $('#editaragregartel input[name="numero"]').val(part_telefono[1]);
            return false;
     //   FIN DEL ARMADO// 
    });
  //FIN DEL EVENTO CLICK
});
$('#listadotel tbody').on('click','a',function(){
var telefonosJSON=$('#'+editando).find('input[name="telefonos[]"]').val();
var row=$(this).parent().parent().attr('id');
var telefonosARRAY=JSON.parse(telefonosJSON);
$(telefonosARRAY).each(function(key,value){
    if(value.row==row){
        
        if(value.codigo=='-1'){
           
            $('#'+row).remove();
            telefonosARRAY.splice(key,1);
        }else{
            value.baja='1';
            $('#'+row).hide();
            
            
        }
    }
});
$('#'+editando).find('.telefonos input[type="hidden"]').val(JSON.stringify(telefonosARRAY));
//devolver el jason al input
return false;
});
return false;

});














////////////////////////////////////RAZON SOCIAL/////////////////////////////////////////////



var sltcondicion=[];// en este array guardo los valores del select condicion
$('#razonsocial select[name="condicion"] option').each(function(){
   
    var g={
            'id':$(this).attr('value'),
            'nombre':$(this).text()
    };
    sltcondicion.push(g);
});


var slttipoDocumento=[];// en este array guardo los valores del select condicion
$('#razonsocial select[name="tipoDeDocumento"] option').each(function(){
   
    var g={
            'id':$(this).attr('value'),
            'nombre':$(this).text()
    };
    slttipoDocumento.push(g);
});
       
$("#razonsocial").validate({// validacion del form
    rules: {
        cuit: {
            required:true,
            digits:true
        },
        razon:{
            required:true
        },
        condicion:{
            required:true
            
        }
              
    },
    messages:{
        cuit:{
            required:'<?=lang('error_requerido')?>',
            digits:'<?=lang('error_numeros')?>'
        },
        razon:{
            required:'<?=lang('error_requerido')?>'
        },
        condicion:{
            required:'<?=lang('error_requerido')?>'
        }
    }
    
    
});
       
       
       
 /////////SUBMIT DEL FORMULARIO RAZON
var modificando='';
var c=0;
$('#contenedorGeneral').on('submit','#razonsocial',function(){
    var cuitVal=$('#razonsocial input[name="cuit"]').val();
    var razonVal=$('#razonsocial input[name="razon"]').val();
    var condVal=$('#razonsocial select[name="condicion"]').val();
    var condNom=$('#razonsocial select[name="condicion"] option:selected').text();
    var tipodocumento=$('#razonsocial select[name="tipoDeDocumento"] option:selected').val();
           
           
    if(modificando==''){
        c++;
        var ultimoTr=$('#razonsocial tbody').find('tr:last').attr('id');
      
       
        if(ultimoTr!=undefined){
            // esta condicion se ejecuta uan sola vez. Cuando la tabla se carga con registros seteo el numerador de tr'S para saber como sigo numerando
            var part=ultimoTr.split("r");
            var z=parseInt(part[1],8);
             z++;
            c=z;
}
        $('#razonsocial tbody').append('<tr id="valor'+c+'"><td class="condicionVal">'+condNom+'<input type="hidden" name="conVal[]" value="'+condVal+'" ><input type="hidden" name="codigoRazon[]" value="-1"><input type="hidden" name="bajaRazon[]" value="0"></td><td class="tipoDeDocumento">'+tipodocumento+'<input type="hidden" value="'+tipodocumento+'" name="tipoDeDocumento[]"></td><td class="cuitVal">'+cuitVal+'<input type="hidden" name="cuitVal[]" value="'+cuitVal+'"></td><td class="razonVal">'+razonVal+'<input type="hidden" name="razonVal[]" value="'+razonVal+'"></td><td class="borrarCondicion"><a name="borrarCondicion" href="#">x</a></td></tr>');
        $('#razonsocial input[type="text"]').val('');
    }else{
     
        $('#razonsocial input[name="guardar"]').val('agregar');
        //modificacion
       var codigo= $('#razonsocial #'+modificando).find('input[name="codigoRazon[]"]').val();
       var baja= $('#razonsocial #'+modificando).find('input[name="bajaRazon[]"]').val();
        
        $('#razonsocial#'+modificando).empty();
        $('#razonsocial #'+modificando).html('<td class="condicionVal">'+condNom+'<input type="hidden" name="conVal[]" value="'+condVal+'"><input type="hidden" name="codigoRazon[]" value="'+codigo+'"><input type="hidden" name="bajaRazon[]" value="'+baja+'"></td><td class="tipoDeDocumento">'+tipodocumento+'<input type="hidden" value="'+tipodocumento+'" name="tipoDeDocumento[]"></td><td class="cuitVal">'+cuitVal+'<input type="hidden" name="cuitVal[]" value="'+cuitVal+'"></td><td class="razonVal">'+razonVal+'<input type="hidden" name="razonVal[]" value="'+razonVal+'"></td><td class="borrarCondicion"><a name="borrarCondicion" href="#">x</a></td>');
        modificando='';
        $('#razonsocial input[type="text"]').val('');
           }
           
           return false;
       });
      
//CLICK EN UN TR DEL LISTADO 
$('#razonsocial tbody').on('click','tr td:not(".borrarCondicion")',function(){
    var codigoTR='#'+$(this).parent().attr('id');
    //alert(codigoTR);
    
    if(codigoTR!='#valor0'){//LA PRIMERA RAZON NO ES EDITABLE
    $('#razonsocial input[name="guardar"]').val('modificar');
    $('#razonsocial input[name="cuit"]').val($(codigoTR).find('.cuitVal').text());
    $('#razonsocial input[name="razon"]').val($(codigoTR).find('.razonVal').text());
    var tipoDocumento=$(codigoTR).find('.tipoDeDocumento').text();
    var con=$(codigoTR).find('.condicionVal').text();
        modificando=$(codigoTR).attr('id');
        $('#cont_condicion').empty().html('<select class="span12" name="condicion"></select>');
        //armo el select de condicion
            $(sltcondicion).each(function(){
       
                if(this.nombre==con){
                    $('select[name="condicion"]').append('<option value="'+this.id+'"selected>'+this.nombre+'</option>');
                }else{
                        
                    $('select[name="condicion"]').append('<option value="'+this.id+'">'+this.nombre+'</option>');
                    }
                });
                
            $('select[name="condicion"]').chosen({width: "100%"});
            $('select[name="tipoDeDocumento"]').empty();
            $(slttipoDocumento).each(function(){
                console.log(this.nombre);
                if(this.id==tipoDocumento){
                    //alert('igual');
                    $('select[name="tipoDeDocumento"]').append('<option value="'+this.id+'"selected>'+this.nombre+'</option>');
                }else{
                        
                    $('select[name="tipoDeDocumento"]').append('<option value="'+this.id+'">'+this.nombre+'</option>');
                    }
                });
            $('select[name="tipoDeDocumento"]').trigger("chosen:updated");;
           // alert(tipoDocumento);
       }else{
           alert('no es editable');
       }
});



//BAJA
  $('#razonsocial tbody').on('click','a',function(){
      var form=[];
      form['id']='razonsocial';
      enviarfrm(form);
      var codigoTR='#'+$(this).parent().parent().attr('id');
      //alert(codigoTR);
      if(codigoTR!='#valor0'){//LA PRIMERA RAZON NO ES EDITABLE NI PUEDE SE DADA DE BAJA
        var codigoRazon=$(codigoTR).find('input[name="codigoRazon[]"]').val();
    
            if(codigoRazon=='-1'){

                $(codigoTR).remove();
            }else{

               $(codigoTR).find('input[name="bajaRazon[]"]').val('1');
               $(codigoTR).hide();
            }
       
        }else{
            alert('no es editable');
        }
        return false;
  });



       });
</script>
