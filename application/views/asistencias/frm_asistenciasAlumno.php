<script>var lenguaje = <?php echo $lang ?>;</script>
<script src="<?php echo base_url('assents/theme/assets/js/jquery.validate.min.js') ?>"></script>
<script src="<?php echo base_url('assents/js/asistencias/frm_asistenciasAlumno.js') ?>"></script>


<div class="modal-content">
    <form id="asistencias">
        <input type="hidden" name="cod_estado_academico" value="<?php echo $cod_estado_academico ?>">
        <div class="modal-header">
            <h4 class="blue bigger"><?php echo lang('asistencia-alumno'); ?><small><i class="icon-double-angle-right"></i>  <?php echo $nombre_alumno ?></small></h4>
        </div>
        <div class="modal-body overflow-visible">
            <div class="row">
                <div class="form-group col-md-5" >
                    <label><?php echo lang('curso') . '/' . lang('plan_academico'); ?></label>
                    <div>
                        <select style="width: 300px;" name="matriculas" <?php if ($matricula_periodo_seleccionar <> -1){ ?>disabled="true"<?php } ?>>
                            <option></option>
                            <?php foreach ($matriculas as $row) { ?>
                                <option value="<?php echo $row['codigo']; ?>"
                                        <?php if ($matricula_periodo_seleccionar == $row['codigo']){ ?>selected="true"<?php } ?>>
                                    <?php echo $row['nombre']; ?>
                                </option>                        
                            <?php } ?> 

                        </select>
                    </div>
                </div>
                <div class="form-group col-md-5">
                    <label><?php echo lang('materia'); ?></label>
                    <div>
                        <select style="width: 300px;" name="materias" <?php if ($materia_seleccionar <> -1){ ?>disabled="true"<?php } ?>>
                            <?php if ($materia_seleccionar <> -1){ ?>
                            <option value="<?php echo $materia_seleccionar ?>">
                            <?php echo $materia; ?>
                            </option>
                            <?php } else { ?>
                            <option></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <table id="ver_horarios" class ="table table-bordered table-condensed" style="width: 98%" align="center">
                    <thead>
                    <th><?php echo lang('fecha'); ?></th>
                    <th><?php echo lang('hora_desde'); ?></th>
                    <th><?php echo lang('hora_hasta'); ?></th>
                    <th><?php echo lang('comision'); ?></th>
                    <th><?php echo lang('asistencias'); ?></th>
                    </thead>
                    <tbody>
                    </tbody>  
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-sm btn-primary" name="btn_guardar_asistencias" type="button">
                <i class="icon-ok"></i>
                <?php echo lang('guardar'); ?>
            </button>
        </div>
    </form>
</div>
<?php 
if ($materia_seleccionar <> -1){ ?>
    <script>
        var codigo = <?php echo $cod_alumno; ?>;
        $('select[name="materias"]').change();
    </script>
<?php } ?>