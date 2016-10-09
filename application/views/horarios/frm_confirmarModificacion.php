<form id="guardarModificacion">
   <div >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3><?php echo lang('repeticion_horario'); ?></h3>
    </div>
       
    <div class="modal-body" >
        
        <?php 
        if(!isset($fin_repeticion['fin'])){
            echo 'Está seguro que desea modificar este evento?';
        }else{
            echo lang('horarios_eventos_series');
        }
        
        ?>
    </div>
    <div class="modal-footer">
        <?php if(!isset($fin_repeticion['fin'])){?>
         <button type="button" class="pregunta btn btn-primary btnBorrar"  name="soloEste" data-toggle="button"><?php echo lang('modificar'); ?></button>
        <?php }else{ ?>
          <button type="button" class="pregunta btn btn-primary btnBorrar"  name="soloEste" data-toggle="button"><?php echo lang('modificar_solo_este'); ?></button>
           <button type="button" class="pregunta btn btn-inverse btnBorrar  " name="esteEnAdelante"  data-toggle="button"><?php echo lang('modificar_eventos_serie'); ?></button> 
           
        <?php } ?>
    </div>
</div> 


            <input type="hidden" name="codigo_horario" value="<?php echo $codigo_horario;?>">
            
            <input type="hidden" name="fechaDesde" value="<?php echo $fechaDesde;?>">
            <input type="hidden" name="horaDesde" value="<?php echo $horaDesde;?>">
            <input type="hidden" name="horaHasta" value="<?php echo $horaHasta;?>">
            <input type="hidden" name="cod_salon" value="<?php echo $cod_salon;?>">
            <input type="hidden" name="cod_comision" value="<?php echo $cod_comision;?>">
            <input type="hidden" name="cod_materia" value="<?php echo $cod_materia;?>">
            <input type="hidden" name="cod_profesor" value="<?php echo $cod_profesor;?>">
            <input type="hidden" name="tipoRepeticion" value="<?php echo $tipoRepeticion;?>">
            <input type="hidden" name="frecuenciaRepeticion" value="<?php echo $frecuenciaRepeticion;?>">
            
            <input type="hidden" name="tipoFinalizacion" value="<?php echo $tipoFinalizacion;?>">
            
            
            
            
            
            
            <input type="hidden"  name="finalizacion" value="<?php echo isset($fin_repeticion['fin']) ? formatearFecha_pais($fin_repeticion['fin']) : '' ?>">
            <?php 
           
            foreach($dias as $key=>$dia){
                echo "<input type='hidden' value='".$key."' name='idDia[]'>";
                
            }
            
            ?>
        
   
</form>
<script>
$(document).ready(function(){
    
        
        var codigo_horario=$('input[name="codigo_horario"]').val();
        
        var i=1;
        $('.btnBorrar').on('click',function(){
        // alert($('#guardarModificacion').serialize());
        var dataPOST=$('#guardarModificacion').serialize();  
        var nombre=$(this).attr('name');
        var modifica_serie='';
        var soloeste='';
        var datta='';
        //alert('llama '+i);
        i++;
        if(nombre=='esteEnAdelante'){
           //alert('!');
            modifica_serie=true;
            
             var postModificado=dataPOST.replace('&tipoRepeticion=','&tipoRepeticion=1');
            datta=postModificado+'&tipoFinalizacion=1';
        }else{
            modifica_serie=false;
            var postModificado=dataPOST.replace('&tipoRepeticion=','&tipoRepeticion=0');
            datta=postModificado;
        }
        
         
        console.log('paso');
            $.ajax({
                url:'<?php echo base_url('horarios/guardarHorario');?>',
                //data:'codigo_horario='+codigo_horario+'&soloeste='+soloeste+'',
                data:datta+'&modifica_serie='+modifica_serie,
                type:'POST',
                dataType:'json',
                cache:false,
                success:function(respuesta){
                    if(respuesta.codigo==2){// error de superposicion
                            $.fancybox.close();
                            console.log(respuesta); 
                            var horaC=respuesta.custom.dia.split(' ');
                            var horaF=respuesta.custom.dia2.split(' ');
                            var msj='La modificacion no puede realizarse.<br>Se superpone con un evento el dia '+horaC[0]+' que comienza '+horaC[1]+' y finaliza '+horaF[1];

                            bootbox.alert(msj,function(){

                                  revertFunc(); 
                          });
                                            
                    }
                    
                    if(respuesta.codigo==1){// success!
                        
                        var unset=respuesta.custom.unset;
                        $(unset).each(function(){
                            $('#calendar').fullCalendar( 'removeEvents',this.id);   
                        });
                        var nuevo=respuesta.custom.nuevo; 
                        $('#calendar').fullCalendar('addEventSource',nuevo);
                        $.fancybox.close();
                    }
                    
                    if(respuesta.codigo==0){// error en tipo de dato
                        
                        alert(respuesta.respuesta);
                        $.fancybox.close();
                    }
                 }
            });
        
        return false;
   });
});
</script>

