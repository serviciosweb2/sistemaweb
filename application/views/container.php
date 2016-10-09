<!DOCTYPE html>
<?php $ci = &get_instance();
$url = $_SERVER['REQUEST_URI'];
?>

<html lang="en"  <?php echo isset($cachear) ? 'manifest="cache.manifest"' : ''?>>
<head>

<meta http-equiv="no-cache">
<meta http-equiv="Expires" content="-1">
<meta http-equiv="Cache-Control" content="no-cache">
    <link rel="icon" href="<?php echo base_url('assents/img/cloud.ico')?>" type="image/gif" sizes="16x16">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta charset="UTF-8"/>
    <title><?php echo lang($seccion["titulo"]) ?></title>
    <?php
    $filial = $this->session->userdata('filial');
    $claves1 = Array('notificaciones', 'ver_todas_las_notificaciones', '_idioma', 'ayer', 'hoy', 'maniana',
                    'proxima_semana', 'a_las', 'la_semana_pasada', 'ver_mas', 'ver_todas_las_consultas',
                    "ERROR", "BIEN", "mensajes", "fallo_envio_alertas_alumnos"
                );

    $momentLang = getLang($claves1);
    ?>
    <script src="<?php echo base_url('assents/js/librerias/jquery/jquery-2.1.0.min.js')?>"></script>
    <script>
        var BASE_LANG = JSON.parse ( '<?php echo isset($lang) ? $lang : json_encode(array()); ?>' );
        var BASE_MENU_JSON = JSON.parse('<?php echo isset($menuJson) ? $menuJson : json_encode(array()); ?>')
        var BASE_MOMENT_LANG = JSON.parse ( '<?php echo isset($momentLang) ? $momentLang : json_encode(array()) ?>' );
        var BASE_URL = '<?php ECHO base_url()?>';
        var BASE_SEPARADOR='<?php echo $filial['moneda']['separadorDecimal']?>';
        var BASE_DECIMALES='<?php echo $filial['moneda']['decimales']?>';
        var BASE_SIMBOLO='<?php echo $filial['moneda']['simbolo']?>';
        var BASE_SEPARADORMILES='<?php echo $filial['moneda']['separadorMiles']?>';
        var BASE_IDIOMA = '<?php echo get_idioma()?>';
        var BASE_IDIOMA_DATATABLE ="";
        var BASE_OFFLINE = JSON.parse('<?php echo json_encode($filial['offline'])?>');
        var BASE_PAIS = <?php echo $filial['pais'] ?>;
        $(document).ready(function(){
            var myhtml = $('body').html(); // ¿para que esta esto?
        });
    </script>
     <?php
    $onoffline="";
    if($filial['offline']['habilitado']=='1' and $this->config->item('modo_offline')){
        $onoffline="onoffline='redirectOFFLINE();'";
    ?>
    <script src="<?php echo base_url('assents/js/offline/sincronizar.js')?>"></script>
    <?php  } ?>

    <script src="<?php echo base_url("assents/js/ajax_controler.js")?>"></script>
    <script src="<?php echo base_url('assents/js/librerias/bootstrap/bootstrap.min.js')?>"></script>
    <script src="<?php echo base_url("assents/js/general_sistema.js")?>"></script>
    <script src="<?php echo base_url('assents/theme/assets/js/jquery-ui.min.js')?>"></script>
    <script src="<?php echo base_url("assents/js/librerias/fancy/jquery.fancybox.js")?>"></script>
    <script src="<?php echo base_url("assents/theme/assets/js/chosen.jquery.min.js")?>"></script>
    <script src="<?php echo base_url("assents/js/librerias/datatables/jquery.dataTables.1.10.0.js")?>"></script>
    <script src="<?php echo base_url("assents/theme/assets/js/jquery.dataTables.bootstrap.js")?>"></script>
    <script src="<?php echo base_url('assents/theme/assets/js/jquery.gritter.min.js')?>"></script>
    <script src="<?php echo base_url('assents/js/librerias/bootstrap-modal/bootstrap-modalmanager.js') ?>"></script>
    <script src="<?php echo base_url("assents/js/elegir_filial.js")?>"></script>
    <link rel="stylesheet" href="<?php echo base_url("assents/theme/assets/css/chosen.css")?>"/>
    <style>
        #notifySINCRO {
            position: fixed;
            bottom: 0;
        }

        #notifySINCRO {
            background: #0070FF;
            line-height: 2;
            text-align: center;
            color: white;
            font-size: 10px;
            font-family: sans-serif;
            font-weight: bold;
            text-shadow: 0 1px 0 #84BAFF;
            box-shadow: 0 0 15px #00214B
        }

        .footer {
            padding-top: 75px;
            height: 0;
            width: 0;
        }

        .sidebar~.footer .footer-inner {
            left: 190px;
        }

        .footer .footer-inner {
            text-align: center;
            position: absolute;
            z-index: auto;
            left: 0;
            right: 0;
            bottom: 0;
        }

        .footer .footer-inner .footer-content {
            position: absolute;
            left: 12px;
            right: 12px;
            bottom: 4px;
            padding: 8px;
            line-height: 36px;
            border-top: 3px double #E5E5E5;
        }

        .sidebar.menu-min~.footer .footer-inner {
            left: 43px;
        }

        .sidebar~.footer .footer-inner {
            left: 190px;
        }

        #detallesDeErrorAjax.modal.fade.in {
            top: 20% !important;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url("/assents/css/vistaPanelGeneral.css")?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url("assents/css/fancy/jquery.fancybox.css")?>">
    <link rel="stylesheet" href="<?php echo base_url('assents/theme/assets/css/jquery.gritter.css')?>"/>

    <!---->
    <!-- basic styles -->
    <link rel="stylesheet" href="<?php echo base_url("assents/theme/assets/css/jquery-ui-1.10.3.full.min.css")?>"/>
    <!--<link href="<?php echo base_url("assents/theme/assets/css/bootstrap.min.css")?>" rel="stylesheet"/>-->
    <link href="<?php echo base_url("assents/theme/assets/css/uncompressed/bootstrap.css")?>" rel="stylesheet"/>
    <link rel="stylesheet" href="<?php echo base_url("assents/theme/assets/css/font-awesome.min.css")?>"/>

    <!-- page specific plugin styles -->
    <!-- fonts -->
    <link rel="stylesheet" href="<?php echo base_url("assents/theme/assets/css/ace-fonts.css")?>"/>
    <!-- ace styles -->
    <link rel="stylesheet" href="<?php echo base_url("assents/theme/assets/css/uncompressed/ace.css")?>"/>
    <link rel="stylesheet" href="<?php echo base_url("assents/theme/assets/css/ace-rtl.min.css")?>"/>
    <link rel="stylesheet" href="<?php echo base_url("assents/theme/assets/css/ace-skins.min.css")?>"/>
    <link rel="stylesheet" href="<?php echo base_url("assents/theme/assets/css/ace.min.css")?>"/>
    <link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal.css');?>"/>
    <link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal-bs3patch.css');?>"/>
    <!-- inline styles related to this page -->
    <!-- ace settings handler -->

    <script src="<?php echo base_url("assents/theme/assets/js/ace-extra.min.js")?>"></script>
    <script src="<?php echo base_url()?>assents/js/librerias/moment/moment-with-langs.min.js"></script>

