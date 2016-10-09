
<script>
    var menuJson = <?php echo $menuJson ?> ;
    var cod_usuario_logiado = <?php echo $this->session->userdata('codigo_usuario') ?> ;
</script>
<link rel="stylesheet" href="<?php echo base_url('assents/css/configuracion/configuracion_general.css')?>"/>
<script src="<?php echo base_url('assents/theme/assets/js/jquery.validate.min.js'); ?>"></script>
<script src="<?php echo base_url('assents/js/configuracion/vistaUsuarios.js'); ?>"></script>

<link rel="stylesheet" href="<?php echo base_url('assents/css/jquery.timepicker.css'); ?>"/>
<script src="<?php echo base_url('assents/js/jquery.timepicker.js'); ?>"></script>



<div class="col-md-12 col-xs-12">
        
        <div class="tabbable">
           <?php
             $data['tab_activo']='vistaUsuarios';
             $this->load->view('configuracion/vista_tabs',$data);
            ?>                
            <div class="tab-content">
                
                <div id="usuarios" class="tab-pane in active">
                  
                      
                        
                        <div class="table-responsive">

                                <?php 
                                    $tmpl=array ( 'table_open'=>'<table id="tablaUsuarios" cellpadding="0" cellspacing="0"
                                    border="0" class="table table-striped table-bordered" oncontextmenu="return false" onkeydown="return false">'); 

                                    $this->table->set_template($tmpl); 

                                    $this->table->set_heading('','','','','','','');
                                    echo $this->table->generate(); 
                                ?>


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