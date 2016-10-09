<script>
    var columns = <?php echo $columns ?>;      
</script>
<script src="<?php echo base_url('assents/js/certificados/vista_certificados.js')?>"></script>

<style>
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

<div class="col-md-12 col-xs-12">
    <div class="tabbable">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active">
                <a data-toggle="tab" href="#certificaciones_iga" onclick="">
                    <?php echo strtoupper(lang('certificaciones_iga')); ?>
                </a>
            </li>
            <?php if ($certifica_ucel){ ?> 
            <li>
                <a data-toggle="tab" href="#certificaciones_ucel" onclick="listarCertificacionesUCEL();">
                    <?php echo strtoupper(lang('certificaciones_ucel')); ?>
                </a>
            </li>    
            <?php } ?>
        </ul>
        <div class="tab-content">
            <div id="certificaciones_iga" class="tab-pane in active" style="overflow: auto;">
                <div class="row">
                    <div class="col-md-12 col-xs-12 col-sm-12">
                        <button data-toggle="dropdown" class="btn btn-primary dropdown-toggle">
                            <?php echo lang("acciones"); ?>
                            <i class="icon-angle-down icon-on-right"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="javascript:void(0)" onclick="aprobar();">
                                    <?php echo lang('aprobar'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" onclick="modificarAprobacion();">
                                    <?php echo lang('modificar'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" onclick="cancelarCertificados();">
                                    <?php echo lang('cancelar_certificado'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" onclick="habilitarCertificadosCancelados();">
                                    <?php echo lang('HABILITAR'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" onclick="revertirCertificados();">
                                    <?php echo lang('REVERTIR'); ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="row" >
                    <div class="col-md-12 col-xs-12 col-sm-12">
                        <table id="tableCertificacionesIGA" class="table table-bordered table-striped" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>&nbsp;</th>
                                    <th><?php echo lang("matricula"); ?></th>
                                    <th><?php echo lang("frm_nuevaMatricula_nombApellido"); ?></th>
                                    <th><?php echo lang("documento"); ?></th>
                                    <th><?php echo lang("curso_presu_as"); ?></th>
                                    <th><?php echo lang("comision"); ?></th>
                                    <th><?php echo lang("fechaDesde_horario"); ?></th>
                                    <th><?php echo lang("fecha_hasta_"); ?></th>
                                    <th><?php echo lang("titulo"); ?></th>
                                    <th><?php echo lang("fecha_pedido"); ?></th>
                                    <th><?php echo lang("estado"); ?></th>
                                    <th><?php echo lang("detalles"); ?></th>
                                    <th><?php echo lang("usuario_que_genero_el_pedido"); ?></th>
                                    <th><?php echo lang("entregado"); ?></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div id="certificaciones_ucel" class="tab-pane" style="overflow: auto;">
                <div class="row">  <!-- style="width: 1900px;"-->
                    <div class="col-md-12 col-xs-12 col-sm-12">
                        <button data-toggle="dropdown" class="btn btn-primary dropdown-toggle">
                            <?php echo lang('acciones'); ?>
                            <i class="icon-angle-down icon-on-right"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="javascript:void(0)" onclick="aprobar(true);">
                                    <?php echo lang('aprobar'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" onclick="modificarAprobacion(true);">
                                    <?php echo lang('modificar'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" onclick="cancelarCertificados(true);">
                                    <?php echo lang('cancelar_certificado'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" onclick="habilitarCertificadosCancelados(true);">
                                    <?php echo lang('HABILITAR'); ?>
                                </a>
                            </li>
                             <li>
                                <a href="javascript:void(0)" onclick="revertirCertificados(true);">
                                    <?php echo lang('REVERTIR'); ?>
                                </a>
                            </li>
                        </ul>
                        </ul>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-xs-12 col-sm-12">
                        <table id="tableCertificacoinesUCEL" class="table table-bordered table-striped" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>&nbsp;</th>
                                    <th><?php echo lang("matricula"); ?></th>
                                    <th><?php echo lang("frm_nuevaMatricula_nombApellido") ?></th>
                                    <th><?php echo lang("documento"); ?></th>
                                    <th><?php echo lang("curso_presu_as"); ?></th>
                                    <th><?php echo lang("comision"); ?></th>
                                    <th><?php echo lang("fechaDesde_horario"); ?></th>
                                    <th><?php echo lang("fecha_hasta_"); ?></th>
                                    <th><?php echo lang("titulo"); ?></th>
                                    <th><?php echo lang("fecha_pedido"); ?></th>
                                    <th><?php echo lang("estado"); ?></th>
                                    <th><?php echo lang("usuario_que_genero_el_pedido"); ?></th>
                                    <th><?php echo lang("entregado"); ?></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div name="container_menu_filters_temp" style="position: absolute; bottom: 0px;">
    <div id="div_table_filters" class="table_filter" name="div_table_filters" style="padding-left: 12px; padding-right: 12px;">
        <table style="width: 256px;">
            <tr>
                <td><?php echo lang("estado_alumno"); ?></td>
            </tr>
            <tr>
                <td>
                    <select name="filtro_estado" style="width: 302px;" class="select_chosen">
                        <option value="-1">(<?php echo strtolower(lang("TODOS")); ?>)</option>
                        <option value="finalizado"><?php echo lang("finalizados"); ?></option>
                        <option value="cancelado"><?php echo lang("cancelados"); ?></option>
                        <option value="pendiente"><?php echo lang("pendiente"); ?></option>
                        <option value="pendiente_impresion"><?php echo lang("pendiente_impresion"); ?></option>
                        <option value="pendiente_aprobar"><?php echo lang("pendiente_aprobar"); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><?php echo lang("comisiones"); ?></td>
            </tr>
            <tr>
                <td>
                    <select name="filtro_comisiones_iga" style="width: 302px;" class="select_chosen" onchange="filtro_comisiones_iga_change();">
                        <option value="-1">(<?php echo lang('todas'); ?>)</option>
                    <?php foreach ($arrComisiones as $comision){ ?> 
                        <option value="<?php echo $comision['codigo'] ?>">
                            <?php echo $comision['nombre']; ?>
                        </option>
                    <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><?php echo lang("cursos"); ?></td>
            </tr>
            <tr>
                <td>
                    <select name="filtro_cursos_iga" style="width: 302px;" class="select_chosen">
                        <option value="-1">(<?php echo lang("todos") ?>)</option>
                        <?php foreach ($arrCursos as $curso){ ?> 
                        <option value="<?php echo $curso['codigo'] ?>">
                            <?php echo $curso['nombre_'.get_idioma()]; ?>
                        </option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;">
                    <button class="btn btn-sm btn-success" type="button" name="btnBuscar" onclick="listar_certificacion_iga();" style="margin-top: 16px;">
                        <?php echo lang("buscar"); ?>
                    </button>
                </td>
            </tr>
        </table>
    </div>
    <div style="bottom: 0px; height: 100%; left: 0px; position: fixed; width: 100%; z-index: 20; display: none;" name="contenedorPrincipal"></div>
</div>
<div name="container_menu_filters_temp_ucel" style="position: absolute; bottom: 0px;">
    <div id="div_table_filters" class="table_filter" name="div_table_filters_ucel" style="display: none; padding-left: 12px; padding-right: 12px;">
        <table style="width: 256px;">
            <tr>
                <td><?php echo lang("estado_alumno"); ?></td>
            </tr>
            <tr>
                <td>
                    <select name="filtro_estado_ucel" class="select_chosen" style="width: 302px;">
                        <option value="-1">(<?php echo strtolower(lang("TODOS")); ?>)</option>
                        <option value="finalizado"><?php echo lang("finalizados"); ?></option>
                        <option value="cancelado"><?php echo lang("cancelados"); ?></option>
                        <option value="pendiente_impresion"><?php echo lang("pendiente_impresion"); ?></option>
                        <option value="pendiente_aprobar"><?php echo lang("pendiente_aprobar"); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><?php echo lang("comisiones"); ?></td>
            </tr>
            <tr>
                <td>
                    <select name="filtro_comisiones_ucel" style="width: 302px;" class="select_chosen" onchange="filtro_comisiones_ucel_change();">
                        <option value="-1">(<?php echo lang('todas'); ?>)</option>
                    <?php foreach ($arrComisiones as $comision){ ?> 
                        <option value="<?php echo $comision['codigo'] ?>">
                            <?php echo $comision['nombre']; ?>
                        </option>
                    <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><?php echo lang("cursos"); ?></td>
            </tr>
            <tr>
                <td>
                    <select name="filtro_cursos_ucel" style="width: 302px;" class="select_chosen">
                        <option value="-1">(<?php echo lang("todos") ?>)</option>
                        <?php foreach ($arrCursos as $curso){ 
                            if ($curso['codigo'] == 1 || $curso['codigo'] == 2){ ?> 
                        <option value="<?php echo $curso['codigo'] ?>">
                            <?php echo $curso['nombre_'.get_idioma()]; ?>
                        </option>
                        <?php } 
                        } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;">
                    <button class="btn btn-sm btn-success" type="button" name="btnBuscar" onclick="listar_certificacion_ucel();" style="margin-top: 16px;">
                        <?php echo lang("buscar"); ?>
                    </button>
                </td>
            </tr>
        </table>
    </div>
</div>
<?php 

//echo "<pre>"; print_r($arrCursos); echo "</pre>";
?>