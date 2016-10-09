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
<script src="<?php echo base_url('assents/js/asistencias/frm_profesor_horario.js')?>"></script>
<div class="col-md-12">
    <div class="modal-content">
    <div class="modal-header">
            <h4 class="blue bigger">Cargar Profesores</h4>
    </div>

    <div class="modal-body overflow-visible">
            <div class="row">
                <div id="areaTablas">
        <table id="horario_profesores" class="table table-responsive">
            <thead>
                                <th>Horarios</th>
                                <th>Dictado por:</th>
                                
                        </thead>
                        <tbody>
                                 <?php
                                
                                    foreach($horarios as $key=>$horario){
                                        $action = $horario['cod_profesor'] != '' ? 'update' : 'insert';
                                        echo '<tr>';
                                        echo '<td>';
                                        echo'<label class="horario" data-codigo='.$horario['codigo'].'>'.$horario['horario'].'</label>';
                                        echo '</td>';
                                        echo '<td>';
                                        echo '<select class="profesores" data-placeholder="Seleccione Profesor" data-action='.$action.'>';
                                        echo '<option></option>';
                                       
                                        foreach($profesores as $profesor){
                                            $check = '';
                                            if($profesor['codigo'] == $horario['cod_profesor']){
                                                $check = 'selected';
                                            }
                                            echo '<option  value='.$profesor['codigo'].' '.$check.'>'.$profesor['nombre'].'</option>';
                                        }
                                        echo '</select>';
                                        echo '</td>';
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

