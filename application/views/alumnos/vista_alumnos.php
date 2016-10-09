<link rel="stylesheet" href="<?php echo base_url('assents/css/tel-master/intlTelInput.css')?>"/>
<script src="<?php echo base_url('assents/js/librerias/tel-master/intlTelInput.js')?>"></script>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal.css');?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal-bs3patch.css');?>"/>

<script src="<?php echo base_url('assents/js/generalTelefonos.js')?>"></script>
<script src="<?php echo base_url()?>assents/js/librerias/bootstrap-modal/bootstrap-modal.js"></script>
<script src="<?php echo base_url()?>assents/js/librerias/bootstrap-modal/bootstrap-modalmanager.js"></script>
<script src="<?php echo base_url('assents/theme/assets/js/inputMask/jquery.inputmask.js')?>"></script>
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
<script>    
    var columns = <?php echo $columns?>;
    var codigo_aspirante = '<?php echo $codigo_aspirante?>';
</script>
<script src="<?php echo base_url('assents/js/alumnos/alumnos.js');?>"></script>
<script>
    var ver_fancy = '<?php echo $abrir_fancy;?>';
</script>
<p><canvas id="canvas" height="240" width="320" style="display: none;"></canvas></p>
<!-- modificacion franco ticket 5053-> -->
<input type="hidden" value="<?php echo "alumnos"; ?>" name="report_name">
<!-- <-modificacion franco ticket 5053 -->
<div id="msgComfirmacion" class="modal fade" data-width="40%" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3><i class="icon-warning-sign red"></i><?php echo lang('ADVERTENCIA');?></h3>
    </div>    
    <div class="modal-body">      
        <div class="row">
            <form id="frmcomfirmacion">                
                <div class="col-md-12 col-xs-12" id="textoMsg">                    
                </div>                
            </form>
        </div>
    </div>  
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn">Cancelar</button>
        <button type="button" id="btn-ok-cambio-estado" class="btn btn-primary" onclick="cambiarEstado();"><?php echo lang('continuar')?></button>
    </div>
</div>
<div class="col-md-12 col-xs-12">
    <!-- modificacion franco ticket 5053->       
    <form id='exportar' action="<?php //echo base_url('reportes/exportarReporte')?>" method="post">
        <input name ='exportar_reporte' type='hidden' value=''>
        
    </form>
    <!-- <-modificacion franco ticket 5053             -->
    <div id="areaTablas">
             
        <?php $tmpl=array ( 
            'table_open'=>'<table id="academicoAlumnos" width="100%" class="table table-striped table-condensed  table-bordered"  oncontextmenu="return false"
            onkeydown="return false">'); $this->table->set_template($tmpl);
            //modificacion franco ticket 5053 -> (agregado de columnas a dibujar en la tabla)
            $this->table->set_heading(array('','','','','','','','','',''/*,'' este espacio es para un posible agregado de razon social*/, '','')); 
            //<-modificacion franco ticket 5053
            echo $this->table->generate();
            
        ?>
    </div>
</div>

 <!-- modificacion franco ticket 5053->      -->
<div name="container_menu_filters_temp">
    <div id="div_table_filters" class="table_filter" name="div_table_filters">
        <table style="width: 100%;">
            <tr>
                <td>
                    <table style="width: 100%;">
                        <tr>                    
                            <!--<td><?php //echo lang("tipo_de_contacto") ?></td>-->
                            <td><?php echo lang("provincia") ?></td>
                        </tr>
                        <tr>
                            <td>
                                <select name="filtro_provincia" class="select_chosen" id="filtro_prov">
                                    <option value="-1">(<?php echo lang("todos"); ?>)</option>
                                    <?php foreach ($provincias as $prov){ ?> 
                                    <option value="<?php echo $prov['id']; ?>">
                                        <?php echo $prov['nombre']; ?>
                                    </option>    
                                    <?php } ?>
                                   
                                </select>
                            </td>
                        </tr>
                        <tr>                    
                            <!--<td><?php //echo lang("tipo_de_contacto") ?></td>-->
                            <td><?php echo lang("localidad") ?></td>
                        </tr>
                        <tr>
                            <td>
                                <select name="filtro_localidad" class="select_chosen" id="filtro_loc" style="width:151px;">
                                    <option value="-1">(<?php echo lang("todos"); ?>)</option>
                                    
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
                            <td><?php echo lang("como_nos_conocio") ?></td>
                        </tr>
                        <tr>
                            <td>
                                <select name="filtro_como_nos_conocio" class="select_chosen">
                                    <option value="-1">(<?php echo lang("todos"); ?>)</option>
                                    <?php foreach ($comonoscono as $como){ ?> 
                                    <option value="<?php echo $como['codigo']; ?>">
                                        <?php echo $como['nombre']; ?>
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
                            <td><?php echo lang('estado_alumno'); ?></td>
                            <td><?php echo lang('datos_talle'); ?></td>
                        </tr>
                        <tr>
                            <td>
                                <select name="filtro_estado" style="width: 138px;" class="select_chosen">
                                    <option value="-1">(<?php echo lang("todos") ?>)</option>
                                    <option value="habilitada"><?php echo lang("HABILITADO");//lang("manana"); ?></option>
                                    <option value="inhabilitada"><?php echo lang("INHABILITADO");//lang("tarde"); ?></option>                                   
                                </select>
                            </td>
                            <td>
                                <select name="filtro_talle" style="width: 138px;" class="select_chosen">
                                    <option value="-1">(<?php echo lang("todos"); ?>)</option>
                                    <?php foreach ($talles as $talle){ ?> 
                                    <option value="<?php echo $talle['codigo']; ?>">
                                        <?php echo $talle['talle']; ?>
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
                                    <input name="filtro_fecha_alta_desde" value="" class="date-picker" type="text" readyonly="true" style="margin-right: 0px; width: 96px;">
                                    <span class="input-group-addon" style="padding: 3px 6px;">
                                        <i class="icon-calendar bigger-110"></i>
                                    </span>
                                </div>
                            </td>                            
                            <td>
                                <div class="input-group">
                                    <input name="filtro_fecha_alta_hasta" value="" class="date-picker" type="text" readyonly="true" style="margin-right: 0px; width: 96px;">
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
                    <button class="btn btn-sm btn-success" type="button" name="btnBuscar" onclick="listar();" ><?php echo lang("buscar"); ?></button>
                </td>
            </tr>
        </table>
    </div>
</div>
<div style="bottom: 0px; height: 100%; left: 0px; position: fixed; width: 100%; z-index: 20; display: none;" name="contenedorPrincipal"></div>
<form name="frm_exportar" action="<?php echo base_url()."alumnos/listar" ?>" target="new_target" method="POST">
    <!--<input type="hidden" value="" name="tipo_contacto">-->
    <input type="hidden" value="" name="localidad">
    <input type="hidden" value="" name="como_nos_conocio">
    <input type="hidden" value="" name="estado">
    <input type="hidden" value="" name="talle">
    <input type="hidden" value="" name="fecha_alta_desde">
    <input type="hidden" value="" name="fecha_alta_hasta">
    <input type="hidden" value="" name="tipo_reporte">    
    <input type="hidden" value="" name="iSortCol_0">
    <input type="hidden" value="" name="sSortDir_0">
    <input type="hidden" value="" name="iDisplayLength">
    <input type="hidden" value="" name="iDisplayStart">
    <input type="hidden" value="" name="sSearch">
    <input type="hidden" value="exportar" name="action">
</form>

 <!-- <-modificacion franco ticket 5053      -->
