
<link rel="stylesheet" href="<?php echo base_url('assents/theme/assets/css/bootstrap-timepicker.css')?>"/>
<script src="<?php echo base_url('assents/theme/assets/js/date-time/bootstrap-timepicker.min.js')?>"></script>
<script src="<?php echo base_url('assents/js/configuracion/frm_nuevo_receso.js')?>"></script>
<div class="modal-content">
    <form id="nuevo_receso_filial">
        <?php
        $nombre = '';
          if($cod_receso != -1){
              $nombre = lang('modificar_receso');
          }else{
              $nombre = lang('nuevo_receso');
          }
        ?>
    <div class="modal-header">
            <h4 class="blue bigger"><?php echo $nombre;?></h4>
    </div>

    <div class="modal-body overflow-visible">
        
            <div class="row">
                <div class="form-group col-xs-12">
                    <?php
                    $fecha_desde = '';
                    $fecha_hasta ='';
                    $nombre = '';
                    $hora_desde = '';
                    $hora_hasta = '';
                    if($cod_receso != -1){
                        $fecha_desde = $array_lista_receso[0]['fecha_desde_formateada'];
                        $fecha_hasta = $array_lista_receso[0]['fecha_hasta_formateada'];
                        $nombre = $array_lista_receso[0]['nombre'];
                        $hora_desde = $array_lista_receso[0]['hora_desde'];
                        $hora_hasta = $array_lista_receso[0]['hora_hasta'];
                    }
                    ?>
                    <label><?php echo lang('nombre_receso');?></label>
                    
                    <input id="nombre_receso" class="form-control" name="nombre_receso_filial" type="text" value="<?php echo $nombre;?>"></input>
                    <label><?php echo lang('fecha_desde')?></label>
                    <input class="form-control" name="fecha_desde" id="fecha_desde_receso" placeholder="<?php echo lang('seleccione_fecha');?>" value="<?php echo $fecha_desde;?>">
                   
                    <label><?php echo lang('hora_desde');?>:</label>
                        
                           <div class='input-group bootstrap-timepicker'>
                                <input name="horaInicio" type='text' class="form-control inputHora" value="<?php echo $hora_desde;?>">
                                <span class="input-group-addon">
                                    <i class="icon-time"></i>
                                </span>
                            </div>

             
                
                    
                    
                    
                    <label><?php echo lang('fecha_hasta')?></label>
                    <input class="form-control" name="fecha_hasta" id="fecha_hasta_receso" placeholder="<?php echo lang('seleccione_fecha');?>" value="<?php echo $fecha_hasta;?>">
                    <label><?php echo lang('hora_hasta');?>:</label>
                        
                            <div class='input-group bootstrap-timepicker'  >
                                <input name="horaFin" type='text' class="form-control inputHora" value="<?php echo $hora_hasta;?>">
                                <span class="input-group-addon">
                                    <i class="icon-time"></i>
                                </span>
                            </div>
                    
                    <input type="hidden" name="cod_receso" value="<?php echo $cod_receso?>"></input>
                    
                </div>
            </div>
    </div>

    <div class="modal-footer">
            <button class="btn btn-sm btn-primary" type="submit" onclick="guardarReceso(event);">
                    <i class="icon-ok"></i>
                    <?php echo lang('guardar')?>
            </button>
    </div>
    </form>
</div>