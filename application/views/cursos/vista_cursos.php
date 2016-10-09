    <style>
    
    td.highlight {
        background-color: whitesmoke !important;
    }   
    .bootbox{
        width: auto !important;
    }
    .modal.fade.in {
        top: 25%;
    }
</style>

<script>
    var menuJson = <?php echo $menuJson ?>;
    var columns = <?php echo $columns ?>;
</script>

<script src="<?php echo base_url('assents/js/cursos/cursos.js');?>"></script>

        <div class="col-md-12 col-xs-12">
            <div id="areaTablas">                                
              
                    <?php 
                        $tmpl=array ( 'table_open'=>'<table id="academicoCursos" width="100%" class="table table-striped table-condensed table-bordered"  oncontextmenu="return false" onkeydown="return false">'); 
                        $this->table->set_template($tmpl); $this->table->set_heading(array('','','', '','','','', ''));
                        echo $this->table->generate(); 
                    ?>
            </div>
        </div>
      
