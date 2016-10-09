<script src="<?php echo base_url('assents/js/configuracion/vistaPlanPago.js') ?>"></script>

<style>

    .widget-body{

        max-height: 400px !important;
        overflow: auto;

    }


    #widgetPuntosVentas .table-responsive{


        max-height: 300px !important;
        overflow: auto;

    }


    .chosen-results{


        max-height: 80px !important;

    }


    textarea{
        min-height: 200px !important;
    }

</style>






<div class="col-md-12 col-xs-12">
    <div id="areaTablas" class="">
        <div class="tabbable">
            <?php
            $data['tab_activo'] = 'configPlanPago';
            $this->load->view('configuracion/vista_tabs', $data);
            ?>               
            <div class="tab-content">
                <div id="facturacion" class="tab-pane in active">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6 no-padding-left no-padding-right">
                                <!--PLANED DE PAGO-->
                                <div class="col-xs-12 col-sm-12 widget-container-span ui-sortable">
                                    <div class="widget-box">
                                        <div class="widget-header  header-color-blue">
                                            <h6>
                                                <i class="icon-sort"></i>
                                                <?php echo lang('planpago_periodo') ?>
                                            </h6>

                                            <div class="widget-toolbar">


                                                <a href="#" data-action="collapse">
                                                    <i class="icon-chevron-up"></i>
                                                </a>


                                            </div>


                                            <div class="widget-toolbar no-border">


                                                <button class="btn btn-info btn-xs" type="button" name="nuevaPeriodicidad">
                                                    <i class="icon-ok"></i>
                                                    <?php echo lang('nuevo') ?>
                                                </button>


                                            </div>


                                        </div>

                                        <div class="widget-body"><div class="widget-body-inner" style="display: block;">
                                                <div class="widget-main">
                                                    <!--                                                <div class="row">
                                                                                                        <div class="col-md-12 col-xs-12">
                                                                                                            <button class="btn btn-info btn-xs" type="button" name="nuevaPeriodicidad">
                                                                                                                            <i class="icon-ok"></i>
                                                    <?php echo lang('nuevo') ?>
                                                                                                            </button>
                                                                                                        </div>
                                                                            
                                                                                                    </div>-->
                                                    <br>
                                                    <div class="row">
                                                        <div class="col-md-12 col-xs-12">


                                                            <div class="table-responsive" id="periodicidad">



                                                            </div>


                                                        </div>
                                                    </div>

                                                    <!--                                                    <div class="row">
                                                    
                                                    
                                                                                                            <div class="form-group col-md-12 col-xs-12">
                                                    
                                                                                                                <div class="col-md-5 col-xs-5"><?php echo lang('usa_descuento_condicionado') ?></div>
                                                                                                                <div class="col-md-5 col-xs-5"><hr></div>
                                                                                                                <div class="col-md-1 col-xs-1">
                                                                                                                    <label>
                                                                                                                        <input name="descuentosCondicionados" class="ace ace-switch ace-switch-6" type="checkbox" onchange="guardarConfiguracionDescuentos(this);" <?php echo $dtoCondicionado == 0 ? '' : 'checked' ?>>
                                                                                                                        <span class="lbl"></span>
                                                                                                                    </label>
                                                                                                                </div>
                                                    
                                                                                                            </div>
                                                    
                                                    
                                                                                                        </div>-->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 widget-container-span ui-sortable">
                                    <div class="widget-box">
                                        <div class="widget-header  header-color-blue">
                                            <h6>
                                                <i class="icon-sort"></i>
                                                <?php echo lang('descuentos') ?>
                                            </h6>

                                            <div class="widget-toolbar">


                                                <a href="#" data-action="collapse">
                                                    <i class="icon-chevron-up"></i>
                                                </a>


                                            </div>



                                        </div>

                                        <div class="widget-body"><div class="widget-body-inner" style="display: block;">
                                                <div class="widget-main">


                                                    <div class="row">


                                                        <div class="form-group col-md-12 col-xs-12">

                                                            <div class="col-md-5 col-xs-5"><?php echo lang('usa_descuento_condicionado') ?></div>
                                                            <div class="col-md-5 col-xs-5"><hr></div>
                                                            <div class="col-md-1 col-xs-1">
                                                                <label>
                                                                    <input name="descuentosCondicionados" id="descuentosCondicionados" class="ace ace-switch ace-switch-6 chosen-default" type="checkbox" onchange="guardarConfiguracionDescuentos('descuentosCondicionados');" <?php echo $dtoCondicionado == 0 ? '' : 'checked' ?>>
                                                                    <span class="lbl"></span>
                                                                </label>
                                                            </div>

                                                        </div>


                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12 col-xs-12">
                                                            <?php $oculto = $dtoCondicionado == '0' ? 'hide' : '' ?>

                                                            <div class="table-responsive <?php echo $oculto ?>" id="requisitos">

                                                                <table id="tablarequerimientos" class="table table-striped">
                                                                    <thead>
                                                                    <th><?php echo lang('propiedades') ?></th>
                                                                    <th></th>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td><label><?php echo lang('dias_prorroga'); ?></label></td>
                                                                            <td>
                                                                                <select name="dias_prorroga" onchange="guardarConfiguracionDescuentos('descuentosCondicionados');">

                                                                                    <?php
                                                                                    for ($index = 0; $index <= 31; $index++) {

                                                                                        $s = $dtoCondicionado_dias == $index ? 'selected' : '';

                                                                                        echo '<option value="' . $index . '" ' . $s . '>' . $index . '</option>';
                                                                                    }
                                                                                    ?>
                                                                                </select>

                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>

                                                            </div>


                                                        </div>
                                                    </div>



                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 no-padding-left no-padding-right">
                                <!--CUENTA CORRIENTE-->

                                <div class="col-xs-12 col-sm-12 widget-container-span ui-sortable">
                                    <div class="widget-box">
                                        <div class="widget-header  header-color-pink">
                                            <h6>
                                                <i class="icon-sort"></i>
                                                <?php echo lang('cuenta_corriente') ?>
                                            </h6>

                                            <div class="widget-toolbar">

                                                <a href="#" data-action="collapse">
                                                    <i class="icon-chevron-up"></i>
                                                </a>


                                            </div>
                                        </div>

                                        <div class="widget-body">
                                            <div class="widget-body-inner" style="display: block;">
                                                <div class="widget-main">



                                                    <div class="row">

                                                        <div class="form-group col-md-12 col-xs-12">

                                                            <div class="col-md-5 col-xs-5"><?php echo lang('vigencia_presupuesto') ?></div>
                                                            <div class="col-md-4 col-xs-4"><hr></div>
                                                            <div class="col-md-3 col-xs-3">
                                                                <select name="vigencia" onchange="guardarValorCuentaCorriente(this);">
                                                                    <?php
                                                                    for ($index = 1; $index <= 100; $index++) {

                                                                        $b = $vigenciaPresupuesto == $index ? 'selected' : '';
                                                                        echo '<option value="' . $index . '" ' . $b . '>' . $index . '</option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                                <!--<input class="form-control input-sm" onkeypress="" value="<?php echo $vigenciaPresupuesto ?>" name="vigencia">-->
                                                            </div>

                                                        </div>

                                                    </div>
                                                    <div class="row">

                                                        <div class="form-group col-md-12 col-xs-12">

                                                            <div class="col-md-5 col-xs-5"><?php echo lang('cantidad_alertas_deudores') ?></div>
                                                            <div class="col-md-4 col-xs-4"><hr></div>
                                                            <div class="col-md-3 col-xs-3 form-group">
                                                                <select name="cantAlertas" onchange="guardarValorCuentaCorriente(this);">

                                                                    <?php
                                                                    for ($index = 1; $index <= 100; $index++) {

                                                                        $s = $alertaDeudores == $index ? 'selected' : '';

                                                                        echo '<option value="' . $index . '" ' . $s . '>' . $index . '</option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                                <!--<input class="form-control input-sm" onkeypress="" value="<?php echo $alertaDeudores ?>" name="cantAlertas">-->
                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="row">

                                                        <div class="form-group col-md-12 col-xs-12">

                                                            <div class="col-md-5 col-xs-5"><?php echo lang('baja_directa_morosos') ?></div>
                                                            <div class="col-md-5 col-xs-5"><hr></div>
                                                            <div class="col-md-1 col-xs-1">
                                                                <label>
                                                                    <input name="bajaMorosos" class="ace ace-switch ace-switch-6" type="checkbox" value="1" onchange="guardarValorCuentaCorriente(this);"<?php echo $bajaMorosos == 0 ? '' : 'checked' ?>>
                                                                    <span class="lbl"></span>
                                                                </label>
                                                            </div>

                                                        </div>

                                                    </div>




                                                    <div class="row">

                                                        <div class="form-group col-md-12 col-xs-12">

                                                            <div class="col-md-5 col-xs-5"><?php echo lang('sugerencia_baja') ?></div>
                                                            <div class="col-md-5 col-xs-5"><hr></div>
                                                            <div class="col-md-1 col-xs-1">
                                                                <label>
                                                                    <input name="alertasSugerencia" class="ace ace-switch ace-switch-6" type="checkbox" value="1" onchange="guardarValorCuentaCorriente(this);"<?php echo $alertaSugerenciaBaja == 0 ? '' : 'checked' ?>>
                                                                    <span class="lbl"></span>
                                                                </label>


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
</div>

