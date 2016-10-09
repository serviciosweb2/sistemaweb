<script src="<?php echo base_url('assents/js/matriculas/frm_baja.js') ?>"></script>
<script src="<?php echo base_url('assents/js/impresiones.js') ?>"></script>
<div class="modal-content">
    <div class="modal-header">
        <h3 class="blue bigger"><?php echo lang('INHABILITAR');?>
            <small>
                <i class="icon-double-angle-right"></i>
                <?php  echo $nombreAlumno ?>
            </small>
        </h3>
    </div>
    <div class="modal-body overflow-visible">
        <form class="form-line" id="frm-baja" role="form">
            <div class="row">
                <div class="col-md-12">
                    <input name="codigo_alumno"  type="hidden" value="<?php echo $cod_alumno ?>"/>
                    <input name="cod_plan_academico"  type="hidden" value="<?php echo $cod_plan_academico ?>"/>
                    <div class="form-group <?php echo count($periodos) === 1 ? "hide" : ""  ?>" >
                        <label class=" control-label" for="motivo"><?php echo lang('periodos');?></label>
                        <div>
                            <select class="width-80 chosen-select" id="periodo" name="periodo" data-placeholder="Seleccione periodo">
                                <?php
                                foreach ($periodos as $periodo) {
                                    $select =     $periodo[0] == $tipoPeriodo ? "selected" : "";
                                    echo " <option value='" . $periodo[0] . "'"  . $select . ">" . $periodo[1] . "</option>";
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class=" control-label" for="motivo"><?php echo lang('motivo');?></label>
                        <div>
                            <select class="width-80 chosen-select" id="motivo" name="motivo" data-placeholder="Seleccione Motivo">
                                <?php foreach ($motivos as $rowmotivo) {
                                echo " <option value='" . $rowmotivo["id"] . "'>" . $rowmotivo["motivo"] . "</option>";
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" >
                        <label class=" control-label" for="motivo"><?php echo lang('matricula_baja');?></label>
                        <div>
                            <textarea class="form-control limited" id="form-field-9" maxlength="50" name="comentario" ></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-danger"  id="btn-baja" type="submit">
            <i class="fa fa-arrow-downbigger-110"></i>
            <?php echo lang('INHABILITAR');?>
        </button>
    </div>
</div>