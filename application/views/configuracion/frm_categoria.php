<script>

var _accion='<?php echo $accion ?>';

</script>
<script src="<?php echo base_url('assents/js/configuracion/frm_categoria.js')?>"></script>

<div class="modal-content">
   
    <form id="frmCategoria">
        
        <div class="modal-header">
            <?php
                        $titulo='';
                            
                            if($accion=='nueva'){
                                
                                //$titulo= $objCategoria->getCodigo()==-1 ? 'Nueva categoria' : 'Nueva Subcategoria';
                            
                                    $titulo.='Nueva subcategoria';
                                
                            }else{
                                
                               $titulo.='Modificar subcategoria';
                                
                            }
                        
                        ?>
                <h4 class="blue bigger"><?php echo $titulo?></h4>
        </div>

        <div class="modal-body overflow-visible">
                <div class="row">

                    <div class="col-md-12 col-xs-12 form-group">
                        
                        <label>Nombre</label>
                        <input class="form-control" value="<?php echo $accion=='nueva' ? '' :  $objCategoria->nombre ?>" name="nombre_categoria">

                    </div>


                </div>
        </div>

        <div class="modal-footer">

            <button class="btn btn-sm btn-primary" type="submit">
                        <i class="icon-ok"></i>
                        <?php echo lang('guardar')?>
                </button>
            <input name="cod_padre" value="<?php echo $objCategoria->getCodigo()?>" type="hidden">
        </div>
    
    </form>
</div>