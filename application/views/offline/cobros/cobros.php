<script src="<?php echo base_url('assents/js/librerias/ajaxchosen/lib/ajax-chosen.js')?>"></script>
<script>
    
    var LANG = JSON.parse('<?php echo $lang ?>');
    console.log(LANG);
    var tipoCheque = JSON.parse('<?php echo $tipoCheque?>');
    var mediosPago = JSON.parse('<?php echo json_encode($mediosPago) ?>');
    
    if(!puedeTrabajarOffline())
    {
         window.location.href = BASE_URL;
       
    }
    
</script>

<script src="<?php echo base_url('assents/js/librerias/jquery-serialize/jquery.serializeJSON.min.js')?>"></script>
<script src="<?php echo base_url('assents/js/offline/cobros/cobros.js')?>"></script>
<style>
    #detalleCtacte tbody tr td 
    {
        padding: 1px !important;
        width: 20px !Important;
    }
    .navbar
            {
                background: #a069c3!important;
            }
</style>


<div class='col-md-12'>
    
    <div class='row'>
        <div class='col-md-2'>
            <button class='btn btn-info various' href="#inline"><?php echo lang('nuevo-cobro')?></button>
        </div>
    </div>
   
    <div class='row'>
        <div class='col-md-12'>
            <table class='table table-bordered' id='cobrosOffline'>
            <thead>
            <th><?php echo lang('codigo')?></th>
            <th><?php echo lang('listadoResponsables_nombreAepellido')?></th>
            <th><?php echo lang('importe')?></th>
            <th><?php echo lang('saldo')?></th>
            <th><?php echo lang('medio_pago')?></th>
            <th><?php echo lang('fecha')?></th>
            <th><?php echo lang('estado')?></th>
            </thead>
            <tbody></tbody>
            </table>
        </div>
        
    </div>

</div>


<!--FRM COBRO-->
    <div id="inline" style="display:none;">
        
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="blue bigger"><?php echo lang('nuevo-cobro')?></h4>
            </div>

            <div class="modal-body overflow-visible">
                <form id="cobro">
                <div class="row" id='modulo1'>

                    <div class='col-md-4 form-group'>
                        <label><?php echo lang('fecha')?></label>
                        <input class='form-control' name="fecha_cobro">
                    </div>
                    <div class='col-md-4 form-group'>
                        <label><?php echo lang('alumnos')?></label>
                        <select class='form-control' name="alumnos"  multiple onchange="listarCTACTE(this);" data-placeholder="<?php echo lang('SELECCIONE_UNA_OPCION')?>"></select>
                    </div>
<!--                    <div class='col-md-4 form-group'>
                        <label><?php echo lang('medio_de_pago')?></label>
                        <select class='form-control' name="medio_cobro" data-placeholder="<?php echo lang('seleccione_medio_pago');?>">
                            <option value=""></option>
                                    <?php 
                                    foreach($mediosPago as $medio)
                                    {
                                       echo '<option value="'.$medio['codigo'].'">'.lang($medio["medio"]).'</option>';
                                    }
                                    ?>
                        </select>
                    </div>-->

                </div><!--! FIN primer modulo-->
                <div class='row' id='modulo2'>
                    <div class='col-md-12'>
                        <table class='table table-bordered' id='detalleCtacte'>
                            <thead>
                            <th></th>
                            <th><?php echo lang('descripcion') ?></th>
                            <th><?php echo lang('fecha_vencimiento')?></th>
                            <th><?php echo lang('importe')?></th>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table> 
                    </div>
                </div><!--! FIN segundo modulo-->
                <div class='row hide' id='modulo3'>
                    
                      <div class='col-md-4 form-group'>
                        <label><?php echo lang('medio_de_pago')?></label>
                        <select class='form-control' name="medio_cobro" data-placeholder="<?php echo lang('seleccione_medio_pago');?>" onchange="verDetallesMedios();">
                            <option value=""></option>
                                    <?php 
                                    foreach($mediosPago as $medio){
                                       echo '<option value="'.$medio['codigo'].'">'.lang($medio["medio"]).'</option>';
                                    }
                                    ?>
                        </select>
                    </div>
                    
                    
                </div><!--! FIN trercer modulo-->
                <div class='row' id='modulo4'>
                    <div class="form-group col-md-3  col-xs-12 col-md-offset-9">
                          <label class="" for="exampleInputFile"><?php echo lang('monto_total')?></label>
                            <div class="input-group">
                                <span class="input-group-addon">$</span>
                                <input type="text" class="form-control " name="total_cobrar" value="" onkeyup="validarEntrada(this)">
                                <span class="input-group-btn">
                                    <button class="btn btn-default btn-sm" name="calcularTotal" type="button" onclick="calcular_Total();"><i class="icon-refresh"></i></button>
                                </span>
                            </div>

                        </div>
                </div><!--! FIN cuarto modulo-->
                </form>
            </div>

            <div class="modal-footer">
<!--                <button class="btn btn-sm hide" id='btnVolver' onclick="volver();">
                        <i class="icon-remove"></i>
                        Cancel
                </button>-->

                <button class="btn btn-sm btn-primary btnPaso1" onclick="irSegundoPaso();">
                        <i class="icon-ok"></i>
                        <?php echo lang('continuar')?>
                </button>

                <button class="btn btn-sm btn-primary btnPaso2 hide" onclick='volver()'>
                        <i class="icon-ok"></i>
                        <?php echo lang('volver')?>
                </button>
                <button class="btn btn-sm btn-primary btnPaso2 hide" onclick='guardar();'>
                        <i class="icon-ok"></i>
                        <?php echo lang('guardar')?>
                </button>
            </div>
        </div>
    </div>
    
    
<!--FRM PIN-->
    <div id="confirmarpin" style="display:none;">
        <div class="modal-content">
            <form id="frmpin"  onsubmit="guardarFINAL(this,event);">
                <div class="modal-header">
                        <h4 class="blue bigger"><?php echo lang('ingrese_pin');?></h4>
                </div>

                <div class="modal-body overflow-visible">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label><?php echo lang('pin')?></label>
                                <input name="pinACCESS" class="form-control" type="password" value="">
                            </div>
                            

                        </div>
                </div>

                <div class="modal-footer">
                        

                    <button class="btn btn-sm btn-primary" type="submit">
                                <i class="icon-ok"></i>
                                <?php echo lang('aceptar')?>
                        </button>
                </div>
        </form>
    </div>
        
    </div>
    
    
<!--FRM RECIbO-->
    <div  id="recibo" style="display:none">
        
            <div class="modal-content">
                <div class="modal-header">
                        <h4 class="blue bigger"></h4>
                </div>

                <div class="modal-body overflow-visible">
                        <div class="row">
                            <div class="col-md-12 alumnoRBO">
                               
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 medioRBO">
                               
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 fechaRBO">
                               
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 importeRBO">
                               
                            </div>
                        </div>
                </div>

                <div class="modal-footer no-print">
                        
                        <button class="btn btn-sm btn-primary" onclick="imprimirRecibo();">
                                <i class="icon-ok"></i>
                                imprimir
                        </button>
                </div>
        </div>
       
    </div>