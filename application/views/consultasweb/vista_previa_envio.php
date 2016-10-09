<center>
    <table>
        <tr>
            <td style="padding-left: 26px;">
                <h4><?php echo lang("vista_previa"); ?></h4>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $html; ?>
            </td>
        </tr>
        <tr>
             <td colspan="2" style="padding-bottom: 13px; padding-right: 10px; padding-top: 12px; text-align: right;">
                <table>
                    <tr>
                        <td style="width: 320px; text-align: center;">
                            <div id="area_de_notificacion" class="help-block"></div>
                        </td>
                        <td style="width: 340px; text-align: right;">
<!--                            <button id="btnCancelar" class="btn btn-danger btn-save" data-last="Finish" onclick="cancelarResponderConsulta();" style="margin-right: 8px;"><?php echo lang("cancelar"); ?></button>
                            <button id="btnAnterior" class="btn btn-success btn-save" data-last="Finish" type="submit" onclick="responderConsultaSeleccionaTemplates();"><< <?php echo lang("volver"); ?></button>
                            <button id="btnSiguiente" class="btn btn-success btn-save" data-last="Finish" type="submit" onclick="enviarTemplate();"><?php echo lang("enviar"); ?></button>-->
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</center>
<?php foreach ($templates_seleccionados as $template){ ?> 
<input type="hidden" name="select_templates" value="<?php echo $template ?>">
<?php } ?>
<input type="hidden" value="<?php echo $cod_consulta ?>" id="codigo_consulta_responder">