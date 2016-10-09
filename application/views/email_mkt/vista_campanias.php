<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal.css')?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal-bs3patch.css')?>"/>
<script src="<?php base_url()?>assents/js/librerias/bootstrap-modal/bootstrap-modal.js"></script>
<script src="<?php base_url()?>assents/js/librerias/bootstrap-modal/bootstrap-modalmanager.js"></script>
<script src="<?php echo base_url("assents/js/librerias/datatables/jquery.dataTables.min.js")?>"></script>
<!-- 601 -->
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

<link rel="stylesheet" href="<?php echo base_url('assents/css/consultasweb/consultasweb.css')?>"/>
<!-- 601 -->

<script src="<?php echo base_url('assents/theme/assets/js/bootstrap-tag.min.js')?>"></script>
<script src="<?php echo base_url('assents/theme/assets/js/jquery.hotkeys.min.js')?>"></script>
<script src="<?php echo base_url('assents/theme/assets/js/bootstrap-wysiwyg.min.js')?>"></script>
<script src="<?php echo base_url('assents/theme/assets/js/jquery-ui-1.10.3.custom.min.js')?>"></script>
<script src="<?php echo base_url('assents/theme/assets/js/jquery.ui.touch-punch.min.js')?>"></script>
<script src="<?php echo base_url('assents/theme/assets/js/jquery.slimscroll.min.js')?>"></script>

<script src="<?php echo base_url('assents/js/consultasweb/consultasweb.js')?>"></script>
<!-- Script de prueba, borrarlo atnes de commitear el reporte de consultas web -->
<script>
    var probarWS = function (queryData, varName) {
        $.ajax({
            url: BASE_URL + 'consultasweb/reporte_consultas_paneldecontrol_comparativo_capaz_nose',
            type: 'POST',
            data:queryData,
            success: function (response){
                respuesta = JSON.parse(response);
                console.log(response);
                if(varName){
                    window[varName] = respuesta;
                }
            }
        });
    }
</script>
<style>
    .message-item .time{
        width: auto!important;
    }

    #tdContenedorResponder input{
        padding-bottom: 0px;
        padding-top: 0px;
    }

    #tdContenedorResponder select{
        height: 22px;
        padding-bottom: 0;
        padding-top: 0;
        width: 160px;
    }

    .field_required:enabled {
        border: 1px solid red;
    }

    .field_required:disabled {
        border: 1px solid #CACACA;
    }
