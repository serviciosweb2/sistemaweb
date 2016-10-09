<script src="<?php echo base_url('assents/js/configuracion/configFacturacion.js') ?>"></script>

<style>    
    .widget-body{        
        max-height: 400px !important;
        overflow: auto;        
    }    

    #widgetPuntosVentas .table-responsive{
        max-height: 300px !important;
        overflow: auto;        
    }
    table span:hover{
        cursor: pointer;
    }
</style>
<script>
    var edita_mora = <?php echo $edita_mora ? "true" : "false"; ?>;
</script>

<div class="col-md-12 col-xs-12">
    <div id="areaTablas" class="">
        <div class="tabbable">
            <?php
            $data['tab_activo'] = 'configFacturacion';
            $this->load->view('configuracion/vista_tabs', $data);
            ?>               
            <div class="tab-content">
                <div id="facturacion" class="tab-pane in active">                    
                    <div class="row">
                        <!--COLUMNA DE LA IZQUIERDA-->
                        <div class="col-md-6">                            
                            <div class="row">                                
                                <div class="col-md-12 col-xs-12 widget-container-span ui-sortable">
                                    <div class="widget-box" style="opacity: 1; z-index: 0;" id="widgetImpuesto">
                                        <div class="widget-header  header-color-orange">
                                            <h6>
                                                <i class="icon-sort"></i>
                                                <?php echo lang('impuestos') ?>
                                            </h6>
                                            <div class="widget-toolbar">
                                                <a href="#" data-reload="widgetImpuesto">
                                                    <i class="icon-refresh"></i>
                                                </a>
                                                <a href="#" data-action="collapse">
                                                    <i class="icon-chevron-up"></i>
                                                </a>
                                            </div>
                                            <div class="widget-toolbar no-border">
                                                <button class="btn  btn-xs" type="button" name="nuevaPeriodicidad" data-nuevoImpuesto="true">
                                                    <i class="icon-ok"></i>
                                                    <?php echo lang('nuevo') ?>                                           
                                                </button>
                                            </div>
                                        </div>
                                        <div class="widget-body">
                                            <div class="widget-body-inner" style="display: block;">
                                                <div class="widget-main">
                                                    <div class="table-responsive">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">                                
                                <div class="col-md-12 col-xs-12 widget-container-span ui-sortable">
                                    <div class="widget-box" style="opacity: 1; z-index: 0;" id="widgetFacturantes">
                                        <div class="widget-header  header-color-purple">
                                            <h6>
                                                <i class="icon-sort"></i>
                                                <?php echo lang('facturantes') ?>
                                            </h6>
                                            <div class="widget-toolbar">
                                                <a href="#" data-reload="widgetFacturantes">
                                                    <i class="icon-refresh"></i>
                                                </a>
                                                <a href="#" data-action="collapse">
                                                    <i class="icon-chevron-up"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="widget-body">
                                            <div class="widget-body-inner" style="display: block;">
                                                <div class="widget-main">
                                                    <div class="table-responsive">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-xs-12 widget-container-span ui-sortable" id="widgetCajas">
                                    <div class="widget-box" style="opacity: 1; z-index: 0;">
                                        <div class="widget-header  header-color-orange">
                                            <h6>
                                                <i class="icon-sort"></i>
                                                <?php echo lang('cajas'); ?>
                                            </h6>
                                            <div class="widget-toolbar">
                                                <a href="#" data-reload="widgetCajas">
                                                    <i class="icon-refresh"></i>
                                                </a>
                                                <a href="#" data-action="collapse">
                                                    <i class="icon-chevron-up"></i>
                                                </a>
                                            </div>
                                            <div class="widget-toolbar no-border">
                                                <button class="btn btn-xs" type="button" name="nuevaCaja" data-nuevaCaja="1">
                                                    <i class="icon-ok"></i>
                                                    <?php echo lang('nueva_caja'); ?>
                                                </button>
                                            </div>
                                        </div>                                        
                                        <div class="widget-body">
                                         <div class="widget-body-inner" style="display: block;">
                                                <div class="widget-main">
                                                    <div class="tabbable">
                                                        <ul class="nav nav-tabs" id="TabCajas">
                                                            <li class="active">
                                                                <a data-toggle="tab" href="#activo">
                                                                    <i class="icon-circle light-green middle"></i>
                                                                    <?php echo lang('activo') ?>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a data-toggle="tab" href="#inactivo">
                                                                    <?php echo lang('inactivo') ?>
                                                                    <i class="icon-circle light-red middle"></i>
                                                                </a>
                                                            </li>
                                                        </ul>

                                                        <div class="tab-content">
                                                            <div id="activo" class="tab-pane in active">
                                                                <div class="table-responsive">
                                                                </div>
                                                            </div>

                                                            <div id="inactivo" class="tab-pane">
                                                                <div class="table-responsive">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">                                
                                <div class="col-md-12 col-xs-12 widget-container-span ui-sortable">
                                    <div class="widget-box" style="opacity: 1; z-index: 0;" id="widgetSugerenciaBajas">
                                        <div class="widget-header  header-color-purple">
                                            <h6>
                                                <i class="icon-sort"></i>
                                                <?php echo lang('sugerencias_de_baja') ?>
                                            </h6>
                                            <div class="widget-toolbar">
                                                <a href="#" data-action="collapse">
                                                    <i class="icon-chevron-up"></i>
                                                </a>
                                            </div>
                                            <div class="widget-toolbar no-border">
                                            </div>
                                        </div>
                                        <div class="widget-body">
                                            <div class="widget-body-inner" style="display: block;">
                                                <div class="widget-main">
                                                    <div class="row">
                                                        <div class="form-group col-md-12 col-xs-12">
                                                            <div class="col-md-5 col-xs-5"><?php echo lang('cantidad__cuotas_debe') ?></div>
                                                            <div class="col-md-4 col-xs-4"><hr></div>
                                                            <div class="col-md-3 col-xs-3">
                                                                <select name="MesesBajaDeudores" onchange="guardarValorSugerencia(this);">
                                                                    <?php
                                                                    for ($x = 1; $x <= 100; $x++) {
                                                                        $selected = $MesesBajaDeudores == $x ? 'selected' : '';
                                                                        echo '<option value="' . $x . '" ' . $selected . '>' . $x . '</option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-md-12 col-xs-12">
                                                            <div class="col-md-5 col-xs-5"><?php echo lang('meses_vencidas_suegerencia') ?></div>
                                                            <div class="col-md-4 col-xs-4"><hr></div>
                                                            <div class="col-md-3 col-xs-3">
                                                                <select name="mesesVencidaBaja" onchange="guardarValorSugerencia(this);">
                                                                    <?php
                                                                    $selected = '';
                                                                    for ($x = 1; $x <= 100; $x++) {
                                                                        $selected = $mesesVencidaBaja == $x ? 'selected' : '';
                                                                        echo '<option value="' . $x . '" ' . $selected . '>' . $x . '</option>';
                                                                    } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>                            
                            <div class="row">                                
                                <div class="col-md-12 col-xs-12 widget-container-span ui-sortable">
                                    <div class="widget-box" style="opacity: 1; z-index: 0;" id="widgetFacturacionFragmentada">
                                        <div class="widget-header header-color-blue">
                                            <h6>
                                                <i class="icon-sort"></i>
                                                <?php echo lang('facturacion_segmentada') ?>
                                            </h6>
                                            <div class="widget-toolbar">
                                                <a href="#" data-action="collapse">
                                                    <i class="icon-chevron-up"></i>
                                                </a>
                                            </div>
                                            <div class="widget-toolbar no-border">
                                            </div>
                                        </div>
                                        <div class="widget-body">
                                            <div class="widget-body-inner" style="display: block;">
                                                <div class="widget-main">
                                                    <form id="configuracion_facturacion_segmentada"> 
                                                        <div class="row">
                                                            <div class="col-sm-7 col-xs-7"><?php echo lang('activar_facturacion_nominada');?></div>
                                                            <div class="col-sm-1 col-xs-1"><hr></div>
                                                            <div class="col-md-4 col-xs-4">
                                                                <label>
                                                                    <input name="facturacion_nominada" id="facturacionNominada" class="ace ace-switch ace-switch-6 chosen-default" type="checkbox" <?php echo $facturacion_nominada == 0 ? '' : 'checked' ?>>
                                                                    <span class="lbl"></span>
                                                                </label>
                                                            </div>
                                                        </div>                                                                                           

                                                        <div class="row">
                                                            <div class="col-sm-7 col-xs-7"><?php echo lang('activar_facturacion_segmentada');?></div>
                                                            <div class="col-sm-1 col-xs-1"><hr></div>
                                                            <div class="col-md-4 col-xs-4">
                                                                <label>
                                                                    <input name="facturacion_segmentada" id="facturacionSegmentada" class="ace ace-switch ace-switch-6 chosen-default" type="checkbox" <?php echo $facturacion_segmentada == 0 ? '' : 'checked' ?>>
                                                                    <span class="lbl"></span>
                                                                </label>
                                                            </div>
                                                        </div>                                                                                           

                                                        <div class="row tipo_numerico">
                                                            <div class="col-sm-7 col-xs-7"><?php echo lang('monto_por_segmento_factura');?></div>
                                                            <div class="col-sm-1 col-xs-1"><hr></div>
                                                            <div class="col-md-4 col-xs-4">
                                                                <input type="text" class="form-control" name="monto_segmento" value="<?php echo isset($monto_segmento) ?  $monto_segmento : '';?>">
                                                            </div>
                                                        </div>                                                                                           

                                                        <div class="row">
                                                            <div class="col-md-4 col-md-offset-8">
                                                                <label class="pull-right">
                                                                    <button type="button" class="btn btn-success btn-save" name="enviar" onclick="guardarConfiguracionFacturacionSegmentada(event)">
                                                                        <i class="icon-ok"></i>
                                                                        <?php echo lang("guardar"); ?>
                                                                    </button>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>                            
                        </div>   
                        <!--COLUMNA DE LA DERECHA-->
                        <div class="col-md-6">                            
                            <div class="row">
                                <div class="col-md-12 col-xs-12 widget-container-span ui-sortable" id="widgetPuntosVentas">
                                    <div class="widget-box" style="opacity: 1; z-index: 0;">
                                        <div class="widget-header  header-color-blue">
                                            <h6>
                                                
                                                <i class="icon-sort"></i>
                                                <?php echo lang('puntos_venta') ?>
                                            </h6>
                                              
                                            <div class="widget-toolbar">
                                                <?php if ($pais == 1) { ?>
                                                <button class="btn  btn-xs" type="button" name="actualizarPtosVta" aling="left" onclick="actualizarPuntosVenta();">
                                                   Actualizar  
                                                </button>
                                           <?php } ?>
                                                <a href="#" data-reload="widgetPuntosVentas">
                                                    
                                                </a>
                                                <a href="#" data-action="collapse">
                                                    <i class="icon-chevron-up"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="widget-body">
                                            <div class="widget-body-inner" style="display: block;">
                                                <div class="widget-main">
                                                    <div class="tabbable">
                                                        <ul class="nav nav-tabs" id="TabPuntoVenta">
                                                            <li class="active">
                                                                <a data-toggle="tab" href="#activo">
                                                                    <i class="icon-circle light-green middle"></i>
                                                                    <?php echo lang('activo') ?>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a data-toggle="tab" href="#inactivo">
                                                                    <?php echo lang('inactivo') ?>
                                                                    <i class="icon-circle light-red middle"></i>
                                                                </a>
                                                            </li>
                                                        </ul>

                                                        <div class="tab-content">
                                                            <div id="activo" class="tab-pane in active">
                                                                <div class="table-responsive">
                                                                </div>
                                                            </div>

                                                            <div id="inactivo" class="tab-pane">
                                                                <div class="table-responsive">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-xs-12 widget-container-span ui-sortable" id="widgetMoras">
                                    <div class="widget-box" style="opacity: 1; z-index: 0;">
                                        <div class="widget-header  header-color-blue">
                                            <h6>
                                                <i class="icon-sort"></i>
                                                <?php echo lang('administrador_de_moras'); ?>
                                            </h6>
                                            <div class="widget-toolbar">
                                                <a href="#" data-reload="widgetMoras">
                                                    <i class="icon-refresh"></i>
                                                </a>
                                                <a href="#" data-action="collapse">
                                                    <i class="icon-chevron-up"></i>
                                                </a>
                                            </div>
                                            <div class="widget-toolbar no-border" <?php if (!$edita_mora){ ?>style="display: none;"<?php } ?>>
                                                <button class="btn btn-xs" type="button" name="nuevaMora" data-nuevacaja="1">
                                                    <i class="icon-ok"></i>
                                                    <?php echo lang('nueva_mora'); ?>
                                                </button>
                                            </div>
                                            <div class="widget-toolbar no-border" <?php if (!$edita_mora){ ?>style="display: none;"<?php } ?>>
                                                <button class="btn btn-xs" type="button" name="resetear_moras" onclick="resetear_moras();">
                                                    <i class="icon-ok"></i>
                                                    Reiniciar Moras
                                                </button>
                                            </div>
                                        </div>
                                        <div class="widget-body">
                                            <div class="widget-body-inner" style="display: block;">
                                                <div class="widget-main">
                                                    <div class="table-responsive">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-xs-12 widget-container-span ui-sortable" id="widgetMorasCursosCortos">
                                    <div class="widget-box" style="opacity: 1; z-index: 0;">
                                        <div class="widget-header  header-color-red3">
                                            <h6>
                                                <i class="icon-sort"></i>
                                                <?php echo lang('administrador_de_moras_cursos_cortos'); ?>
                                            </h6>
                                            <div class="widget-toolbar">
                                                <a href="#" data-reload="widgetMorasCursosCortos">
                                                    <i class="icon-refresh"></i>
                                                </a>
                                                <a href="#" data-action="collapse">
                                                    <i class="icon-chevron-up"></i>
                                                </a>
                                            </div>
                                            <div class="widget-toolbar no-border" <?php if (!$edita_mora){ ?>style="display: none;"<?php } ?>>
                                                <button class="btn btn-xs" type="button" name="nuevaMoraCursosCortos" data-nuevacaja="1">
                                                    <i class="icon-ok"></i>
                                                    <?php echo lang('nueva_mora'); ?>
                                                </button>
                                            </div>
                                            <div class="widget-toolbar no-border" <?php if (!$edita_mora){ ?>style="display: none;"<?php } ?>>
                                                <button class="btn btn-xs" type="button" name="resetear_moras" onclick="resetear_moras();">
                                                    <i class="icon-ok"></i>
                                                    Reiniciar Moras
                                                </button>
                                            </div>
                                        </div>
                                        <div class="widget-body">
                                            <div class="widget-body-inner" style="display: block;">
                                                <div class="widget-main">
                                                    <div class="table-responsive">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">                                
                                <div class="col-md-12 col-xs-12 widget-container-span ui-sortable">
                                    <div class="widget-box" style="opacity: 1; z-index: 0;" id="widgetConceptos">
                                        <div class="widget-header  header-color-purple">
                                            <h6>
                                                <i class="icon-sort"></i>
                                                <?php echo lang('conceptos') ?>
                                            </h6>

                                            <div class="widget-toolbar">
                                                <a href="#" data-reload="widgetConceptos">
                                                    <i class="icon-refresh"></i>
                                                </a>
                                                <a href="#" data-action="collapse">
                                                    <i class="icon-chevron-up"></i>
                                                </a>
                                            </div>
                                            <div class="widget-toolbar no-border">
                                                <button class="btn  btn-xs" type="button" name="nuevoConcepto" data-nuevoConcepto="true">
                                                    <i class="icon-ok"></i>
                                                    <?php echo lang('nuevo') ?>  
                                                </button>
                                            </div>
                                        </div>
                                        <div class="widget-body">
                                            <div class="widget-body-inner" style="display: block;">
                                                <div class="widget-main">
                                                    <div class="table-responsive">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">                                
                                <div class="col-md-12 col-xs-12 widget-container-span ui-sortable">
                                    <div class="widget-box" style="opacity: 1; z-index: 0;" id="widgetTerminalesPos">
                                        <div class="widget-header  header-color-orange">
                                            <h6>
                                                <i class="icon-sort"></i>
                                                <?php echo lang('TARJETA') ?>
                                            </h6>

                                            <div class="widget-toolbar">
                                                <a href="#" data-reload="widgetTerminalesPos">
                                                    <i class="icon-refresh"></i>
                                                </a>
                                                <a href="#" data-action="collapse">
                                                    <i class="icon-chevron-up"></i>
                                                </a>
                                            </div>
                                            <div class="widget-toolbar no-border">

                                            </div>
                                        </div>

                                        <div class="widget-body">
                                            &nbsp&nbsp&nbsp
                                            <span class="lbl"  style="font-size: 15px;" ><?php echo lang("terminales") ?></span>

                                            <button class="btn  btn-xs" type="button" name="nuevaTerminal"aling="right" data-nuevoConcepto="true">
                                                <i class="icon-ok"></i>
                                                <?php echo lang('nuevo') ?>  
                                            </button>

                                            <div class="widget-body-inner" style="display: block;">
                                                <div class="widget-main">
                                                    <div class="table-responsive">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                    <?php if ($pais == 2) { ?>
                            <div class="row">                                
                                <div class="col-md-12 col-xs-12 widget-container-span ui-sortable">
                                    <div class="widget-box" style="opacity: 1; z-index: 0;" id="widgetTerminalesPos">
                                        <div class="widget-header  header-color-orange">
                                            <h6>
                                                <?php echo lang('etiquetas_boleto') ?>
                                            </h6>

                                            <div class="widget-toolbar">
                                                <a href="#" data-reload="widgetTerminalesPos">
                                                    <i class="icon-refresh"></i>
                                                </a>
                                                <a href="#" data-action="collapse">
                                                    <i class="icon-chevron-up"></i>
                                                </a>
                                            </div>
                                            <div class="widget-toolbar no-border">

                                            </div>
                                        </div>

                                        <div class="widget-body">
                                            &nbsp&nbsp&nbsp

                                            <div class="widget-body-inner" style="display: block;">


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
                          <button class='btn btn-success btn-save' onclick='enviarEtiquetas()'>Guardar preferencias.</button>





                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                    <?php } ?>


                        </div>
                    </div>
                    <?php if ($pais == 2) { ?>
                        <hr>
                        <div class="row">


                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <input id="habilitar_boleto_bancario" class="ace ace-checkbox-2" type="checkbox" name="habilitar_boleto_bancario"
                                       <?php if ($utilizaBoletoBancario && count($cuentas_bancarias) > 0) { ?> checked="true" <?php } ?> onclick="utilizarBoletosBancarios();">
                                <span class="lbl" style="font-size: 18px;"><?php echo lang("habilitar_boleto_bancario") ?></span>
                            </div>
                        </div>
                        <br>
                        <div class="row"<?php if (!$utilizaBoletoBancario || count($cuentas_bancarias) == 0) { ?> style="display: none;" <?php } ?> name="div_configuracion_boleto_bancario">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12" id="div_descripcion_cuentas">
                                        <?php
                                        foreach ($cuentas_bancarias as $codBanco => $cuentaBancaria) {
                                            foreach ($cuentaBancaria['cuentas'] as $codCuenta => $cuenta) {
                                                foreach ($cuenta['boletos_bancarios'] as $boleto) {
                                                    ?> 
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <a style="cursor: pointer;" onclick="editarCuentaBancaria(<?php echo $codBanco ?>, <?php echo $codCuenta ?>, <?php echo $boleto['carteira'] ?>);">
                                                                <?php
                                                                echo $cuentaBancaria['nombre'] . " " . lang("conta") . " " . $cuenta['conta'] . " " . lang("carteira") . " " . $boleto['carteira'];
                                                                $check = $cuenta['estado'] == "habilitada";
                                                                ?>
                                                            </a>
                                                            <label>
                                                                <input class="ace ace-switch ace-switch-6" type="checkbox" <?php if ($check) { ?>checked="" <?php } ?> name="cuenta_habilitada"
                                                                       onclick="cambiarEstadoCuenta(<?php echo $codBanco ?>, <?php echo $codCuenta ?>, this);">
                                                                <span class="lbl"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                            }
                                        } ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <button id="agregaCuentaBancaria" class="btn btn-success btn-save" data-last="Finish" onclick="agregar_cuenta_bancaria();"><?php echo lang('agregar_cuenta'); ?></button>
                                    </div>
                                </div>
                            </div>
                            <div>

                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
