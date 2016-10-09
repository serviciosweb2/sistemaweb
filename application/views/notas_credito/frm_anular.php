<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-select.min.css') ?>"/>
<script src="<?php echo base_url() ?>assents/js/bootstrap-select.min.js"></script>
<script>
    var claves = Array("nc_anulada_correctamente", "BIEN", "ERROR");
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

        $('button[type="submit"]').on('click', function() {
            $('#baja').submit();
        });

        $('button[name="enviar"]').on('click', function() {
            $('#baja').submit();
            return false;
        });

        $('#baja').on('submit', function() {
            var dataPOST = $(this).serialize();
            $.ajax({
                url: BASE_URL + 'notascredito/anular',
                type: 'POST',
                data: dataPOST,
                cache: false,
                dataType: 'json',
                success: function(respuesta) {

                    if (respuesta.codigo == '1') {
                        $.gritter.add({
                            title: lang_confirmar.BIEN,
                            text: lang_confirmar.nc_anulada_correctamente,
                            sticky: false,
                            time: '3000',
                            class_name: 'gritter-success'
                        });
                        $.fancybox.close(true);
                        oTable.fnDraw();

//                        gritter(lang.validacion_ok,true);
//                        
//                        oTable.fnDraw();
//                        
//                        setTimeout( function(){ $.fancybox.close(true); }, 1500 );

                    } else {

                        $.gritter.add({
                            title: lang_confirmar.ERROR,
                            text: respuesta.errors,
                            sticky: false,
                            time: '3000',
                            class_name: 'gritter-error'
                        });

                    }
                }
            });
            return false;
        });
    });
</script>

<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="blue bigger"><?php echo lang("anular_nc"); ?><small><i class="icon-double-angle-right"></i>  <?php echo $cod_nc . ', ' . $alumno ?></small></h4>
    </div>
    <div class="modal-body overflow-visible">
        <form  id="baja">
            <div class="row">
                <input type="hidden" name="codigo" value="<?php echo $cod_nc ?>">

                <div class="form-group col-xs-6">
                    <label ><?php echo lang('motivo'); ?></label>
                    <select  class="form-control" id="exampleInputEmail1" name="motivo" data-placeholder="<?php echo lang('seleccione_motivo'); ?>">
                        <option></option>
                        <?php foreach ($movitos as $motivoValor) { ?>
                            <option value='<?php echo $motivoValor["id"] ?>'>
                                <?php echo $motivoValor["motivo"]; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>        
            </div>
            <div class="row">
                <div class="form-group col-xs-12">
                    <label for="exampleInputPassword1"><?php echo lang("comentario"); ?></label>
                    <textarea  class="form-control" name="comentario" id="exampleInputPassword1" placeholder="<?php echo lang('comentario'); ?>"></textarea>
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