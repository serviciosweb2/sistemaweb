<script src="<?php echo base_url('assents/js/cupones/cupones.js'); ?>"></script>
<script>
    var columns = JSON.parse('<?php echo $columns?>');
    var lang_cupones = <?php echo $lang ?>;
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
                <table id="tbl_cupones_landing" cellpadding="0" cellspacing="0" border="0" 
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
                            <td><?php echo lang("curso") ?></td>
                        </tr>
                        <tr>
                            <td>
                                <select name="filtro_curso_interes" class="select_chosen">
                                    <option value="-1">(<?php echo lang("todos"); ?>)</option>
                                    <?php foreach ($arrCursos as $curso){ ?> 
                                    <option value="<?php echo $curso['codigo'] ?>">
                                        <?php echo $curso['nombre'] ?>
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
                                    <input name="filtro_fecha_desde" value="<?php echo $filtro_fecha_desde ?>" class="date-picker" type="text"
                                           readyonly="true" style="margin-right: 0px; width: 96px;">
                                    <span class="input-group-addon" style="padding: 3px 6px;">
                                        <i class="icon-calendar bigger-110"></i>
                                    </span>
                                </div>
                            </td>                            
                            <td>
                                <div class="input-group">
                                    <input name="filtro_fecha_hasta" value="<?php echo $filtro_fecha_hasta ?>" class="date-picker" type="text" 
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
<form name="frm_exportar" action="<?php echo base_url()."cupones/listar" ?>" target="new_target" method="POST">
    <input type="hidden" value="" name="fecha_desde">
    <input type="hidden" value="" name="fecha_hasta">
    <input type="hidden" value="" name="cod_curso">
    <input type="hidden" value="" name="tipo_reporte">    
    <input type="hidden" value="" name="iSortCol_0">
    <input type="hidden" value="" name="sSortDir_0">
    <input type="hidden" value="" name="iDisplayLength">
    <input type="hidden" value="" name="iDisplayStart">
    <input type="hidden" value="" name="sSearch">
    <input type="hidden" value="exportar" name="action">
</form>