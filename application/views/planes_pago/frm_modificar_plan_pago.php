<link rel="stylesheet" href="<?php echo base_url('assents/css/planes_pago/frm_plan_pago.css') ?>"/>
<!--Ticket 4393 -mmori- se cambia el idioma del datepicker para brasil-->
<?php $arpa = $this->session->userdata('filial');?>
<script>
    var langFrm = <?php echo $langFrm ?>;
    var separador_decimal = '<?php echo $separadorDecimal; ?>';
    var actualizar_periodos = <?php echo $actualizar_periodos ? "true" : "false" ?>;
    //Ticket 4393 -mmori- se cambia el idioma del datepicker para brasil
    var pais = <?php echo $arpa['pais'] ?>;
    var alertas = '<?php echo $alertas ?>';
    var puedeModificar = '<?php echo $puedeModificar ?>';
</script>
<script src="<?php echo base_url('assents/js/planes_pago/frm_plan_pago.js'); ?>"></script>
<script src="<?php echo base_url('assents/js/librerias/jquery-serialize/jquery.serializeJSON.min.js'); ?>"></script>
<div id="stack1" class="modal     " tabindex="-1" data-focus-on="input:first" data-width="80%">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3><?php echo lang('financiacion');?></h3>
    </div>
    <div class="modal-body financiacion-body">
    </div>
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn"><?php echo lang('cerrar');?></button>
        <button type="button" class="btn btn-primary btn-financiacion" name="btn_financiacion_guardar" onclick="guardarFinanciacionModificada()"><?php echo lang('guardar');?></button>
    </div>
