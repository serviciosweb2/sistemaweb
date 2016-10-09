<script src="<?php echo base_url("assents/js/librerias/datatables/jquery.dataTables.min.js")?>"></script>
<script src="<?php echo base_url('assents/js/librerias/bootstrap-modal/bootstrap-modalmanager.js')?>"></script>
<script src="<?php echo base_url('assents/js/librerias/bootstrap-modal/bootstrap-modal.js')?>"></script>
<script src="<?php echo base_url('assents/js/impresiones.js') ?>"></script>

<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal.css')?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal-bs3patch.css')?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/css/matricula/matricula.css');?>"/>
<script>
    var langFrm = <?php echo $langFrm?>;
    
</script>

<style>
    .label-success {        
        width: 84px !important;        
    }
    
    #menuMover{
/*        background-color: #428bca !important;
        border-width: 5px;
        color: white !important;
        padding: 6px 12px;
        height: 38px;*/
    }
    .padding3{
        padding-left: 3px !Important;
    }
    
</style>

<script src="<?php echo base_url('assents/js/matriculas/frm_pasar_a_libres.js');?>"></script>


<div class="col-md-12 col-xs-12">
    <div id="areaTablas">
        <table id="academicoMatriculas_pasar_a_libres" width="100%" class="table table-striped table-condensed table-bordered table table-hover" oncontextmenu="return false" onkeydown="return false"> 
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
                    <th><?php echo lang("porc_asistencia") ?></th>
                </tr>
            </thead>
        </table>
    </div>
</div>