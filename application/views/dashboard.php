<script >
            
             (function(){
       
        $.ajax({
            url: BASE_URL+"offline/ping",
            type: "POST",
            data: "",
            dataType:"JSON",
            cache:false,
            async: false,
            error:function(){
                window.location.href= BASE_URL+'offline';
            },
            success:function(respuesta){
                if(respuesta.codigo==1){
                    console.log('ping:',respuesta.codigo);
                }
            }
        });
        
    })();
</script>
<script src="<?php echo base_url('assents/js/librerias/moment/moment-with-langs.min.js')?>"></script>
<script src="<?php echo base_url('assents/theme/assets/js/jquery.slimscroll.min.js')?>"></script>
<script>
    var columns = JSON.parse('<?php echo $getColumns ?>');
    var tienePermisoConsultasWeb = '<?php echo $tienePermiso_consultasWeb?>';
</script>
<script src="<?php echo base_url('assents/js/dashboard/dashboard.js')?>"></script>

<style>
    
/*    #Wsugerencias .table-responsive{
        
        max-height: 300px !important;
        overflow: auto !important;
    }*/
    
    
/*    #lista{
        
        max-height: 300px !important;
        overflow: auto !important;
        
    }*/
    
    
/*    #Wconsultasweb hr{
     
        margin-top: 0px !important;
        margin-bottom: 5px !important;
        
    }*/
    
    #Wtareas .form-actions{
        
        margin-bottom: 0px !important;
        
    }
    
    .dataTables_wrapper .row:first-child{
        
        padding-top: 0px !important; 
        padding-bottom: 0px !important; 
        
        
    }
    
    
    .dataTables_wrapper .row:first-child+.dataTable {
 border-top: 0px solid #DDD !important; 
 border-bottom: 0px solid #DDD !important;
}

.fotoMSJ{
    padding-top: 10.8% !important;
}

.textoMSJ{
    
    color: #c8c8c8;
    font-size: 20pt ;
}
    
</style>

       
<!-- PAGE CONTENT BEGINS --> 

                    <div class="col-xs-12 col-md-12">
                    <div class="row">

                            <div class="col-xs-12">
                                <div class="widget-box " id="Wcomunicados">
                                        <div class="widget-header">
                                                <h4 class="lighter smaller">
                                                        <i class="icon-comment blue"></i>
                                                        <?php echo lang('comunicados');?>
                                                </h4>
                                        </div>

                                        <div class="widget-body">
                                                <div class="widget-main">

                                                </div><!-- /widget-main -->
                                        </div><!-- /widget-body -->
                                </div><!-- /widget-box -->
                            </div>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="row">
                        <div class="col-xs-12">
                                <div class="widget-box" id="Wconsultasweb">
                                        <div class="widget-header">
                                                <h4 class="lighter smaller">
                                                        <i class="icon-comment blue"></i>
                                                        <?php echo lang('consultas_web');?>
                                                </h4>
                                        </div>

                                        <div class="widget-body">
                                                <div class="widget-main ">
                                      
                                                </div>
                                            
                                                
                                        </div>
<!--
                                                     
                                                </div><!-- /widget-main -->
                                       
                                </div><!-- /widget-box -->
                        </div>
                    </div>
                    
                    <div class="row">

                        <div class="col-xs-12">
                                <div class="widget-box " id="Wtareas">
                                        <div class="widget-header">
                                            
                                                <h4 class="lighter smaller">
                                                        <i class="icon-rss orange"></i><?php echo lang('tareas');?>
                                                        
                                                </h4>

                                                <div class="widget-toolbar no-border">
                                                        <ul class="nav nav-tabs" id="recent-tab">
                                                                <li class="active">
                                                                        <a data-toggle="tab" href="#noconcretadas"  onclick="listadoTareas('noconcretadas');"><?php echo lang('pendientes');?></a>
                                                                </li>

                                                                <li>
                                                                        <a data-toggle="tab" href="#concretadas" onclick="listadoTareas('concretadas');"><?php echo lang('concretadas');?></a>
                                                                </li>

                                                                <li>
                                                                        <a data-toggle="tab" href="#eliminadas" onclick="listadoTareas('eliminadas');"><?php echo lang('eliminadas');?></a>
                                                                </li>
                                                                
                                                        </ul>
                                                    
                                                </div>
                           
                                            
                                        </div>

                                        <div class="widget-body">
                                                <div class="widget-main padding-4">
                                                        
                                                        <div class="tab-content padding-8 overflow-visible">
                                                           
                                                                <div id="noconcretadas" class="tab-pane active listadoTareas"></div>
                                                                
                                                                <div id="concretadas" class="tab-pane"></div>

                                                                
                                                                <div id="eliminadas" class="tab-pane"></div>

                                                                        

                                                        </div>

                                                </div>
                                               
                                                <form id="frmTareas">
                                                    <div class="form-actions">
                                                        <div class="row">
                                                            <div class="col-xs-12">                                                                   
                                                                <input type="hidden"  name="codigo" value="-1">                                                                    
                                                                <input placeholder="<?php echo lang('escriba_nueva_tarea');?>"  class="form-control" name="respuesta">
                                                            
                                                            </div>
                                                        </div>
                                                        <div class="row" style="padding-top: 6px;">
                                                            <div class="col-xs-9">
                                                                <select name="usuarios_asignados[]" multiple="true" data-placeholder="<?php echo lang("asigne_usuarios_para_la_nueva_tarea") ?>">
                                                                    <?php foreach ($usuarios_tareas as $usuario){ ?> 
                                                                    <option value="<?php echo $usuario['codigo'] ?>">
                                                                        <?php echo $usuario['nombre']." ".$usuario['apellido'] ?>
                                                                    </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-xs-2">
                                                                <span class="input-group-btn">
                                                                    <button class="btn btn-sm btn-info no-radius" type="submit">
                                                                        <i class="icon-share-alt"></i>
                                                                        <?php echo lang('guardar')?>
                                                                    </button>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                        </div><!-- /widget-body -->
                                        
                                </div><!-- /widget-box -->
                                
                        </div>

                    </div>
                </div>
                
                <div class="col-md-6">
                    
                    <div class="row">
                        <form id="frmSugerenciaBaja">
                        <div class="col-xs-12">
                                <div class="widget-box" id="Wsugerencias">
                                        <div class="widget-header widget-header-flat">
                                                <h4 class="lighter">

                                                       <?php echo lang('sugerencias_de_baja');?>
                                                </h4>

                                                <div class="widget-toolbar">
                                                        <a href="#" data-action="collapse">
                                                                <i class="icon-chevron-up"></i>
                                                        </a>
                                                </div>

                                                <div class="widget-toolbar no-border">
                                                    <button class="btn btn-minier btn-primary dropdown-toggle" type="submit">
                                                            <?php echo lang('dar_de_baja')?>

                                                    </button>

                                                </div>
                                        </div>

                                        <div class="widget-body">
                                            <div class="widget-body-inner" style="display: block;">
                                                <div class="widget-main no-padding">

                                                    <!--<div class="table-responsive" id="contentTablaSugerencias">-->
                                                        <table class="table table-striped" id="tablaSugerencias">
                                                             <thead>

                                                            </thead>
                                                        <body></body>
                                                        </table>




                                                    <!--</div>-->

                                                </div><!-- /widget-main -->
                                        </div></div><!-- /widget-body -->
                                </div><!-- /widget-box -->
                        </div>
                        </form>

                        </div>



                </div>

<!-- PAGE CONTENT ENDS -->
        

              