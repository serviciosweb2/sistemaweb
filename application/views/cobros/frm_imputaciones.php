<script src="<?php echo base_url('assents/js/cobros/frm_imputaciones.js')?>"></script>
<script src="<?php echo base_url('assents/js/bootnotify/bootstrap-notify.js')?>"></script>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootnotify/bootstrap-notify.css')?>"/>

<style>
    .panel{
        margin-bottom: 0px!important;
    }
  .page-header{
        margin-top: 0px!important;
    }
    
    .controlTabla{
        max-height: 150px;
        overflow: auto;
    }
</style>

<?php $this->load->helper('cobros'); ?>







 <div class="modal-content">
    <div class="modal-header">
            
            <h4 class="blue bigger"><?php echo lang('imputaciones') ?><small><i class="icon-double-angle-right"></i>  <?php echo formatiarReferencia($detalleReferencia).', '.$alumnoformateado; ?></small></h4>
    </div>

    <div class="modal-body overflow-visible">
    
        
          
          
          
        <div class="row">
            <div class="col-md-12">
                <span class="label label-info"><?php echo lang('imputaciones'); ?>:</span>
            </div>
        </div>
          
        
        <div class="row">
              
              <div class="col-md-12 controlTabla table-responsive" >
                  <!--<small class="text-primary">Imputacion :</small>-->
                
                  <?php 
                  
                  if($imputacionesCobro==''){
                      
                      echo '<div class="row" id="imputaciones_cobro"><div class="col-md-12"><div class="panel panel-default"><h3><small>No tiene imputaciones</small></h3></div></div></div>';
                  
                   }else{
                      
                      echo '<table class="table table-striped table-bordered" id="tablaImputaciones" ">
                      <thead><th>'.lang('descripcion').'</th><th>'.lang('valor').'</th><th>'.lang('estado').'</th></thead>
                  <tbody>';
                  
                  
                        foreach($imputacionesCobro as $imputacion){
                            echo '<tr><td>'.$imputacion["descripcion"].'</td><td>'.$imputacion["valorImputacion"].'</td><td>'.$imputacion["estado"].'</td></tr>';
                        }
                        
                        echo ' </tbody>
                  </table>';
                  }
                     ?> 
                 
              </div>
              
          </div>
        
        
        <div class="row">
              
              
                      <div class="col-md-12 text-right">
                       
                            <label>
                                
                                <small><?php echo lang('total_imputaciones') ?></small>
                            </label>
                            <p name="totalImputaciones"></p>
                                <!--<input class="form-control " name="totalImputaciones" readonly="" type="text">-->
                                    
                       
                        
                    </div>
                
              
          </div>
          
         
          <hr>
          
          <?php $inline = count($ctacteImputar) > 0 ? 'aImputar' : 'aImputar hide';?>
          
          <div class="row aImputar">
              <div class="col-md-12 col-xs-12"> 
                  <span class="label label-danger"><?php echo lang('imputar');?></span>
                  <span> <?php echo lang('saldo_restante')?> <i class="icon-double-angle-right"></i> <span id="saldoRestante" class="text-primary"></span></span>
              </div>
          </div>
           <div class="row aImputar">
              <div class="col-md-12  controlTabla table-responsive">
                  <form id="imputaciones">
<!--                  <small class="text-primary">Imputacion :</small>-->
                  
                  <table class="table table-striped table-bordered" id="aImputar">
                      <thead><th><?php echo lang('descripcion');?></th><th><?php echo lang('fecha_vencimiento');?></th><th><?php echo lang('importe');?></th><th><?php echo lang('saldo_a_cobrar') ?></th><th><?php  echo lang('imputar');?></th></thead>
                  <tbody>
                     <?php 
                        foreach($ctacteImputar as $imputacion){
                            echo '<tr><td>'.$imputacion["descripcion"].'</td><td>'.$imputacion["fechavenc"].'</td><td>'.$imputacion["importeformateado"].'</td><td>'.$imputacion['saldocobrarformateado'].'</td><td>
                                <div class="input-group">
                                  <span class="input-group-addon">
                                    <input type="checkbox" name="codigoImputar[]" value="'.$imputacion['codigo'].'">
                                  </span>
                                  <input type="" name="valorImputar[]" data-importe="'.$imputacion['saldocobrarformateado'].'" class="form-control" value="'.$imputacion['saldocobrarformateado'].'" disabled>
                                </div><!-- /input-group -->
                              <!-- /.col-lg-6 --></td></tr>';
                        }
                     
                     ?> 
                  </tbody>
                  </table>
                  </form>
              </div>
          </div>
          <br>
          <div class="row aImputar">
              
              <div class="col-md-12 col-xs-12">
                      <div class="col-md-3 col-md-offset-9 col-xs-12">
                        <div class="input-group">
                            <input type="text" class="form-control " name="saldoDisponible" readonly>
                            <span class="input-group-btn">
                                <button class="btn btn-default btn-sm" type="button" name="calcularSaldo"><?php  echo lang('calcular_total');?></button>
                            </span>
                         </div>
                        
                    </div>
                
              </div>
          </div>
          
          
     
    
    </div>

    <div class="modal-footer">
            

            <button class="btn btn-sm btn-primary aImputar" type="submit">
                    <i class="icon-ok"></i>
                    <?php echo lang('guardar'); ?>
            </button>
    </div>
