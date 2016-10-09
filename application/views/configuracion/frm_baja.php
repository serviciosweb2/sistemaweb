<script src="<?php echo base_url('assents/js/configuracion/frm_baja.js')?>"></script>
<div class="modal-content">
      <form id="baja">
          <input type="hidden" name="cod_usuario" value="<?php echo $cod_usuario?>">
    <div class="modal-header">
        <button type="button" class="close">&times;</button>
            <h4 class="blue bigger"><?php echo lang('cambiar_estado');?></h4>
    </div>

    <div class="modal-body overflow-visible">
            <div class="row">
                <div class="form-group col-md-12 col-xs-12">
                    <label><?php echo lang('motivo');?></label>
                    <select name="motivo" class="form-control" data-placeholder="<?php echo lang('seleccione_motivo')?>">
                        <option></option>
                        <?php 
                        foreach($motivos as $motivo){
                            
                            echo '<option value="'.$motivo['id'].'">'.$motivo['motivo'].'</option>';
                            
                        }
                        
                        ?>
                    </select>
                    
                </div>
            </div>
        
            <div class="row">
                <div class="form-group col-md-12 col-xs-12">
                    <label><?php echo lang('comentario');?></label>
                    <textarea class="form-control" name="comentario" placeholder ="<?php echo lang('comentario');?>"></textarea>
            
                </div>
            
            </div>
    </div>

    <div class="modal-footer">
            
            <button class="btn btn-sm btn-primary" type="submit">
                    <i class="icon-ok"></i>
                    <?php echo lang('guardar')?>
            </button>
    </div>
           </form>
</div>

