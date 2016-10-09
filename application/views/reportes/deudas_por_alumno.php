<link rel="stylesheet" href="<?php echo base_url('assents/css/tel-master/intlTelInput.css')?>"/>
<script src="<?php echo base_url('assents/js/librerias/tel-master/intlTelInput.js')?>"></script>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal.css');?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal-bs3patch.css');?>"/>
<!--<script src="<?php echo base_url('assents/js/generalTelefonos.js')?>"></script>-->
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
    var langTodas = '<?php echo lang("todas")?>';
    var langTodos = '<?php echo lang("TODOS")?>';
    var idioma = '<?php echo get_idioma()?>';
    var subtotal = '<?php echo lang("subtotal")?>';
    var simboloPesos = '<?php echo $pesos?>';
    var total = '<?php echo lang("total")?>';
    var total_de_esta_pagina = '<?php echo lang("total_de_esta_pagina")?>';
    var total_general = '<?php echo lang("total_general")?>';
    
</script>
<script src="<?php echo base_url('assents/js/reportes/deudas_por_alumno.js');?>"></script>



<div class="col-md-12 col-xs-12">
        <div id="areaTablas">
        <?php 
            $tmpl=array ( 
            'table_open'=>'<table id="reporteDeudasPorAlumno" width="100%" class="table table-striped table-condensed  table-bordered"  oncontextmenu="return false"
            onkeydown="return false">'); 
            $this->table->set_template($tmpl); 
            $this->table->set_heading(array('','','','', '','','','','')); 
            echo $this->table->generate();
        ?>
    </div>
</div>


<div style="bottom: 0px; height: 100%; left: 0px; position: fixed; width: 100%; z-index: 20; display: none;" name="contenedorPrincipal">    
</div>
<div name="container_menu_filters_deudas_por_alumno" style="position: absolute; bottom: 0px;">
    <div id="div_table_filters" class="table_filter" name="div_table_filters_deudas_por_alumno" style="display: none; padding-left: 12px; padding-right: 12px;">
        <table style="width: 256px;">
            <tr>
                <td><?php echo lang("cantidad_de_cuotas_adeudadas"); ?></td>
            </tr>
            <tr>
                <td>
                    <select id="cant_cuotas" class="select_chosen" style="width: 302px;">
                        <option value="0"><?php echo lang("todas")?></option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">+3</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><?php echo lang("fecha_ultimo_pago"); ?></td>
            </tr>
            <tr>
                <td>
                    <select id="ultimo_pago_select">
                        <option value="-1"><? echo lang("filtrar_por"); ?></option>
                        <option value="1"><? echo lang("mayor_que"); ?></option>
                        <option value="2"><? echo lang("menor_que"); ?></option>
                        <option value="3"><? echo lang("entre_"); ?></option>
                        <option value="4"><? echo lang("igual_a"); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="text" id="fecha_pago_desde" style="display: none;  width: 75px;">
                    <span id="span_y_" style="display: none;">y</span>
                    <input type="text" id="fecha_pago_hasta" style="display: none;  width: 75px;">
                </td>    
            </tr>
            <tr>
                <td><?php echo lang("saldo_acumulado"); ?></td>
            </tr>
            <tr>
                <td>
                    <select id="saldo_acumulado">
                        <option value="-1"><? echo lang("filtrar_por"); ?></option>
                        <option value="1"><? echo lang("mayor_que"); ?></option>
                        <option value="2"><? echo lang("menor_que"); ?></option>
                        <option value="3"><? echo lang("entre_"); ?></option>
                        <option value="4"><? echo lang("igual_a"); ?></option>
                    </select>
                
                    <input type="text" id="desd" style="width: 30px; display: none;">
                    <span id="span_y" style="display: none;">y</span>
                    <input type="text" id="hast" style="width: 30px; display: none;">
                </td>
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
<!--            <tr>
                <td><?php echo lang("periodo"); ?></td>
            </tr>
            <tr>
                <td>
                    <select id="periodo" style="width: 50%;">
                        <option value="0"><?php echo lang("TODOS")?></option>
                        <option value="1"><?php echo lang('enero'); ?></option>
                        <option value="2"><?php echo lang('febrero'); ?></option>
                        <option value="3"><?php echo lang('marzo'); ?></option>
                        <option value="4"><?php echo lang('abril'); ?></option>
                        <option value="5"><?php echo lang('mayo'); ?></option>
                        <option value="6"><?php echo lang('junio'); ?></option>
                        <option value="7"><?php echo lang('julio'); ?></option>
                        <option value="8"><?php echo lang('agosto'); ?></option>
                        <option value="9"><?php echo lang('septiembre'); ?></option>
                        <option value="10"><?php echo lang('octubre'); ?></option>
                        <option value="11"><?php echo lang('noviembre'); ?></option>
                        <option value="12"><?php echo lang('diciembre'); ?></option>
                    </select>
                    <select id="anio" style="width: 40%; display:none;">
                        <option value="0"><?php echo lang("TODOS")?></option>
                        <option value="2000">2000</option>
                        <option value="2001">2001</option>
                        <option value="2002">2002</option>
                        <option value="2003">2003</option>
                        <option value="2004">2004</option>
                        <option value="2005">2005</option>
                        <option value="2006">2006</option>
                        <option value="2007">2007</option>
                        <option value="2008">2008</option>
                        <option value="2009">2009</option>
                        <option value="2010">2010</option>
                        <option value="2011">2011</option>
                        <option value="2012">2012</option>
                        <option value="2013">2013</option>
                        <option value="2014">2014</option>
                        <option value="2015">2015</option>
                        <option value="2016">2016</option>
                        <option value="2017">2017</option>
                        <option value="2018">2018</option>
                        <option value="2019">2019</option>
                        <option value="2020">2020</option>
                    </select>
                </td>
            </tr>-->
            <tr>
                <td><?php echo lang("comision"); ?></td>
            </tr>
            <tr>
                <td>
                    <select id="comision" class="select_chosen" style="width: 302px;">
                        
                    </select>
                </td>
            </tr>
            <tr>
                <td><?php echo lang("turno"); ?></td>
            </tr>
            <tr>
                <td>
                    <select id="turno" class="select_chosen" style="width: 302px;">
                        <option value="0"><? echo lang('filtrar_por');?></option>
                        <option value="1"><? echo lang('manana');?></option>
                        <option value="2"><? echo lang('tarde');?></option>
                        <option value="3"><? echo lang('noche');?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><?php echo lang("tipo_de_deuda"); ?></td>
            </tr>
            <tr>
                <td>
                    <select id="tipo_deuda" class="select_chosen" style="width: 302px;">
                        <option value="0"><? echo lang('filtrar_por');?></option>
                        <option value="<? echo lang('deuda_pasiva');?>"><? echo lang('deuda_pasiva');?></option>
                        <option value="<? echo lang('deuda_activa');?>"><? echo lang('deuda_activa');?></option>
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
<form name="frm_exportar" action="<?php echo base_url()."reportes/listarDeudasPorAlumnos" ?>" target="new_target" method="POST">
    <input type="hidden" value="" name="length">
    <input type="hidden" value="" name="start">
    <input type="hidden" value="" name="search">
    <input type="hidden" value="" name="iSortCol_0">
    <input type="hidden" value="" name="sSortDir_0">
    <input type="hidden" value="" name="cant_cuotas">
    <input type="hidden" value="" name="ultimo_pago_select">
    <input type="hidden" value="" name="fecha_pago_desde">
    <input type="hidden" value="" name="fecha_pago_hasta">    
    <input type="hidden" value="" name="saldo_acumulado">
    <input type="hidden" value="" name="desd">
    <input type="hidden" value="" name="hast">
    <input type="hidden" value="" name="cursos">
    <input type="hidden" value="" name="periodo">
    <input type="hidden" value="" name="anio">
    <input type="hidden" value="" name="comision">
    <input type="hidden" value="" name="turno">
    <input type="hidden" value="" name="tipo_deuda">
    <input type="hidden" value="" name="tipo_reporte">
</form>