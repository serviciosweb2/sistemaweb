<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal.css')?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal-bs3patch.css')?>"/>
<script>
    var menuJson = <?php echo $menuJson?>;
    var columnsFinales = <?php echo $columnsFinales?>;
    var columnsParciales = <?php echo $columnsParciales?>;
</script>
<script src="<?php echo base_url('assents/js/librerias/bootstrap-modal/bootstrap-modalmanager.js')?>"></script>
<script src="<?php echo base_url('assents/js/librerias/bootstrap-modal/bootstrap-modal.js')?>"></script>
<script src="<?php echo base_url('assents/js/examenes/examenes.js')?>"></script>
<script src="<?php echo base_url('assents/js/impresiones.js') ?>"></script>

<div class="col-md-12 col-xs-12">
    <div id="areaTablas" class="col-md-12 ">
        <div class="row">
            <ul class="nav nav-tabs">
                <li class="active" ><a href="#Finales" data-toggle="tab"><?php echo lang('finales'); ?></a></li>
                <li><a href="#Parciales" data-toggle="tab"><?php echo lang('parciales'); ?></a></li>
            </ul>
            <div class="tab-content">                    
                <div class="tab-pane active" id="Finales">
                    <div class="table-responsive">
                     <?php $tmpl = array ( 'table_open'=>'
                        <table id="academicoExamenesFinales" class="table table-striped table-bordered table-hover"
                        oncontextmenu="return false" onkeydown="return false" style="width=100% !important;">');
                        $this->table->set_template($tmpl); $this->table->set_heading(array('','', '', '','','','','','',''));
                        echo $this->table->generate(); ?>
                    </div>                        
                </div>                    
                <div class="tab-pane " id="Parciales">                        
                    <div class="table-responsive">  
                        <div id="areaTablas">
                        <?php $tmpl = array ( 'table_open'=>'
                            <table id="academicoExamenesParciales" class="table table-striped table-bordered table-hover"
                            oncontextmenu="return false" onkeydown="return false" style="width=100% !important;">'); 
                            $this->table->set_template($tmpl); $this->table->set_heading(array('','','','','','','','','','')); 
                            echo $this->table->generate(); ?>    
                        </div>
                    </div>
                </div>          
            </div>
        </div>
    </div>
</div>