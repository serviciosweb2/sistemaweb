<script src="<?php echo base_url() ?>assents/js/jquery.validate.min.js"></script>
<script src="<?php echo base_url('assents/js/librerias/ajaxchosen/lib/ajax-chosen.js') ?>"></script>
<script>
    var matricula = <?php echo $matricula?>
</script>
<script src="<?php echo base_url('assents/theme/assets/js/jquery.maskedinput.min.js') ?>"></script>
<div class='modal-content'>
    <form id='habilitarRematriculacion' action='rematriculaciones/habilitar' method='post'>
        <input type='hidden' name='matricula' value='<?php echo $matricula; ?>'/>
        <input type='hidden' name='fechaDesde' value='<?php echo $fechaDesde; ?>'/>
        <input type='hidden' name='fechaHasta' value='<?php echo $fechaHasta; ?>'/>
        <input type='hidden' name='cod_comision' value='<?php echo $cod_comision; ?>'/>
        <input type='hidden' name='cod_curso' value='<?php echo $cod_curso; ?>'/>
        <input type='hidden' name='tipo' value='<?php echo $tipo; ?>'/>
        <div class="modal-header">
        <?php
        if($tipo == "Habilitar"){
        ?>
            <h4 class="blue"><?php echo lang('habilitar_rematriculacion') ?></h4>
        <?php
        } else {
        ?>
            <h4 class="blue"><?php echo lang('deshabilitar_rematriculacion') ?></h4>
        <?php
        }
        ?>
        </div>
        <div class="modal-body" >
                <div class="row no-padding-top">
                    <div class="col-md-10 form-group no-padding-right">
                        <div class="blue bigger-110" id="obs">
                            <?php echo lang('observaciones') . ' '; ?>
                        </div>
                    </div>
                    <div class="col-md-10 form-group no-padding-bottom">
                        <textarea name="observaciones" id="ob" class="form-control pull-left" maxlength="511" style="resize: none;"></textarea>
                    </div>
                </div>
        </div>

    </form>
    <div class="modal-footer">
        <button class="btn  btn-success" id="submitHabilitacion">
            <i class="icon-ok bigger-110"></i>

        <?php
        if($tipo == "Habilitar"){
        ?>
            <?php echo lang('habilitar_rematriculacion'); ?>
        <?php
        } else {
        ?>
            <?php echo lang('deshabilitar_rematriculacion'); ?>
        <?php
        }
        ?>
        </button>
    </div>
</div>
<script>
//Era solamente este JavaScript, no me parecio serio embeberlo
$('#submitHabilitacion').click(function(){
  $.ajax({  
    type: "POST",  
    url: BASE_URL + "rematriculaciones/habilitar",  
    data: $('#habilitarRematriculacion').serialize(),  
    dataType: 'JSON',
    success: function(response) {  
        if(typeof response.success !== undefined){
            console.log('èxito!');
            $.gritter.add({
                title: lang.BIEN,
                text: lang.validacion_ok,
                sticky: false,
                time: '3000',
                class_name: 'gritter-success'

            });
            window.habilitacionok = "si";
        }
        $.fancybox.close();
    }  
  });
});
</script>
