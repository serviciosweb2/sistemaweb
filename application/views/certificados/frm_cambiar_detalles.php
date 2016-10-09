<script>
    var langFrm = <?php echo $langFrm ?>;
</script>
<script src="<?php echo base_url('assents/js/certificados/frm_cambiar_detalles.js')?>"></script>
<input type="hidden" name="certificacion" value="<?php echo isset($certificacion) ? $certificacion : '' ?>">
<div class="modal-content">
    <form id="modificarDetalle">
        <div class="modal-header">
            <h4 class="blue bigger"><?php echo lang('modificar')?></h4>
        </div>
        <div class="modal-body overflow-visible">
            <div class="row">
                <div class="col-md-6 col-xs-12 form-group">
                    <label><?php echo lang('fecha_inicio')?></label>
                    <input name="fecha_inicio" class="form-control fecha" value="<?php echo $fechas['fecha_inicio']?>">
                </div>                
                <div  class="col-md-6 col-xs-12 form-group">
                    <label><?php echo lang('fecha_fin')?></label>
                    <input name="fecha_fin" class="form-control fecha" value="<?php echo $fechas['fecha_fin']?>">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input name='certificados' type="hidden" value='<?php echo json_encode($certificados)?>'>            
            <button class="btn btn-sm btn-primary" type="submit">
                <i class="icon-ok"></i>
                <?php echo lang('guardar')?>
            </button>
        </div>
    </form>
</div>