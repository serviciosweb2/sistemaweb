<script src="<?php echo base_url('assents/js/matriculas/cambios_estado_academico.js');?>"></script>
<div class="col-md-12 col-xs-12">
    <div id="areaTablas">
        <table id="estadosAcademicos" width="100%" class="table table-striped table-condensed table-bordered table table-hover" oncontextmenu="return false" onkeydown="return false">  
            <thead>
                <tr>
                    <th><?php echo lang("ALUMNO"); ?></th>
                    <th><?php echo lang("comision"); ?></th>
                    <th><?php echo lang("materia"); ?></th>
                    <th><?php echo lang("porc_asistencia"); ?></th>
                    <th><?php echo lang("cambiar_a"); ?></th>
                </tr>
            </thead>
            <?php foreach ($arrEstadosAcademicos as $estadoAcademico){ ?> 
            <tr>
                <td><?php echo ucwords(strtolower($estadoAcademico['alumno_nombre'])); ?></td>
                <td><?php echo $estadoAcademico['comision_nombre'] ?></td>
                <td><?php echo $estadoAcademico['materia_nombre'] ?></td>
                <td><?php echo $estadoAcademico['porcasistencia'] ?></td>
                <td>
                    <button class="btn btn-xs btn-success" onclick="cambiar_estado(<?php echo $estadoAcademico['codigo'] ?>, 'regular', this);"
                            <?php if ($estadoAcademico['porcasistencia'] < $porcasistencia){ ?>disabled="true"<?php } ?>>
                        <?php echo lang("regular") ?>
                    </button>
                    &nbsp;&nbsp;
                    <button class="btn btn-xs btn-success" onclick="cambiar_estado(<?php echo $estadoAcademico['codigo'] ?>, 'recursa', this);">
                        <?php echo lang("recursa"); ?>
                    </button>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
</div>

