<script src="<?php echo base_url('assents/js/caja/adv_cajas_cerradas.js')?>"></script>
<div class="modal-content" name="adv_cajas_cerradas">
    <div class="modal-header">
        <button class="close" data-dismiss="modal" type="button">×</button>
        <h4 class="blue bigger"><?php echo lang("advertencia_de_cajas_cerradas"); ?></h4>
    </div>

    <div class="modal-body overflow-visible">
        <div class="row">
            <?php echo lang("la_accion_que_intenta_realizar_requiere_que_exista_al_menos_una_caja_abierta"); ?><br><br>
            <?php echo lang("quiere_abrir_una_caja_ahora"); ?>
        </div>
    </div>
    
    <div class="modal-footer">
       <?php $clase= isset($cod_compra) ? '' : ' hidden'?>
        <button class="btn btn-sm btn-danger" type="" name="btnCancelar">
            <?php echo lang("cancelar"); ?>
        </button>
        <button class="btn btn-sm btn-primary" type="" name="btnAceptar">
            <i class="icon-ok"></i>
            <?php echo lang("aceptar"); ?>
        </button>
        <button class="btn btn-sm btn-primary<?php echo $clase?>" data-idCompra="<?php echo  isset($cod_compra) ? $cod_compra : ''?>" type="" name="" onclick="showFrmCompras(this);">
            <i class="icon-ok"></i>
           continuar sin abrir
        </button>
    </div>    
</div>
<?php if (isset($ejecutar_script) && $ejecutar_script <> ''){ ?> 
    <input type="hidden" name="ejecutar_script" value="<?php echo $ejecutar_script ?>">
<?php } ?>