</head>
<div class="no-margin no-padding" id="divBlock"><i class="icon-spinner icon-spin icon-2x white"></i></div>
<body <?php echo $onoffline?> >
    <!--DIV MENSAJES DE ERROR AJAX-->
    <div id="detallesDeErrorAjax" class="modal fade" data-width="80%" tabindex="-1" data-backdrop="static" data-keyboard="false">
        <div class="modal-header">
            <h3><?php echo lang("detalle"); ?></h3>
        </div>
        <div class="modal-body">
            <div class="row contenedorDetalle">

            </div>
        </div>
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn"><?php echo lang('cancelar')?></button>
        </div>
    </div>
    <!--DIV AVISOS DE AJAX-->
    <div class="row">
        <!--aviso ajax-->
        <div class=" col-md-2 col-md-offset-5 col-xs-6 col-xs-offset-4  text-center ajaxLoad">
            <span class="">
                <i class="icon-spin icon-spinner orange2 bigger-160"></i>
            </span>
            <span>
                <?php echo lang('accion_en_curso');?>
            </span>
        </div>
            <!--aviso error ajax-->
        <div class=" col-md-2 col-md-offset-5 col-xs-6 col-xs-offset-4  text-center msjAjaxError">
            <div class="row">
                <i class="icon-wrench icon-animated-wrench bigger-125"></i>
                OCURRIO UN ERROR
            </div>
            <div class="row">
                <span class="">
                <a href="javascript:void(0)" onclick="detalleAjaxError();">Detalle</a>
                |<a style="position: absolute;" href="javascript:void(0)" onclick="cerrarMensajeError();">CERRAR</a>
                </span>
            </div>
        </div>
    </div>
    <div class="navbar navbar-default" id="navbar">
        <script type="text/javascript">
            try{
                ace.general_things(jQuery);
            }
            catch(e){}
        </script>
        <div class="navbar-container" id="navbar-container">
            <div class="navbar-header pull-left">
                <a href="<?php echo base_url()?>" class="navbar-brand">
                    <img src="<?php echo base_url("assents/img/logo.png");?>" />
                </a>
            </div>
            <div class="navbar-header pull-right" role="navigation">
                <ul class="nav ace-nav">
                    <li class="online">
                        <a id="irChat" onclick="abrirChat();" style="background-color: #d15b47">
                            <i class="icon-comments smaller-20"></i><label style="cursor: pointer;">&nbsp;<?php echo lang('chat');?></label>
                        </a>
                        <ul class="pull-right dropdown-navbar dropdown-menu dropdown-caret dropdown-close">
                        </ul>
                    </li>
                    <li class="purple">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <i class="icon-bell-alt" id="icon_alerta"></i>
                            <span class="badge badge-important" name="cantidad_alertas_container" id="cantidad_alertas_container"></span>
                        </a>
                        <ul class="pull-right dropdown-navbar navbar-pink dropdown-menu dropdown-caret dropdown-close" name="descripcion_alertas_container" style="width: 288px;">
                        </ul>
                    </li>
                    <?php $arrSecciones = $this->session->userdata('secciones');
                    if ($arrSecciones['consultasweb']['habilitado'] == 1){ ?>
                    <li class="green">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <i id="icon_envelope" class="icon-envelope"></i>
                            <span class="badge badge-success" name="cantidad_consultas_web" id="cantidad_consultas_web"></span>
                        </a>
                        <ul class="pull-right dropdown-navbar dropdown-menu dropdown-caret dropdown-close" name="descripcion_consultas_web">
                        </ul>
                    </li>
                    <?php } ?>
                    <li class="light-blue">
                        <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                            <img class="nav-user-photo" src="<?php echo base_url('assents/theme/assets/avatars/profile-pic.jpg')?>" alt="Photo"/>
                            <span class="user-info">
                                <small></small>
                                <?php
                                $filial = $ci->session->userdata('filial');
                                $filiales = $ci->session->userdata('filiales'); 
                                
                                ?>
                                <?php  echo $ci->session->userdata('nombre').'<br>'.$filial['nombre']?>
                            </span>
                            <i class="icon-caret-down"></i>
                        </a>
                        <ul class="user-menu pull-right dropdown-menu dropdown-yellow dropdown-caret dropdown-closer">
                            
                            <?php $entro = false; foreach ($filiales as $fil) { 
                                if ($fil['cod_filial'] != $filial['codigo']){
                                $entro = true;
                            ?>
                            <li>
                                <a class="elegir" data-filial="<?php echo $fil['cod_filial'] ?>" href="#">
                                    <i class="icon-user"></i>
                                    <?php echo $fil['nombre'] ?>
                                </a>
                            </li>
                            <?php }} if ($entro) { ?> <li class="divider"></li> <?php } ?>
                            <li>
                                <a href="<?php echo base_url("configuracion/frm_usuario")?>">
                                    <i class="icon-user"></i>
                                    <?php echo lang('perfil')?>
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="<?php echo base_url("login/logout")?>">
                                    <i class="icon-off"></i>
                                    <?php echo lang('salir');?>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="main-container " id="main-container">
            <script type="text/javascript">
                try {
                    ace.settings.check('main-container' , 'fixed');
                }
                catch(e){}
            </script>
    <a class="menu-toggler" id="menu-toggler" href="#">
        <span class="menu-text"></span>
    </a>
    <div class="sidebar" id="sidebar">
        <script type="text/javascript">
            try {
                ace.settings.check('sidebar' , 'fixed');
            }
            catch(e){}
        </script>
        <div class="sidebar-shortcuts" id="sidebar-shortcuts">
            <div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">

                <button id='atajo_aspirantes' class="btn btn-warning" onclick="window.location.href='<?php echo base_url('aspirantes')?>'" title="<?php echo lang('aspirantes');?>">
                    <i class="icon-group"></i>
                    </button>
                <button id='atajo_nuevo_alumno' class="btn btn-info" onclick="window.location.href='<?php echo base_url('alumnos/index/true')?>'" title="<?php echo lang('nuevo_alumnos');?>">
                    <i class="icon-academico"></i>
                </button>

                <button id="atajo_facturacion" class="btn btn-success" onclick="window.location.href='<?php echo base_url('facturacion')?>'" title="<?php echo lang('facturacion');?>">
                    <i class="icon-administrativo"></i>
                </button>

                <button id="atajo_configuracion" class="btn btn-danger" onclick="window.location.href='<?php echo base_url('configuracion')?>'" title="<?php echo lang('configuracion');?>">
                    <i class="icon-cog"></i>
                </button>
            </div>
            <div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
                <span class="btn btn-success"></span>
                <span class="btn btn-info"></span>
                <span class="btn btn-warning"></span>
                <span class="btn btn-danger"></span>
            </div>
        </div>
        <ul class="nav nav-list">
            <?php $ci =&  get_instance();
            $this->load->view('menu');
            ?>
        </ul>
        <div class="sidebar-collapse" id="sidebar-collapse">
            <i class="icon-double-angle-left" data-icon1="icon-double-angle-left" data-icon2="icon-double-angle-right"></i>
        </div>
        <script type="text/javascript">
                try{
                    ace.settings.check('sidebar' , 'collapsed');
                }
                catch(e){}
        </script>
    </div>
    <div class="main-content">
        <div class="breadcrumbs" id="breadcrumbs">
            <script type="text/javascript">
                try{
                    ace.settings.check('breadcrumbs' , 'fixed');
                }
                catch(e){}
            </script>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home home-icon"></i>
                    <?php $control = isset($seccion["control"]) ? $seccion["control"] : ''; ?>
                    <a href="#">
                        <?php echo lang($seccion["categoria"]) ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo base_url().$control?>">
                        <?php echo lang($seccion["titulo"]) ?>
                    </a>
                </li>
                <?php if (isset($seccion['subcategoria'])){ ?>
                <li>
                    <a href="#">
                        <?php echo lang($seccion['subcategoria']); ?>
                    </a>
                </li>
                <?php } ?>
            </ul>
        </div>
        <div class="page-content">
            <div class="row">
            <?php $codigo_aspirante['codigo_aspirante'] = isset($cod_aspirante) ? $cod_aspirante : ''; ?>
                 <?php   $this->load->view($page,$codigo_aspirante);?>
            </div>
        </div>
    </div>
    <div class="footer">
        <div class="footer-inner">
            <div class="footer-content center">
                <span class="bigger-120">
                    <span class="blue bolder"><?php echo lang('sistemaiga')?></span>
                    <?php echo lang('copyright')?>
                </span>

                &nbsp; &nbsp;
                <span class="action-buttons">
