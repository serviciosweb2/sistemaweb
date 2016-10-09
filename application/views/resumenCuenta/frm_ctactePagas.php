

<script>
$('.fancybox-wrap').ready(function(){
    
   
    
    function initVista(){
    
    function enviarPost(){
        
        var x=0;
        
        tablaCreditos.$('.has-error').each(function(){
           
           x++;
           
        });
        
        return  x == 0 ?  true :  false ;
          
        
       
       
    }
     
    function validarEntrada(elemento){
        
       
       var resultado=[];
       
        resultado.codigo=1;
       
        resultado.msg='';
        
        var valorEntrada=$(elemento).val();
        
        console.log('ENTRA '+valorEntrada );
        
        //var valorConPunto=valorEntrada.replace("?",".");
        
       
    
    var pattern= new RegExp("^([0-9]{0,10})\\"+BASE_SEPARADOR+"?([0-9]{1,"+BASE_DECIMALES+"})$");
        
       function msjError(){
        
    
            var cadena='Formato esperado '+'XX'+BASE_SEPARADOR;
            
            for  ( var i=0; i < 2 ; i++) {
            
              cadena+='X';
            }
            
    return cadena;} 

        if(pattern.test(valorEntrada)){
            
            $(elemento).closest('.input-group').removeClass('has-error');
            
            
        }else{
            
            
            $(elemento).closest('.input-group').addClass('has-error');
            
            
            $.gritter.add({
                                    title: 'Upps!',
                                    text: msjError(),
                                    //image: $path_assets+'/avatars/avatar1.png',
                                    sticky: false,
                                    time: '3000',
                                    class_name:'gritter-error'
                        });
            
            
        }
        
       
        
        return resultado;
        
    }
    
    function calcularTotal(){
        
        
        var total=0.00;
        
        tablaCreditos.$('input[name="codigoImputar[]"]').each(function(){
            
            if($(this).is(':checked')){
                
                var valor=$(this).closest('.input-group').find('input[name="valorImputar[]"]').val();
                
               
              
                
               
            }else{
                
                
                $(this).closest('.input-group').removeClass('has-error');
                
                
            }
        });
        
        //console.log(parseFloat(total).toFixed(2));
        console.log(total);
        
        
        return parseFloat(total).toFixed(2);
    }
    

    var tablaCreditos=$('#aImputar').dataTable({
      "bLengthChange": false,
      "iDisplayLength": 5
 });
 
  
 
 
    $('.fancybox-wrap').on('click','input[name="codigoImputar[]"]',function(){
        
        
        if($(this).is(':checked')){
              
                //var valor=$(this).closest('.input-group').find('input[name="valorImputar[]"]').val();

                $(this).closest('.input-group').find('input[name="valorImputar[]"]').prop('disabled',false);

               validarEntrada($(this).closest('.input-group').find('input[name="valorImputar[]"]'));
               
            }else{
                
                $(this).closest('.input-group').find('input[name="valorImputar[]"]').prop('disabled',true);
                $(this).closest('.input-group').removeClass('has-error');
                
                
            }
        
        
        
        
        
    
    
           
       
        
     
                  
    
    });
 
 
    $('.fancybox-wrap').on('focusout','input[name="valorImputar[]"]',function(){
    
       validarEntrada(this);
        
    });
 
    $('.fancybox-wrap').on('click','button[name="submit"]',function(){

        if(enviarPost()){
            $('#imputaciones').submit();
        }else{
            
             $.gritter.add({
                            title: 'Upp!',
                            text: 'revise los campos en rojo',
                            //image: $path_assets+'/avatars/avatar1.png',
                            sticky: false,
                            time: '3000',
                            class_name:'gritter-error'
                }); 
            
            
        };
        return false;
    }); 
    
    
    $('.fancybox-wrap').on('click','button[name="getTotal"]',function(){
     
        var dataPOST=tablaCreditos.$('input[name="valorImputar[]"], input[name="codigoImputar[]"]').serialize();
     
           
     
     
     
        
        if(enviarPost()){
           
                    $.ajax({
                        url: BASE_URL+'ctacte/calcularTotal',
                        type: "POST",
                        data: dataPOST,
                        dataType:"JSON",
                        cache:false,
                        success:function(respuesta){
                           
                           if(respuesta.codigo==1){
                               
                            
                                $('input[name="total"]').val(respuesta.total);   
                           
                            }else{
                               
                               $.gritter.add({
                                    title: 'Uppss!',
                                    text: respuesta.msgerror,
                                    //image: $path_assets+'/avatars/avatar1.png',
                                    sticky: false,
                                    time: '3000',
                                    class_name:'gritter-error'
                        });
                               
                           }
                            
                        }
                });
            
        }else{
            
            
            
             $.gritter.add({
                            title: 'Upp!',
                            text: 'revise los campos en rojo',
                            //image: $path_assets+'/avatars/avatar1.png',
                            sticky: false,
                            time: '3000',
                            class_name:'gritter-error'
                }); 
            
            
            
            
            
        };
        
        
        
        
        
        return false;
    });
    
    
    
    $('.fancybox-wrap').on('submit','#imputaciones',function(){
        
        var cod_alumno=$('input[name="cod_alumno"]').val();
        var motivo=$('input[name="motivo"]').val();
        var dataPOST=tablaCreditos.$('input[name="valorImputar[]"], input[name="codigoImputar[]"]').serialize()+'&motivo='+motivo+'&cod_alumno='+cod_alumno;
        
        $.ajax({
            url: BASE_URL+'ctacte/guardarNotaCredito',
            type: "POST",
            data: dataPOST,
            dataType:"JSON",
            cache:false,
            success:function(respuesta){
                if(respuesta.codigo==1){
                    console.log(respuesta);
                    $.gritter.add({
                                    title: 'OK!',
                                    text: 'Guardado Correctamente',
                                    //image: $path_assets+'/avatars/avatar1.png',
                                    sticky: false,
                                    time: '3000',
                                    class_name:'gritter-success'
                        });
                        
                        $.fancybox.close(true);
                        codigo=respuesta.custom;
                       
                            
                            $.ajax({
                                url: BASE_URL+'cobros/frm_imputaciones',
                                type: "POST",
                                data:'codigo='+respuesta.custom,
                                cache:false,
                                success:function(respuesta2){
                                            $.fancybox.open(respuesta2,{
                                                
                                                scrolling:'auto'
                                                
                                                
                                            });
                                }
                                
                        });
                            
                            
                    
                        
                        
                        
                }else{
                    
                    console.log('LLAMADA GRITTER');
                    
                    $.gritter.add({
                                    title: 'Upps!',
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
    
 
    
//               base_validar('imputaciones');
//       
//                $('input[name="motivo"]').rules( "add", {
//                   
//                    required:true,
//                    
//                });
                

//               $('input[name^="valorImputar"]').each(function(key,element){
//                    
//                    var id='#'+$(this).attr('id');
//                    
//                    $(id).rules( "add", {
//                          
//                        moneda:true
//                    });
//                                     
//                });
              

        

//                $(".modal-footer").click(function(){
//                 
//
//                console.log($("#imputaciones").valid());
//   
//            });
    
    
    
    
    
    
    
    
    
    
    
    
    
    }
    
    
    var cuentas= $('.fancybox-wrap input[name="cuentas"]').val();
    
    $('.mensaje').hide();
    
    $('.vista').hide();
    
    $('button[name="submit"]').hide();
    
    if(cuentas>0){
        
            $('.vista').show();

            $('button[name="submit"]').show();

            var configuracion=JSON.parse($('input[name="configuracion"]').val());

            initVista();

 

      
        
    }else{
        
            $('.mensaje').show(); 
        
    }
       
   }); 
   

</script>
<?php
//$str = str_replace("ll", "?", "hola ll");
//echo $str;
//echo $separador;
?>

<div class="modal-content">
    
   
    
    
    
    <div class="modal-header">
<!--            <button type="button" class="close" data-dismiss="modal">&times;</button>-->
            <h4 class="blue bigger"><?php echo lang('notacredito_ctacte');?><small><i class="icon-double-angle-right"></i>  <?php echo $nombreFormateado ?></h4>
    </div>

    <div class="modal-body overflow-visible">
        <input type="hidden" value="<?php echo count($ctacteAlumno)?>" name="cuentas">
        
        <input type="hidden" value='<?php echo json_encode(array('separador'=>$separador,'decimales'=>$decimales));?>' name="configuracion">
        
        <div class="mensaje"><?php echo lang('no_puede_generar_nota_credito');?></div>
            
          <form id="imputaciones">
            <div class="row vista">
              
              <div class="col-xs-12  controlTabla table-responsive">
                
                      <input type="hidden" name="cod_alumno" value="<?php echo $objalumno->getCodigo()?>">

                        <table class="table table-striped table-bordered" id="aImputar">
                      <thead><th><?php echo lang('descripcion');?></th><th><?php echo lang('fecha_vencimiento');?></th><th><?php echo lang('importe');?></th><th><?php echo lang('pagado');?></th><th></th></thead>
                  <tbody>
                      
                     <?php 
                        foreach($ctacteAlumno as $key=>$imputacion){
                            
                            $valorVista=$imputacion['saldoNotaCredito'];
                            
                            echo '<tr><td>'.$imputacion["descripcion"].'</td>';
                            echo '<td>'.$imputacion["fechavenc"].'</td>';
                            echo '<td>'.$imputacion["importeformateado"].'</td>';
                            echo '<td>'.$imputacion['pagadoformateado'].'</td>';
                            echo '<td>
                                <div class="input-group">
                                  <span class="input-group-addon">
                                    <input type="checkbox" name="codigoImputar[]" value="'.$imputacion['codigo'].'">
                                  </span>
                                  <input type="" id="in'.$key.'" name="valorImputar[]" data-importe="'.$imputacion["pagado"].'" class="form-control" value="'.$valorVista.'" disabled>
                                </div>
                                </td></tr>';
                        }
                     
                     ?> 
                  </tbody>
                  </table>
                 
              </div>
           
            </div>
        <div class="row vista">
            <div class="col-xs-9">
                <label><?php echo lang('observaciones');?></label>
            <input class="form-control" name="motivo">
            </div>
            <div class="col-xs-3">
                <label><?php echo lang('total');?></label>
                <!--<input name="total" class="form-control" id="pruebatest" value="0.00" readonly>-->
                <div class="input-group">
          <input class="form-control"  name="total" readonly>
          <span class="input-group-btn">
            <button name="getTotal" class="btn btn-info btn-sm" type="button"><?php echo lang('ver_total');?></button>
          </span>
        </div>
               
            </div>
        </div>
         </form>
    </div>

    <div class="modal-footer">
<!--            <button class="btn btn-sm" data-dismiss="modal">
                    <i class="icon-remove"></i>
                    Cancel
            </button>-->

            <button class="btn btn-sm btn-primary" name="submit">
                    <i class="icon-ok"></i>
                    <?php echo lang('guardar');?>
            </button>
    </div>
</div>
	
