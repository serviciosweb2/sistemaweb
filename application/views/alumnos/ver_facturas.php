
<script>
    var facturasAlumno = '<?php echo json_encode($facturasAlumno)?>';
</script>
<script src="<?php echo base_url('assents/js/alumnos/ver_facturas.js')?>"></script>
<div class="modal-content">
    <div class="modal-header">
            <h4 class="blue bigger"><h4 class="blue bigger"><?php echo lang('ver_facturas')?><small><i class="icon-double-angle-right"></i>  <?php echo $nombreFormateado?></small></h4></h4>
    </div>

    <div class="modal-body overflow-visible">
            <div class="row">
                <table id="ver_facturas" class ="table table-bordered table-condensed">
                  
                    <thead>
                    <th><?php echo lang('razon_social');?></th>
                    <th><?php echo lang('punto_venta');?></th>
                    <th><?php echo lang('tipo_factura');?></th>
                    <th><?php echo lang('nro_factura');?></th>
                    <th><?php echo lang('total');?></th>
                    </thead>
                    <tbody>
                        
                    </tbody>  
                
            </table>


            </div>
    </div>

<!--    <div class="modal-footer">
            <button class="btn btn-sm" data-dismiss="modal">
                    <i class="icon-remove"></i>
                    Cancel
            </button>

            <button class="btn btn-sm btn-primary">
                    <i class="icon-ok"></i>
                    Save
            </button>
    </div>
</div> -->
