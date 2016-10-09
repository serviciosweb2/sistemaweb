
<script>
    var idioma = '<?php echo get_idioma()?>';
    var menuJson = <?php echo $menuJson ?>;
    var transferir_archivos = '<?php echo $trasferir_archivo ?>';
    var cod_alumno = <?php echo $cod_alumno ?>;
    var cod_matricula = <?php echo $cod_matricula ?>;
    var matriculasEmitir = JSON.parse(<?php echo json_encode($matriculas);?>);
    var embed = true;
    var desde = <?php echo $desde; ?>;
    var hasta = <?php echo $hasta; ?>;

</script>
<script src="<?php echo base_url('assents/js/rematriculaciones/frm_emitir.js'); ?>"></script>
<script src="<?php echo base_url('assents/js/impresiones.js'); ?>"></script>    
<style>
    .popover{
        max-width: none!important;
    }
    
    .table_filter{
        -moz-border-bottom-colors: none;
        -moz-border-left-colors: none;
        -moz-border-right-colors: none;
        -moz-border-top-colors: none;
        background-color: #ffffff;
        border-bottom: 1px solid #afafb6;
        border-image: none;
        border-radius: 4px;
        border-top: 1px solid #afafb6;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
        cursor: default;
        padding: 8px 0;
        position: absolute;
        right: 20px;
        text-align: left;
        top: 18px;
        z-index: 1000;
    }
    
    .fecha_date_picker{
        margin: 0 0px !important;
    }

</style>


