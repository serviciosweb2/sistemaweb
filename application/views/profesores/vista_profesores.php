<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal.css')?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal-bs3patch.css')?>"/>
<script>
    var menuJson = <?php echo $menuJson?>;
    var columns = <?php echo $columns?>;
</script>
<script src="<?php base_url()?>assents/js/librerias/bootstrap-modal/bootstrap-modal.js"></script>
<script src="<?php base_url()?>assents/js/librerias/bootstrap-modal/bootstrap-modalmanager.js"></script>






<script src="<?php echo base_url('assents/js/profesores/profesores.js')?>"></script>


    <div class="col-md-12 col-xs-12">
        <div id="areaTablas">
            <?php $tmpl=array ( 'table_open'=>'
            <table id="academicoProfesores" width="100%" class="table table-bordered table-condensed" oncontextmenu="return false"
            onkeydown="return false">'); 
            $this->table->set_template($tmpl); 
            $this->table->set_heading(array('','', '', '','',''));
            echo $this->table->generate(); ?>
        </div>
    </div>
