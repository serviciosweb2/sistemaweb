<style>
    .chosen-results{
        max-height: 80px !important;
    }
  
    .tags{
        width: auto !important;
        margin-bottom: 10px !important;
    }   

    <?php foreach ($salones as $salon){ ?>
    .class_label_salon_checked_<?php echo $salon['codigo'] ?>{
        background-color:<?php echo $salon["color"] ?> !important;
         border-color: <?php echo $salon['color']; ?> !important;
    }
    
    .label_salon_disabled_<?php echo $salon['codigo']?>{
        background-color: white !important;
        border-color: white !important;
    }
    
    .borde_salon_<?php echo $salon['codigo']?>{
        border: 1px solid <?php echo $salon['color']?> !important;
    }
    <?php } ?>
</style>
<?php
$escala_notas_examen =  isset($arrayEscalaNotas) ? json_encode($arrayEscalaNotas) : json_encode(array());
$idioma = get_idioma(); ?>
<script>
    var escala_notas = JSON.parse('<?php echo $escala_notas_examen?>');
    var nota_aprueba_parcial = '<?php echo $config_notas_examenes['nota_aprueba_parcial']?>';
    var nota_aprueba_final = '<?php echo $config_notas_examenes['nota_aprueba_final']?>';
    var nota_desde_examen = '<?php echo isset($config_notas_examenes['numero_desde']) ? $config_notas_examenes['numero_desde'] : '' ?>';
    var nota_hasta_examen = '<?php echo isset($config_notas_examenes['numero_desde']) ? $config_notas_examenes['numero_hasta'] : '' ?>';
    var formato_nota = '<?php echo $config_notas_examenes['formato_nota']?>';
</script>

