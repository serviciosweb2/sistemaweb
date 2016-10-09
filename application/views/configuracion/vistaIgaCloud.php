<style>

</style>
<?php
$idioma = get_idioma(); ?>

<link rel="stylesheet" href="<?php echo base_url('assents/css/dynatree/ui.dynatree.css')?>"/>
<script src="<?php echo base_url('assents/js/librerias/dynatree/jquery.dynatree.js');?>"></script>
<script src="<?php echo base_url('assents/js/configuracion/vistaIgaCloud.js');?>"></script>

<div class="col-md-12 col-xs-12">
    <div id="areaTablas" class="">
        <div class="tabbable">
            <?php $data['tab_activo'] = 'config_igacloud';
            $this->load->view('configuracion/vista_tabs',$data); ?>
            <div class="tab-content">
                <div id="tabAcademico" class="tab-pane in active">
                    <div class="row">
                        <div class="col-md-6 col-xs-12">
                            <!--WIDGET 2-->
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 widget-container-span ui-sortable">
                                    <div class="widget-box">
                                        <div class="widget-header header-color-green2">
                                            <h5><?php echo lang('como_nos_conocio');?></h5>
                                            <div class="widget-toolbar">
                                                <a href="#" data-action="collapse">
                                                    <i class="1 icon-chevron-up bigger-125"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="widget-body">
                                            <div class="widget-main">
                                                <div class="row">
                                                    <div class="col-sm-12 col-xs-12">
                                                        <div id="tree">

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>