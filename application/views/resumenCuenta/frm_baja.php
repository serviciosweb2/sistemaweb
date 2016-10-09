<script>
    var langFRM;
    $('.fancybox-wrap').ready(function() {

        var clavesFRM = Array("validacion_ok", "BIEN");

        var langFRM = '';

        $.ajax({
            url: BASE_URL + 'entorno/getLang',
            data: "claves=" + JSON.stringify(clavesFRM),
            type: "POST",
            dataType: "JSON",
            async: false,
            cache: false,
            success: function(respuesta) {
                langFRM = respuesta;
                initFRM();
            }
        });

        function initFRM() {

            $('#frmBaja select').chosen({
                width: '100%'
            });

            $('.modal-content').on('submit', '#frmBaja', function() {
                var dataPOST = $(this).serializeArray();
                $.ajax({
                    url: BASE_URL + 'ctacte/baja',
                    data: dataPOST,
                    type: 'POST',
                    cache: false,
                    dataType: 'json',
                    success: function(respuesta) {

                        if (respuesta.codigo == 0) {
                            $.gritter.add({
                                text: respuesta.msgerror,
                                image: '',
                                sticky: false,
                                time: '3000',
                                class_name: 'gritter-error'
                            });

                        } else {

                            $.fancybox.close();
                            oTable.fnDraw();
                            $.gritter.add({
                                title: langFRM.BIEN,
                                text: langFRM.validacion_ok,
                                sticky: false,
                                time: '3000',
                                class_name: 'gritter-success'
                            });

                            oTableDetalle.row(commentFile).remove().draw();
                        }


                    }

                });
                return false;
            });
        }
    });

    $('.fancybox-wrap').ready(function() {

    });
</script>

<!--VISTA-->
<div class="modal-content" >
    <form id='frmBaja'> 
        <input name="codigo" value="<?php echo $cod_ctacte; ?>" type="hidden" />
        <div class="modal-header">
            <h3 class="blue bigger"><?php echo lang('baja_ctacte'); ?><small>
                    <i class="icon-double-angle-right"></i>
                    <?php echo $descripcion ?>
                </small></h3> 
        </div>

        <div class="modal-body overflow-visible" >

            <form id="frmBaja">

                <div class="row" >
                    <div class="form-group col-md-12">
                        <label><?php echo lang('motivo'); ?></label>
                        <select class="form-control"  name="motivo" data-placeholder="<?php echo lang('motivo'); ?>">
                            <option></option>
                            <?php
                            foreach ($motivos as $motivo) {
                                echo '<option value=' . $motivo['id'] . '>' . $motivo['motivo'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="row">

                    <div class="form-group col-md-12">
                        <label ><?php echo lang('comentario'); ?></label>
                        <textarea class="form-control" name="comentario"></textarea>
                    </div>

                </div>

        </div>

        <div class="modal-footer">
            <button type="submit" class="btn  btn-success" id="btn-guardar">
                <?php echo lang('guardar'); ?>
                <i class="icon-arrow-right icon-on-right bigger-110"></i>
            </button>						


        </div>
    </form>
</div>