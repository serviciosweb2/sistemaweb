<script>
    var diasHabilitados = <?php echo $diasDeshabiltados?>;
    var menorHorario = '<?php echo $horarios_filial['menor_horario']?>';
    var mayorHorario = '<?php echo $horarios_filial['mayor_horario']?>';
    var idioma = '<? echo get_idioma()?>';
</script>
<link rel="stylesheet" href="<?php echo base_url('assents/css/horarios/general_horarios.css') ?>"/>
<script src="<?php echo base_url('assents/js/librerias/bootstrap-modal/bootstrap-modalmanager.js')?>"></script>
<script src="<?php echo base_url('assents/js/librerias/bootstrap-modal/bootstrap-modal.js')?>"></script>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal.css')?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal-bs3patch.css')?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/js/librerias/fullcalendar/fullcalendar.css') ?>" />
<link href='<?php echo base_url('assents/js/librerias/fullcalendar/fullcalendar.print.css') ?>' rel='stylesheet' media="print"/>
<?php $this->load->helper('alumnos'); ?>
<script src="<?php echo base_url('assents/js/librerias/jquery-print/jQuery.print.js')?>"></script>

<style>
    #menuDerecha label:hover{
        background: transparent !important;
    }
    .dropdown-toggle:hover{
        background: transparent !important; 
    }
    .sidebar-collapse2 {
        border-bottom: 1px solid #e0e0e0;
        background-color: #f3f3f3;
        text-align: center;
        padding: 3px 0;
        position: relative;
    }

    .sidebar-collapse2:before {
        content: "";
        display: inline-block;
        height: 0;
        border-top: 1px solid #e0e0e0;
        position: absolute;
        left: 15px;
        right: 15px;
        top: 13px;
    }
    
    .sidebar-collapse2>[class*="icon-"] {
        display: inline-block;
        cursor: pointer;
        font-size: 14px;
        color: #aaa;
        border: 1px solid #bbb;
        padding: 0 5px;
        line-height: 18px;
        border-radius: 16px;
        background-color: #fff;
        position: relative;
    }

    #menuDerecha{    
        border-left: 1px dotted #e5e5e5;    
    }
    
    .fc-event-title{
        font-size: 9px !important;
    }
    
    #scrollInterno{
        overflow-x: hidden !important;
        overflow-y: hidden !important;
    }
    
    .border-bottom{
        border-bottom: 0px !important;
    }

    .borde {
        text-align: center;
        color: white;
        letter-spacing: 0;
        text-shadow: -1px -1px 1px #333, 1px -1px 1px #333, -1px 1px 1px #333, 1px 1px 1px #333;
    }

    .triangulo{
        font-size: 50px;
        transform: rotate(135deg);
        text-shadow: 0.01em 0.05em white;
    }

    menuColapsado{
       height: 500px !important;
    }

    menuSinColapsar{
        width: 43px !important;
    }

    .fc-event-time{
        display: none !important;
    }

    .triangulo-equilatero-bottom-right {
         border-left: 10px solid transparent;
         border-top: 10px solid transparent;
         border-right: 10px solid #f0ad4e;
         border-bottom: 10px solid #f0ad4e;
    }
    
    .chosen-container-multi .chosen-choices {
        max-height: 100px;
        overflow: auto;
    }
    
    .salones_referencia{
        width: 10px !important;
        height: 10px !important;
    }
    
    #referencias{
        width: 780px !important;
    }
    
</style>

<script src="<?php echo  base_url() ?>assents/js/chosen.jquery.js"></script>
<div id="repeticion" class="modal     " tabindex="-1" data-focus-on="input:first"  data-width="50%">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3><?php echo lang('repeticion_eventos');?></h3>
    </div>
    <div class="modal-body" >
       <?php echo lang('eventos_horarios');?>
    </div>
    <div class="modal-footer">
        <div class="row">
            <div class="col-md-6">
                <button type="button" class="pregunta btn btn-primary" onclick="guardar_repeticion(this);" name="soloEste" data-toggle="button"><?php echo lang('modificar_solo_este');?></button>
            </div>
            <div class="col-md-6">                
                <button type="button" class="pregunta btn btn-inverse" onclick="guardar_repeticion(this);" name="esteEnAdelante"  data-toggle="button"><?php echo lang('modificar_eventos_serie');?></button>
            </div>
        </div>
    </div>
</div>

