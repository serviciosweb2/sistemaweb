<script>
    var langFrm = <?php echo $langFrm ?>;
</script>
<script src="<?php echo base_url('assents/js/configuracion/frm_nuevoImpuesto.js')?>"></script>

<div class="modal-content">
    <form id="nuevoImpuesto">
    <div class="modal-header">
            <!--<button type="button" class="close" data-dismiss="modal">&times;</button>-->
            <h4 class="blue bigger"><?php echo lang('nuevo_impuesto');?></h4>
    </div>

    <div class="modal-body overflow-visible">
            <div class="row">
                <div class="form-group col-xs-12">
                    
                    <input class="form-control" type="hidden" name="cod_impuesto" value="<?php echo $objImpuesto->getCodigo()?>">
                    <label><?php echo lang('nombre')?></label>
                    <input class="form-control" name="nombre_impuesto" value="<?php echo $objImpuesto->nombre?>">
                    
                    <label><?php echo lang('valor')?></label>
                    <input class="form-control" name="valor_impuesto" value="<?php echo $objImpuesto->valor?>">
                    
                    <label><?php echo lang('tipo')?></label>
                    <select class="form-control" name="tipo_impuesto" data-placeholder="<?php echo lang('seleccione_tipo_impuesto');?>">
                        <option></option>
                        <?php
                         foreach($tipos_impuestos as $tipo){
                                    
                                   $selected = $tipo['codigo'] == $objImpuesto->tipo ? 'selected' : '';
                                    
                                    echo '<option value='.$tipo['codigo'].' '.$selected.'>'.$tipo['nombre'].'</option>';
                                    
                                }
                        ?>
                    </select>
                    <?php if (!empty($impuestos_general)) { ?>
                    <label><?php echo lang('iva')?></label>
                    <select class="form-control" name="cod_impuesto_general" data-placeholder="<?php echo lang('seleccione_tipo_impuesto');?>">
                        <option></option>
                        <?php
                         foreach($impuestos_general as $imp_gral){
                                    
                                   $selected = $imp_gral['codigo'] == $objImpuesto->cod_impuesto ? 'selected' : '';
                                    
                                    echo '<option value='.$imp_gral['codigo'].' '.$selected.'>'.$imp_gral['nombre'].'</option>';
                                    
                                }
                        ?>
                    </select>
                    <?php } ?>
                    <br>
                    <label><?php echo lang('estado')?></label>
                    <br>
                    <label>
                        
                        <input name="estado_impuesto" class="ace ace-switch ace-switch-6" type="checkbox" <?php if ($check = $objImpuesto->baja  == 0) { ?> checked="true" <?php }?>>
                        <span class="lbl"></span>
                    </label>
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