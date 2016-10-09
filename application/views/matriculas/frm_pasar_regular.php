<script src="<?php echo base_url("assents/js/librerias/datatables/jquery.dataTables.min.js")?>"></script>
<script src="<?php echo base_url('assents/js/librerias/bootstrap-modal/bootstrap-modalmanager.js')?>"></script>
<script src="<?php echo base_url('assents/js/librerias/bootstrap-modal/bootstrap-modal.js')?>"></script>
<script src="<?php echo base_url('assents/js/impresiones.js') ?>"></script>

<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal.css')?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal-bs3patch.css')?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/css/matricula/matricula.css');?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/css/reportes/reportes.css');?>"/>
<script>
    var frmLang = <?php echo $frmLang ?>;
    var todos = <?php if ($todos) { echo "1"; } else { echo "0"; } ?>;
    
</script>
<script src="<?php echo base_url('assents/js/matriculas/frm_pasar_a_regular.js');?>"></script>

<style>
    .label-success {        
        width: 84px !important;        
    }
    
    .padding-left-3{
        padding-left: 3px !Important;
    }
</style>
<div class="col-md-12 col-xs-12">
    <div id="areaTablas">
        <table id="academicoMatriculas_reagularizar_alumnos" width="100%" class="table table-striped table-condensed table-bordered table table-hover" oncontextmenu="return false" onkeydown="return false"> 
            <thead>
                <tr>
                    <th>
                        <label class="inline">
                            <input class="ace" type="checkbox" name="seleccionar_todos" onclick="checkAllEstadoAcademico();" readonly="true">
                            <span class="lbl"></span>
                        </label>
                    </th>
                    <th><?php echo lang("ALUMNO"); ?></th>
                    <th><?php echo lang("curso"); ?></th>
                    <th><?php echo lang("materia"); ?></th>
                    <!--Ticket 4771 - mmori- agrego nueva columna comision-->
                    <th><?php echo lang("comision"); ?></th>
                    <th><?php echo lang("porc_asistencia") ?></th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<div class="table_filter" id="div_table_filters" name="div_table_filters" style="z-index: 1000; width: 372px; top: 126px;">
    <div class="filtro_opciones" style="min-width: 374px; border: none;">
        <div class="row">
            <div class="col-md-12">
                <?php echo lang("curso"); ?>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-12">
                <!--Ticket 4771 -mmori- Modifico style-->
                <select name="filtro_cursos" class="select_chosen" onchange="get_materias();" style="width: 100%;">
                    <option value="-1">(<?php echo strtolower(lang("todos")); ?>)</option>
                    <?php foreach ($arrCursos as $curso){ ?> 
                    <option value="<?php echo $curso['codigo'] ?>">
                        <?php echo $curso['nombre_es'] ?>
                    </option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php echo lang("materia") ?>
            </div>        
        </div>
        <div class="row">
            <div class="form-group col-md-12">
                <!--Ticket 4771 -mmori- Modifico style-->
                <select name="filtro_materias" class="select_chosen" style="width: 100%;">
                    <option value="-1">(<?php echo strtolower(lang("todas")); ?>)</option>
                    <?php foreach ($arrMaterias as $materia){ ?>
                    <option value="<?php echo $materia['codigo'] ?>">
                        <?php echo $materia['nombre_es'] ?>
                    </option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <!--Ticket 4771 -mmori- Agrego filtro comision-->
        <div class="row">
            <div class="col-md-12">
                <?php echo lang("comision") ?>
            </div>        
        </div>
        <div class="row">
            <div class="form-group col-md-12">
                <!--Ticket 4771 -mmori- Modifico style-->
                <select name="filtro_comision" class="select_chosen" style="width: 100%;">
                    <option value="-1">(<?php echo strtolower(lang("todas")); ?>)</option>
                    <?php foreach ($arrComisiones as $comision){ ?>
                    <option value="<?php echo $comision['codigo'] ?>">
                        <?php echo $comision['nombre'] ?>
                    </option>
                    <?php } ?>
                </select>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <?php echo lang("fechaDesde_horario"); ?>
            </div>
            <div class="col-md-6">
                <?php echo lang("fecha_hasta_"); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="input-group" style="width: 100%;">
                    <input name="filtro_fecha_desde" value="" class="date-picker" type="text" readyonly="true" style="width: 138px;">
                    <span class="input-group-addon" style="padding: 3px 6px;">
                        <i class="icon-calendar bigger-110"></i>
                    </span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group" style="width: 100%;">
                    <input name="filtro_fecha_hasta" value="" class="date-picker" type="text" readyonly="true" style="width: 138px;">
                    <span class="input-group-addon" style="padding: 3px 6px;">
                        <i class="icon-calendar bigger-110"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="row" style="margin-top: 12px;">
            <center>
                <div class="col-md-12">
                    <button class="btn btn-primary" onclick="listar();"><?php echo lang("buscar"); ?></button>
                </div>
            </center>
        </div>
    </div>
</div>
<div name="contenedorPrincipal" style="bottom: 0px; height: 100%; left: 0px; position: fixed; width: 100%; z-index: 20; display: none;"></div>