<link rel="stylesheet" href="<?php echo base_url('assents/css/datepicker3.css') ?>"/>
<script src="<?php echo base_url('assents/js/aspirantes/frm_presupuestar.js') ?>"></script>
<script src="<?php echo base_url('assents/js/impresiones.js') ?>"></script>

<?php $idioma = 'nombre_' . get_idioma();
$diasAgregados = time() + ( $diasVigenciaPresupuesto * 24 * 60 * 60);
$date = date('Y-m-j', $diasAgregados);
$this->load->helper('formatearfecha'); ?>

<input name="idioma" type="hidden" value="<?php echo get_Idioma(); ?>">
<div id="stack1" class="modal" tabindex="-1" data-focus-on="input:first" data-width="80%">
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
    <div class="modal-header">
        <h4 class="blue bigger"><?php echo lang('presupuesto') ?>
            <small>
                <i class="icon-double-angle-right"></i>
                <?php echo ' ' . $aspirante->nombre . ' ' . $aspirante->apellido ?>
            </small>
        </h4>
    </div>

    <div class="modal-body overflow-visible">
        <form id="presupuesto">
            <input name="cod_aspirante" type="hidden" value="<?php echo $aspirante->getCodigo() ?>">
            <div class="row">
                <div class="form-group col-md-6 ">
                    <label><?php echo lang('curso'); ?></label>
                    <select class="form-control" name="cursos" id="cod_plan_academico" data-placeholder="<?php echo lang('seleccione_opcion') ?>">
                        <option></option>
                        <?php
                        foreach ($cursos as $curso) {
                            echo '<option value="' . $curso['cod_plan_academico'] . '">' . $curso['nombre'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group col-md-5">
                    <label ><?php echo lang('fecha_vigencia'); ?></label>
                    <input  type="text" class="form-control input-sm" name="fechaVigencia" value="<?php echo formatearFecha_pais($date) ?>">
                </div>
            </div>
            <div class="row periodos  hide">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table id="tablaPeriodos" class="table table-striped table-bordered table-condensed ">
                            <thead>
                                <tr>
                                    <th class="center">
                                        <label>
                                            <span class="lbl"></span>
                                        </label>
                                    </th>
                                    <th><?php echo lang('cursado'); ?></th>
                                    <th><?php echo lang('comision'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6 col-xs-12">
                    <label ><?php echo lang('planes') ?></label>
                    <select class="form-control" name="plan" data-placeholder="<?php echo lang('seleccione_opcion') ?>"></select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive" id='div_detalle_financiacion'>
                        <center>
                            <table class="table table-striped table-bordered" id="financiacion">
                                <tbody>
                                </tbody>
                            </table>
                        </center>
                    </div>
                </div>
            </div>
            <div class="row no-padding-top">
                <div class="col-md-2 form-group no-padding-right"> 
                    <div class="blue bigger-110 pull-left" id="obs"><?php echo lang('observaciones') . ' '; ?><span class="icon-caret-down icon-on-right"></span></div>
                </div>
                <div class="col-md-6 form-group no-padding-bottom"> 
                    <textarea name="observaciones" class="form-control hide pull-left"></textarea>
                </div>
            </div>
            <div class="col-md-12 alert alert-danger" id="errores">capa de errores</div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-sm btn-success">
            <i class="icon-ok"></i>
            <?php echo lang('presupuestar-aspirante'); ?>
        </button>
    </div>        
</div>