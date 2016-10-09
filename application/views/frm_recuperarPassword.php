<!DOCTYPE html>
<html>
    <head>
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

		<!-- inline styles related to this page -->

		<!-- ace settings handler -->
               
                <script src="<?php echo base_url('assents/js/librerias/jquery/jquery-2.1.0.min.js')?>"></script>
		<script src="<?php echo base_url("assents/theme/assets/js/ace-extra.min.js")?>"></script>
                <script src=" https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js"></script>
                
	
     
    
                
     
     
     
     
     
     
     
    



 
 
    

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
								

                                                            <div id="forgot-box" class="forgot-box widget-box no-border visible" >
									<div class="widget-body">
										<div class="widget-main">
											<h4 class="header red lighter bigger">
												<i class="icon-key"></i>
												<?php echo lang('recuperar_contraseña');?>
											</h4>

											<div class="space-6"></div>
											<p>
												<?php echo lang('ingrese_email_instrucciones');?>
											</p>

                                                                                        <form id="frmRecuperarPass" action="<?php echo base_url('login/recuperarPassword')?>" method="POST">
												<fieldset>
													<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input type="email" class="form-control" placeholder="<?php echo lang('ingrese_email');?>" name="email"/>
															<i class="icon-envelope"></i>
														</span>
                                                                                                            <?php echo form_error('email'); ?>
                                                                                                            <?php
                                                                                                            if(isset($msgerror)){
                                                                                                                echo $msgerror;
                                                                                                            }
                                                                                                            
                                                                                                            ?>
                                                                                                            
													</label>

													<div class="clearfix">
														<button type="submit" class="width-35 pull-right btn btn-sm btn-danger">
															<i class="icon-lightbulb"></i>
															<?php echo lang('enviarme');?>
														</button>
													</div>
												</fieldset>
											</form>
										</div><!-- /widget-main -->

										<div class="toolbar center">
											<a href="<?php echo base_url()?>" class="back-to-login-link">
												<?php echo lang('volver_login');?>
												<i class="icon-arrow-right"></i>
											</a>
										</div>
									</div><!-- /widget-body -->
								</div><!-- /forgot-box -->

								
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
			if("ontouchend" in document) document.write("<script src='assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
		</script>

		<!-- inline scripts related to this page -->

		<script type="text/javascript">
			function show_box(id) {
			// jQuery('.widget-box.visible').removeClass('visible');
			// jQuery('#'+id).addClass('visible');
			}
		</script>
	</body>
</html>

