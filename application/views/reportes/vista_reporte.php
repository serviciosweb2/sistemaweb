<style>
    .tags{
        width: 300px !important;
        max-width: 300px !important;
    }
    .tags .tag{
        font-size: 85% !important;
    }
    
    .tags span{
        float: left !important;
        background: #ffb752 !important;
    }
    
    .tags .tag-warning {
        background-color: #e4e6e9 !important;
    }
    
    #agregar_tag {
        float: left !important;
    }
    .tags input[type="text"], .tags input[type="text"]:focus {
        width: auto !important;
    }
</style>

<script>
    var nombre_reporte = '<?php echo $nombre_reporte;?>';
    var arrObjColums = <?php echo json_encode($reporte['columns'])?>;
    var filtrosReportes = <?php echo isset($filters['filters']) ? json_encode($filters['filters'][0]['advanced_filters']): json_encode(array())?>;
</script>
<script src="<?php echo base_url('assents/js/impresiones.js')?>"></script>
<script src="<?php echo base_url('assents/js/reportes/reportes.js')?>"></script>
<link rel="stylesheet" href="<?php echo base_url('assents/css/datepicker3.css')?>"/>
<script src="<?php echo base_url('assents/js/librerias/jquery-serialize/jquery.serializeJSON.min.js')?>"></script>
<script src="<?php echo base_url('assents/theme/assets/js/uncompressed/bootstrap-tag.js');?>"></script>
<link rel="stylesheet" href="<?php echo base_url('assents/css/reportes/reportes.css')?>"/>



