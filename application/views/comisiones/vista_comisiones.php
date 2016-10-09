<script>
    var menuJson = <?php echo $menuJson?>;
    var columns = <?php echo $columns?>;
</script>
<script src="<?php echo base_url('assents/js/comisiones/comisiones.js');?>"></script>

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
<?php $filtros = array("codigo"=>lang("codigo"),"nombre"=>lang("nombre"),"curso"=>lang("curso"),"cant_inscriptos"=>lang("cantidad_inscriptos"),"capacidad"=>lang("capacidad"), "estado"=>lang("estado"));?>
    <!-- modificacion franco ticket 5149->   agrego los botones de nuevaComision y cambios periodo que estaban anteriormente   -->
    <div class="col-sm-12" id="top_buttons">
        <div class="dataTables_buttons" id="academicoComisiones_buttons">
            <div class="btn-group">
                <button class="btn btn-primary boton-primario" accion="nuevaComision">
                    <i class="icon-comision"></i>
                    <?php echo lang("nuevaComision"); ?>
                </button>
                <button class="btn btn-primary boton-primario" accion="comisiones_cambios_periodo" style="padding-left: 6px; margin-left: 24px;">
                    <i class="icon-arrow-right icon-on-right" style="margin-right: 4px;"></i>
                    <?php echo lang("comisiones_cambios_periodo"); ?>
                </button>
            </div>
        </div>
    </div>
    <!-- <-modificacion franco ticket 5149      -->
<div class="col-md-12 col-xs-12">
    
    <div class="row" id="areaTablas">
        <?php $tmpl=array ( 'table_open'=>'
        <table id="academicoComisiones" width="100%" class="table table-striped table-condensed table-bordered"  oncontextmenu="return false"
        onkeydown="return false">'); 
        $this->table->set_template($tmpl); 
        $this->table->set_heading(array('','','', '','','')); 
        echo $this->table->generate(); ?>       
    </div>
</div>

<div name="div_agregar_horario" style="display: none;">
    <input type="hidden" value="" name="codigo_comision_baja">
    <div class="modal-header">
        <h3><?php echo "Baja de Comision"; ?></h3>
    </div>
    <div class="modal-body">      
        <div class="row">
            <div class="form-group col-md-12">
                <label><?php echo lang('fecha_desde') ?></label>
                <input type="text" id="fechaDesde" name="fechaDesde" class="form-control date-picker" value="<?php echo date("d/m/Y") ?>">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" id="btn-ok-cambio-estado" onclick="bajaComision();" class="btn btn-primary">
            <?php echo lang('continuar')?>
        </button>
    </div>
</div>

<!-- modificacion franco ticket 5149->   agrego la busqueda con filtros avanzados y los botones exportar e imprimir   -->
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
<form name="frm_exportar" action="<?php echo base_url()."comisiones/listar" ?>" target="new_target" method="POST">
    <!--<input type="hidden" value="" name="tipo_contacto">-->
    <input type="hidden" value="" name="comsion">
    <input type="hidden" value="" name="fechaDesde">
    <input type="hidden" value="" name="fechaHasta">
    <input type="hidden" value="" name="tipo_reporte">    
    <input type="hidden" value="" name="iSortCol_0">
    <input type="hidden" value="" name="sSortDir_0">
    <input type="hidden" value="" name="iDisplayLength">
    <input type="hidden" value="" name="iDisplayStart">
    <input type="hidden" value="" name="sSearch">
    <input type="hidden" value="exportar" name="action">
</form>

 <!-- <-modificacion franco ticket 5149 -->