<div class="col-md-12 col-xs-12">

    <div id="fuelux-wizard" class="row-fluid hide" data-target="#step-container">
        <ul class="wizard-steps">
            <?php
            $paso = 1;
            if (count($facturantes) > 1) {
                ?>
                <li data-target="#step<?php echo $paso; ?>" id="step<?php echo $paso; ?>" class="step">
                    <span class="step"><?php echo $paso; ?></span>
                    <span class="title"><?php echo lang('seleccione_emisor');?></span>
                </li>
                <?php
                $paso++;
            }
            ?>
            <li data-target="#step<?php echo $paso ?>" id="step<?php echo $paso ?>" class="step">
                <span class="step"><?php echo $paso ?></span>
                <span class="title"><?php echo lang('seleccione_cuotas'); ?></span>
            </li>


            <?php $paso++ ?>
            <li data-target="#step<?php echo $paso ?>" id="step<?php echo $paso ?>" class="step">
                <span class="step"><?php echo $paso ?></span>
                <span class="title"><?php echo lang('verifique_datos'); ?></span>
            </li>
            <?php $paso++ ?>
            <li data-target="#step<?php echo $paso ?>" id="step<?php echo $paso ?>" class="step">
                <span class="step"><?php echo $paso ?></span>
                <span class="title"><?php echo lang('imprimir_boletos_descargar_remesa'); ?></span>
            </li>
        </ul>
    </div>
    <div class="row hide" id="facturanteSeleccion"  >
        <div class="col-md-12 ">
            <p></p>
            <?php foreach ($facturantes as $key => $facturante) { ?>            
            <p> 
                <button class="btn btn-info btn-block facturates" codigo='<?php echo $facturante["codigo"] ?>' onclick="showGenerarBoleto(<?php echo $facturante["codigo"] ?>);"><?php echo $facturante["razon_social"] ?></button>
            </p>
            <?php } ?>
        </div>
    </div>    
    <div class="row hide center" id='no-hay-cuentas'>
        <div class="col-md-12">            
            <h4><?php echo lang('no_existe_empresa_imprimir_boleto');?></h4>
            <i class="ace-icon fa icon-ban-circle  red" style="font-size: 200px;"></i>
            <p>                                
                <a class="btn btn-primary" href="boletos"><?php echo lang("volver"); ?></a>
            </p>         
        </div>        
    </div>

    <div id="areaTablasGenerarBoletos" style="display: none;">
        <form id="frm_generar_boleto">
            <?php
            $tmpl = array('table_open' => '
            <table id="boletosBancariosGenerar" cellpadding="0" cellspacing="0"
            border="0" width="100%" class="table table-striped table-bordered table-condensed" oncontextmenu="return false"
            onkeydown="return false">');
            $this->table->set_template($tmpl);
            $arrColDef = array();
            foreach ($arrColumnasBoleto as $columna) {
                $arrColDef[] = $columna['nombre'];
            }
            $this->table->set_heading($arrColDef);
            echo $this->table->generate();
            ?>
        </form>
        <br>
        <div class="row-fluid wizard-actions">
            <div class="col-md-12 col-xs-12 no-padding right  ">
                <div class="btn-group" name="btn_emitir_boleto">
                    <button name="btn_generar_boletos_cancelar" class="btn btn-prev boton-primario" data-original-title="" title="<?php echo lang('volver_al_listado'); ?>" onclick="cancelarPreviewBoletos();">
                        <?php echo lang('atras'); ?><i class="icon-arrow-left"></i>
                    </button>
                    <button name="btn_generar_boletos" class="btn btn-success btn-next boton-primario" data-original-title="" title="<?php echo lang('genera_boleto'); ?>" onclick="generarBoletosConfirmar();" disabled="true">
                        <?php echo lang('siguiente'); ?><i class="icon-arrow-right icon-on-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="areaTablasGenerarBoletosConfirmar" style="display: none;">
        <h3 class="lighter block green"><?php echo lang('confirme_datos_correctos'); ?></h3>
        <table id="boletosBancariosGenerar" cellpadding="0" cellspacing="0"
               border="0" width="100%" class="table table-striped table-bordered table-condensed" oncontextmenu="return false"
               onkeydown="return false">
            <thead>
                <tr>
                    <?php
                    foreach ($arrColumnasBoleto as $key => $columna) {
                        if ($key > 0){ ?>
                            <th> <?php echo $columna['nombre']; ?> </th>
                        <?php }
                    } ?>
                </tr>
            </thead>
            <tbody name="tbody_detalles">
            </tbody>
        </table>




        <div class="row-fluid wizard-actions">
            <div class="col-md-12 col-xs-12 right">
                <div class="btn-group" name="btn_emitir_boleto">
                    <button name="btn_generar_boletos_cancelar" class="btn btn-prev boton-primario" data-original-title="" title="Volver al listado" onclick="cancelarPreviewBoletos();">
                        <?php echo lang('atras'); ?><i class="icon-arrow-left"></i>
                    </button>
                </div>
                <div class="btn-group" name="btn_emitir_boleto">
                    <button name="btn_generar_boletos_aceptar" class="btn btn-success btn-next boton-primario" data-original-title="" title="Confirmar boletos" onclick="generarBoletos();">
                        <?php echo lang('aceptar'); ?><i class="icon-arrow-right icon-on-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div> 
    <div id="areaTablasVistaImprimir" style="display: none;">
        <input type="hidden" name="remesa_creada" value="">        
        <div class="row">
            <div class="col-md-12 center proceso-correcto">
                <i class="ace-icon fa icon-ok-circle  green" style="font-size: 200px;"></i>
                <h4 class="blue"><?php echo lang('boletos_emitidos_correctamente'); ?></h4>
            </div>
        </div>
        <div class="row" style="margin-top: 64px;">
            <div class="col-md-3 col-xs-12">&nbsp;</div>
            <div class="col-md-3 col-xs-12">
                <center>
                    <button class="btn btn-lg btn-primary"  onclick="imprimirRemesa();">
                        <i class="icon-print bigger-160"></i>
                        <?php echo lang("imprimir_boletos"); ?>
                    </button>
                </center>
            </div>
            <div class="col-md-3 col-xs-12">
                <center>
                    <button class="btn btn-lg btn-success"  onclick="descargarRemessa();">
                        <i class="icon-save bigger-160"></i>
                        <?php echo lang("descargar_remessa"); ?>
                    </button>
                </center>
            </div>
            <div class="col-md-3 col-xs-12">&nbsp;</div>
        </div>
        <div class="row-fluid wizard-actions" >
            <div class="col-md-12 col-xs-12 right">           
                <div class="btn-group" name="btn_emitir_boleto">
                    <button name="btn_generar_boletos" class="btn btn-success btn-next  boton-primario" data-original-title="" title="<?php echo lang('volver_emision_boletos'); ?>" onclick="verListadoBoletosEmitidos();">
                        <?php echo lang("finalizar"); ?>
                    </button>
                </div>    
            </div>
        </div>
    </div>
</div>

<script>
showFacturantes();
</script>

