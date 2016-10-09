<div class="modal-header">
    <h3 class="blue bigger"><?php echo lang("confirmar_envio_de_facturas"); ?></h3>
</div>

<div class="modal-body overflow-visible">
    <h5><?php echo lang("alumnos"); ?></h5>
    <table style="margin-left: 16px;">
        <?php foreach ($arrFacturasEnviar as $factura){ ?>
        <tr>
            <td>
                
                <?php echo $factura['razon_social'] ; ?>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>


<div class="modal-footer">
    <button id="btn-facturar" class="btn btn-danger" value="enviar" type="button" onclick="cancelarEnvioFacturas();"><?php echo lang("cancelar"); ?></button>
    <button id="btn-facturar" class="btn btn-success" value="enviar" type="button" onclick="enviarFacturas();"><?php echo lang("enviar"); ?></button>
</div>

<?php
//print_r($arrFacturasEnviar);