</div>
 
 
 
<!--<div class="panel panel-default">
  <div class="panel-heading">Panel heading without title</div>
  <div class="panel-body">
      <div class="col-md-12">
          <div class="row">
              <div class="page-header">
                  <div class="row">
                      <div class="col-md-12">
                        <div class="col-md-3"> 
                            <h3>Saldo: <small><?php echo $moneda['simbolo']?></small></h3>
                        </div>
                        <div class="col-md-6 errorSaldo alert alert-danger"> 
                            Saldo insuficiente 
                        </div>
                    </div>
                  </div>
              </div>
          </div>
          
 
          
          <div class="row">
              <div class="col-md-12 controlTabla" >
                  <small class="text-primary">Imputacion :</small>
                  <span class="label label-info">Imputaciones:</span>
                  <?php 
                  
                  if($imputacionesCobro==''){
                      echo '<div class="row" ><div class="col-md-12"><div class="panel panel-default"><h3><small>No tiene imputaciones</small></h3></div></div></div>';
                  
                      
                      
                  }else{
                      echo '<table class="table table-striped table-bordered" id="tablaImputaciones" ">
                      <thead><th>valor</th><th>descripcion</th></thead>
                  <tbody>';
                  
                  
                  
                      
                        foreach($imputacionesCobro as $imputacion){
                            echo '<tr><td>'.$moneda['simbolo'].$imputacion["valorImputacion"].'</td><td>'.$imputacion["descripcion"].'</td></tr>';
                        }
                        
                        echo ' </tbody>
                  </table>';
                  }
                     ?> 
                 
              </div>
              
          </div>
          
          <br>
         
           <div class="row">
              <div class="col-md-12  controlTabla table-responsive">
                  <form id="imputaciones">
                  <small class="text-primary">Imputacion :</small>
                  <span class="label label-danger">A imputar</span>
                  <table class="table table-striped table-bordered" id="aImputar">
                      <thead><th>descripcion</th><th>fecha vencimiento</th><th>importe</th><th>Saldo a cobrar</th><th>Imputar</th></thead>
                  <tbody>
                     <?php 
                        foreach($ctacteImputar as $imputacion){
                            echo '<tr><td>'.$imputacion["descripcion"].'</td><td>'.$imputacion["fechavenc"].'</td><td>'.$moneda["simbolo"].$imputacion["importe"].'</td><td>'.$imputacion['saldocobrar'].'</td><td>
                                <div class="input-group">
                                  <span class="input-group-addon">
                                    <input type="checkbox" name="codigoImputar[]" value="'.$imputacion['codigo'].'">
                                  </span>
                                  <input type="" name="valorImputar[]" data-importe="'.$imputacion["saldocobrar"].'" class="form-control" value="'.$imputacion['saldocobrar'].'" disabled>
                                </div> /input-group 
                               /.col-lg-6 </td></tr>';
                        }
                     
                     ?> 
                  </tbody>
                  </table>
                  </form>
              </div>
          </div>
          
          
      </div>
  </div>
  <div class="panel-footer">
      
      <div class="row">
          <div class="col-md-12">
              <div class="col-md-2 pull-right text-right">
            <input type="submit" class="btn btn-success " value="guardar">
          </div>  
          </div>
    </div>
   
  </div>
</div>-->