<!--                    <a class="" target="_blank" href="<?php echo lang('link_youtube_tutorial'); ?>">
                        <i class="icon-youtube red bigger-150"></i>
                    </a>-->
                    <a onclick="abrirChat();">
                        <i class="icon-comments bigger-150" style="cursor: pointer;"></i>
                    </a>
                    <a href="<?php echo base_url("/tickets"); ?>">
                        <i class="icon-cloud bigger-150" style="cursor: pointer;" title="Tickets"></i>
                    </a>
                </span>
            </div>
        </div>
    </div>
    <a href="javascript:void(0)" id="indicadorOffline" class="btn-scroll-up btn btn-sm btn-purple">
        <i class="icon-lock icon-only bigger-110"></i>
        <?php echo lang('trabaja_offline')?>
    </a>
    <a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
        <i class="icon-double-angle-up icon-only bigger-110"></i>
    </a>

    <div id="notifySINCRO" class="col-md-2" style="display:none">
        <?php echo lang('actualizando_offline')?>
        <i class="icon-spinner icon-spin icon-2x white"></i>
    </div>
    <?php
    // rgliksberg idioma en el chat
     $codigo_filial = $filial['codigo'];
  
     if ($this->session->userdata('idioma') == 'es'){
              $idioma = 'sp';
          } else {
              $idioma = 'pt-br';
          }
      ?>
    <script type="text/javascript">
      <?php echo 'var idioma="'.$idioma.'";'; ?>
      <?php echo 'var codigo_filial="'.$codigo_filial.'";'; ?>

        if("ontouchend" in document)
            document.write("<script src='assents/theme/assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");

    </script>
    <script src="<?php echo base_url("assents/theme/assets/js/typeahead-bs2.min.js")?>"></script>
    <script src="<?php echo base_url("assents/theme/assets/js/bootbox.min.js")?>"></script>
    <script src="<?php echo base_url("assents/theme/assets/js/ace-elements.min.js")?>"></script>
    <script src="<?php echo base_url("assents/theme/assets/js/ace.min.js")?>"></script>
    <script src="<?php echo base_url("assents/js/chat.js")?>"></script>
</body>
</html>
