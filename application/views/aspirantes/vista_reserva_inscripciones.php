<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal.css')?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal-bs3patch.css')?>"/>

<script src="<?php base_url()?>assents/js/librerias/bootstrap-modal/bootstrap-modal.js"></script>
<script src="<?php base_url()?>assents/js/librerias/bootstrap-modal/bootstrap-modalmanager.js"></script>


<script src="<?php echo base_url("assents/js/librerias/datatables/jquery.dataTables.min.js")?>"></script>
<script>
    var columns = JSON.parse('<?php echo $columns?>');
    

</script>


<script src="<?php echo base_url('assents/js/aspirantes/reservas_inscripciones.js')?>"></script>

<div class="col-md-12 col-xs-12">
        <div id="areaTablas">
            <?php $tmpl=array ( 'table_open'=>'
            <table id="reserva_inscripcion" cellpadding="0" cellspacing="0"
            border="0" class="table table-striped table-bordered table-condensed " oncontextmenu="return false" 
            onkeydown="return false" style="width:100% !important;">'); $this->table->set_template($tmpl); $this->table->set_heading(array('','',
               '','','','', '')); echo $this->table->generate();
                ?>
        </div>
    </div>