<input type="hidden" value="<?php echo $reporte['report_name'] ?>" name="report_name">
<div class="col-md-12 col-xs-12" name="reporte_alumnos">
    <form id='exportar' action="<?php echo base_url('reportes/exportarReporte')?>" method="post">
        <input name ='exportar_reporte' type='hidden' value=''>
        
        </form>
    <div id="areaTablas" class="table-responsive">
        <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper" role="grid">            
            <div class="row">                
                <!--DISPLAY-->
                <div class="col-sm-6">
                    <div id="DataTables_Table_0_length" class="dataTables_length">
                        <label>
                            <?php  echo lang('mostrar');?>
                            <select name="DataTables_Table_0_length" size="1" aria-controls="DataTables_Table_0">
                                <?php foreach ($reporte['iPaginations'] as $paginacion){ ?> 
                                <option value="<?php echo $paginacion ?>"
                                        <?php if ($paginacion == $reporte['iPaginationSelected']){ ?> selected="true" <?php } ?>>
                                    <?php echo $paginacion; ?>
                                </option>    
                                <?php } ?>
                                <option value="-1"><?php echo lang('TODOS')?></option>
                            </select>
                            <?php echo lang('resultados');?>
                        </label>
                    </div>
                </div>
                <!--SEARCH-->
                <div class="col-sm-6">
                    <div id="administracion_filter" class="dataTables_filter">
                        <label>
                        <?php echo lang('buscar');?>:
                       
                        <span class="input-icon input-icon-right">
                            <?php
                                if(isset($filters['filters'])){
                                  foreach($filters['filters'][0]['advanced_filters'] as $key=>$valor){
                                        echo '<input id="form-field-icon-2" aria-controls="DataTables_Table_0"  name="search_table" value="'.$valor['filter_name'].'">';
                                    }  
                                }else{
                                    echo '<input id="form-field-icon-2" aria-controls="DataTables_Table_0"  name="search_table">';
                                }
                                    
                                
                            ?>
                            
                           </span>
                        
                        </label>
                         <i class="icon-caret-down grey bigger-110 bigger-140" style="margin-right: 3px; cursor: pointer" name="table_filters"></i>
                        <i id="imprimir_informe" class="icon-print grey"  style="cursor: pointer" onclick="getTable(1);"></i>
                        <i id="exportar_informe" class="icon-external-link" style="cursor: pointer" onclick="getTable(1,1);"></i>
                            <!--FILTROS DE LA TABLA-->
                            <div name="contenedorPrincipal" style=" bottom: 0; height: 100%; left: 0; position: fixed; width: 100%; z-index: 20; display: none;"></div>
                            <div class="table_filter" id="div_table_filters" name="div_table_filters" style="display: none; z-index: 1000"> 
                                
                                <div class="row" style="padding-bottom: 0px;">
                                    <div class="col-md-6">
                                        <h5 class="purple"><i class="icon-filter purple"></i>&nbsp;<?php echo lang('filtros'); ?></h5>
                                    </div>
                                    <div class="col-md-6">
                                        <h5 class="purple"><i class="icon-filter purple"></i>&nbsp;<?php echo lang('agrupar_por') ?></h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <!--FILTROS COMUNES-->
                                    <div class="col-md-6 option_content">                                       
                                        <?php foreach ($reporte['common_filters'] as $filter){ 
                                            
                                            $filtraralinicio = ((isset($filtrar_al_inicio) && $filtrar_al_inicio != false && in_array($filter->id,$filtrar_al_inicio)));
                                            
                                            $checked = (isset($filters['default']) && $filters['default'] > -1 
                                                    && isset($filters['filters'][$filters['default']]['common_filters'])
                                                    && in_array($filter->id, $filters['filters'][$filters['default']]['common_filters']) || $filtraralinicio)?> 
                                            <div class="checkbox">
                                                <label title="<?php echo $filter->hint; ?>">
                                                    <input class="ace" type="checkbox" name="common_filters" value="<?php echo $filter->id ?>"
                                                           <?php if ($checked){ ?> checked="true" <?php } ?>>
                                                    <span class="lbl"><?php echo $filter->display ?></span>
                                                </label>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <!--GRUPOS-->
                                    <div class="col-md-6 option_content">
                                        
                                    </div>
                                </div>
                                
                                
                                <!--FILTROS PERSONALIZADOS-->
                                <div class="row">
                                    <div class="col-md-12 option_content">
                                        <h5 class="purple"><i class=" icon-asterisk purple"></i>&nbsp;<?php echo lang('filtros_personalizados'); ?></h5>
                                    </div>
                                </div>
                                <div name='div_filtros_personalizados_guardados' style='padding-left: 14px;'>
                                    <?php if (isset($filters['filters'])){
                                        foreach ($filters['filters'] as $key => $filter){ ?>
                                    <?php if (isset($filter['advanced_filters'])){ ?>
                                    <div class="row" style='padding: 0px; background-color: white; border-style: none;' id="filtros_personalizados_guardados_<?php echo $filter['advanced_filters'][0]['filter_code'] ?>">
                                        <div class="col-md-11 option_content">
                                            <span class="filtros_personale_usuario<?php if ($key == $filters['default']){ ?> filtros_personales_usuario_selected<?php } ?>" name="filtros_personalizados_usuario" id="<?php echo $filter['advanced_filters'][0]['filter_code'] ?>">
                                                <i class="icon-filter"></i>
                                                <?php echo $filter['advanced_filters'][0]['filter_name']; ?>
                                            </span>
                                        </div>
                                        <?php if ($filter['solo_lectura'] != '1'){ ?>
                                        <div class="col-md-1 option_content">
                                            <i id="<?php echo $filter['advanced_filters'][0]['filter_code'] ?>" class="icon-remove red" name="remove_filtro_personalizado" style="cursor: pointer" title="Eliminar Filtro"></i>
                                        </div>
                                        <?php } ?> 
                                    </div>                                                                        
                                        <?php } }
                                    } ?> 
                                </div>
                                <div class="row">                                    
                                    <div class="filtro_opciones" style="border-top-style: none; padding-left: 26px;" id="filtro_opciones_guardar_filtro_actual">
                                        <span class="icon-caret-right icon-only"></span>&nbsp;&nbsp;<?php echo lang('guardar_filtro_actual'); ?>
                                    </div>
                                    <div class="option_content"  id="filtro_opciones_guardar_filtro_actual_display" style="display: none;">
                                        <div class="row" style="padding: 0px;">
                                            <div class="form-group col-md-12 col-xs-12">                                        
                                                <input class="form-control input-sm" style="width: 94%" type="text" placeholder="<?php echo lang("nombre_del_filtro"); ?>" value="" name="save_filter_name">
                                            </div>
                                        </div>
                                        <div class="row" style="padding: 0px;">
                                            <div class="form-group col-md-6 col-xs-6" style="margin-bottom: 0px;">
                                                <div class="checkbox">
                                                    <label>
                                                        <input class="ace" type="checkbox" name="guardar_filtro_compartir_con_todos">
                                                        <span class="lbl"><?php echo lang("compartir_con_todos"); ?></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6 col-xs-6" style="margin-bottom: 0px;">
                                                <div class="checkbox">
                                                    <label>
                                                        <input class="ace" type="checkbox" name="guardar_filtro_usar_por_defecto">
                                                        <span class="lbl"><?php echo lang("usar_por_defecto"); ?></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <center>
                                            <button class="btn btn-sm btn-success" type="button" name="btn_guardar_filtros_usuarios"><?php echo lang('guardar'); ?></button>
                                        </center>
                                    </div>                                    
                                </div>
                                
                                <!--BUSQUEDA AVANZADA-->
                                <div class="filtro_opciones" id="filtro_opciones_busqueda_avanzada">
                                    <span class="icon-caret-right icon-only"></span>&nbsp;&nbsp;<?php echo lang('busqueda_avanzada'); ?>
                                </div>
                                <div class="option_content" id="filtro_opciones_busqueda_avanzada_display" style="display: none;">
                                    <div name="grupo_filtros_avanzados">
                                        <!--GRAFICA FILTROS AVANZADOS-->
                                        <?php if (isset($filters['default']) && $filters['default'] > -1 
                                                && isset($filters['filters'][$filters['default']]['advanced_filters'])){ 
                                            $filtrosAvanzados = $filters['filters'][$filters['default']]['advanced_filters'];
                                            foreach ($filtrosAvanzados as $key => $filtroAvanzado){ ?>
                                                <div class="row filter_<?php echo $key ?>" id="<?php echo $key ?>" name="filtro_avanzado_usuario" style="border-style: none; padding: 0px; background-color: white;">                                                    
                                                    <div class="form-group col-md-3" id="campo_1">
                                                        <select name="filtro_avanzado_usuario_campo" id="<?php echo $key ?>" style="width: 100%" class="filter_<?php echo $key ?>">
                                                            <option value="-1">(<?php echo strtolower(lang('SELECCIONE_UNA_OPCION')); ?>)</option>
                                                            <?php foreach ($reporte['columns'] as $id => $myColumn){ ?>
                                                            <option value="<?php echo $id ?>" <?php if ($id == $filtroAvanzado['field']){ ?> selected="true" <?php } ?>>
                                                                <?php echo $myColumn->display; ?>
                                                            </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-md-3" id="condicion_1">
                                                        <select name="filtro_avanzado_usuario_condicion" id="<?php echo $key ?>" class="filter_<?php echo $key ?>" style="width: 100%">
                                                            <?php 
                                                            $tipoDato = '';
                                                            if (isset($filtroAvanzado['data_set']['filters'])){ 
                                                                foreach ($filtroAvanzado['data_set']['filters'] as $dataType){ ?> 
                                                            <option value="<?php echo $dataType['id'] ?>"
                                                                    <?php if ($filtroAvanzado['filter'] == $dataType['id']){ ?> selected="true" <?php } ?>>
                                                                <?php echo $dataType['display']; ?>
                                                            </option>
                                                                <?php }
                                                            } else { ?>
                                                            <option value="-1">(<?php echo strtolower(lang('seleccione')); ?>)</option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-md-5 filter_<?php echo $key ?>" id="valores_1" name="filtro_avanzado_div_valores">
                                                        <?php if (isset($filtroAvanzado['data_set']['type']) && $filtroAvanzado['data_set']['type'] == "simple"){
                                                                switch ($filtroAvanzado['dataType']) {
                                                                    case "integer": ?>
                                                                        <input type="text" name="filtro_avanzado_usuario_valor" class="filter_<?php echo $key ?>_0" 
                                                                                value="<?php echo $filtroAvanzado['value1'] ?>" onkeypress="return ingresarNumero(this, event);">
                                                                        <?php if ($filtroAvanzado['filter'] == "entre"){ ?> 
                                                                        <input type="text" name="filtro_avanzado_usuario_valor" class="filter_<?php echo $key ?>_1" 
                                                                                value="<?php echo $filtroAvanzado['value2'] ?>" onkeypress="return ingresarNumero(this, event);">   
                                                                        <?php } 
                                                                        break;
                                                                    
                                                                    case "string": ?>
                                                                        <input type="text" name="filtro_avanzado_usuario_valor" class="filter_<?php echo $key ?>_0"
                                                                               <?php echo $filtroAvanzado['value1'] ?>>
                                                                        <?php if ($filtroAvanzado['filter'] == "entre"){ ?>
                                                                        <input type="text" name="filtro_avanzado_usuario_valor" class="filter_<?php echo $key ?>_1"
                                                                               <?php echo $filtroAvanzado['value2'] ?>>
                                                                        <?php }
                                                                        break;
                                                                    
                                                                    case "date": ?>
                                                                        <div class="input-group">
                                                                            <input name="filtro_avanzado_usuario_valor" class="estilo_date_picker form-control date-picker filter_<?php echo $key ?>_0" 
                                                                                   value="<?php echo $filtroAvanzado['value1'] ?>" type="text" readyonly="true">
                                                                            <span class="input-group-addon" style="padding: 3px 6px;">
                                                                                <i class="icon-calendar bigger-110"></i>
                                                                            </span>
                                                                        <?php if ($filtroAvanzado['filter'] == "entre"){ ?>                                                                        
                                                                            <input name="filtro_avanzado_usuario_valor" class="estilo_date_picker form-control date-picker filter_<?php echo $key ?>_1" 
                                                                                   value="<?php echo $filtroAvanzado['value2'] ?>" type="text" readyonly="true">
                                                                            <span class="input-group-addon" style="padding: 3px 6px;">
                                                                                <i class="icon-calendar bigger-110"></i>
                                                                            </span>
                                                                        <?php } ?>                                                                        
                                                                        </div>
                                                                        <?php
                                                                        break;
                                                                    
                                                                    case "boolean": ?>
                                                                        <select name="filtro_avanzado_usuario_valor" class="filter_<?php echo $key ?>_0">
                                                                            <option value="true"><?php echo lang("verdadero"); ?></option>
                                                                            <option value="false"><?php echo lang("falso"); ?></option>
                                                                        </select>
                                                                        <?php
                                                                        break;
                                                                    
                                                                    default:
                                                                        break;
                                                                }                                                            
                                                        } else if (isset($filtroAvanzado['data_set']['set'])) { ?>
                                                            <select name="filtro_avanzado_usuario_valor" class="select-chosen filter_<?php echo $key ?>_0" style="width: 236px;">
                                                                <?php foreach ($filtroAvanzado['data_set']['set'] as $setValue){ ?> 
                                                                <option value="<?php echo $setValue['id'] ?>"
                                                                        <?php if ($setValue['id'] == $filtroAvanzado['value1']){ ?> selected="true" <?php } ?>>
                                                                    <?php echo $setValue['value'] ?>
                                                                </option>
                                                                <?php } ?>
                                                            </select>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="form-group col-md-1">
                                                        <i class="icon-remove red" style="cursor: pointer" name="remove_filtro_avanzado" id="<?php echo $key ?>" class="filter_<?php echo $key ?>"></i>
                                                    </div>
                                                    <input type="hidden" class="filter_<?php echo $key ?>" name="filtro_avanzado_data_type" 
                                                           value="<?php echo $filtroAvanzado['dataType'] ?>">
                                                    <input type="hidden" class="filter_<?php echo $key ?>" name="filtro_avanzado_set_values" value=''>
                                                    <?php if (isset($filtroAvanzado['data_set']['set'])){ ?>
                                                    <script>
                                                        var jsonset = '<?php echo str_replace("'", "´", json_encode($filtroAvanzado['data_set']['set'])) ?>';
                                                        $("[name=filtro_avanzado_set_values].filter_<?php echo $key ?>").val(jsonset);
                                                    </script>
                                                    <?php } ?>
                                                </div>
                                            <?php } ?> 
                                                <input type="hidden" value="<?php echo $key ?>" name="cantidad_registros_filtros_usuarios">
                                        <?php } else { ?>
                                        <!--GRAFICA FILTROS POR DEFECTO-->
                                        <div class="row filter_0" id="0" name="filtro_avanzado_usuario" style="border-style: none; padding: 0px;">
                                            <input type="hidden" class="filter_0" name="filtro_avanzado_data_type" value="">
                                            <input type="hidden" class="filter_0" name="filtro_avanzado_set_values" value="">
                                            <div class="form-group col-md-3" id="campo_1">
                                                <select name="filtro_avanzado_usuario_campo" id="0" style="width: 100%" class="filter_0">
                                                    <option value="-1">(<?php echo strtolower(lang('SELECCIONE_UNA_OPCION')); ?>)</option>
                                                    <?php foreach ($reporte['columns'] as $id => $myColumn){ ?>
                                                        <?php if($id != 'curso_titulo') { ?>
                                                            <option value="<?php echo $id ?>"><?php echo $myColumn->display; ?></option>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-3" id="condicion_1">
                                                <select name="filtro_avanzado_usuario_condicion" id="0" class="filter_0" style="width: 100%">
                                                    <option value="-1">(<?php echo strtolower(lang("SELECCIONE_UNA_OPCION")); ?>)</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-5 filter_0" id="valores_1" name="filtro_avanzado_div_valores">

                                            </div>
                                            <div class="form-group col-md-1">
                                                <i class="icon-remove red" style="cursor: pointer" name="remove_filtro_avanzado" id="0" class="filter_0"></i>
                                            </div>
                                        </div>
                                        <input type="hidden" value="0" name="cantidad_registros_filtros_usuarios">
                                        <?php } ?>
                                    </div>
                                    <span style="cursor: pointer;" id="agregar_filtro_busqueda_usuario">
                                        <i class="icon-plus smaller-75"></i>&nbsp;&nbsp;<?php echo lang("agregar_condicion"); ?>
                                    </span>
                                    <div>
                                        <center>
                                            <button class="btn btn-sm btn-success" name="btnBuscar" type="button"><?php echo lang("buscar"); ?></button>
                                        </center>
                                    </div>
                                    
<!--                                        <center>
                                            <button class="btn btn-sm btn-success" name="btnImprimir" type="button" onclick="getTable(1);" ><?php echo lang("imprimir"); ?></button>
                                        </center>-->
                                    
                                </div>
                                
                                <!--AGREGAR QUITAR COLUMNAS-->
                                <div class="filtro_opciones" id="filtro_opciones_show_hide_columns">
                                    <span class="icon-caret-right icon-only"></span>&nbsp;&nbsp;<?php echo lang('agregar_quitar_columnas'); ?>
                                </div>
                                <div class="option_content" id="filtro_opciones_show_hide_columns_display" style="display: none;">
                                    <table style="width: 100%">
                                    <?php $i = 1;
                                    foreach ($reporte['columns'] as $id => $myColumn){ 
                                        if (isset($filters['default']) && $filters['default'] > -1 && isset($filters['filters'][$filters['default']])){
                                            $visible = in_array($id, $filters['filters'][$filters['default']]['field_view']);
                                        } else {
                                            $visible = $myColumn->visible;
                                        }
                                    if (($i + 1) % 2 == 0){ 
                                        
                                        ?> 
                                        <tr>
                                    <?php } ?>
                                            <td>                                        
                                                <label>
                                                    <input class="ace <?php echo $id  ?>" type="checkbox" name="columnas_visibles" id="<?php echo $id  ?>" value="<?php echo $id ?>"
                                                           <?php if ($visible){ ?> checked="true" <?php } ?> onclick="hideShowColumns(this);">
                                                    <span class="lbl"><?php echo $myColumn->display ?></span>
                                                </label>                                        
                                            </td>
                                    <?php if ($i % 2 == 0){ ?> 
                                        </tr>
                                    <?php }
                                    $i++; } ?>
                                    </table>
                                </div>                            
                            </div>
                            <!--/FILTROS DE LA TABLA-->
                    </div>
                </div>
                
                
            </div>
            
            <!--TABLE-->
            <table id="DataTables_Table_0" class="table table-striped table-bordered dataTable" border="0" cellspacing="0" cellpadding="0" style="width: 100%;" name="tabla_reportes" aria-describedby="DataTables_Table_0_info">
                <thead id="table_head">
                    <tr role="row">
                        <?php foreach ($reporte['columns'] as $id => $myColumn){ 
                            if (isset($filters['default']) && $filters['default'] > -1 && isset($filters['filters'][$filters['default']])){
                                $visible = in_array($id, $filters['filters'][$filters['default']]['field_view']);
                            } else {
                                $visible = $myColumn->visible;
                            } ?>
                            <!-- Donde aparecen los encabezados de la tabla -->
                        <th class="sorting table_col_<?php echo $id ?> tableHead" role="columnheader" tabindex="0" aria-controls="DataTables_Table_0" 
                            rowspan="1" colspan="1" aria-label="<?php echo $myColumn->display; ?>: activate to sort column ascending" 
                            id='table_th_<?php echo $id ?>' style='display: <?php  echo $visible ? "table-cell;" : "none;" ?>'
                            name="<?php echo $id ?>">
                            <?php echo $myColumn->display; ?>
                        </th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody  id="table_body" role="alert" aria-live="polite" aria-relevant="all">

                    <?php 
                    //Donde se despliega la data del reporte
                    if (count($reporte['aaData']) > 0){
                        $arrColumnasAcumulables = '';
                        foreach($reporte['columns'] as $nom_col=>$objColumna){
                            if($objColumna->acumulable){
                                $arrColumnasAcumulables[$nom_col] = array("total_acumulado"=>'');
                            }
                        }
                       $campoAcumulable = false;
                      
                        foreach ($reporte['aaData'] as $data){ ?> 
                    <tr>
                        <?php
                        if($arrColumnasAcumulables != ''){
                            foreach($arrColumnasAcumulables as $nomCol=>$valor){
                               $arrColumnasAcumulables[$nomCol]['total_acumulado'] = $arrColumnasAcumulables[$nomCol]['total_acumulado'] + $data[$nomCol];
                           } 
                        }
                        
                        foreach ($reporte['columns'] as $id => $myColumn){ 
//                            if ($myColumn->acumulable){ 
//                                $campoAcumulable += $data[$id]; 
//                            }
                          
                            if (isset($filters['default']) && $filters['default'] > -1 && isset($filters['filters'][$filters['default']])){
                                $visible = in_array($id, $filters['filters'][$filters['default']]['field_view']);
                            } else {
                                $visible = $myColumn->visible;
                            } ?> 
                        <td class="table_col_<?php echo $id ?>" style='display: <?php echo $visible ? "table-cell;" : "none;"?>'>
                            <?php echo $data[$id] ?>
                        </td>
                        <?php } ?>
                    </tr>
                        <?php }                        
                    } else { ?>                    
                    <tr class="odd">
                        <td class="dataTables_empty" valign="top" colspan="<?php echo count($reporte['columns']) ?>">
                            <?php echo lang('no_hay_datos_disponivles_pata_mostrar'); ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
                <?php 
                
                if (isset($arrColumnasAcumulables) && $arrColumnasAcumulables !== ''){?>            
                <tfoot style='background-color: #F6F6F6;'>
                    <tr>        
                        <?php $i = 0;?>
                        <?php foreach ($reporte['columns'] as $id => $myColumn){
                            if (isset($filters['default']) && $filters['default'] > -1 && isset($filters['filters'][$filters['default']])){
                                $visible = in_array($id, $filters['filters'][$filters['default']]['field_view']);
                            } else {
                                $visible = $myColumn->visible;
                            } ?> 
                
                        <td class="table_col_<?php echo $id ?>" style='display: <?php echo $visible ? "table-cell;" : "none;"?>'><?php echo $i == 0 ? '<strong>'.lang('total').'</strong>' : ''?>
                            <?php 
                            if($arrColumnasAcumulables != ''){
                                foreach($arrColumnasAcumulables as $nCol=>$val){
                               if ($nCol == $id){ ?>
                            <span style='font-size: 15px;' name='valor_acumulable'><?php echo $arrColumnasAcumulables[$nCol]['total_acumulado'];?></span>
                            <?php } else { ?>&nbsp;<?php }  }}?> 
                            
                            
                           
                        </td>
                        <?php $i++;} ?>
                    </tr>
                </tfoot>
                <?php } ?>
            </table>
            
            <div class="row">
                
                <!--SHOWING-->
                <div class="col-sm-6">
                    <div id="administracionCobros_info" class="dataTables_info">
                        <?php echo lang("mostrando"); ?> 
                        <span id='limit_min'><?php echo $reporte['iLimitMin']; ?></span>
                        <?php echo lang("hasta"); ?> 
                        <span id='limit-top'><?php echo count($reporte['aaData']) > $reporte['iLimitMax'] ? $reporte['iLimitMax'] : count($reporte['aaData']) ?></span>
                        <?php echo lang("de"); ?> 
                        <span id='rows_total'><?php echo $reporte['iTotalRecords'] ?></span>
                        <?php echo lang("registros"); ?>
                    </div>
                </div>
                
                <!--PAGINATION-->
                <div class="col-sm-6">
                    <div class="dataTables_paginate paging_bootstrap" style="width: 100%;">
                        <ul class="pagination">
                            <li class="prev<?php if ($reporte['iPagesCount'] == 1){ ?> disabled<?php } ?>">
                                <a id="prev">
                                    <i class="icon-double-angle-left"></i>
                                </a>
                            </li>
                            <?php if ($reporte['iPagesCount'] > 1){
                                $imprimirSalto = $reporte['iPagesCount'] > 15;                                
                                $startSalto = 8;
                                $endSanlto = $reporte['iPagesCount'] - 3;
                                for ($i = 1; $i <= $reporte['iPagesCount']; $i++){ 
                                    if ($imprimirSalto && $i >= $startSalto && $i < $endSanlto){
                                        if ($i == $startSalto){ ?>
                            <li class="step disabled">
                                <a id="step">...</a>
                            </li>
                                        <?php }                                            
                                    } else { ?>
                            <li id="<?php echo $i ?>" <?php if ($i == 1){ ?>class="active" <?php } ?>>
                                <a class="number page_<?php echo $i ?>" id="<?php echo $i ?>"><?php echo $i; ?></a>
                            </li>
                                    <?php } 
                                }
                            } ?>
                            <li class="next<?php if ($reporte['iPagesCount'] == 1){ ?> disabled<?php } ?>">
                                <a id="next">
                                    <i class="icon-double-angle-right"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
<select name="filtro_avanzado_usuario_campo_original" style="display: none;">
    <option value="-1">(<?php echo lang("SELECCIONE_UNA_OPCION") ?>)</option>
    <?php foreach ($reporte['columns'] as $id => $myColumn){ ?> 
    <option value="<?php echo $id ?>"><?php echo $myColumn->display; ?></option>
    <?php } ?>
</select>
<?php // echo "<pre>"; print_r($filters); echo "</pre>"; ?>