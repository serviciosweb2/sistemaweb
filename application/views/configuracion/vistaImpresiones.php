<link rel="stylesheet" href="<?php echo base_url('assents/css/configuracion/configuracion_general.css')?>"/>
<script src="<?php echo base_url('assents/theme/assets/js/jquery.validate.min.js'); ?>"></script>
<script src="<?php echo base_url('assents/js/configuracion/configuracion_general.js'); ?>"></script>

<link rel="stylesheet" href="<?php echo base_url('assents/css/jquery.timepicker.css'); ?>"/>
<script src="<?php echo base_url('assents/js/jquery.timepicker.js'); ?>"></script>
<script src="<?php echo base_url('assents/js/horarios/frm_horario.js'); ?>"></script>


<div class="col-md-12 col-xs-12">
        <div id="areaTablas" class="">
            
        <div class="tabbable">
            
            
            
            <ul class="nav nav-tabs" id="myTab">                
                <li class="active">
                    <a data-toggle="tab" href="#printers" data-metodo="">
                        <i class="green icon-print bigger-110"></i>
                        <?php echo lang("Impresiones"); ?>
                    </a>
                </li>
                <li>
                    <a data-toggle="tab" href="#filial" data-metodo="">
                        <i class="green icon-cogs bigger-110"></i>
                        <?php echo lang("filial"); ?>
                    </a>
                </li>
                <li>
                    <a data-toggle="tab" href="#usuarios" data-metodo="">
                        <i class="green icon-cogs bigger-110"></i>
                        <?php echo lang("usuarios"); ?>
                    </a>
                </li>
            </ul>                
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
                                                        <option value="-1">Imprimir por el navegador</option>
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
                                                    <button id="btnSaveAdvanced" class="btn btn-success btn-save" onclick="saveAdvancedSetting(<?php echo $myGoogleAccount->getIdFilial(); ?>);" data-last="Finish"><?php echo lang('guardar');?></button>
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
                            <input class="ace ace-checkbox-2" type="checkbox" name="habilitar_cloud_print" id="habilitar_cloud_print"
                                   <?php if ($myGoogleAccount->isEnabled()) { ?> checked="true" <?php } ?>>
                            <span class="lbl" style="font-size: 18px;"><?php echo lang('Habilitar_Impresion_Cloud_Print'); ?></span>
                        </label>
                    </div>                        
                    <table style="width: 554px;">
                        <tr>
                            <td style="width: 372px;">
                                <div id="div_cloud_print" <?php if (!$myGoogleAccount->isEnabled()){ ?> style="display: none" <?php } ?>>
                                    <form class="form-horizontal" id="validation-form" role="form">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="cloud_print_user"><?php echo lang('google_email')?></label>
                                            <div class="col-sm-9">
                                                <input id="cloud_print_user" class="col-xs-10 col-sm-5 valid input-xlarge" style="width: 408px;" value="<?php echo $myGoogleAccount->user ?>" type="email" placeholder="email@email.com">
                                            </div>
                                        </div>
                                        <div class="space-1"></div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="cloud_print_pass"><?php echo lang('password');?></label>
                                            <div class="col-sm-9">
                                                <input id="cloud_print_pass" class="col-xs-10 col-sm-5 valid input-xlarge" style="width: 408px;" value="<?php echo $myGoogleAccount->pass ?>" type="password" placeholder="Password" name="password">
                                            </div>
                                        </div>
                                        <div class="space-1"></div>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table style="width: 100%;">
                                    <tr>
                                        <td>
                                            <div class="help-block" id="msg_error"></div>
                                        </td>
                                        <td style="width: 60px;">
                                            <div class="row-fluid wizard-actions">
                                                <button id="btnSave" class="btn btn-success btn-save" data-last="Finish" onclick="saveGoogleAccount(<?php echo $myGoogleAccount->getIdFilial(); ?>)">
                                                    Guardar
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <input type="hidden" id="listarCloudPrint" value="<?php echo $myGoogleAccount->isEnabled() ? 1 : 0 ?>">
                    <input type="hidden" id="googleAccountFilial" value="<?php echo $myGoogleAccount->getIdFilial() ?>">
                    <div id="div_cloud_print_devices" style="display: none" >
                        <hr>
                        <h4><?php echo lang('Mis_Dispositivos'); ?></h4>
                        <div id="cloud_printer_list"></div>
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
