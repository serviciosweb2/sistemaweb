<script>
    var langFrm = <?php echo $langFrm?>;
</script>
<script src="<?php echo base_url('assents/js/examenes/frm_cambiar_estado_examen.js')?>"></script>

<div class="modal-content">
     <form>
    <div class="modal-header">
            
            <h4 class="blue bigger"><?php echo lang('INHABILITAR'); ?><small><i class="icon-double-angle-right"></i>  <?php echo$cod_examen.', '.$nombre_materia ?></small></h4>
    </div>

    <div class="modal-body overflow-visible">
            <div class="row">
                
                 
            <div class="form-group col-md-12">
                <label>

                     <?php echo lang('motivo'); ?> 

                </label>
                
                    <select name="motivo" id="form-field-1"  data-placeholder="<?php echo lang('seleccione_motivo');?>">
                        <option value=""></option>
                        <?php 
                        
                        foreach($motivos as $motivo){
                            echo '<option value="'.$motivo['id'].'">'.$motivo['motivo'].'</option>';
                        }
                        ?>
                    </select>
                
            </div>
            
            </div>
        <div class="row">
           
            <div class="form-group col-md-12">
                <label ><?php echo lang('comentario'); ?></label>
               
                    <textarea name="comentario" class="form-control" id="form-control" placeholder='<?php echo lang('comentario');?>'></textarea>
                    
                
            </div>
            
            
            
            <input class="form-control" name='codigo' type='hidden' value='<?php echo $cod_examen?>'>
      
        

            </div>
    </div>

    <div class="modal-footer">
            

            <button class="btn btn-sm btn-primary" type="submit">
                    <i class="icon-ok"></i>
                    <?php echo lang('guardar')?>
            </button>
    </div>
    </form>
</div>
    

<!--<div class="page-content">
    <div class="page-header">

        <h1><?php echo lang('cambiar-estado-examen'); ?>
            <small><i class="icon-double-angle-right"></i>Common form elements and layouts</small>
        </h1>

    </div>

<div class="row">

    <div class="col-xs-12">
        

         PAGE CONTENT BEGINS 

        
        <form class="form-horizontal" role="form">
            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1">

                     <?php echo lang('motivo'); ?> 

                </label>
                <div class="col-sm-9">
                    <select name="motivo" id="form-field-1" class="chosen-select width-80" type="text" data-placeholder="Seleccione motivo">
                        <option value=""></option>
                        <?php 
                        
                        foreach($motivos as $motivo){
                            echo '<option value="'.$motivo['id'].'">'.$motivo['motivo'].'</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="space-4"></div>
            
            <div class="space-4"></div>
            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-input-readonly"><?php echo lang('comentario'); ?></label>
                <div class="col-sm-9">
                    <textarea name="comentario" id="form-control" class="col-xs-10 col-md-7" type="text" placeholder='comentario...'></textarea>
                    
                </div>
            </div>
            
            
            
            <input class="form-control" name='codigo' type='hidden' value='<?php echo $cod_examen?>'>
      
        </form>
    </div>
</div>
  

    



<div class="row">
        <div class="clearfix form-actions">

            <div class="col-md-offset-3 col-md-9">
                <button class="btn btn-info" type="submit">
                    <i class="icon-ok bigger-110"></i>


                                                                                                                <?php echo lang('guardar'); ?>


                </button>
                <button class="btn" type="reset">
                    <i class="icon-undo bigger-110"></i>


                                                                                                                <?php echo lang('volver'); ?>


                </button>
            </div>

        </div>

        </div>
</div>  -->