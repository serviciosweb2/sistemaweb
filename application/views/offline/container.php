<!DOCTYPE html>
<html lang="en">
    <?php $ci = &get_instance();?>
	<head>

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta charset="UTF-8"/>

		<!-- basic styles -->
                <style>
                    .navbar{
                        //background: #B91616 !important;
                        }
                </style>
                <?php 
                    $filial = $this->session->userdata('filial');
  
                 ?>
         <script>        
        
        var BASE_URL = '<?php ECHO base_url()?>';
        var BASE_SEPARADOR='<?php echo $filial['moneda']['separadorDecimal']?>';
        var BASE_DECIMALES='<?php echo $filial['moneda']['decimales']?>';
        var BASE_SIMBOLO='<?php echo $filial['moneda']['simbolo']?>';
        var BASE_SEPARADORMILES='<?php echo $filial['moneda']['separadorMiles']?>';
        var BASE_IDIOMA = '<?php echo get_idioma()?>';
        var BASE_IDIOMA_DATATABLE ="";
        var BASE_OFFLINE = JSON.parse('<?php echo json_encode($filial['offline'])?>');
       /* var BASE_IDIOMA_DATATABLE=<?php echo lang('dataTableLang')?>;*/
       function redirectOnline(){
           
           setTimeout(function(){
               
                window.location.href = BASE_URL;
            
            },4000);
            
        }
        
        function puedeTrabajarOffline()
        {
            if( BASE_OFFLINE.token == localStorage.getItem('tkoff'))
            {
            //$('#indicadorOffline').show();
            //window.location.href = BASE_URL;
            
            return true;
            }
            else
            {
                return false;
            }
        }
        
        
        
        </script>
        
       
        
        <link rel="stylesheet" type="text/css" href="<?php echo base_url("assents/css/vistaPanelGeneral.css")?>">
        <style>
            .navbar
            {
                background: #a069c3 !important;
            }
        </style>
        <script src="<?php echo base_url('assents/js/librerias/jquery/jquery-2.1.0.min.js')?>"></script>
        
        <script src="<?php echo base_url('assents/js/offline/config_sql.js')?>"></script>
    
        <!--<script src="<?php echo base_url('assents/js/offline/cobros/cobros_sql.js')?>"></script>-->
        
        
        <link rel="stylesheet" href="<?php echo base_url('assents/theme/assets/css/jquery.gritter.css')?>"/>
        <script src="<?php echo base_url('assents/js/librerias/fancy/jquery.fancybox.js')?>"></script>
        <link rel="stylesheet" href="<?php echo base_url('assents/css/fancy/jquery.fancybox.css')?>"/>
        <link rel="stylesheet" href="<?php echo base_url("assents/theme/assets/css/jquery-ui-1.10.3.full.min.css")?>"/>
        <script src="<?php echo base_url('assents/theme/assets/js/jquery-ui-1.10.3.full.min.js')?>"></script>
        <script src="<?php echo base_url('assents/theme/assets/js/jquery.gritter.min.js')?>"></script>
        <script src="<?php echo base_url('assents/js/librerias/datatables/jquery.dataTables.1.10.0.js')?>"></script>
        <script src="<?php echo base_url("assents/theme/assets/js/jquery.dataTables.bootstrap.js")?>"></script>
        <script src="<?php echo base_url('assents/theme/assets/js/chosen.jquery.min.js')?>"></script>
        <script src="<?php echo base_url('assents/js/librerias/crypt/jquery.crypt.js')?>"></script>
        <script src="<?php echo base_url('assents/js/librerias/jquery-print/jQuery.print.js')?>"></script>
        <link rel="stylesheet" href="<?php echo base_url('assents/theme/assets/css/chosen.css')?>"/>
       
                
               
  
                
                
		<link href="<?php echo base_url('assents/theme/assets/css/bootstrap.min.css')?>" rel="stylesheet" />
		<link rel="stylesheet" href="<?php echo base_url('assents/theme/assets/css/font-awesome.min.css')?>" />

		<!--[if IE 7]>
		  <link rel="stylesheet" href="assets/css/font-awesome-ie7.min.css" />
		<![endif]-->

		<!-- page specific plugin styles -->

		<!-- fonts -->

		<link rel="stylesheet" href="<?php echo base_url('assents/theme/assets/css/ace-fonts.css')?>" />

		<!-- ace styles -->

		<link rel="stylesheet" href="<?php echo base_url('assents/theme/assets/css/ace.min.css')?>" />
		<link rel="stylesheet" href="<?php echo base_url('assents/theme/assets/css/ace-rtl.min.css')?>" />
		<link rel="stylesheet" href="<?php echo base_url('assents/theme/assets/css/ace-skins.min.css')?>" />

		<!--[if lte IE 8]>
		  <link rel="stylesheet" href="assets/css/ace-ie.min.css" />
		<![endif]-->

		<!-- inline styles related to this page -->

		<!-- ace settings handler -->

		<script src="<?php echo base_url('assents/theme/assets/js/ace-extra.min.js')?>"></script>

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->

		<!--[if lt IE 9]>
		<script src="assets/js/html5shiv.js"></script>
		<script src="assets/js/respond.min.js"></script>
		<![endif]-->
                <script>
                 document.addEventListener('online');
                </script>
	</head>
