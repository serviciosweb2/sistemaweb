<script>
    var langFrm = <?php echo $langFrm ?>;
</script>
<script src="<?php echo base_url('assents/js/librerias/ajaxchosen/lib/ajax-chosen.js')?>"></script>
<script src="<?php echo base_url('assents/js/certificados/frm_certificado.js')?>"></script>
<style>
    .chosen-results{
        max-height: 100px !important;
    }
</style>
<div class="modal-content">
    <form id="frmNuevoPedido">
        <div class="modal-header">
            <h4 class="blue bigger"><?php echo lang('nuevo_pedido')?></h4>
        </div>
        <div class="modal-body overflow-visible">
            <div class="row">
                <div class="col-md-12 col-xs-12 form-group">
                    <label><?php echo lang('alumnos')?></label>
                    <select name="alumno" data-placeholder="<?php echo lang('alumnos_cobro')?>" multiple>
                        <option></option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-xs-12 form-group">
                    <label><?php echo lang('a_certificar')?></label>
                    <select name="certificados[]" data-placeholder="<?php echo lang('SELECCIONE_UNA_OPCION')?>">
                        <option></option>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-sm btn-primary">
                <i class="icon-ok"></i>
                <?php echo lang('guardar'); ?>
            </button>
        </div>
    </form>
</div>