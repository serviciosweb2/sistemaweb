
<script>
    var langFrm = <?php echo $langFrm?>;
</script>
<script src="<?php echo base_url('assents/js/examenes/frm_cargarnotaexamen.js')?>"></script>
<!--<script src="<?php echo base_url('assents/js/librerias/jquery-serialize/jquery.serializeJSON.min.js')?>"></script>-->
<div class="modal-content">
    <form id="cargarNotas">
        <div class="modal-header">
            <?php
                $nombreMateria = "nombre_" . get_idioma();
 
            ?>
            <h4 class="blue bigger">  <?php echo lang('cargar-nota'); ?><small><i class="icon-double-angle-right"></i><?php echo ' '.$cod_examen.',  '.$materia->$nombreMateria;?></small></h4>
        </div>
        <div class="modal-body overflow-visible">
            <div class="row">                
                <div class="table-responsive">        
                    <table id="notas" class="table table-striped table-bordered table-hover">
                        <thead>                
                            <th><?php echo lang('Alumno'); ?></th>
                            <th><?php echo lang('escrito');?></th>
                            <th><?php echo lang('oral');?></th>
                            <th><?php echo lang('definitivo');?></th>
                            <th><?php echo lang('fecha_inscripcion');?></th>
                            <th><?php echo lang('fecha');?></th>
                            <th><?php echo lang('ausente');?></th>
                        </thead>
                        <tbody>                
                        <?php 
                        foreach($inscriptosExamen as $i=>$inscriptos){
                            $esc='';
                            $oral='';
                            $def='';
                            $aus = $inscriptos['ausente'] == 'ausente';
                            
                            if(!empty($inscriptos['notas'])){
                                
                                $esc= $inscriptos['notas'][2]['nota'];
                                $oral=$inscriptos['notas'][1]['nota'];
                                $def=$inscriptos['notas'][0]['nota'];
                            } ?>                
                            <tr>
                                <td>
                                    <?php echo $inscriptos['nombre_apellido'] ?>
                                    <input type="hidden" value="<?php echo $inscriptos['codigo'] ?>" name="alumnos[<?php echo $i ?>][cod_inscripto]">
                                    <input type="hidden" value="<?php echo $inscriptos['cod_estado_academico'] ?>" name="alumnos[<?php echo $i ?>][cod_estado_academico]">
                                </td>
                                <?php if($configuracion_notas['formato_nota'] == 'numerico'){?>
                                <td>
                                    <input type="" class="form-control" id="input_1_<?php echo $inscriptos['codigo'] ?>" name="alumnos[<?php echo $i ?>][notas][escrito]" 
                                           value="<?php echo $esc ?>" <?php if ($aus) { ?> readonly="true" <?php } ?>>
                                </td>
                                <td>
                                    <input type="" class="form-control" id="input_2_<?php echo $inscriptos['codigo'] ?>" name="alumnos[<?php echo $i ?>][notas][oral/teorico]" 
                                           value="<?php echo $oral ?>" <?php if ($aus) { ?> readonly="true" <?php } ?>>
                                </td>
                                <td>
                                    <input type="" class="form-control" id="input_3_<?php echo $inscriptos['codigo'] ?>" name="alumnos[<?php echo $i ?>][notas][definitivo]" 
                                           value="<?php echo $def ?>" <?php if ($aus) { ?> readonly="true" <?php } ?>>
                                </td>
                                <?php }else{?>
                                     
                                        <td>
                                            <select class="form-control" name="alumnos[<?php echo $i ?>][notas][escrito]">
                                                <option></option>
                                                <?php 
                                                
                                                foreach($escala_notas as $key=>$nota){
                                                    $selected = '';
                                                    if($nota == $esc){
                                                        $selected='selected';
                                                    }
                                                    echo '<option value='.$nota.' '.$selected.'>'.$nota.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control" name="alumnos[<?php echo $i ?>][notas][oral/teorico]">
                                                <option></option>
                                                <?php 
                                                foreach($escala_notas as $key=>$nota){
                                                    $selec = '';
                                                    if($nota == $oral){
                                                        $selec='selected';
                                                    }
                                                    echo '<option value='.$nota.' '.$selec.'>'.$nota.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control" name="alumnos[<?php echo $i ?>][notas][definitivo]">
                                                <option></option>
                                                <?php 
                                                foreach($escala_notas as $key=>$nota){
                                                    $select = '';
                                                    if($nota == $def){
                                                        $select='selected';
                                                    }
                                                    echo '<option value='.$nota.' '.$select.'>'.$nota.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </td>
                                     
                                <?php }?>
                                <td>
                                    <?php echo $inscriptos['fechadeinscripcion'] ?>
                                </td>
                                <td>
                                    <?php echo $inscriptos['fecha'] ?>
                                </td>
                                <td class="text-center">
                                    <label>
                                        <input id="<?php echo $inscriptos['codigo'] ?>" name="alumnos[<?php echo $i ?>][ausente]" <?php if ($aus) { ?> checked="true" <?php } ?> type="checkbox" class="ace check_ausente">
                                        <span class="lbl"></span>
                                    </label>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>            
                    </table>            
                </div>
            </div>
        </div>
        <div class="modal-footer">            
            <button class="btn btn-sm btn-primary" type="submit">
                <i class="icon-ok"></i>
                <?php echo lang('guardar')?>
            </button>        
        </div>
    </form>
</div>