</style>
<div class="col-xs-12">

    <div class="row">
        <div class="col-xs-12">
            <div class="tabbable">
                <ul id="inbox-tabs" class="inbox-tabs nav nav-tabs padding-16 tab-size-bigger tab-space-1">
                    <li class="active">
                        <a data-toggle="tab" href="#inbox" >
                            <i class="blue icon-inbox bigger-130"></i>
                            <span class="bigger-110"><?php echo lang('campanias'); ?></span>
                        </a>
                    </li>
                    <?php /*
                    <li>
                        <a data-toggle="tab" href="#inbox_externa">
                            <i class="red icon-inbox bigger-130"></i>
                            <span class="bigger-110"><?php echo lang('inbox_externa'); ?></span>
                        </a>
                    </li>
					*/ ?>
                    <!-- 601
                        <li>
                        <a data-toggle="tab" href="#cerradas" >
                            <i class="orange icon-location-arrow bigger-130 "></i>
                            <span class="bigger-110"><?php// echo lang('enviadas'); ?></span>
                        </a>
                    </li>-->
                    <li>
                        <a data-toggle="tab" href="#eliminado" >
                            <i class="green icon-pencil bigger-130"></i>
                            <span class="bigger-110"><?php echo lang('listas') ?></span>
                        </a>
                    </li>
                </ul>
                <button class="btn btn-purple" style="float: right; margin-right: 16px; margin-top: -50px;" onclick="nuevaConsulta()">
                    <i class=" icon-envelope bigger-130"></i>
                    <?php echo lang('nueva_consulta'); ?>
                </button>
                <div class="tab-content no-border no-padding">
                    <div class="tab-pane active" id="inbox">
                        <div class="message-container">
                            <div class="detalleList">

                                <div id="id-message-list-navbar" class="message-navbar align-center clearfix">
                                    <div style="padding-bottom: 10px;">
                                        <div class="nav-search minimized pull-right" style="position: inherit;">

                                            <form class="form-search">
                                            <span class="input-icon">
                                                <input type="text" value="" autocomplete="off" class="input-small nav-search-input" placeholder="<?php echo lang('buscar');?>">
                                                <i class="icon-search nav-search-icon"></i>

                                            </span>
                                                <i class="icon-caret-down grey bigger-110 bigger-140" style="margin-right: 3px; cursor: pointer" name="table_filters" title="<?php echo lang('busqueda_avanzada');?>"></i>
                                                <!-- Inicio Busqueda Avanzada -->
                                                <div class="table_filter" id="div_table_filters" name="div_table_filters" style="display: none;">

                                                    <div class="option_content" id="filtro_opciones_busqueda_avanzada_display" >
                                                        <div name="grupo_filtros_avanzados">
                                                            <div class="row filter_0" id="0" name="filtro_avanzado_usuario" style="border-style: none; padding: 0px;">
                                                                <div class="form-group col-md-12" >
                                                                    <span><?php echo lang("estado_busqueda") ?></span>
                                                                    <select name="filtro_estado" class="select_chosen" style="width: 300px;">
                                                                        <option value="-1">(<?php echo lang("todos"); ?>)</option>
                                                                        <option value="0"><?php echo lang('no_leidos'); ?></option>
                                                                        <option value="1"><?php echo lang('leidos'); ?></option>
                                                                        <!--<option value="2">No Respondido</option>-->
                                                                        <option value="3"><?php echo lang('respondidas'); ?></option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group col-md-12" >
                                                                    <span><?php echo lang("curso_busqueda") ?></span>
                                                                    <select name="filtro_curso" class="select_chosen" style="width: 300px;">
                                                                        <option value="-1">(<?php echo lang("todos"); ?>)</option>
                                                                        <?php foreach ($arrCursos as $curso){ ?>
                                                                            <option value="<?php echo $curso['codigo'] ?>">
                                                                                <?php echo $curso['nombre'] ?>
                                                                            </option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group col-md-6" id="campo_1">
                                                                    <span><?php echo lang('fecha_desde'); ?></span>
                                                                    <div class="input-group">
                                                                        <input name="FechaDesde" value="" class="date-picker" type="text" readyonly="true" style="margin-right: 0px; width: 96px;">
                                                                <span class="input-group-addon" style="padding: 3px 6px;">
                                                                    <i class="icon-calendar bigger-110"></i>
                                                                </span>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group col-md-6" id="condicion_1">
                                                                    <span><?php echo lang('fecha_hasta'); ?></span>
                                                                    <div class="input-group">
                                                                        <input name="FechaHasta" value="" class="date-picker" type="text" readyonly="true" style="margin-right: 0px; width: 96px;">
                                                                <span class="input-group-addon" style="padding: 3px 6px;">
                                                                    <i class="icon-calendar bigger-110"></i>
                                                                </span>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                            <input type="hidden" value="0" name="cantidad_registros_filtros_usuarios">
                                                        </div>
                                                        <div>
                                                            <center>
                                                                <button class="btn btn-sm btn-success" name="btnBuscar" type="button" onclick="init();"><?php echo lang("buscar"); ?></button>
                                                            </center>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div name="contenedorPrincipal" style=" bottom: 0; height: 100%; left: 0; position: fixed; width: 100%; z-index: 20; display: none;"></div>
                                                <!-- Fin Busqueda Avanzada -->
                                            </form>
                                        </div>
                                    </div>

                                    <br>

                                    <div class="message-bar">
                                        <div class="message-infobar" id="id-message-infobar">
                                            <span class="blue bigger-150"><?php echo lang("asunto_comunicado"); ?></span>
                                            <span class="grey bigger-110"></span>
                                        </div>
                                        <div class="message-toolbar hide" name="botones_acciones">
                                            <!-- Ticket 601
                                                <div class="inline position-relative align-left">
                                                <a href="#" class="btn-message btn btn-xs dropdown-toggle" data-toggle="dropdown">
                                                    <i class="icon-folder-close-alt bigger-110"></i>
                                                    <span class="bigger-110"><?php/* echo lang("enviar_a"); ?></span>
                                                    <i class="icon-caret-down icon-on-right"></i>
                                                </a>

                                                <ul class="dropdownList dropdown-menu dropdown-lighter dropdown-caret dropdown-125">
                                                    <li>
                                                        <a href="cerrado">
                                                            <i class="icon-stop pink2"></i>
                                                            &nbsp; <?php echo lang("concretadas"); ?>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="noconcretada">
                                                            <i class="icon-stop blue"></i>
                                                            &nbsp; <?php echo lang("noconcretada"); */?>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>-->
                                            <a href="eliminada" class="borrarList btn btn-xs btn-message">
                                                <i class="icon-trash bigger-125"></i>
                                                <span class="bigger-110"><?php echo lang("eliminar"); ?></span>
                                            </a>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="messagebar-item-left">
                                            <label class="inline middle">
                                                <input type="checkbox"  class="ace id-toggle-all">
                                                <span class="lbl"></span>
                                            </label>
                                            &nbsp;
                                            <div class="inline position-relative">
                                                <a href="#" data-toggle="dropdown" class="dropdown-toggle">
                                                    <i class="icon-caret-down bigger-125 middle"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-lighter dropdown-100">
                                                    <li>
                                                        <a class="id-select-message-all" href="#"><?php echo lang('todos'); ?></a>
                                                    </li>
                                                    <li>
                                                        <a class="id-select-message-none" href="#"><?php echo lang('ninguno'); ?></a>
                                                    </li>
                                                    <li class="divider"></li>
                                                    <li>
                                                        <a class="id-select-message-unread" href="#"><?php echo lang('leidos'); ?></a>
                                                    </li>
                                                    <li>
                                                        <a class="id-select-message-read" href="#"><?php echo lang('no_leidos'); ?></a>
                                                    </li>

                                                </ul>
                                            </div>
                                        </div>
                                        <div class="nav-search minimized">
                                            <span class="grey bigger-110"><?php echo lang('remitente'); ?></span>
                                        </div>
                                        <div class="inline messagebar-item-right">
                                            <span class="grey bigger-110"><?php echo lang('fecha'); ?></span>
                                        </div>

                                    </div>

                                </div>

                                <div class="message-list-container">
                                    <form id="frminbox">
                                        <div class="message-list" id="message-list">

                                        </div>
                                    </form>
                                </div>
                                <div class="message-footer clearfix">
                                    <div class="pull-left" id="TotalMensajes" > </div>
                                    <div class="pull-right">
                                        <div class="inline middle"> </div>
                                        &nbsp; &nbsp;
                                        <ul class="pagination middle">
                                            <li class="firthPage">
                                                <span>
                                                    <i class="icon-step-backward middle"></i>
                                                </span>
                                            </li>
                                            <li class="prevPage">
                                                <span href="#">
                                                    <i class="icon-caret-left bigger-140 middle"></i>
                                                </span>
                                            </li>
                                            <li>
                                                <span>
                                                    <input name="numeroPagina" value="1"  type="text" readonly>
                                                </span>
                                            </li>
                                            <li class="nexPage ">
                                                <span href="#" >
                                                    <i class="icon-caret-right bigger-140 middle"></i>
                                                </span>
                                            </li>
                                            <li class="lastPage">
                                                <span href="#">
                                                    <i class="icon-step-forward middle"></i>
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div id="id-message-list-navbar" class="message-navbar align-center clearfix">
                                <div class="inline messagebar-item-right">
                                    <div class="nav-search minimized">
                                        <form class="form-search">
                                                    <span class="input-icon">
                                                        <input type="text" value="" autocomplete="off" class="input-small nav-search-input" placeholder="<?php echo lang('buscar');?>">
                                                        <i class="icon-search nav-search-icon"></i>
                                                    </span>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>





                    <div class="tab-pane active hide" id="inbox_externa">
                        <div class="message-container">
                            <div class="detalleList">
                                <div id="id-message-list-navbar" class="message-navbar align-center clearfix">
                                    <div class="message-bar">
                                        <div class="message-infobar" id="id-message-infobar">
                                            <span class="blue bigger-150"><?php echo "Inbox Externa";//lang("inbox_externa"); ?></span>
                                            <span class="grey bigger-110"></span>
                                        </div>
                                        <div class="message-toolbar hide" name="botones_acciones">
                                            <a id="marcar-como-leidos-boton" href="#" class="btn-message btn btn-xs" data-toggle="dropdown">
                                                <i class="icon-eye-open bigger-110"></i>
                                                <span class="bigger-110"><?php echo lang("marcar_como_leidos"); ?></span>
                                            </a>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="messagebar-item-left">
                                            <label class="inline middle">
                                                <input type="checkbox"  class="ace id-toggle-all">
                                                <span class="lbl"></span>
                                            </label>
                                            &nbsp;
                                            <div class="inline position-relative">
                                                <a href="#" data-toggle="dropdown" class="dropdown-toggle">
                                                    <i class="icon-caret-down bigger-125 middle"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-lighter dropdown-100">
                                                    <li>
                                                        <a class="id-select-message-all" href="#"><?php echo lang('todos'); ?></a>
                                                    </li>
                                                    <li>
                                                        <a class="id-select-message-none" href="#"><?php echo lang('ninguno'); ?></a>
                                                    </li>
                                                    <li class="divider"></li>
                                                    <li>
                                                        <a class="id-select-message-unread" href="#"><?php echo lang('leidos'); ?></a>
                                                    </li>
                                                    <li>
                                                        <a class="id-select-message-read" href="#"><?php echo lang('no_leidos'); ?></a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="nav-search minimized">
                                            <form class="form-search">
                                                <span class="input-icon">
                                                    <input type="text" value="" autocomplete="off" class="input-small nav-search-input" placeholder="<?php echo lang('buscar');?>">
                                                    <i class="icon-search nav-search-icon"></i>
                                                </span>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="message-list-container">
                                    <form id="frm_inbox_externa">
                                        <div class="message-list" id="message-list">

                                        </div>
                                    </form>
                                </div>
                                <div class="message-footer clearfix">
                                    <div class="pull-left" id="TotalMensajes" > </div>
                                    <div class="pull-right">
                                        <div class="inline middle"> </div>
                                        &nbsp; &nbsp;
                                        <ul class="pagination middle">
                                            <li class="firthPage">
                                                <span>
                                                    <i class="icon-step-backward middle"></i>
                                                </span>
                                            </li>
                                            <li class="prevPage">
                                                <span href="#">
                                                    <i class="icon-caret-left bigger-140 middle"></i>
                                                </span>
                                            </li>
                                            <li>
                                                <span>
                                                    <input name="numeroPagina" value="1"  type="text" readonly>
                                                </span>
                                            </li>
                                            <li class="nexPage ">
                                                <span href="#" >
                                                    <i class="icon-caret-right bigger-140 middle"></i>
                                                </span>
                                            </li>
                                            <li class="lastPage">
                                                <span href="#">
                                                    <i class="icon-step-forward middle"></i>
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ticket 601
                    <div class="tab-pane" id="cerradas">
                        <div class="message-container">
                            <div class="detalleList">
                                <div id="id-message-list-navbar" class="message-navbar align-center clearfix">
                                    <div class="message-bar">
                                        <div class="message-infobar" id="id-message-infobar">
                                            <span class="blue bigger-150"><?php// echo lang('cerradas'); ?></span>
                                            <span class="grey bigger-110"></span>
                                        </div>
                                        <div class="message-toolbar hide" name="botones_acciones">
                                            <a href="eliminado" class="borrarList btn btn-xs btn-message">
                                                <i class="icon-trash bigger-125"></i>
                                                <span class="bigger-110"><?php// echo lang('eliminar'); ?></span>
                                            </a>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="messagebar-item-left">
                                            <label class="inline middle">
                                                <input type="checkbox"  class="ace id-toggle-all">
                                                <span class="lbl"></span>
                                            </label>
                                                &nbsp;
                                            <div class="inline position-relative">
                                                <a href="#" data-toggle="dropdown" class="dropdown-toggle">
                                                    <i class="icon-caret-down bigger-125 middle"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-lighter dropdown-100">
                                                    <li>
                                                        <a class="id-select-message-all" href="#"><?php// echo lang('todos'); ?></a>
                                                    </li>
                                                    <li>
                                                        <a class="id-select-message-none" href="#"><?php// echo lang('ninguno'); ?></a>
                                                    </li>
                                                    <li class="divider"></li>

                                                    <li>
                                                        <a class="id-select-message-unread" href="#"><?php// echo lang('leidos'); ?></a>
                                                    </li>
                                                    <li>
                                                        <a class="id-select-message-read" href="#"><?php// echo lang('no_leidos'); ?></a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="nav-search minimized">
                                            <form id="cerradasSearch" class="form-search">
                                                <span class="input-icon">
                                                    <input type="text" value="" autocomplete="off" class="input-small nav-search-input" placeholder="<?php// echo lang('buscar');?>">
                                                    <i class="icon-search nav-search-icon"></i>
                                                </span>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="message-list-container">
                                    <form id="frmcerradas">
                                        <div class="message-list" id="message-list">
                                        </div>
                                    </form>
                                </div>
                                <div class="message-footer clearfix">
                                    <div class="pull-left" id="TotalMensajes" ></div>
                                    <div class="pull-right">
                                        <div class="inline middle"> </div>
                                        &nbsp; &nbsp;
                                        <ul class="pagination middle">
                                            <li class="firthPage">
                                                <span>
                                                    <i class="icon-step-backward middle"></i>
                                                </span>
                                            </li>
                                            <li class="prevPage">
                                                <span href="#">
                                                    <i class="icon-caret-left bigger-140 middle"></i>
                                                </span>
                                            </li>
                                            <li>
                                                <span>
                                                    <input name="numeroPagina" value="1"  type="text" readonly>
                                                </span>
                                            </li>
                                            <li class="nexPage">
                                                <span href="#">
                                                    <i class="icon-caret-right bigger-140 middle"></i>
                                                </span>
                                            </li>
                                            <li class="lastPage">
                                                <span href="#">
                                                    <i class="icon-step-forward middle"></i>
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>-->
                    <div class="tab-pane" id="eliminado">
                        <div class="message-container">
                            <div class="detalleList">
                                <div id="id-message-list-navbar" class="message-navbar align-center clearfix">
                                    <div class="message-bar">
                                        <div class="message-infobar" id="id-message-infobar">
                                            <span class="blue bigger-150"><?php echo lang('eliminadas'); ?></span>
                                            <span class="grey bigger-110"></span>
                                        </div>
                                        <div class="message-toolbar hide" name="botones_acciones">
                                            <div class="inline position-relative align-left">
                                                <a href="#" class="btn-message btn btn-xs dropdown-toggle" data-toggle="dropdown">
                                                    <i class="icon-folder-close-alt bigger-110"></i>
                                                    <span class="bigger-110"><?php echo lang('mover_a')?></span>
                                                    <i class="icon-caret-down icon-on-right"></i>
                                                </a>
                                                <ul class="dropdownList dropdown-menu dropdown-lighter dropdown-caret dropdown-125">
                                                    <li>
                                                        <a href="cerrado">
                                                            <i class="icon-stop pink2"></i>
                                                            &nbsp; <?php echo lang('cerradas'); ?>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="eliminado">
                                                            <i class="icon-stop blue"></i>
                                                            &nbsp; <?php echo lang('eliminadas'); ?>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="messagebar-item-left">
                                            <label class="inline middle">
                                                <input type="checkbox"  class="ace id-toggle-all">
                                                <span class="lbl"></span>
                                            </label>
                                            &nbsp;
                                            <div class="inline position-relative">
                                                <a href="#" data-toggle="dropdown" class="dropdown-toggle">
                                                    <i class="icon-caret-down bigger-125 middle"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-lighter dropdown-100">
                                                    <li>
                                                        <a class="id-select-message-all" href="#"><?php echo lang('todos'); ?></a>
                                                    </li>
                                                    <li>
                                                        <a class="id-select-message-none" href="#"><?php echo lang('ninguno'); ?></a>
                                                    </li>
                                                    <li class="divider"></li>
                                                    <li>
                                                        <a class="id-select-message-unread" href="#"><?php echo lang('no_leidas'); ?></a>
                                                    </li>
                                                    <li>
                                                        <a class="id-select-message-read" href="#"><?php echo lang('leidas'); ?></a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="nav-search minimized">
                                            <form class="form-search">
                                                <span class="input-icon">
                                                    <input type="text" value="" autocomplete="off" class="input-small nav-search-input" placeholder="<?php echo lang('buscar');?>">
                                                    <i class="icon-search nav-search-icon"></i>
                                                </span>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="message-list-container">
                                    <form id="frmeliminado">
                                        <div class="message-list" id="message-list">
                                        </div>
                                    </form>
                                </div>
                                <div class="message-footer clearfix">
                                    <div class="pull-left" id="TotalMensajes" >  </div>
                                    <div class="pull-right">
                                        <div class="inline middle">  </div>
                                        &nbsp; &nbsp;
                                        <ul class="pagination middle">
                                            <li class="firthPage">
                                                <span>
                                                    <i class="icon-step-backward middle"></i>
                                                </span>
                                            </li>
                                            <li class="prevPage">
                                                <span href="#">
                                                    <i class="icon-caret-left bigger-140 middle"></i>
                                                </span>
                                            </li>
                                            <li>
                                                <span>
                                                    <input name="numeroPagina" value="1"  type="text" readonly>
                                                </span>
                                            </li>
                                            <li  class="nexPage">
                                                <span href="#">
                                                    <i class="icon-caret-right bigger-140 middle"></i>
                                                </span>
                                            </li>
                                            <li class="lastPage">
                                                <span href="#">
                                                    <i class="icon-step-forward middle"></i>
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>





                </div>
            </div>
        </div>
    </div>


    <div class="vistaDetalle hide">
        <div id="id-message-item-navbar" class=" message-navbar align-center clearfix">
            <div class="message-bar">
                <div class="message-toolbar" name="botones_acciones">
                    <div class="inline position-relative align-left" >

                        <!-- Email message tools -->
                        <div id="inbox_externa_buttons" style="display: none;">
                            <a href="mostrar_form_nueva_consulta" class="btn-message btn btn-xs" data-toggle="dropdown">
                                <i class="icon-envelope-alt bigger-110"></i>
                                <span class="bigger-110"><?php echo lang("procesar_consulta"); ?></span>
                            </a>
                        </div>
                        <!-- end Email message tools -->

                        <a href="#" id="menuMover" class="btn-message btn btn-xs dropdown-toggle" data-toggle="dropdown">
                            <i class="icon-folder-close-alt bigger-110"></i>
                            <span class="bigger-110"><?php echo lang('mover_a')?></span>
                            <i class="icon-caret-down icon-on-right"></i>
                        </a>
                        <ul id="mover" class="dropdown-menu dropdown-lighter dropdown-caret dropdown-125">
                            <li>
                                <a href="abierta">
                                    <i class="icon-stop pink2"></i>
                                    &nbsp; <?php echo lang('inbox'); ?>
                                </a>
                            </li>
                            <!--
                            <li>
                                <a href="cerrado">
                                    <i class="icon-stop pink2"></i>
                                    &nbsp; <?php// echo lang('concretadas'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="eliminada">
                                    <i class="icon-stop blue"></i>
                                    &nbsp; <?php// echo lang('no_concretadas'); ?>
                                </a>
                            </li>-->
                        </ul>
                    </div>
                </div>
            </div>
            <div>
                <div class="messagebar-item-left">
                    <a href="#" class="btn-back-message-list">
                        <i class="icon-arrow-left blue bigger-110 middle"></i>
                        <b class="bigger-110 middle"><?php echo lang('volver'); ?></b>
                    </a>
                </div>
            </div>
        </div>
        <div class=" message-content" id="id-message-content">
        </div>
    </div>

</div>

<?php if (isset($accion)){ ?>
    <input type="hidden" data-accion="<?php echo $accion ?>" name="ver_consulta_web" value="<?php echo $codigo ?>">
<?php } ?>