<script src="<?php echo base_url('assents/js/configuracion/vistaAcademicos.js');?>"></script>
<script src="<?php echo base_url('assents/theme/assets/js/bootstrap-tag.min.js');?>"></script>
<?php 
$separadores=array(' ','.',';','-','_','|','-',':',',');
$formatos=array(    
    array(
        'codigo'=>0,
        'nombre'=>lang('nombre_y_apellido'),
    ),
    array(
        'codigo'=>1,
        'nombre'=>lang('apellido_y_nombre')
        )
); ?>
<div class="col-md-12 col-xs-12">
    <div id="areaTablas" class="">
        <div class="tabbable">
            <?php $data['tab_activo'] = 'config_academico';
            $this->load->view('configuracion/vista_tabs',$data); ?>               
            <div class="tab-content">                
                <div id="tabAcademico" class="tab-pane in active">
                    <div class="row">                    
                        <div class="col-md-6 col-xs-12">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 widget-container-span ui-sortable">
                                    <div class="widget-box">
                                        <div class="widget-header header-color-orange">
                                            <h5><?php echo lang('examenes');?></h5>
                                            <div class="widget-toolbar">
                                                <a href="#" data-action="collapse">
                                                    <i class="1 icon-chevron-up bigger-125"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="widget-body">
                                            <div class="widget-main">                                                        
                                                <div class="row">
                                                    <div class="col-sm-7 col-xs-7"><?php echo lang('AlertaExamenesSinNota');?></div>
                                                    <div class="col-sm-3 col-xs-3"><hr></div>
                                                    <div class="col-sm-2 col-xs-2">
                                                        <label>
                                                            <input name="AlertaExamenesSinNota" value="1" class="ace ace-switch ace-switch-6" type="checkbox" onchange="guardarValor(this);" <?php echo $alerta_examen_sin_nota == 1 ? 'checked' : ''?>>
                                                            <span class="lbl"></span>
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-7 col-xs-7"><?php echo lang('horas_cierre_inscripcion');?></div>
                                                    <div class="col-sm-2 col-xs-3"><hr></div>
                                                    <div class="col-sm-3 col-xs-3">
                                                        <select id="horas_cierre_inscripcion" data-placeholder="Horas" onchange="guardarHorasInscripcionesExamen();">
                                                            <option></option>
                                                            <?php
                                                            for($i=0; $i<=300;$i++){
                                                                $selected = '';
                                                                if($i == $horasInscripcionExamen){
                                                                    $selected = 'selected';
                                                                }
                                                                    echo '<option value='.$i.' '.$selected.'>'.$i. 'hs</option>';
                                                                } ?>
                                                        </select>
                                                    </div>
                                                </div>  
                                            </div>                               
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <div class="row">
                                <div class="col-xs-12 col-sm-12 widget-container-span ui-sortable">
                                    <div class="widget-box">
                                        <div class="widget-header header-color-green">
                                            <h5><?php echo lang('estado_academico_matriculas');?></h5>
                                            <div class="widget-toolbar">
                                                <a href="#" data-action="collapse">
                                                    <i class="1 icon-chevron-up bigger-125"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="widget-body">
                                            <div class="widget-main">
                                                <div class="row">
                                                    <div class="col-sm-7 col-xs-7"><?php echo lang('CantMaxExamenesFinal');?></div>
                                                    <div class="col-sm-3 col-xs-3"><hr></div>
                                                    <div class="col-sm-2 col-xs-2">
                                                        <select name="CantMaxExamenesFinal" onchange="guardarValor(this);">
                                                            <?php for ($index = 1; $index <= 100; $index++) {
                                                                $selected = $cant_maxima_examen==$index ? 'selected' : '';
                                                                echo '<option  value="'.$index.'" '.$selected.'>'.$index.'</option>';
                                                            } ?>
                                                        </select>
                                                    </div>
                                                </div>                                                                
                                                <div class="row">
                                                    <div class="col-sm-7 col-xs-7"><?php echo lang('MesesDuracionRegularidad');?></div>
                                                    <div class="col-sm-3 col-xs-3"><hr></div>
                                                    <div class="col-sm-2 col-xs-2">
                                                        <select name="MesesDuracionRegularidad"  onchange="guardarValor(this);">
                                                            <?php for ($index = 1; $index <= 100; $index++) {
                                                                $selected = $meses_duracion_regularidad==$index ? 'selected' : '';
                                                                echo '<option  value="'.$index.'" '.$selected.'>'.$index.'</option>';
                                                            } ?>
                                                        </select>
                                                    </div>
                                                </div>                                                                  
                                                <div class="row">
                                                    <div class="col-sm-7 col-xs-7"><?php echo lang('PorcentajeAsistenciaRegular');?></div>
                                                    <div class="col-sm-3 col-xs-3"><hr></div>
                                                    <div class="col-sm-2 col-xs-2">
                                                        <select name="PorcentajeAsistenciaRegular" onchange="guardarValor(this);">
                                                            <?php for ($index = 1; $index <= 100; $index++) {
                                                                $selected= $porcentaje_asistencia_regular== $index ? 'selected' :'';
                                                                echo '<option  value="'.$index.'" '.$selected.'>'.$index.'</option>';
                                                            } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 widget-container-span ui-sortable">
                                    <div class="widget-box">
                                        <div class="widget-header header-color-blue">
                                            <h5><?php echo lang('matricula');?></h5>
                                            <div class="widget-toolbar">
                                                <a href="#" data-action="collapse">
                                                    <i class="1 icon-chevron-up bigger-125"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="widget-body">
                                            <div class="widget-main">
                                                <div class="row">
                                                    <div class="col-sm-7 col-xs-7"><?php echo lang('todos_periodos_curso');?></div>
                                                    <div class="col-sm-3 col-xs-3"><hr></div>
                                                    <div class="col-sm-2 col-xs-2">
                                                        <label>
                                                            <input name="CursosTodosPeriodos" value="1" class="ace ace-switch ace-switch-6" type="checkbox" onchange="guardarValor(this);"  <?php echo $cursos_periodos == 1 ? 'checked' : ''?>>
                                                            <span class="lbl"></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--WIDGET 2-->
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 widget-container-span ui-sortable">
                                    <div class="widget-box">
                                        <div class="widget-header header-color-pink">
                                            <h5><?php echo lang('NombreFormato');?></h5>
                                            <div class="widget-toolbar">
                                                <a href="#" data-action="collapse">
                                                    <i class="1 icon-chevron-up bigger-125"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="widget-body">
                                            <div class="widget-main">												
                                                <div class="row">
                                                    <div class="col-sm-7 col-xs-7"><?php echo lang('NombreFormato');?></div>
                                                    <div class="col-sm-1 col-xs-1"><hr></div>
                                                    <div class="col-sm-4 col-xs-4">
                                                        <select name="NombreFormato" onchange="guardarValor(this);">
                                                           <?php foreach($formatos as $formato){
                                                                $selected= $nombre_formato== $formato['codigo'] ? 'selected' : ''; 
                                                                echo '<option value='.$formato['codigo'].' '.$selected.' >'.$formato['nombre'].'</option>';
                                                            } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-7 col-xs-7"><?php echo lang('NombreSeparador');?></div>
                                                    <div class="col-sm-1 col-xs-1"><hr></div>
                                                    <div class="col-sm-4 col-xs-4">
                                                        <select class="form-control" name="NombreSeparador" onchange="guardarValor(this);">
                                                            <?php foreach($separadores as $separador){
                                                                $nombre= $separador==' ' ? 'espacio' : $separador;
                                                                $selected= $separador==$nombre_separador ? 'selected' :'';
                                                                echo '<option value="'.$separador.'" '.$selected.'>'.$nombre.'</option>';
                                                            } ?>
                                                        </select>
                                                    </div>
                                                </div>                                                                                        
                                            </div>					
                                        </div>
                                    </div>
                                </div>
                            </div>                            
                            <!--WIDGET 5-->
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 widget-container-span ui-sortable">
                                    <div class="widget-box">
                                        <div class="widget-header header-color-green">
                                            <h5><?php echo lang('comisiones');?></h5>
                                            <div class="widget-toolbar">
                                                <a href="#" data-action="collapse">
                                                    <i class="1 icon-chevron-up bigger-125"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="widget-body">
                                            <div class="widget-main">
                                                <div class="row">
                                                    <div class="col-sm-7 col-xs-7"><?php echo lang('desea_ver_nombre_viejo_comision');?></div>
                                                    <div class="col-sm-3 col-xs-3"><hr></div>
                                                    <div class="col-sm-2 col-xs-2">
                                                        <label>
                                                            <input name="verNombreViejoComision" value="1" class="ace ace-switch ace-switch-6" type="checkbox" onchange="guardarValor(this);" <?php echo $ver_nombre_viejo_comision == 1 ? 'checked' : ''?>>
                                                            <span class="lbl"></span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-7 col-xs-7"><?php echo lang('habilitar_capacidad_comision');?></div>
                                                    <div class="col-sm-3 col-xs-3"><hr></div>
                                                    <div class="col-sm-1 col-xs-1">
                                                        <label>
                                                            <input name="CapacidadComision" value="1" class="ace ace-switch ace-switch-6" type="checkbox" onchange="guardarValor(this);" <?php echo $matriculas_sin_cupo == 1 ? 'checked' : ''?>>
                                                            <span class="lbl"></span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-7 col-xs-7"><?php echo lang('permitir_matricular_en_comisiones_sin_cupo');?></div>
                                                    <div class="col-sm-3 col-xs-3"><hr></div>
                                                    <div class="col-sm-1 col-xs-1">
                                                        <label>
                                                            <input name="comisionesSinCupo" value="1" class="ace ace-switch ace-switch-6" type="checkbox" onchange="guardarValor(this);" <?php echo $comisiones_sin_cupo == 1 ? 'checked' : ''?>>
                                                            <span class="lbl"></span>
                                                        </label>
                                                    </div>
                                                </div>                                                            
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> 
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 widget-container-span ui-sortable">
                                    <div class="widget-box">
                                        <div class="widget-header header-color-green">
                                            <h5><?php echo lang("como_nos_conocio"); ?></h5>
                                            <div class="widget-toolbar">
                                                <a data-action="collapse" href="#">
                                                    <i class="1 icon-chevron-up bigger-125"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="widget-body">
                                            <div class="widget-main" style="overflow-y: scroll; max-height: 300px;">
                                                <?php foreach ($arrComoNosConocio as $como_nos_conocio){ ?>
                                                <div class="row">
                                                    <div class="col-sm-7 col-xs-7">
                                                        <?php echo $como_nos_conocio["descripcion_$idioma"] ?>
                                                    </div>
                                                    <div class="col-sm-3 col-xs-3">
                                                        <hr>
                                                    </div>
                                                    <div class="col-sm-2 col-xs-2">
                                                        <label>
                                                            <input class="ace ace-switch ace-switch-6" type="checkbox" 
                                                              value="<?php echo $como_nos_conocio['codigo'] ?>" onchange="guardar_como_nos_conocio(this);"
                                                              <?php if ($como_nos_conocio['habilitado'] == 1){ ?>checked="true"<?php } ?>>
                                                            <span class="lbl"></span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>                        
                        <div class="col-md-6 col-xs-12">                            
                            <div class="row">                                
                                <div class="col-xs-12 col-sm-12 widget-container-span ui-sortable" id="alertaExamen">
                                    <div class="widget-box">
                                        <div class="widget-header header-color-pink">
                                            <h5><?php echo lang('alerta_examen');?></h5>
                                            <div class="widget-toolbar">
                                                <a href="#" data-action="collapse">
                                                    <i class="1 icon-chevron-up bigger-125"></i>
                                                </a>
                                            </div>
                                            <div class="widget-toolbar no-border">
                                                <button class="btn btn-xs btn-light bigger" name="nuevaAlerta">
                                                    <i class="icon-ok"></i>
                                                    <?php echo  lang('nuevo')?>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="widget-body">
                                            <div class="widget-main">
                                                <div class="row">
                                                    <div class="col-md-12 col-xs-12">
                                                    <div class="table-responsive"></div> 
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 widget-container-span ui-sortable">
                                    <div class="widget-box">
                                        <div class="widget-header header-color-red">
                                            <h5><?php echo lang('configuracion_notas_examen');?></h5>
                                            <div class="widget-toolbar">
                                                <a href="#" data-action="collapse">
                                                    <i class="1 icon-chevron-up bigger-125"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="widget-body">
                                            <div class="widget-main">
                                                <form id="configuracion_notas"> 
                                                    <div class="row">
                                                        <div class="col-sm-7 col-xs-7"><?php echo lang('elija_tipo_nota');?></div>
                                                        <div class="col-sm-1 col-xs-1"><hr></div>
                                                        <div class="col-sm-4 col-xs-4">
                                                            <select name="NombreFormato" onchange="ocultarCapas(this)" data-placeholder="Seleccione tipo">
                                                                <option></option>
                                                                <?php $numerico = '';
                                                                $alfabetico = '';
                                                                if ($config_notas_examenes['formato_nota'] == 'alfabetico'){
                                                                    $numerico = 'style="display:none"';
                                                                } else {
                                                                    $alfabetico = 'style="display:none"';
                                                                }
                                                                foreach ($tipos_notas as $key => $valor){
                                                                    $slc = '';
                                                                    if($key == $config_notas_examenes['formato_nota']){
                                                                        $slc = 'selected';
                                                                    }
                                                                    echo '<option  value="'.$key.'" '.$slc.'>'.$valor.'</option>';
                                                                } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row tipo_numerico" <?php echo $numerico;?>>
                                                        <div class="col-sm-7 col-xs-7"><?php echo lang('numero_nota_desde');?></div>
                                                        <div class="col-sm-1 col-xs-1"><hr></div>
                                                        <div class="col-md-4 col-xs-4">
                                                            <input type="text" class="form-control" onkeyup="calcularRango()" name="numero_desde" value="<?php echo isset($config_notas_examenes['numero_desde']) ?  $config_notas_examenes['numero_desde'] : '';?>">
                                                        </div>
                                                    </div>
                                                    <div class="row tipo_numerico" <?php echo $numerico;?>>
                                                        <div class="col-sm-7 col-xs-7"><?php echo lang('numero_nota_hasta');?></div>
                                                        <div class="col-sm-1 col-xs-1"><hr></div>
                                                        <div class="col-md-4 col-xs-4">
                                                            <input type="text" class="form-control" onkeyup="calcularRango()" name="numero_hasta" value="<?php  echo isset($config_notas_examenes['numero_hasta']) ?  $config_notas_examenes['numero_hasta'] : '';?>">
                                                        </div>
                                                    </div>                                                                                           
                                                    <div class="row tipo_alfabetico" <?php echo $alfabetico;?>>
                                                        <div class="col-sm-7 col-xs-7"><?php echo lang('alta_escala');?><br><?php echo lang('menor_a_mayor');?></div>
                                                        <div class="col-sm-1 col-xs-1"><hr></div>
                                                        <div class="col-md-4 col-xs-4">
                                                            <input type="text" class="form-group"  name="escala_notas" id="form-field-tags"  value="<?php echo isset($config_notas_examenes['escala_nota']) ? $config_notas_examenes['escala_nota'] : '' ;?>" placeholder="Escriba nota ..." />
                                                        </div>
                                                    </div>                                                                                            
                                                    <div class="row">
                                                        <div class="col-sm-7 col-xs-7"><?php echo lang('nota_aprueba_parcial');?></div>
                                                        <div class="col-sm-1 col-xs-1"><hr></div>
                                                        <div class="col-md-4 col-xs-4">
                                                            <select name="nota_aprueba_parcial" data-placeholder="Seleccione Nota">
                                                                <option></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-7 col-xs-7"><?php echo lang('nota_aprueba_final');?></div>
                                                        <div class="col-sm-1 col-xs-1"><hr></div>
                                                        <div class="col-md-4 col-xs-4">
                                                            <select name="nota_aprueba_final" data-placeholder="Seleccione Nota">
                                                                <option></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4 col-md-offset-8">
                                                            <label class="pull-right">
                                                                <button type="button" class="btn btn-success btn-save" name="enviar" onclick="guardarConfiguracionNotas(event)">
                                                                    <i class="icon-ok"></i>
                                                                    <?php echo lang("guardar"); ?>
                                                                </button>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </form>    
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 widget-container-span ui-sortable">
                                    <div class="widget-box">
                                        <div class="widget-header header-color-red">
                                            <h5><?php echo lang("salones"); ?></h5>
                                            <div class="widget-toolbar">
                                                <a data-action="collapse" href="#">
                                                    <i class="1 icon-chevron-up bigger-125"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="widget-body">
                                            <div class="widget-main">
                                                <div class="row" name="div_abm_salones">
                                                    <center>
                                                        <table style="width: 322px;">
                                                            <tr>
                                                                <td>
                                                                    <div id="external-events no_imprimir">                            
                                                                        <?php foreach ($salones as $salon) { ?>
                                                                        <div class="external-event no_imprimir class_label_salon_checked_<?php echo $salon['codigo'] ?> "  data-value="<?php echo $salon['codigo']?>" data-toggle="buttons" data-class="label-green" style="position: relative;">
                                                                            <label class="class_label_salon_checked_<?php echo $salon['codigo'] ?>" name="label_salon_<?php echo $salon['codigo'] ?>" style="height: 30px; max-width: 250px; width: 88%;  padding-bottom: 20px; padding-top: 2px;">
                                                                                <span style="font-size: small;" class="borde pull-left"> <?php echo $salon["salon"] ?></span>
                                                                            </label>
                                                                            <div class="menu-salon" style="float: right;">
                                                                                <button data-toggle="dropdown" data-value="<?php echo $salon['codigo']?>" class="btn btn-xs dropdown-toggle class_label_salon_checked_<?php echo $salon['codigo'] ?>">
                                                                                    <span class="icon-caret-down icon-on-righ" style="color:black;"></span>
                                                                                </button>		
                                                                                <ul class="dropdown-menu dropdown-inverse  pull-right dropdown-menu dropdown-yellow dropdown-caret dropdown-closer" >
                                                                                    <li data-toggle="dropdown">
                                                                                        <a accion="modificar" salon="<?php echo $salon["codigo"] ?>" href="#" onclick="agregar_modificar_salon(<?php echo $salon['codigo'] ?>);">
                                                                                            <?php echo lang('modificar'); ?>
                                                                                        </a>
                                                                                    </li>
                                                                                    <?php if($salon["tienehorarios"] <= 0){ ?>
                                                                                    <li>
                                                                                        <a accion="bajaSalon" salon="<?php echo $salon["codigo"] ?>" href="#" onclick="agregar_modificar_salon(<?php echo $salon['codigo'] ?>);">
                                                                                            <?php echo lang('baja'); ?>
                                                                                        </a>
                                                                                    </li>
                                                                                    <?php } ?>
                                                                                </ul>
                                                                            </div>                                  
                                                                        </div>
                                                                        <?php } ?>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div class="row no_imprimir">
                                                                        <div class="col-md-12">
                                                                            <button class="btn-link" onclick="agregar_modificar_salon(-1);"><?php echo lang("nuevo_salon"); ?></button>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </center>
                                                </div>                                               
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>                  
                    </div>
                </div>        
            </div>            
        </div>
    </div>
</div>