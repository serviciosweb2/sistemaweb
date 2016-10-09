<script src="<?php echo base_url('assents/js/configuracion/configuracion_impresion_extra.js'); ?>"></script>
<div class="modal-header">
    <h4 class="blue bigger"><?php echo lang("configuracion_de_impresion") ?> <?php echo lang($script_name); ?></h4>    
</div>
<table style="margin: 10px 14px; width: 400px">
    <tr>
        <td style="width: 216px;">
            <span class="lbl">
                <?php echo lang("cantidad_de_copias"); ?>
            </span>
        </td>
        <td>
            <select id="impresion_cantidad_copias" class="form-control" style="width: 130px;">
                <?php for ($i = 1; $i <= 5; $i++){ ?> 
                <option value="<?php echo $i ?>"
                        <?php if (isset($configuracion['copias']) && $configuracion['copias'] == $i){ ?> selected="true" <?php } ?>>
                    <?php echo $i ?>
                </option>    
                <?php } ?>
            </select>
        </td>
    </tr>
    <tr>
        <td><?php echo lang("formato_papel"); ?></td>
        <td>
            <select id="impresion_formato_papel" class="form-control" style="width: 130px;">
                <option value="A4" <?php if (isset($configuracion['papel']) && $configuracion['papel'] == "A4"){ ?>selected="true"<?php } ?>>A4</option>
                <option value="A5" <?php if (isset($configuracion['papel']) && $configuracion['papel'] == "A5"){ ?>selected="true"<?php } ?>>A5</option>
                <option value="folio1" <?php if (isset($configuracion['papel']) && $configuracion['papel'] == "folio1"){ ?>selected="true"<?php } ?>>Folio 1</option>
                <option value="folio2" <?php if (isset($configuracion['papel']) && $configuracion['papel'] == "folio2"){ ?>selected="true"<?php } ?>>Folio 2</option>
                <option value="folio3" <?php if (isset($configuracion['papel']) && $configuracion['papel'] == "folio3"){ ?>selected="true"<?php } ?>>Folio 3</option>
            </select>
        </td>
    </tr>
    <tr>
        <td>Modelo Factura Electrónica</td>
        <td>
            <select id="modelo_factura_electronica" style="width: 130px;">
                <option value="fe_inferior" 
                    <?php if (isset($configuracion['modelo_factura_electronica']) 
                            && $configuracion['modelo_factura_electronica'] == 'fe_inferior'){ ?> selected="true" <?php } ?>>
                    FE - Inferior
                </option>
                <option value="fe_superior"
                    <?php if (isset($configuracion['modelo_factura_electronica'])
                            && $configuracion['modelo_factura_electronica'] == 'fe_superior'){ ?> selected="true" <?php } ?>>
                    FE - Superior
                </option>
            </select>
        </td>
    </tr>
    <tr style="height: 34px;">
        <td colspan="2">
            <label style="font-size: 13px;">
                <input class="ace" type="checkbox" id="impresion_imprimir_razon"
                       <?php if (isset($configuracion['imprimir_razon']) && $configuracion['imprimir_razon'] == 1) { ?> checked="true" <?php } ?>>
                <span class="lbl"> <?php echo lang("imprimir_razon_social_alumnos"); ?></span>
            </label>
        </td>
    </tr>
    <tr style="height: 34px;">
        <td colspan="2">
            <label style="font-size: 13px;">
                <input class="ace" type="checkbox" id="impresion_muestra_cantidad_total_cuotas"
                       <?php if (isset($configuracion['muestra_total_cuotas']) && $configuracion['muestra_total_cuotas'] == 1) { ?> checked="true" <?php } ?>>
                <span class="lbl"> <?php echo lang("mostrar_la_cantidad_total_de_cuotas"); ?></span>
            </label>
        </td>
    </tr>
    <?php if ($agregarConfiguracionRUT){ ?>
    <tr style="height: 34px;">
        <td colspan="2">
            <label style="font-size: 13px;">
                <input class="ace" type="checkbox" id="impresion_muestrar_ruc"
                       <?php if (!isset($configuracion['mostrar_ruc']) || $configuracion['mostrar_ruc'] == 1) { ?>checked="true"<?php } ?>>
                <span class="lbl"><?php echo lang("mostrar_ruc") ?></span>
            </label>
        </td>
    </tr>
    <tr style="height: 34px;">
        <td colspan="2">
            <label style="font-size: 13px;">
                <input class="ace" type="checkbox" id="impresion_muestrar_com"
                       <?php if (!isset($configuracion['mostrar_com']) || $configuracion['mostrar_com'] == 1) { ?>checked="true"<?php } ?>>
                <span class="lbl"><?php echo lang("mostrar_comision") ?></span>
            </label>
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
                    <button id="btnGuardar" class="btn btn-success btn-save" onclick="guardarConfiguracionFacturacion();" data-last="Finish"><?php echo lang("guardar"); ?></button>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<input type="hidden" id="script_id" value="<?php echo $id_script ?>">