<script src="<?php echo base_url('assents/js/librerias/moment/moment-with-langs.min.js')?>"></script>
<script src="<?php echo base_url('assents/js/impresiones.js') ?>"></script>
<style>
    .popover{
        max-width:none!important;
    }
    
    #detalleResumen_length{
        
        padding-top: 12%!important;
    }
</style>
<script src="<?php echo base_url('assents/js/facturacion/frm_ctacte.js')?>"></script>



<input name="cod_alumno" type="hidden" value="<?php echo $cod_alumno?>">

    
<div id="modalDtalle" class="modal fade" data-width="auto" tabindex="-1" data-backdrop="static" data-keyboard="false">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="blue bigger"><?php echo lang('detalles') ?></h4>
  </div>
    
    <div class="modal-body">
      
      <div class="row">
          <div class="col-md-12 col-xs-12">
              <label><?php echo lang('imputaciones')?>:</label>
              <table id="inputaciones" class="table table-bordered table-condensed" style="width: 100%"><thead></thead><tbody></tbody></table>
          </div>
      </div>
        
        <div class="row">
            <div class="col-md-12 col-xs-12">
                <label><?php echo lang('Facturas');?>:</label>
                <table id="facturas" class="table table-bordered table-condensed" style="width: 100%"><thead></thead><tbody></tbody></table>
            </div>
        </div>
  
    </div>
  <div class="modal-footer">
<!--    <button type="button" data-dismiss="modal" class="btn">Cancelar</button>-->
    <button type="button" data-dismiss="modal" id="btn-ok-cambio-estado" class="btn btn-primary"><?php echo lang('continuar');?></button>
  </div>
</div>

