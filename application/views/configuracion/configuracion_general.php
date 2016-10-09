<link rel="stylesheet" href="<?php echo base_url('assents/css/configuracion/configuracion_general.css')?>"/>
<script src="<?php echo base_url('assents/theme/assets/js/jquery.validate.min.js'); ?>"></script>
<script src="<?php echo base_url('assents/js/configuracion/configuracion_general.js'); ?>"></script>

<link rel="stylesheet" href="<?php echo base_url('assents/css/jquery.timepicker.css'); ?>"/>
<script src="<?php echo base_url('assents/js/jquery.timepicker.js'); ?>"></script>
<script src="<?php echo base_url('assents/js/horarios/frm_horario.js'); ?>"></script>

<script>
    function autorizar()
    {

        if($("#habilitar_cloud_print").is(':checked'))
        {
         
            var popup = window.open('googleLogin',"Link","toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=yes,resizable=0,width=430,height=600,left=50,top=50");
        
        }
        else
        {
          
            $.get('googleLogOut').done(function() 
            {
                location.reload();
            })  
        }
        
    }
</script>

<div class="col-md-12 col-xs-12">
        <div id="areaTablas" class="">
           
        <div class="tabbable">
            
             <?php
             $data['tab_activo']='configplan';
             $this->load->view('configuracion/vista_tabs',$data);
            ?>
            
            
                           
            <div class="tab-content">
                <div id="printers" class="tab-pane in active">
                    <div id="div_cloud_print_configuration">
                        <h4><?php echo lang('Configuracion_Scripts_de_Impresion'); ?>&nbsp;&nbsp;&nbsp;
                        </h4>
                        <div id="printers_scripts_configuration">
                            <table>
                                <tr>
                                    <td>
                                        <table>
                                        <?php foreach ($scripts as $script){ ?>
                                            <tr style="height: 46px;">
                                                <td style="padding-right: 20px; width: 158px;">
                                                    <?php echo lang($script['script']); ?>                                                    
                                                </td>
                                                <td style="padding-right: 20px;">
                                                    <select name="impresoras_scripts_forma_impresion" id="impresoras_scripts_forma_impresion_<?php echo $script['id'] ?>"
                                                            class="chosen-select" style="width: 220px;" onchange="scriptsFormasChange(<?php echo $script['id'] ?>)">
                                                        <option value="imprimir" <?php if ($script['metodo'] == "imprimir"){ ?> selected="true" <?php } ?>>
                                                           <?php echo lang('imprimir_siempre');?>
                                                        </option>
                                                        <option value="preguntar" <?php if ($script['metodo'] == "preguntar"){ ?> selected="true" <?php } ?>>
                                                            <?php echo lang('preguntar_siempre');?>
                                                        </option>
                                                        <option value="no_imprimir" <?php if ($script['metodo'] == "no_imprimir"){ ?> selected="true" <?php } ?>>
                                                            <?php echo lang('no_imprimir_nunca');?>
                                                        </option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="impresoras_scripts" id="impresora_script_<?php echo $script['id'] ?>" 
                                                            class="chosen-select" style="width: 220px;" onchange="selectPrinterScriptChange(<?php echo $myGoogleAccount->getIdFilial(); ?>);">
                                                        <option value="-1"><?php echo lang('imprimir_por_navegador');?></option>
                                                    </select>
                                                    <input type="hidden" name="impresora_script_<?php echo $script['id'] ?>" value="<?php echo $script['id'] ?>">
                                                    <input type='hidden' id='printers_scripts_<?php echo $script['id'] ?>' value='<?php echo $script['printer_id'] ?>'>
                                                    <input type='hidden' value='<?php echo $script['id'] ?>'  name='printers_scripts_selected'>
                                                </td>
                                                <td>
                                                    <i class="icon-cog bigger-160" style="margin-left: 10px; cursor: pointer" onclick="configuracionExtraImpresion(<?php echo $script['id'] ?>,'<?php echo $script['script'] ?>');"></i>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <table style="width: 100%;">
                                            <tr>
                                                <td>
                                                    <div class="help-block" id="msg_error2"></div>
                                                </td>
                                                <td style="width: 60px;">
                                                    <button id="btnSaveAdvanced" class="btn btn-success btn-save" onclick="saveAdvancedSetting(<?php echo $myGoogleAccount->getIdFilial(); ?>);" data-last="Finish"><i class="icon-ok"></i><?php echo lang('guardar');?></button>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <hr>
                     
                    <div class="checkbox">
                        <label>
                       
                            <input class="ace ace-checkbox-2" type="checkbox" name="habilitar_cloud_print" id="habilitar_cloud_print" onclick="autorizar();" <?php if($checkcloudprint) echo "checked"?>>
                            <span class="lbl" style="font-size: 18px;"><?php echo lang('Habilitar_Impresion_Cloud_Print'); ?></span>
                        </label>
                    </div>                        
                     
                    <input type="hidden" id="listarCloudPrint" value="<?php echo $myGoogleAccount->isEnabled() ? 1 : 0 ?>">
                    <input type="hidden" id="googleAccountFilial" value="<?php echo $myGoogleAccount->getIdFilial() ?>">
                    <div id="div_cloud_print_devices" style="display: none" >
                        <hr>
                        <h4><?php echo lang('Mis_Dispositivos'); ?></h4>
                        <div id="cloud_printer_list"></div>
                    </div>                        
                    <div name="div_configuracion_pie_pagina_hoja_membretada">
                        <div class="row">
                            <div class="col-md-12">
                                <hr>
                                <h4><?php echo lang("configuracion_de_impresion"); ?></h4>
                                <label>
                                    <input id="agregar_pie_en_hojas_membretadas" class="ace ace-checkbox-2" type="checkbox" 
                                           <?php if ($pieHojaMembretada == 1){ ?> checked="true" <?php } ?> name="agregar_pie_en_hojas_membretadas">
                                    <span class="lbl"><?php echo lang("agregar_pie_de_pagina_en_hojas_membretadas"); ?></span>                            
                                </label>
                                <br>
                                <label>
                                    <input id="agregar_encabezado_en_informes" class="ace ace-checkbox-2" type="checkbox" 
                                           <?php if ($repetirEncabezadoInformes == 1){ ?> checked="true" <?php } ?> name="agregar_encabezado_en_informes">
                                    <span class="lbl"><?php echo lang("repetir_encabezado_en_los_informes"); ?></span>                            
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4" id="msg_error_config_hojas">&nbsp;</div>
                            <div class="col-md-8">
                                <button id="btnConfiguracionPapel" class="btn btn-success btn-save" onclick="guardarConfiguracionHojaMembretada();" data-last="Finish"><i class="icon-ok"></i><?php echo lang('guardar');?></button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="filial" class="tab-pane">
                    <table>
                        <tr>
                            <td>
                                <h4><?php echo lang("configuracion_horario_atencion") ?></h4>
                                <label>
                                    <input class="ace" type="radio" name="form-field-radio" value="sin_especificar" onclick="verOcultarDobleHorario();"
                                           <?php if (!isset($arrHorariosAtencion['especifica_horario']) || $arrHorariosAtencion['especifica_horario'] == 0){ ?> checked="true" <?php } ?>>
                                    <span class="lbl">&nbsp;&nbsp;&nbsp;<?php echo lang("prefiero_no_especificar_mi_horario_de_atencion") ?></span>
                                </label>
                                <br>
                                <label style="margin-top: 8px;">
                                    <input class="ace" type="radio" name="form-field-radio" value="especifica" onclick="verOcultarDobleHorario();"
                                           <?php if (isset($arrHorariosAtencion['especifica_horario']) && $arrHorariosAtencion['especifica_horario'] == 1){ ?> checked="true" <?php } ?>>
                                    <span class="lbl">&nbsp;&nbsp;&nbsp;<?php echo lang("mi_horario_de_atencion_es") ?>:</span>
                                </label>
                            </td>
                        <tr id="descripcion_horario_atencion"
                            <?php if (!isset($arrHorariosAtencion['especifica_horario']) || $arrHorariosAtencion['especifica_horario'] == 0){ ?> style="display: none;" <?php } ?>>
                            <td>
                                <table style="margin-left: 40px; margin-top: 26px; width: 600px;">
                                    <?php foreach ($arrDias as $dia){ ?>
                                    <input type="hidden" name="dias_de_atencion" value="<?php echo $dia ?>">
                                    <tr style="height: 30px;">
                                        <td style="text-align: right; padding-right: 8px;"><?php echo lang("dia_{$dia}"); ?></td>
                                        <td style="width: 128px;">
                                            <div class=" form-group col-md-3" style="padding: 0px; margin-bottom: 0px; width: 51px;">
                                                <input id="<?php echo $dia ?>_e1" class="form-control ui-timepicker-input" type="text" value="<?php echo isset($arrHorariosAtencion[$dia]['e1']) ? $arrHorariosAtencion[$dia]['e1'] : "" ?>" 
                                                       name="horaFilial_1" autocomplete="off" style="width: 50px;"
                                                       <?php if (isset($arrHorariosAtencion[$dia]['cerrado']) && $arrHorariosAtencion[$dia]['cerrado'] == 1){ ?> disabled="true" <?php } ?>>
                                            </div>
                                            <div class=" form-group col-md-3" style="padding: 5px 3px 0px 3px; width: 10px;">-</div>
                                            <div class=" form-group col-md-3" style="padding: 0;  margin-bottom: 0px;">
                                                <input id="<?php echo $dia ?>_s1" class="form-control ui-timepicker-input" type="text" value="<?php echo isset($arrHorariosAtencion[$dia]['s1']) ? $arrHorariosAtencion[$dia]['s1'] : "" ?>" 
                                                       name="horaFilial_2" autocomplete="off" style="width: 50px;"
                                                       <?php if (isset($arrHorariosAtencion[$dia]['cerrado']) && $arrHorariosAtencion[$dia]['cerrado'] == 1){ ?> disabled="true" <?php } ?>>
                                            </div>                                            
                                        </td>
                                        <td name="horario_cortado" style="width: 128px;
                                            <?php if (!isset($arrHorariosAtencion['doble_horario']) || $arrHorariosAtencion['doble_horario'] == 0){ ?> display: none <?php } ?>">
                                            <div class=" form-group col-md-3" style="padding: 0px; margin-bottom: 0px; width: 51px;">
                                                <input id="<?php echo $dia ?>_e2" class="form-control ui-timepicker-input" type="text" value="<?php echo isset($arrHorariosAtencion[$dia]['e2']) ? $arrHorariosAtencion[$dia]['e2'] : "" ?>" 
                                                       name="horaFilial_3" autocomplete="off" style="width: 50px;"
                                                       <?php if (isset($arrHorariosAtencion[$dia]['cerrado']) && $arrHorariosAtencion[$dia]['cerrado'] == 1){ ?> disabled="true" <?php } ?>>
                                            </div>
                                            <div class=" form-group col-md-3" style="padding: 5px 3px 0px 3px; width: 10px;">-</div>
                                            <div class=" form-group col-md-3" style="padding: 0;  margin-bottom: 0px;">
                                                <input id="<?php echo $dia ?>_s2" class="form-control ui-timepicker-input" type="text" value="<?php echo isset($arrHorariosAtencion[$dia]['s2']) ? $arrHorariosAtencion[$dia]['s2'] : "" ?>" 
                                                       name="horaFilial_4" autocomplete="off" style="width: 50px;"
                                                       <?php if (isset($arrHorariosAtencion[$dia]['cerrado']) && $arrHorariosAtencion[$dia]['cerrado'] == 1){ ?> disabled="true" <?php } ?>>
                                            </div>                                     
                                        </td>
                                        <td style="padding-bottom: 12px;">                                            
                                            <label style="font-size: 12px;">
                                                <input class="ace" type="checkbox" id="cerrado_<?php echo $dia; ?>" value="<?php echo $dia ?>" onclick="diaCerrado(this.value);"
                                                       <?php if (isset($arrHorariosAtencion[$dia]['cerrado']) && $arrHorariosAtencion[$dia]['cerrado'] == 1){ ?> checked="true" <?php } ?>>
                                                <span class="lbl">&nbsp;<?php echo lang("cerrado"); ?></span>
                                            </label>
                                        </td>
                                        <td style="padding-bottom: 12px;">
                                            <?php if ($dia == "lunes"){ ?>
                                            &DoubleDownArrow;<button class="btn btn-link" onclick="repetirHorario();"><?php echo lang("aplicar_a_todo") ?></button>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </table>
                                <label>
                                    <input class="ace" type="checkbox" name="indicar_dos_horarios" onclick="verOcultarDosHorarios();"
                                           <?php if (isset($arrHorariosAtencion['doble_horario']) && $arrHorariosAtencion['doble_horario'] == 1){ ?> checked="true" <?php } ?>>
                                    <span class="lbl">&nbsp;<?php echo lang("indicar_dos_horarios_en_un_solo_dia"); ?></span>
                                </label>
                            </td>                     
                        </tr>
                        <tr>                            
                            <td>
                                <table width="100%">
                                    <tr>
                                        <td>
                                            <div id="msg_error3" class="help-block"></div>
                                        </td>
                                        <td style="width: 60px;">                                    
                                            <button id="btnGuardarHorariosAtencion" class="btn btn-success btn-save" data-last="Finish" onclick="guardarHorarioFilial();"><?php echo lang("guardar") ?></button>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>                        
                    </table>
                </div>
                <div id="usuarios" class="tab-pane">
                    <div class="tables-responsive">
                       
                        <?php $tmpl=array ( 'table_open'=>'
            <table id="tablaUsuarios" cellpadding="0" cellspacing="0"
            border="0" class="table table-striped table-bordered" oncontextmenu="return false"
            onkeydown="return false">'); 
                        
            $this->table->set_template($tmpl); 
//            $arrColumnas = array(); 
//            foreach($columnasTabla as $value) { 
//                $arrColumnas[] = $value["nombre"]; 
//                
//            } 
                $this->table->set_heading('','','','','','','');
                echo $this->table->generate(); ?>
                        
                        
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $(".chosen-select").chosen({
        create_option: true,
        persistent_create_option: true
    });
    
    $('.ui-timepicker-input').timepicker({
        'timeFormat': 'H:i' ,
        'step': 15        
    });
    
</script>