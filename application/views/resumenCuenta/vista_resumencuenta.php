<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal.css') ?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal-bs3patch.css') ?>"/>
<script src="<?php echo base_url('assents/js/impresiones.js') ?>"></script>
<script src="<?php base_url() ?>assents/js/librerias/bootstrap-modal/bootstrap-modal.js"></script>
<script src="<?php base_url() ?>assents/js/librerias/bootstrap-modal/bootstrap-modalmanager.js"></script>

<script>
    var menuJson = <?php echo $menuJson; ?>;
    var columns = <?php echo $columns ?>;
</script>
<script src="<?php echo base_url('assents/js/resumendecuenta/resumendecuenta.js'); ?>"></script>

<style>
    .table_filter{
        margin-right: 1%;
        margin-top: 1%;
        -moz-border-bottom-colors: none;
        -moz-border-left-colors: none;
        -moz-border-right-colors: none;
        -moz-border-top-colors: none;
        background-color: #FFFFFF;
        border-bottom-color: #AFAFB6;
        border-bottom-left-radius: 4px;
        border-bottom-right-radius: 4px;
        border-bottom-style: solid;
        border-bottom-width: 1px;
        border-image-outset: 0 0 0 0;
        border-image-repeat: stretch stretch;
        border-image-slice: 100% 100% 100% 100%;
        border-image-source: none;
        border-image-width: 1 1 1 1;
        border-left-color-ltr-source: physical;
        border-left-color-rtl-source: physical;
        border-left-color-value: #AFAFB6;
        border-left-style-ltr-source: physical;
        border-left-style-rtl-source: physical;
        border-left-style-value: solid;
        border-left-width-ltr-source: physical;
        border-left-width-rtl-source: physical;
        border-left-width-value: 1px;
        border-right-color-ltr-source: physical;
        border-right-color-rtl-source: physical;
        border-right-color-value: #AFAFB6;
        border-right-style-ltr-source: physical;
        border-right-style-rtl-source: physical;
        border-right-style-value: solid;
        border-right-width-ltr-source: physical;
        border-right-width-rtl-source: physical;
        border-right-width-value: 1px;
        border-top-color: #AFAFB6;
        border-top-left-radius: 4px;
        border-top-right-radius: 4px;
        border-top-style: solid;
        border-top-width: 1px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
        cursor: default;
        position: absolute; 
        right: 20px; 
        top: 18px;
        padding: 8px 0px;
        z-index: 1000;
        text-align: left;
    }

    .verDetalleDeudor{
        cursor: pointer !important;
    }

    .popover{
        max-width:none!important;
    }

    #detalleResumen_length{
        padding-top: 12%!important;
    }

</style>

<div id="modalDtalle" class="modal fade" data-width="auto" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="blue bigger"><?php echo lang('detalles') ?></h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12 col-xs-12">
                <label><?php echo lang('imputaciones') ?>:</label>
                <table id="inputaciones" class="table table-bordered table-condensed" style="width: 100%">
                    <thead></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-xs-12">
                <label><?php echo lang('Facturas'); ?>:</label>
                <table id="facturas" class="table table-bordered table-condensed" style="width: 100%">
                    <thead></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn"><?php echo lang('cancelar')?></button>
        <button type="button" data-dismiss="modal" id="btn-ok-cambio-estado" class="btn btn-primary"><?php echo lang('continuar'); ?></button>
    </div>
</div>

