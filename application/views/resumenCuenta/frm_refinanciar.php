<script src="<?php echo base_url('assents/js/resumendecuenta/frm_refinanciar.js');?>"></script>
<script src="<?php echo base_url('assents/js/jquery.maskedinput.js'); ?>"></script>


<div class="modal-content" >
    <div class="modal-header">
       
        <h3 class="blue bigger"><?php echo lang("refinanciar_ctacte"); ?>
            <small>
                <i class="icon-double-angle-right"></i>
                    <?php echo $nombreAlumno ?>
            </small>
        </h3>
    </div>
    <div class="modal-body overflow-visible">
        <div id="vista_1">
            
            
            <div class="row">
                <div class="col-md-3 col-xs-12">
        
                        <label><?php echo lang("conceptos"); ?></label>
                        <select id="conceptos" class="form-group" onchange='cambioConceptos();'>
                            <option value='-1'></option>
                            <?php foreach ($conceptos as $concepto){ ?>
                            <option value='<?php echo $concepto['codigo'] ?>|<?php echo $concepto['concepto'] ?>'>
                                <?php echo $concepto['nombre'] ?>
                            </option>
                            <?php } ?>
                        </select>
         
                
                </div>
                 </div>
            <div class="row">
        
                <div class="col-md-12">
                    <h4><small>Seleccione cuotas a refinanciar</small></h4>
                    <div class="table-responsive">
                       <table class="table table-striped table-bordered" id="tableDetalle" style='width: 100%;'>
                            <thead>
                                <th style="width: 20px;">&nbsp;</th>
                                <th><?php echo lang("descripcion"); ?></th>
                                <th  style="width: 40px;" ><?php echo lang("importe") ?></th>
                                <th  style="width: 40px;"><?php echo lang("fecha"); ?></th>
                            </thead>
                            <tbody>
                            </tbody>
                       </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 col-xs-12">
                    <label><?php echo lang("importe_refinanciar"); ?></label>
                    <div class="input-group">
                        <input class="form-control" type="text"  name="importe_seleccionado">
                    </div>
                </div>
                <div class="col-md-3 col-xs-12">
                    <label><?php echo lang("fecha_primer_cuota"); ?></label>
                    <div class="input-group">
                        <input class="form-control" type="text" disabled="true" name="fecha_primer_pago">
                    </div>
                </div>
                <div class="col-md-3 col-xs-12">
                    <label><?php echo lang("cantidad_de_cuotas"); ?></label>
                    <div class="input-group">
                        <input class="form-control" type="text" value="1" name="cantidad_cuotas" disabled="true"onkeypress="return ingresarNumero(this, event);">
                    </div>
                </div>
<!--                <div class="col-md-2 col-xs-12">
                    <label><?php echo lang("aplicar_como"); ?></label>
                    <div class="input-group">
                        <select class="form-control" name="porcentaje_aplica" disabled="true">
                            <option value="recargo"><?php echo lang("recargo"); ?></option>
                            <option value="descuento"><?php echo ucfirst(lang("planpago_descuento")); ?></option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2 col-xs-12">
                    <label><?php echo lang("porcentaje"); ?></label>
                    <div class="input-group">
                        <input class="form-control" type="text" name="porcentaje" disabled="true" value="0" onkeypress="return ingresarFloat(this, event);">
                    </div>
                </div>                -->
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
                    <label><?php echo lang("detalle_nueva_financiacion"); ?></label>
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
    
        
    </div>
  
</div>
      <div class="modal-footer">
        <button class="btn btn-sm btn-primary" type="" name="btn_volver" id="btn_volver" onclick="volverARefinanciacion();" style="display: none;">
            <i class="icon-arrow-left icon-on-left"></i>
            <?php echo lang("volver"); ?>
        </button>
        <button class="btn btn-sm btn-primary" type="" name="btn_guardar" id="btn_guardar" onclick="guardarRefinanciacion();" style="display: none;">
            <i class="icon-ok"></i>
            <?php echo lang("guardar"); ?>
        </button>
        <button class="btn btn-sm btn-primary" type="" name="enviarForm" id="enviarForm" disabled="true" onclick="previsualizarRefinanciacion();">
            <i class="icon-arrow-right icon-on-right"></i>
            <?php echo lang("siguiente"); ?>
        </button>
    </div>
 </div>

<input type="hidden" id="codigo_alumno" value="<?php echo $alumno->getCodigo(); ?>">
<script>
    $(".select-chosen").chosen({
        width:'100%'
    });
    $("[name=fecha_primer_pago]").datepicker({
        dateFormat: "dd/mm/yy"
    });
    $("[name=fecha_primer_pago]").mask("99/99/9999",{placeholder:"_"});
</script>