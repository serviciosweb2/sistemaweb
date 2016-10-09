<!--VISTA-->
<script>
    var langFrm = <?php echo $langFrm?>;
</script>
<script src="<?php echo base_url("assents/js/horarios/frm_inscriptos_horario.js") ?>"></script>





<div class="modal-content" >
       <?php if(count($inscriptos) > 0){?>
        <?php // print_r($inscriptos)?>
        <div class="modal-header">
            <h3 class="blue bigger"><?php echo lang('inscriptos');?> </h3>
            <small><?php echo $horario["nombre"] ?>   - <?php echo $horario["materia"] ?> - <?php echo $horario["horario"] ?>(<?php echo $horario["dia"] ?>)</small>
        </div>
        
    
        
    
    
      <div id="id-message-item-navbar" class="message-navbar align-center clearfix">
                    <div class="message-bar">
                        <div class="message-toolbar">
                            <div class="inline position-relative align-left">
                                <a href="#" class="btn-message btn btn-xs ">
                                   
                                      <i class="icon-mail-forward green"></i>
                                            &nbsp; <?php echo lang('cambiar_horario');?>
                                            
                                            
                                            
                                  

                                   
                                </a>

                                
                            </div>

                         

                     
                        </div>
                    </div>

                    <div>
                   

             
                    </div>
                </div>
        <div class="modal-body overflow-visible" >
                <form id="inscriptos">
          
                <input name="codigo_horario" type="hidden" value="<?php echo $horario["codigo"] ?>" />
                <table id="tablaInscriptos" class="table table-striped table-bordered table-hover">
                    <thead>     <th></th>
                        <th><?php echo lang('Alumno');?></th>
                        <th><?php echo lang('comision');?></th>
                        <th><?php echo lang('asistencias');?></th>
                    </thead></tbody>
                    <?php foreach ($inscriptos as $inscripto) { ?>
                        <tr>
                            <td><label class="inline">
                                    <input type="checkbox" value="<?php echo $inscripto["cod_matricula_horario"] ?>" name="cod_matricula_horario[]" class="ace" <?php echo $inscripto["estado"] != "" ? "disabled" : "" ?> >
                                    <span class="lbl"></span>
                                </label>
                            </td>

                            <td><?php echo $inscripto["nombreapellido"] ?></td>
                            <td><?php echo $inscripto["comisionalumno"] ?></td>
                            <td><select id="asistencia" class="asistencia" data-placeholder="<?php echo lang('seleccione_asistecia');?>"   matricula-inscripcion="<?php echo $inscripto["cod_matricula_horario"] ?>">
                                    <option value=""></option>
                                        <?php foreach ($arrAsistencias as $asistencia) { ?>
                                             
                                        <option value="<?php echo $asistencia["id"] ?> " <?php echo $inscripto["estado"] ==  $asistencia["id"] ? "selected" : "" ?>  ><?php echo $asistencia["nombre"] ?> </option>
                                        
                                          <?php   }  ?>
                                        
                                        
                          
                                <?php echo lang($inscripto["estado"]) ?>
                                
                                </select></td>
                        </tr>
                    <?php } ?>

                </tbody>
                </table>


            </form>


        </div>


   <?php }else{?>
    
    <div class="row">
        
         <div class="col-md-12">
       <div class="alert alert-info">
											<button type="button" class="close" data-dismiss="alert">
												<i class="icon-remove"></i>
											</button>
											<strong><?php echo lang('alumnos_horarios');?></strong><br/>
<?php echo lang('alerta_necesita_atencion');?>
											<br>
										</div> 
        
        
        
        
    </div>
      </div>
   <?php }?>
</div>



