<link rel="stylesheet" href="<?php echo base_url('assents/theme/assets/css/bootstrap-timepicker.css')?>"/>
<script src="<?php echo base_url('assents/theme/assets/js/date-time/bootstrap-timepicker.min.js')?>"></script>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal.css')?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal-bs3patch.css')?>"/>
<script src="<?php base_url()?>assents/js/librerias/bootstrap-modal/bootstrap-modal.js"></script>
<script src="<?php base_url()?>assents/js/librerias/bootstrap-modal/bootstrap-modalmanager.js"></script>

<script>
    var langFrm = <?php echo $langFrm ?>;
    <?php if (isset($tieneNotasCargadas) && $tieneNotasCargadas){ ?>
        var tieneNotasCargadas = true;
    <?php } else { ?>
        var tieneNotasCargadas = false;
    <?php } ?>
</script>
<script src="<?php echo base_url('assents/js/examenes/frm_examen_parcial.js')?>"></script>

<style>
    .chosen-results{
        max-height: 100px !important;
    }
</style>  
<?php
    $nombre = 'nombre_'.get_idioma();
    $this->load->helper('formatearfecha');

    $hay_inscriptos = (isset($alumnos) && is_array($alumnos) && count($alumnos) > 0) ? true : false;
?>
<div class="modal-content">
    <?php $titulo = '';    
    if(isset($examen)){        
        $titulo =  lang('modificar_examen');
    } else {        
        $titulo =  lang('nuevo-examen-parcial');        
    } ?>
    <div class="modal-header">
        <h4 class="blue bigger"><?php echo $titulo ?></h4>
    </div>

    <div class="modal-body overflow-visible">
        <input type="hidden" name="nombreMateria" disabled value="<?php echo $nombre?>">

        <?php if ($hay_inscriptos) { ?>
            <div class="alert alert-block alert-warning">
                <?php /* Algunos campos <strong class="red">no pueden ser modificados</strong> debido a que el examen ya tiene alumnos inscriptos. */ ?>
                <?php echo lang('warning_examen_modificacion_alumnos_inscriptos'); ?>
            </div>
        <?php } ?>

        <div id="stack1" class="modal  fade" tabindex="-1" data-focus-on="input:first" data-width="50%">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h3><?php echo lang('alumnos_a_inscribir'); ?></h3>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <form id="frmDetallesAlumnos">
                        <table class="table" id="tablaDetallesAlumnos">
                            <thead>
                                <th><?php echo lang('nombre'); ?></th>
                            </thead>
                            <tbody>
                            <?php if(isset($alumnos)){
                                    foreach(json_decode($alumnos,true) as $alumno){ ?>
                                <tr>
                                    <td>
                                        <input type="hidden" name="alumnos[]" value="<?php echo $alumno['codigo'] ?>">
                                        <?php echo $alumno['nombre'] ?>
                                    </td>
                                    <td> 
                                        <?php echo $alumno['apellido'] ?>
                                    </td>
                                </tr>
                                <?php }
                            } ?>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn"><?php echo lang('cerrar'); ?></button>
            </div>
        </div>                
        <form id="examen" role="form">    
            <input name="codigo" type="hidden" value='<?php echo isset($examen) ? $examen->getCodigo() : '-1'?>'>
            <div class="row">
                <div class="form-group col-md-3 col-xs-12">
                    <label for="Tipoexamen"><?php echo lang('tipo_examen'); ?></label>                    
                    <select name="tipoExamen" class="form-control" data-placeholder="<?php echo lang('seleccione_tipo');?>"<?php echo ($hay_inscriptos) ? ' disabled' : '' ?>>
                        <option></option>
                        <?php $tipo = isset($examen->tipoexamen) ? $examen->tipoexamen : '';
                        foreach($examenes as $ex){ ?>
                        <option value="<?php echo $ex['id'] ?>" <?php if ($ex['id'] == $tipo){ ?>selected="true"<?php } ?>>
                            <?php echo $ex['nombre']; ?>
                        </option>
                        <?php } ?>
                    </select>                    
                </div>
                <div class="form-group col-md-3 col-xs-12">
                    <label for="Tipoexamen"><?php echo lang('curso'); ?></label>                    
                    <select name="Curso"  class="form-control"  data-placeholder="<?php echo lang('seleccione_curso');?>"<?php echo ($hay_inscriptos) ? ' disabled' : '' ?>>
                        <option></option>
                        <?php $c = isset($comisionCurso) ? $comisionCurso[0]['cod_curso'] : '';
                        foreach($cursosHabilitados as $curso){                        
                        $ckd = $c == $curso['codigo'] ? 'selected' :'';
                        ?>
                        <option value="<?php echo $curso['cod_plan_academico'] ?>" <?php if ($c == $curso['codigo']){ ?>selected="true"<?php } ?>>
                            <?php echo $curso[$nombre]; ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-md-3 col-xs-12">
                    <label for="Tipoexamen"><?php echo lang('comision'); ?></label>                    
                    <select name="Comision"  class="form-control"  placeholder="Seleccione Comision" data-placeholder="<?php echo lang('seleccione_comision');?>"<?php echo ($hay_inscriptos) ? ' disabled' : '' ?>>
                        <option></option>
                        <?php if(isset($comisiones)){
                        $c = isset($comisionCurso) ? $comisionCurso[0]['cod_comision'] : '';
                        foreach($comisiones as $comision){ ?>                                
                        <option value="<?php echo $comision['codigo'] ?>" <?php if ($c == $comision['codigo']){ ?>selected="true"<?php } ?>>
                            <?php echo $comision['nombre']; ?>
                        </option>
                            <?php }
                        } ?>
                    </select>
                </div>
                <div class="form-group col-md-2 col-xs-12">
                    <label for="Tipoexamen"><?php echo lang('materia'); ?></label>                   
                    <select name="materia"  class="form-control"  data-placeholder="<?php echo lang('seleccione_materia');?>"<?php echo ($hay_inscriptos) ? ' disabled' : '' ?>>                           
                        <?php
                        $nombre_materia_seleccionada = null;
                        if(isset($materias)){
                        $c = isset($examen->materia) ? $examen->materia : '';
                        foreach($materias as $materia){ ?>                                
                        <option value="<?php echo $materia['codigo'] ?>" <?php if ($c == $materia['codigo']){ $nombre_materia_seleccionada = $materia[$nombre]; ?>selected="true"<?php } ?>>
                            <?php
                                echo $materia[$nombre];
                            ?>
                        </option>
                        <?php }
                        } ?>
                    </select>
                </div>
                <?php $dysplay = '';
                $cod_examen = isset($examen) ? $examen->getCodigo() : '';
                if($cod_examen != ''){
                   $dysplay = 'style="display:none"';
                } ?>
                <div class="form-group col-md-1 col-xs-12 " <?php echo $dysplay?>>
                    <label><br></label>
                    <button class="btn btn-warning  btn-xs col-xs-12 detalle">
                        <i class="icon-group"></i>
                    </button>
                </div>            
            </div>
			
			<?php
				$displaySelectExamenPadre = false;
				if ($tipo !== '' && $tipo == 'RECUPERATORIO_PARCIAL') {
					$displaySelectExamenPadre = true;
				}
			?>
			<div id="row_examen_padre" class="row" <?php if (!$displaySelectExamenPadre) { ?>style="display: none;"<?php }; ?>>
				<div class="form-group col-md-6 col-xs-12">
					<label for="examen_padre"><?php echo lang('recuperatorio_de'); ?></label>                   
					<select name="examen_padre" class="form-control" data-placeholder="<?php /* echo lang('examenes'); */ ?>Seleccione un examen" <?php echo ($hay_inscriptos) ? ' disabled' : '' ?>>                           
						<?php if(isset($parciales_pasados)){
						$codigo_examen_seleccionado = isset($examen->codigo_examen_padre) ? $examen->codigo_examen_padre : '';
						foreach($parciales_pasados as $current_parcial_pasado){ ?>                                
						<option value="<?php echo $current_parcial_pasado['codigo'] ?>" <?php if ($codigo_examen_seleccionado == $current_parcial_pasado['codigo']){ ?>selected="true"<?php } ?>>
							<?php
                                echo (!is_null($nombre_materia_seleccionada)) ? $nombre_materia_seleccionada : '';
                                echo " - ".$current_parcial_pasado["fecha"];
                            ?>
						</option>
						<?php }
						} ?>
					</select>
				</div>
			</div>
			
            <div class="row">                 
                <div class="form-group col-md-4 col-xs-12">
                    <label for="Tipoexamen"><?php echo lang('hora_inicio'); ?></label>                        
                    <div class='input-group bootstrap-timepicker' id='horaInicio'>
                        <input name="horaInicio" type='text' class="form-control horaInput" value="<?php echo isset($examen->hora) ? $examen->hora : ''?>">
                        <span class="input-group-addon">
                            <i class="icon-time"></i>
                        </span>
                    </div>                      
                </div>
                <div class="form-group col-md-4 col-xs-12">
                    <label for="Tipoexamen"><?php echo lang('hora_fin'); ?></label>                        
                    <div class='input-group bootstrap-timepicker' id='horaFin' >
                        <input name="horaFin" type='text' class="form-control horaInput" value="<?php echo isset($examen->horafin) ? $examen->horafin : ''?>" >
                        <span class="input-group-addon">
                            <i class="icon-time"></i>
                        </span>
                    </div>                       
                </div>
                <div class="form-group col-md-4 col-xs-12">
                    <label for="Tipoexamen"><?php echo lang('fecha'); ?></label>                    
                    <div class='input-group date' id='datetimepicker1' onclick="abrirCalendario(this);">
                        <input type='text' class="form-control" name="fecha" value="<?php echo isset($examen->fecha) ? formatearFecha_pais($examen->fecha) : ''?>" readonly="true" style='background-color: white !important; cursor: pointer;'>
                        <span class="input-group-addon">
                            <i class="icon-calendar"></i>
                        </span>
                    </div>                    
                </div>
            </div>                
            <div class="row">                
                <div class="form-group col-md-4 col-xs-12">
                    <label for="Tipoexamen"><?php echo lang('salon_final'); ?></label>                    
                    <select  multiple name="salonCocina[]"  class="contacts"  data-placeholder="<?php echo lang('seleccione_salon');?>">
                         <?php foreach($salones as $salon){                                
                            $chk='';
                            $test='';                                
                            $salonARRAY=array(
                                'cod_salon'=>$salon['codigo'],
                                'cod_examen_salon'=>-1
                            );                         
                            if (isset($salonesExamen)){                                    
                                foreach($salonesExamen as $salonExamen){
                                    if($salon['codigo'] == $salonExamen['cod_salon']){
                                        $chk='selected';
                                        $salonARRAY=$salonExamen;
                                    }
                                    $test=$salonExamen['cod_salon'];
                                }                                
                            }                                  
                            echo "<option value='".json_encode($salonARRAY)."'  ".$chk.">".$salon['salon']."</option>";
                        } ?>
                    </select>                  
                </div>                    
                <div class="form-group col-md-4 col-xs-12">
                    <label for="Tipoexamen" ><?php echo lang('profesores'); ?></label>                                    
                    <select  multiple id="select_profesores"  name="profesores[]"  class="form-control"  data-placeholder="<?php echo lang('seleccione_profesor')?>">
                        <?php foreach($profesores as $profesor){
                            $chk='';
                            $profesorARRAY=array(
                                'codprofesor'=>$profesor['codigo'],
                                'cod_examen_profesor'=>-1
                            );
                            $codigoProfesor=$profesor['codigo'];
                            if(isset($profesoresExamen)){
                                foreach($profesoresExamen as $profesorExamen){
                                   if($profesor['codigo']== $profesorExamen['codprofesor']){
                                      $chk='selected'; 
                                      $profesorARRAY=$profesorExamen;
                                   }
                                }
                            }
                            echo "<option value='".json_encode($profesorARRAY)."'  ".$chk.">".$profesor['nombre']."</option>";
                        } ?> 
                    </select>                              
                </div>
                <div class="form-group col-md-4 col-xs-12">
                    <label for="Tipoexamen"><?php echo lang('cupo'); ?></label>                    
                    <input name="cupo"  class="form-control"  placeholder="<?php echo lang('cupo');?>" value="<?php echo isset($examen->cupo) ? $examen->cupo : ''?>">
                </div>
                <div class="form-group col-md-4 col-xs-12">
                    <label for="Tipoexamen"><?php echo lang('observaciones');?></label>                    
                    <textarea name="observaciones"  class="form-control"  placeholder="<?php echo lang('observaciones');?>"><?php echo isset($examen->observaciones) ? $examen->observaciones : ''?></textarea>
                </div>
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-sm btn-primary" name="guardar">
            <i class="icon-ok"></i>
            <?php echo lang('guardar'); ?>
        </button>
    </div>
</div>