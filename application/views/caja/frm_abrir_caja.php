<script>
    var langFrm = <?php echo $langFrm ?>;
</script>
<script src="<?php echo base_url('assents/js/caja/frm_abrir_caja.js')?>"></script>
<div class="modal-content">
    <div class="modal-header">
        <!--<button class="close" data-dismiss="modal" type="button">×</button>-->
        <h4 class="blue bigger"><?php echo lang("apertura_de_caja") ?></h4>
    </div>
    <div class="modal-body overflow-visible">
        <div class="row">
            <div class="form-group  col-md-12 col-xs-12">
                <label><?php echo lang("caja") ?></label>
                <select name="cajas_abrir" class="select-chosen" style="width: 140px;" onchange="listarDatosCaja();">
                    <?php foreach ($cajas as $caja){ ?>
                    <option value="<?php echo $caja['codigo'] ?>">
                        <?php echo $caja['nombre'] ?>
                    </option>
                    <?php } ?>
                </select>
            </div>
        </div>        
        <div class="row">
            <div class="col-md-12 col-xs-12">
                <table class="table table-striped table-bordered dataTable" id="detalle_saldos_caja">
                    <thead>
                        <th style="width: 160px;"><?php echo lang("medio"); ?></th>
                        <th style="width: 120px;"><?php echo lang("saldo"); ?></th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>        
    </div>    
    <div class="modal-footer">
        <button class="btn btn-sm btn-primary" type="" name="abrir_caja" onclick="abrirCaja();">
            <i class="icon-ok"></i>
            <?php echo lang("abrir"); ?>
        </button>
    </div>    
</div>
<?php if (isset($ejecutar_script) && $ejecutar_script <> ''){ ?> 
<input type="hidden" name="ejecutar_script" value="<?php echo $ejecutar_script ?>">
<?php } ?>
