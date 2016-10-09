<style>
    
.table>thead>tr>td.info, .table>tbody>tr>td.info, .table>tfoot>tr>td.info, .table>thead>tr>th.info, .table>tbody>tr>th.info, .table>tfoot>tr>th.info, .table>thead>tr.info>td, .table>tbody>tr.info>td, .table>tfoot>tr.info>td, .table>thead>tr.info>th, .table>tbody>tr.info>th, .table>tfoot>tr.info>th {
background-color: #d9edf7;
}

.content_presupuestos{
    
    max-height: 300px !important;
    overflow: auto;
    
}


#modalDetalle .table-responsive{
    
    max-height: 250px !important;
    overflow: auto;
    
}
    
</style>
<script src="<?php echo base_url('assents/js/impresiones.js')?>"></script>
<script src="<?php echo base_url('assents/js/aspirantes/ver_presupuestos.js')?>"></script>

<?php  
function tablaPresupuesto($presupuestoAspirante){
                                                                                                                                                                                                                                                                                    
    $tabla='<table class="table table-colapse table-bordered" id="presupuestos"><thead><th>'.lang('nombre_curso').'</th><th>'.lang('nombre_concepto').'</th><th>'.lang('nombre_plan_pago').'</th><th>'.lang('cantidad_de_cuotas').'</th><th>'.lang('valor').'</th><th>'.lang('d').'</th></thead><tbody>';
    
    foreach($presupuestoAspirante as $codigo=>$presupuesto){

        
      
        
        foreach($presupuesto['detalle'] as $k=>$detalle){
            
            if($k==0){
                
                $tabla.='<tr class="info"><td colspan="6" class="text-center">'.lang('codigo').'&nbsp;&nbsp;&nbsp;'.$codigo.'&nbsp;&nbsp&nbsp'.lang('fecha').'&nbsp;&nbsp;&nbsp;'.$detalle['fecha'].' '.'<i class="icon-print grey" style="cursor: pointer" onclick="imprimirPresupuesto('.$codigo.')""></i></td></tr>';
                
            }
            
            
            $dataPOST=array(
                
                'codigo_concepto'=>$detalle['codigo_concepto'],
                'codigo_plan'=>$detalle['codigo_plan'],
                'codigo_financiacion'=>$detalle['codigo_financiacion']
            );
            
            
            $tabla.="<tr>  <td>".$detalle['nombre_es']."</td>  <td>".$detalle['nombre_concepto']."</td>  <td>".$detalle['nombre_plan']."</td>  <td>".$detalle['cantidad_cuotas']."</td>  <td>".$detalle['valor_total_concepto']."</td>  <td><button class='btn btn-minier btn-info' data-detalle='".json_encode($dataPOST)."'>".lang('detalle')."</button></td>  </tr>";
            
        }
            
    }
    
    $tabla.='</tbody></table>';
    
    return $tabla;
    
}
?>

<div id="modalDetalle"  class="modal fade" data-width="auto" tabindex="-1" data-backdrop="static" data-keyboard="false">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title"><?php echo lang('detalle')?></h4>
  </div>
    <div class="modal-body">
      
      <div class="row">
          <div class="table-responsive">
              
              
              
          </div>
      </div>
      
  </div>
<!--  <div class="modal-footer">
    <button type="button" data-dismiss="modal" class="btn">Cancelar</button>
    <button type="button" data-dismiss="modal" id="btn-ok-cambio-estado" class="btn btn-primary"><?php echo lang('cerrar')?></button>
  </div>-->
</div>


<div class="modal-content">
    <div class="modal-header">
          
            <h4 class="blue bigger"><?php echo lang('presupuestos')?><small><i class="icon-double-angle-right"></i>  <?php echo $nombreFormateado?></small></h4>
    </div>

    <div class="modal-body">
            <div class="row">
                <div class="table-responsive content_presupuestos">
                    <?php
                    if( (count($presupuestoAspirante) > 0) && ($presupuestoAspirante!='') ){

                        echo tablaPresupuesto($presupuestoAspirante); 
                    
                       
                    }else{
                        
                    
        echo '<div class="alert alert-danger">
      <strong>'.lang('upps').'</strong>'.lang('aspirante_sin_presupuesto').'
    </div>';
                        
                    }
                    
                    ?>
                    </div>
            </div>
    </div>

<!--    <div class="modal-footer">
            <button class="btn btn-sm" data-dismiss="modal">
                    <i class="icon-remove"></i>
                    <?php echo lang('cerrar')?>
            </button>

            
    </div>-->
</div>