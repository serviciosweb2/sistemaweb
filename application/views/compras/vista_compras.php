<script>
    
    var menuJson = <?php echo $menuJson ?>;
     var columns = <?php echo $columns ?>;
    
</script>
<script src="<?php echo base_url('assents/js/compras/compras.js')?>"></script>
<style>
    .vistaArticulos{
        display: none;
    }
    
    .vistaProvedores{
        display: none;
    }
    
    .prueba{
        height:auto !important;
        width: '100%' !important;
    }
    
   
</style>
<div class="col-md-12 col-xs-12 vistaCompras modulo">
    <h3 class="header smaller lighter blue"><?php echo lang('config_compras')?></h3>
        <div id="areaTablas" class="table-responsive">
            <?php $tmpl=array ( 'table_open'=>'
            <table id="administracionCompras" cellpadding="0" cellspacing="0"
            border="0" class="table table-striped table-bordered table-condensed" oncontextmenu="return false"
            onkeydown="return false">'); 
            $this->table->set_template($tmpl); 
            $this->table->set_heading(
                    array('','','','','','','')); 
            echo $this->table->generate(); ?>
        </div>
        </div>




<div class="col-md-12 col-xs-12 vistaArticulos menucontextua modulo">
    <h3 class="header smaller lighter blue"><?php echo lang('articulos')?></h3>
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





<div class="col-md-12 col-xs-12 vistaProvedores modulo">
    <h3 class="header smaller lighter blue"><?php echo lang('proveedor')?></h3>
    <div id="areaTablas">
        <table id="tableProveedores" class="table table-bordered table-condensed"  onkeydown="return false" oncontextmenu="return false">
            <thead>

                    <th><?php echo lang('codigo') ?></th>
                    <th><?php echo lang('nombre') ?></th>
                    <th><?php echo lang('domicilio') ?></th>
                    <th><?php echo lang('identificacion')?></th>
                    <th><?php echo lang('telefono') ?></th>
                    <th><?php echo lang('email') ?></th>
                    <th><?php echo lang('descripcion'); ?></th>
                    <th><?php echo lang('estado') ?></th>

            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>