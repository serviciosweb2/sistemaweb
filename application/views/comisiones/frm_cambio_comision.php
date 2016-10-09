<script src="<?php echo base_url("assents/js/comisiones/frm_cambio_periodo.js")?>"></script>
<script>
    var langFrm_cambio_periodo = <?php echo $lang ?>;    
</script>
<div class="modal-content" >
    <div class="modal-header">                                      
        <h3 class="blue bigger"><?php echo lang('comisiones'); ?> - <?php echo lang('cambio_de_comision') ?></h3> 
    </div>
    <div class="modal-body overflow-visible">
        <?php if($totaAlumnosOrigen > 0) { ?>
        <div class='row' style='min-height: 382px !important'>
            <div id='div_listado_comisiones'>
                <div class='form-group col-md-12'>
                    <div class=' content-tabla-ctacte'>
                        <table id="tableCambioComision" cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered dataTable no-footer" role="grid">
                            <thead>
                                <tr>
                                    <th><?php echo lang("comision_origen"); ?></th>
                                    <th style=""><?php echo lang("observaciones") ?></th>
                                    <th style='width: 220px;'><?php echo lang('comision_destino'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                <tr>
                                    <td><?php echo $nombre_comision_origen?></td>
                                    <td style="">
                                        
                                        <span class='text-success' style='font-size: 11px; cursor: pointer;'  onclick='mostrar_alumnos_cursando(<?php echo $cod_comision_origen ?>);'>
                                            (<?php echo lang("alumnos_cursando").$totaAlumnosOrigen; ?>)
                                        </span>
                                        
                                    </td>
                                    <td>
                                        <select name='cod_comision_destino' id="cod_comision_destino" class='select_chosen' style='width: 200px;' <?php if (!isset($arrComisiones) || count($arrComisiones) == 0){ ?>disabled="true"<?php } ?> data-placeholder='Seleccione Comisión'>
                                        <?php   if (isset($arrComisiones))
                                                {                                            
                                                    if (count($arrComisiones) > 0)
                                                    { 
                                                        $tieneComisiones = false;
                                                        foreach ($arrComisiones as $comision)
                                                        { 
                                                            if ($comision['codigo'] <> $cod_comision_origen)
                                                            { 
                                                                $tieneComisiones = true; ?> 
                                                                <option value="<?php echo $comision['codigo'] ?>">
                                                                    <?php echo $comision['nombre'] ?>
                                                                </option>
                                                    <?php   }
                                                        }
                                                        if (!$tieneComisiones)
                                                        { ?> 
                                                            <option value="-1"><?php echo lang("sin_registros"); ?></option>     
                                                <?php   }
                                                    }
                                                    else 
                                                    { ?>
                                                        <option value="-1"><?php echo lang("sin_registros"); ?></option>
                                            <?php   }
                                                } ?>
                                        </select>
                                    </td>
                                </tr>
                                
                                <input type='hidden' id='comision_origen' value='<?php echo $cod_comision_origen ?>'>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-group col-md-12">
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
        <?php } else {?>
        <div id='div_listado_comisiones'>
            <div class='form-group col-md-12'>
                <div class=' content-tabla-ctacte'>
                    <table style="min-height: 50px;" id="tableCambioComision" cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered dataTable no-footer" role="grid">
                        <tr>
                            <td style="text-align: center;"><?php echo lang("la_comision_seleccionada_no_tiene_alumnos"); ?></td>
                        </tr>
                    </table>
                </div>    
            </div>
        </div>    
        <?php } ?>
    </div>
    <div class="modal-footer">
        <?php if($totaAlumnosOrigen > 0) { ?>
        <button name="btn_guardar" class="btn  btn-success" value="guardar" onclick='guardarCambioComision();'>
            <i class="icon-ok bigger-110"></i>
            <?php echo lang('guardar'); ?>
        </button>
        <?php } ?>
        <button name="btn_volver" class="btn  btn-success" value="volver" onclick='volver();' style="display: none;">
            <i class="icon-ok bigger-110"></i>
            <?php echo lang('volver'); ?>
        </button>        
    </div>
</div>