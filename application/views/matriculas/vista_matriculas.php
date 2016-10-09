<script src="<?php echo base_url("assents/js/librerias/datatables/jquery.dataTables.min.js")?>"></script>
<script src="<?php echo base_url('assents/js/librerias/bootstrap-modal/bootstrap-modalmanager.js')?>"></script>
<script src="<?php echo base_url('assents/js/librerias/bootstrap-modal/bootstrap-modal.js')?>"></script>
<script src="<?php echo base_url('assents/js/impresiones.js') ?>"></script>

<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal.css')?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal-bs3patch.css')?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/css/matricula/matricula.css');?>"/>
<script>
    var menuJson = <?php echo $menuJson?>;
    var columns = <?php echo $columns?>;    
</script>
<script src="<?php echo base_url('assents/js/matriculas/matriculas.js');?>"></script>

<style>
    .label-success {
        
        width: 84px !important;
        
    }
    
    #menuMover{
        background-color: #428bca !important;
        border-width: 5px;
        color: white !important;
        padding: 6px 12px;
        height: 38px;
    }
    #ui-datepicker-div {
        z-index: 9999999 !important;
    }
    
</style>
<div class="col-md-12 col-xs-12">
    <div id="areaTablas">
        <?php 
        $tmpl=array ('table_open'=>'<table id="academicoMatriculas" width="100%" class="table table-striped table-condensed table-bordered table table-hover" oncontextmenu="return false" onkeydown="return false">'); 
        $this->table->set_template($tmpl); 
        $this->table->set_heading(array('','','', '','','')); 
        echo $this->table->generate(); 
        ?>
    </div>
</div>
<input type="hidden" name="hd_regularizar_alumnos" value="<?php echo $seccion_regularizar_alumnos ? 1 : 0; ?>">
<input type="hidden" name="hd_pasar_libres" value="<?php echo $seccion_pasar_libres ? 1 : 0; ?>">
