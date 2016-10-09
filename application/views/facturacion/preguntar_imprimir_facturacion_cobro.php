<center>
<div class="modal-header">
    <h4 class="blue bigger"><?php echo lang("seleccion_de_la_impresion") ?></h4>
</div>
    <table style="margin: 20px 35px 0px 35px;">
        <tr>
            <td><h4><?php echo lang("factura"); ?></h4></td>
        </tr>
        <tr>
            <td><?php echo lang("seleccione_impresora"); ?></td>
        </tr>
        <tr>
            <td>
                <select name="impresora_imprimir" id="impresora_imprimir_factura" class="chosen-select" style="width: 320px;">
                    <option value="-2"><?php echo lang("no_imprimir"); ?></option>
                    <option value="-1" <?php if ($printer_default_facturas == '' || $printer_default_facturas == -1){ ?> selected="true" <?php } ?>>
                        <?php echo lang("Imprimir_por_el_navegador"); ?>
                    </option>
                    <?php foreach ($arrImpresoras as $impresora){ ?> 
                    <option value="<?php echo $impresora['printer_id'] ?>"
                            <?php if ($impresora['printer_id'] == $printer_default_facturas){ ?> selected="true" <?php } ?>>
                        <?php echo $impresora['display'] ?>
                    </option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <table style="width: 100%">
                    <tr>
                        <td>
                            <?php echo lang("cantidad_de_copias"); ?>&nbsp;
                        </td>
                        <td>
                            <select id="impresion_cantidad_copias_factura" class="form-control" style="width: 68px; float: right;">
                                <?php for ($i = 1; $i <= 5; $i++){ ?> 
                                <option value="<?php echo $i ?>"
                                        <?php if (isset($cantidad_copias_facturas) && $cantidad_copias_facturas == $i){ ?> selected="true" <?php } ?>>
                                    <?php echo $i; ?>
                                </option>    
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td><h4><?php echo lang("recibo_de_cobros"); ?></h4></td>
        </tr>
        <tr>
            <td><?php echo lang("seleccione_impresora"); ?></td>
        </tr>
        <tr>
            <td>
                <select name="impresora_imprimir" id="impresora_imprimir_cobros" class="chosen-select" style="width: 320px;">
                    <option value="-2"><?php echo lang("no_imprimir"); ?></option>
                    <option value="-1" <?php if ($printer_default_cobros == '' || $printer_default_cobros == -1){ ?> selected="true" <?php } ?>>
                        <?php echo lang("Imprimir_por_el_navegador"); ?>
                    </option>
                    <?php foreach ($arrImpresoras as $impresora){ ?> 
                    <option value="<?php echo $impresora['printer_id'] ?>"
                            <?php if ($impresora['printer_id'] == $printer_default_cobros){ ?> selected="true" <?php } ?>>
                        <?php echo $impresora['display'] ?>
                    </option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <table style="width: 100%">
                    <tr>
                        <td>
                            <?php echo lang("cantidad_de_copias"); ?>&nbsp;
                        </td>
                        <td>
                            <select id="impresion_cantidad_copias_cobros" class="form-control" style="width: 68px; float: right;">
                                <?php for ($i = 1; $i <= 5; $i++){ ?> 
                                <option value="<?php echo $i ?>"
                                        <?php if (isset($cantidad_copias_cobros) && $cantidad_copias_cobros == $i){ ?> selected="true" <?php } ?>>
                                    <?php echo $i; ?>
                                </option>    
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td><hr></td>
        </tr>
    </table>
    <div id="area_mensajes" value="" style="height: 20px;"></div>
    <div class="modal-footer">
        <table style="float: right; margin-right: 16px;">
            <tr>
                <td style="padding-right: 20px;">
                    <button id="btnCancelar" class="btn btn-danger btn-save" data-last="Finish" onclick="cerrarPreguntarImprimir();"><?php echo lang("cancelar"); ?></button>
                </td>
                <td>
                    <button id="btnImprimir" class="btn btn-success btn-save" data-last="Finish" onclick="imprimirFacturaYRecibo();"><?php echo lang("imprimir"); ?></button>
                </td>
            </tr>
        </table>
    </div>
</center>
<input type="hidden" value="<?php echo $factura ?>" id="codigo_factura">
<input type="hidden" value="<?php echo $cobro ?>" id= "codigo_cobro">
<script>
    $(".chosen-select").chosen();
</script>