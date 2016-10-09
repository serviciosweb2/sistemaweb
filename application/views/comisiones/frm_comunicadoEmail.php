<?php 
$nombre='nombre_'.get_idioma();
?>

<script src="<?php echo base_url('assents/theme/assets/js/bootstrap-tag.min.js')?>"></script>
<script src="<?php echo base_url('assents/theme/assets/js/jquery.hotkeys.min.js')?>"></script>
<script src="<?php echo base_url('assents/theme/assets/js/bootstrap-wysiwyg.min.js')?>"></script>
<script src="<?php echo base_url('assents/theme/assets/js/jquery-ui-1.10.3.custom.min.js')?>"></script>
<script src="<?php echo base_url('assents/theme/assets/js/jquery.ui.touch-punch.min.js')?>"></script>
<script src="<?php echo base_url('assents/theme/assets/js/jquery.slimscroll.min.js')?>"></script>
<script src="<?php echo base_url('assents/js/librerias/moment/moment-with-langs.min.js')?>"></script>
<script src="<?php echo base_url('assents/js/comisiones/frm_comunicadoEmail.js')?>"></script>
<style>
    
    .message-item .time{
        width: 95px!important;
    }
    
    .hr-18{
        margin: 0px 0px !important;        
    }
    
    .message-form .wysiwyg-editor{
        max-height: 200px !important;
        min-height: 200px !important;
    }
    
    .chosen-results{
        max-height: 80px !important;
    }
    
    .message-navbar .nav-search{
        left: 1% !important;   
    }
    
</style>

