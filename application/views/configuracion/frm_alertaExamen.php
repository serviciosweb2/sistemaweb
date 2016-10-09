<script>
    var langFrm = <?php echo $langFrm?>;
</script>
<script src="<?php echo base_url('assents/js/configuracion/frm_alertaExamen.js')?>"></script>

<style>
    
   
    
    
    .chosen-results{
        
        
        max-height: 60px !important;
        
    }
  
    
</style>


    <div class="modal-content">
         <input value='<?php echo $selectPeriodos ?>' name="selectPeriodos" type="hidden">
        <form id="nuevaAlerta">
            <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="blue bigger"><?php echo lang('nueva_alerta');?></h4>
            </div>

            <div class="modal-body overflow-visible">
                    <div class="row">
                       

                        <div class="col-md-4 col-xs-12 form-group">
                            <label><?php echo lang('cantidad');?></label>
                            <input name="cantidad" value="" class="form-control inpu-sm">
                        </div>
                        
                        <div class="col-md-4 col-xs-12 form-group">
                            <label><?php echo lang('unidad_tiempo');?></label>
                            <select name="unidadTiempo"></select>
                        </div>
                        
                        <div class="col-md-4 col-xs-12 form-group">
                            <label><?php echo lang('tipo');?></label>
                            <select name="tipo">
                                <?php foreach($tipo_examen as $tipo){
                                    
                                    echo '<option value="'.$tipo['id'].'">'.$tipo['nombre'].'</option>';
                                    
                                }?>
                            </select>
                        </div>


                    </div>
            </div>

            <div class="modal-footer">
                    <button class="btn btn-sm btn-primary">
                            <i class="icon-ok"></i>
                            <?php echo lang('guardar');?>
                    </button>
            </div>
        </form>
    </div>
