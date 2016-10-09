<div>
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
                <span class="title">Instrucciones del boleto</span>
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
    <div id="areaTablasBoletos">
        <div class="row">
            <div class="col-md-12" name="area_botones_accion"></div>
        </div>
        <?php
        $tmpl = array('table_open' => '
        <table id="boletosBancarios" cellpadding="0" cellspacing="0"
        border="0" width="100%" class="table table-striped table-bordered table-condensed" oncontextmenu="return false"
        onkeydown="return false">');
        $this->table->set_template($tmpl);
        $arrColDef = array();
        foreach ($arrColumnas as $columna) {
            $arrColDef[] = $columna['nombre'];
        }
        $this->table->set_heading($arrColDef);
        echo $this->table->generate();
        ?>
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
                    <button name="btn_generar_boletos" class="btn btn-success btn-next boton-primario" data-original-title="" title="<?php echo lang('genera_boleto'); ?>" onclick="generarBoletosInstrucciones();" disabled="true">
                        <?php echo lang('siguiente'); ?><i class="icon-arrow-right icon-on-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div id="areaFormularioInstruccionesBoleto" style="display:none;"> 
    <form id='frm_etiquetas_boleto' class='form-horizontal' role='form' style='padding-left:40px;padding-top:10px'>
        <div class='form-group'>
            <div class="checkbox">
                <label>
                     <input id="cb-cobrarJuros" name="cb-cobrarJuros" type="checkbox" class="ace" onclick='checkForm(this)'checked>
                         <span class="lbl">Cobrar juros</span>
                </label>
                <div class="radio">
                    <label>
                         <input id='rb-Juros-pelobanco' name="rb-Juros" type="radio" class="ace" value='pelobanco' onclick='checkForm(this)' checked>
                             <span class="lbl">Definido pelo banco (FACP)</span>
                    </label>
                </div>
    
                <div class="radio">
                    <label>
                         <input id='rb-Juros-pelaiga' name="rb-Juros" type="radio" 
                                class="ace" value='pelaiga' onclick='checkForm(this)'>
                          <span class="lbl">Definido pela IGA  <input id='tf-juroiga' 
                                name='tf-juroiga' class='input-mini' type='number' />%</span>
                    </label>
                </div>
            </div> 
        </div>
        <div class='form-group'>
            <div class="checkbox">
                <label>
                     <input id='cb-cobrarMulta' name="cb-cobrarMulta" 
                            type="checkbox" class="ace" onclick='checkForm(this)' checked>
                         <span class="lbl">Cobrar multa</span>
                </label>

                <ul>
                        <label>
                                <span class="lbl">
                                Valor da multa <input id='valorMulta' name='valorMulta' 
                                class='input-mini' type='number' value='2'/> %
                                </span>
                        </label>
                        <br />
                        <label>
                                <span class="lbl">
                                Cobrar multa apos <input id='multaApos' name='multaApos' 
                                class='input-mini' type='number' value='10' /> dias de atraso.
                                </span>
                        </label>
                </ul>
            </div>
    
        </div>
        <div class='form-group'>
   
            <div class="radio">
                <label>
                     <input id='rb-aposovencimiento-naoreceber' 
                            name="rb-aposovencimiento" type="radio" 
                            class="ace" value='naoreceber' 
                            onclick='checkForm(this);'>
                        <span class="lbl">Nao receber aposovencimiento</span>
                </label>
           </div>
    
            <div class="radio">
                <label>
                     <input id='rb-aposovencimiento-banco' name="rb-aposovencimiento" type="radio" class="ace" value='banco' checked
                            onclick='checkForm(this);'>
                        <span class="lbl">Aposovencimiento somente no banco emisor</span>
                </label>
                <div class="checkbox">
                    <label>
                        <input id='cb-diasatraso' name="cb-diasatraso" type="checkbox" class="ace"
                            onclick='checkForm(this);'>
                            <span class="lbl">
                            Nao receber apos <input id='tf-diasatraso' name='tf-diasatraso' 
                                                    class='input-mini' type='number' value='10' /> dias de atraso.
                            </span>
                    </label>
                </div>
            </div>
        </div>
        <div class='form-group'>
            <div class="checkbox">
                <label>
                     <input id='cb-inclusaoapos' name="cb-inclusaoapos" type="checkbox" class="ace" checked
                     onclick='checkForm(this)'>
                         <span class="lbl">
                            Inclusao no serasa apos <input id='tf-inclusaoapos' name='tf-inclusaoapos' 
                                                           class='input-mini' type='number' value='10' /> 
                            dias de atraso.
                         </span>
                </label>
            </div>
        </div>
        <div class='form-group'>
            <div class="radio">
                <label>
                     <input id='rb-valorDesconto-desconto' name="rb-valorDesconto" type="radio" class="ace" value='desconto'>
                         <span class="lbl">Valor com desconto</span>
                </label>
            </div>
            <div class="radio">
                <label>
                     <input id='rb-valorDesconto-cheio' name="rb-valorDesconto" type="radio" class="ace" value='cheio' checked>
                         <span class="lbl">Valor cheio</span>
                </label>
            </div>
            <div class="checkbox">
                <label>
                     <input id='cb-descontroVencimiento' name="cb-descontroVencimento" type="checkbox" class="ace" checked>
                         <span class="lbl">
                            Descontro valor fixo ate a data de vencimiento
                        </span>
                </label>
            </div>
        </div> 
    </form>
<!--
    Para ir usamos generarBoletosConfirma.
    Para volver, hay que hacer una funcion.
-->    

        <div class="row-fluid wizard-actions">
            <div class="col-md-12 col-xs-12  right">
                <div class="btn-group" name="btn_emitir_boleto">
                    <button name="btn_generar_boletos_cancelar" class="btn btn-prev boton-primario" data-original-title="" title="Volver al listado" onclick="cancelarInstruccionesBoletos();">
                        <?php echo lang('atras'); ?><i class="icon-arrow-left"></i>
                    </button>
                </div>
                <div class="btn-group" name="btn_emitir_boleto">
                    <button name="btn_generar_boletos_aceptar" class="btn btn-success btn-next boton-primario" data-original-title="" title="Confirmar boletos" onclick="generarBoletosConfirmar();">
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
        <div class="widget-box transparent ui-sortable-handle" id="widget-box-12">
            <div class="widget-header">
                <h4 class="widget-title lighter">Instrucciones</h4>
            </div>

            <div class="widget-body">
                <div class="widget-main padding-6 no-padding-left no-padding-right">
                    <div id="etiquetasBoletoBancario" style='padding-left:15px;' >
                    </div>
                </div>
            </div>
        </div>


        <div class='form-group'>
            <div class="checkbox">
                <label>
                     <input name="cb-enviarRemessa" type="checkbox" class="ace">
                         <span class="lbl">Enviar remessa ao banco</span>
                </label>
            </div>
        </div>
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
