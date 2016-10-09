<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-datetimepicker/bootstrap-datetimepicker.css')?>" >
<link rel="stylesheet" href="<?php echo base_url('assents/css/daterangepicker.css')?>" >

<style>
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
        right: 66px;
        text-align: left;
        top: 18px;
        z-index: 1000;
    }
    .filtro-fecha > input{
        margin: 0px !important;
        width: 175px !important;
        height: 20px !important;
    }
</style>

<script>    
    var columns = <?php echo $columns;?>;
</script>

<div class="col-md-12 col-xs-12" name="reporte_facebook">
    <!--filtros personalizados-->
    <div name="container_menu_filters_temp">
        <div id="div_table_filters" class="table_filter" name="div_table_filters">
            <table style="width: 100%;">
                <tr>
                    <td>
                        <table style="width: 100%;">
                            <tr>
                                <td><?php echo lang("campana") ?></td>
                            </tr>
                            <tr>
                                <td>
                                    <select name="filtro_campana" class="select_chosen" id="filtro_campana">
                                        <option value="-1">(<?php echo lang("todos"); ?>)</option>
                                        <?php foreach ($campanas_filter as $campana){ ?> 
                                        <option value="<?php echo $campana['codigo']; ?>">
                                            <?php echo $campana['nombre'];?>
                                        </option>    
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><?php echo lang("fecha"); ?></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="input-group pull-left filtro-fecha">
                                        <span class="input-group-addon">
                                            <i class="icon-calendar bigger-110"></i>
                                        </span>
                                        <input class="form-control" type="text" name="date-range-picker" id="id-date-range-picker-1">
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center;">
                        <button class="btn btn-sm btn-success" type="button" name="btnBuscar" onclick="listar();" ><?php echo lang("buscar"); ?></button>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    
    <div class="col-md-12 col-xs-12">
        <div id="areaTablas">
            <?php $tmpl=array ( 
                'table_open'=>'<table id="facebook-table" width="100%" class="table table-striped table-condensed  table-bordered"  oncontextmenu="return false"
                onkeydown="return false">'); $this->table->set_template($tmpl);
                $this->table->set_heading(array('','','',''));
                echo $this->table->generate();
            ?>
        </div>
    </div>
</div>
<div style="bottom: 0px; height: 100%; left: 0px; position: fixed; width: 100%; z-index: 20; display: none;" name="contenedorPrincipal"></div>

<form name="frm_exportar" action="<?php echo base_url()."reportes/listar_facebook_ads_campanas" ?>" target="new_target" method="POST">
    <!--<input type="hidden" value="" name="tipo_contacto">-->
    <input type="hidden" value="" name="campana">
    <input type="hidden" value="" name="fecha_desde">
    <input type="hidden" value="" name="fecha_hasta">
    <input type="hidden" value="" name="tipo_reporte">    
    <input type="hidden" value="" name="iSortCol_0">
    <input type="hidden" value="" name="sSortDir_0">
    <input type="hidden" value="" name="iDisplayLength">
    <input type="hidden" value="" name="iDisplayStart">
    <input type="hidden" value="" name="sSearch">
    <input type="hidden" value="exportar" name="action">
</form>

<script src="<?php echo base_url()?>assents/js/moment.js"></script>
<script src="<?php echo base_url()?>assents/js/daterangepicker.js"></script>
<script src="<?php echo base_url('assents/js/reportes/facebook_ads_campanas.js');?>"></script>
