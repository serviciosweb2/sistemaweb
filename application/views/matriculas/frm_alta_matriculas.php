<script src="<?php echo base_url('assents/js/matriculas/frm_alta_matriculas.js');?>"></script>


<div class="modal-content">

   
    <div class="modal-header">
        <h3 class="blue bigger"><?php echo lang('habilitar_matriculas');?><small>
                <i class="icon-double-angle-right"></i>
                <?php  echo $nombreAlumno ?>
            </small></h3> 
    </div>
    
    <div class="modal-body overflow-visible">
        <form class="form-line" id="frm-alta" role="form">
            <input  name="cod_alumno" type="hidden" value="<?php echo  $cod_alumno ?>"/>
            <div class="row">
                <div class="col-md-12  form-group">
                    <div  class="row">
                        <div class="col-md-12">
                            <label for="form-field-9"><?php echo lang('seleccione_periodo_alta');?></label> 
                        </div>
                    </div>
                        <div class="row">
                        <?php 
                        foreach ($matriculas_alta as $mat_periodo) { ?>


                            <div class="col-md-4">

                                <label>
                                    <input name="cod_matriculas_periodos[]" type="checkbox" class="ace" value="<?php echo $mat_periodo["cod_matricula_periodo"] ?>" checked <?php echo $mat_periodo["estado"] === "inhabilitada" ? "" : "disabled" ?>>
                                    <span class="lbl"><?php echo $mat_periodo["nombre"] ?><?php echo $mat_periodo["estado"] === "inhabilitada" ? "" : "(" . lang($mat_periodo["estado"]) . ")" ?></span>
                                </label>


                            </div>

                    <?php } ?>
                    </div>
                    <div class="form-group" >
                                <label class=" control-label" for="motivo"><?php echo lang('matricula_alta'); ?></label>
                                <div>
                                    <textarea class="form-control limited" id="form-field-9" maxlength="50" name="comentario" ></textarea>
                                </div>
                            </div>
            
            
        </form> 
 
      
    </div>

    <div class="alert alert-danger hide"></div>
          <div class="modal-footer">             
	    <button class="btn btn-success"  id="btn-alta" type="submit">
            <i class="fa fa-arrow-downbigger-110"></i>
           <?php echo lang('HABILITAR');?>
            </button>
											</div>
        
        
    </form>
      
    
    
</div>

