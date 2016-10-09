<script src="<?php echo base_url('assents/js/configuracion/configuracion_impresion_extra.js'); ?>"></script>
<div class="modal-header">
    <h4 class="blue bigger"><?php echo lang("configuracion_de_impresion") ?> <?php echo lang($script_name); ?></h4>    
</div>
<table style="margin: 10px 14px; width: 400px">
    <tr>
        <td style="width: 216px;">
            <?php echo lang("cantidad_de_copias"); ?>
        </td>
        <td>
            <select id="impresion_cantidad_copias" class="form-control" style="width: 52px;">
                <?php for ($i = 1; $i <= 5; $i++){ ?> 
                <option value="<?php echo $i ?>"
                        <?php if (isset($configuracion['copias']) && $configuracion['copias'] == $i){ ?> selected="true" <?php } ?>>
                    <?php echo $i ?>
                </option>    
                <?php } ?>
            </select>
        </td>
    </tr>
    <?php if ($id_script == 10){ ?>
    <tr>
        <td colspan="2">
            <input class="ace" type="checkbox" id="impresion_imprimir_razon" 
                <?php if (!isset($configuracion['imprimir_razon']) || $configuracion['imprimir_razon'] == 1){ ?>checked="true" <?php } ?>>
            <span class="lbl"><?php echo lang("imprimir_razon_social_alumnos"); ?></span>            
        </td>
    </tr>
    <?php } ?>
</table>
<div id="area_mensajes" style="height: 42px;" value=""></div>
<div class="modal-footer">
    <table style="float: right; margin-right: 16px;">
        <tbody>
            <tr>
                <td style="padding-right: 20px;">
                    <button id="btnCancelar" class="btn btn-danger btn-save" onclick="cerrarFancy();" data-last="Finish"><?php echo lang("cancelar"); ?></button>
                </td>
                <td>
                    <button id="btnGuardar" class="btn btn-success btn-save" onclick="guardarConfiguracionExtra();" data-last="Finish"><?php echo lang("guardar"); ?></button>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<input type="hidden" id="script_id" value="<?php echo $id_script ?>">