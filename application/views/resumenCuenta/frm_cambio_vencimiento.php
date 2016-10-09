<script src="<?php echo base_url('assents/js/resumendecuenta/frm_cambio_vencimiento.js') ?>"></script>

<div class="modal-content">
    <div class="modal-header">
        <!--<button class="close" data-dismiss="modal" type="button">×</button>-->
        <h4 class="blue bigger">
            <?php echo lang("cambio_de_vencimientos_en_cuenta_corriente"); ?>
            <small>
                <i class="icon-double-angle-right"></i>
                <?php echo $nombreAlumno ?>
            </small>
        </h4>
    </div>

    <div class="modal-body overflow-visible">        
        <div class="row">
            <div class="col-md-12 col-xs-12">
                <div class="form-group col-md-6  col-xs-12">			
                    <label><?php echo lang("conceptos"); ?></label>
                    <select id="conceptos" class="form-group " onchange='cambioConceptos();'>
                        <option value='-1'></option>
                        <?php foreach ($conceptos as $concepto){ ?>
                        <option value='<?php echo $concepto['codigo'] ?>|<?php echo $concepto['concepto'] ?>'>
                            <?php echo $concepto['nombre'] ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-md-6  col-xs-12">			
                    <label><?php echo lang("planpago_periodo"); ?></label>
                    <select id="periodicidad" class="form-group " <?php  echo count($periodicidad) > 0 ? '' : 'disabled'?>>
                        <?php  foreach ($periodicidad as $key => $value){ ?>
                        <option value='<?php echo $key ?>'>
                            <?php echo $value['valor'].' '.$value['traducido'] ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
            </div>        
        </div>
        <div class="row" id='div_visualizar_ctacte'>   
            <div class="col-md-12">
                <div class="table-responsive">
                   <table  class="table table-striped table-bordered" id="detalleCTACTE" style='width: 100%;'>
                       <thead>
                           <tr>
                        <th><?php echo lang("descripcion"); ?></th>
                        <th><?php echo lang("importe") ?></th>
                        <th><?php echo lang("fecha"); ?></th>
                        </tr>
                   </thead>
                   <tbody></tbody>
                   </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal-footer">
        <button class="btn btn-sm btn-primary" type="" name="enviarForm" onclick="guardarCambioFecha();" id="btn_guardar_fechas">
            <i class="icon-ok"></i>
            Guardar
        </button>
    </div>    
</div>
<div id="divTempTransform"></div>
<input type='hidden' id='codigo_alumno' value='<?php echo $alumno->getCodigo(); ?>'>
<script>
    $("select").chosen({
        width:'100%'
    });
</script>