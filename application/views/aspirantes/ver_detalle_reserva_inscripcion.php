
                                    
<div class="modal-content">
    <div class="modal-body overflow-visible">
              <div class="page-content">
                                            <div class="row">
                                                    <div class="col-md-12">
                                                            <!-- PAGE CONTENT BEGINS -->

                                                            <div class="row">
                                                                    <div class="col-md-12  area_impresion">
                                                                            <div class="widget-box transparent invoice-box">
                                                                                    <div class="widget-header widget-header-large">
                                                                                            <h3 class="blue lighter pull-left position-relative ">
                                                                                                   <?php echo lang('reserva_de_inscripcion');?>
                                                                                            </h3>

                                                                                            <div class="widget-toolbar no-border invoice-info">
                                                                                                    <span class="invoice-info-label"><?php echo lang('codigo');?>:</span>
                                                                                                    <span class="red"><?php echo $objReserva->getCodigo();?></span>

                                                                                                    <br />
                                                                                                    <span class="invoice-info-label"><?php echo lang('fecha');?>:</span>
                                                                                                    <span class="blue"><?php echo formatearFecha_pais($objReserva->fecha)?></span>
                                                                                            </div>

                                                                                            <div class="widget-toolbar hidden-480">
                                                                                                    <a href="javascript:void(0)">
                                                                                                            <i class="icon-print" style="display: none !important"></i>
                                                                                                    </a>
                                                                                            </div>
                                                                                    </div>

                                                                                    <div class="widget-body">
                                                                                            <div class="widget-main padding-24">
                                                                                                    <div class="row">
                                                                                                            <div class="col-sm-8">
                                                                                                                    <div class="row">
                                                                                                                            <div class="col-xs-11 label label-lg label-info arrowed-in arrowed-right">
                                                                                                                                    <b><?php echo lang('reserva_info');?></b>
                                                                                                                            </div>
                                                                                                                    </div>

                                                                                                                    <div class="row">
                                                                                                                            <ul class="list-unstyled spaced">
                                                                                                                                    <li>
                                                                                                                                            <i class="icon-caret-right blue"></i>
                                                                                                                                            <?php echo lang('nombre');?>:
                                                                                                                                            <b class="black"><?php echo ucwords(strtolower($objReserva->nombre));?></b>
                                                                                                                                    </li>

                                                                                                                                    <li>
                                                                                                                                            <i class="icon-caret-right blue"></i>
                                                                                                                                            <?php echo lang('email');?>:
                                                                                                                                            <b class="black"><?php echo $objReserva->email;?></b>
                                                                                                                                    </li>

                                                                                                                                    
                                                                                                                                    <li>
                                                                                                                                            <i class="icon-caret-right blue"></i>
                                                                                                                                            <?php echo lang('telefono');?>:
                                                                                                                                            <b class="red"><?php echo $objReserva->telefono?></b>
                                                                                                                                    </li>

                                                                                                                                    
                                                                                                                            </ul>
                                                                                                                    </div>
                                                                                                            </div><!-- /span -->


                                                                                                    </div><!-- row -->

                                                                                                    <div class="space"></div>

                                                                                                    <div class="col-md-12">
                                                                                                            <table class="table table-striped table-bordered table-responsive">
                                                                                                                    <thead>
                                                                                                                            <tr>
                                                                                                                                    <th><?php echo lang('comision');?></th>
                                                                                                                                    <th class="hidden-xs"><?php echo lang('curso_presu_as');?></th>
                                                                                                                                    <th class="hidden-480"><?php echo lang('frm_nuevaMatricula_PlanDePago');?></th>
<!--                                                                                                                                    <th><?php echo lang('matricula');?></th>
                                                                                                                                    <th><?php echo lang('cuota');?></th>-->
                                                                                                                                    <th><?php echo lang('fecha_vigencia');?></th>
                                                                                                                            </tr>
                                                                                                                    </thead>

                                                                                                                    <tbody>
                                                                                                                            <tr>
                                                                                                                               <td><?php echo $arrDetalle[0]['nombre_comision'];?></td>
                                                                                                                                <td><?php echo $arrDetalle[0]['nombre_curso'];?></td>
                                                                                                                                <td><?php echo $arrDetalle[0]['plan_pago'] == '' ? lang('no_especifica') :$arrDetalle[0]['plan_pago'];?></td>
<!--                                                                                                                                <td><?php echo $arrDetalle[0]['plan_pago'] == '' ? lang('no_especifica') : $arrDetalle[0]['valormatricula'];?></td>
                                                                                                                                <td><?php echo $arrDetalle[0]['plan_pago'] == '' ? lang('no_especifica') : $arrDetalle[0]['valor_nro_cuotas'];?></td>-->
                                                                                                                                <td><?php echo $arrDetalle[0]['plan_pago'] == '' ? lang('no_especifica') : $arrDetalle[0]['fechavigencia'];?></td>
                                                                                                                            </tr>
                                                                                                                    </tbody>
                                                                                                            </table>
                                                                                                    </div>

<!--                                                                                                    <div class="hr hr8 hr-double hr-dotted"></div>

                                                                                                    <div class="row">
                                                                                                            <div class="col-sm-5 pull-right">
                                                                                                                    <h4 class="pull-right">
                                                                                                                            Total amount :
                                                                                                                            <span class="red">$395</span>
                                                                                                                    </h4>
                                                                                                            </div>
                                                                                                            <div class="col-sm-7 pull-left"> Extra Information </div>
                                                                                                    </div>

                                                                                                    <div class="space-6"></div>
                                                                                                    <div class="well">
                                                                                                            Thank you for choosing Ace Company products.
                                    We believe you will be satisfied by our services.
                                                                                                    </div>-->
                                                                                            </div>
                                                                                    </div>
                                                                            </div>
                                                                    </div>
                                                            </div>

                                                            <!-- PAGE CONTENT ENDS -->
                                                    </div><!-- /.col -->
                                            </div><!-- /.row -->
                                    </div><!-- /.page-content -->

    </div>

    
</div> 