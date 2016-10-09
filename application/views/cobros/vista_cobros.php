<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal.css') ?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal-bs3patch.css') ?>"/>
<script src="<?php base_url() ?>assents/js/librerias/bootstrap-modal/bootstrap-modal.js"></script>
<script src="<?php base_url() ?>assents/js/librerias/bootstrap-modal/bootstrap-modalmanager.js"></script>
<script src="<?php echo base_url('assents/js/cobros/cobros.js'); ?>"></script>
<script src="<?php echo base_url('assents/js/impresiones.js') ?>"></script>
<script>
    var tiene_proveedores = <?php echo $contratos_tarjeta ? "true" : "false" ?>;
    var moneda_simbolo = '<?php echo $moneda_simbolo ?>';
    var separador_decimal = '<?php echo $separador_decimal ?>';
</script>

<style>
    .popover{
        max-width: none!important;
    }

    .verDetalleDeudor{
        cursor: pointer !important;
    }

    .buscador{        
        margin-top: 12px !important;        
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
        padding: 8px 8px;
        position: absolute;
        right: 24px;
        text-align: left;
        top: 18px;
        z-index: 1000;
    }
    
</style>

<div id="modalDtalle" class="modal fade" data-width="auto" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="blue bigger"><?php echo lang('detalles'); ?></h4>
    </div>
    <div class="modal-body">
        <div class="row" id="contenido">
        </div>
        <div class="row">
            <div class="table-responsive contenedorTabla">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" id="btn-ok-cambio-estado" class="btn btn-primary"><?php echo lang('continuar'); ?></button>
    </div>
</div>

<!---- cobros -->

