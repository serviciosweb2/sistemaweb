<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-select.min.css') ?>"/>
<script src="<?php echo base_url() ?>assents/js/bootstrap-select.min.js"></script>

<script>
    var cod_nc_confirmar = '<?php echo $cod_nc; ?>';
    var lang_confirmar = '';
    var claves = Array("validacion_ok", "BIEN", "ERROR");

    $('.fancybox-wrap').ready(function() {

        $.ajax({
            url: BASE_URL + 'entorno/getLang',
            data: "claves=" + JSON.stringify(claves),
            dataType: 'JSON',
            type: 'POST',
            cache: false,
            async: false,
            success: function(respuesta) {
                lang_confirmar = respuesta;
            }
        });

        $('select').chosen({
            width: '100%'
        });


        $('button[name="enviar"]').on('click', function() {

            $('#confirmar').submit();

            $.ajax({
                url: BASE_URL + 'notascredito/confirmar',
                type: 'POST',
                data: 'codigo=' + cod_nc_confirmar,
                cache: false,
                dataType: 'json',
                success: function(respuesta) {

                    if (respuesta || respuesta.codigo == 1) {
                        $.gritter.add({
                            title: lang_confirmar.BIEN,
                            text: lang_confirmar.validacion_ok,
                            sticky: false,
                            time: '3000',
                            class_name: 'gritter-success'
                        });
                        $.fancybox.close(true);
                        oTable.fnDraw();

                    } else {

                        $.gritter.add({
                            title: lang_confirmar.ERROR,
                            text: respuesta.errors,
                            sticky: false,
                            time: '3000',
                            class_name: 'gritter-error'
                        });

                    }
                    return false;
                }
            });
        });
    });
</script>

<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="blue bigger"><?php echo lang("confirmar_nc"); ?><small><i class="icon-double-angle-right"></i>  <?php echo $cod_nc . ', ' . $alumno ?></small></h4>
    </div>
    <div class="modal-body overflow-visible">
        <form  id="confirmacion">

            <div class="row">
                <div class="form-group col-xs-12">
                    <label for="exampleInputPassword1"><?php echo lang("esta_seguro_que_desea_confirmar_nc"); ?></label>

                </div>
            </div>
        </form> 
    </div>
    <div class="modal-footer">
        <button class="btn btn-sm btn-primary" name="enviar">
            <i class="icon-ok"></i>
            <?php echo lang("guardar"); ?>
        </button>
    </div>
</div>