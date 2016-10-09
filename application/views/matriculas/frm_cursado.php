<script src="<?php echo base_url('assents/js/matriculas/frm_cursando.js') ?>"></script>
<script src="<?php echo base_url('assents/js/impresiones.js') ?>"></script>
<style>

    .porcAsistenaciaTd{
        text-align: center;
    }

    input[type="checkbox"].ace, input[type="radio"].ace{
        z-index: 9999 !important;
    }
    
    i{
        cursor: pointer
    }
    
    .chosen-results{
        max-height: 100px !important;
    }
    
</style>
<div id="stack1" class="modal     " tabindex="-1" data-focus-on="input:first" data-width="30%">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3><?php echo lang('cambiar-estado'); ?></h3>
    </div>
    <div class="modal-body cambio-body">
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-success btn-cambio-estado"><?php echo lang('guardar'); ?></button>
    </div>
</div>

<div class="modal-content">
    <div class="modal-header">
        <h3 class="blue bigger"><?php echo lang('cursando'); ?>
            <small>
                <i class="icon-double-angle-right"></i>
                <?php echo $nombreAlumno ?>
            </small>
        </h3>
    </div>

    <div class="modal-body overflow-visible">
        <form id="cursado">
            <div class="row">
                <div class="tabbable">
                    <ul class="nav nav-tabs" id="myTab">
                        <?php $i = 0;
                        foreach ($periodos as $key => $periodo) { ?>
                        <li <?php echo $i == 0 ? "class='active'" : "" ?>>
                            <a data-toggle="tab" href="#tab<?php echo $key ?>">
                            <?php echo $periodo['estado_mat_per'] == 'habilitada' 
                                    ? $periodo['nombre_periodo'] 
                                    : $periodo['nombre_periodo'] . ' ' . strtoupper(lang($periodo['estado_mat_per'])) ?>
                            </a>
                         </li>
                        <?php $i++;
                        } ?>
                    </ul>
                    <div class="tab-content">
                        <?php $i = 0;
                        foreach ($periodos as $key => $periodo) { ?>
                        <div id="tab<?php echo $key ?>" class="tab-pane <?php echo $i == 0 ? 'active' : '' ?>">
                            <table class="table table-striped table-bordered table-hover table-responsive" >
                                <li class=" btn btn-xs pull-right blue">
                                    <a href="#nuevoPedido" class=" no-padding">
                                        <i class="icon-print " style="cursor: pointer" onclick="imprimirEstadoAcademico(<?php echo $periodo['materias'][0]['codmatricula']?>);"></i>
                                    </a>
                                </li>
                                <tr>
                                    <th><?php echo lang('materias'); ?></th>
                                    <th><?php echo lang('estado'); ?></th>
                                    <th><?php echo lang('asistencias'); ?></th>
                                    <th><?php echo lang('comision'); ?></th>
                                </tr>
                                <?php foreach ($periodo["materias"] as $materia) {
                                    $disabled = $periodo['estado_mat_per'] != 'habilitada' ? 'disabled' : '';
                                    $tr = $disabled == 'disabled' ? 'danger' : '' ?>
                                <tr class="<?php echo $tr ?>">
                                    <td><?php echo $materia["nombre_" . get_idioma()]; ?>
                                        <input name="cod_estado_academico[]" type="hidden" value="<?php echo isset($materia["codestadoacademico"]) ? $materia["codestadoacademico"] : ""; ?>" />
                                    </td>
                                    <?php switch ($materia["estado"]) {
                                        case "aprobado":
                                        case "recursa":
                                            echo "<td>" . lang($materia["estado"]) . "</td>";
                                            break;

                                        default:
                                            $cambiarestado = $disabled == 'disabled' ? '' : ' cambiarestado';

                                            echo "<td id='".$materia["codestadoacademico"]."'>" . lang($materia["estado"]) . " <i class='icon-cambiar-estado " . $cambiarestado . "' data=" . $materia["codestadoacademico"] . "></i></td>";
                                            break;
                                    } ?>
                                    <td class="porcAsistenaciaTd">
                                        <?php echo $materia["porcasistencia"] == "" ? "-" : $materia["porcasistencia"]; ?>
                                    </td>
                                    <td>
                                    <?php switch ($materia["estado"]) {
                                        case "no_curso" : ?>
                                            <div>
                                                <select class="form-control" id="comision_destino" name="comision_destino[]" <?php echo $disabled ?> data-placeholder="<?php echo lang('seleccionar') ?>" style="width: 120px;">
                                                    <option value="-1"></option>
                                                    <?php foreach ($materia["comisiones_destino"] as $comision) {
                                                        $cupo = $comision["habilita"] == '0'? "disabled='disabled'" : "";
                                                        echo "<option value='" . $comision["codigo"] . "'" . $cupo . ">" . $comision["nombre"] . "</option>";
                                                    } ?>
                                                </select>
                                                <?php if ($disabled == 'disabled'){ ?> 
                                                <input type="hidden" id="comision_destino" name="comision_destino[]" value="-1">
                                                <?php } ?>
                                            </div>
                                            <?php if (count($periodo['materias']) > 1){ ?>
                                            <div style="color: #428bca; cursor: pointer" onclick="replicarATodas(this);">
                                                <span class="icon-repeat" style="color: #428bca; margin-left:5px;">&nbsp;</span>  
                                                <span style="font-size: 12px;"><?php echo lang('replicar_a_todas'); ?></span>
                                            </div>
                                            <?php } 
                                            break;

                                        case "cursando" : ?>
                                            <div>
                                                <select class="form-control" id="comision_destino" name="comision_destino[]" data-placeholder="<?php echo lang('seleccionar') ?>" style="width: 120px;">
                                                    <option value="-1">
                                                        <?php echo isset($materia["comision"]) ? $materia["comision"]->nombre : ""; ?>
                                                    </option>
                                                    <?php foreach ($materia["comisiones_destino"] as $comision){
                                                        // Fix cambiar de comision alumno si esta cursando
                                                         $cupo = $comision["habilita"] == '0'? "" : "";
                                                        echo "<option value='" . $comision["codigo"] . "'" . $cupo . ">" . $comision["nombre"] .  "</option>";
                                                    } ?>
                                                </select>
                                                <?php if (isset($materia["comision"])){ ?> 
                                                <input type="hidden" id="comsion_destino_codigo" value="<?php echo $materia['comision']->getCodigo(); ?>">
                                                <?php } ?>
                                            </div>
                                            <?php if (count($periodo["materias"]) > 1){ ?>
                                            <div style="color: #428bca; cursor: pointer;" onclick="replicarATodas(this);">
                                                <span class="icon-repeat" style="margin-left:5px;">&nbsp;</span>
                                                <span style="font-size: 12px;"><?php echo lang("replicar_a_todas"); ?></span>
                                            </div>
                                            <?php } 
                                            break;

                                        default: ?>
                                            <div><?php echo $materia["descripcion"]; ?></div>
                                            <input type="hidden" name="comision_destino[]" value="-1">
                                            <?php 
                                            break;
                                        } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            </table>
                        </div>
                        <?php $i++; } ?>
                    </div>
                </div>
            </div>
            <input type='hidden' value='<?php echo $cod_plan_academico ?>' name='cod_plan_academico'>
            <input type='hidden' value='<?php echo $cod_alumno ?>' name='cod_alumno'>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-primary boton-primario pull-left" id="btn-imprimir" onclick="imprimirEstadoAcademico();">
        <i class="icon-print" style="cursor: pointer" ></i>
        <?php echo lang('imprimir');?>
    </button>
    <button type="button" class="btn  btn-success" id="btn-guardar">
        <?php echo lang('guardar'); ?>
        <i class="icon-arrow-right icon-on-right bigger-110"></i>
    </button>
</div>