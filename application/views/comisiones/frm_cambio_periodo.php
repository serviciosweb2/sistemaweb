<script src="<?php echo base_url("assents/js/comisiones/frm_cambio_periodo.js")?>"></script>
<script>
    var langFrm_cambio_periodo = <?php echo $lang ?>;    
</script>
<div class="modal-content" >
    <div class="modal-header">                                      
        <h3 class="blue bigger"><?php echo lang('comisiones'); ?> - <?php /*Ticket 4581 -mmori- Se modifica el titulo para representar correctamente la acción deseada.*/echo ($cod_comision_origen != "") ? lang('cambio_de_comision') : lang('pasaje_de_periodos') ?></h3> 
    </div>
    <div class="modal-body overflow-visible">
        <div class='row' style='min-height: 382px !important'>
            <div id='div_listado_comisiones'>
                <div class='form-group col-md-12'>
                    <div class=' content-tabla-ctacte'>
                        <table id="tableComisiones" cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered dataTable no-footer" role="grid">
                            <thead>
                                <tr>
                                    <th <?php if ($cod_comision_origen != null){ ?>style="display: none;"<?php } ?>>&nbsp;</th>
                                    <th><?php echo lang("comision_origen"); ?></th>
                                    <th <?php if ($cod_comision_origen != null){ ?>style=""<?php } ?>><?php echo lang("observaciones") ?></th>
                                    <th style='width: 220px;'><?php echo lang('comision_destino'); ?></th>
                                </tr>
                            </thead>
                     
                            <?php foreach ($comisiones_cambiar as $codComision => $comision){ 
                      
                                $cantidadEstadoCursando = $comision['totaAlumnosOrigen'];
                                ?>
                            <tr>
                                <td <?php if ($cod_comision_origen != null){ ?>style="display: none;"<?php } ?>>
                                    <div class="">
                                        <label>
                    <!--Fix para pasar de comision cuando el alumno esta libre dado de baja -->
                    <?php //if ($cantidadEstadoCursando > 0){ ?><!--disabled="false"--><?php //} ?>
                                            <input type='checkbox' class='checkselect ace ace-checkbox-2' name='cod_comision' 
                                                   value='<?php echo $codComision ?>' onclick='cod_comsion_checked(this);'                
                   <?php if ($cod_comision_origen != null){ ?>checked="true"<?php } ?>>
                                            <span class="lbl"></span>
                                        </label>
                                    </div>
                                    <input type='hidden' name='cod_plan_academico' value='<?php echo $comision['cod_plan_academico']; ?>'>
                                    <input type='hidden' name='cod_tipo_periodo' value='<?php echo $comision['cod_tipo_periodo']; ?>'>
                                </td>
                                <td><?php echo $comision['nombre'];  ?></td>
                                <td <?php if ($cod_comision_origen != null){ ?>style=""<?php } ?>>
                                    <?php if ($cantidadEstadoCursando > 0){ ?>
                                    <span class='text-success' style='font-size: 11px; cursor: pointer;'  onclick='mostrar_alumnos_cursando(<?php echo $codComision ?>);'>
                                        (<?php echo lang("total_alumnos").$comision['cantidad']. ' - Cursando: '.$comision['totaAlumnosOrigen']; ?>)
                                    </span>    
                                    <?php } ?>
                                </td>
                                <td>
                                    <select name='cod_comision_destino' class='select_chosen' style='width: 200px;' <?php if ($cantidadEstadoCursando > 0 || !isset($arrComisiones) || count($arrComisiones) == 0){ ?>disabled="true"<?php } ?> data-placeholder='Seleccione Comisión'>
                                        <?php if (isset($arrComisiones)){                                            
                                            if (count($arrComisiones) > 0){ 
                                                $tieneComisiones = false;
                                                foreach ($arrComisiones as $comision){ 
                                                    if ($comision['codigo'] <> $cod_comision_origen){ 
                                                        $tieneComisiones = true; ?> 
                                        <option value="<?php echo $comision['codigo'] ?>">
                                            <?php echo $comision['nombre'] ?>
                                        </option>
                                                    <?php }
                                                 }
                                                 if (!$tieneComisiones){ ?> 
                                        <option value="-1"><?php echo lang("sin_registros"); ?></option>     
                                                <?php }
                                            } else { ?>
                                        <option value="-1"><?php echo lang("sin_registros"); ?></option>
                                            <?php }
                                        } ?>
                                    </select>
                                </td>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>
                <div class="form-group col-md-12" <?php if ($cod_comision_origen == null){ ?>style='display: none;'<?php } ?>>
                    <div class="form-group col-md-4">
                        <label><?php echo lang("fecha"); ?></label>
                        <div class="input-group">
                            <input class="form-control date-picker" value="<?php echo $fecha_desde; ?>"  id="fecha_cambio_comision" name="fecha_cambio_comision" type="text" data-date-format="dd-mm-yyyy">
                            <span class="input-group-addon">
                                <i class="icon-calendar bigger-110"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div id='div_listado_alumnos_cursando'>
                <div class='form-group col-md-12'>
                    <div class=' content-tabla-ctacte'>                       
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button name="btn_guardar" class="btn  btn-success" value="guardar" onclick='guardarPasajeComision();'>
            <i class="icon-ok bigger-110"></i>
            <?php echo lang('guardar'); ?>
        </button>
        <button name="btn_volver" class="btn  btn-success" value="volver" onclick='volver();' style="display: none;">
            <i class="icon-ok bigger-110"></i>
            <?php echo lang('volver'); ?>
        </button>        
    </div>
</div>