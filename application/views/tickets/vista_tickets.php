<script src="<?php echo base_url('assents/js/tickets/tickets.js');?>"></script>
<script>
    var columns = JSON.parse('<?php echo $columns?>');
    var langFrm = <?php echo $langFrm; ?>;
</script>
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
        right: 24px;
        text-align: left;
        top: 18px;
        z-index: 1000;
    }
</style>
<div class="col-md-12 col-xs-12">
    <div id="areaTablas">
        <?php $tmpl=array ( 'table_open'=>'
                <table id="tbl_tickets" cellpadding="0" cellspacing="0" border="0"
                    class="table table-striped table-bordered table-condensed " oncontextmenu="return false"
                    onkeydown="return false" style="width:100% !important;">');
        $this->table->set_template($tmpl); $this->table->set_heading(array('', '', '', '', '', '', ''));
        echo $this->table->generate(); ?>
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
                        </tr>
                        <tr>
                            <td>
                                <select name="filtro_estado" class="select_chosen" style="width: 286px;">
                                    <option value="-1">(<?php echo lang("todos"); ?>)</option>
                                    <option value="1" selected="true"><?php echo lang("tikets_estado_Abierto"); ?></option>
                                    <option value="2"><?php echo lang("tikets_estado_En Espera"); ?></option>
                                    <option value="3"><?php echo lang("tikets_estado_Cerrado"); ?></option>
                                    <option value="4"><?php echo lang("tikets_estado_Proceso"); ?></option>
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
                            <td><?php echo lang("area") ?></td>
                        </tr>
                        <tr>
                            <td>
                                <select name="filtro_area" class="select_chosen" style="width: 286px;">
                                    <option value="-1">(<?php echo lang("todos"); ?>)</option>
                                    <option value="3"><?php echo lang('sistemas') ?></option>
                                    <option value="30"><?php echo lang("soporte"); ?></option>
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
                            <td><?php echo lang("prioridad"); ?></td>
                        </tr>
                        <tr>
                            <td>
                                <select name="filtro_prioridad" style="width: 286px;" class="select_chosen">
                                    <option value="-1">(<?php echo lang("todos") ?>)</option>
                                    <option value="1"><?php echo lang("tikets_prioridad_Baja"); ?></option>
                                    <option value="2"><?php echo lang("tikets_prioridad_Media"); ?></option>
                                    <option value="3"><?php echo lang("tikets_prioridad_Alta"); ?></option>
                                    <option value="4"><?php echo lang("tikets_prioridad_Critica"); ?></option>
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
                                    <input name="filtro_fecha_desde" value="" class="date-picker" type="text" readyonly="true" style="margin-right: 0px; width: 96px;">
                                    <span class="input-group-addon" style="padding: 3px 6px;">
                                        <i class="icon-calendar bigger-110"></i>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input name="filtro_fecha_hasta" value="" class="date-picker" type="text" readyonly="true" style="margin-right: 0px; width: 96px;">
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