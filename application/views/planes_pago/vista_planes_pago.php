<script>
    var columns =  <?php echo $columns ?>;
    var menuJson = <?php echo $menuJson ?>;
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
        padding: 8px 0;
        position: absolute;
        right: 66px;
        text-align: left;
        top: 18px;
        z-index: 1000;
    }
    
    .fecha_date_picker{
        margin: 0 0px !important;
    }
</style>
<script src="<?php echo base_url("assents/js/librerias/datatables/jquery.dataTables.min.js")?>"></script>
<script src="<?php echo base_url('assents/js/planes_pago/planes_pago.js');?>"></script>
<script src="<?php echo base_url('assents/js/librerias/bootstrap-modal/bootstrap-modalmanager.js')?>"></script>
<script src="<?php echo base_url('assents/js/librerias/bootstrap-modal/bootstrap-modal.js')?>"></script>
<link rel="stylesheet" href="<?php echo base_url('assents/css/planes_pago/planes_pago.css')?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal.css')?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal-bs3patch.css')?>"/>
<div class="col-md-12 col-xs-12">
    <div id="areaTablas" class="table-responsive">
        <?php $tmpl=array ( 'table_open'=>'
        <table id="administracionPlanesPago" class="table table-striped table-condensed table-bordered table-hover"
        oncontextmenu="return false" onkeydown="return false" style="width:100% !important;">');
        $this->table->set_template($tmpl);
        $this->table->set_heading(array('','', '', '','','','',''));
        echo $this->table->generate(); ?>
    </div>
</div>
<div id="confirmaEliminar" class="modal  fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-body modal-confirmacion">
        <p></p>
    </div>
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn">
            <?php echo lang('cancelar');?>
        </button>
        <button type="button" data-dismiss="modal" id="btn-ok-cambio-estado" class="btn btn-primary">
            <?php echo lang('continuar');?>
        </button>
    </div>
</div>
<div id="filtros_datatable" name="container_menu_filters_temp">
    <div id="div_table_filters" class="table_filter" name="div_table_filters" style="display: none;">
        <div class="row" style="padding-bottom: 0px;">
            <div class="col-md-6">
                <?php echo lang("fecha_inicio_desde"); ?>           
            </div>
            <div class="col-md-6">
                <?php echo lang("fecha_inicio_hasta"); ?>            
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="input-group">
                    <input name="fecha_inicio_desde" value="" class="date-picker fecha_date_picker" type="text" readyonly="true" style="width: 96px;">
                    <span class="input-group-addon" style="padding: 3px 6px;">
                    <i class="icon-calendar bigger-110"></i>
                </span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <input name="fecha_inicio_hasta" value="" class="date-picker fecha_date_picker" type="text" readyonly="true" style="width: 96px;">
                    <span class="input-group-addon" style="padding: 3px 6px;">
                    <i class="icon-calendar bigger-110"></i>
                </span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <?php echo lang("fecha_vigencia_desde"); ?>
            </div>
            <div class="col-md-6">
                <?php echo lang("fecha_vigencia_hasta"); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="input-group">
                    <input name="fecha_vigencia_desde" value="" class="date-picker fecha_date_picker" type="text" readyonly="true" style="width: 96px;">
                    <span class="input-group-addon" style="padding: 3px 6px;">
                        <i class="icon-calendar bigger-110"></i>
                    </span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <input name="fecha_vigencia_hasta" value="" class="date-picker fecha_date_picker" type="text" readyonly="true" style="width: 96px;">
                    <span class="input-group-addon" style="padding: 3px 6px;">
                        <i class="icon-calendar bigger-110"></i>
                    </span>
                </div>
            </div>
        </div>        
        <div class="row">
            <div class="col-md-12">
                <?php echo lang("curso"); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <select name="filtro_plan_academico" style="width: 300px;" class="select_chosen">
                    <option value="-1">(<?php echo lang("todos"); ?>)</option>
                    <?php foreach ($arrPlanesAcademicos as $plan){ ?> 
                    <option value="<?php echo $plan['codigo'] ?>">
                        <?php echo $plan["nombre_".get_idioma()] ?>
                    </option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <?php echo lang("modalidad"); ?>
            </div>
            <div class="col-md-6">
                <?php echo lang("periodo"); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <select name="filtro_modalidad" style="width: 134px;" class="select_chosen">
                    <option value="-1">(<?php echo lang("todos") ?>)</option>
                    <option value="normal"><?php echo lang("modalidad_normal"); ?></option>
                    <option value="intensiva"><?php echo lang("modalidad_intensiva") ?></option>
                </select>
            </div>
            <div class="col-md-6">
                <select name="filtro_periodo" style="width: 134px;" class="select_chosen">
                    <option value="-1">(<?php echo lang("todos"); ?>)</option>
                    <option value="1"><?php echo lang("1_PERIODO"); ?></option>
                    <option value="2"><?php echo lang("2_PERIODO"); ?></option>
                    <option value="3"><?php echo lang("3_PERIODO"); ?></option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php echo lang("estado"); ?>
            </div>
        </div>        
        <div class="row">
            <div class="col-md-12">
                <select name="filtro_baja" style="width: 300px;" class="select_chosen">
                    <option value="-1">(<?php echo lang("todos"); ?>)</option>
                    <option value="0"><?php echo lang("HABILITADO"); ?></option>
                    <option value="1"><?php echo lang("INHABILITADO"); ?></option>
                </select>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="col-md-12">
                <center>
                    <button class="btn btn-primary boton-primario " onclick="listarPlanesAcademicos();">
                        <?php echo lang('filtrar') ?>
                    </button>
                </center>
            </div>
        </div>
    </div>
    <div style="bottom: 0px; height: 100%; left: 0px; position: fixed; width: 100%; z-index: 20; display: none;" name="contenedorPrincipal"></div>
</div>
<form name="exportar" method="POST" action="<?php echo base_url("planespago/listar") ?>" target="new_target_1">
    <input type="hidden" name="iSortCol_0" value="">
    <input type="hidden" name="sSortDir_0" value="">
    <input type="hidden" name="iDisplayLength" value="">
    <input type="hidden" name="iDisplayStart" value="">
    <input type="hidden" name="sSearch" value="">
    <input type="hidden" name="fecha_inicio_desde" value="">
    <input type="hidden" name="fecha_inicio_hasta" value="">
    <input type="hidden" name="fecha_vigencia_desde" value="">
    <input type="hidden" name="fecha_vigencia_hasta" value="">
    <input type="hidden" name="plan_academico" value="">
    <input type="hidden" name="modalidad" value="">
    <input type="hidden" name="periodo" value="">
    <input type="hidden" name="baja" value="">
    <input type="hidden" name="formato" value="">
</form>