<input type="hidden" name="cantAlumnos" value="<?php echo $cantAlumnos[0]['cantInscriptos'];?>">
<div class="modal-content">
    <div class="modal-header">
        <h4 class="blue bigger"><?php echo lang('comunicados_por_email'); ?><small><i class="icon-double-angle-right"></i>  <?php echo ' '.$cod_comision.', '.$nombre_comision?></small></h4>
    </div>
    <div class="modal-body overflow-visible">
        <div class="mensaje"><?php echo lang('no_alumnos_inscriptos'); ?></div>
        <div class="row vista">
            <div class="col-xs-12">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="tabbable">
                                    <ul id="inbox-tabs" class="inbox-tabs nav nav-tabs padding-16 tab-size-bigger tab-space-1">
                                        <li class="li-new-mail pull-right">
                                            <a data-toggle="tab" href="#write" data-target="write" class="btn-new-mail">
                                                <span class="btn bt1n-small btn-purple no-border">
                                                    <i class=" icon-envelope bigger-130"></i>
                                                    <span class="bigger-110"><?php  echo lang('nuevo_comunicado');?></span>
                                                </span>
                                            </a>
                                        </li> 
                                        <li class="active">
                                            <a data-toggle="tab" href="#inbox" >
                                                <i class="blue icon-inbox bigger-130"></i>
                                                <span class="bigger-110"><?php echo lang('enviadas'); ?></span>
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content no-border no-padding">                                                                                                
                                        <div class="tab-pane active" id="inbox">
                                            <div class="message-container">
                                                <div id="id-message-new-navbar" class="hide message-navbar align-center clearfix">
                                                    <div class="message-item-bar">
                                                        <div class="col-md-2">
                                                            <a href="#" class="btn-back-message-list no-hover-underline">
                                                                <i class="icon-arrow-left blue bigger-110 middle"></i>
                                                                <b class="middle bigger-110"><?php echo lang('back'); ?></b>
                                                            </a>
                                                        </div>
                                                        <div class="col-md-2"><?php echo lang('enviar_a'); ?></div>
                                                        <div class="col-md-6">
                                                            <select name="codMateria" data-placeholder="Seleccione materia">
                                                                <option value="" selected><?php echo lang('todas_las_materias'); ?></option>
                                                            <?php foreach($materias as $mta){ ?>
                                                                <option value="<?php echo $mta['codigo'] ?>">
                                                                    <?php echo $mta[$nombre] ?>
                                                                </option>
                                                            <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="detalleList">                                                                                                               
                                                    <div id="id-message-list-navbar" class="message-navbar align-center">
                                                        <div class="message-bar"></div>
                                                        <div class="row">
                                                            <div class="nav-search no-padding" style="width: 100%;">
                                                                <form class="" id="inboxSearch">
                                                                    <input type="hidden" name="cod_comision" value="<?php echo $comision->getCodigo()?>">
                                                                    <div class="row">
                                                                        <div class=" col-md-4 col-xs-4">
                                                                            <input  name="filtrar" value="" class="form-control input-sm" autocomplete="off" class="input-small nav-search-input" placeholder="<?php echo lang('buscar');?>">
                                                                        </div>
                                                                        <div class=" col-md-4 col-xs-4">
                                                                            <select data-placeholder="<?php echo lang('seleccionar_materia')?>" name="cod_materia">
                                                                                <option value=""></option>
                                                                                <?php foreach($materias as $materia){ ?>
                                                                                <option value="<?php echo $materia['codigo'] ?>">
                                                                                    <?php echo $materia[$nombre] ?>
                                                                                </option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>                                                         
                                                    </div>                                                                                                              
                                                    <div class="message-list-container">
                                                        <form id="frminbox">
                                                             <div class="message-list" id="message-list">

                                                             </div>
                                                        </form>
                                                     </div>
                                                    <div class="message-footer clearfix">
                                                        <div class="pull-left" id="TotalMensajes"></div>
                                                        <div class="pull-right">
                                                            <div class="inline middle"></div>
                                                            &nbsp; &nbsp;
                                                            <ul class="pagination middle">
                                                                <li class="firthPage">
                                                                    <span>
                                                                        <i class="icon-step-backward middle"></i>
                                                                    </span>
                                                                </li>
                                                                <li class="prevPage">
                                                                    <span href="#">
                                                                        <i class="icon-caret-left bigger-140 middle"></i>
                                                                    </span>
                                                                </li>
                                                                <li>
                                                                    <span>
                                                                        <input name="numeroPagina" value="1"  type="text" readonly>
                                                                    </span>
                                                                </li>
                                                                <li class="nexPage ">
                                                                    <span href="#" >
                                                                        <i class="icon-caret-right bigger-140 middle"></i>
                                                                    </span>
                                                                </li>
                                                                <li class="lastPage">
                                                                    <span href="#">
                                                                        <i class="icon-step-forward middle"></i>
                                                                    </span>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>                                                                                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>                                                               
                        <form id="id-message-form" class="hide message-form  col-xs-12">
                            <div class="row"></div>
                            <div class="hr hr-18 dotted"></div>
                            <div class="row">                                   
                                <div class="form-group col-md-6">
                                    <label class="" for="form-field-recipient"><?php echo lang('alumnos'); ?>:</label>											
                                    <select name="alumnosComunicados[]" multiple class="chosen-select tag-input-style">
                                    </select>											
                                </div>                                          
                                <div class="form-group col-md-6 ">
                                    <label class="" for="form-field-subject"><?php echo lang('asunto_comunicado'); ?></label>
                                    <div class="input-icon block col-xs-12 no-padding">
                                            <input maxlength="100" type="text" class="col-xs-12" name="asunto" id="form-field-subject" placeholder="<?php echo lang('asunto');?>">
                                            <i class="icon-comment-alt"></i>
                                    </div>
                                </div>                                                                                   
                            </div>
                            <div class="hr hr-18 dotted"></div>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label class="col-sm-3 control-label no-padding-right">
                                        <span class="inline space-24 hidden-480"></span>
                                        <?php echo lang('mensaje_comunicados'); ?>
                                    </label>
                                    <div class="col-sm-9 no-padding">
                                        <div class="wysiwyg-editor"></div>
                                    </div>
                                </div>
                            </div>       
                        </form>                                                                
                        <div class="vistaDetalle hide">
                            <div id="id-message-item-navbar" class=" message-navbar align-center clearfix">
                                <div class="message-bar">
                                    <div class="message-toolbar">
                                        <div class="inline position-relative align-left" >
                                            <ul id="mover" class="dropdown-menu dropdown-lighter dropdown-caret dropdown-125">
                                                <li>
                                                    <a href="abierta">
                                                        <i class="icon-stop pink2"></i>
                                                        &nbsp; <?php echo lang('inbox'); ?>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="cerrado">
                                                        <i class="icon-stop pink2"></i>
                                                        &nbsp; <?php echo lang('cerradas'); ?>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="eliminado">
                                                        <i class="icon-stop blue"></i>
                                                        &nbsp; <?php echo lang('no_concretadas'); ?>
                                                    </a>
                                                </li>					
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class="messagebar-item-left">
                                        <a href="#" class="btn-back-message-list">
                                            <i class="icon-arrow-left blue bigger-110 middle"></i>
                                            <b class="bigger-110 middle"><?php echo lang('back'); ?></b>
                                        </a>
                                    </div>
                                    <div class="messagebar-item-right">
                                        <i class="icon-time bigger-110 orange middle"></i>
                                        <span id="timeCabezera1" class="time grey">Today, 7:15 pm</span>
                                    </div>
                                </div>
                            </div>
                            <div class=" message-content" id="id-message-content">
                                <div class="message-header clearfix">
                                    <div class="pull-left">
                                        <span id="asuntoMensaje" class="blue bigger-125"><?php echo lang('click_abrir_mensaje');?></span>
                                        <div class="space-4"></div>
                                            <a href="#" id="nombreSender" class="sender">John Doe</a>
                                            &nbsp;
                                            <i class="icon-time bigger-110 orange middle"></i>
                                            <span id="timeCabezera2" class="time"></span><br>
                                            <span id="destinatarios"></span>
                                    </div>
                                    <div class="action-buttons pull-right"></div>                                            
                                </div>
                                <div class="hr hr-double"></div>
                                <div class="message-body"></div>
                                <div class="hr hr-double"></div>
                            </div>
                            <div class=" message-footer message-footer-style2 clearfix">
                                <div class="pull-left"></div>
                            </div>
                        </div>                                            
                    </div>
                </div>
            </div>    
        </div>   
    </div>
    <div class="modal-footer">
        <button class="btn btn-sm btn-primary" name="submit" disabled>
            <i class="icon-ok"></i>
           <?php echo lang('enviar'); ?>
        </button>
    </div>	
</div>
							
								