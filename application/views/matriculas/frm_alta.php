<script src="<?php echo base_url('assents/js/matriculas/frm_alta.js');?>"></script>


<div class="modal-content">

    
    <div class="modal-header">
        <h3 class="blue bigger"><?php echo lang('habilitar_matriculas');?><small>
									<i class="icon-double-angle-right"></i>
									<?php  echo $nombreAlumno ?>
								</small></h3> 
    </div>
    
    <div class="modal-body overflow-visible">
        <form id='frm-alta'>
            <input  type="hidden" value="<?php echo $periodos[0][0] ?>" name="periodo"/>
            <input type="hidden" value="<?php echo $cod_alumno ?>" name="cod_alumno"/>
            <input name="cod_plan_academico"  type="hidden" value="<?php echo $cod_plan_academico ?>"/>
            
            
            
        </form> 
        <div class="row">
            <div class=" form-group col-md-12">
                <select class="form-control" disabled="disabled">
       
                    <?php foreach ($periodos as $periodo) {
                        $select = "";
                        if($tipoPeriodo !=-1){
                      $select =   $periodo[0] == $tipoPeriodo ? "selected" : "";
                        }
                        ?>
                    <option value="<?php echo $periodo[0] ?>"<?php echo $select ?>><?php echo $periodo[1] ?></option>
                    <?php } ?>
                </select>  
                        
            </div>
                       </div>
            <div class="row">
   
                <div class="col-md-12">
                    <div class="table-responsive">
                        
                        <table class="table table-striped table-bordered table-hover">
                            <tr><th><?php echo lang('descripcion');?></th><th><?php echo lang('fecha_vencimiento');?></th><th><?php echo lang('importe');?></th><th><?php echo lang('saldo');?></th></tr>
                        <?php                                          foreach ($ctacte as $ct) { ?>

                        <tr>
                            
                             <td><?php echo lang( $ct["descripcion"]) ?></td>
                            <td><?php echo  $ct["fechavenc"] ?></td>
                            <td><?php echo  $ct["importe"] ?></td>
                        <td><?php echo  $ct["saldo"] ?></td>
                        </tr>
                                                <?php } ?>
                        
                    </table>  
                        
                    </div>
                  
                    
                    
                </div>
                </div>
        
        
        
        
        
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

