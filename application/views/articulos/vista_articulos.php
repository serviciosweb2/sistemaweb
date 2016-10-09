
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal.css')?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal-bs3patch.css')?>"/>

<script src="<?php base_url()?>assents/js/librerias/bootstrap-modal/bootstrap-modal.js"></script>
<script src="<?php base_url()?>assents/js/librerias/bootstrap-modal/bootstrap-modalmanager.js"></script>

<script src="<?php echo base_url('assents/js/articulos/vista_articulos.js');?>"></script>


    <div class="col-md-12 col-xs-12">
        <div id="areaTablas">
            
            <?php 
            
            $tmpl=array ( 
                'table_open'=>'<table id="articulos" width="100%" class="table table-striped table-condensed table-bordered"  oncontextmenu="return false"
                onkeydown="return false">'); 
            $this->table->set_template($tmpl); 
                $this->table->set_heading(array('','','','', '','','','')); 
                echo $this->table->generate();
            ?>
        </div>
    </div>

