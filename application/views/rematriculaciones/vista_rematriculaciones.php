<script src="<?php echo base_url("assents/js/librerias/datatables/jquery.dataTables.min.js")?>"></script>
<script src="<?php echo base_url('assents/js/librerias/bootstrap-modal/bootstrap-modalmanager.js')?>"></script>
<script src="<?php echo base_url('assents/js/librerias/bootstrap-modal/bootstrap-modal.js')?>"></script>
<script src="<?php echo base_url('assents/js/impresiones.js') ?>"></script>

<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal.css')?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal-bs3patch.css')?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/css/matricula/matricula.css');?>"/>
<style>
    .disabledRow td{
        color:#F44;
    }

    .greyRow td{
        color:#666;
    }
</style>
<script>
    var menuJson = <?php echo $menuJson?>;
    var columns = <?php echo $columns?>;
    var comisiones = <?php echo json_encode($comisiones); ?>;
    var cursos =  <?php echo json_encode($cursos); ?>;
    var anio = <?php echo $anio;?>;
    var curso = <?php echo ("[".implode($curso, ',')."]");?>;
    var comision = <?php echo ("[".implode($comision, ',')."]");?>;
</script>

<script src="<?php echo base_url('assents/js/rematriculaciones/rematriculaciones.js');?>"></script>

<div class="col-md-12 col-xs-12">
<?php
    $anios = array($anio - 1, $anio, $anio + 1, $anio + 2);
?>


<table>
<tr><td><?php echo lang('año');?>:</td><td><?php echo lang('trimestre');?>:</td></tr>
<tr>
<td valign="top">
<select name='selectAnio' id='selectAnio'>
</select>
</td>
<td valign="top">
<select name='selectTrimestre' id='selectTrimestre'>
    <option value=1 <?php echo ($trimestre == 1)?"selected":""?>>Enero-Marzo</option>
    <option value=2 <?php echo ($trimestre == 2)?"selected":""?>>Abril-Junio</option>
    <option value=3 <?php echo ($trimestre == 3)?"selected":""?>>Julio-Septiembre</option>
    <option value=4 <?php echo ($trimestre == 4)?"selected":""?>>Octubre-Diciembre</option>
</select>
</td>
<tr>
<td><?php echo lang('curso');?></td><td><?php echo lang('comision');?></td>
</tr>
<tr>
<td valign="top">
<select name='selectCurso' id='selectCurso' multiple>
</select>
</td>
<td valign="top">
<select name='selectComision' id='selectComision' multiple>
</select>
</td>
</tr>
</table>
<button id='boton-recargar' class='btn btn-primary boton-primario' onclick='recargar()'><?php echo lang("filtrar"); ?></button>
<div>
<?php 
foreach($cursos as $curso){
?>
    <h3><?php echo $curso['nombre_pt']?></h3>
<?php
    $comisiones = $curso['comisiones'];
    foreach($comisiones as $comision){
?>
    <h4><?php echo $comision['nombre'];?></h4>
    
    <div id="areaTablas-<?php echo $comision['codigo'];?>">
        <?php
        $tmpl=array ('table_open'=>'<table id="tablaRematriculas-' . $comision['codigo'] . '" width="100%" class="table table-striped table-condensed table-bordered table table-hover" oncontextmenu="return false" onkeydown="return false">');
        $this->table->set_template($tmpl);
        $this->table->set_heading(array('','','', '','','','', '', ''));
        echo $this->table->generate();
        ?>
    </div>
    <div>
        <button id='boton-reimprimir-<?php echo $comision['codigo'];?>' 
                class='btn btn-primary boton-primario' 
                onclick='reimprimir("<?php echo $comision['codigo']; ?>")'>
        <?php echo lang("reimprimir_seleccionados"); ?>
        </button>
        <button id='boton-emitir-<?php echo $comision['codigo'];?>' 
                class='btn btn-primary boton-primario' 
                onclick='emitir("<?php echo $comision['codigo']; ?>")'>
        <?php echo lang("emitir_seleccionados"); ?>
        </button>
    </div>

<?php 
    }
}
?>
</div>
<form name="frm_exportar" action="<?php echo base_url()."rematriculaciones/exportar" ?>" target="new_target" method="POST">
    <input type="hidden" value="" name="comision">
    <input type="hidden" value="" name="fechaDesde">
    <input type="hidden" value="" name="fechaHasta">
    <input type="hidden" value="" name="tipo_reporte">    
    <input type="hidden" value="" name="iSortCol_0">
    <input type="hidden" value="" name="sSortDir_0">
    <input type="hidden" value="" name="iDisplayLength">
    <input type="hidden" value="" name="iDisplayStart">
    <input type="hidden" value="" name="sSearch">
    <input type="hidden" value="exportar" name="action">
</form>

