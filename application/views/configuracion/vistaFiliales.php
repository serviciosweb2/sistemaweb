<link rel="stylesheet" href="<?php echo base_url('assents/css/configuracion/configuracion_general.css')?>"/>
<script>
    
//    var tkoff= JSON.parse('<?php echo json_encode($token)?>');
//    console.log(tkoff);

</script>
<script src="<?php echo base_url('assents/theme/assets/js/jquery.validate.min.js'); ?>"></script>
<script src="<?php echo base_url('assents/js/configuracion/configuracion_general.js'); ?>"></script>

<link rel="stylesheet" href="<?php echo base_url('assents/css/jquery.timepicker.css'); ?>"/>
<script src="<?php echo base_url('assents/js/jquery.timepicker.js'); ?>"></script>
<script src="<?php echo base_url('assents/js/horarios/frm_horario.js'); ?>"></script>

     
<div class="col-md-12">
    <div id="areaTablas" class="">
        <div class="tabbable">
             <?php
             $data['tab_activo']='vistaFiliales';
             $this->load->view('configuracion/vista_tabs',$data);
             ?>  
         
            
            <div class="tab-content">
                <div id="filial" class="tab-pane in active">
                    <div class="row">
                        <div class="col-md-12">
                            
                            <div class="col-md-6 no-padding-left no-padding-right">
                                <div class="col-md-12 col-xs-12 widget-container-span ui-sortable" id="horarios_filial">
                                    <div class="widget-box" style="opacity: 1; z-index: 0;">
                                        <div class="widget-header  header-color-blue">
                                            <h6>
                                                <i class="icon-sort"></i>
                                                <?php echo lang("configuracion_horario_atencion");?>
                                            </h6>
                                            <div class="widget-toolbar">
                                                <a href="#" data-reload="widgetMoras">
                                                    <i class="icon-refresh"></i>
                                                </a>
                                                <a href="#" data-action="collapse">
                                                    <i class="icon-chevron-up"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="widget-body">
                                            <div class="widget-body-inner" style="display: block;">
                                                <div class="widget-main">
                                                    <table>
                        <tr>
                        
                            <td>
                                <label>
                                    <input class="ace" type="radio" name="form-field-radio" value="sin_especificar" onclick="verOcultarDobleHorario();"
                                           <?php if (!isset($arrHorariosAtencion['especifica_horario']) || $arrHorariosAtencion['especifica_horario'] == 0){ ?> checked="true" <?php } ?>>
                                    <span class="lbl">&nbsp;&nbsp;&nbsp;<?php echo lang("prefiero_no_especificar_mi_horario_de_atencion") ?></span>
                                </label>
                                <br>
                                <label style="margin-top: 4px;">
                                    <input class="ace" type="radio" name="form-field-radio" value="especifica" onclick="verOcultarDobleHorario();"
                                           <?php if (isset($arrHorariosAtencion['especifica_horario']) && $arrHorariosAtencion['especifica_horario'] == 1){ ?> checked="true" <?php } ?>>
                                    <span class="lbl">&nbsp;&nbsp;&nbsp;<?php echo lang("mi_horario_de_atencion_es") ?>:</span>
                                </label>
                            </td>
                       
                       
                             <tr id="descripcion_horario_atencion"
                            <?php if (!isset($arrHorariosAtencion['especifica_horario']) || $arrHorariosAtencion['especifica_horario'] == 0){ ?> style="display: none;" <?php } ?>>
                            <td>
                                <table style="">
                                    <?php foreach ($arrDias as $dia){ ?>
                                    <input type="hidden" name="dias_de_atencion" value="<?php echo $dia ?>">
                                    <tr style="height: 30px;">
                                        <td style="text-align: right; padding-right: 5px;"><?php echo lang("dia_{$dia}"); ?></td>
                                        <td style="width: 128px;">
                                            <div class=" form-group col-md-3" style="padding: 0px; margin-bottom: 0px; width: 51px;">
                                                <input id="<?php echo $dia ?>_e1" class="form-control ui-timepicker-input" type="text" value="<?php echo isset($arrHorariosAtencion[$dia]['e1']) ? $arrHorariosAtencion[$dia]['e1'] : "" ?>" 
                                                       name="horaFilial_1" autocomplete="off" style="width: 45px;"
                                                       <?php if (isset($arrHorariosAtencion[$dia]['cerrado']) && $arrHorariosAtencion[$dia]['cerrado'] == 1){ ?> disabled="true" <?php } ?>>
                                            </div>
                                            <div class=" form-group col-md-3" style="padding: 5px 3px 0px 3px; width: 10px;">-</div>
                                            <div class=" form-group col-md-3" style="padding: 0;  margin-bottom: 0px;">
                                                <input id="<?php echo $dia ?>_s1" class="form-control ui-timepicker-input" type="text" value="<?php echo isset($arrHorariosAtencion[$dia]['s1']) ? $arrHorariosAtencion[$dia]['s1'] : "" ?>" 
                                                       name="horaFilial_2" autocomplete="off" style="width: 45px;"
                                                       <?php if (isset($arrHorariosAtencion[$dia]['cerrado']) && $arrHorariosAtencion[$dia]['cerrado'] == 1){ ?> disabled="true" <?php } ?>>
                                            </div>                                            
                                        </td>
                                        <td name="horario_cortado" style="width: 128px;
                                            <?php if (!isset($arrHorariosAtencion['doble_horario']) || $arrHorariosAtencion['doble_horario'] == 0){ ?> display: none <?php } ?>">
                                            <div class=" form-group col-md-3" style="padding: 0px; margin-bottom: 0px; width: 40px;">
                                                <input id="<?php echo $dia ?>_e2" class="form-control ui-timepicker-input" type="text" value="<?php echo isset($arrHorariosAtencion[$dia]['e2']) ? $arrHorariosAtencion[$dia]['e2'] : "" ?>" 
                                                       name="horaFilial_3" autocomplete="off" style="width: 35px;"
                                                       <?php if (isset($arrHorariosAtencion[$dia]['cerrado']) && $arrHorariosAtencion[$dia]['cerrado'] == 1){ ?> disabled="true" <?php } ?>>
                                            </div>
                                            <div class=" form-group col-md-3" style="padding: 5px 3px 0px 3px; width: 10px;">-</div>
                                            <div class=" form-group col-md-3" style="padding: 0;  margin-bottom: 0px;">
                                                <input id="<?php echo $dia ?>_s2" class="form-control ui-timepicker-input" type="text" value="<?php echo isset($arrHorariosAtencion[$dia]['s2']) ? $arrHorariosAtencion[$dia]['s2'] : "" ?>" 
                                                       name="horaFilial_4" autocomplete="off" style="width: 35px;"
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
                                        <td style="padding-bottom: 5px;">
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
                                        <td style="width: 60px;" class="pull-right"> 
                                            
                                                 <button id="btnGuardarHorariosAtencion" class="btn btn-success btn-save " data-last="Finish" onclick="guardarHorarioFilial();"><i class="icon-ok"></i><?php echo lang("guardar") ?></button> 
                                            
                                          
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>   
                       
                           
                        
                                             
                           </tr>
                            </table>  
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                 
                            </div>
                            
                             
                                 
                             <div class="col-md-6 no-padding-left no-padding-right">
                            
                            <div class="col-md-12 widget-container-span ui-sortable">
                             <div class="widget-box" style="opacity: 1; z-index: 0;">
                                        <div class="widget-header  header-color-blue">
                                            <h6><?php echo lang('configuracion_separadores');?></h6>


                                    </div>

                                    <div class="widget-body">
                                            <div class="widget-main padding-6 no-padding-left no-padding-right">
                                                <form id="frmSeparadores">
                                                    
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="col-md-6 col-xs-6 form-group">
                                                            <label><?php echo lang('separador_decimal');?></label>
                                                            <select name="SeparadorDecimal" onchange="validarSeparador(this);">
                                                                
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6 col-xs-6 form-group">
                                                            <label><?php echo lang('separador_miles');?></label>
                                                            <select name="SeparadorMiles" onchange="validarSeparador(this);"></select>
                                                        </div>
                                                        </div>
                                                        
                                                        
                                                    </div>
                                                    
                                                </form>
                                            </div>
                                    </div>
                            </div>
                        </div>
                        
                            <div class="col-sm-12 widget-container-span ui-sortable">
                            <div class="widget-box" style="opacity: 1; z-index: 0;">
                                    <div class="widget-header header-color-blue">
                                            <h6><?php echo lang('preguntar_offline')?></h6>

                                    </div>

                                    <div class="widget-body">
                                       <div class="widget-main padding-6 no-padding-left no-padding-right">
                                                <form id="frmSeparadores">
                                                    
                                                    <div class="row">
                                                        <div class="form-group col-md-12 col-xs-12">
                                                         <?php
                                                            $mensaje='';
                                                            
                                                            if(isset($token))
                                                            {
                                                                if($token['nombreEquipo']!='')
                                                                {
                                                                    $mensaje = lang('equipo_offline').' '.$token['nombreEquipo'];
                                                                }
                                                            }
                                                         ?>   
<!--                                                        <div class="col-md-4 col-xs-4">¿Este equipo puede trabajar offline?</div>-->
                                                        <!--<div class="<?php echo $col_a;?>"><hr></div>-->
                                                        <div class="col-md-2 col-xs-2">
                                                            <label>
                                                                <input name="chkOFFLINE" class="ace ace-switch ace-switch-6" type="checkbox" onchange="habilitarOffline(this);" <?php echo $token['estado'] ==1 ? 'checked' : ''?>>
                                                                <span class="lbl"></span>
                                                            </label>
                                                        </div>
                                                        <div class="col-md-10 col-xs-10 nombreEquipo">
                                                            
                                                            <?php 
                                                            
                                                            $filial = $this->session->userdata('filial');
    
                                                            if(isset($filial['offline']['habilitado']))
                                                            {
                                                               
                                                                if($filial['offline']['habilitado']!=1)
                                                                {
                                                                    echo $mensaje;
                                                                }
                                                                else
                                                                {
                                                                    echo lang('trabaja_offline');
                                                                }
                                                                
                                                            }
                                                            
                                                            
                                                            ?>
                                                        </div>
                                                      

                                                        </div>
                                                        
                                                        
                                                    </div>

                                                    
                                                </form>
                                            </div>
                                    </div>
                            </div>
                        </div>
                            
                            
                            <div class="col-sm-12 widget-container-span ui-sortable">
                                <div class="widget-box" style="opacity: 1; z-index: 0;">
                                    <div class="widget-header header-color-blue">
                                           <h6><?php echo lang('dias_que_cobra_filial');?></h6>
                                    </div>
                                    <div class="widget-body ">
                                        <div class="widget-main">
                                            <form id="frmCobroFilial">
                                                <div class="row">
                                                <div class="col-md-12">
                                                    <?php
                                                        $selected = '';

                                                           foreach($lista_dias_filial as $rowDia){
                                                               $selected = '';
                                                               foreach($cobro_filial_dias['dias_cobro_filial'] as $valor){
                                                                   if($rowDia == $valor){
                                                                       $selected = 'checked';
                                                                   }
                                                               }
                                                               echo '<input name="dia[]" value='.$rowDia.' type="checkbox" class="ace ace-checkbox-2" '.$selected.'><span class="lbl">'.lang($rowDia).'</span>';
                                                           }
                                                            ?>
                                                </div>
                                                </div>
                                                <div class="space"></div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label class="pull-right">
                                                            <button type="button" class="btn btn-success btn-save" onclick="guardarDiasCobroFilial();"><i class="icon-ok"></i><?php echo lang('guardar');?></button>
                                                        </label>
                                                            
                                                        </div>
                                                 </div>
                                                    
                                                </form>
                                          </div>
                                       </div>
                                    </div>   
                               </div>
                        
                        
                    </div>
                            
                       
                    
                            
                            
                        </div>
                    </div>
                    
                    <div class="row">
                         <div class="col-md-12">
                         <div class="col-md-12 widget-container-span ui-sortable" id="widgetReceso">
                                    <div class="widget-box" style="opacity: 1; z-index: 0;">
                                        <div class="widget-header  header-color-blue">
                                            <h6>
                                                <i class="icon-sort"></i>
                                                <?php echo lang('periodos_receso_filial');?>
                                            </h6>
                                            <div class="widget-toolbar">
                                                <a href="#" data-reload="widgetMoras">
                                                    <i class="icon-refresh"></i>
                                                </a>
                                                <a href="#" data-action="collapse">
                                                    <i class="icon-chevron-up"></i>
                                                </a>
                                            </div>
                                            <div class="widget-toolbar no-border">
                                                <button class="btn btn-xs" type="button" name="nuevo_receso" onclick="nuevo_receso(-1);">
                                                    <i class="icon-ok"></i>
                                                    <?php echo lang('nuevo_receso');?>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="widget-body">
                                            <div class="widget-body-inner" style="display: block;">
                                                <div class="widget-main">
                                                    <div class="table-responsive">
                                                        <table>
                                                            <?php
                                                                 
                                                            ?>
                                                        </table>
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

<!--ADVERTENCIA LOGOUT-->
<div style="display:none" id="logOutMSJ">
    
        <div class="modal-content">
            <div class="modal-header">
                    <h4 class="blue bigger"><?php echo lang('ADVERTENCIA')?></h4>
            </div>

            <div class="modal-body overflow-visible">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo lang('advertencia_logout')?>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-sm btn-primary" onclick="logOut();">
                            <i class="icon-ok"></i>
                            <?php echo lang('salir')?>
                    </button>
            </div>
        </div>
</div>

<script>
    $(".chosen-select").chosen({
        create_option: true,
        persistent_create_option: true,
         
    });
    
    $('.ui-timepicker-input').timepicker({
        'timeFormat': 'H:i' ,
        'step': 15        
    });
    
</script>