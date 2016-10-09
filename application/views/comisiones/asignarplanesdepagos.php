<script>
    var langFrm= <?php echo $langfRM ?>;
    var dias_prorroga = '<?php echo $comision->dias_prorroga?>'
    var fecha_inicio_comision = '<?php echo $fecha_inicio_comision?>';
</script>
<script src="<?php echo base_url('assents/theme/assets/js/fuelux/fuelux.spinner.min.js')?>"></script>
<script src="<?php echo base_url('assents/js/comisiones/frm_asignarplanes.js')?>"></script>

<?php $this->load->helper('formatearfecha'); ?>
<link rel="stylesheet" href="<?php echo base_url('assents/css/comisiones/frm_asignarplanes.css?');?>"/>
<div class="modal-content">
    <div class="modal-header">
        <h4 class="blue bigger">
            <?php echo lang('asignarPlanes');?>
            <small>
                <i class="icon-double-angle-right"></i>
                <?php echo $nombre_comision?>
            </small>
        </h4>
        <i class="icon-time bigger-110 orange middle"></i>&nbsp&nbsp
        <?php echo $horario_comision['horarios']?>
    </div>
    <div class="modal-body overflow-visible">
        <div class="row">
            <form id="planes_pago_comision">
                <input name="cod_comision" type="hidden" value="<?php echo $id_comision?>">
                <div class="tabla_planes_vigentes">
                    <table id="planes_comision" class="table table-bordered" style="width: 100% !important;">
                        <thead>
                            <th><?php echo lang('planes');?></th>
                            <th><?php echo lang('activo');?></th>
                            <th><?php echo lang('mostrar_en_web');?></th>
                            <th><?php echo lang('fecha_hasta');?></th>
                            <th><?php echo "Mostrar Financiacion en web" ?></th>
                        </thead>
                        <tbody>
                    <?php $checked = '';
                    $selected = '';
                    foreach($planesNoAsignados as $rowPlanNoAsignado){
                        $checked = '';
                        $selected = '';
                        $desactivarMostrar ='disabled';
                        $checkDisabled = 'disabled';
                        $div = 'hide';
                        $checkearActivarDias = '';
                        $mostarFinanciacion = false;
                        foreach($planAsignado as $rowPlanAsignado){
                            if ($rowPlanNoAsignado['codigo'] == $rowPlanAsignado['codigo']){
                                $mostarFinanciacion = isset($rowPlanAsignado['mostrar_financiacion_web']) && $rowPlanAsignado['mostrar_financiacion_web'] == 1;
                            }
                            $seleccionar_periodo = '';
                            if(isset($rowPlanAsignado['dias_prorroga'])){
                                $seleccionar_periodo = 'selected';
                            }

                            if($rowPlanNoAsignado['codigo'] == $rowPlanAsignado['codigo']){
                                $checked = 'checked';
                                $desactivarMostrar = '';
                                if($rowPlanAsignado['mostrar_web'] == 1){
                                    $selected = 'checked';
                                    $checkDisabled = '';
                                    $div = '';
                                    $checkearActivarDias = 'checked';
                                }
                            }
                        } 
                        $disabledMostrarWeb = '';
                        $mostrarTooltip = '';
                        if($fecha_inicio_comision == 'no_tiene_horarios'){
                            $disabledMostrarWeb = 'disabled';
                            $mostrarTooltip = 'mostrar_tooltip';
                            $desactivarMostrar = '';
                        } ?>
                            <tr>
                                <td><?php echo $rowPlanNoAsignado['nombre'] ?></td>
                                <td>
                                    <input  type="checkbox" class="activar_plan" name="activar_plan" value='<?php echo $rowPlanNoAsignado['codigo']?>' onclick="activarPlan(this)" <?php echo $checked?>>
                                </td>
                                <td>
                                    <label style="padding-top: 10px;">
                                        <input name="mostrar_web" class="ace ace-switch ace-switch-6 habilitar_web" type="checkbox" value='<?php echo $rowPlanNoAsignado['codigo'] ?>' onclick="mostrarWeb(this,event)"  
                                            <?php echo $selected.' '.$disabledMostrarWeb.' '.$desactivarMostrar ?>>
                                        <span class="lbl <?php echo $mostrarTooltip ?>" title="<?php echo lang('no_tiene_horarios') ?>" ></span>
                                    </label>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                    <?php $disabled="disabled";
                                        if($selected == 'checked'){
                                            $disabled = '';
                                        }
                                        if($fecha_inicio_comision == 'no_tiene_horarios'){
                                            $fecha_inicio_com = lang('no_tiene_horarios');
                                        } else {
                                            $fecha_inicio_com = $fecha_inicio_comision;
                                        } ?>
                                        <div class="col-md-6">
                                        <label>Inicio: <?php echo $fecha_inicio_com ?>
                                            <input type="checkbox" class="habilitar_dias" name="habilitar_dias" <?php echo $disabledMostrarWeb ?>
                                                   onclick="activarHabilitar_Dias(this,event)" <?php echo $checkDisabled.' '.$checkearActivarDias ?>>
                                        </label>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="spinner_dias <?php echo $div ?>" style="padding-top: 9px;">
                                                <input type="text" class="input-mini" name="dias_prorroga"/>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <label style="padding-top: 10px;">
                                        <input name="mostrar_financiacion_web" class="ace ace-switch ace-switch-6" type="checkbox" value='<?php echo $rowPlanNoAsignado['codigo'] ?>'
                                            <?php if ($selected == ''){ ?>disabled="true"<?php } ?> <?php if ($mostarFinanciacion){ ?>checked="true"<?php } ?>
                                            onchange="mostrar_ocultar_financiacion_web(this);">
                                        <span class="lbl" title=""></span>
                                    </label>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>