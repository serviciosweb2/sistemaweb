<link rel="stylesheet" href="<?php echo base_url('assents/css/tel-master/intlTelInput.css')?>"/>
<script src="<?php echo base_url('assents/js/librerias/tel-master/intlTelInput.js')?>"></script>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal.css');?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal-bs3patch.css');?>"/>
<script src="<?php echo base_url()?>assents/js/librerias/bootstrap-modal/bootstrap-modal.js"></script>
<script src="<?php echo base_url()?>assents/js/librerias/bootstrap-modal/bootstrap-modalmanager.js"></script>
<script src="<?php echo base_url('assents/theme/assets/js/inputMask/jquery.inputmask.js')?>"></script>
<style>
    #gritter-notice-wrapper{
        z-index: 90000  !Important;
    }
    
    table .texto {
        /*position: absolute !important;*/
        display: block !important;
    }
    
    table .test {
        position: absolute !important;
        z-index: 30 !important;
        display: none;
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
<script>    
    var columns = <?php echo $columns?>;
    var fechaDesde = '<?php echo $fechaDesde?>';
    var fechaHasta = '<?php echo $fechaHasta?>';
    var codCurso = '<?php echo $codCurso?>';
    var cod_plan_academico = '<?php echo $cod_plan_academico?>';
    var codigo = '<?php echo $codigo?>';
    var titulo = '<?php echo $titulo?>';    
    var cod_tipo_periodo = '<?php echo $cod_tipo_periodo?>';
    var clausulaFechas = '<?php echo $clausulaFechas?>';
    var desdeInscripcionesYbajas = '<?php echo $desdeInscripcionesYbajas?>';
    var lang = <?php echo $lang?>;
</script>
<script src="<?php echo base_url('assents/js/reportes/bajas.js');?>"></script>

<div class="col-md-12 col-xs-12">
        <div id="areaTablas">
        <?php 
            $tmpl=array ( 
            'table_open'=>'<table id="reporteBajas" width="100%" class="table table-striped table-condensed  table-bordered"  oncontextmenu="return false"
            onkeydown="return false">'); 
            $this->table->set_template($tmpl); 
            $this->table->set_heading(array('','','','','','','')); 
            echo $this->table->generate();
        ?>
    </div>
</div>


<div style="bottom: 0px; height: 100%; left: 0px; position: fixed; width: 100%; z-index: 20; display: none;" name="contenedorPrincipal">    
</div>
<div name="container_menu_filters_bajas" style="position: absolute; bottom: 0px;">
    <div id="div_table_filters" class="table_filter" name="div_table_filters_bajas" style="display: none; padding-left: 12px; padding-right: 12px;">
        <table style="width: 256px;">
            
            <tr>
                <td><?php echo lang("fecha_baja"); ?></td>
            </tr>
            <tr>
                <td>
                    <select id="fecha_emision">
                        <option value="-1" selected="selected"><?php echo lang("filtrar_por"); ?></option>
                        <option value="1"><?php echo lang("mayor_que"); ?></option>
                        <option value="2"><?php echo lang("menor_que"); ?></option>
                        <option value="3"><?php echo lang("entre_"); ?></option>
                        <option value="4"><?php echo lang("es_igual_a"); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="text" id="fechaDesde" value="<?php echo $fechaDesde ?>" style="width: 75px;">
                    <span id="span_y_" style="display: none;">y</span>
                    <input type="text" id="fechaHasta" value="<?php echo $fechaHasta ?>" style="width: 75px;">
                </td>    
            </tr>
            <tr>
                <td><?php echo lang("cod_alumno"); ?></td>
            </tr>
            <tr>
                <td><input type="number"  min="0" id="cod_alumno"></td>
            </tr>
            
            <tr>
                <td><?php echo lang("cod_matricula_periodo"); ?></td>
            </tr>
            <tr>
                <td><input type="number"  min="0" id="cod_mat_periodo"></td>
            </tr>
            
            <tr style="display:none">
                <td><?php echo lang("cod_plan_academico"); ?></td>
            </tr>
            <tr style="display:none">
                <td><input type="number"  min="0" value='<?php echo $cod_plan_academico?>' id="cod_plan_academico"></td>
            </tr>
            
            <tr style="display:none">
                <td><?php echo lang("cod_tipo_periodo"); ?></td>
            </tr>
            <tr style="display:none">
                <td><input type="number"  min="0" value="<?php echo $cod_tipo_periodo?>" id="cod_tipo_periodo"></td>
            </tr>
                        
            <tr>
                <td><?php echo lang("curso"); ?></td>
            </tr>
            <tr>
                <td>
                    <select id="cursos" class="select_chosen" style="width: 302px;">
                        
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;">
                    <button class="btn btn-sm btn-success" type="button" name="btnBuscar" onclick="filtrar();" style="margin-top: 16px;">
                        <?php echo lang("buscar"); ?>
                    </button>
                    <button class="btn btn-sm btn-warning" type="button" name="btnLimpiar" onclick="limpiar();" style="margin-top: 16px;">
                        <?php echo lang("limpiar"); ?>
                    </button>
                </td>
            </tr>
        </table>
    </div>
</div>
<form name="frm_exportar" action="<?php echo base_url()."reportes/listarReporteBajas" ?>" target="new_target" method="POST">
    
    <input type="hidden" value="" name="length">
    <input type="hidden" value="" name="start">
    <input type="hidden" value="" name="iSortCol_0">
    <input type="hidden" value="" name="sSortDir_0">
    <input type="hidden" value="" name="search">
    <input type="hidden" value="" name="cod_alumno">
    <input type="hidden" value="" name="cod_tipo_periodo">
    <input type="hidden" value="" name="clausulaFechas">
    <input type="hidden" value="" name="fechaDesde">
    <input type="hidden" value="" name="fechaHasta">
    <input type="hidden" value="" name="codCurso">
    <input type="hidden" value="" name="cod_mat_periodo">
    <input type="hidden" value="" name="cod_plan_academico">
    <input type="hidden" value="" name="tipo_reporte">
    <input type="hidden" value="exportar" name="action">
    
</form>
