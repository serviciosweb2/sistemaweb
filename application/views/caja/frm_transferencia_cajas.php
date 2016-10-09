<script>
    var langFrm = <?php echo $langFrm ?>;
</script>
<script src="<?php echo base_url('assents/js/caja/frm_transferencia_cajas.js')?>"></script>

<div class="modal-content" name="frm_transferencia_cajas">
    <div class="modal-header">
        <!--<button class="close" data-dismiss="modal" type="button">×</button>-->
        <h4 class="blue bigger"><?php echo lang("transferencia_entre_cajas"); ?></h4>
    </div>
    <div class="modal-body overflow-visible">
        <div class="row">
            <div class="form-group col-md-12 col-xs-12">
                <label><?php echo lang("caja_origen") ?></label><br>
                <select name="transferencia_caja_origen" class="select-chosen form-control">
                    <?php foreach ($cajas_origen as $caja){ ?> 
                    <option value="<?php echo $caja['codigo'] ?>"
                            <?php if ($caja['codigo'] == $caja_salida){ ?> selected="true" <?php } ?>>
                        <?php echo $caja['nombre'] ?>
                    </option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-12 col-xs-12">
                <label><?php echo lang("caja_destino"); ?></label><br>
                <select name="transferencia_caja_destino" class="select-chosen form-control">
                    <?php foreach ($cajas_destino as $caja){ ?> 
                    <option value="<?php echo $caja['codigo'] ?>" 
                        <?php if ($caja_salida != $caja['codigo']){ ?> selected="true" <?php } ?>>
                        <?php echo $caja['nombre'] ?>
                    </option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6 col-xs-12">
                <label><?php echo lang("importe") ?></label>
                <input type="text" value="0" name="transferencia_importe" class="form-control" onkeypress="return ingresarFloat(this, event)">
            </div>
            <div class="form-group col-md-6 col-xs-12">
                <label><?php echo lang("medioPago_factura") ?></label>
                <select name="transferencia_medio_pago" class="select-chosen from-control" style="width: 208px;">
                    <?php foreach ($medios_pago as $medio_pago){ ?> 
                    <option value="<?php echo $medio_pago['codigo'] ?>">
                        <?php echo lang($medio_pago['medio']); ?>
                    </option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-12 col-xs-12">
                <label><?php echo lang("descripcion") ?></label>
                <input type="text" value="" name="transferencia_descripcion" class="form-control" style="width: 442px;">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-sm btn-primary" type="" name="abrir_caja" onclick="guardarTransferencia();">
            <i class="icon-ok"></i>
            <?php echo lang("transferir") ?>
        </button>
    </div>
</div>