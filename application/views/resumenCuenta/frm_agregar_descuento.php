<?php $disabled = $importe_total == 0 ? "disabled='true'" : ""; ?>
<script src="<?php echo base_url('assents/js/resumendecuenta/frm_agregar_descuento.js') ?>"></script>
<?php $tieneDescuentoFormaPago = false; ?>
<div class="modal-content" >
    <div class="modal-header">
        <h3 class="blue bigger">
            <?php echo lang('agregar_descuento'); ?>
        </h3> 
    </div>
    <div class="modal-body overflow-visible" >            
        <div class="row" >
            <div class=" col-md-12">
                <table class="table table-striped table-bordered " oncontextmenu="return false"
                       onkeydown="return false" cellspacing="0" cellpadding="0" border="0" id="ctacteDescuentos">
                    <thead>
                        <tr>
                            <th><?php echo lang("tipo_de_descuento"); ?></th>
                            <th><?php echo lang("descuento"); ?> %</th>
                            <th><?php echo lang("dias_de_vencida"); ?></th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <?php foreach ($arrDescuentos as $descuento){ 
                        if ($descuento['forma_descuento'] == 'plan_pago'){
                            $tieneDescuentoFormaPago = true;
                        } ?> 
                    <tr <?php if ($descuento['forma_descuento'] == 'plan_pago'){ ?>style="color: green;"<?php } ?>>
                        <td><?php echo lang($descuento['estado']); ?></td>
                        <td><?php echo $descuento['descuento']; ?></td>
                        <td>
                            <?php echo $descuento['estado'] == Vmatriculaciones_ctacte_descuento::getEstadoCondicionado() ? $descuento['dias_vencido'] : "---"; 
                            $suma_descuento += round(($importe_total * $descuento['descuento'] / 100), 2);
                            ?>
                        </td>
                        <td>
                            <?php if ($descuento['activo'] == 1){ ?> 
                                <i class="icon-trash bigger-140 red" style="cursor: pointer" onclick="eliminar_descuento_condicionado(<?php echo $descuento['codigo'] ?>, this)" title="<?php echo lang('quitar_descuento_condicionado + ') ?>"></i>
                            <?php } else { ?> 
                                <i class="icon-reply icon-only bigger-140 green" style="cursor: pointer" onclick="activar_descuento_condicionado(<?php echo $descuento['codigo'] ?>, this)" title="<?php echo lang('reactivar_descuento_condicionado') ?>"></i>
                            <?php } ?>
                        </td>
                    </tr>    
                    <?php } ?>
                </table>
            </div>
        </div>
        <?php if ($tieneDescuentoFormaPago){ ?>
        <div class="row">
            <div class="from-group col-md-12 col-xs-12">
                <label style="color: green; font-size: 11px;"><b>(*)</b> <?php echo lang("representan_descuentos_del_plan_de_pago") ?></label>
            </div>
        </div>
        <?php } ?>
        <div class="row">
            <div class="form-group col-md-3 col-xs-12">
                <label><?php echo lang("forma_de_descuento"); ?></label>
                <select name="forma_descuento" class="select_chosen form-control input-sm" onchange="calcular_total();" <?php echo $disabled ?>>
                    <option value="importe"><?php echo lang("importe"); ?></option>
                    <option value="porcentaje"><?php echo lang("porcentaje"); ?></option>
                </select>
            </div>
            <div class="form-group col-md-3 col-xs-12">
                <label><?php echo lang("valor"); ?></label><br>
                <input type="text" value="0" name="valor_descuento" class="form-control input-sm"  onchange="calcular_total();"
                       onkeypress="return ingresarFloat(this, event, '<?php echo $separador_decimal; ?>');" <?php echo $disabled ?>>
            </div>
            <div class="form-group col-md-3 col-xs-12">
                <label><?php echo lang("tipo_de_descuento"); ?></label>
                <select name="tipo_descuento" class="select_chosen form-control"  onchange="tipo_descuento_change(this);" <?php echo $disabled ?>>
                    <option value="<?php echo Vmatriculaciones_ctacte_descuento::getEstadoCondicionado(); ?>">
                        <?php echo lang("condicionado"); ?>
                    </option>
                    <option value="<?php echo Vmatriculaciones_ctacte_descuento::getEstadoNoCondicionado(); ?>">
                        <?php echo lang("no_condicionado"); ?>
                    </option>
                </select>
            </div>
            <div class="form-group col-md-3 col-xs-12" id="div_dias_vencida">
                <label><?php echo lang("dias_de_vencida"); ?></label>
                <input type="text" value="0" name="dias_vencida"  class="form-control input-sm" <?php echo $disabled ?>
                       onkeypress="return ingresarNumero(this, event);">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-12">
                <?php echo lang("importe_final"); ?>:
                <?php if ($importe_total > 0){ ?>
                &nbsp;<?php echo $simbolo_moneda; ?>
                <span id="vista_importe_final">
                    <?php echo $importe_total; ?>
                </span>
                <?php } else { ?> 
                <b><?php echo lang('la_linea_de_cuenta_corriente_seleccionada_no_posee_saldo_para_aplicar_descuento'); ?></b>    
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="modal-footer">        
        <button type="button" class="btn  btn-success" id="btn-guardar" onclick="guardarDescuentoCondicionado();" name="btn_guardar_descuento" <?php echo $disabled ?>>
            <?php echo lang('guardar'); ?>
            <i class="icon-arrow-right icon-on-right bigger-110"></i>
        </button>
    </div>
</div>
<script>
    var cod_ctacte = <?php echo $cod_ctacte; ?>;
    var separador_decimal = '<?php echo $separador_decimal; ?>';
    var importe_total = <?php echo $importe_total; ?>;
    var langFrm_agregar_descuento = <?php echo $lang; ?>;
</script>