<!DOCTYPE html>
<html lang="en" >
    <head>
        <style>.alert{
            padding: 5px ! important;
}</style>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
     <title><?php echo lang('sistema_IGA')?></title>  
     <!---->
    	<!-- basic styles -->
        
        <link rel="stylesheet" href="<?php echo base_url("assents/theme/assets/css/jquery-ui-1.10.3.full.min.css")?>" />
        <link href="<?php echo base_url("assents/theme/assets/css/bootstrap.min.css")?>" rel="stylesheet" />
		<link rel="stylesheet" href="<?php echo base_url("assents/theme/assets/css/font-awesome.min.css")?>" />

		

		<!-- page specific plugin styles -->

		<!-- fonts -->

		<link rel="stylesheet" href="<?php echo base_url("assents/theme/assets/css/ace-fonts.css")?>" />

		<!-- ace styles -->

		<link rel="stylesheet" href="<?php echo base_url("assents/theme/assets/css/ace.min.css")?>" />
		<link rel="stylesheet" href="<?php echo base_url("assents/theme/assets/css/ace-rtl.min.css")?>" />
		<link rel="stylesheet" href="<?php echo base_url("assents/theme/assets/css/ace-skins.min.css")?>" />
                <link rel="stylesheet" type="text/css" href="<?php echo base_url("assents/css/fancy/jquery.fancybox.css")?>">
		<!-- inline styles related to this page -->

		<!-- ace settings handler -->
                <script>
                    var BASE_URL  = '<?php ECHO base_url()?>';
                    var BASE_IDIOMA  = '<?php ECHO get_idioma()?>';
                </script>
                <script src="<?php echo base_url('assents/js/librerias/jquery/jquery-2.1.0.min.js')?>"></script>
               
		<script src="<?php echo base_url("assents/theme/assets/js/ace-extra.min.js")?>"></script>
                <script src=" https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js"></script>
                <script src="<?php echo base_url("assents/js/librerias/fancy/jquery.fancybox.js")?>"></script>
                <script src="<?php echo base_url("assents/js/chat.js")?>"></script>
                
                <script src="<?php echo base_url('assents/theme/assets/js/jquery-ui-1.10.3.full.min.js')?>"></script>
     
                
                
     
     
     
     
     
     
     
    
     <script type="text/javascript">



 
    
    $(document).ready(function(){
        
        if(localStorage.getItem('tkoff')==null){
            
            $('input[name="trabajaOffline"]').val('');
            
        }else{
            
            $('input[name="trabajaOffline"]').val( localStorage.getItem('tkoff') );
            
        }
        
    });
          
      

      
     </script>
    </head>
    
   

    
    <body class="login-layout">
		<div class="main-container">
			<div class="main-content">
				<div class="row">
					<div class="col-sm-10 col-sm-offset-1">
						<div class="login-container">
							<div class="center">
							      
                                                            <div class="logo-login"><img src="<?php echo base_url("assents/img/logo.png");?>" /></div>
                                                            <h4 class="white">&copy; <?php echo lang('instituto_gastronomico');?> </h4>
							</div>

							<div class="space-6"></div>

							<div class="position-relative">
								<div id="login-box" class="login-box visible widget-box no-border">
									<div class="widget-body">
										<div class="widget-main">
											<h4 class="header blue lighter bigger center">
											
												<?php echo lang('ingrese_datos');?>
											</h4>

											<div class="space-6"></div>

                                                                                        <form id="frmLogin" action="<?php echo base_url('login/validaLogin')?>" method="POST">
												<fieldset>
                                                                                                    
													<label class="block clearfix">
                                                                                                            
														<span class="block input-icon input-icon-right">
															<input type="text" class="form-control" placeholder="<?php echo lang('usuario');?>" name="usuario" />
                                                                                                                       
															<i class="icon-user"></i>
														</span>
                                                                                                            <?php echo form_error('usuario'); ?>
													</label>
                                                                                                        
													<label class="block clearfix">
                                                                                                            
														<span class="block input-icon input-icon-right"> 
															<input type="password" class="form-control" placeholder="<?php echo lang('password');?>" name="pass"/>
															<i class="icon-lock"></i>
														</span>
                                                                                                            <?php echo form_error('pass'); ?>
													</label>

													
                                                                                                            <?php 
                                                                                if(isset($respuesta)){
                                                                                   
                                                                                    echo '<div class="row"><div class="col-md-12 col-xs-12 alert alert-danger">'.$respuesta.'</div></div>';
                                                                                    echo '<div class="space"></div>';
                                                                                }
                                                                                
                                                                                ?>
													<div class="clearfix">
														<label class="inline">
															<input type="checkbox" class="ace" />
															<span class="lbl"> <?php echo lang('recordarme');?></span>
														</label>

														<button type="submit" value="LogIn" class="width-35 pull-right btn btn-sm btn-orange">
															<i class="icon-key"></i>
															<?php echo lang('ingresar');?>
														</button>
                                                                                                             <input type="hidden" class="form-control"  name="trabajaOffline" value=""/>
													</div>

													<div class="space-4"></div>
												</fieldset>
											</form>
                                                                                            <div class="social-or-login center">
												<span class="bigger-110"><?php echo lang('necesitas_ayuda')?></span>
                                                                                            </div>
                                                                                        <div class="social-login center">                                                                                            
                                                                                            <a class="btn btn-danger"  target="_blank" href="<?php echo lang('link_youtube_tutorial') ?>">
                                                                                                <i class="icon-youtube"></i>
                                                                                            </a>

                                                                                            <a class="btn btn-info fancybox.iframe" id="irChat" href="http://iga-la.net/panelcontrol/soporte/chat/client.php?locale=sp&style=iga&url=http://iga-la.net/panelcontrol/soporte/chat/conectar.php&referrer=&filial=999">
                                                                                                <i class="icon-comments"></i>
                                                                                            </a><!--

												<a class="btn btn-danger">
													<i class="icon-google-plus"></i>
												</a>-->
											</div>
										</div><!-- /widget-main -->

										<div class="toolbar clearfix">
											<div>
												<a href="<?php echo base_url('login/frm_recuperarPassword')?>"  class="forgot-password-link">
													<i class="icon-arrow-left"></i>
													<?php echo lang('olvido_password');?>
												</a>
											</div>

											
										</div>
                                                                                
                                                                                
									</div><!-- /widget-body -->
								</div><!-- /login-box -->

								

                                                                       
							</div><!-- /position-relative -->
						</div>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div>
		</div><!-- /.main-container -->

		<!-- basic scripts -->

		<!--[if !IE]> -->

		<script type="text/javascript">
			window.jQuery || document.write("<script src='assets/js/jquery-2.0.3.min.js'>"+"<"+"/script>");
		</script>

		<!-- <![endif]-->

		<!--[if IE]>
<script type="text/javascript">
 window.jQuery || document.write("<script src='assets/js/jquery-1.10.2.min.js'>"+"<"+"/script>");
</script>
<![endif]-->

		<script type="text/javascript">
			if("ontouchend" in document) document.write("<script src='assents/theme/assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
		</script>

		<!-- inline scripts related to this page -->

		<script type="text/javascript">
			function show_box(id) {
			 jQuery('.widget-box.visible').removeClass('visible');
			 jQuery('#'+id).addClass('visible');
			}
		</script>
	</body>
</html>