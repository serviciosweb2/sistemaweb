<script>
    var langFrm = <?php echo $langFrm?>;
</script>
<script src="<?php echo base_url('assents/js/caja/frm_cerrar_caja.js')?>"></script>

<div class="modal-content" name="frm_cerrar_caja">
    <div class="modal-header">
        <!--<button class="close" data-dismiss="modal" type="button">×</button>-->
        <h4 class="blue bigger"><?php echo lang("cierre_de_caja"); ?></h4>
    </div>
    <div class="modal-body overflow-visible">
        <div class="row" id="div_cerrar_cajas">
            <?php foreach ($cajas as $caja){ ?>
            <div class="row">
                <div class="form-group  col-md-12 col-xs-12" style="margin-bottom: 0px; height: 20px;">
                    <label><?php echo lang($caja['medio']) ?></label>
                </div>
            </div>
            <div class="row">
                <div class="form-group  col-md-6 col-xs-12">
                    <input type="text" class="form-control" name="valor_caja" id="<?php echo $caja['codigo'] ?>"
                           value="<?php echo $caja['saldo_concepto_formateado'] ?>" readonly="true">
                </div>
                <div class="form-group  col-md-6 col-xs-12">
                    <input type="text" class="form-control" name="valor_caja_<?php echo $caja['codigo'] ?>"
                           value="0">
                </div>
            </div>
            <?php } ?>
        </div>
        <div  class="row" id="div_preguntar_arqueo_caja" style="display: none; height: 80px;">
            <div class="row">
                <div class="form-group  col-md-12 col-xs-12" style="margin-bottom: 0px; height: 20px;">
                    <?php echo lang("alguno_de_los_saldos_ingresados_no_corresponden_con_los_saldos_reales"); ?><br>
                    <?php echo lang("quiere_agregar_movimientos_de_caja_para_compensar_los_valores"); ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal-footer">
        <button class="btn btn-sm btn-primary" type="" name="cerrar_caja" onclick="cerrarCaja();">
            <i class="icon-ok"></i>
            <?php echo lang("cerrar"); ?>
        </button>
        <button class="btn btn-sm btn-danger" type="" name="cancelar_preguntar_movimiento" onclick="canclearAgregarMovimiento();" style="display: none;">
            <?php echo lang("cancelar"); ?>
        </button>
        <button class="btn btn-sm btn-primary" type="" name="aceptar_agregar_movimiento" onclick="agregarMovimientoCaja();" style="display: none;">
            <?php echo lang("aceptar"); ?>
        </button>
    </div>
</div>
<input type="hidden" value="<?php echo $codigo_caja ?>" name="codigo_caja_cerrar">