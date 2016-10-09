   <div >
    <div class="modal-header">
<!--        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>-->
        <h3><?php echo lang('borrar_eventos'); ?></h3>
    </div>
                <input type="hidden" name="codigo_horario" value="<?php echo $codigo_horario;?>"> 
    <div class="modal-body" >
        <?php //echo 'existenCorrelativos = '.$existenCorrelativos.'<br>'?>
        
            
            <?php 
            if($existenCorrelativos==1){
                if($tiene_asistencia){
                 echo lang('evento_con_asistencias_cargadas');   
                }else{
                   echo lang('horarios_eventos_series'); 
                }
           }else{
                if($tiene_asistencia){
                   echo lang('evento_con_asistencias_cargadas');    
                }else{
                    echo lang('horarios_evento');
                }
                
                
            }
            
            
            ?>
    </div>
    <div class="modal-footer">
             <?php
            if($existenCorrelativos==1){
                echo '<input type="submit" class="btn btn-primary btnBorrar" value="'.lang('este_evento').'" name="botton2">';
                $ocultar = '';
                if($tiene_asistencia){
                    $ocultar = 'hide';
                }
                echo' <input type="submit" class="btn btn-inverse btnBorrar '.$ocultar.'" name="esteEnAdelante" value="'.  lang('este_evento_en_adelante').'">';
            }else{
                
                echo '<input type="submit" class="btn btn-inverse btnBorrar" value="'.  lang('eliminar').'" name="boton1">';
            }
            
            ?>
        
        
        
    </div>
</div>
<script>

$('.fancybox-wrap').ready(function(){
    
    var clavesFRM=Array("validacion_ok","TIENE_ASISTENCIAS_CARGADAS","ERROR");
    
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
    
    function initFRM(){
        
       $('.fancybox-wrap select').chosen({
        width:'100%',
        allow_single_deselect: true
    });
    
    
          var codigo_horario=$('input[name="codigo_horario"]').val();
    
        var i=1;
        
        $('.btnBorrar').on('click',function(){
           
        var nombre=$(this).attr('name');
        
        var soloeste='';
        
        //alert('llama '+i);
        i++;
        if(nombre=='esteEnAdelante'){
           
            soloeste=false;
        }else{
            soloeste=true;
        }
        

            $.ajax({
                url:'<?php echo base_url('horarios/bajaHorario');?>',
                data:'codigo_horario='+codigo_horario+'&soloeste='+soloeste,
                type:'POST',
                dataType:'json',
                cache:false,
                success:function(respuesta){
                    $.fancybox.close();
                         if (respuesta.codigo === 3) {
                        
                                    var msj = langFRM.TIENE_ASISTENCIAS_CARGADAS;/// 'La modificacion no puede realizarse.<br>Tiene  asistencias cargadas';

                              $.gritter.add({
                                                        title: langFRM.ERROR,
                                                        text: msj ,
                                                        sticky: false,
                                                        time: '3000',
                                                        class_name: 'gritter-error'
                                                    });

                                        revertFunc();
                                    

                                }
                                
                                
                                
                        if (respuesta.codigo === 1) {           
                    var unset=respuesta.custom.unset;
                             $(unset).each(function(){
                               $('#calendar').fullCalendar( 'removeEvents',this.id);   
                             });
                             
                             $.gritter.add({
                                    
                                    text: langFRM.validacion_ok,
                                    image: '',
                                    sticky: false,
                                    time: '3000',
                                    class_name:'gritter-success'
                        });
                             
                         }
                 }
            });
        
        return false;
   });
    
    
     
    }
    
   
    
    
});




</script>