</div>
<div class="modal-content">
    <?php
    $json = array(
        array(
            'valor'=>'1',
            'unidadTiempo'=>'days',
            'codigo'=>1,
            'baja'=>0
        ),
        array(
            'valor'=>'2',
            'unidadTiempo'=>'month',
            'codigo'=>2,
            'baja'=>0
        ),
        array(
            'valor'=>'2',
            'unidadTiempo'=>'year',
            'codigo'=>3,
            'baja'=>0
        )
    );
    ?>
    <div class="modal-header" style="padding-bottom: 0px; height: 41px;">
        <h4 class="blue bigger"><?php echo lang('modificar-planes-pago')?></h4>
    </div>
    <div class="modal-body overflow-auto" >
        <?php $cod_curso = isset($plancurso[0]["cod_curso"]) ? $plancurso[0]["cod_curso"] : ""; ?>
        <form role="form" id="frm-plan-pago">
            <input name="codigo-plan" id="codigo-plan" type="hidden" value="<?php echo $plan->getCodigo() ?>" >
            <input type="hidden" name="plan_original" value="<?php echo $plan_original ?>">
            <input name="config-periodo" id="config-periodo" name="config-periodo"  type="hidden" value="<?php echo $muestraPeriodo ?>" >
            <div class="row">
                <div class="form-group col-md-4 ">
                    <label><?php echo lang('nombre_plan');?></label>
                    <div>
                        <input type="text" class="form-control"  id="nombrePlan"  name="nombrePlan" value="<?php echo $plan->nombre ?>" placeholder="<?php echo lang('nombre');?>"
                            <?php if (!$puedeModificar){ ?> readonly="true" <?php } ?>>
                    </div>
                </div>
                <div class="form-group col-md-2">
                    <label>&nbsp;</label>
                    <?php if($descuento_condicionado != 0){ ?>
                        <label  for=""></label>
                        <div class="checkbox">
                            <label id="title_tooltip" title="<?php echo lang('alumnos_requisitos_perdera_descuento');?>">
                                <input type="checkbox" name="descuentocond" <?php if ($plan->descon === "1") { ?> checked="true" <?php } ?>
                                    <?php if (!$puedeModificar){ ?> disabled="true" <?php } ?>>
                                <?php echo lang('perdida_de_descuento'); ?>
                            </label>
                        </div>
                    <?php } ?>
                </div>
                <div class="col-md-2" <?php if (count($periodicidad) < 2){ ?>style="display: none"<?php } ?>>
                    <div class="form-group">
                        <label  for="periodicidad"><?php echo lang('planpago_periodo');?></label>
                        <select class="form-control input-sm" id="periodocidad" name="periodocidad">
                            <?php foreach ($periodicidad as $key=>$row) { ?>
                                <option value="<?php echo $row['codigo'] ?>" <?php if ($row['codigo'] == $plan->periodo){ ?> selected="true" <?php } ?>>
                                    <?php echo $row['valor']." ".$row['traducido']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row" name="div_ver_fechas_vigencias"
                         <?php if ($plan->fechainicio == '' && $plan->fechavigencia == ''){ ?>style="display: none;"<?php } ?>>
                        <div class="form-group col-md-6">
                            <label><?php echo lang('fecha_inicio'); ?></label>
                            <div class="input-group ">
                                <input type="text" class="form-control  fecha" id="fecha-inicio" <?php if (($plan->getCodigo() <> -1 && $plan->fechainicio < date("Y-m-d")) || (!$puedeModificar)){ ?> disabled="true" <?php } ?>
                                       value="<?php echo $plan->fechainicio == "" ? "" : formatearFecha_pais($plan->fechainicio) ?>"
                                       name="fecha-inicio" placeholder="<?php echo lang('fecha_inicio'); ?>">
                                <span class="input-group-addon">
                                    <i class="icon-calendar bigger-110"></i>
                                </span>
                            </div>
                        </div>
                        <div class="form-group col-md-6 col-xs-12">
                            <label><?php echo lang('fecha_vigencia');?></label>
                            <div class="input-group">
                                <input type="text" class="form-control fecha"  id="fecha-fin"  value="<?php echo $plan->fechavigencia == "" ? "" : formatearFecha_pais($plan->fechavigencia) ?>"  name="fecha-fin" placeholder="<?php echo lang("fecha_fin") ?>"
                                    <?php if (!$puedeModificar){ ?> disabled="true" <?php } ?>>
                                <span class="input-group-addon">
                                    <i class="icon-calendar bigger-110"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row" name="div_ver_vigencia_desde_hoy"
                         <?php if ($plan->fechainicio <> '' || $plan->fechavigencia <> ''){ ?>style="display: none;"<?php } ?>>
                        <div class="col-md-12">
                            <label>&nbsp;</label><br>
                            <span class="blue" style="font-size: 16px;"><?php echo lang("vigente_desde_hoy") ?></span>
                            <span class="btn btn-link" style="height: 34px;" onclick="mostrarFechasVigencia();">(<?php echo lang("cambiar") ?>)</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-4 col-xs-12">
                    <label><?php echo lang('cursos') ?></label>
                    <div>
                        <select class="form-control" id="cursos" name="cursos" data-placeholder="<?php echo lang('seleccione_curso');?>" <?php if ($plan->getCodigo() > 0){ ?>disabled="true"<?php } ?>  >
                            <option value=""></option>
                            <?php foreach ($cursos as $curso) { ?>
                                <option value="<?php echo $curso["cod_plan_academico"] ?>"
                                        <?php if (in_array($curso['cod_plan_academico'], $arrCursosAsignados)){ ?>selected="true"<?php }?>>
                                    <?php echo $curso["nombre"] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <label><?php echo lang('cursado'); ?></label>
                    <table id="table_periodos" class="table table-striped table-bordered table-condensed">
                        <tbody id="tbody_periodos_modalidades">
                        <?php if (count($arrPeriodosPlan) > 0 ){
                            foreach ($arrPeriodosPlan as $key => $periodoPlan){ ?>
                                <tr>
                                    <?php if ($muestraPeriodo){ ?>
                                        <td>
                                            <label>
                                                <input type="checkbox" class="ace checkperiodo" name="periodos" checked="" value="<?php echo $periodoPlan['cod_tipo_periodo'] ?>" disabled="true">
                                                <span class="lbl"></span>
                                            </label>
                                        </td>
                                    <?php } ?>
                                    <td>
                                        <?php echo $periodoPlan['nombre_periodo']." [".$periodoPlan['nombre_modalidad']."]"; ?>
                                        <?php if (!$muestraPeriodo){ ?>
                                            <input type="checkbox" class="checkperiodo" name="periodos" checked="" value="<?php echo $periodoPlan['cod_tipo_periodo'] ?>" style="display: none;">
                                        <?php } ?>
                                        <input type="hidden" name="periodos_modalidad" id="periodos_modalidad" value="<?php echo $periodoPlan['modalidad'] ?>">
                                    </td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td>(<?php echo lang("seleccionar"); ?>)</td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-2">
                    <label><?php echo lang("precio_lista_matricula") ?></label>
                    <input type="text" class="form-control" id="matricula_precio_lista" name="matricula_precio_lista"
                           value="<?php echo isset($arrConceptosPrecios[5]) ? str_replace(".", $separadorDecimal, $arrConceptosPrecios[5]) : "0" ?>" placeholder="<?php echo lang("precio_lista_matricula") ?>"
                           onkeypress="return ingresarFloat(this, event, '<?php echo $separadorDecimal; ?>');" onchange="precioListaMatriculasChange();"
                        <?php if (!$puedeModificar){ ?> readonly="true" <?php } ?>>
                </div>
                <div class="col-md-2">
                    <label><?php echo lang("precio_lista_curso") ?></label>
                    <input type="text" class="form-control" id="curso_precio_lista" name="curso_precio_lista"
                           value="<?php echo isset($arrConceptosPrecios[1]) ? str_replace(".", $separadorDecimal, $arrConceptosPrecios[1]) : 0; ?>" placeholder="<?php echo lang("precio_lista_curso") ?>"
                           onkeypress="return ingresarFloat(this, event, '<?php echo $separadorDecimal; ?>');" onchange="precioListaCursoChange();"
                        <?php if (!$puedeModificar){ ?> readonly="true" <?php } ?>>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h6 class="smaller lighter blue"><?php echo lang('detalles')?></h6>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-condensed" id="tablaFinanciaciones">
                        <thead>
                        <tr>
                            <th class="col-md-1"><?php echo lang('concepto'); ?></th>
                            <th class="col-md-1"><?php echo lang('cuotas')?></th>
                            <th class="col-md-1"><?php echo lang('valor');?></th>
                            <th class="col-md-1"><?php echo lang('descuento');?></th>
                            <th class="col-md-1"><?php echo lang('fecha_limite_matriculacion'); ?></th>
                            <th class="col-md-1"><?php echo lang("fecha_fin_vigencia") ?></th>
                            <th class="col-md-1">&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?php if ($puedeModificar){ ?>
                        <a href="#" id="agregar-mas"><?php echo lang('agregar');?> +</a>
                    <?php } ?>
                </div>
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-success" id="btn-guardar" <?php if (!$puedeModificar){ ?> disabled="true" <?php } ?>><?php echo lang('guardar');?></button>
    </div>
</div>
<input type="hidden" name="hd_anio" value="<?php echo date("Y") ?>">
<input type="hidden" name="hd_dia" value="<?php echo date("j") ?>">
<input type="hidden" name="hd_mes" value="<?php echo date("n") ?>">
<div style="display: none">
    <select name="template_financiacion" style="display: none;">
        <?php foreach ($arrFinanciaciones as $financiacion){ ?>
            <option value="<?php echo $financiacion['codigo'] ?>">
                <?php echo $financiacion['numero_cuotas']; ?>
            </option>
        <?php } ?>
    </select>
</div>
<input type="hidden" name="puede_modificar" value="<?php echo $puedeModificar ? "1" : "0" ?>">

