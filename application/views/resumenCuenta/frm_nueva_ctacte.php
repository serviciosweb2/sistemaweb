<script src="<?php echo base_url('assents/js/resumendecuenta/frm_nueva_ctacte.js'); ?>"></script>

<div class="modal-header">
    <h4 class="blue bigger"><?php echo lang('nueva_cta_cte') ?>
        <small>
            <i class="icon-double-angle-right"></i>
            <?php echo $alumno->nombre . ' ' . $alumno->apellido ?>
        </small>
    </h4>
</div>    
<div class="modal-body">      
    <form id="frm-agregar-conceptos">


        <input value="<?php echo $_POST["codigo"] ?>" name="alumno" type="hidden"/>


        <div id="paso_1">
            
            
        <div class="row">
            <div class="col-md-12">
                <h5 class="text-primary"><input type="radio" name="accion" value="linea"> <?php echo lang('agregar_linea_cta_cte') ?></h5>

            </div>

        </div>


        <div id="vista_1" class="hide"> 

            <div id="vista_conceptos"> 


                <div class="row">
                    <div class="col-md-6 col-xs-12">
                        <label><?php echo lang("concepto"); ?></label>
                        <div class="input-group">
                            <select id="conceptos" name="cod_concepto" class=" width-100 form-group" onchange='cambioConceptos();' width="100%">
                                <option value='-1'></option>
                                <?php foreach ($conceptos as $concepto) { ?>
                                    <option value='<?php echo $concepto['codigo'] ?>'>
                                        <?php echo $concepto['nombre'] ?>
                                    </option>
                                <?php } ?>
                            </select>    


                        </div>

                    </div>

                </div>    

                <div class="row">
                    <div class="col-md-6 col-xs-12">
                        <label><?php echo lang("cantidad_de_cuotas"); ?></label>
                        <div class="input-group">
                            <input class="form-control" type="text" value="1" name="cuotas"  onkeypress="return ingresarNumero(this, event);">
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-12">
                        <label><?php echo lang("importe"); ?></label>
                        <div class="input-group">
                            <input class="form-control" type="text"  name="importe_seleccionado">
                        </div>
                    </div>



                </div>
                


                <div class="row">

                    <div class="col-md-6 col-xs-12">
                        <label><?php echo lang("planpago_periodo"); ?></label>
                        <div class="input-group">
                            <select name="cuota_periodo" class="form-control" >
                                <?php foreach ($periodicidad as $value) { ?>
                                    <option value="<?php echo $value["codigo"] ?>">
                                        <?php echo $value["nombre"] ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>



                    <div class="col-md-6 col-xs-12">
                        <label><?php echo lang("fecha_primer_cuota"); ?></label>
                        <div class="input-group">
                            <input class="form-control" type="text"   name="fecha_primer_pago_concepto" value="">
                            <span class="input-group-addon">
                                <i class="icon-calendar bigger-110"></i>
                            </span>
                        </div>
                    </div>


                </div>


            </div>
        </div>







        <div class="row" id="plan-de-pagos-radio">
            <div class="col-md-12">
                <h5 class="text-primary"><input type="radio" name="accion" value="plan"><?php echo lang('agregar_plan_pago') ?></h5>
            </div>

        </div>



        <div id="vista_2" class="hide">

            <div class="row">
                <div class="col-md-12">
                    <label><?php echo lang("matricula"); ?></label>
                    <select id="matriculas" name="matricula" class="chosen-select" >
                        <option value='-1'></option>
                        <?php foreach ($matriculas as $matricula) { ?>
                            <option value='<?php echo $matricula['codigo'] ?>'>
                                <?php echo $matricula['descripcion'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="row">



                <div class="col-md-12">
                    <label> <?php echo lang('detalleplan_plan') ?></label>
                    <select class=" chosen-select" name="planes" data-placeholder="<?php echo lang("SELECCIONE_UNA_OPCION") ?>"></select>

                </div>        

            </div>



            <div class="clearfix" style="min-height: 10px"></div>

            <div class="row  padding-2" >
                <div class="col-sm-12" >
                    <div class="table-responsive">
                        <table id="esquema" class="table table-striped table-bordered table-hover">                               
                        </table>
                    </div>
                </div>
            </div>








        </div>
        </div>
        <div id="paso_2" class="hide">

            <div class="row">

                <div class="col-md-12">
     
                    <h5 class="text-warning bigger-110 orange"> <?php echo lang("se_agregaran_los_siguientes_registros_ctacte") ?></h5>
                        <table class="table table-striped table-bordered" id="tabla_nueva_financiacion">
                            <thead>
                            <th ><?php echo lang("cuota"); ?></th>
                            <th ><?php echo lang("descripcion"); ?></th>
                            <th><?php echo lang("importe"); ?></th>
                            <th><?php echo lang("fecha"); ?></th>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
            
                </div>
            </div>



        </div>




      



    </form> 


</div>
<div class="modal-footer">
      <button class="btn btn-sm btn-primary" type="" name="btn_volver" id="btn_volver" onclick="volverAFinanciacion();" style="display: none;">
            <i class="icon-arrow-left icon-on-left"></i>
            <?php echo lang("volver"); ?>
        </button>
        <button class="btn btn-sm btn-primary" type="" name="btn_guardar" id="btn_guardar" onclick="guardarFinanciacion();" style="display: none;">
            <i class="icon-ok"></i>
            <?php echo lang("guardar"); ?>
        </button>
    <button class="btn btn-sm btn-primary" type="" name="enviarForm" id="enviarForm" disabled="true" onclick="previsualizarFinanciacion();">
        <i class="icon-arrow-right icon-on-right"></i>
        <?php echo lang("siguiente"); ?>
    </button>
</div>