<link rel="stylesheet" href="<?php echo base_url('assents/css/comisiones/frm_comisiones.css')?>"/>
<script src="<?php echo base_url('assents/js/chosen.jquery.js')?>"></script>
<script src="<?php echo base_url('assents/js/jquery.validate.min.js')?>"></script>
<script src="<?php echo base_url("assents/js/comisiones/frm_comisiones.js")?>"></script>
<style>
    
   span .btn-default {
        color: #333 !important;
        background-color: #fff  !important;
        border-color: #ccc  !important;
    }
    
    span .btn {
        color: #858585 !important;
        margin-bottom: 0;
        font-weight: 400;
        text-align: center;
        vertical-align: middle;
        cursor: pointer;
        background-image: none;
        border: 1px solid #d5d5d5 !important;
        white-space: nowrap;
        padding: 6px 12px;
        font-size: 14px;
        line-height: 1.42857143;
        border-radius: 0px !important;
        -webkit-user-select: none; 
    }
  
  
  
    span .btn:hover {
        background: none !important;
        cursor:text;
    }
  
  .chosen-container .chosen-results{
      max-height: 70px !important;
  }
    
</style>
<!-- // modificacion franco ticket 5149-> se agrego a los select la clase chosen-control para corregir bug que modificaba otros select fuera del fancy-box -->
<div class="modal-content" >
    <form id='nuevaComision'>
        <div class="modal-header">
            <h3 class="blue bigger"><?php echo $comision->getCodigo() !== -1 ? lang('modificar-comision') : lang('nuevaComision')  ?></h3>
        </div>
        <div class="modal-body overflow-visible">
            <input name="modalidad" type="hidden" value="<?php echo $comision->modalidad?>">
            <input name="cod_comision" value="<?php echo $comision->getCodigo() ?>" type="hidden"/>
            <div class="row">
                <?php $readonly = $comision->getCodigo() !== -1 ? "disabled='disabled'" : "" ?>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputEmail1"><?php echo lang('nombre_del_curso');?></label>
                        <?php if(isset($tiene_inscriptos) && $tiene_inscriptos  === 1){
                              $dejarModificar = "disabled='disabled'";
                          } else {
                             $dejarModificar = '';
                          } ?>
                        <select name="cod_plan_academico" id="cod_plan_academico" class="form-control chosen-control" data-placeholder="seleccione curso" <?php echo $dejarModificar ?>>
                            <option></option>
                        <?php foreach($cursos as $curso){
                                $disabled = '';
                                $nombreIdioma='nombre_' .get_Idioma();
                                if(isset($tiene_inscriptos) && $tiene_inscriptos  === 0){
                                    if($curso['cod_plan_academico'] != $comision->cod_plan_academico){
                                        $disabled = 'disabled';
                                    }
                                }
                                $selected =  $curso['cod_plan_academico'] == $comision->cod_plan_academico ? "selected" : "";
                                echo '<option value="'.$curso['cod_plan_academico']. '" '.   $selected . ' '.$disabled.'>'.$curso['nombre'].'</option>';
                            } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputEmail1"><?php echo lang('periodos'); ?> </label>
                        <?php if(isset($tiene_inscriptos) && $tiene_inscriptos  === 1){
                            $dejarModificar = "disabled='disabled'";
                        } else {
                           $dejarModificar = '';
                        } ?>
                        <select name="periodos" id="periodos" class="form-control chosen-control" data-placeholder="<?php echo lang('periodo')?>" <?php echo $dejarModificar ?>  >
                            <option></option>
                            <?php foreach($periodos as $periodo){
                                    foreach($periodo['modalidad'] as $modalidad_periodo){
                                        $selected =  $periodo['cod_tipo_periodo'] == $comision->cod_tipo_periodo && $modalidad_periodo['modalidad'] == $comision->modalidad ? "selected" : "";
                                        $nombre_periodo = $modalidad_periodo['nombre_periodo'].'['.$modalidad_periodo['modalidad'].']';
                                        echo '<option value="'.$periodo['cod_tipo_periodo']. '" ' . $selected  .  ' data-modalidad="'.$modalidad_periodo['modalidad'].'">'.$nombre_periodo.'</option>'; 
                                    }
                                } ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputEmail1"><?php echo lang('anio_lectivo'); ?></label>
                        <select name="ciclo" id="ciclo_lectivo" class="form-control chosen-control" data-placeholder="<?php echo lang('anio_lectivo');?>">
                          <?php if($comision->getCodigo() != -1){
                                foreach($ciclos_lectivos as $key=>$ciclo){
                                    $select = '';
                                    if($ciclo['codigo'] == $comision->ciclo){
                                        $select = 'selected';
                                    }
                                  echo '<option value='.$ciclo['codigo'].' '.$select.'>'.$ciclo['ciclo_lectivo'].'<option>';
                                }
                             } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="comision_descripcion"><?php echo lang("alias") ?></label>
                    <input type="text" name="comision_descipcion" value="<?php echo $nombre; ?>" class="form-control input-sm" onkeypress="return verificar_caracteres();">
                </div>
            </div>
            <div class="row">                
                <div class="col-md-12">
                    <label><?php echo lang('nombre');?></label>
                    <div class="form-group">
                        <label class="label-lg" id="prefijo"><?php echo $prefijo; ?></label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 alert alert-danger" id="errores"></div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn  btn-success" type="submit" value="guardar">
                <i class="icon-ok bigger-110"></i>
                <?php echo lang('guardar'); ?>
            </button>
        </div>
    </form>
</div>
