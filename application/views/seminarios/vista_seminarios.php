<link rel="stylesheet" href="<?php echo base_url('assents/css/tel-master/intlTelInput.css')?>"/>
<script src="<?php echo base_url('assents/js/librerias/tel-master/intlTelInput.js')?>"></script>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal.css');?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal-bs3patch.css');?>"/>
<script src="<?php echo base_url('assents/js/generalTelefonos.js')?>"></script>
<script src="<?php echo base_url()?>assents/js/librerias/bootstrap-modal/bootstrap-modal.js"></script>
<script src="<?php echo base_url()?>assents/js/librerias/bootstrap-modal/bootstrap-modalmanager.js"></script>
<script src="<?php echo base_url('assents/theme/assets/js/inputMask/jquery.inputmask.js')?>"></script>

<script src="<?php echo base_url('assents/js/seminarios/seminarios.js');?>"></script>
<script src="<?php echo base_url('assents/js/impresiones.js');?>"></script>

<div class="col-md-12">
    <?php if (count($horarios) > 0){ ?>
    <center>
    <?php echo lang('horarios'); ?>&nbsp;&nbsp;
        <select name="filtro_horario" onchange="buscarInscriptos();">
            <option value="-1">(todos)</option>
            <?php foreach ($horarios as $horario){
                $dia = substr($horario['fecha'], 0, 10);
                $hora = substr($horario['fecha'], 11, 5); ?>
            <option value="<?php echo $horario['id'] ?>">
                <?php echo getFechaTextual($dia, false)." ".$hora." hs."; ?>
            </option>
            <?php } ?>
        </select>
    </center>
</div>

<div class="col-md-12 col-xs-12">
    <div id="areaTablas">
        <?php 
        $tmpl=array ( 
            'table_open'=>'<table id="academicoAlumnos" width="100%" class="table table-striped table-condensed  table-bordered"  oncontextmenu="return false"
            onkeydown="return false">'); $this->table->set_template($tmpl); 
            $this->table->set_heading(array(
                lang('horario'),
                lang('cupo'),
                lang('nombre'),
                lang('telefono'), 
                lang('documento'),
                lang('email'), 
                lang('fecha_inscripcion'))
                    ); 
            echo $this->table->generate();
        ?>
    </div>
    <?php } else { ?>
    <br><br><br>
    <center>
        <span class="orange" style="font-size: 20px;">
            <?php echo lang('no_se_han_definido_seminarios'); ?>
        </span>
    </center>
    <?php } ?>
</div>
