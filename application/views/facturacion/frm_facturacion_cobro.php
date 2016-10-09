<script>
    var monedaSimbolo = '<?php echo $moneda["simbolo"] ?>';
    var langFrm = <?php echo $langFrm ?>;
</script>
<style>
    .chosen-results{
        
        max-height: 130px !important;
    }
    
    .fancybox-wrap .modal-header {
        padding: 1px!important;
        
}


 
.mySearch input{
    border: 1px solid #6fb3e0 !Important;
    width: 20px !Important;
    height: 28px!important;
    border-radius: 50px!important;
    font-size: 13px !Important;
    color: #666!important;
    z-index: 11;
    -webkit-transition: width ease .15s !Important;
    transition: width ease .15s !Important;
}


.mySearch input:focus{
    
    width: 100% !important;
    border-radius: 0px!important;
    
}

.mySearch i:focus{
    
    width: 100% !important;
    border-radius: 0px!important;
    
}
.mySearch .input-icon{
    width: 100% !important
}
.mySearch label{
    /*padding-bottom: 80px !important;*/
}

</style>

<?php 

$colAlumnos = count($facturantes) > 1 ? 'col-md-4' : 'col-md-8';

$displayFacturante = count($facturantes) > 1 ? '' : 'hide';
$ultimoDiaMes = date("d", (mktime(0, 0, 0,date("m") + 1, 1, date("Y")) - 1));
?>

<script>
    var ultimoDiaMes = <?php echo $ultimoDiaMes; ?>;
    
    $('.fancybox-wrap').ready(function()
    {
        
        $('i').on('click',function()
        {
           $(this).prev().focus();
        });
    });
    
    
</script>
<script src="<?php echo base_url('assents/js/librerias/ajaxchosen/lib/ajax-chosen.js')?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo  base_url('assents/css/facturacion/frm_facturacion_cobro.css') ?>"/>
<script src="<?php echo base_url('assents/js/facturacion/frm_facturacion_cobro.js') ?>"></script>
<div class="modal-content" >
    <?php  if ($validaciones == ''){ ?>
        <div class="modal-header">
            <h4 class="blue"><?php echo lang("facturacion_y_cobro"); ?></h4>
        </div>
        <div class="modal-body overflow-visible">
            <form class="form " role="form">
                <div class="row factura">
                    <div class="col-md-12 ">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label><?php echo lang("fecha"); ?></label>
                                <div class="input-group">
                                    <input class="form-control date-picker" value="<?php echo formatearFecha_pais($fecha); ?>"  id="fecha-factura" name="fecha-factura" type="text" data-date-format="dd-mm-yyyy">
                                    <span class="input-group-addon">
                                        <i class="icon-calendar bigger-110"></i>
                                    </span>
                                </div>
                            </div> 
                            <div class="form-group <?php echo ' '.$colAlumnos?>">
                                <label><?php echo lang("Alumno"); ?></label>
                                <div>
                                    <select id="alumnos" name="alumnos"   data-placeholder ="<?php echo lang('seleccione_alumno');?>" multiple>
                                        <option></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-4 <?php echo ' '.$displayFacturante?>">
                                <label><?php echo lang("facturante"); ?></label>
                                <div>
                                    <select id="facturante" name="facturante" class="form-control ">
                                        <?php
                                        foreach ($facturantes as $facturante) { ?>
                                        <option value='<?php echo $facturante['codigo'] ?>'>
                                            <?php echo $facturante['razon_social'] ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4 razon-social-group">
                                <label><?php echo lang("razon_social"); ?></label>
                                <select id="razones_sociales" name="razones_sociales" class=" form-control" data-live-search="true" data-placeholder ="<?php echo lang('razon_social');?>"></select>
                            </div>
                            <div class="form-group col-md-4">
                                <label><?php echo lang("tipo_factura"); ?></label>
                                <select id="tipo_factura" name="tipo_factura" class=" form-control" data-placeholder ="<?php echo lang('tipo_factura');?>"
                                        <?php ?> multiple="true" <?php  ?>>
                                </select>
                            </div>
                            <div class="form-group col-md-4 mySearch no-margin">
                                <label><br></label>
                                <span class="input-icon">
                                <input  class="form-control" aria-controls="ctacteTable" onkeyup="mySearch(this)">
                                <i class="icon-search blue "></i>
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <div class=" content-tabla-ctacte"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-3 col-md-offset-9">
                                <div class="input-group">
                                    <input class="form-control input-mask-product" type="text"  id="total-general" name="total_general" >
                                    <span class="input-group-addon" style="cursor: pointer;" onclick="calcularTotalFacturacion();">
                                        <i class="icon-refresh"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div id="elementos-ctacte"></div>                        
                    </div>
                </div>
                <div class="cobro row">
                    <div class="col-md-12 ">
                        
                            <div class="form-group col-md-12">
                                <label><?php echo lang("medio_de_pago"); ?></label>
                                <select id="medio_pago" name="medio_pago" class="form-control" data-placeholder="<?php echo lang('seleccione_medio_pago') ?>">
                                    <option></option>
                                    <?php
                                    foreach ($mediosPago as $medio){ ?>
                                    <option value="<?php echo $medio['codigo'] ?>">
                                        <?php echo $medio['medio']; ?>
                                    </option>
                                    <?php } ?>
                                </select>
                                <div class=" detalle-medio">
                                    <div class="caja"></div>
                                    <div class="medios"></div>
                                </div>
                        </div>
                        
                    </div>                    
                </div>
            </form>  
            <div class="modal-footer">
        <button class="btn btn-success previous-factura" id="btn-volver" type="button" style="display: none;">
            <i class="icon-arrow-left icon-on-left"></i>
            <?php echo lang("volver"); ?>
        </button>
        <button class="btn btn-success" id="btn-facturar" type="button" style="display: none;">
            <i class="icon-ok"></i>
            <?php echo lang("boton_confirmarEnvio_formAlumnos"); ?>
        </button>
        <button class="btn btn-success" id="btn-cobrar" type="button"><?php echo lang("cobro"); ?>
            <i class="icon-arrow-right icon-on-right"></i>
        </button>
    </div>
</div>
        <?php } else { ?>
             <div class="error-container">
                <div class="well-sm">
                    <h1 class="grey lighter smaller">
                        <span class="blue bigger-125">
                            <i class="icon-random"></i>
                            <?php echo lang('configuracion'); ?>
                        </span>
                        <?php echo lang('accion_requerida'); ?>
                    </h1>
                    <hr>
                    <h3 class="lighter smaller">
                        <?php echo lang('ocurrieron_errores_configuracion'); ?>
                        <i class="icon-wrench icon-animated-wrench bigger-125"></i>                      
                    </h3>
                    <h6 class="lighter smaller">
                        <?php echo $validaciones ?>                            
                    </h6>
                    <div class="space"></div>
                    <div>
                        <h4 class="lighter smaller"><?php  echo lang('necesita_ayuda');?></h4>
                        <ul class="list-unstyled spaced inline bigger-110 margin-15">
                            <li>
                                <i class="icon-hand-right blue"></i>
                                <span class="blue bigger-125"><?php echo lang('contactese_con_casa_central');?></span>
                                  </li>           
                      </ul>
                    </div>
                    <hr>
                    
                </div>
            </div>
        <?php } ?>
    </div>
    