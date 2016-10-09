<script>
    var idioma = '<?php echo get_idioma()?>';
    var menuJson = <?php echo $menuJson ?>;
    var transferir_archivos = '<?php echo $trasferir_archivo ?>';
    var cod_alumno = <?php echo $cod_alumno ?>;
    var cod_matricula = <?php echo $cod_matricula ?>;
<?php
if(isset($embed) && isset($matriculas)){
?>
    var matriculasEmitir = JSON.parse(<?php echo json_encode($matriculas);?>);
    var embed = true;
    var desde = <?php echo $desde; ?>;
    var hasta = <?php echo $hasta; ?>;
<?php
}
?>

</script>
<script src="<?php echo base_url('assents/js/boleto_bancario/boleto_bancario.js'); ?>"></script>
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
    
    .embed-emitir{
        overflow:scroll;
        padding:40px;
    }
</style>


<div
    <?php 
    if(isset($embed)){
    ?>
        class="col-md-12 col-xs-12 embed-emitir"
    <?php
    } else {
    ?>

        class="col-md-12 col-xs-12"
    <?php 
    }
    ?>
>

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
                                           <input id="juros-cobrar" name="juros-cobrar" type="checkbox" class="ace" onclick='checkForm(this)'>
                                               <span class="lbl">Cobrar juros</span>
                                      </label>
                                      <div class="radio">
                                          <label>
                                               <input id='juros-tipo-banco' name="juros-tipo" type="radio" class="ace" value='banco' onclick='checkForm(this)'>
                                                   <span class="lbl">Definido pelo banco (FACP)</span>
                                          </label>
                                      </div>
                          
                                      <div class="radio">
                                          <label>
                                               <input id='juros-tipo-iga' name="juros-tipo" type="radio" 
                                                      class="ace" value='iga' onclick='checkForm(this)'>
                                                <span class="lbl">Definido pela IGA  <input id='juros-valoriga' 
                                                      name='juros-valoriga' class='input-mini' type='number' />%</span>
                                          </label>
                                      </div>
                                  </div> 
                              </div>
                              <div class='form-group'>
                                  <div class="checkbox">
                                      <label>
                                           <input id='multa-cobrar' name="multa-cobrar" 
                                                  type="checkbox" class="ace" onclick='checkForm(this)'>
                                               <span class="lbl">Cobrar multa</span>
                                      </label>
                      
                                      <ul>
                                              <label>
                                                      <span class="lbl">
                                                      Valor da multa <input id='multa-valor' name='multa-valor' 
                                                      class='input-mini' type='number'/> %
                                                      </span>
                                              </label>
                                              <br />
                                              <label>
                                                      <span class="lbl">
                                                      Cobrar multa apos <input id='multa-dias' name='multa-dias' 
                                                      class='input-mini' type='number' /> dias de atraso.
                                                      </span>
                                              </label>
                                      </ul>
                                  </div>
                          
                              </div>
                              <div class='form-group'>
                         
                                  <div class="radio">
                                      <label>
                                           <input id='venc-tipo-nao' 
                                                  name="venc-tipo" type="radio" 
                                                  class="ace" value='nao' 
                                                  onclick='checkForm(this);'>
                                              <span class="lbl">Nao receber aposovencimiento</span>
                                      </label>
                                 </div>
                          
                                  <div class="radio">
                                      <label>
                                           <input id='venc-tipo-banco' name="venc-tipo" type="radio" class="ace" value='banco'
                                                  onclick='checkForm(this);'>
                                              <span class="lbl">Aposovencimiento somente no banco emisor</span>
                                      </label>
                                      <div class="checkbox">
                                          <label>
                                              <input id='venc-limite' name="venc-limite" type="checkbox" class="ace"
                                                  onclick='checkForm(this);'>
                                                  <span class="lbl">
                                                  Nao receber apos <input id='venc-dias' name='venc-dias' 
                                                                          class='input-mini' type='number'/> dias de atraso.
                                                  </span>
                                          </label>
                                      </div>
                                  </div>
                              </div>
                              <div class='form-group'>
                                  <div class="checkbox">
                                      <label>
                                           <input id='inclu-apos' name="inclu-apos" type="checkbox" class="ace"
                                           onclick='checkForm(this)'>
                                               <span class="lbl">
                                                  Inclusao no serasa apos <input id='inclu-dias' name='inclu-dias' 
                                                                                 class='input-mini' type='number' /> 
                                                  dias de atraso.
                                               </span>
                                      </label>
                                  </div>
                              </div>
                              <div class='form-group'>
                                  <div class="radio">
                                      <label>
                                           <input id='valorBoleto-desconto' name="valorBoleto" type="radio" class="ace" value='desconto'>
                                               <span class="lbl">Valor com desconto</span>
                                      </label>
                                  </div>
                                  <div class="radio">
                                      <label>
                                           <input id='valorBoleto-cheio' name="valorBoleto" type="radio" class="ace" value='cheio' >
                                               <span class="lbl">Valor cheio</span>
                                      </label>
                                  </div>
                                  <div class="checkbox">
                                      <label>
                                           <input id='descontoFixo' name="descontoFixo" type="checkbox" class="ace">
                                               <span class="lbl">
                                                  Descontro valor fixo ate a data de vencimiento
                                              </span>
                                      </label>
                                  </div>
                              </div> 
                            <div class='form-group'>
                                <div class="checkbox">
                                    <label>
                                         <input id="cb-enviarRemessa" name="cb-enviarRemessa" type="checkbox" class="ace">
                                         <span class="lbl">Enviar remessa ao banco</span>
                                    </label>
                                </div>
                            </div>
                          </form>
                          <button class='btn btn-success btn-save' onclick='enviarEtiquetas()'>Guardar nuevas preferencias.</button>

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
        <?php
        if($embed){        
        ?>
        <div style='height:120px;overflow-y:scroll'>
        <?php } ?>
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


        <?php
        if($embed){        
        ?>
        </div>
        <?php } ?>



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
    <div id="areaTablasBoletosConfiormarBaja" style="display: none;">
        <?php
        $tmpl = array('table_open' => '
        <table id="boletosBancariosConfirmarBaja" cellpadding="0" cellspacing="0"
        border="0" width="100%" class="table table-striped table-bordered table-condensed" oncontextmenu="return false"
        onkeydown="return false">');
        $this->table->set_template($tmpl);
        $arrColDef = array();
        foreach ($arrColumnas as $key => $columna) {
            if ($key == 1 || $key == 3 || $key == 5) {
                $arrColDef[] = $columna['nombre'];
            }
        }
        $this->table->set_heading($arrColDef);
        echo $this->table->generate();
        ?>
        <div class="row">
            <div class="col-md-12 col-xs-12">
                <center>
                    <div class="btn-group" name="btn_emitir_boleto">
                        <button name="btn_canclear_boletos_cancelar" class="btn btn-info boton-primario" data-original-title="" title="<?php echo lang('volver_al_listado'); ?>" onclick="verListadoBoletosEmitidos();">
                            <?php echo lang('cancelar'); ?>
                        </button>
                    </div>
                    <div class="btn-group" name="btn_emitir_boleto">
                        <button name="btn_cancelar_boletos_aceptar" class="btn btn-info boton-primario" data-original-title="" title="<?php echo lang('confirmar_cancelacion_boletos'); ?>" onclick="confirmaBajaBoletos();">
                            <?php echo lang('aceptar'); ?>
                        </button>
                    </div>
                </center>
            </div>
        </div>
    </div>    
</div>
<div style="display: none;" name="div_template_detalle_boleto">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="blue bigger"><?php echo lang("detalle_boleto_bancario"); ?></h4>
        </div>
        <div class="modal-body overflow-visible">
            <div class="row">
                <div class="col-md-12 col-xs-12">
                    <table  cellpadding="0" cellspacing="0" border="0" width="100%" onkeydown="return false"
                            class="table table-striped table-bordered table-condensed" oncontextmenu="return false">
                        <thead>
                            <tr>
                                <th><?php echo lang("cuenta_corriente_de_emision"); ?></th>
                                <th><?php echo lang("fecha_vencimiento"); ?></th>
                                <th><?php echo lang("importe"); ?></th>
                            </tr>
                        </thead>
                        <tbody name="tbody_detalle_ctacte_original">                                
                        </tbody>
                    </table>
                </div>
            </div>
            <div name="movimientos_historico">
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <h5><b><?php echo lang("seguimiento_historico"); ?></b></h5>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <table cellpadding="0" cellspacing="0" border="0" width="100%" onkeydown="return false"
                               class="table table-striped table-bordered table-condensed" oncontextmenu="return false">
                            <thead>
                                <tr>
                                    <th><?php echo lang("estado"); ?></th>
                                    <th><?php echo lang("fecha"); ?></th>
                                </tr>
                            </thead>
                            <tbody name="tbody_detalle_movimientos_historicos">                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div name="imputaciones_boleto" >
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <h5><b><?php echo lang('imputacione_realizadas'); ?></b></h5>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <table cellpadding="0" cellspacing="0" border="0" width="100%" onkeydown="return false"
                               class="table table-striped table-bordered table-condensed" oncontextmenu="return false">
                            <thead>
                                <tr>
                                    <th><?php echo lang("descripcion_ctacte_facturar"); ?></th>
                                    <th><?php echo lang("fecha_vencimiento"); ?></th>
                                    <th><?php echo lang("importe"); ?></th>
                                    <th><?php echo lang("imputado"); ?></th>
                                </tr>
                            </thead>
                            <tbody name="tbody_imputaciones_boleto">                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">&nbsp;</div>
    </div>
</div>
<div id="filtros_datatable" name="container_menu_filters_temp">
    <div id="div_table_filters" class="table_filter" name="div_table_filters" style="display: none;">
        <table style="width: 300px; align: center;">
            <tr>
                <td>
                    <div class="col-md-12">
                        <label><?php echo lang('fecha_desde');?></label>
                        <div class="input-group">
                            <input class="form-control date-picker fecha_date_picker" id="fecha_desde" type="text" data-date-format="dd-mm-yyyy">
                            <span class="input-group-addon">
                                <i class="icon-calendar"></i>
                            </span>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="col-md-12">
                        <label><?php echo lang('fecha_hasta');?></label>
                        <div class="input-group">
                            <input class="form-control date-picker fecha_date_picker" id="fecha_hasta" type="text" data-date-format="dd-mm-yyyy">
                            <span class="input-group-addon">
                                    <i class="icon-calendar"></i>
                            </span>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;">
                    <button class="btn btn-sm btn-info" type="button" name="btn_default" onclick="filtrar(1);"><?php echo lang('mes_actual');?></button>
                    <button class="btn btn-sm btn-info" type="button" name="btnBuscar" onclick="filtrar(0);"><?php echo lang('filtrar');?></button>
                </td>
            </tr>
        </table>
    </div>
    <div style="bottom: 0px; height: 100%; left: 0px; position: fixed; width: 100%; z-index: 20; display: none;" name="contenedorPrincipal"></div>
</div>
<div class="table_filter" id="div_table_filters" name="div_table_filters_exportar" style="z-index: 1000; top: 172px; width: 320px; padding-left: 10px; display: none; right: 68px;"> 
    <div class="filtro_opciones" style="border: none">
        <div class="row">
            <div class="col-md-6">
                <?php echo lang("fecha_emision_desde"); ?>           
            </div>
            <div class="col-md-6">
                <?php echo lang("fecha_emision_hasta"); ?>            
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="input-group">
                    <input name="filtro_emision_fecha_desde" value="" class="date-picker" type="text" readyonly="true" style="width: 96px;">
                    <span class="input-group-addon" style="padding: 3px 6px;">
                    <i class="icon-calendar bigger-110"></i>
                </span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <input name="filtro_emision_fecha_hasta" value="" class="date-picker" type="text" readyonly="true" style="width: 96px;">
                    <span class="input-group-addon" style="padding: 3px 6px;">
                    <i class="icon-calendar bigger-110"></i>
                </span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <?php echo lang("fecha_vencimiento_desde"); ?>
            </div>
            <div class="col-md-6">
                <?php echo lang("fecha_vencimiento_hasta"); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="input-group">
                    <input name="filtro_vencimiento_fecha_desde" value="" class="date-picker" type="text" readyonly="true" style="width: 96px;">
                    <span class="input-group-addon" style="padding: 3px 6px;">
                        <i class="icon-calendar bigger-110"></i>
                    </span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <input name="filtro_vencimiento_fecha_hasta" value="" class="date-picker" type="text" readyonly="true" style="width: 96px;">
                    <span class="input-group-addon" style="padding: 3px 6px;">
                        <i class="icon-calendar bigger-110"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php echo lang("estado"); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <select name="filtro_estado">
                    <option value="-1">(<?php echo lang("todos") ?>)</option>
                    <?php foreach ($estados_boletos as $estado){ ?> 
                    <option value="<?php echo $estado ?>">
                        <?php echo lang($estado); ?>
                    </option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="col-md-12">
                <center>
                    <button class="btn btn-primary boton-primario " onclick="listarExportar();">
                        <?php echo lang('filtrar') ?>
                    </button>
                </center>
            </div>
        </div>
    </div>
</div>
<div name="contenedorPrincipalExportar" onclick="ver_ocultar_filtros();"
     style="bottom: 0px; height: 100%; left: 0px; position: fixed; width: 100%; z-index: 20; display: none;"></div>

<form name="frm_exportar" method="POST" action="<?php echo base_url("boletos/getBoletosDataTable") ?>" target="new_target_1">
    <input type="hidden" name="iSortCol_0" value="">
    <input type="hidden" name="sSortDir_0" value="">
    <input type="hidden" name="iDisplayLength" value="">
    <input type="hidden" name="iDisplayStart" value="">
    <input type="hidden" name="sSearch" value="">
    <input type="hidden" name="fecha_vencimiento_desde" value="">
    <input type="hidden" name="fecha_vencimiento_hasta" value="">
    <input type="hidden" name="fecha_emision_desde" value="">
    <input type="hidden" name="fecha_emision_hasta" value="">
    <input type="hidden" name="estado" value="">
    <input type="hidden" name="exportar" value="">
</form>

<?php
if(isset($embed) && $embed == true){
?>
<script>
showFacturantes();
</script>
<?php
}
?>


