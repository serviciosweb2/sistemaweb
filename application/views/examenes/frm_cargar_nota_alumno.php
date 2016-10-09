
<script src="<?php echo base_url('assents/theme/assets/js/jquery.validate.min.js') ?>"></script>
<script src="<?php echo base_url('assents/js/examenes/frm_cargar_nota_alumno.js') ?>"></script>

<div class="modal-content">
    <form id="cargarNotas">
        <div class="modal-header">
            <h4 class="blue bigger"><?php echo lang('cargar-nota'); ?><small><i class="icon-double-angle-right"></i>  <?php echo $nombreAluFormateado ?></small></h4>
        </div>

        <div class="modal-body overflow-visible">
            <div class="row">
                <div class="table-responsive">

                    <table  class="table table-striped table-bordered" id="notas">

                        <thead>
                        <th><?php echo lang('codigo'); ?></th><th><?php echo lang('materia'); ?></th><th><?php echo lang('tipo'); ?></th><th><?php echo lang('escrito'); ?></th><th><?php echo lang('oral'); ?></th><th><?php echo lang('definitivo'); ?></th><th><?php echo lang('fecha_examen'); ?></th><th><?php echo lang('fecha_inscripcion'); ?></th><th><?php echo lang('ausente'); ?></th>

                        </thead>
                        <tbody>

                            <?php
                            
                            foreach ($examenAlumno as $i => $examen) {
                                
                                $PermiteCargarNotas = strtotime($examen['fecha'] . $examen['hora']) > strtotime("now")  ? false : true ;
                          
                               
                                $esc = '';
                                $oral = '';
                                $def = '';
                                $aus = $examen['estado'] == 'ausente' ? 'checked' : '';
                                if (!empty($examen['notas'])) {
                                    $esc = $examen['notas'][2]['nota'];
                                    $oral = $examen['notas'][1]['nota'];
                                    $def = $examen['notas'][0]['nota'];
                                }
                                
                                $input = $PermiteCargarNotas ? "" : "disabled";
                                echo '<tr>';
                                echo '<td>' . $examen['codigo'] . '<input type="hidden" value="' . $examen['codigo'] . '" name="examenes[' . $i . '][codigoExamen]"'  .  $input . '><input name="examenes[' . $i . '][codInscripcion]" type="hidden" value="' . $examen['codInscripcion'] . '" ' . $input. '></td>';
                                echo '<td> ' . $examen['materia'] . '</td>';
                                echo '<td> ' . $examen['tipoExamen'] . '</td>';
                                if($configuracion_notas['formato_nota'] == 'numerico'){
                                    echo '<td> <div class="form-group"><input type="" class="form-control"  name="examenes[' . $i . '][notas][escrito]" value="' . $esc . '"' . $input . '></div></td>';
                                    echo '<td><input    type="" class="form-control"  name="examenes[' . $i . '][notas][oral/teorico]" value="' . $oral . '"' . $input . ' ></td>';
                                    echo '<td><input type="" class="form-control"  name="examenes[' . $i . '][notas][definitivo]" value="' . $def . '"' . $input . ' ></td>';
                                }else{
                                    echo '<td><select name="examenes[' . $i . '][notas][escrito]">';
                                    echo '<option></option>';
                                    foreach($escala_notas as $key=>$nota){
                                        $selected = '';
                                                    if($nota == $esc){
                                                        $selected='selected';
                                                    }
                                        echo '<option value='.$nota.' '.$selected.'>'.$nota.'</option>';
                                    }
                                    echo '</select></td>';
                                    
                                    echo '<td><select name="examenes[' . $i . '][notas][oral/teorico]">';
                                    echo '<option></option>';
                                     foreach($escala_notas as $key=>$nota){
                                         $selec = '';
                                                    if($nota == $oral){
                                                        $selec='selected';
                                                    }
                                        echo '<option value='.$nota.' '.$selec.'>'.$nota.'</option>';
                                    }
                                    echo '</select></td>';
                                    
                                    
                                    echo '<td><select name="examenes[' . $i . '][notas][definitivo]">';
                                    echo '<option></option>';
                                     foreach($escala_notas as $key=>$nota){
                                         $select = '';
                                                    if($nota == $def){
                                                        $select='selected';
                                                    }
                                        echo '<option value='.$nota.' '.$select.'>'.$nota.'</option>';
                                    }
                                    echo '</select></td>';
                                }
                                echo '<td>' . formatearFecha_pais($examen['fecha']) . '</td>';
                                echo '<td>' . formatearFecha_pais($examen['fechadeinscripcion'])     . '</td>';
                                echo '<td><label><input class="ace" type="checkbox" ' . $aus . ' name="examenes[' . $i . '][ausente]" ' . $input . '></input><span class="lbl"></span></label></td>';
                                echo '</tr>';
                            }
                            ?>


                        </tbody>

                    </table>

                </div> 
            </div>
        </div>

        <div class="modal-footer">
            <!--            <button class="btn btn-sm" data-dismiss="modal">
                                <i class="icon-remove"></i>
                                Cancel
                        </button>-->

            <button class="btn btn-sm btn-primary" type="submit">
                <i class="icon-ok"></i>
