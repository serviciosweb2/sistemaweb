<script src="<?php echo base_url("assents/js/horarios/frm_excepciones.js")?>"></script>
<div class="modal-content" >
    

        <div class="modal-header">
            <h3 class="blue bigger"><?php echo lang('cambiar_horario'); ?></h3> 
        </div>
    <div class="modal-body overflow-visible" >
        <form id="frm-excepciones">
            
            
            <input type="hidden" value="<?php echo $codigo_horario ?>"   id="codigo_horario"/>
      
        
        
  

<div class="row">
  
    <div class="col-md-12 form-group">
        <label><?php echo lang('seleccionar_alumnos');?></label>
        <select multiple="" data-placeholder="<?php echo lang("seleccionar_alumnos"); ?>" class="chosen-select  tag-input-style" name="cod_matricula_horario[]">
    <?php foreach ($alumnos as $alumno) { ?>
    <option   value="<?php echo  $alumno["cod_matricula_horario"] ?>"  <?php echo $alumno["selected"] ==true ? "selected" : "" ?>><?php  echo $alumno["nombreapellido"] ?> </option>

 <?php } ?>
    
    
</select>   
        
        
        
    </div>

    
    
    
</div>




         
            <table id="tablaHorariosCambiar" class="table table-striped table-bordered table-hover">
    <thead>
        <tr>
            <th></th>
            <th ><?php echo lang('fecha'); ?></th>
            <th ><?php echo lang('horarios'); ?></th>
            <th ><?php echo lang('comision'); ?></th>
      
        </tr>
    </thead>
    
        <tbody>
 
                    </tbody>
    
</table>

  </form>
        
  </div> 
 <div class="modal-footer">
            <button class="btn  btn-success" id="enviar-excepciones" type="submit"  value="enviar">
                <i class="icon-ok bigger-110"></i>
                <?php echo lang('guardar'); ?>
            </button>


        </div>  

</div>






