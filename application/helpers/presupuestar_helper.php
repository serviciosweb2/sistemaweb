<script>
$(document).ready(function(){
// convierto los select a select chosen_:
        $('#contenedorPresupuesto select').chosen();
        $('#seleccionPlanes').hide();
        $('#descripciones').hide();
        $('.alert').hide();
            
// CHANGE EN EN SELECT DE CURSO        
  $('.fancybox-wrap').on('change','#cursos',function(){
            x=0; // esta variable en cero permite cargar los select de cuotas por ajax
            n=1;// esta variable en 1 permite agregar 3 presupuestos como maximo
            var select=$(this).attr('id');
            //var cod_curso=$('#'+select+' option:selected').attr('value');
            var cod_curso=$(this).val();
            //alert(cod_curso);
            $('#cabezeraPlan').empty();
            $('.presupuesto').remove();
            
            
            $.ajax({
                url:'<?=base_url('aspirantes/listarComisiones')?>',
                type:'GET',
                data:'cod_curso='+cod_curso,
                dataType:'json',
                success:function(respuesta){
                //alert(respuesta);
                var listaComisiones='';
                   $(respuesta).each(function( key , value){
                       //alert(value['nombre']);
                          
                          listaComisiones+='<tr><td><input type="radio" name="comision" value='+value.codigo+'></td><td>'+value.nombre+'</td>';  
                          
                          listaComisiones+='<td><small>'+value['cupo']+'</small></td>';
                          
                          listaComisiones+='<td><div class="span12 format"><small>'+value.horarioCursado +'</small></div></td></tr>';
                    });
                    
                    $('#listacomisiones tbody').html(listaComisiones);
                    
                    $('#listacomisiones tbody').hide().fadeIn();
                    $.fancybox.update();
                }
                
            });
        });  
    // var x=0; // seteo variable para evitar llamadas ajax innecesarias
     var resulConsulta='';// en esta variable voy a guardar la respuesta de ajax.
//CLICK EN LOS INPUT'S RADIO  
 $('#listacomisiones tbody').on('click','input:radio',function(){
                var cod_comision=$(this).val();
               
              // if (x==0){
                $.ajax({
                    url:BASE_URL+'aspirantes/listarPlan',
                    type:'GET',
                    dataType:'json',
                    data:'cod_comision='+cod_comision,
                    cache:false,
                    success:function(respuesta){
                    resulConsulta=respuesta;
                            //alert($('#cabezeraPlan').parent().next().attr('id'));
                           //declaro titulo y fecha del plan:_ 
                           
                           $('#listapresupuestos').empty();
                           n=1;
                            //var titulo='<div class="span4 text-center">'+respuesta.nombreplan+'</div>';
                            
                            //titulo+='<div class="span7 text-center">'+respuesta.fechainicio+' - '+respuesta.fechavigencia+'</div>';
                            
                            
                               
                               
                           
                         
                            //declaro un select  que tendra las formas de pago de dicho plan_:
                           
                            // inserto el select vacio y la cabezera del plan_:
                            
                            $('select[name="plan"]').empty().append('<option></option>');
                            $(respuesta).each(function(){
                             //alert(this.codigoplan);
                                $('select[name="plan"]').append('<option value="'+this.codigo+'">'+this.nombre+'</option>');   
                            });
                            $('#seleccionPlanes').fadeIn();
                            //$('select[name="plan"]').chosen();
                           
                            
                            // vacio el contenedor de detalles_:
                            
                           //ingreso un opcion vacia en el select para que se muestre el place-holder_:
                            //$('#selectplanes select').append('<option></option>');
                            // recorro las formas de pago y pongo una opcion por cada una_:
                            
                    
                            // llamo al chosen
                        $('#selectplanes select').chosen();
                        x=1;// la seteo en uno para que si hay un cambio de comision no vuelva a cargar por ajax
                            $.fancybox.update();
                        }
                    
                });
               
        
        });
// CLICK EN EL SELECT DE PLANES
var detalle=[];
 $('#presupuestar').on('change','select[name="plan"]',function(){
    var cod_plan='cod_plan='+$(this).val();
  
        $.ajax({
            url:'<?php echo base_url('aspirantes/listarCuotas')?>',
            type:'GET',
            data:cod_plan,
            dataType:'json',
            success:function(respuesta){
            
            var plan1='<div class="panel panel-default">';
            plan1+='<div class="panel-body">';
            plan1+='<div class="col-md-3"><select name="select[]" class="form-control"></select></div>';
            plan1+='<div class="col-md-9"><p></p></div></div></div>';
            
      
            $('#descripciones').fadeIn();
            $('#descripciones').html(plan1);
            console.log('PLANES:');
            console.log(respuesta);
               //$('select[name="select[]"]').parent().empty().append('<select data-placeholder="Seleccione Nº cuotas" name="select[]"><option></option></slect>');
              detalle=[];
             for (var r in respuesta){
                detalle.push(respuesta[r]);
                console.log('RESPUESTA'+r);
                console.log(respuesta[r].detalle);
                $('select[name="select[]"]').append('<option></option><option value="'+respuesta[r].detalle.codigoMatricula+'">'+respuesta[r].detalle.cuotas.length+' cuota/s</option>');
            }
    
    
    
   
                //$('select[name="select[]"]').chosen();
                 $.fancybox.update();
            }
            
           
        });
 });       
        
//CHANGE EN LOS SELECT'S  DE CUOTAS    
 $('#descripciones').on('change','select[name="select[]"]',function(){
 
        // tomo el value de la opcion seleccionada y muestro la descripcion correspondiente_:
//        alert('!');
        var nombre=$(this).attr('descripcion');
        var inline=$(this).val();
        var valor=$(this).val();
        var vistaDiv=$(this).parent().parent().find('p');
        //alert($(this).parent().parent().find());
        //alert($(this).val());
        $(detalle).each(function(key,value){
           // alert(value.detalle.codigoMatricula);
            if(value.detalle.codigoMatricula==valor){
            
                var vistaDetalle=value.detalle.matricula;
               
                $(value.detalle.cuotas).each(function(clave,valor){
                    
                    vistaDetalle+=valor;
                });
               vistaDiv.html(vistaDetalle).hide().fadeIn('slow');
              
            }
            
        });
      
       });
  
// CLICK EN BOTON AGREGAR
  var n=1;// esta variable es una bandera para saber cuantos presupuestos estoy dejando dar
 $('.fancybox-wrap').on('click','input[name="agregarPrsupuesto"]',function (){
     //alert('click ' +resulConsulta.nombreplan);
     //alert(n);
     var cantPlanes=$(detalle).length;
     //alert(cantPlanes);
     if(n<cantPlanes){
                    if( n!=3){
                     // tengo que generar una nueva capa con un cont. para un select y para las descirpciones
                      
                    var select='<div class="panel panel-default"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
                        select+='<div class="panel-body">';
                        select+='<div class="col-md-3"><select name="select[]" class="form-control"></select></div>';
                        select+='<div class="col-md-9"><p>Descripcion</p></div></div></div>';
                      $('#descripciones').append(select);
                      //$('select[name="select[]"]').last().html('<option>falta terminar el script</option>');
                      // vacio el contenedor de detalles_:
                      //$('#detallePlan-'+n).empty();
                      //ingreso un opcion vacia en el select para que se muestre el place-holder_:
                      $('.select select').last().append('<option></option>');
                      // recorro las formas de pago y pongo una opcion por cada una_:
                      $(detalle).each(function(key,value){
                          
                      console.log(value.detalle.codigoMatricula);
                      key++;
                            $('select[name="select[]"]').last().append('<option value="'+value.detalle.codigoMatricula+'">'+key+' cuota/s</option>');
                             //inserto el detalle correspondiente a cada forma de pago con display none_: 
                           
                            
                     });
                     $('.select select').last().chosen();
                      $.fancybox.update();
                     n++;
                     }else{
                     alert('no se pueden dar mas de 3 presupuestos a la vez');
                     }
     }else{
     alert('solo hay '+cantPlanes+' planes');
     }
  return false;
    }); 
    
//CLICK EN BOTON BORRAR
 $('.fancybox-wrap').on('click','.close',function(){

       // var parent=$(this).parent().parent().remove();
        n--;
        //alert(parent);
        //return false;
    });
    
// CLICK EN SUBMIT
 $('.fancybox-wrap').on('submit','#presupuestar',function(){
    var valores=[];
    var submit='1';
    //alert('submit esta en=>'+submit);
  $('#descripciones select').each(function(key,value){
     // alert($(value).attr('name'));
      //alert($(value).attr('name'));
      if($(value).val()==''){// priemro seteo que no este vacio ningun select
          alert($(value).attr('name')+' esta vacio');
           if(submit!=0){
                submit=0;
            }
      }else{// si no esta empieza a validar
      valores[key]=$(value).val();// tomo el value de cada select para comparar
        var ban=0;// esta bandera me va decir si este campo se repite
        $(valores).each(function(k,v){ 
    //recorro los valores  y los comparo este select select, 
            
            if(v==$(value).val()){
                ban++;
    // si coincide aumenta la bandera a 1 . 
            }
            
            
        });
        
        if(ban>1){
            //si la bandera me da un valor mas grande que 1 
    // quiere decir que esta repetido
            alert('campos repetidos');
             
            if(submit!=0){
                submit=0;// si esta repetido no dejo submitar
            }
        }   
    }
  });
  //alert(valores[0]);
     if(submit==0){
         alert('error');
         submit='';
     }else{   
    var datta=$(this).serialize();
    //alert('correcto enviando');
    //alert(datta);
    $.ajax({
            url:BASE_URL+'aspirantes/guardar_presupuesto',
            data:datta,
            type:'POST',
            dataType:'JSON',
            success:function(respuesta){
                if(respuesta.codigo==1){
                    //$.fancybox.close(true);
                    $('.bottom-right').notify({
                                message: { 
                                    text:' Guardado correctamente !' 
                                },
                                type:'info'
                                }).show(); // for the ones that aren't closable and don't fade out there is a .hide() function.
                              //setTimeout(function(){$.fancybox.close(true)},1500);  
                }else{
                    $('.alert').hide().html(respuesta.msgerror).fadeIn();
                }
            }
            
    });
     }
    return false;
    });
});
</script>
