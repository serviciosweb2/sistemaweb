<table>
    <tr style="background-color: #2A65A7">
        <td colspan="2" style="color: #FFFFFF; font-weight: bold; padding-left: 8px; padding-top: 10px;">
            <?php echo lang("nueva_consulta"); ?>
        </td>
    </tr>
    <tr>
        <td style="padding-left: 10px;">
            <table style="width: 220px;">
                <tr>
                    <td>
                        <label for="asunto"><?php echo lang("asunto"); ?></label><br>
                        <select id="asunto" class="chosen-select" style="width: 280px;">
                            <option value="-1">(<?php echo lang("seleccionar") ?>)</option>
                            <?php foreach ($arrCursos as $curso){ ?> 
                            <option value="<?php echo $curso['codigo'] ?>">
                                <?php echo $curso[$campo_curso]; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </td>        
                </tr>
                <tr>
                    <td>
                        <label for="nombre_apellido"> <?php echo lang("nombre_y_apellido"); ?> </label>
                        <div>
                            <input id="nombre_apellido" class="col-xs-10 col-sm-5" type="text" placeholder="name" style="width: 280px;">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="telefono"> <?php echo lang("telefono"); ?> </label>
                        <div>
                            <input id="telefono" class="col-xs-10 col-sm-5" type="text" placeholder="telephone" style="width: 280px;">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="email"> <?php echo lang("email") ?> </label>
                        <div>
                            <input id="email" class="col-xs-10 col-sm-5" type="email" placeholder="emial@email.com" style="width: 280px;">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="email"> <?php echo lang("como_nos_conocio") ?> </label>
                        <select id="como_nos_conocio_codigo" class="chosen-select" style="width: 280px;">
                            <option value="-1">(<?php echo lang("seleccionar") ?>)</option>
                            <?php foreach ($arrMedios as $cnc){ ?> 
                            <option value="<?php echo $cnc['codigo'] ?>">
                                <?php echo $cnc['nombre']; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
            </table>
        </td>
        <td style="vertical-align: top; padding-right: 10px;">
            <table>
                <tr>
                    <td>
                        <label for="consulta"><?php echo lang("consulta"); ?></label><br>
                        <textarea id="consulta" class="form-control" placeholder="Default Text" style="height: 236px; width: 378px;"></textarea>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="padding-bottom: 13px; padding-right: 10px; padding-top: 12px; text-align: right;">
            <button id="btnCancelar" class="btn btn-danger btn-save" data-last="Finish" onclick="cerrarFancy();" style="margin-right: 8px;"><?php echo lang("cancelar") ?></button>
            <button id="btnGuardar" class="btn btn-success btn-save" data-last="Finish" type="submit" onclick="guardarConsulta();"><?php echo lang("guardar"); ?></button>
        </td>
    </tr>
</table>
<script>
    $(".chosen-select").chosen();
</script>