<?php echo lang('guardar'); ?>
            </button>
        </div>
    </form>
</div>


<!--<div class="page-content">
    <div class="page-header"><?php echo lang('cargar-nota'); ?></div>
    
    
    
        <div class="table-responsive">
            <form id="cargarNotas">
            <table  class="table table-striped table-bordered" id="notas">
            
                <thead>
               <th><?php echo lang('codigo'); ?></th><th><?php echo lang('escrito'); ?></th><th><?php echo lang('oral'); ?></th><th><?php echo lang('definitivo'); ?></th><th><?php echo lang('escrito'); ?></th><th><?php echo lang('fecha_inscripcion'); ?></th><th><?php echo lang('ausente'); ?></th>
                    
                </thead>
                <tbody>
                    
<?php
foreach ($examenAlumno as $i => $examen) {
    $esc = '';
    $oral = '';
    $def = '';
    $aus = $examen['estado'] == 'ausente' ? 'checked' : '';
    if (!empty($examen['notas'])) {
        $esc = $examen['notas'][2]['nota'];
        $oral = $examen['notas'][1]['nota'];
        $def = $examen['notas'][0]['nota'];
    }


    echo '<tr>';
    echo '<td>' . $examen['codigo'] . '<input type="hidden" value="' . $examen['codigo'] . '" name="examenes[' . $i . '][codigoExamen]"><input name="examenes[' . $i . '][codInscripcion]" type="hidden" value="' . $examen['codInscripcion'] . '"></td>';
    echo '<td> <div class="form-group"><input type="" class="form-control"  name="examenes[' . $i . '][notas][escrito]" value="' . $esc . '" readonly></div></td>';
    echo '<td><input type="" class="form-control"  name="examenes[' . $i . '][notas][oral/teorico]" value="' . $oral . '" readonly></td>';
    echo '<td><input type="" class="form-control"  name="examenes[' . $i . '][notas][definitivo]" value="' . $def . '" readonly></td>';
    echo '<td>' .  $examen['fecha'] . '</td>';
    echo '<td>' .    $examen['fechadeinscripcion'] . '</td>';
    echo '<td><label><input class="ace" type="checkbox" ' . $aus . ' name="examenes[' . $i . '][ausente]"></input><span class="lbl"></span></label></td>';
    echo '</tr>';
}
?>
                    
                    
                </tbody>
                
            </table>
            </form>
        </div>
    
<div class="row">
<div class="clearfix form-actions">

    <div class="col-md-offset-3 col-md-9">
        <button class="btn btn-info" type="submit">
            <i class="icon-ok bigger-110"></i>
<?php echo lang('guardar'); ?>
        </button>

    </div>

</div>

</div>
</div>-->

