<?php $idioma = get_idioma();
$nombreCurso = 'nombre_'.$idioma;
echo '<input name="nombreCurso" type="hidden" value="'.$nombreCurso.'">'; ?>
<link rel="stylesheet" href="<?php echo base_url('assents/theme/assets/css/jquery.gritter.css')?>"/>
<!--SCRIPT-->
<script src="<?php echo base_url('assents/js/librerias/moment/moment-with-langs.min.js')?>"></script>
<script src="<?php echo base_url('assents/theme/assets/js/jquery.gritter.min.js')?>"></script>
<style>
    .ausente{
        background: rgba(255,0,0,0.5);
    }
    .presente {
        background: rgba(50,200,50,0.5);
    }
</style>

<div class="col-md-12 col-xs-12">
    <div id="areaTablas" class="">
        <div class="row">
            <div class="col-md-4">
                <form  role="form">
                    <div class="form-group">
                        <label class="control-label no-padding-right" for="form-field-1"><?php echo lang('cursos')?></label>
                        <select id="form-field-1" class="width-100" name="cursos" onchange="habilitarBotones(true);" data-placeholder="<?php echo lang('seleccione_curso')?>">
                            <option></option>
                            <?php foreach($cursos as $curso){
                                echo '<option value="'.$curso['codigo'].'">'.$curso[$nombreCurso].'</option>';
                            } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label no-padding-right" for="form-field-1"><?php echo lang('comision')?></label>
                        <select id="form-field-1" class="width-100" name="comisiones" onchange="habilitarBotones(true);" data-placeholder="<?php echo lang('seleccione_comision');?>"></select>
                    </div>
                    <div class="form-group">
                        <label class=" control-label no-padding-right" for="form-field-1"><?php echo lang('materia')?></label>
                        <?php
                            $filial = $this->session->userdata('filial');
                            $filial = intval($filial['codigo']);
                            foreach ($filialesActivas as $fActiva) {
                                ($filial == $fActiva) ? $esta = 1 : $esta = 0;
                            }
                            if($esta == 0){
                        ?>
                            <select id="form-field-1" class=" width-100" name="clases"  data-placeholder="<?php echo lang('seleccione_materia');?>"></select>
                        <?php
                            } else {
                        ?>
                            <div class="input-group">
                                <select id="form-field-1" class=" width-100" name="clases"  data-placeholder="<?php echo lang('seleccione_materia');?>"></select>
                                <span class="input-group-btn">
                                    <button id="btnAsistenciasWeb" class="btn btn-xs btn-success disabled" type="button" data-rel="tooltip" title="Ver asistencias a las unidades del campus" data-materia="" data-comision="" >
                                        <i class="icon-youtube-play bigger-170"></i>
                                    </button>
                                </span>
                            </div>
                        <?php
                            }
                        ?>
                    </div>
                </form>
            </div>
            <div class="col-md-2 col-md-offset-5">
                <div class="row">
                    <div name="calendario-asistencia" class="no-margin no-padding" ></div>
                </div>
                <div class="row">
                    <div class="btn-group">
                        <div class="col-md-6 align-left ">
                            <a href="javascript:void(0)" class="" onclick="verFecha('anterior');" name="btnAnterior">←<?php echo lang('asistencias');?></a>
                        </div>
                        <div class="col-md-6 align-right">
                            <a href="javascript:void(0)" onclick="verFecha('siguiente');" name="btnAnterior"> <?php echo lang('asistencias').'→';?> </a>
                        </div>
                    </div>
                 </div>
                <div class="row">
                    <div class="col-md-12 text-center ">
                        <select id="periodo">
                            <option disabled selected>Periodo</option>
                            <option value="1 week">Semanal</option>
                            <option value="1 month">Mensual</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row"></div>
        <div class="hr-20"></div>
        <div class="row">
            <div  id="horarios" class="col-md-7"></div>
        </div>
        <div class="row pietablaAsistencias">
            <div class="col-md-6 no-padding-left ">
                <input type="hidden" id="fecha_seleccionada" value="">
                <div class=" col-md-12 btn-group left-block">
                    <button class="btn btn-primary boton-primario" name="btn_printer_job" disabled="true" onclick="imprimirAsistencias('','horizontalmente');">
                         <i class="icon-print bigger"></i>
                        <?php echo lang("imprimir_planilla") ?>
                    </button>
                    <button class="btn  dropdown-toggle btn-primary" data-toggle="dropdown">
                        <span class="icon-caret-down icon-only"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-default">
                        <li>
                            <a onclick="imprimirAsistencias('vacia','horizontalmente');"><?php echo lang('visualizar_planilla_vacia');?></a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6 right">
                <button id="guardar_asistencias" class="btn btn-success" type="button" style="float:right" onclick="guardar_asistencias();">
                    <i class="icon-ok bigger-110"></i>
                    <?php echo lang('guardar');?>
                </button>
                <button id="limpiar_asistencias" onclick="limpiar_asistencias();" type="button" class="btn btn btn-primary boton-primario" style="float:right; margin-right:10px">
                    <i class="icon-ok bigger-110"></i>
                    <?php echo "Limpiar asistencias"/*lang('guardar');*/?>
                </button>
            </div>
        </div>
        <div class="row">
            <div class="alert alert-danger hide">
                <?php echo lang('cargue_profesor_tabla_asistencia');?>
                <br>
            </div>
            <div class="col-md-12 ">
                <form id="asistencias">
                    <div class="table-responsive"></div>
                </form>
            </div>
        </div>
        <div></div>
        <div class="row pietablaAsistencias" >
            <div class="col-md-6 no-padding-left ">
                <input type="hidden" id="fecha_seleccionada" value="">
                <div class=" col-md-12 btn-group left-block">
                    <button class="btn btn-primary boton-primario" name="btn_printer_job" disabled="true" onclick="imprimirAsistencias('','horizontalmente');">
                         <i class="icon-print bigger"></i>
                        <?php echo lang("imprimir_planilla") ?>
                    </button>
                    <button class="btn  dropdown-toggle btn-primary" data-toggle="dropdown">
                        <span class="icon-caret-down icon-only"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-default">
                        <li>
                            <a onclick="imprimirAsistencias('vacia','horizontalmente');"><?php echo lang('visualizar_planilla_vacia');?></a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6 right">
                <button id="guardar_asistencias" class="btn btn-success" type="button" style="float:right" onclick="guardar_asistencias();">
                    <i class="icon-ok bigger-110"></i>
                    <?php echo lang('guardar');?>
                </button>
                <button id="limpiar_asistencias" type="button" onclick="limpiar_asistencias();" class="btn btn btn-primary boton-primario" style="float:right; margin-right:10px">
                    <i class="icon-ok bigger-110"></i>
                    <?php echo "Limpiar asistencias"/*lang('guardar');*/?>
                </button>
            </div>
        </div>
    </div>
</div>
<form name="frm_exportar" action="<?php echo base_url()."asistencias/get_asistencias_web_alumno" ?>" target="new_target" method="POST">
    <input type="hidden" value="" name="cod_comision">
    <input type="hidden" value="" name="cod_materia">
    <input type="hidden" value="" name="tipo_reporte">
    <input type="hidden" value="" name="iSortCol_0">
    <input type="hidden" value="" name="sSortDir_0">
    <input type="hidden" value="" name="iDisplayLength">
    <input type="hidden" value="" name="iDisplayStart">
    <input type="hidden" value="" name="sSearch">
    <input type="hidden" value="exportar" name="action">
</form>
<script src="<?php echo base_url('assents/js/asistencias/asistencias.js')?>"></script>
<script src="<?php echo base_url('assents/js/impresiones.js') ?>"></script>