<div class="col-md-12 col-xs-12" id="contenedor_administracioResumenCuenta">
    <div id="areaTablas" class="table-responsive">
        <?php
        $tmpl = array('table_open' => '
            <table id="administracioResumenCuenta" cellpadding="0" cellspacing="0"
            border="0" class="table table-striped table-bordered table-condensed" oncontextmenu="return false"
            onkeydown="return false">');
        $this->table->set_template($tmpl);
        $this->table->set_heading(array('', '', '', '', '', '', ''));
        echo $this->table->generate();
        ?>
    </div>
</div>
<div class="col-md-12 col-xs-12" id="contenedor_detalleResumen">
    <div class="row">
        <div class="col-sm-1">
            <a href="#" class="btn-back-message-list" onclick="volver();">
                <i class="icon-arrow-left blue bigger-110 middle"></i>
                <b class="bigger-110 middle"><?php echo lang('volver')?></b>
            </a>
        </div>
        <div class="col-sm-5">
            <button id="btnSaveAdvanced" class="btn btn-info btn-sm btn-save" data-last="" onclick="frm_nuevactacte();"><?php echo lang('nuevo') ?></button>
        </div>
        <div class="form-group col-sm-6 text-right">
            <?php echo lang('buscar') ?>
            <span class="input-icon input-icon-right">
                <input id="form-field-icon-2" onkeyup="buscarDetalle(this);" type="text" aria-controls="DataTables_Table_0" name="search_table">
                <i class="icon-caret-down grey" style="margin-right: 7px; cursor: pointer" name="table_filters"></i>
            </span> 
            <i id="imprimir_informe" class="icon-print grey" style="cursor: pointer" onclick="imprimirDetalleCtacte();" data-original-title="" title=""></i>
        </div>
        <div class="table_filter" id="div_table_filters" name="div_table_filters" style="z-index: 1000;"> 
            <div class="row" style="padding-bottom: 0px;min-width: 200px;">
                <div class="col-md-12">
                    <h5 class="purple"><i class="icon-filter purple"></i>&nbsp;<?php echo lang('filtros'); ?></h5>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12"> 
                    <div class="checkbox no-padding">
                        <label title="Inscriptos este año">
                            <input class="ace" id="con_saldo" type="checkbox" onchange="filtrar();" name="common_filters" value="anio">
                            <span class="lbl"><?php echo lang('nocobradas_consaldo'); ?></span></br>
                        </label>
                    </div>
                    <div class="checkbox2 no-padding">
                        <input class="ace" id="habilitadas" type="checkbox" onchange="filtrar();" name="common_filters2" value="otro">
                        <span class="lbl"><?php echo lang('habilitadas'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="areaTablas_2" class="table-responsive">
        <table class="table table-striped table-bordered " oncontextmenu="return false"
               onkeydown="return false" cellspacing="0" cellpadding="0" border="0" id="detalleResumen">
            <thead>
                <th><?php echo lang('codigo'); ?></th>
                <th><?php echo lang('descripcion'); ?></th>
                <th><?php echo lang('importe'); ?></th>
                <th><?php echo lang('saldo'); ?></th>
                <th><?php echo lang('fecha_vencimiento'); ?></th>
                <th><?php echo lang('ver_detalle'); ?></th>
                <th>filtro</th>
                <th>cod_concepto</th>
                <th>filtro2</th>
            </thead>
            <tbody>               
            </tbody>
        </table>
    </div>    
</div>
<div id="area_tablas_3" class="table_responsible" style="display: none;">
    <div class="col-sm-1" style="margin-bottom: 40px;">
        <a href="#" class="btn-back-message-list" onclick="volver();">
            <i class="icon-arrow-left blue bigger-110 middle"></i>
            <b class="bigger-110 middle"><?php echo lang("volver"); ?></b>
        </a>
    </div>
    <table class="table table-striped table-bordered" oncontextmenu="return false" onkeydown="return false" cellspaciong="0"
           border="0" id="table_descuentos_condicionados_perdidos">
        <thead>
            <tr>
                <th><?php echo lang("ALUMNO") ?></th>
                <th><?php echo lang("curso"); ?></th>
                <th><?php echo lang("fecha_perdida_descuento"); ?></th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <?php foreach ($condicionado_perdido as $condicionado){ ?> 
        <tr id="tr_<?php echo $condicionado['codigo'] ?>">
            <td><?php echo $condicionado['nombre_alumno'] ?></td>
            <td><?php echo $condicionado['nombre_curso'] ?></td>
            <td><?php echo formatearFecha_pais($condicionado['fecha_perdida_descuento']); ?></td>
            <td>
                <button class="btn btn-info btn-xs" onclick="verDescuentoPerdidoMatricula(<?php echo $condicionado['codigo'] ?>);">
                    <?php echo lang("ver_detalle"); ?>
                </button>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>