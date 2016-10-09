<script>
    var langFrm = <?php echo $langFrm ?>;
    
</script>
<script src="<?php echo base_url('assents/js/configuracion/frm_puntoDeVenta.js')?>"></script>

<style>    
    #contenedorUsuarios{        
        max-height: 200px;
        overflow: auto;       
    }    
</style>
<div class="modal-content" name="frm_punto_venta">
    <input type="hidden" name="codigo_punto_venta" value="<?php echo $myPuntoVenta->getCodigo(); ?>">
    <div class="modal-header">
        <h4 class="blue bigger">                
            <?php echo lang('modificar').' '.lang('punto_venta'); ?>                
        </h4>
    </div>
    <div class="modal-body overflow-visible">
        <div class="row">                
            <div class="col-md-6 col-xs-12 form-group">
                <label>Proximo Numero Utilizar</label>
                <input type="text" name="proximo_numero" value="<?php echo $myPuntoVenta->nro ?>">
            </div>                
            <div class="col-md-6 col-xs-12 form-group">
                 <label>            
                     Habilitado<br>
                    <input name="activo" class="ace ace-switch ace-switch-6" type="checkbox" 
                        <?php if ($myPuntoVenta->estado == "habilitado") { ?> checked="true" <?php }?>>
                    <span class="lbl"></span>
                </label>                    
            </div>
            
        </div>                
        <div class="row">                
            <div class="col-md-12 col-xs-12 form-group">
                Usuarios Habilitados
            </div>
        </div>
        <div class="row">
            <?php foreach ($usuarios as $usuario){ ?> 
            <div class="col-md-12 col-xs-12 form-group">
                <input name="usuarios_habilitados" class="ace ace-switch ace-switch-6" type="checkbox" value="<?php echo $usuario['codigo']; ?>"
                        <?php if (in_array($usuario['codigo'], $usuarios_habilitados)) { ?> checked="true" <?php }?>>
                <span class="lbl">&nbsp;&nbsp;&nbsp;<?php echo "{$usuario['nombre']} {$usuario['apellido']}"; ?></span>
            </div>
            <?php } ?>
        </div>        
    </div>
    <div class="modal-footer">
        <button class="btn btn-sm btn-primary" name="btn_guardar_punto_venta">
            <i class="icon-ok"></i>
            <?php echo lang('guardar')?>
        </button>
    </div>
</div>