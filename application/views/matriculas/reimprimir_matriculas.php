<script src="<?php echo base_url('assents/js/impresiones.js') ?>"></script>
<script src="<?php echo base_url('assents/js/matriculas/reimprimir_matriculas.js') ?>"></script>
<div class="modal-content" id="reimprimir_matriculas">
    <div class="modal-header">
        <button class="close" data-dismiss="modal" type="button">×</button>
        <h4 class="blue bigger"><?php echo lang("reimprimir_matricula") ?></h4>
    </div>
    
    <div class="modal-body overflow-visible">
        <form id="frm-factura" class="form">
            <div class="row">
                <div class="col-md-12">
                    <h3><?php echo lang("guardarmat_alumno"); ?>: <?php echo $myAlumno->apellido.", ".$myAlumno->nombre ?></h3>
                </div>
            </div>
            <?php foreach ($matriculas_periodos as $matricula_periodo){ ?>
            <div class="row">
                <div class="col-md-12">
                    <label>
                        <input class="ace" type="radio" name="codigo_matricula" value="<?php echo $matricula_periodo['cod_matricula'] ?>">
                        <span class="lbl"><?php echo $matricula_periodo['fecha_emision']."(" . $matricula_periodo['nombre'] .")" ?></span>
                    </label>
                </div>
            </div>
            <?php } ?>            
        </form>
    </div>    
    <div class="modal-footer">
        <button class="btn btn-sm btn-primary" name="btn_reimrpimir">
            <i class="icon-ok"></i>
            <?php echo lang("imprimir"); ?>
        </button>
    </div>    
</div>