<div class="notifications bottom-right"></div>
<div id="area_impresion" class="">
    <div id="areaTablas" class="col-sm-9">            
        <div id='calendar' class=""></div>        
    </div>
    <div class="col-sm-3 no_imprimir" id="menuDerecha">        
        <div    name="calendario-asistencia" class="no-margin no-padding no_imprimir ocultar" ></div>
        <div class="widget-box transparent ocultar">
            <h4>
                <label>
                    <input name="seleccionar_todos" checked id="selec_todos" onclick="seleccionarTodos(this);" class="ace ace-checkbox-2 no_imprimir" type="checkbox">
                    <span class="lbl no_imprimir"><?php echo lang('ver_todos_los_salones');?></span>
                </label>
            </h4>
            <div class="widget-body">
                <div class="widget-main no-padding">
                    <div id="external-events no_imprimir">                            
                        <?php foreach ($salones as $salon) { ?>
                        <div class="external-event no_imprimir class_label_salon_checked_<?php echo $salon['codigo'] ?> "  data-value="<?php echo $salon['codigo']?>" data-toggle="buttons" data-class="label-green" style="position: relative;">
                            <label class="btn class_label_salon_checked_<?php echo $salon['codigo'] ?>" name="label_salon_<?php echo $salon['codigo'] ?>" style="height: 30px; max-width: 250px; width: 88%;  padding-bottom: 20px; padding-top: 2px;">
                                <input type="checkbox" value="<?php echo $salon["codigo"] ?>" class="codSalon" checked="true">
                             <span style="font-size: small;" class="borde pull-left"> <?php echo $salon["salon"] ?></span>
                            </label>                                                              
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <a class="no_imprimir ocultar" href="javascript:void(0)" onclick="mostrarFiltros();">
            <h4><?php echo lang('filtros_horarios');?>
                <i class="icon-filter"></i></h4>
        </a>
        <div class="widget-box transparent ocultar filtro_horario no_imprimir">
            <div class="widget-header">
                <h4><?php echo lang('comisiones');?></h4>
            </div>
            <div class="widget-body">
                <div class="widget-main  ">
                    <select name="comisiones" multiple class="chosen-select width-80 tag-input-style"
                        data-placeholder="<?php echo lang('mostrando_todo');?>"></select>
                </div>
            </div>
        </div>      
        <div class="widget-box transparent ocultar filtro_horario no_imprimir">
            <div class="widget-header">
                <h4><?php echo lang('materias');?></h4>               
            </div>
            <div class="widget-body">
                <div class="widget-main ">
                    <select name="materias" multiple class="chosen-select width-80 tag-input-style"
                        data-placeholder="<?php echo lang('mostrando_todo');?>"></select>
                </div>
            </div>
        </div>
        <div class="widget-box transparent ocultar filtro_horario no_imprimir">
            <div class="widget-header">
                <h4><?php echo lang('profesores')?></h4>               
            </div>
            <div class="widget-body">
                <div class="widget-main ">
                    <select name="profesores" multiple class="chosen-select width-80 tag-input-style"
                        data-placeholder="<?php echo lang('mostrando_todo');?>"></select>
                </div>
            </div>
        </div>
        <div class="widget-box transparent ocultar no_imprimir">
            <div class="widget-header"></div>
            <div class="widget-body">                
                <div class="widget-main ">
                    <button class="btn btn-primary btn-feriados" id="btn-feriados"><?php echo lang('feriados');?></button> 
                </div>                
            </div>
        </div>
        <div class="widget-box transparent no_imprimir">
            <div class="widget-header"></div>
            <div class="widget-body no-border-bottom no_imprimir">
                <div class="sidebar-collapse2 no_imprimir" id="sidebar-collapse" onclick="collapsarMenu(this)">
                    <i class="icon-double-angle-right" data-icon1="icon-double-angle-right" data-icon2="icon-double-angle-right"></i>
                </div>                
            </div>
        </div>
    </div>
    <br>
    <table id="referencias" style="display: none">
        <tbody>
            <tr>
            <?php $i=0;
            foreach($salones as $valor){
                if($i % 4 == 0){ ?>            
                <td class="no_imprimir salon_<?php echo $valor['codigo']; ?>">
                    <div class="salones_referencia" style="background: <?php echo $valor['color'] ?>"></div>
                </td>
                <td class="no_imprimir salon_<?php echo $valor['codigo']; ?>">
                    <label style="font-size: x-small"><?php echo $valor['salon']; ?></label>
                </td>            
                <?php } else { ?>
                <td class="no_imprimir salon_<?php echo $valor['codigo'] ?>">
                    <div class="salones_referencia" style="background: <?php $valor['color'] ?>"></div>
                </td>
                <td class="no_imprimir salon_<?php echo $valor['codigo'] ?>">
                    <label style="font-size: x-small"><?php $valor['salon'] ?></label>
                </td>
                <?php }
                $i++;
            } ?>
            </tr>
        </tbody>
    </table>
</div>

<input type="hidden" value="<?php echo date("Y-").str_pad((date("m") - 1), 2, "0", STR_PAD_LEFT)."-01"; ?>" name="calendar_fechaInicio">
<input type="hidden" value="<?php echo date("Y-").str_pad((date("m") + 1), 2, "0", STR_PAD_LEFT)."-01"; ?>" name="calendar_fechaFin">

<style>
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

<script src="<?php echo base_url('assents/js/librerias/moment/moment.min.js'); ?>"></script>
<script src="<?php echo base_url("assents/theme/assets/js/date-time/bootstrap-timepicker.min.js"); ?>"></script>
<script src="<?php echo base_url('assents/js/librerias/fullcalendar/fullcalendar.min.js'); ?>"></script>
<script src="<?php echo base_url('assents/js/librerias/fullcalendar/gcal.js'); ?>"></script>
<script src="<?php echo base_url('assents/js/librerias/fullcalendar/lang/all.js'); ?>"></script>
<script src="<?php echo base_url('assents/js/horarios/horarios.js') ?>"></script>