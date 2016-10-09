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
                
	
     
                
                
     
     
     
     
     
     
     
    
     <script type="text/javascript">


 
 
    
    $(document).ready(function(){
          
      
    
    //RESCATO LOS DATOS DEL FORM CUANDO HAY SUBMIT
//                $('body').on('submit','form',function(){
//                    var valores=$('form').serialize();
//                    $.ajax({
//                        url:'<?php echo base_url()?>'+'login/validaLogin',
//                        type:'POST',
//                        cache:false,
//                        dataType:'json',
//                        data:valores,
//                        success:function(respuesta){
//                    if(respuesta.estado=='1'){
//                        
//                       
//                    
//                    window.location.href='<?php echo base_url("dashboard")?>';   
//                    }else{
//                        alert(respuesta.respuesta);
//                    }
//                        }
//                    });
//                 
//                    return false;
//                });
          //----->FIN
        
           //VALIDACION DEL FORMULARIO ANTES DEL SUBMIT
//            $('form').validate({
//               rules:{
//                   usuario:{
//                       required:true,
//                        
//                   },
//                   pass:{
//                     required:true  
//                       
//                   }
//               }
//           });
           });
           //----->FIN
      
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
                                                                                                <?php echo lang('nueva_contraseña');?>
												<?php //echo lang('ingrese_datos');?>
											</h4>

											<div class="space-6"></div>

                                                                                        <form id="frmNuevoPass" action="<?php echo base_url('login/guardarCambioPassword')?>" method="POST">
												<fieldset>
                                                                                                    
													<label class="block clearfix">
                                                                                                            
														<span class="block input-icon input-icon-right">
                                                                                                                        
                                                                                                                        <input type="hidden" class="form-control"  name="hash" value="<?php echo $hash?>"/>
															
                                                                                                                        <input type="password" class="form-control" placeholder="<?php echo lang('ingrese_password');?>" name="password">
															
                                                                                                                        <i class="icon-user"></i>
														</span>
                                                                                                            <?php echo form_error('password'); ?>
													
                                                                                                        </label>
                                                                                                        
													<label class="block clearfix">
                                                                                                            
														<span class="block input-icon input-icon-right"> 
															<input type="password" class="form-control" placeholder="<?php echo lang('cofirmar_password');?>" name="password2"/>
															<i class="icon-lock"></i>
														</span>
                                                                                                            <?php echo form_error('password2'); ?>
													</label>

													
                                                                                                            <?php 
                                                                                                            
                                                                                                            if(isset($respuesta['msgerrors'])){
                                                                                                             
                                                                                                                echo '<div class="row"><div class="col-md-12 col-xs-12 alert alert-danger">'.$respuesta['msgerrors'].'</div></div>';
                                                                                                                echo '<div class="space"></div>';
                                                                                                            }
                                                                                
                                                                                ?>
													<div class="clearfix">
<!--														<label class="inline">
															<input type="checkbox" class="ace" />
															<span class="lbl"> <?php echo lang('recordarme');?></span>
														</label>-->

														<button type="submit" value="LogIn" class="width-35 pull-right btn btn-sm btn-orange">
															<i class="icon-key"></i>
															<?php echo lang('cambiar');?>
														</button>
													</div>

													<div class="space-4"></div>
												</fieldset>
											</form>

	
										</div><!-- /widget-main -->

<!--										<div class="toolbar clearfix">
											<div>
												<a href="<?php echo base_url('login/frm_recuperarPassword')?>"  class="forgot-password-link">
													<i class="icon-arrow-left"></i>
													<?php echo lang('olvido_password');?>
												</a>
											</div>

											
										</div>-->
                                                                                
                                                                                
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
			if("ontouchend" in document) document.write("<script src='assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
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