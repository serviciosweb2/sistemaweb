<script src="<?php echo base_url('assents/js/caja/modificar_importe_caja.js') ?>"></script>
<script>
    var langFrm = <?php echo $langFrm; ?>;
</script>
<div class="modal-content">
    <div class="modal-header">
        <h4 class="blue bigger"><?php echo lang("modificar_importe_caja"); ?></h4>
    </div>
    <div class="modal-body">
        <?php if ($myMovimientoCaja->cod_concepto <> 'PARTICULARES'){ ?>
        <div class="form-group  col-md-12 col-xs-12">
            <label><?php echo lang("solo_se_pueden_editar_movimientos_particulares"); ?></label>
        </div>
        <?php } else { ?>
        <div class="row">
            <div class="form-group  col-md-6 col-xs-12">
                <label class="control-label"><?php echo lang("fecha") ?></label>
                <input type="text" class="form-control" readonly="true" value="<?php echo formatearFecha_pais($myMovimientoCaja->fecha_hora, true); ?>">
            </div>
            <div class="form-group  col-md-6 col-xs-12">
                <label class="control-label"><?php echo lang("medio_pago") ?></label>
                <input type="text" class="form-control" readonly="true" value="<?php echo lang($myMedioPago->medio); ?>">
            </div>            
        </div>
        <div class="row">
            <div class="form-group  col-md-12 col-xs-12">
                <label class="control-label"><?php echo lang("descripcion") ?></label>
                <input type="text" class="form_control" readonly="true" value="<?php echo $myMovimientoCaja->observacion ?>" style="width: 100%;">
            </div>
        </div>
        <div class="row">
            <div class="form-group  col-md-6 col-xs-12">
                <label class="control-label"><?php echo lang("tipo_movimiento") ?></label>
                <input type="text" class="form-control" readonly="true" value="<?php echo $myMovimientoCaja->haber > 0 ? lang("entrada_caja_cabecera") : lang("salida_caja_cabecera"); ?>">
            </div>
            <div class="form-group  col-md-6 col-xs-12">
                <label class="control-label"><?php echo lang("importe") ?></label>
                <input name="importe_movimiento_caja" type="text" class="form-control" value="<?php echo $myMovimientoCaja->haber > 0 ? $myMovimientoCaja->haber : $myMovimientoCaja->debe; ?>"
                       onkeypress="return ingresarFloat(this, event);">                
            </div>
        </div>
        <?php } ?>
    </div>
    <div class="modal-footer">
        <?php if ($myMovimientoCaja->cod_concepto == 'PARTICULARES'){ ?>
        <button class="btn  btn-success" type="buton" onclick="guardar_importe_movimiento(<?php echo $myMovimientoCaja->getCodigo(); ?>);">
            <i class="icon-ok bigger-110"></i>
            <?php echo lang('guardar'); ?>
        </button>
        <?php } ?>
    </div>
</div>