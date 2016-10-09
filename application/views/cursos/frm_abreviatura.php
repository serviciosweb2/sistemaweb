
<?php



$idioma='nombre_'.get_idioma();
//echo '<pre>'; 
//print_r($abreviaturaCurso);
//echo '</pre>';

?>
<script>
    var langFrm = <?php echo $langFrm?>;
</script>
<script src="<?php echo base_url('assents/js/cursos/frm_abreviatura.js')?>"></script>
<div class="modal-content">
        <div class="modal-header">
<!--                <button type="button" class="close" data-dismiss="modal">&times;</button>-->
                <h4 class="blue bigger"><?php echo lang('modificar_abreviatura'); ?><small><i class="icon-double-angle-right"></i>  <?php echo $curso->$idioma?></small></h4>
        </div>

        <div class="modal-body overflow-visible">
            
            <form id="frmAbreviatura">
                <div class="row">
                <input  type="hidden" name="cod_curso_habilitado" value="<?php echo count($abreviaturaCurso)==0 ? '' : $abreviaturaCurso[0]['cod_curso']?>">
                <div class="form-group col-md-12 col-xs-12">
                    <label><?php echo lang('abreviatura_curso'); ?></label>
                    <input class="form-control"  name="abreviatura" value="<?php echo count($abreviaturaCurso)==0 ? '' : $abreviaturaCurso[0]['abreviatura']?>">
                </div>
                </div>
            </form>
         
        </div>

        <div class="modal-footer">
<!--                <button class="btn btn-sm" data-dismiss="modal">
                        <i class="icon-remove"></i>
                        Cancel
                </button>-->

                <button id="submit" class="btn btn-sm btn-primary">
                        <i class="icon-ok"></i>
                        <?php echo lang('guardar'); ?>
                </button>
        </div>
										</div>