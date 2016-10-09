<script>

    var columnas = '<?php echo $columnas ?>';
    var langFrm = <?php echo $langFrm ?>;
    
//    console.log('LANGS',langFrm);

</script>
<script src="<?php echo base_url('assents/js/resumendecuenta/frm_avisoDeudores.js') ?>"></script>

<?php
$test = array();

foreach ($test as $k => $a) {

    echo $a;
}

function getBody($registros) {
    $verDetalle = lang('ver_detalle');
    foreach ($registros as $registro) {
        $disabled = $registro['alertar'] == 1 ? '' : 'disabled';
        $desabilitar = $registro['tienemail'] == '1' ? '' : 'disabled';
        $valores = json_encode($registro);

        echo <<<eot
<tr>
    <td><div class="checkbox" $desabilitar><label><input name="ctacte[]" class="ace ace-checkbox-2" type="checkbox" value='$valores' $disabled><span class="lbl"></span></label></div></td>
    <td>$registro[nombre]</td>
    <td>$registro[apellido]</td>
    <td>$registro[deudaTotal]</td>
    <td>$registro[fechavenc]</td>
    <td><button class="btn btn-info btn-xs boton-primario botonDetalle" value="$registro[cod_alumno]" ><i></i>$verDetalle</button></td>
</tr>
eot;
    }
}
?>

<div id="detalleAvisoDeudores" class="modal fade" data-width="80%" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-header">

        <h4 class="blue bigger"><?php echo lang('detalle'); ?><small></small></h4>
    </div>

    <div class="modal-body">

        <div class="row" id="contenido">

        </div>


        <div class="row">

            <div class="table-responsive contenedorTabla">

            </div>

        </div>
    </div>
    <div class="modal-footer">
        <!--    <button type="button" data-dismiss="modal" class="btn">Cancelar</button>-->
        <button type="button" data-dismiss="modal" id="btn-ok-cambio-estado" class="btn btn-primary"><?php echo lang('continuar'); ?></button>
    </div>
</div>




<div class="modal-content">
    <div class="modal-header">

        <h4 class="blue bigger"><?php echo lang('enviar_aviso_ctacte'); ?>
            <small><i class="icon-double-angle-right"></i> 
<?php echo lang('selecciones_quien_avisar'); ?>
            </small>
        </h4>
    </div>

    <div class="modal-body">
        <div class="row">

            <table id="tablaAvisoDeudores" class="table table-bordered table-condensed" style="width:100%!important;">
                <thead>

                </thead>
                <tbody>

                </tbody>
            </table>

        </div>    




    </div>

    <div class="modal-footer">


        <button class="btn btn-sm btn-primary" name="enviarAvisos">
            <i class="icon-ok"></i>
<?php echo lang('enviar'); ?>
        </button>
    </div>
</div>

