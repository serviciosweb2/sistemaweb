<link rel="stylesheet" href="<?php echo base_url('assents/theme/assets/css/jquery.datetimepicker.css'); ?>"/>
<style type="text/css" media="screen">
    .fancybox-lock .fancybox-overlay {
        overflow-y: auto;
    }

    .fancybox-inner {
        overflow: auto;
    }

    .fancybox-inner .chosen-results {
        max-height: 150px;
    }
</style>

<script src="<?php echo base_url('assents/theme/assets/js/jquery.datetimepicker.js'); ?>"></script>
<script>
    var langFrm = <?php echo $langFrm ?>;
</script>

<script src="<?php echo base_url('assents/js/caja/frm_nuevo_movimiento.js')?>"></script>
<div class="modal-content" name="frm_nuevo_movimiento">
    <div class="modal-header">
        
        <h4 class="blue bigger"><?php echo lang("nuevo_movimiento_de_caja"); ?> (<?php echo $nombre_caja; ?>)</h4>
    </div>
    <div class="modal-body overflow-visible">
        
        <div class="row">
            <div class="form-group col-md-6 col-xs-12">
                <label><?php echo lang("metodo"); ?></label>
                <div class="input-group">
                    <select name="nuevo_movimiento_metodo" class="select-chosen" style="width: 208px;">
                        <option value="salida"><?php echo lang("salida_caja_cabecera"); ?></option>
                        <option value="entrada"><?php echo lang("entrada_caja_cabecera"); ?></option>
                    </select>
                </div>
            </div>
            <div class="form-group col-md-6 col-xs-12">
                
                <label><?php echo lang("importe"); ?></label>
                <div class="input-group">
                    <input type="text" value="0" name="nuevo_movimiento_importe"  style="width: 212px;">
                </div>
            </div>
        </div>
        
        <div class="row" id="row_rubro">
            <div class="form-group col-md-6 col-xs-12">
                <label><?php echo lang("rubro"); ?></label>
                <div>
                    <select name="rubro" class="select-chosen" style="width: 208px;">
                        <option value="nada"><?php echo lang('seleccione_opcion') ?></option>
                            
                    <?php foreach ($rubros as $rubros){ ?> 
                        <option value="<?php echo $rubros['rubro']?>">
                            <?php echo lang($rubros['rubro']) ?>
                        </option>
                    <?php } ?>
                    </select>
                </div>
            </div>
            <div class="form-group col-md-6 col-xs-12">
                <label><?php echo lang("subrubro"); ?></label>
                <div>
                    <select name="subrubro" style="width: 208px;" class="select-chosen">
                        <option value="0"><?php echo lang('seleccione_opcion') ?></option>
                    </select>
                </div>    
            </div>
        </div> 
        
        <div class="row">
            <div class="form-group col-md-6 col-xs-12">
                <label><?php echo lang('fecha');?></label>
                <div class="input-group">
                    <input type="text" name="nuevo_movimiento_fecha" value="<?php echo $fecha ?>" class="form-control">
                    <span class="input-group-addon">
                        <i class="icon-calendar bigger-110"></i>
                    </span>
                </div>
            </div>            
            <div class="form-group col-md-6 col-xs-12">
                <label><?php echo lang("medio_de_pago") ?></label>
                <div class="input-group">
                    <select name="nuevo_movimiento_medio_pago" style="width: 208px;" class="select-chosen">
                        <?php foreach ($medios_pago as $medio_pago){ ?> 
                        <option value="<?php echo $medio_pago['codigo'] ?>">
                            <?php echo lang($medio_pago['medio']) ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-12 col-xs-12">
                <label><?php echo lang("descripcion_ctacte_facturar"); ?></label>
                <div class="input-group">
                    <input type="text" class="form-control" name="nuevo_movimiento_descripcion" style="width: 442px;">
                </div>
            </div>
        </div>
           
    </div>
    <div class="modal-footer">
        <button class="btn btn-sm btn-primary" type="" name="abrir_caja" onclick="guardarNuevoMovimiento();">
            <i class="icon-ok"></i>
            <?php echo lang("agregar_movimiento"); ?>
        </button>
    </div>
</div>
<input type="hidden" value="<?php echo $codigo_caja ?>" name="nuevo_movimiento_codigo_caja">
<input type="hidden" name="min_date_dia" value="<?php echo $min_date_dia ?>">
<input type="hidden" name="min_date_mes" value="<?php echo $min_date_mes ?>">
<input type="hidden" name="min_date_anio" value="<?php echo $min_date_anio ?>">
<input type="hidden" name="min_date_his" value="<?php echo $min_date_hora.":".$min_date_min.":".$min_date_seg ?>">
<input type="hidden" name="max_date_dia" value="<?php echo date("d"); ?>">
<input type="hidden" name="max_date_mes" value="<?php echo date("m"); ?>">
<input type="hidden" name="max_date_anio" value="<?php echo date("Y"); ?>">
<input type="hidden" name="max_date_his" value="<?php echo date("H:i:s"); ?>">

<!--<script>
    var min_date_dia = '<?php echo $min_date_dia ?>';
    var min_date_mes = '<?php echo $min_date_mes ?>';
    var min_date_anio = '<?php echo $min_date_anio ?>';
    var min_date_his = '<?php echo $min_date_hora.":".$min_date_min.":".$min_date_seg ?>';
    var max_date_dia = '<?php echo date("d"); ?>';
    var max_date_mes = '<?php echo date("m"); ?>';
    var max_date_anio = '<?php echo date("Y"); ?>';
    var max_date_his = '<?php echo date("H:i:s"); ?>';
</script>-->