<!--ononline="conexion.preguntarInline();" onoffline="conexion.preguntarOffline();"-->
	<body ononline="redirectOnline();">
		<div class="navbar navbar-default" id="navbar">
			<script type="text/javascript">
				try{ace.settings.check('navbar' , 'fixed')}catch(e){}
			</script>

			<div class="navbar-container" id="navbar-container">
				<div class="navbar-header pull-left">
					<a href="#" class="navbar-brand">
						<small>
							<i class="icon-leaf"></i>
							IGA Offline
						</small>
					</a><!-- /.brand -->
				</div><!-- /.navbar-header -->

				<div class="navbar-header pull-right" role="navigation">
					<ul class="nav ace-nav">
						<li class="light-purple">
                        <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                            <img class="nav-user-photo" src="<?php echo base_url('assents/theme/assets/avatars/profile-pic.jpg')?>" alt="Photo"/>
                            <!--<span class="user-info">-->
<!--                                <small></small>-->
                                <?php
                                $filial = $ci->session->userdata('filial');
                                ?>
                                <?php  //echo $ci->session->userdata('nombre').'<br>'.$filial['nombre']?>
                            <!--</span>-->
                            <!--<i class="icon-caret-down"></i>-->
                        </a>
<!--                        <ul class="user-menu pull-right dropdown-menu dropdown-yellow dropdown-caret dropdown-closer">
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
                        </ul>-->
                    </li>
					</ul><!-- /.ace-nav -->
				</div><!-- /.navbar-header -->
			</div><!-- /.container -->
		</div>

		<div class="main-container" id="main-container">
			<script type="text/javascript">
				try{ace.settings.check('main-container' , 'fixed')}catch(e){}
			</script>

			<div class="main-container-inner">
				<a class="menu-toggler" id="menu-toggler" href="#">
					<span class="menu-text"></span>
				</a>

				<div class="sidebar" id="sidebar">
					<script type="text/javascript">
						try{ace.settings.check('sidebar' , 'fixed')}catch(e){}
					</script>

					<div class="sidebar-shortcuts" id="sidebar-shortcuts">
						<div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
							<button class="btn btn-success">
								<i class="icon-signal"></i>
							</button>

							<button class="btn btn-info">
								<i class="icon-pencil"></i>
							</button>

							<button class="btn btn-warning">
								<i class="icon-group"></i>
							</button>

							<button class="btn btn-danger">
								<i class="icon-cogs"></i>
							</button>
						</div>

						<div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
							<span class="btn btn-success"></span>

							<span class="btn btn-info"></span>

							<span class="btn btn-warning"></span>

							<span class="btn btn-danger"></span>
						</div>
					</div><!-- #sidebar-shortcuts -->

					<ul class="nav nav-list">
						<?php $ci =&  get_instance();
                                                    $this->load->view('menu');
                                                 ?>
					</ul><!-- /.nav-list -->

					<div class="sidebar-collapse" id="sidebar-collapse">
						<i class="icon-double-angle-left" data-icon1="icon-double-angle-left" data-icon2="icon-double-angle-right"></i>
					</div>

					<script type="text/javascript">
						try{ace.settings.check('sidebar' , 'collapsed')}catch(e){}
					</script>
				</div>

				<div class="main-content">
					<div class="breadcrumbs" id="breadcrumbs">
						<script type="text/javascript">
							try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}
						</script>

						<ul class="breadcrumb">
							
							<!--<li class="active">Blank Page</li>-->
						</ul><!-- .breadcrumb -->

						
					</div>

					<div class="page-content">
						<div class="row">
                                                   
                                                    <?php   $this->load->view($page);?>
							<div class="col-xs-12">
								<!-- PAGE CONTENT BEGINS -->

								<!-- PAGE CONTENT ENDS -->
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.page-content -->
				</div><!-- /.main-content -->

				
			</div><!-- /.main-container-inner -->

			<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
				<i class="icon-double-angle-up icon-only bigger-110"></i>
			</a>
		</div><!-- /.main-container -->

		<!-- basic scripts -->

		<!--[if !IE]> -->

		<script type="text/javascript">
                   
//			window.jQuery || document.write("<script src='assents/theme/assets/js/jquery-2.0.3.min.js'>"+"<"+"/script>");
		</script>

		<!-- <![endif]-->

		<!--[if IE]>
<script type="text/javascript">
 
  window.jQuery || document.write("<script src='assets/js/jquery-1.10.2.min.js'>"+"<"+"/script>");
</script>
<![endif]-->

		<script type="text/javascript">
//			if("ontouchend" in document) document.write("<script src='assents/theme/assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
		</script>
		<script src="<?php echo base_url('assents/theme/assets/js/bootstrap.min.js')?>"></script>
		<script src="<?php echo base_url('assents/theme/assets/js/typeahead-bs2.min.js')?>"></script>

		<!-- page specific plugin scripts -->

		<!-- ace scripts -->

		<script src="<?php echo base_url('assents/theme/assets/js/ace-elements.min.js')?>"></script>
		<script src="<?php echo base_url('assents/theme/assets/js/ace.min.js')?>"></script>

		<!-- inline scripts related to this page -->
	</body>
</html>