<!--  NUEVA CTA CTE  -->
<div id="modelNuevacc" class="modal fade" data-width="auto" tabindex="-1" data-backdrop="static" data-keyboard="false">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="blue bigger"><?php echo lang('nueva_cta_cte') ?>
      <small>
                <i class="icon-double-angle-right"></i>
                    <?php echo $alumno->nombre.' '.$alumno->apellido ?>
            </small>
    </h4>
  </div>
    
    <div class="modal-body">
      
      <div id="vista_1">
 
            <div class="row">
                <div class="col-md-3 col-xs-12">
        
                        <label><?php echo lang("conceptos"); ?></label>
                        <select id="conceptos" class="form-group" onchange='cambioConceptos();'>
                            <option value='-1'></option>
                            <?php foreach ($conceptos as $concepto){ ?>
                            <option value='<?php echo $concepto['codigo'] ?>'>
                                <?php echo $concepto['nombre'] ?>
                            </option>
                            <?php } ?>
                        </select>
         
                
                </div>
                 </div>
            <div class="row">
        
                
            </div>
            <div class="row">
                <div class="col-md-4 col-xs-12">
                    <label><?php echo lang("importe"); ?></label>
                    <div class="input-group">
                        <input class="form-control" type="text"  name="importe_seleccionado">
                    </div>
                </div>
                <div class="col-md-3 col-xs-12">
                    <label><?php echo lang("fecha_primer_cuota"); ?></label>
                    <div class="input-group">
                        <input class="form-control" type="text"  disabled="true" name="fecha_primer_pago">
                    </div>
                </div>
                <div class="col-md-3 col-xs-12">
                    <label><?php echo lang("cantidad_de_cuotas"); ?></label>
                    <div class="input-group">
                        <input class="form-control" type="text" value="1" name="cantidad_cuotas" disabled="true" onkeypress="return ingresarNumero(this, event);">
                    </div>
                </div>
                
                <div class="form-group col-md-2 col-xs-12">
                    <label><?php echo lang("planpago_periodo"); ?></label>
                    <div class="input-group">
                        <select id="periodicidad" class="form-control" >
                            <?php  foreach ($periodicidad as  $value){ ?>
                            <option value='<?php echo $value["codigo"] ?>'>
                                <?php echo $value["nombre"] ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
  
         <div id="vista_2" style="display: none;">
            <div class="row">
                <div class="form-group col-md-12">
                    <label><?php echo lang("detalle"); ?></label>
                    <div id="tablaCtacte"></div>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive">
                       <table class="table table-striped table-bordered" id="tabla_nueva_financiacion">
                            <thead>
                                <th ><?php echo lang("cuota"); ?></th>
                                <th ><?php echo lang("descripcion"); ?></th>
                                <th><?php echo lang("importe") ?></th>
                                <th><?php echo lang("fecha"); ?></th>
                            </thead>
                            <tbody>
                            </tbody>
                       </table>
                    </div>
                </div>
            </div>
        </div>
        
        
         <button class="btn btn-sm btn-primary" type="" name="btn_volver" id="btn_volver" onclick="volverAFinanciacion();" style="display: none;">
            <i class="icon-arrow-left icon-on-left"></i>
            <?php echo lang("volver"); ?>
        </button>
        <button class="btn btn-sm btn-primary" type="" name="btn_guardar" id="btn_guardar" onclick="guardarFinanciacion();" style="display: none;">
            <i class="icon-ok"></i>
            <?php echo lang("guardar"); ?>
        </button>
        
        
    </div>
  <div class="modal-footer">
<!--    <button type="button" data-dismiss="modal" class="btn">Cancelar</button>-->
    <button class="btn btn-sm btn-primary" type="" name="enviarForm" id="enviarForm" disabled="true" onclick="previsualizarFinanciacion();">
            <i class="icon-arrow-right icon-on-right"></i>
            <?php echo lang("siguiente"); ?>
        </button>
  </div>
    
    
    
</div>


<div class="modal-content">
    <div class="modal-header">
            <!--<button type="button" class="close" data-dismiss="modal">&times;</button>-->
            <h4 class="blue bigger"><?php echo lang('cuenta_corriente');?>
            <small>
                    <i class="icon-double-angle-right"></i>
                    <?php echo $alumno->nombre.' '.$alumno->apellido?>
            </small>
            </h4>
    </div>

    <div class="modal-body overflow-visible">
<!--        <div class="row">
            <div class="col-xs-12">
                <select name="filtro" data-placeholder="<?php echo lang('seleccione')?>" onchange="filtrar(this);">
                    
                    <option value="" selected><?php echo lang('todas')?></option>
                    <option value="consaldo"><?php echo lang('consaldo')?></option>
                    <option value="sinsaldo"><?php echo lang('sinsaldo')?></option>
                    
                </select>
            </div>
        </div>  -->
<button id="btnSaveAdvanced" class="btn btn-success btn-sm btn-save" data-last="" onclick="frm_nuevactacte();"><?php echo lang('nuevo')?></button>
        <div class="row">
            <div class="col-xs-12">     
        <div class="table-responsive">
            <table class="table table-striped table-bordered " cellspacing="0" cellpadding="0" border="0" id="detalleResumen">
                <thead>
                    <th><?php echo lang('descripcion');?></th>
                    <th><?php echo lang('importe');?></th>
                    <th><?php echo lang('saldo');?></th>
                    <th><?php echo lang('fecha_vencimiento');?></th>
                    <th><?php echo lang('ver_detalle');?></th>
                    <th>filtro</th>
<!--                    <th><?php echo lang('ver_facturas');?></th>
                    <th><?php echo lang('imputaciones');?></th>-->
                </thead>
                <tbody>
                    <?php 
                    foreach($ctacte as $cuenta){ 
                        
                        
                        $verFactura='' ; // $cuenta[ 'saldo']='566.00'; 
                        
                        $verImputaciones='' ; // $cuenta[ 'saldo']='566.00';
                        
//                       
                            
                            if($cuenta['habilitar']==0){
                                $verFactura='disabled';
                            }
                            
                            if($cuenta['importe'] === $cuenta['saldo']){
                                $verImputaciones='disabled';
                            }
                            
                            echo '<tr><td>'.$cuenta['descripcion'].'</td>';
                            echo '<td>'.$cuenta['importeformateado'].'</td>';
                            echo'<td>'.$cuenta['saldoformateado'].'</td>';
                            echo '<td>'.$cuenta['fechavenc'].'</td>';
                            echo '<td><button  class="btn btn-info btn-xs" onClick="getInputacionesFacturas('.$cuenta['codigo'].');">ver</button></td>';
                            echo '<td>'.$cuenta['filtro'].'</td>';
//                            echo '<td><a data-content="" data-metodo="getFacturas" class="btn btn-xs btn-info verFactura" href="'.$cuenta['codigo'].'"'.$verFactura.'>ver</a></td>';
//                            echo '<td><a data-content="" data-metodo="getImputaciones" class="btn btn-xs btn-info verImputacion" href="'.$cuenta['codigo'].'"  '.$verImputaciones.'>ver</a></td></tr>'; 
                            
                        } 
                        
                    ?>
                </tbody>
            </table>
        
    </div>
    </div></div>

            </div>
   

    <div class="modal-footer">
            <button id="btnSaveAdvanced" class="btn btn-info btn-sm btn-save" data-last="Finish" onclick="imprimirDetalleCtacte();"><?php echo lang('imprimir');?></button>

            <button id="btnSaveAdvanced" class="btn btn-success btn-sm btn-save" data-last="Finish" onclick="cerrarVentana();"><?php echo lang('cerrar')?></button>
    </div>
</div>
			
<!--<script>
    $(".select-chosen").chosen({
        width:'100%'
    });
    $("[name=fecha_primer_pago]").datepicker({
        dateFormat: "dd/mm/yy"
    });
    $("[name=fecha_primer_pago]").mask("99/99/9999",{placeholder:"_"});
</script>-->