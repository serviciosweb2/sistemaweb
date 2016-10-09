<script src="<?php echo base_url('assents/js/tickets/generar_tickets.js');?>"></script>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal.css')?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal-bs3patch.css')?>"/>
<script src="<?php base_url()?>assents/js/librerias/bootstrap-modal/bootstrap-modal.js"></script>
<script src="<?php base_url()?>assents/js/librerias/bootstrap-modal/bootstrap-modalmanager.js"></script>


<script>     

<?php 
    
    foreach ($subcategorias as $i => $subcats){
        echo 'var subcategoria'.$i.' = [';
        
        foreach($subcats as $sub){
            echo '"'.lang($sub). '",';
        }

        echo '];';
    }
?>
</script>

<div class="modal-content">
    <div class="modal-header">              
        <h4 class="blue bigger"><?php echo lang("reportar_error"); ?></h4>
    </div>
    <form id="nuevo_ticket">
        <div class="modal-body overflow-visible">            
            <div class="row" id="primer_fila">
                <div class="form-group col-md-6 col-xs-12">
                    <label><?php echo  lang('asunto'); ?>:*</label>
                    <input name="nombre" class="form-control" type="text" value="">
                </div>                                        
                <div style="clear:both"></div>
                <div class="form-group col-md-3 col-xs-12">
                    <label><?php echo  lang('categoria'); ?>:*</label>
                    <select class="form-control" name="categorias" id="categorias" data-placeholder="<?php echo lang('categoria')?>">
                        <?php foreach($categorias as $i => $value){ ?>
                        <option value="<?php echo $i;?>">
                            <?php echo lang($value) ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-md-3 col-xs-12">
                    <label><?php echo  lang('subcategoria');  ?>:*</label>
                    <select class="form-control" name="subcategorias" id="subcategorias" data-placeholder="<?php echo lang('categoria')?>">
                        <?php foreach($subcategorias[0] as $sub){?>
                        <option value="<?php echo lang($sub)?>"><?php echo lang($sub)?></option>
                        <?php } ?>
                    </select>
                </div>                                        
                <div style="clear:both"></div>
                <div class="form-group col-md-6 col-xs-12">
                    <label><?php echo lang('descripcion'); ?>:*</label>
                    <textarea name="descripcion" class="form-control" type="text" rows="6"></textarea>
                </div>
            </div>
        </div>
    </form>
    
    <div class="modal-footer">
        <button class="btn btn-sm btn-primary submit" name='enviar' id="enviar">
            <i class="icon-ok"></i>
            <?php echo lang('enviar')?>
        </button>
    </div>
</div>     