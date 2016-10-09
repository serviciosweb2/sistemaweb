<script src="<?php echo base_url('assents/theme/assets/js/jquery.slimscroll.min.js')?>"></script>
<script>    
    var DATACOMENTARIOS = JSON.parse('<?php echo json_encode($comentarios)?>');
    var langFrm = <?php echo $langFrm ?>;
    var idioma = '<?php echo $idioma?>';
</script>
<script src="<?php echo base_url('assents/js/matriculas/frm_comentarios_matriculas.js')?>"></script>
<style>    
    .fotoMSJ{
        padding-top: 10.8% !important;
    }
    .textoMSJ{    
        color: #c8c8c8;
        font-size: 20pt ;
    }
</style>
<div class="widget-box ">
    <div class="widget-header">
        <h4 class="lighter smaller">
            <i class="icon-comment blue"></i>
            <?php echo lang('agregar_comentario')?>
        </h4>
    </div>
    <div class="widget-body">
        <div class="widget-main no-padding">                      
            <div class="dialogs"></div>
            <form id="nuevo_comentario">
                <div class="form-actions">
                    <input type="hidden" value="<?php echo $cod_alumno;?>" name="cod_alumno">
                    <input type="hidden" value="<?php echo $cod_plan_academico;?>" name="cod_plan_academico">
                    <div type="hidden" class="input-group">
                        <input placeholder="<?php echo lang('agregar_comentario')?>" type="text" class="form-control" name="comentario">
                        <span class="input-group-btn">
                            <button class="btn btn-sm btn-info no-radius" type="submit">
                                <i class="icon-share-alt"></i>
                                <?php echo lang('enviar')?>
                            </button>
                        </span>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>