<div class="col-md-12 col-xs-12 vista-cobro ">
    <div class="row">
        <div id="areaTablas" class="table-responsive">
            <?php
            $tmpl = array('table_open' => '
                <table id="administracionCobros" cellpadding="0" cellspacing="0"
                border="0" class="table table-striped table-bordered table-condensed" oncontextmenu="return false"
                onkeydown="return false">');
            $this->table->set_template($tmpl);
            $arrColumnas = array();
            $this->table->set_heading('', '', '', '', '', '', '', '','', '', '', '');
            echo $this->table->generate();
            ?>
        </div>
    </div>
</div>

<!---- cobros -->

<div class="col-sm-12  vista-archivos-consolidar hide ">
    <div class="row">
        <div class="col-sm-1">
            <a href="#" class="btn-back-message-list" onclick="volver();">
                <i class="icon-arrow-left blue bigger-110 middle"></i>
                <b class="bigger-110 middle"><?php echo lang('volver')?></b>
            </a>
        </div>            
    </div>    
    <div class="row"> 
        <div class="col-sm-6  ">
            <div class="widget-box ">
                <div class="widget-header">
                    <h4 class="widget-title"><?php echo lang('cargar_archivo_resumen')?></h4>
                </div>
                <div class="widget-body">
                    <div class="widget-main">
                        <div class="row subir-archivo">
                            <form id="subir-resumen" method="post" action="sendRetorno">
                                <div class="row padding-6">
                                    <div class="col-xs-6">
                                            <?php echo lang('provedor_resumen');?>
                                        <select name="provedores" class="form-control">
                                            <option></option>    
                                            <option value="2">CIELO</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <input  id="file-retorno" type="file" name="archivoretorno[]" multiple />
                                        <div class="hr hr-12 dotted"></div>                                       
                                    </div>
                                </div>
                                <div>                                    
                                    <div class="col-md-12 align-right "> <button type="submit" class="btn btn-sm btn-success  btn-confirmar-resumen"><?php echo lang('procesar_extractos');?></button></div>
                                </div>
                            </form> 
                        </div>
                        <div class="row resultado-subir-archivo hide">
                            <div class="col-md-12 col-xs-12 ">
                                <div class="row">
                                    <div class="col-md-12 resultado-subida"></div>
                                </div> 

                                <div class="row">
                                    <div class="col-md-12"><button class="btn btn-success ok-reset">OK</button></div>
                                </div> 
                            </div>  
                        </div>
                    </div>
                </div>
            </div>
        </div> 
        <div class="col-sm-6 ">
            <div class="widget-box ">
                <div class="widget-header">
                    <h4 class="widget-title"><?php echo lang('resumenes_cargados')?></h4>
                </div>
                <div class="widget-body">
                    <div class="widget-main">
                        <table id="ArchivoResumenesCargados" class="table table-striped table-condensed table-bordered table-hover dataTable no-footer">
                            <thead>
                                <tr>
                                    <th><?php echo lang('nombre_archivo'); ?></th>
                                    <th><?php echo lang('matriz')?></th>
                                    <th><?php echo lang('secuencial')?></th>
                                    <th><?php echo lang('fecha_inicio')?></th>
                                    <th><?php echo lang('fecha_fin')?></th>
                                </tr>                                
                            </thead> 
                        </table>
                    </div>
                </div>
            </div>
        </div>  
    </div>
</div>
<div name="container_menu_filters_temp">
    <div id="div_table_filters" class="table_filter" name="div_table_filters">
        <table style="width: 100%;">
            <tr>
                <td>
                    <table style="width: 100%;">
                        <tr>
                            <td><?php echo lang("estado") ?></td>
                            <td><?php echo lang("medio") ?></td>
                        </tr>
                        <tr>
                            <td style="padding-left: 4px;">
                                <select name="filtro_estado" class="select_chosen" style="width: 138px;">
                                    <option value="-1">(<?php echo lang("todos"); ?>)</option>
                                    <?php foreach ($arrEstados as $estado){ ?> 
                                    <option value="<?php echo $estado; ?>"
                                            <?php if ($estado == 'confirmado'){ ?>selected="true"<?php } ?>>
                                        <?php echo lang($estado); ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td style="padding-left: 4px;">
                                <select name="filtro_medio" class="select_chosen" style="width: 138px;">
                                    <option value="-1">(<?php echo lang("todos"); ?>)</option>
                                    <?php foreach($arrMedios as $medio){ ?> 
                                    <option value="<?php echo $medio['codigo'] ?>">
                                        <?php echo lang($medio['medio']); ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>            
            <tr>
                <td>
                    <table style="width: 100%">
                        <tr>
                            <td><?php echo lang("caja"); ?></td>
                            <td><?php echo lang("saldo") ?></td>
                        </tr>
                        <tr>
                            <td>
                                <select name="filtro_caja" class="select_chosen" style="width: 138px;">
                                    <option value="-1">(<?php echo lang("todos") ?>)</option>
                                    <?php foreach ($arrCajas as $caja){ ?> 
                                    <option value="<?php echo $caja['codigo'] ?>">
                                        <?php echo ucwords($caja['nombre']) ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td>
                                <select name="filtro_saldo" class="select_chosen" style="width: 138px;">
                                    <option value="-1">(<?php echo lang("todos"); ?>)</option>
                                    <option value="1"><?php echo lang("consaldo"); ?></option>
                                    <option value="0"><?php echo lang("sinsaldo"); ?></option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table style="width: 100%;">
                        <tr>
                            <td><?php echo lang("filtrar_fechas"); ?></td>
                        </tr>
                        <tr>
                            <td>
                                <select name="filtro_tipo_fecha" style="width: 278px;" class="select_chosen" onchange="filtro_tipo_fecha_change();">
                                    <option value="sin_fechas">(<?php echo lang("todos") ?>)</option>
                                    <option value="periodo" selected="true"><?php echo lang("por_periodos") ?></option>
                                    <option value="fecha"><?php echo lang("por_fechas") ?></option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table style="width: 100%" name="filtro_tipo_fecha_periodo">
                        <tr>
                            <td><?php echo lang("mes"); ?></td>
                            <td><?php echo lang("anio"); ?></td>
                        </tr>
                        <tr>
                            <td>
                                <select name="filtro_periodo_mes" style="width: 138px;" class="select_chosen">
                                    <?php foreach ($arrMeses as $mes => $nombreMes){ ?>
                                    <option value="<?php echo str_pad($mes, 2, "0", STR_PAD_LEFT); ?>"
                                            <?php if ($mes == date("m")){ ?>selected="true"<?php } ?>>
                                        <?php echo $nombreMes ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td>
                                <select name="filtro_periodo_anio" style="width: 138px;" class="select_chosen">
                                    <?php for ($i = 2004; $i <= date("Y"); $i++){ ?> 
                                    <option value="<?php echo $i ?>" 
                                        <?php if ($i == date("Y")){ ?>selected="true" <?php } ?>>
                                        <?php echo $i; ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table style="width: 100%; display: none;" name="filtro_tipo_fecha_fecha">
                        <tr>
                            <td>
                                <?php echo lang("fecha_desde"); ?>
                            </td>
                            <td>
                                <?php echo lang("fecha_hasta"); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="input-group">
                                    <input name="filtro_fecha_desde" value="<?php echo date("01/m/Y") ?>" class="date-picker" type="text"
                                           readyonly="true" style="margin-right: 0px; width: 96px;">
                                    <span class="input-group-addon" style="padding: 3px 6px;">
                                        <i class="icon-calendar bigger-110"></i>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input name="filtro_fecha_hasta" value="<?php echo date("d/m/Y") ?>" class="date-picker" type="text" 
                                           readyonly="true" style="margin-right: 0px; width: 96px;">
                                    <span class="input-group-addon" style="padding: 3px 6px;">
                                        <i class="icon-calendar bigger-110"></i>
                                    </span>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;">
                    <button class="btn btn-sm btn-success" type="button" name="btnBuscar" onclick="listar();"><?php echo lang("buscar"); ?></button>
                </td>
            </tr>
        </table>
    </div>
</div>
<div style="bottom: 0px; height: 100%; left: 0px; position: fixed; width: 100%; z-index: 20; display: none;" name="contenedorPrincipal"></div>
<form name="exportar" method="POST" action="<?php echo base_url("cobros/listar") ?>" target="new_target_1">
    <input type="hidden" name="iSortCol_0" value="">
    <input type="hidden" name="sSortDir_0" value="">
    <input type="hidden" name="iDisplayLength" value="">
    <input type="hidden" name="iDisplayStart" value="">
    <input type="hidden" name="sSearch" value="">
    <input type="hidden" name="action" value="exportar">
    <input type="hidden" name="tipo_reporte" value="exportar">
    <input type="hidden" name="fecha_desde_t" value="">
    <input type="hidden" name="fecha_hasta_t" value="">
    <input type="hidden" name="periodo_mes" value="">
    <input type="hidden" name="periodo_anio" value="">
    <input type="hidden" name="selectEstado" value="">
    <input type="hidden" name="medio_pago" value="">
    <input type="hidden" name="caja" value="">
    <input type="hidden" name="saldo" value="">
</form>