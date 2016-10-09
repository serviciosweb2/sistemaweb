

<?php 

$titulo= isset($objCompra) ? lang('modificar_compra') : lang('nueva_compra');

$provedorSeteado= isset($objCompra) ? $objCompra->cod_proveedor : '';

?>
<style>
    .prueba{
      width: 1000px !important; 
    }
 </style>
<script>

//var G_articulos=<?php //echo json_encode($articulos)?>;

var langFrm = <?php echo $langFrm?>; 

var G_cajas = <?php echo isset($cajas) ? json_encode($cajas) : json_encode(array())?>;

var G_comprobrantes=<?php echo isset($comprobantes) ? json_encode($comprobantes) : '{}' ?>;

var G_comprobantesCompra=<?php echo isset($comprobantesCompra) ? json_encode($comprobantesCompra) : json_encode(array()) ?>;

var G_pagosCompra=<?php echo isset($pagosCompra) ? json_encode($pagosCompra) : json_encode(array()) ?>;

var G_mediosPago=<?php echo json_encode($mediosPago)?>

var G_compras=<?php echo isset($renglones) ? json_encode($renglones) : json_encode(array()) ?>

var G_categorias=<?php echo isset($categorias) ? json_encode($categorias) : json_encode(array()) ?>

var G_impuestos=<?php echo isset($impuestos) ? json_encode($impuestos) : json_encode(array()) ?>

var G_tiposFacturas=<?php echo isset($tiposFactura) ? json_encode($tiposFactura) : json_encode(array()) ?>


console.log('pagos caja',G_cajas);
//console.log('TIPOSFACTURAS',G_tiposFacturas);
//console.log('LISTACOMPROBANTES:',G_comprobrantes);
//console.log('MEDIOPAGO:',G_mediosPago);
//console.log('RENGLONES:',G_compras);
//console.log('PAGOS',G_pagosCompra);
//console.log('CATEGORIAS',G_categorias);


</script>

<style>
    
    #ui-datepicker-div{
        
        z-index:9000 !important;
    }
    
</style>

<script src="<?php echo base_url('assents/js/compras/frm_compras.js')?>"></script>
<div class="modal-content">
     <form id="compra">
    <div class="modal-header">
          
            <h4 class="blue bigger"><?php echo $titulo ?>
                <small><i class="icon-double-angle-right"></i>
                  </small>
            </h4>
    </div>

    <div class="modal-body">
           
             
               
                 
                    
                    <div class="row">

                        <div class="form-group col-md-6 col-xs-12">
                            <label><?php echo lang('proveedor');?></label>
                            <select name="cod_proveedor" data-placeholder="<?php echo lang('seleccionar_proveedor');?>">
                                <option></option>
                                <?php 
                                    foreach($proveedores as $provedor){
                                         
                                        $selected= $provedor['codigo']==$provedorSeteado ? 'selected' : '';
                                        
                                        echo '<option value="'.$provedor['codigo'].'" '.$selected.'>'.$provedor['nombre'].'</option>';
                                    
                                    }
                                ?>
                            </select>
                            
                        </div>
                    
                    
                        <div class="form-group col-md-6 col-xs-12">
                            <label><?php echo lang('fecha');?></label>
                            <input name="fecha" placeholder="<?php echo lang('click_para_seleccionar_fecha');?>" value="<?php echo isset($objCompra) ? formatearFecha_pais($objCompra->fecha) : '' ?>" class="form-control input-sm fecha">
                            
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 col-xs-12">
                            
                             <div class="tabbable">
                                    <ul class="nav nav-tabs" id="myTab">
                                            <li class="active">
                                                <a data-toggle="tab" href="#compras">

                                                            <?php echo lang('orden_de_compra');?>
                                                    </a>
                                            </li>

                                            <li>
                                                <a data-toggle="tab" href="#comprobantes">
                                                            <?php echo lang('comprobantes');?>

                                                    </a>
                                            </li>

                                            <li class="<?php echo count($cajas)>0 ? '' : 'disabled'?>">
                                                    <a data-toggle="<?php echo count($cajas)>0 ? 'tab' : ''?>" href="<?php echo count($cajas)>0 ? '#pagos' : 'JavaScript:void(0)'?>">
                                                            <?php echo lang('pagos');?> &nbsp;

                                                    </a>


                                            </li>
                                    </ul>

                                    <div class="tab-content">
                                    <div id="compras" class="tab-pane in active">
                                            
                                        <div class="row">
                                            <div class="col-md-12 col-xs-12 contenedor">
<!--                                                <table id="tablaCompras" class="table table-bordered table-condensed">
                                                    <thead>
                                                       
                                                        <th>_producto</th>
                                                        <th>_Cantidad</th>
                                                        <th>_Precio unitario</th>
                                                        <th>_Precio total</th>
                                                        <th></th>
                                                    
                                                    </thead>
                                                    
                                                    <tbody>
                                                        
                                                    </tbody>
                                                
                                                </table>-->
                                            </div>
                                            
                                        </div>
                                        <div class="row">
                     

                        
                                            <div class="col-md-2 col-md-offset-10 col-xs-12">
                                                <label></label>
                                                <div class="input-group">
                                                    <input  class="form-control" name="total" readonly>
                                                     <span class="input-group-btn">
                                                         <button class="btn btn-default btn-sm"  type="button" onclick="actualizarTotal();"><i class="icon-refresh"></i></button>
                                                    </span>
                                                </div>
                                             </div>
                                        
                                        
                                        </div>
                                        
                                        
                                        
                                    </div>

                                    <div id="comprobantes" class="tab-pane">
                                            

                                        
                                    <div class="row">
                                        <div class="col-md-12 col-xs-12 contenedor"></div>
                                    </div>
                                        
                                    </div>

                                    <div id="pagos" class="tab-pane">
<!--                                        <div class="row">
                                            
                                            <div class="col-md-4 col-xs-4 form-group">
                                                <select name="cod_caja" data-placeholder= "<?php echo lang('seleccionar_caja');?>">
                                                    
                                                    <option></option>
                                                    
                                                    <?php 
                                                        $selected= count($cajas)==1 ? 'selected' : '';
                                                        foreach($cajas as $caja){
                                                            
                                                            echo '<option value="'.$caja['codigo'].'" '.$selected.'>'.$caja['nombre'].'</option>';
                                                            
                                                        }
                                                    
                                                    ?>
                                                    
                                                </select>
                                            </div>
                                            
                                        </div>-->
                                        

                                    <div class="row">
                                        <div class="col-md-12 col-xs-12 contenedor">
                                            
                                        </div>
                                    </div>
                                    
                                    </div>
                                
                                    

                                   
                            </div>
                            </div>
                            
                        </div>
                    </div>
                 
     
                    
               
                
                
           
    </div>

    <div class="modal-footer">
<!--            <button class="btn btn-sm" data-dismiss="modal">
                    
                <i class="icon-remove"></i>
                    
            </button>-->
            
            
            <input name="cod_compra" value="<?php echo $cod_compra?>" type="hidden">
            <button class="btn btn-sm btn-primary" type="submit">
                    <i class="icon-ok"></i>
                    <?php echo lang('guardar')?>
            </button>
    </div>
     </form>
    <form id='bajass'><div id="bajas"></div></form>
</div>