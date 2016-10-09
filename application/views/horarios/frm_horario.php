<link rel="stylesheet" href="<?php echo base_url('assents/css/horarios/frm_horario.css'); ?>"/>
<?php
$this->load->helper('datepicker');
?>
<link rel="stylesheet" href="<?php echo base_url('assents/css/jquery.timepicker.css'); ?>"/>
<script src="<?php echo base_url('assents/js/jquery.timepicker.js'); ?>"></script>
<script src="<?php echo base_url('assents/js/horarios/frm_horario.js'); ?>"></script>
<div class="modal-content">
    <form id="nuevoEvento">
        <input type="hidden" value="<?php echo $horario->getCodigo(); ?>" name="codigo_horario" />
        <div class="modal-header">
            <h3 class="blue bigger"><?php echo $horario->getCodigo() === -1 ?  lang('nuevo_evento') :  lang('modificar_evento'); ?><small>
                </small></h3> 
        </div>
        <div class="modal-body overflow-visible">
            <div class="row">
                <div class="form-group col-md-6">
                    <label ><?php echo lang('fechaDesde_horario'); ?></label>
                    <input type="text" id="fechaDesde" name="fechaDesde"
                           class="form-control " value="<?php echo $horario->dia == '' ? formatearFecha_pais($fechaNuevoEvent) : formatearFecha_pais($horario->dia); ?>">
                </div>
                <div class=" form-group col-md-3">
                    <label ><?php echo lang('horadesde_horario'); ?></label>
                    <input type="text"  name="horaDesde" class="form-control"
                           id="horaDesde" value="<?php echo $horario->horadesde == '' ? $horaComienzo : $horario->horadesde ?>">
                </div>
                <div class="form-group col-md-3">
                    <label><?php echo lang('horaHasta_horario'); ?></label>
                    <input type="text" class="col-md-12" name="horaHasta"
                           id="horaHasta" value="<?php echo $horario->horahasta == '' ? $horaFinal : $horario->horahasta ?>">
                </div>
            </div>
            <div class="row">
                <div class=" form-group col-md-3"> 
                    <label><?php echo lang('salon_final'); ?></label>
                    <select class="form-control" name="cod_salon" name="cod_salon"
                            data-placeholder="<?php echo lang('seleccione_salon')?>">
                        <option></option>
                        <?php foreach ($salones as $salon) {
                            $selected = $salon['codigo'] == $horario->cod_salon ? 'selected' : '';
                            echo "<option value='" . $salon['codigo'] . "' " . $selected . ">" . $salon['salon'] . "</option>";
                        } ?>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label><?php echo lang('comision'); ?></label>
                    <select class="form-control" name="cod_comision"
                            data-placeholder="<?php echo lang('seleccione_comision');?>">
                        <option></option>
                        <?php foreach ($comisiones as $comision) {
                            $selected = $comision['codigo'] == $horario->cod_comision ? 'selected' : '';
                            echo ' <option value="' . $comision['codigo'] . '"' . $selected . '>' . $comision['nombre'] . '</option>';
                        } ?>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label><?php echo lang('materia'); ?></label>
                    <select class="col-md-12" name="cod_materia" data-placeholder="<?php echo lang('seleccione_materia');?>">
                        <option></option>
                        <?php $nombreMateria = 'nombre_' . get_Idioma();                       
                        foreach ($materias as $materia) {
                            $selected = $materia['codigo'] == $horario->cod_materia ? 'selected' : '';
                            echo ' <option value="' . $materia['codigo'] . '"' . $selected . '>' . $materia[$nombreMateria] . '</option>';
                        } ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">        
                    <label><?php echo lang('profesores'); ?></label>
                    <select class="col-md-12 tag-input-style" name="profesores[]"
                            data-placeholder="<?php echo lang('seleccione_profesor');?>" multiple >
                        <option></option>
                        <?php
                        $nombreMateria = 'nombre_' . get_Idioma();
                        foreach ($profesores as $profesor) {
                            $selected = $profesor['selec'] == true ? 'selected' : '';
                            echo '<option value="' . $profesor['codigo'] . '"' . $selected . '>' . $profesor['nombre'] . '</option>';
                        } ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">       
                    <label><?php echo lang('repetir'); ?></label>
                    <input name="vista_botones" value="<?php echo $dias == false ? '' : 1 ?>" type="hidden">
                    <select class="col-md-12" name="tipoRepeticion" data-placeholder="<?php echo lang('seleccione_repeticion');?>">
                        <?php $bandera = $dias == false ? '' : 1;
                        foreach ($repeticion as $key => $tipoRepeticion) {
                            $selected = $bandera == $key ? 'selected' : '';
                            echo '<option value="' . $key . '" ' . $selected . '>' . lang($tipoRepeticion) . '</option>';
                        } ?>
                    </select>
                </div>
            </div>
            <div class="row repetir" style="display:<?php echo $bandera == 1 ? 'inline' : 'none' ?>;">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12  form-group" >       <label><?php echo lang('repetir_cada'); ?></label>
                            <select class="col-md-12 estado" name="frecuenciaRepeticion">
                                <?php
                                for ($i = 1; $i <= 5; $i++) {
                                    echo "<option value='" . $i . "'>" . $i . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 repetir form-group" >        <label><?php echo lang('repetir_el');?></label>
                            <div class='row'>
<?php
for ($i = 1; $i <= 7; $i++) {
    $checked = ( isset($dias[$i])) ? 'checked' : '';
    ?>
                                    <div class='col-md-3'>

                                        <input   type='checkbox' name='idDia[]' value='<?php echo $i ?>' class='ace ace-checkbox-2 estado' <?php echo $checked ?>>
                                        <span class="lbl"> <?php echo lang("dia_" . $i) ?></span>
                                    </div> 



<?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">

                        <div class=' form-group col-md-12'>

                            <?php
                            $titulo = '';
                            $value = '';
                            $id = '';
                            $data = '';
                            $checked = '';
                            $valor = '';
                            foreach ($tipofinalizacion as $key => $tipo) {
                                switch ($key) {
                                    case 0:
                                        $titulo = 'Desp.de';
                                        $value = $key;
                                        $data = 'input1';
                                        $valor = '';
                                        breask;
                                    case 1:
                                        $titulo = 'el';
                                        $value = $key;
                                        $id = 'datepicker';
                                        $data = 'input2';
                                        $checked = 'checked';
                                        $valor = (isset($fin_repeticion['fin'])) ? formatearFecha_pais($fin_repeticion['fin']) : '';
                                        break;
                                }
                                ?>




                            <div
                                class="col-md-4">
                                <div class="row"> <label><?php echo lang('fecha_finaliza_repeticion'); ?></label>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <input class="form-control date-picker input-mask-date" type="text" id="<?php echo $id ?> form-field-mask-1" name="finalizacion" value="<?php echo $valor ?>">
                                            <span class="input-group-btn">
                                                <button class="btn btn-sm btn-default" type="button">
                                                    <i class="icon-calendar bigger-110"></i>
                                        
                                                </button>
                                            </span>
                                        </div>




                             
                                    </div>
                                </div>

                            </div><?php } ?></div>
                    </div> 



                </div>
            </div>




            <div class="row">
                <div class="col-md-12 alert alert-error" id="errores"></div>
            </div>









        </div>

        <div class="modal-footer">
            <button class="btn  btn-success" type="button" onclick="enviar_horarios();"  value="enviar">
                <i class="icon-ok bigger-110"></i>
                <?php echo lang('guardar'); ?>
            </button>


        </div>    


    </form>

</div>

