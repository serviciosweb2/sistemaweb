<?php
/**
 * Created by PhpStorm.
 * User: braulio
 * Date: 06/06/16
 * Time: 15:19
 */
?>
<script>
    var idioma = '<? echo get_idioma()?>';
</script>
<link rel="stylesheet" href="<?php echo base_url('assents/css/reportes/vista_rentabilidad.css')?>" >
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-datetimepicker/bootstrap-datetimepicker.css')?>" >
<link rel="stylesheet" href="<?php echo base_url('assents/css/daterangepicker.css')?>" >

<div class="page-header">
    <!--Titulo-->
    <h1>
        <?php echo lang('utilidad');?>
        <span class="pull-right">
            <span class="help-button" data-rel="tooltip" data-placement="top" title="<?php echo lang('click_derecho_sobre_un_egreso_modificar_subrubro');?>">?</span>
            <!--<a id="fullscreen" class="btn btn-white btn-info" data-rel="tooltip" title="Pantalla completa">
                <i class="icon-fullscreen"></i>
            </a>-->
            <a id="print_btn" class="btn btn-white btn-info" data-rel="tooltip" title="<?php echo lang('imprimir');?>">
                <i class="icon-print"></i>
            </a>
        </span>
        <small class="filial">IGA <?php echo $this->session->userdata['filial']['nombre']; ?></small>
    </h1>
</div>

<div class="col-xs-12">
    <div class="row">

        <!-- PORCENTAJE -->
        <div class="col-sm-12 col-xs-12 no-padding center detalles">
            <h1><span id="porc_total"></span></h1>
        </div>

        <div class="col-sm-3 col-xs-12 no-padding"></div>

        <!-- TOTAL -->
        <div class="col-sm-6 col-xs-12 no-padding center detalles">
            <h1><small id="total" class="right"></small></h1>
        </div>

        <!-- DATE RANGE -->
        <div class="col-sm-3 col-xs-12 no-padding fecha">
            <div class="input-group pull-right">
                <span class="input-group-addon">
                    <i class="icon-calendar bigger-110"></i>
                </span>

                <input class="form-control" type="text" name="date-range-picker" id="id-date-range-picker-1" />
            </div>
        </div>

    </div>
</div>

<div class="col-sm-6 col-xs-12 no-padding-left">
    <!--WIDGET INGRESOS-->
    <div class="widget-container-col">
        <div class="widget-box widget-color-green light-border">
            <div class="widget-header">
                <h4 class="widget-title"><?php echo lang('INGRESOS');?></h4>
            </div>
            <div class="widget-body">
                <div class="widget-main">
                    &nbsp; <span id="ingreso" class="pull-right"></span>
                </div>
            </div>
        </div>
    </div>

    <!--DATA TABLE INGRESOS-->
    <div class="widget-color-green">
        <div class="table-header widget-header white">
            <?php echo lang('detalle');?>
        </div>
        <table id="tabla_ingresos" width="100%" class="table table-striped table-bordered table-hover">
           <thead></thead>
            <tbody></tbody>
        </table>
    </div>

</div>


<div class="col-sm-6 col-xs-12 no-padding">
    <!--WIDGET EGRESOS-->
    <div class="widget-container-col">
        <div class="widget-box widget-color-red light-border">
            <div class="widget-header">
                <h4 class="widget-title"><?php echo lang('EGRESOS');?></h4>
            </div>
            <div class="widget-body">
                <div class="widget-main">
                    &nbsp; <span id="egreso" class="pull-right"></span>
                </div>
            </div>
        </div>
    </div>

    <!--DATA TABLE INGRESOS-->
    <div class="widget-color-red">
        <div class="table-header widget-header white">
            <?php echo lang('detalle');?>
        </div>
        <table id="tabla_egresos" width="100%" class="table table-striped table-bordered table-hover">
            <thead></thead>
            <tbody></tbody>
        </table>
    </div>

</div>

<div id="responsive" class="modal fade" tabindex="-1" data-width="760" style="display: none;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title"><?php echo lang("editar_movimiento_caja_subrubro")?> </h4>
        <span id="cod_mov"></span>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <h4><?php echo lang("subrubro")?></h4>
                <select id="mySelect" class="select-chosen" style="width: 208px;">
                    <option value="nada"><?php echo lang('seleccione_opcion') ?></option>
                </select>

            </div>

        </div>
    </div>
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-default"><?php echo lang('cerrar') ?></button>
        <button type="button" class="btn btn-primary" id="aceptarCambioSub"><?php echo lang('guardar') ?></button>
    </div>
</div>

<script src="<?php echo base_url()?>assents/js/moment.js"></script>
<script src="<?php echo base_url()?>assents/js/daterangepicker.js"></script>
<script src="<?php echo base_url()?>assents/js/reportes/vista_rentabilidad.js"></script>