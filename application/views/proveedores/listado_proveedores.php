<script src="<?php echo base_url('assents/js/proveedores/listado_proveedores.js')?>"></script>
<div class="modal-content" name="listado_proveedores">
    <div class="modal-header">
<!--        <button class="close" data-dismiss="modal" type="button">×</button>-->
        <h4 class="blue bigger"><?php echo lang('listado_de_proveedores'); ?></h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-primary boton-primario" name="nuevo_proveedor">
                    <i class="icon-bookmark"></i>
                    <?php echo lang('nuevo_proveedor'); ?>
                </button>
            </div>
        </div>
        <div class="row" id="areaTablas">
            <div class="col-md-12 col-xs-12">
                <table id="tableProveedores" class="table table-bordered"  onkeydown="return false" oncontextmenu="return false">
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
    <div class="modal-footer">
    </div>    
</div>