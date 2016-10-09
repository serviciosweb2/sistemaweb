<script>
    var langFrm = <?php echo $langFrm ?>;
</script>
<script src="<?php echo base_url('assents/js/articulos/frm_articulos.js')?>"></script>
<style>
    .chosen-results{
        max-height: 100px !important;
    }
</style>
<?php


//    echo '<pre>'; 
//print_r($articuloImpuestos);
//echo '</pre>';
//
//echo '<pre>'; 
//print_r($impuestos);
//echo '</pre>';

//echo '<pre>'; 
//print_r($objArticulo->estado);
//echo '</pre>';
// importesFormateados
    
//echo '<pre>'; 
//print_r($importesFormateados);
//echo '</pre>';
    $codigo = isset($objArticulo) ? $objArticulo->getCodigo() : -1;
    $nombre='';
    $costo='';
    $cod_unidad_medida='';
    $estado='habilitado';
    $cod_categoria='';
    $stock='';

   
   if($codigo!=-1){
    
    $nombre=$objArticulo->nombre;
    $costo = isset($importesFormateados) ? $importesFormateados['costoformateado'] : '';
    $cod_unidad_medida=$objArticulo->cod_unidad_medida;
    $estado=$objArticulo->estado;
    $cod_categoria=$objArticulo->cod_categoria;
    $stock=isset($importesFormateados) ? $importesFormateados['stockformateado'] : '';
   
}
    



?>
<div class="modal-content">
    <form id="frmArticulo">
        <div class="modal-header">
                <h4 class="blue bigger"><?php echo lang('nuevo_articulo');?></h4>
                
                <input type="hidden" name="codigo" value="<?php echo $codigo ?>">
        </div>

        <div class="modal-body overflow-visible">
                <div class="row">
                    
                    <div class="form-group col-md-4">
                        <label><?php echo lang('nombre');?></label>
                        <input class="form-control" value="<?php echo $nombre ?>" name="nombre">
                    </div>
                    <div class="form-group col-md-4">
                        <label><?php echo lang('costo');?></label>
                        <input class="form-control" value="<?php echo $costo;?>" name="costo">
                    </div>
                    <div class="form-group col-md-4">
                        <label><?php echo lang('unidad_de_medida');?></label>
                        <select class="form-control"name="cod_unidad" data-placeholder="<?php echo lang('seleccione_unidad');?>">
                            <option></option>
                            <?php 
                            foreach($unidades as $unidad){
                                $selected=$cod_unidad_medida ==$unidad['codigo'] ? 'selected' :'';
                                
                                echo '<option value="'.$unidad['codigo'].'" '.$selected.'>'.$unidad['unidad'].'</option>';
                            }
                            ?>
                            
                        </select>
                    </div>
                </div>
            
                <div class="row">
                    
                    
                    
                    <div class="form-group col-md-4">
                         <label><?php echo lang('categoria');?></label>
                         <select class="form-control" name="cod_categoria" data-placeholder="<?php echo lang('seleccione_categoria');?>">
                             <option></option>
                             <?php 
                                foreach($categorias as $categoria){
                                    
                                    $selected=$cod_categoria== $categoria['codigo'] ? 'selected' : '' ;
                                    
                                    $padre= $categoria['nombrepadre']== '' ? $categoria['nombre'] : $categoria['nombrepadre'];
                                    
                                    $hijo= $categoria['nombrepadre']== '' ? '' :' ('.$categoria['nombre'].')' ;
                                    
                                    echo '<option value="'.$categoria['codigo'].'" '.$selected.'>'.$padre.$hijo.'</option>';
                                    
                                }
                             
                             ?>
                         </select>
                    </div>
                    <div class="form-group col-md-4">
                         <label><?php echo lang('stock');?></label>
                        <input class="form-control" value="<?php echo $stock?>" name="stock">
                    </div>
                    
                </div>
                <div class="row">
                    <div class="form-group col-md-8 col-sm-12">
                        <label><?php echo lang('impuestos');?></label>
                        <select name="impuestos[]" multiple="multiple" data-placeholder="<?php echo lang('seleccione_impuesto');?>">
                            
                           <?php 
                            
                                foreach($impuestos as $impuesto){
                                    
                                    $selected='';
                                    
                                    foreach($articuloImpuestos as $articuloImpuesto){
                                        
                                        if($articuloImpuesto['cod_impuesto']==$impuesto['codigo']){
                                            
                                            $selected="selected";
                                            
                                        }
                                        
                                    }
                                    
                                    echo '<option value="'.$impuesto['codigo'].'" '.$selected.'>'.$impuesto['nombre'].'</option>';
                                    
                                }
                            
                           ?>
                            
                        </select>
                    </div>
<!--                    <div class="form-group col-md-4 col-sm-12">
                        <label><?php echo lang('estado');?></label>
                        <select name="estado" data-placeholder="<?php echo lang('seleccione_estado');?>">
                             <option></option>
                             
                            <option value="habilitado" <?php echo $estado=='habilitado' ? 'selected' : ''?>><?php echo lang('HABILITADO')?></option>
                            <option value="inhabilitado" <?php echo $estado=='inhabilitado' ? 'selected' : ''?>><?php echo lang('INHABILITADO')?></option>
                            
                        </select>
                        
                    </div>-->
                </div>
        </div>

        <div class="modal-footer">
               
                <button class="btn btn-sm btn-primary" >
                        <i class="icon-ok"></i>
                        <?php echo lang('guardar')?>
                </button>
        </div>
    </form>
</div>