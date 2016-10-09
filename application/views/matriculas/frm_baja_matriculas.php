<script src="<?php echo base_url('assents/js/impresiones.js') ?>"></script>
<script src="<?php echo base_url('assents/js/matriculas/frm_baja_matriculas.js') ?>"></script>
<style>
    
.batman 
{
    max-height: 70px!important;   
}
    
</style>

<div class="modal-content">
    <div class="modal-header">
        <h3 class="blue bigger"><?php echo lang('INHABILITAR'); ?>
            <small>
                <i class="icon-double-angle-right"></i>
                <?php echo $nombreAlumno ?>
            </small>
        </h3>
    </div>
    <div class="modal-body overflow-visible">
        <form class="form-line" id="frm-baja" role="form">

            <input  name="cod_alumno" type="hidden" value="<?php echo  $cod_alumno ?>"/>
            <div class="row">
                <div class="col-md-12  form-group">
                    <div  class="row">
                        <div class="col-md-12">
                            <label for="form-field-9"><?php echo lang('seleccione_periodo_baja');?></label> 
                        </div>
                    </div>


                    <div class="row">
                        <?php 
                        foreach ($matriculas_periodos as $mp) { ?>


                            <div class="col-md-4">

                                <label>
                                    <input name="cod_matriculas_periodos[]" type="checkbox" class="ace" value="<?php echo $mp["cod_matricula_periodo"] ?>" checked <?php echo $mp["estado"] === "habilitada" ? "" : "disabled" ?>>
                                    <span class="lbl"><?php echo $mp["nombre"] ?><?php echo $mp["estado"] === "habilitada" ? "" : "(" . lang($mp["estado"]) . ")" ?></span>
                                </label>


                            </div>

<?php } ?>
                    </div>



                    <div>



                    </div>
                    <div class="row">
                        <div class="col-md-12">
                   











                            <div class="form-group">
                                <label class=" control-label" for="motivo"><?php echo lang('motivo'); ?></label>
                                <div>
                                    <select class="width-80 chosen-select batman" id="motivo" name="motivo" data-placeholder="Seleccione Motivo">
                                        <?php
                                        foreach ($motivos as $rowmotivo) {
                                            echo " <option value='" . $rowmotivo["id"] . "'>" . $rowmotivo["motivo"] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" >
                                <label class=" control-label" for="motivo"><?php echo lang('observaciones'); ?></label>
                                <div>
                                    <textarea class="form-control limited" id="form-field-9" maxlength="150" rows="4" name="comentario" ></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger"  id="btn-baja" type="submit">
                        <i class="fa fa-arrow-downbigger-110"></i>
<?php echo lang('INHABILITAR'); ?>
                    </button>
                </div>
            </div>
            
            <script>
                $('.chosen-results').addClass("batman");
            </script>            