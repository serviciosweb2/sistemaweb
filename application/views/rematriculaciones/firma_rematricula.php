<script>
    var columns = <?php echo $columns?>;
</script>
<script src="<?php echo base_url("assents/js/librerias/datatables/jquery.dataTables.min.js")?>"></script>
<script src="<?php echo base_url('assents/js/librerias/bootstrap-modal/bootstrap-modalmanager.js')?>"></script>
<script src="<?php echo base_url('assents/js/librerias/bootstrap-modal/bootstrap-modal.js')?>"></script>
<script src="<?php echo base_url('assents/js/impresiones.js') ?>"></script>
<script src="<?php echo base_url('assents/js/librerias/ajaxchosen/lib/ajax-chosen.js') ?>"></script>
<script src="<?php echo base_url('assents/js/chosen.jquery.js')?>"></script>

<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal.css')?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal-bs3patch.css')?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/css/matricula/matricula.css');?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/css/datepicker3.css') ?>"/>

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

    #gritter-notice-wrapper{
        z-index: 90000  !Important;
    }
</style>
<?php $filtros = array("documento"=>lang("documento"),"nombre"=>lang("nombre"),"apellido"=>lang("apellido"),"matricula"=>lang("matricula"),"comision"=>lang("comision"), "ciclo"=>lang("ciclo"),"firmo"=>lang("firmo"),"ano"=>lang("año"),"trimestre"=>lang("trimestre"),"fecha"=>lang("fecha"));?>

<div class="col-sm-12" id="top_buttons">
    <div class="btn-group">
        <button id="nuevaFirma" class="btn btn-primary boton-primario" accion="nuevaFirma">
            <i class="icon-comision"></i>
            <?php echo lang("agregar"); ?>
        </button>
    </div>
</div>
<script>
    var columns = <?php echo $columns;?>;
</script>

<row>
    <div class="col-md-12 col-xs-12">
        <div class="row" id="areaTablas">
            <?php $tmpl=array ( 'table_open'=>'
        <table id="firmaRematricula-table" width="100%" class="table table-striped table-condensed table-bordered"  oncontextmenu="return false"
        onkeydown="return false">');
            $this->table->set_template($tmpl);
            $this->table->set_heading(array('','','','', '', '','','','',''));
            echo $this->table->generate(); ?>
        </div>
    </div>
</row>
</div>

<div name="container_menu_filters_temp">
    <div id="div_table_filters" class="table_filter col-sm-12" name="div_table_filters">
        <table style="width: 100%;">
            <tr>
                <td>
                    <table style="width: 100%;">
                        <tr>
                            <td><?php echo lang("busqueda_avanzada");//lang("nombre") ?></td>
                        </tr>

                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <div class="option_content" id="filtro_opciones_busqueda_avanzada_display" >
                                    <div name="grupo_filtros_avanzados">
                                        <?php foreach($columnas as $key => $valor){ ?>
                                            <div class="row filter_<?php echo $key ?>" id="<?php echo $key ?>" name="filtro_avanzado_usuario" style="border-style: none; padding: 0px; background-color: white;<?php if($key!=0) echo 'display:none';?>">
                                                <div class="form-group col-md-3" id="campo_1">
                                                    <select name="filtro_avanzado_usuario_campo" id="<?php echo $key ?>" style="width: 100%;" class="filter_<?php echo $key ?>">
                                                        <option value="-1" selected="true">(<?php echo strtolower(lang('SELECCIONE_UNA_OPCION')); ?>)</option>
                                                        <?php  foreach($filtros as $id=>$myColumn){ ?>
                                                            <option value="<?php echo $id; ?>" <?php /*if ($id == $key){ ?> selected="true" <?php }*/ ?>>
                                                                <?php echo $myColumn; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-3" id="condicion_1">
                                                    <select name="filtro_avanzado_usuario_condicion" id="<?php echo $key ?>" class="filter_<?php echo $key ?>" style="width: 100%;">
                                                        <option value="-1">(<?php echo strtolower(lang('SELECCIONE_UNA_OPCION')); ?>)</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-5 filter_<?php echo $key ?>" id="valores_1" name="filtro_avanzado_div_valores">

                                                </div>
                                                <?php if($key!=0){?>
                                                    <div class="form-group col-md-1">
                                                        <i class="icon-remove red" style="cursor: pointer" name="remove_filtro_avanzado" id="<?php echo $key ?>" class="filter_<?php echo $key ?>"></i>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>

                                    </div>
                                </div>
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
                                <span style="cursor: pointer;" id="agregar_filtro_busqueda_usuario">
                                    <i class="icon-plus smaller-75"></i>&nbsp;&nbsp;<?php echo lang("agregar_condicion"); ?>
                                </span>
                                <div>
                                    <center>
                                        <button class="btn btn-sm btn-success" name="btnBuscar" type="button"><?php echo lang("buscar"); ?></button>
                                    </center>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

        </table>
    </div>
</div>

<div style="bottom: 0px; height: 100%; left: 0px; position: fixed; width: 100%; z-index: 20; display: none;" name="contenedorPrincipal"></div>
<form name="frm_exportar" action="<?php echo base_url()."rematriculaciones/listar_firmaRematricula" ?>" target="new_target" method="POST">
    <input type="hidden" value="" name="tipo_reporte">
    <input type="hidden" value="" name="iSortCol_0">
    <input type="hidden" value="" name="sSortDir_0">
    <input type="hidden" value="" name="iDisplayLength">
    <input type="hidden" value="" name="iDisplayStart">
    <input type="hidden" value="" name="sSearch">

    <input type="hidden" value="" name="documento">
    <input type="hidden" value="" name="nombre">
    <input type="hidden" value="" name="apellido">
    <input type="hidden" value="" name="matricula">
    <input type="hidden" value="" name="comision">
    <input type="hidden" value="" name="ciclo">
    <input type="hidden" value="" name="firmo">
    <input type="hidden" value="" name="ano">
    <input type="hidden" value="" name="trimestre">
    <input type="hidden" value="" name="fecha">

    <input type="hidden" value="" name="condiciones_doc">
    <input type="hidden" value="" name="condiciones_nom">
    <input type="hidden" value="" name="condiciones_ape">
    <input type="hidden" value="" name="condiciones_mat">
    <input type="hidden" value="" name="condiciones_com">
    <input type="hidden" value="" name="condiciones_cic">
    <input type="hidden" value="" name="condiciones_fir">
    <input type="hidden" value="" name="condiciones_ano">
    <input type="hidden" value="" name="condiciones_tri">
    <input type="hidden" value="" name="condiciones_fec">

    <input type="hidden" value="exportar" name="action">
</form>

<script src="<?php echo base_url('assents/js/rematriculaciones/firma_rematriculaciones.js');?>"></script>