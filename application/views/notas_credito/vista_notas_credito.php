<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal.css') ?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal-bs3patch.css') ?>"/>
<script src="<?php base_url() ?>assents/js/librerias/bootstrap-modal/bootstrap-modal.js"></script>
<script src="<?php base_url() ?>assents/js/librerias/bootstrap-modal/bootstrap-modalmanager.js"></script>
<script src="<?php echo base_url('assents/js/notas_credito/notas_credito.js'); ?>"></script>
<script src="<?php echo base_url('assents/js/impresiones.js') ?>"></script>


<style>
    .popover{
        max-width: none!important;
    }

    .verDetalleDeudor{
        cursor: pointer !important;
    }

    .buscador{        
        margin-top: 12px !important;        
    }

</style>

<div id="modalDtalle" class="modal fade" data-width="auto" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="blue bigger"><?php echo lang('detalles'); ?></h4>
    </div>
    <div class="modal-body">
        <div class="row" id="contenido">
        </div>
        <div class="row">
            <div class="table-responsive contenedorTabla">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" id="btn-ok-cambio-estado" class="btn btn-primary"><?php echo lang('continuar'); ?></button>
    </div>
</div>


<div class="col-md-12 col-xs-12 vista-nc">

    <div class="row">
        <div id="areaTablas" class="table-responsive">
            <?php
            $tmpl = array('table_open' => '
                <table id="administracionNC" cellpadding="0" cellspacing="0"
                border="0" class="table table-striped table-bordered table-condensed" oncontextmenu="return false"
                onkeydown="return false">');
            $this->table->set_template($tmpl);
            $arrColumnas = array();
            $this->table->set_heading('', '', '', '', '', '', '');
            echo $this->table->generate();
            ?>
        </div>
    </div>
</div>









