<div class="col-md-12 col-xs-12" style="margin-top: 40px;">
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <?php echo lang("comentarios") ?>
        </div>
    </div>
</div>
<div class="col-md-12 col-xs-12">
    <div id="areaTablacomentarios">
        <table id="tbl_cupones_landing_comentarios" cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered table-condensed " 
               oncontextmenu="return false" onkeydown="return false" style="width:100% !important;">
            <thead>
                <tr>
                    <th><?php echo lang("fecha") ?></th>
                    <th><?php echo lang("usuario") ?></th>
                    <th><?php echo lang("comentario"); ?></th>
                </tr>
            </thead>
            <?php foreach ($arrComentarios as $comentario){ ?> 
            <tr>
                <td><?php echo formatearFecha_pais($comentario['fecha']); ?></td>
                <td><?php echo $comentario['usuario_nombre'] ?></td>
                <td><?php echo $comentario['comentario'] ?></td>
            </tr>                
            <?php } ?>
        </table>
    </div>
</div>
<div class="col-md-12 col-xs-12" style="margin-bottom: 20px;">
    <div class="row" style="margin-top: 10px;">
        <div class="col-md-12 col-xs-12">            
            <textarea name="comentario" style="width: 666px; height: 72px; resize: none;"></textarea>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-xs-12" style="margin-top: 10px;">
            <center>
                <button class="btn btn-sm btn-primary" type="button" name="btn_guardar" onclick="guardar_comentario(<?php echo $id_cupon; ?>);">
                    <i class="icon-ok"></i>
                    <?php echo lang("guardar") ?>
                </button>
            </center>
        </div>
    </div>
</div>
<script>
    $("#tbl_cupones_landing_comentarios").dataTable({
        aLengthMenu: [[5, 10, 25], [5, 10, 25]],
        bFilter: true,
        bLengthChange: false,
        iDisplayLength: 5
    });
</script>