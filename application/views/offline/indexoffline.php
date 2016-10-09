
<style>
    #moduloSeccionesOffline .header
    {
        border-bottom: 1px dotted #CCC !important;
    }
</style>
<script src="<?php echo base_url('assents/js/offline/generalOffline.js')?>"></script>
<script>
   
    if(!puedeTrabajarOffline())
    {
        $(document).ready(function()
        {
            $('.btnAccionOffline').hide();
        });
         
    }
        
</script>
<div class="col-xs-12">
    <!-- PAGE CONTENT BEGINS -->
    <div class="row" id="moduloMsjSinConexion">
        <div class="error-container">
            <div class="well">
                    <h1 class="grey lighter smaller">
                            <span class="blue bigger-125">
                                    <i class="icon-random"></i>

                            </span>
                            <?php echo lang('error_conexion')?>
                    </h1>

                    <hr>
<!--                    <h3 class="lighter smaller">
                            But we are working
                            <i class="icon-wrench icon-animated-wrench bigger-125"></i>
                            on it!
                    </h3>-->

                    <div class="space"></div>

                    <div>
                            <h4 class="lighter smaller"><?php echo lang('causas')?></h4>

                            <ul class="list-unstyled spaced inline bigger-110 margin-15">
                                    <li>
                                            <i class="icon-hand-right blue"></i>
                                            <?php echo lang('sin_conexion');?>
                                            
                                    </li>

                                    <li>
                                            <i class="icon-hand-right blue"></i>
                                            <?php echo lang('problemas_servidor')?>
                                    </li>
                            </ul>
                    </div>

                    <hr>
                    <div class="space"></div>

                    <div class="center">
                            <a href="javascript:void(0)" class="btn btn-grey btnAccionOffline" onclick="intentarConectar();">
                                    <i class="icon-arrow-left"></i>
                                    <?php echo lang('intentar_conectar')?>
                            </a>

                        <a href="javascript:void(0)" onclick="iniciarSinConexion();" class="btn btn-primary btnAccionOffline">
                                    <i class="icon-dashboard"></i>
                                    <?php echo lang('trabajar_sin_conexion')?>
                            </a>
                    </div>
            </div>
    </div>
    </div>
    <!-- PAGE CONTENT ENDS -->
    
    
    <div class="row hide" id="moduloSeccionesOffline">
        <div class="col-md-12">
             <h3 class="header smaller lighter">
                 <?php echo lang('secciones_offline')?>
             </h3>
        </div>
     
        <div class="col-md-12">
           
            <a href="<?php echo base_url('offline/cobros')?>" class="btn btn-app  btn-light btn-md">
                <i class="icon-administrativo"></i>
                <?php echo lang('cobros')?>
            </a>
        </div>
        
    </div>

    
</div>

