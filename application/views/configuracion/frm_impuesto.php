<script>
    var langFrm = <?php echo $langFrm ?>;
   
</script>
<script src="<?php echo base_url('assents/js/configuracion/frm_impuesto.js')?>"></script>


<input type="hidden" name="conceptos" value='<?php echo json_encode($conceptos)?>'>
<input type="hidden" name="listado" value='<?php echo json_encode($detalleImpuesto)?>'>
<form id="impuesto">
<div class="modal-content">
    
    <div class="modal-header">

            <h4 class="blue bigger"> <?php echo lang('detalle_impuesto')?></h4>
            
            <input type="hidden" name="cod_impuesto" data-nombre="<?php echo $objImpuesto->nombre?>" value='<?php echo $objImpuesto->getCodigo()?>'>
           
    </div>

    <div class="modal-body overflow-visible">
        <div class="row">
            <div class="col-md-4">
                <button class="btn btn-sm btn-primary" id="nuevoImpuesto">
                    <!--<i class="icon-ok"></i>-->
                    <?php echo lang('nuevo_impuesto')?>
            </button> 
            </div>
            <div class="col-md-4">
                <input type="text" name="nombre_impuesto" value="<?php echo $objImpuesto->nombre?>">
            </div>
            <div class="col-md-4">
                <input type="text" name="valor" value="<?php echo $objImpuesto->valor?>">
            </div>
       </div>
        
            
        <br>
            <div class="row">
                    
                <div class="table-responsive">
                    
                    
                    
                    
                </div>
                
                
                
            </div>
    </div>

    <div class="modal-footer">
            

            <button class="btn btn-sm btn-primary">
                    <i class="icon-ok"></i>
                    <?php echo  lang('guardar')?>
            </button>
    </div>
</div>
</form>








