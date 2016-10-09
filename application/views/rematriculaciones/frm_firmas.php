<link rel="stylesheet" href="<?php echo base_url('assents/css/comisiones/frm_comisiones.css')?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/theme/assets/css/jquery.gritter.css')?>"/>

<script src="<?php echo base_url('assents/js/chosen.jquery.js')?>"></script>
<script src="<?php echo base_url('assents/js/jquery.validate.min.js')?>"></script>
<script src="<?php echo base_url('assents/js/librerias/ajaxchosen/lib/ajax-chosen.js') ?>"></script>
<script src="<?php echo base_url("assents/js/rematriculaciones/frm_firmas.js")?>"></script>
<script src="<?php echo base_url('assents/theme/assets/js/jquery.gritter.min.js')?>"></script>

<script>
    $(document).ready(function(){
        $(".chosen-select").chosen();
    });
</script>

<style>

    span .btn-default {
        color: #333 !important;
        background-color: #fff  !important;
        border-color: #ccc  !important;
    }

    span .btn {
        color: #858585 !important;
        margin-bottom: 0;
        font-weight: 400;
        text-align: center;
        vertical-align: middle;
        cursor: pointer;
        background-image: none;
        border: 1px solid #d5d5d5 !important;
        white-space: nowrap;
        padding: 6px 12px;
        font-size: 14px;
        line-height: 1.42857143;
        border-radius: 0px !important;
        -webkit-user-select: none;
    }

    span .btn:hover {
        background: none !important;
        cursor:text;
    }

    .chosen-container .chosen-results{
        max-height: 70px !important;
    }

    .selector{
        width:100%;
    }
</style>

<div class="modal-content" >
    <form id='nuevaComision'>
        <div class="modal-header">
            <h4 class="blue"><?php echo lang('frm_nuevaFirma') ?></h4>
        </div>
        <div class="modal-body" >
            <div class="row">
                <div class="form-group col-md-6" role="form">
                    <label  for="nombre_alumno_firma"><?php echo lang('nombre_y_apellido') ?> </label>
                    <div>
                        <select  class="chosen-select" tabindex="10" name="nombre_alumno_firma" id="nombre_alumno" data-placeholder=<?php echo lang('buscar') ?> >
                            <option></option>
                        </select>
                    </div>
                </div>
                <div class="form-group col-md-6" role="form">
                    <label  for="matricula"><?php echo lang('matricula') ?> </label>
                    <div>
                        <select class="selector" name="matricula_firma" id="matricula">
                            <option></option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-2" role="form">
                    <label  for="anio_firma"><?php echo lang('año') ?> </label>
                    <div>
                        <select  class="selector form-control" name="anio_firma" id="anio">
                            <option value="1">1</option>
                            <option value="2">2</option>
                        </select>
                    </div>
                </div>
                <div class="form-group col-md-6" role="form">
                    <label  for="trimestre_firma"><?php echo lang('trimestre') ?> </label>
                    <div>
                        <select class="selector form-control" name="trimestre_firma" id="trimestre">
                            <option value="1">1º Trimestre (Janeiro - Março)</option>
                            <option value="2">2º Trimestre (Abril - Junho)</option>
                            <option value="3">3º Trimestre (Julho - Setembro)</option>
                            <option value="4">4º Trimestre (Outubro - Dezembro)</option>

                        </select>
                    </div>
                </div>
                <div class="form-group col-md-4" role="form">
                    <label for="firmo_firma"><?php echo lang('firmo') ?> </label>
                    <div>
                        <select class="selector form-control" name="firmo_firma" id="firmo">
                            <option value="si"><?php echo lang('SI')?></option>
                            <option value="no"><?php echo lang('NO')?></option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-success" id="guardarNuevaFirma" type="submit" value="guardar">
                <i class="icon-ok bigger-110"></i>
                <?php echo lang('guardar'); ?>
            </button>
        </div>
    </form>
</div>
