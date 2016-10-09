<script>
var langFrm = <?php echo $langFrm?>;
</script>
<script src="<?php echo base_url('assents/js/configuracion/frm_terminal.js') ?>"></script>
<?php
$cod_terminal = '-1';
$cod_punto_venta = '';
$cod_interno = '';
$tipo_captura = '';
$estado = '';
if ($codigo != '-1') {
    $cod_terminal = $codigo;
    $cod_punto_venta = $terminal['cod_punto_venta'];
    $cod_interno = $terminal['cod_interno'];
    $tipo_captura = $terminal['tipo_captura'];
    $estado = $terminal['estado'];
}
?>
<style>

    .checkbox{

        padding: 0px !important;
        margin: 0px !important;
    }

</style>
<div class="modal-content">
    <div class="modal-header">
        <h4 class="blue bigger">
            <?php
            if ($codigo != '-1') {
                echo lang('modificar_terminal');
            } else {
                echo lang('nueva_terminal');
            }
            ?>

        </h4>
    </div>
    <form id="frmProveedorPos">
        <input type="hidden" name="codigo" value='<?php echo $codigo ?>' />
        <div class="modal-body overflow-visible">
            <div class="row">
                <div class="col-md-12 col-xs-12">   
                    <div class="row">
                        <div class="form-group col-md-6 col-xs-12">

                            <label><?php echo lang('operador') ?></label>

                            <select data-placeholder="<?php echo lang('seleccione_operador'); ?>" name="operador_pos"<?php
                            if ($cod_terminal != '-1') {
                                echo 'disabled';
                            }
                            ?>>
                                <option></option>
                                <?php
                                foreach ($contratos as $rowcontrato) {
                                    $selected = $cod_punto_venta == $rowcontrato['codigo'] ? 'selected' : '';
                                    if (($rowcontrato['estado_contrato'] == 'habilitado' && $rowcontrato['estado_ptovta'] == 'habilitado') || $cod_punto_venta == $rowcontrato['codigo']) {
                                        echo '<option value="' . $rowcontrato['codigo'] . '" ' . $selected . '><div color="red">' . $rowcontrato['nombre'] . '</div> / ' . $rowcontrato['facturante'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group col-md-6 col-xs-12">
                            <label><?php echo lang('codigo_interno') ?></label></br>
                            <input type="text" name="codigo_interno" value="<?php echo $cod_interno?>">
                        </div>

                    </div>
                    <div class="row">
                        <div class="form-group col-md-6 col-xs-12">
                            <label><?php echo lang('tipo_captura') ?></label>

                            <select data-placeholder="" name="tipo_captura">
                                <?php
                                foreach ($capturas as $rowcaptura) {
                                    $selected = $tipo_captura == $rowcaptura['codigo'] ? 'selected' : '';
                                    echo '<option value="' . $rowcaptura['codigo'] . '" ' . $selected . '>' . $rowcaptura['nombre'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                    </div>
                    <div class="row">
                        <div class="form-group col-md-6 col-xs-12">
                            <label><?php echo lang('tarjetas') ?></label>
                            <div class="row">
                                <table id="tarjetascredito">

                                    <?php foreach ($tarjetas as $tarjeta) { ?>
                                        <tr>
                                            <td>
                                                <input name="tarjetas[]" class="ace ace-checkbox-2" type="checkbox" <?php echo in_array($tarjeta['codigo'], $tarjetas_terminal) ? "checked" : ""; ?> value="<?php echo $tarjeta['codigo'] ?>" >
                                                <span class="lbl"><?php echo $tarjeta['nombre'] ?></span>
                                            </td>
                                        </tr>

                                    <?php } ?>


                                </table>
                            </div>
                        </div>


                        <div class="form-group col-md-6 col-xs-12">
                            <label><?php echo lang('tarjetas') ?></label>
                            <div class="row">
                                <table id="tarjetasdebito">

                                    <?php foreach ($tarjetasDebito as $tarjeta) { ?>
                                        <tr>
                                            <td>
                                                <input name="tarjetasDebito[]" class="ace ace-checkbox-2" type="checkbox" <?php echo in_array($tarjeta['codigo'], $tarjetas_debito_terminal) ? "checked" : ""; ?> value="<?php echo $tarjeta['codigo'] ?>" >
                                                <span class="lbl"><?php echo $tarjeta['nombre'] ?></span>
                                            </td>
                                        </tr>

                                    <?php } ?>


                                </table>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="form-group col-md-6 col-xs-12">

                            <label>
                                <input name="estado" class="ace ace-switch ace-switch-6" type="checkbox" <?php echo $estado != 'inhabilitado' ? 'checked' : '' ?>>
                                <span class="lbl"> &nbsp; <?php echo lang('HABILITADO'); ?></span>
                            </label>

                        </div>

                    </div>

                </div>

            </div>    

        </div>
    </form>
    <div class="modal-footer">


        <button class="btn btn-sm btn-primary btn-guardar ">
            <i class="icon-ok"></i>
            <?php echo lang('guardar') ?>
        </button>
    </div>
</div>
