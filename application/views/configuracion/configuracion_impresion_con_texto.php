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
                <?php for ($i = 1; $i <= 5; $i++) { ?> 
                    <option value="<?php echo $i ?>"
                            <?php if (isset($configuracion['copias']) && $configuracion['copias'] == $i) { ?> selected="true" <?php } ?>>
                                <?php echo $i ?>
                    </option>    
                <?php } ?>
            </select>
        </td>
    </tr>
    <?php if ($id_script == 5) { ?>
        <tr>
            <td style="width: 216px; padding-top: 5px;">
                <?php echo lang("imprime_plan_academico") ?>
            </td>
            <td style="padding-top: 5px;">
                <input id="imprimeCurso" value="1" class="ace ace-switch ace-switch-6" type="checkbox"  <?php echo $imprime_plan == 1 ? 'checked' : '' ?>/>
                <span class="lbl"></span>
            </td>
        </tr>
        <tr>
            <td style="width: 216px; padding-top: 5px;">
                <?php echo lang("imprime_titulo") ?>
            </td>
            <td style="padding-top: 5px;">
                <input id="imprimeTitulo" value="1" class="ace ace-switch ace-switch-6" type="checkbox"  <?php echo $imprime_titulo == 1 ? 'checked' : '' ?>/>
                <span class="lbl"></span>
            </td>
        </tr>
    <?php } ?>
    <tr>
        <td colspan="2">
            <?php
            switch ($id_script) {
                case 5:
                    echo lang("texto_pie") . " " . lang("matriculas");
                    break;

                case 1:
                    echo lang("texto_pie") . " " . lang("presupuestos");
                    break;

                default:
                    echo "";
                    break;
            }
            ?>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <textarea id="impresion_pie_matriculas" style="width: 398px; height: 170px; resize: none;"><?php echo isset($texto) ? $texto : ""; ?></textarea>
        </td>
    </tr>
    <?php if ($id_script == 5 && $muestra_foro) { ?>
        <tr>
            <td style="width: 320px; padding-top: 5px;">
                <?php echo lang("prestacion_de_servicio") . ' / ' . lang("foro_da_comarca") ?>
            </td>
        </tr>
        <tr>
            <td colspan="12">
                <div class="col-md-6 form-group"> 
                    <label><?php echo lang('provincia'); ?></label>
                    <select class="form-control input-sm" name="provincia_foro" data-placeholder="<?php echo lang('seleccionar_provincia'); ?>" tabindex="7">
                        <option></option>

                        <?php
                        foreach ($provincias as $list_prov) {
                            $selected = $list_prov['id'] == $provincia_foro ? 'selected' : '';

                            echo '<option value="' . $list_prov['id'] . '" ' . $selected . '>' . $list_prov['nombre'] . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-6 form-group"> 
                    <label><?php echo lang('localidad') ?></label>
                    <select class="form-control input-sm" id="localidad_foro" name="localidad_foro" data-placeholder="<?php echo lang('seleccionar_localidad'); ?>" tabindex="8" <?php echo $selectLocalidad ?>>
                        <optio>
                            </option>
                            <?php
                            foreach ($localidades as $lista_loc) {
                                if ($lista_loc['id'] == $localidad_foro) {
                                    echo '<option value="' . $lista_loc['id'] . '" selected>' . $lista_loc['nombre'] . '</option>';
                                } else {
                                    echo '<option value="' . $lista_loc['id'] . '">' . $lista_loc['nombre'] . '</option>';
                                }
                            }
                            ?>
                    </select>

                </div>
            </td>
        </tr>
    <?php } else if ($id_script == 1){ ?> 
        <tr>
            <td  style="width: 320px; padding-top: 5px;">
                <input type="checkbox" name="mostrar_precio_lista_descuento" <?php if ($mostrarPrecioListaYDescuento == 1){ ?>checked="true"<?php } ?>>
                Mostrar Precio de Lista Y descuentos.
            </td>
        </tr>
    <?php }?>
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
                    <button id="btnGuardar" class="btn btn-success btn-save" onclick="guardarConfiguracionImpresionConTexto();" data-last="Finish"><?php echo lang("guardar"); ?></button>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<input type="hidden" id="script_id" value="<?php echo $id_script ?>">