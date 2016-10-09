<script src="<?php echo base_url() ?>assents/js/jquery.validate.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url('assents/css/matricula/frm_matricula.css') ?>"/>
<script src="<?php echo base_url('assents/js/librerias/ajaxchosen/lib/ajax-chosen.js') ?>"></script>

<script>
    var frmLang = <?php echo $frmLang ?>;
    var todosPeriodos = <?php echo $todosPeriodos ?>;
    var capacidadComision = <?php echo $capacidadComision ?>;
    var fecha_hoy = '<?php echo date("d/m/Y"); ?>';
    var permite_editar_medios = <?php echo $permite_editar_medios ? "true" : "false"; ?>;
</script>

<script src="<?php echo base_url('assents/js/matriculas/frm_matricula.js') ?>"></script>
<script src="<?php echo base_url('assents/js/impresiones.js') ?>"></script>
<script src="<?php echo base_url('assents/theme/assets/js/jquery.maskedinput.min.js') ?>"></script>
<div id="stack1" class="modal" tabindex="-1" data-focus-on="input:first" data-width="90%">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="blue"><?php echo lang('financiacion'); ?></h4>
    </div>
    <div class="modal-body" id="detalle-plan">
    </div>
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-primary ">Ok</button>
    </div>
</div>
<div class="modal-content">
    <form id="nuevaMatricula" >
        <div class="modal-header">
            <h4 class="blue"><?php echo lang('frm_nuevaMatricula') ?></h4>
        </div>
        <div class="modal-body" >
            <div class="col-md-12 ">
                <?php if ($cod_alumno != -1) { ?>
                    <input name="cod_alumno" type="hidden" value="<? echo $cod_alumno ?>" />
                    <input name="cod_plan_academico"  type="hidden" value="<? echo $cod_plan_academico ?>" />
                <?php } ?>
                <?php $enable = $cod_plan_academico != -1 ? "disabled='disabled'" : "" ?>
                <div class="row">
                    <div class="form-group col-md-6" role="form">
                        <label  for="alumnos"><?= lang('nombre_y_apellido') ?> </label>
                        <div>
                            <select  name="cod_alumno" id="cod_alumno" data-placeholder="<?php echo lang('nombre_y_apellido'); ?>" <?php echo $enable ?>  multiple>
                                <option></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label><?php echo lang('curso') . '/' . lang('plan_academico'); ?></label>
                        <div>
                            <select class="form-control chosen-select" name="cod_plan_academico" id='cod_plan_academico_porfa' data-placeholder="<?php echo lang("SELECCIONE_UNA_OPCION") ?>"  <?php echo $enable ?> >
                                <option></option>
                                <?php if (isset($cursos) && is_array($cursos)){
                                    foreach ($cursos as $rowCurso) { ?>
                                    <option value="<?php echo $rowCurso["cod_plan_academico"]; ?>" <?php echo $cod_plan_academico === $rowCurso["cod_plan_academico"] ? "selected" : "" ?> ><?php echo $rowCurso['nombre']; ?></option>
                                <?php } } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row periodos  hide">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <?php $ocultar = $todosPeriodos == '1'? 'type="hidden"' : ' ';?>
                            <table id="tablaPeriodos" class="table table-striped table-bordered table-condensed ">
                                <thead>
                                    <tr>
                                        <th class="center checksPeriodos">
                                            <label>
                                                <span class="lbl"></span>
                                            </label>
                                        </th>
                                        <th><?php echo lang('cursado'); ?></th>
                                        <th><?php echo lang('comision'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($periodos_curso as $periodo_curso) { ?>
                                        <tr class="">
                                            <td class="center checksPeriodos">
                                                <label>
                                                    <input type="checkbox" periodo="'<?php echo $periodo_curso["cod_tipo_periodo"] ?>'" onclick="checkPeriodoChecked(this)" padre="<?php echo $periodo_curso["padre"] ?>" class="ace checkperiodo" <?php echo $desabledckech; ?>>
                                                    <span class="lbl"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <span><?php echo $periodo_curso["nombre"] ?></span>
                                            </td>
                                            <td>
                                                <?php if (count($periodo_curso["modalidad"]) > 1) { ?>
                                                    <select>
                                                        <?php foreach ($periodo_curso["modalidad"] as $modalidad) { ?>
                                                            <option value="<?php echo $modalidad['modalidad'] ?>"><?php echo lang($modalidad['modalidad']) ?></option>
                                                        <?php } ?>
                                                    </select>
                                                <?php } else { ?>
                                                    <span><?php echo lang($periodo_curso["modalidad"][0]['modalidad']); ?></span>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <select class="chosen-single" name="comisiones" id="comisiones" data-placeholder="<?php echo lang("SELECCIONE_UNA_OPCION") ?>"></select>
                                                <span class="btn btn-warning btn-xs hide" id="pop-horarios" data-rel="popover" title="<?php echo lang('frm_nuevaMatricula_HorariosDeCursado') ?>" data-content=""><i class="icon-bell-alt  bigger-110 icon-only"></i></span>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row" >
                    <div class="form-group col-md-6">
                        <label class=" control-label " for="planes"><?php echo lang('detalleplan_plan') ?></label>
                        <div>
                            <select class="width-100 chosen-select" name="planes" style="width: 810px;" data-placeholder="<?php echo lang("SELECCIONE_UNA_OPCION") ?>"></select>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="control-label" for="numero_cupon">
                            <?php echo lang("codigo_cupon"); ?>
                        </label>
                        <input type="text" class="form-control input-sm" name="numero_cupon" id="numero_cupon">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="control-label"><?php echo lang("medio_pago")." ".lang("matricula"); ?></label>
                        <div>
                            <select class="width-100 chosen-select" name="medio_pago_matricula" style="width: 810px;"
                                    data-placeholder="<?php echo lang("SELECCIONE_UNA_OPCION") ?>" onchange="select_medio_pago_change();">
                                <?php foreach ($medios_pago as $medio){ ?>
                                <option value="<?php echo $medio['codigo'] ?>"><?php echo $medio['medio'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="from-group col-md-6">
                        <label class="control-label"><?php echo lang("caja"); ?></label>
                        <div>
                            <select name="caja_cobro_matricula" class="chosen-select"></select>
                        </div>
                    </div>
                </div>
                <div class="row pago"></div>
                <div class="row no-padding-bottom" style="min-height: 140px;">
                    <div class="col-sm-12" >
                        <div id="cobroMatricula" style="padding-bottom: 20px; float: left; width: 100%;"></div>
                        <div class="table-responsive">
                            <table id="esquema" class="table table-striped table-bordered table-hover"></table>
                        </div>
                    </div>
                </div>

                <div class="row" style="clear: both">
                    <div class="from-group col-md-12">
                        <label class="control-label">
                            <?php echo lang("medio_de_pago_de_las_cuotas"); ?>
                        </label>
                        <select class="chosen-select" name="medio_pago_cuotas" data-placeholder="<?php echo lang("SELECCIONE_UNA_OPCION") ?>">

                            <?php foreach($medios_pago as $medio){ ?>
                            <option value="<?php echo $medio['codigo'] ?>">
                                <?php echo $medio['medio']; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="row" style="clear:both">
                    <div class="from-group col-md-6">
                        <label class="control-label"><?php echo lang("documentacion_presentada_alumno"); ?></label>
                        <div>
                            <select id='documentacion' platabindex="3" multiple="" name="documentacion[]" 
                            class="chosen-select" 
                            data-placeholder="<?php echo lang("SELECCIONE_UNA_OPCION") ?>">
                            </select>
                        </div>
                    </div>
                    <div class="from-group col-md-6">
                        <label class="control-label"><?php echo lang("material_entregado_alumno"); ?></label>
                        <div>
                            <select platabindex="3" multiple="" name="material[]" class="chosen-select" data-placeholder="<?php echo lang("SELECCIONE_UNA_OPCION") ?>">
                            <?php foreach($material_entregado as $doc){ ?>
                                <option value = "<?php echo $doc['id'] ?>">
                                    <?php echo lang($doc['material']);?>
                                </option>
                            <?php } ?>
                            </select>
                        </div>
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
                            <span class="icon-caret-down icon-on-right"></span>
                        </div>
                    </div>
                    <div class="col-md-10 form-group no-padding-bottom">
                        <!-- Ticket 4528 se deja fijo el campo observaciones -->
                        <textarea name="observaciones" id="ob" class="form-control pull-left" maxlength="511" style="resize: none;"></textarea>
                    </div>
                </div>
                <div class="col-md-12 alert alert-danger" id="errores">capa de errores</div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn  btn-success" type="submit">
                <i class="icon-ok bigger-110"></i>
                <?php echo lang('matricular'); ?>
            </button>
        </div>
    </form>
</div>
