
<script src="<?php echo base_url('assents/js/librerias/tel-master/intlTelInput.js')?>"></script>
<script src="<?php echo base_url('assents/js/generalTelefonos.js')?>"></script>
<script src="<?php echo base_url('assents/theme/assets/js/inputMask/jquery.inputmask.js')?>"></script>

<link rel="stylesheet" href="<?php echo base_url('assents/css/tel-master/intlTelInput.css')?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal.css') ?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal-bs3patch.css') ?>"/>
<script>
    var menuJson = <?php echo $menuJson ?>;
    var columns = <?php echo $columns ?>;
</script>
<script src="<?php base_url() ?>assents/js/librerias/bootstrap-modal/bootstrap-modal.js"></script>
<script src="<?php base_url() ?>assents/js/librerias/bootstrap-modal/bootstrap-modalmanager.js"></script>

<script src="<?php echo base_url('assents/js/razones_sociales/razones_sociales.js'); ?>"></script>



<div id="msgComfirmacion" class="modal fade" data-width="40%" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3><i class="icon-warning-sign red"></i>    <?php echo lang('ADVERTENCIA')?>!</h3>
    </div>

    <div class="modal-body">

        <div class="row">
            <form id="frmcomfirmacion">

                <div class="col-md-12 col-xs-12" id="textoMsg">

                </div>

            </form>
        </div>

    </div>

</div>







<div class="col-md-12 col-xs-12">
    <div id="areaTablas">
        <?php
        $tmpl = array(
            'table_open' => '<table id="razonesSociales" width="100%" class="table table-striped table-condensed  table-bordered"  oncontextmenu="return false"
                onkeydown="return false">');
        $this->table->set_template($tmpl);
        $this->table->set_heading(array('', '', '', '', '', '', '', '', ''));
        echo $this->table->generate();
        ?>
    </div>
</div>