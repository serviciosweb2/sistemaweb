<div id="detalle_talle_alumno"  data-width="50%" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3><?php echo lang("detalle"); ?></h3>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-4">
                <strong>ID &nbsp;</strong><br>
                <?php echo $ticket['seguimiento'] ?>
            </div>
            <div class="col-md-4">
                <strong><?php echo lang('fecha'); ?>&nbsp;</strong><br>
                <?php echo formatearFecha_pais($ticket['fecha']); ?>
            </div>            
            <div class="col-md-4">
                <strong><?php echo lang("usuario"); ?></strong><br>
                <?php echo $usuario ?>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <strong><?php echo lang("asunto"); ?>&nbsp;</strong>
                <?php echo $ticket['nombre'] ?>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <strong><?php echo lang("descripcion"); ?></strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php echo str_replace("\n", "<br>", $ticket['descripcion']) ?>
            </div>
        </div>        
    </div>
    <div class="modal-footer"></div>