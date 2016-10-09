<script src="<?php echo base_url('assents/js/resumendecuenta/frm_recuperar_descuentos_condicionados.js') ?>"></script>
<script>
    var langFrm_recuperar_descuentos_condicionados = <?php echo $langFRM; ?>
</script>
<?php $arrCtacte = array(); ?>
<div class="modal-content" >
    <div class="modal-header">
        <h3 class="blue bigger">
            <?php echo lang('recuperar_descuentos_condicionados'); ?>
        </h3> 
    </div>
    <div class="modal-body overflow-visible" >            
        <div class="row" >
            <div class="form-group col-md-12">
                <table class="table table-striped table-bordered" oncontextmenu="return false" onkeydown="return false" cellspaciong="0"
                    border="0" id="table_descuentos_condicionados_recuperar">
                    <thead>
                        <tr>
                            <th><?php echo lang("facturar_descripcion"); ?></th>
                            <th><?php echo lang("importe_actual"); ?></th>
                            <th><?php echo lang("importe_recuperado"); ?></th>
                        </tr>
                    </thead>
                    <?php foreach ($arrDescuentos as $descuento){ ?>
                    <tr>
                        <td><?php echo $descuento['descripcion'] ?></td>
                        <td><?php echo $descuento['simbolo_moneda']." ".$descuento['importeformateado'] ?></td>
                        <td>
                            <?php
                            $importe = round($descuento['importe'] - ($descuento['importe'] * $descuento['descuento'] / 100), 2);
                            $importe = str_replace(".", $simbolo_decimal, $importe);
                            echo $descuento['simbolo_moneda']." ".$importe;
                            $arrCtacte[$descuento['codigo']] = $descuento['descripcion'];
                            ?>
                        </td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-12" <?php if (count($arrCtacte) <= 1){ ?>style="display: none;"<?php } ?>>
                <label><?php echo lang("recuperar_desde_la_cuota"); ?>:</label>
                <select name="recuperar_desde_cuota" class="select_chosen">
                    <option value="-1"><?php echo lang("primera_cuota") ?></option>
                    <?php foreach ($arrCtacte as $codigo => $ctacte){ ?> 
                    <option value="<?php echo $codigo ?>">
                        <?php echo $ctacte ?>
                    </option>
                    <?php } ?>
                </select>
            </div>            
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn  btn-success" id="btn-guardar" onclick="recuperarDescuentoCondicionado(<?php echo $cod_matricula ?>);" name="btn_guardar_descuento">
            <?php echo lang('recuperar_descuento'); ?>
            <i class="icon-arrow-right icon-on-right bigger-110"></i>
        </button>
    </div>
</div>