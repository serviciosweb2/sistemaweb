<link rel="stylesheet" href="<?php echo base_url('assents/theme/assets/css/bootstrap-timepicker.css')?>"/>
<script src="<?php echo base_url('assents/theme/assets/js/date-time/bootstrap-timepicker.min.js')?>"></script>
<script>
    var langFrm = <?php echo $langFrm ?>;
    <?php if (isset($tieneNotasCargadas) && $tieneNotasCargadas){ ?>
        var tieneNotasCargadas = true;
    <?php } else { ?>
        var tieneNotasCargadas = false;
    <?php } ?>
</script>
<script src="<?php echo base_url('assents/js/examenes/frm_examen_final.js')?>"></script>
<style>

    .chosen-results{
        max-height: 100px !important;
    }
</style>
<?php $nombre='nombre_'.get_idioma();
 $this->load->helper('formatearfecha'); ?>
<div class="modal-content">
    <?php $titulo = '';
    if(isset($examen)){
        $titulo =  lang('modificar_examen');
    } else {
        $titulo =  lang('nuevo-examen-final');
    } ?>
    <div class="modal-header">
        <h4 class="blue bigger"><?php echo $titulo ?></h4>
    </div>
    <div class="modal-body overflow-visible">
        <form id="examen"  role="form">
            <input type="hidden" name="codigo" value="<?php echo isset ($examen)  ? $examen->getCodigo() : '-1' ?>">
            <div class="row">
                <div class="col-md-10">
                    <div class="row">
                        <div class="form-group col-md-6 col-xs-12">
                            <label for="Tipoexamen"><?php echo lang('tipo_examen'); ?></label>
                            <select name="tipoExamen" class="form-control" data-placeholder=<?php echo lang('seleccione_tipo');?>>
                                <option></option>
                                <?php $tipoExamen= isset($examen->tipoexamen) ? $examen->tipoexamen : '';
                                    foreach($examenes as $ex){
                                       $ckd= $tipoExamen==$ex['id'] ? 'selected' : '';
                                        echo '<option value="'.$ex['id'].'" '.$ckd.'>'.$ex['nombre'].'</option>';
                                    } ?>
                            </select>
                        </div>
                        <div class="form-group col-md-3 col-xs-12" style="display: none">
                            <label for="Tipoexamen"><?php echo lang('curso'); ?></label>
                            <select name="Curso"  class="form-control"  placeholder=<?php echo lang('seleccione_curso');?>></select>
                        </div>
                        <div class="col-md-3 col-xs-12" style="display: none">
                            <div class="form-group">
                                <label for="Tipoexamen"><?php echo lang('comision');?></label>
                                <select name="Comision"  class="form-control"  data-placeholder=<?php echo lang('seleccione_comision');?>></select>
                            </div>
                        </div>
                        <div class="col-md-6 col-xs-12">

                            <div class="form-group">
                                <label for="Tipoexamen" ><?php echo lang('materia');?></label>
                                <select name="materia"  class="form-control"  data-placeholder="<?php echo lang('seleccione_materia');?>">
                                    <?php $examenMateria= isset($examen->materia) ? $examen->materia  : '';
                                    foreach($materias as $materia ){ ?>
                                    <option value="<?php echo $materia['codigo'] ?>" <?php if ($examenMateria == $materia['codigo']){ ?>selected="true"<?php } ?>>
                                        <?php echo $materia[$nombre] ?>
                                    </option>
                                        <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4 col-xs-12">
                            <label for="Tipoexamen"><?php echo lang('hora_inicio'); ?></label>
                            <div class='input-group bootstrap-timepicker'>
                                <input name="horaInicio" type='text' class="form-control inputHora" value="<?php echo isset($examen->hora) ? $examen->hora : ''?>" >
                                <span class="input-group-addon">
                                    <i class="icon-time"></i>
                                </span>
                            </div>
                        </div>
                        <div class="form-group col-md-4 col-xs-12">
                            <label for="Tipoexamen"><?php echo lang('hora_fin'); ?></label>
                            <div class='input-group bootstrap-timepicker'  >
                                <input name="horaFin" type='text' class="form-control inputHora" value="<?php echo isset($examen->horafin) ? $examen->horafin : '' ?>" >
                                <span class="input-group-addon">
                                    <i class="icon-time"></i>
                                </span>
                            </div>
                        </div>
                        <div class="form-group col-md-4 col-xs-12">
                            <label for="Tipoexamen"><?php echo lang('fecha'); ?></label>
                            <div class='input-group date' onclick="abrirCalendario(this)">
                                <input type='text' class="form-control" name="fecha" value="<?php echo isset($examen->fecha) ? formatearFecha_pais($examen->fecha)  : '' ?>" readonly="true" style='background-color: white !important; cursor: pointer;'>
                                <span class="input-group-addon">
                                    <i class="icon-calendar"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4 col-xs-12">
                            <label for="Tipoexamen" ><?php echo lang('salon_final'); ?></label>
                            <select  multiple name="salonCocina[]"  class="contacts"  data-placeholder="<?php echo lang('seleccione_salon');?>">
                                <?php foreach($salones as $salon){
                                    $chkSalon='';
                                    $valoARRAY=array(
                                        'cod_salon'=>$salon['codigo'],
                                        'cod_examen_salon'=>-1,
                                    );
                                    if(isset($salonesExamen)){
                                        foreach($salonesExamen as $sln){
                                            if($salon['codigo'] == $sln['cod_salon']){
                                               $chkSalon='selected';
                                                $valoARRAY=$sln;
                                            }
                                        }
                                    }
                                    echo "<option value='".json_encode($valoARRAY)."'  ".$chkSalon.">".$salon['salon']."</option>";
                                } ?>
                            </select>
                        </div>
                        <div class="form-group col-md-4 col-xs-12">
                            <label for="Tipoexamen"><?php echo lang('cupo'); ?></label>
                            <input name="cupo"  class="form-control"  placeholder="<?php echo lang('cupo');?>" value="<?php echo isset($examen->cupo) ? $examen->cupo : '' ?>">
                        </div>
                        <div class="form-group col-md-4 col-xs-12">
                            <label for="Tipoexamen" ><?php echo lang('profesores'); ?></label>
                            <select  multiple  name="profesores[]"  class="form-control"  data-placeholder="<?php echo lang('seleccione_profesor')?>">
                               <?php foreach($profesores as $profesor){
                                    $chk='';
                                    $profesorARRAY=array(
                                        'codprofesor'=>$profesor['codigo'],
                                        'cod_examen_profesor'=>-1
                                    );
                                    $nombre=$profesor['nombre'].' '.$profesor['apellido'];
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
                    </div>
                </div>


                        <div class="form-group col-md-12 col-xs-12">
                            <label for="Tipoexamen"><?php echo lang('observaciones');?></label>
                                <textarea name="observaciones"  class="form-control"  placeholder="<?php echo lang('observaciones');?>"><?php echo isset ($examen->observaciones) ? $examen->observaciones : '' ?></textarea>
                        </div>

                        <div class="form-group col-md-12 col-xs-12">
                            <label>
                              <?php
                                  $checked = $examen->ver_campus == 1 ? 'checked' : '';
                               ?>
                                <input class="ace ace-switch ace-switch-6" type="checkbox" name="ver_campus" id="ver_campus" onclick=""  <?php echo $checked?>>
                                <span class="lbl" style="font-size: 18px;"><?php echo lang('ver_en_campus'); ?>

                                </span>
                            </label>
                        </div>

                        <div class="form-group col-md-12 col-xs-12">
                            <div class=" col-md-12">
                                <div class="checkbox" >
                                    <label class="hide">
                                        <?php $checked='';
                                        if(isset($examen->inscripcionweb)){
                                            $checked= $examen->inscripcionweb==1 ? 'checked' : '';
                                        } ?>
                                        <input type="checkbox" name="preinscripcionWeb"  <?php echo $checked?> ><?php echo lang('preinscripcion_web');?>
                                    </label>
                                </div>
                            </div>
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
