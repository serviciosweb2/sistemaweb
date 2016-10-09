<form id="cambiarEstado">
    <input type="hidden" value="<?php echo $objEstado->getCodigo(); ?>"  name="codigo"/>
    <div class="row">
        <div class="form-group col-md-12 ">
            <label><?php echo lang('cambiar_a');?></label>
            <select class="form-control " name="estado">
                <option></option>
                <?php foreach ($estadosAca as $estado) {?>
                <option value="<?php echo $estado["codigo"] ?>" 
                    <?php if ($objEstado->estado == Vestadoacademico::getEstadoCursando() && $estado['codigo'] == Vestadoacademico::getEstadoRegular() &&  ($objEstado->porcasistencia < $asistencia_regular || $asistenciasPendientes > 0)){ ?>disabled="true"<?php } ?>>
                        <?php echo $estado["nombre"] ?>
                    <?php if ($objEstado->estado == Vestadoacademico::getEstadoCursando() && $estado['codigo'] == Vestadoacademico::getEstadoRegular()){ 
                        if ($asistenciasPendientes > 0){ ?>
                        (<?php echo lang("falta_carga_asistencia"); ?>)
                            <?php } else if ($objEstado->porcasistencia < $asistencia_regular){ ?>
                            (<?php echo lang("asistencia"); ?>&nbsp;
                            <?php echo $objEstado->porcasistencia == '' ? 0 : $objEstado->porcasistencia ?>%)    
                    <?php } }?>
                </option>
               <?php } ?>

            </select>
        </div>    
    </div>
    <div class="row">                    
        <div class="form-group col-md-12">
            <label><?php echo lang('comentario');?></label>
            <textarea class="form-control" name="comentario"></textarea>
        </div>  
    </div>
    <div class="alert alert-danger hide"></div>
</form>
        
