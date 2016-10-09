<script>
    var menuJson = <?php echo $menuJson?>;
    var columns = <?php echo $columns?>;
    var facturaMail = <?php echo $envio_factura_mail ?>;
    var exportar_facturacion = <?php echo $facturacion_electronica ? "true" : "false"; ?>;
</script>
<script src="<?php echo base_url('assents/js/facturacion/facturacion.js');?>"></script>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-select.min.css')?>"/>
<script src="<?php echo base_url()?>assents/js/bootstrap-select.min.js"></script>
<script src="<?php echo base_url('assents/js/impresiones.js') ?>"></script>
<style>
    #administracionFacturacion_length{
        width: 680px;
    }
    
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
</style>
<div class="col-md-12 col-xs-12">
    <div id="areaTablas">
        <div id="area_botones_superiores"></div>
            <?php $tmpl=array ( 'table_open'=>'
            <table id="administracionFacturacion" cellpadding="0" cellspacing="0"
            border="0" width="100%" class="table table-striped table-bordered table-condensed" oncontextmenu="return false"
            onkeydown="return false">'); 
            $this->table->set_template($tmpl);
            $this->table->set_heading('','','','','','','','','','','');
            echo $this->table->generate(); ?>        
    </div>
    <?php if ($facturacion_electronica){ ?>
    <div id="areaExportar" style="display: none;">
        <button class="btn btn-primary boton-primario " onclick="volvarAFacturas();">
            <i class="icon-reply"></i>
            <?php echo lang("volver"); ?>
        </button>
        <?php $tmpl=array ( 'table_open'=>'
        <table id="tbl_exportar" cellpadding="0" cellspacing="0"
        border="0" width="100%" class="table table-striped table-bordered table-condensed" oncontextmenu="return false"
        onkeydown="return false">'); 
        $this->table->set_template($tmpl);
        $this->table->set_heading('','','','','','','','','','','');
        echo $this->table->generate(); ?>
    </div>
    <form name="frm_exportar" method="POST" action="<?php echo base_url('facturacion/exportar'); ?>">
    </form>
    <?php } ?>    
</div>

<div id="detaleFactura" class="modal fade" data-width="auto" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="blue bigger"><?php echo lang('detalles'); ?></h4>
        </div>
        <div class="modal-body overflow-visible">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th><?php echo lang('descripcion'); ?></th>
                                <th><?php echo lang('importe'); ?></th>
                            </tr>
                        </thead>
                        <tbody id="tbodyDetalle">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>    
        <div class="modal-footer">
        </div>    
    </div>
</div>
<div name="container_menu_filters_temp">
    <div id="div_table_filters" class="table_filter" name="div_table_filters" style="display: none;">
        <table style="width: 256px;">            
            <tr>
                <td style="padding-right: 10px; text-align: right;">
                    <?php echo lang("fecha_desde"); ?>
                </td>
                <td>
                    <div class="input-group">
                        <input name="filtro_facturas_fecha_desde" value="" class="date-picker" type="text" readyonly="true" style="margin-right: 0px; width: 96px;">
                        <span class="input-group-addon" style="padding: 3px 6px;">
                            <i class="icon-calendar bigger-110"></i>
                        </span>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="padding-right: 10px; text-align: right;">
                    <?php echo lang("fecha_hasta"); ?>
                </td>
                <td>
                    <div class="input-group">
                        <input name="filtro_facturas_fecha_hasta" value="" class="date-picker" type="text" readyonly="true" style="margin-right: 0px; width: 96px;">
                        <span class="input-group-addon" style="padding: 3px 6px;">
                            <i class="icon-calendar bigger-110"></i>
                        </span>
                    </div>
                </td>
            </tr>
            <?php if ($facturacion_electronica){ ?>
            <tr>
                <td style="padding-right: 10px; text-align: right;">
                    <?php echo lang("tipo_factura"); ?>
                </td>
                <td>
                    <select name="filtro_facturas_tipo_factura" style="width: 138px;">
                        <option value="-1">(<?php echo lang("todos") ?>)</option>
                        <option value="15"><?php echo lang("producto"); ?></option>
                        <option value="16"><?php echo lang("servicio"); ?></option>
                    </select>
                </td>
            </tr>
            <?php } else { ?> 
            <tr>
                <td>
                    <input type="hidden" name="filtro_facturas_tipo_factura" value="-1">
                </td>
            </tr>    
            <?php } ?>
            <tr>
                <td style="padding-right: 10px; text-align: right;"><?php echo lang("estado") ?></td>
                <td>
                    <select name="filtro_estado" style="width: 138px;">
                        <?php foreach ($arrEstados as $key => $estado){ ?> 
                        <option value="<?php echo $key ?>"
                                <?php if ($key == $estadoSeleccionar){ ?> selected="true" <?php } ?>>
                            <?php echo $estado ?>
                        </option>    
                        <?php } ?>
                    </select>
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
    <div style="bottom: 0px; height: 100%; left: 0px; position: fixed; width: 100%; z-index: 20; display: none;" name="contenedorPrincipal"></div>
</div>
<?php if ($facturacion_electronica){ ?>
<div class="table_filter" id="div_table_filters" name="div_table_filters_exportar" style="z-index: 1000; top: 170px; width: 320px; padding-left: 10px; display: none; right: 38px;"> 
    <div class="filtro_opciones" style="border: none">
        <div class="row">
            <div class="col-md-6">
                <?php echo lang("fecha_desde"); ?>           
            </div>
            <div class="col-md-6">
                <?php echo lang("fecha_hasta"); ?>            
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="input-group">
                    <input name="filtro_fecha_desde" value="<?php echo formatearFecha_pais(date("Y-m-d")) ?>" class="date-picker" type="text" readyonly="true" style="width: 96px;">
                    <span class="input-group-addon" style="padding: 3px 6px;">
                    <i class="icon-calendar bigger-110"></i>
                </span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <input name="filtro_fecha_hasta" value="<?php echo formatearFecha_pais(date("Y-m-d")); ?>" class="date-picker" type="text" readyonly="true" style="width: 96px;">
                    <span class="input-group-addon" style="padding: 3px 6px;">
                    <i class="icon-calendar bigger-110"></i>
                </span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php echo lang("tipo_factura"); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <select name="filtro_tipo_factura" style="width: 94%;">
                    <option value="15"><?php echo lang("producto"); ?></option>
                    <option value="16"><?php echo lang("servicio"); ?></option>
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
<?php } ?>