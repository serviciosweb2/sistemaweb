<style>
    .chosen-container .chosen-results{
      max-height: 70px !important;
  }
</style>
<script>
    var langFrm = <?php echo $langFrm?>;
</script>
<link rel="stylesheet" href="<?php echo base_url('assents/theme/assets/css/jquery.gritter.css')?>"/>
<script src="<?php echo base_url('assents/theme/assets/js/jquery.gritter.min.js')?>"></script>
<script src="<?php echo base_url('assents/js/asistencias/frm_asistencias_web.js')?>"></script>
<div class="col-md-12">
    <div class="modal-content">
        <div class="modal-header no-padding">
            <h4 class="blue bigger">Asistencias Web</h4>
        </div>
        <div class="modal-body overflow-visible">
            <div class="row">
                <div id="areaTablas">
                    <table id="asistencias_web" class="table table-responsive table-striped table-hover dataTable no-footer">
                        <thead>
                            <th>Alumno</th>
                            <?php
                                foreach ($alumnos[0]->unidades as $unidad){
                                    echo '<th>'.$unidad->descripcion.'</th>';
                                }
                            ?>
                        </thead>
                        <tbody>
                             <?php
                                foreach($alumnos as $key => $alumno){
                                    echo '<tr>';
                                    echo '<td><input type="hidden" name="id_usuario" value="'.$alumno->id_usuario.'" />'.$alumno->nombre.'</td>';
                                    foreach ($alumno->unidades as $unidad){
                                        echo '<td>'.$unidad->asistencia.'</td>';
                                    }
                                    echo '</tr>';
                                }
                             ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

