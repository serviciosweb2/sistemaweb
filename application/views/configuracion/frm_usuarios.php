<link rel="stylesheet" href="<?php echo base_url('assents/css/configuracion/configuracion_general.css')?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/css/dynatree/ui.dynatree.css')?>"/>
<script src="<?php echo base_url('assents/theme/assets/js/jquery.validate.min.js'); ?>"></script>
<script src="<?php echo base_url('assents/js/librerias/dynatree/jquery.cookie.js');?>"></script>
<script src="<?php echo base_url('assents/js/librerias/dynatree/jquery.dynatree.js');?>"></script>
<script src="<?php echo base_url('assents/theme/assets/js/jquery.slimscroll.min.js');?>"></script>
<script src="<?php echo base_url('assents/js/configuracion/frm_usuarios.js'); ?>"></script>

<link rel="stylesheet" href="<?php echo base_url('assents/css/jquery.timepicker.css'); ?>"/>
<script src="<?php echo base_url('assents/js/jquery.timepicker.js'); ?>"></script>

<div class="col-md-12 col-xs-12">
    <div class="tabbable">
        <?php if($vista_tab==1){
            $data['tab_activo']='vistaUsuarios';
            $this->load->view('configuracion/vista_tabs',$data);
        } ?>
        <input type="hidden" name="redireccionar" value="<?php echo $vista_tab?>">
        <div class="tab-content">
            <form id="usuario">
                <input type="hidden" value="<?php echo isset($objUsuario) ? $objUsuario->getCodigo() : -1;?>" name="cod_usuario">
                <div id="usuarios" class="tab-pane in active">
                    <div class="page-content">
                        <div class="page-header">
                            <h1>
                                <?php echo isset($objUsuario) ? lang('modificar').' ' : lang('nuevo_usuario').' ' ?>
                                <small>
                                    <i class="icon-double-angle-right"></i>
                                    <?php echo isset($objUsuario) ?  $objUsuario->nombre.' '.$objUsuario->apellido : '';?>
                                </small>
                            </h1>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div>
                                    <div id="user-profile-1" class="user-profile row">
                                        <div class="col-xs-12 col-sm-3 center">
                                            <div>
                                                <span class="profile-picture">
                                                    <img id="avatar" class="editable img-responsive editable-click editable-empty" alt="Alex's Avatar" src=<?php echo base_url("assents/theme/assets/avatars/profile-pic.jpg")?>></img>
                                                </span>
                                                <div class="space-4"></div>
                                                <div class="width-80 label label-info label-xlg arrowed-in arrowed-in-right">
                                                    <div class="inline position-relative">
                                                        <a href="#" class="user-title-label dropdown-toggle" data-toggle="dropdown">
                                                            <i class="icon-circle light-green middle"></i>
                                                            &nbsp;
                                                            <span class="white"><?php echo isset($objUsuario) ? $objUsuario->nombre.' '.$objUsuario->apellido : ''?></span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="space-6"></div>
                                            <div class="hr hr16 dotted"></div>
                                        </div>
                                        <div class="col-xs-12 col-sm-9">
                                            <div class="profile-user-info profile-user-info-striped">
                                                <div class="profile-info-row">
                                                    <div class="profile-info-name"> <?php echo  lang('nombre')?></div>
                                                    <div class="profile-info-value">
                                                        <input name="nombre" class="form-control input-md" value="<?php echo isset($objUsuario) ? $objUsuario->nombre : ''?>">
                                                    </div>
                                                </div>
                                                <div class="profile-info-row">
                                                    <div class="profile-info-name"> <?php echo  lang('apellido')?></div>
                                                    <div class="profile-info-value">
                                                        <input name="apellido" class="form-control input-md" value="<?php echo isset($objUsuario) ? $objUsuario->apellido :''?>">
                                                    </div>
                                                </div>
                                                <div class="profile-info-row">
                                                    <div class="profile-info-name"> <?php echo  lang('domicilio')?></div>
                                                    <div class="profile-info-value">
                                                        <input name="calle" class="form-control input-md" value="<?php echo isset($objUsuario) ? $objUsuario->calle : '';?>">
                                                    </div>
                                                </div>
                                                <div class="profile-info-row">
                                                    <div class="profile-info-name"><?php echo  lang('calle_numero')?> </div>
                                                    <div class="profile-info-value">
                                                        <input name="numero" class="form-control input-md" value="<?php echo isset($objUsuario) ? $objUsuario->numero : '';?>">
                                                    </div>
                                                </div>
                                                <div class="profile-info-row">
                                                    <div class="profile-info-name"> <?php echo  lang('calle_complemento')?></div>
                                                    <div class="profile-info-value">
                                                        <input name="complemento" class="form-control input-md" value="<?php echo isset($objUsuario) ? $objUsuario->complemento : '';?>">
                                                    </div>
                                                </div>
                                                <div class="profile-info-row">
                                                    <div class="profile-info-name"> <?php echo  lang('email')?></div>
                                                    <div class="profile-info-value">
                                                        <input name="email" class="form-control input-md" value="<?php echo isset($objUsuario) ? $objUsuario->email : '';?>"  <?php echo isset($objUsuario) && $objUsuario->email <> '' ? 'readonly' : '';?>>
                                                    </div>
                                                </div>
                                                <div class="profile-info-row">
                                                    <div class="profile-info-name"> <?php echo  lang('idioma')?></div>
                                                    <div class="profile-info-value">
                                                        <select name="idioma" data-placeholder="<?php echo lang('seleccionar_idioma')?>">
                                                            <option></option>
                                                            <?php foreach($listaIdiomas as $idioma){
                                                                $selected= $idioma['id']==$objUsuario->idioma ? 'selected' : '';
                                                                echo '<option value="'.$idioma['id'].'" '.$selected.'>'.$idioma['nombre'].'</option>';
                                                            } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="profile-info-row">
                                                    <div class="profile-info-name"><?php echo  lang('password')?></div>
                                                    <div class="profile-info-value">
                                                        <input name="pass" type="password" class="form-control input-md" value="<?php echo isset($objUsuario) ? $objUsuario->pass : '';?>">
                                                        <input name="pass_old" type="password" style="display: none;" class="form-control input-md" value="<?php echo isset($objUsuario) ? $objUsuario->pass : '';?>">                                                        
                                                    </div>
                                                </div>
                                                <div class="profile-info-row">
                                                    <div class="profile-info-name"> <?php echo  lang('caja_default')?></div>
                                                    <?php if(count($cajas)>0){ ?>
                                                    <div class="profile-info-value">
                                                        <select name="caja_default" data-placeholder="<?php echo lang('seleccionar_caja_default')?>">
                                                            <option></option>
                                                            <?php foreach($cajas as $rowcaja){
                                                                $selected= $rowcaja['default']=='1' ? 'selected' : '';
                                                                echo '<option value="'.$rowcaja['codigo'].'" '.$selected.'>'.$rowcaja['nombre'].'</option>';
                                                            } ?>
                                                        </select>
                                                    </div>
                                                    <?php } else { ?>
                                                    <div class="profile-info-value">
                                                        <input name="caja_default" class="form-control input-md" value="<?php echo lang('usuario_no_tiene_asignadas_cajas');?>">
                                                    </div>
                                                    <?php  } ?>
                                                </div>
                                            </div>
                                            <div class="space-20"></div>
                                            <div class="widget-box transparent" id="WTREE">
                                                <div class="widget-header widget-header-small">
                                                    <h4 class="blue smaller">
                                                        <?php echo  lang('permisos')?>
                                                    </h4>
                                                    <div class="widget-toolbar action-buttons">
                                                        <a href="#" data-reset='true'>
                                                            <i class="icon-refresh blue"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="widget-body">
                                                    <div class="widget-main padding-8">
                                                        <div id="tree">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="hr hr2 hr-double"></div>
                                            <div class="space-6"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix form-actions">
                            <div class="col-md-offset-3 col-md-9">
                                <button class="btn" type="reset" name="volver">
                                    <i class="icon-reply bigger-110"></i>
                                    <?php echo  lang('volver')?>
                                </button>
                                &nbsp; &nbsp; &nbsp;
                                <button class="btn btn-success" type="submit" name="enviar">
                                    <i class="icon-ok bigger-110"></i>
                                    <?php echo  lang('guardar')?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
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