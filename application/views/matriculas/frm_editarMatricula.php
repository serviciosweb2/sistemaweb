<script src="<?php echo base_url() ?>assents/js/jquery.validate.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url('assents/css/matricula/frm_matricula.css') ?>"/>
<script src="<?php echo base_url('assents/js/librerias/ajaxchosen/lib/ajax-chosen.js') ?>"></script>
<script>
    var frmLang = <?php echo $frmLang ?>;
</script>
<script src="<?php echo base_url('assents/js/matriculas/frm_editar_matricula.js') ?>"></script>
<script src="<?php echo base_url('assents/theme/assets/js/jquery.maskedinput.min.js') ?>"></script>
<div class="modal-content">
    <form id="editarMatricula" >
        <div class="modal-header">
            <h4 class="blue"><?php echo lang('matricula') ?></h4>
        </div>
        <div class="modal-body" >
            <div class="col-md-12 ">
                <?php if ($cod_alumno != -1) { ?>
                    <input id="cod_alumno" name="cod_alumno" type="hidden" value="<?php echo $cod_alumno ?>" />
                <?php } ?>
                <?php if ($cod_plan_academico != -1) { ?>
                    <input id="cod_plan_academico" name="cod_plan_academico" type="hidden" value="<?php echo $cod_plan_academico ?>" />
                <?php } ?>
                <div class="row" style="clear:both">
                    <div class="from-group col-md-6">
                        <label class="control-label"><?php echo lang("documentacion_presentada_alumno"); ?></label>
                        <div>
                            <select platabindex="3" multiple="" id="documentacion" name="documentacion[]" class="chosen-select" data-placeholder="<?php echo lang("SELECCIONE_UNA_OPCION") ?>">
                            <?php foreach($documentacion as $doc){ ?>
                                <option value = "<?php echo $doc['codigo'] ?>"
                                        <?php if (in_array($doc['codigo'], $documentacion_entregada_anterior)){ ?>selected="true"<?php } ?>>
                                    <?php echo lang($doc['documentacion']);?>
                                </option>
                            <?php }?>
                            </select>
                        </div>
                    </div>
                    <div class="from-group col-md-6">
                        <label class="control-label"><?php echo lang("material_entregado_alumno"); ?></label>
                        <div>
                            <select platabindex="3" multiple="" id="material" name="material[]" class="chosen-select" data-placeholder="<?php echo lang("SELECCIONE_UNA_OPCION") ?>">
                            <?php foreach($material_entregado as $doc){ ?>
                                <option value = "<?php echo $doc['id'] ?>"
                                        <?php if (in_array($doc['id'], $materiales_entregada_anterior)){ ?>selected="true"<?php } ?>>
                                <?php echo lang($doc['material']);?>
                                </option>
                            <?php }?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row" style="clear:both">
                    <div class="from-group col-md-12">
                        <label class="control-label">
                            <?php echo lang("medio_de_pago_de_las_cuotas") ?>
                        </label>
                        <select class="chosen-select" data-placeholder="<?php echo lang("SELECCIONE_UNA_OPCION"); ?>" name="medio_pago_cuotas">
                            <option></option>
                            <?php foreach ($medios_pago as $medio){ ?>
                            <option value="<?php echo $medio['codigo'] ?>"
                                    <?php if ($medio['codigo'] == $medio_actual){ ?>selected="true"<?php } ?>>
                                <?php echo $medio['medio'] ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="row no-padding-top">
                    <div class="col-md-2 form-group no-padding-right">
                        <div class="blue bigger-110 pull-left">
                            <?php echo lang('documentacion') . ' '; ?>
                        </div>
                    </div>
                </div>                
                <div class="row no-padding-top">
                    <div class="col-md-2 form-group no-padding-right">
                        <div class="blue bigger-110 pull-left" id="obs">
                            <?php echo lang('observaciones') . ' '; ?>
                        </div>
                    </div>
                    <div class="col-md-10 form-group no-padding-bottom">
                        <textarea name="observaciones" id="ob" class="form-control pull-left" maxlength="511" style="resize: none;"><?php echo $observaciones_old?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn  btn-success" type="submit">
                <i class="icon-ok bigger-110"></i>
                <?php echo lang('guardar'); ?>
            </button>
        </div>
    </form>
</div>