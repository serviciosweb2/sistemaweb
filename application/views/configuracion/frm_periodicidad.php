<script>
    var langFrm = <?php echo $langFrm?>;
</script>
<script src="<?php echo base_url('assents/js/configuracion/frm_periodicidad.js')?>"></script>
<style>
    
        .chosen-results{
        
        
        max-height: 70px !important;
        
    }
    
    
</style>

<input name="selectPeriodos" type="hidden" value='<?php echo $selectPeriodos ?>'>


<div class="modal-content">
    <form id="frmPeriodicidad">
        <input name="codigo" type="hidden" value="<?php echo  $codigo;?>">
    <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="blue bigger"><?php echo lang('nueva_periodicidad')?></h4>
    </div>

    <div class="modal-body overflow-visible">
            <div class="row">
                <div class="col-md-12">
                    
                    <div class="col-md-6 form-group">
                        <label><?php echo lang('valor')?></label>
                        <input class="form-control" name="valor" value="">
                    </div>
                    
                    <div class="col-md-6 form-group">
                        <label><?php echo lang('unidad_tiempo')?></label>
                        <select name="unidadTiempo" class="form-control"><option></option></select>
                        
                    </div>
                    
                     
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

