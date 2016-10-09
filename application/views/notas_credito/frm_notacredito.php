<script src="<?php echo base_url('assents/js/librerias/ajaxchosen/lib/ajax-chosen.js') ?>"></script>
<script src="<?php echo base_url('assents/js/notas_credito/frm_notacredito.js') ?>"></script>
<link rel="stylesheet" href="<?php echo base_url('assents/css/datepicker3.css') ?>"/>

<style>
    .chosen-results{
        max-height: 100px !important;
    }

    #detalleCtacte tbody tr td{
        padding: 1px !important;
        /*        width: 20px !Important;    */
    }

    #facturasTable tbody tr td{
        padding: 1px !important;
        /*        width: 20px !Important;    */
    }

    .input-group-addon{

        width:  1% !important;
    }


    .fancybox-wrap .modal-header {
        padding: 1px!important;

    }

    .mySearch input{
        border: 1px solid #6fb3e0 !Important;
        width: 20px !Important;
        height: 28px!important;
        border-radius: 50px!important;
        font-size: 13px !Important;
        color: #666!important;
        z-index: 11;
        -webkit-transition: width ease .15s !Important;
        transition: width ease .15s !Important;
    }


    .mySearch input:focus{

        width: 90% !important;
        border-radius: 0px!important;

    }

    .mySearch i:focus{

        width: 100% !important;
        border-radius: 0px!important;

    }
    .mySearch .input-icon{
        width: 100% !important
    }
    .mySearch label{
        //  padding-bottom: 1px !important;
    }
</style>

<div class="modal-content">
    <?php
    if ($codigo == '') {
        $titulo = lang('nueva_nc');
    } else {
        $titulo = lang('modificar_nc');
    }
    ?>
    <div class="modal-header"  >
        <h4 class="blue"><?php echo $titulo; ?></h4>
    </div>

    <div class="modal-body overflow-visible no-margin-bottom">   

        <form id="nota_credito" class="form " role="form" >

            <div class="row principal">

                <div class="form-group col-md-4 ">
                    <label><?php echo lang('fecha'); ?></label>
                    <div class="input-group">
                        <input class="form-control date-picker" value="<?php
                        if ($codigo == '') {
                            echo formatearFecha_pais(date("Y-m-d"));
                        } else {
                            echo formatearFecha_pais($cobro->fechareal);
                        }
                        ?>"  id="fecha_nota" name="fecha_nota" type="text" data-date-format="dd-mm-yyyy">
                        <span class="input-group-addon">
                            <i class="icon-calendar bigger-110"></i>
                        </span>
                    </div>
                </div>    


                <div class="form-group col-md-4 ">
                    <label for="exampleInputEmail1"><?php echo lang('ALUMNO'); ?></label>
                    <select name="alumnos" data-placeholder="<?php echo lang('seleccione_alumno'); ?>" <?php
                    if ($codigo != '') {
                        echo 'disabled';
                    }
                    ?> multiple>
                                <?php
                                if ($codigo != '') {
                                    echo '<option value="' . $alumno['codigo'] . '" selected >' . $alumno['nombre'] . '</option>';
                                }
                                ?>
                    </select>

                </div>

            </div>

            <div class="row facturas" style="overflow-y: auto;overflow-x:hidden ;">
                <div class="row">
                    <div class="col-md-8">
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mySearch no-margin-top ">

                            <span class="input-icon">
                                <input  class="form-control" aria-controls="facturasTable" onkeyup="mySearchFactura(this)" id="buscadorFactura">
                                <i class="icon-search blue "></i>
                            </span>

                        </div> 
                    </div>
                </div>
                <div class="content-tabla-facturas col-md-12">
                    <table id="facturasTable" cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered table-condensed">
                        <thead>
                        <th></th>
                        <th><?php echo lang('Facturas'); ?></th>
                        <th><?php echo lang('fecha'); ?></th>
                        <th><?php echo lang('importe'); ?></th>
                        <th><?php echo lang('importe_NC'); ?></th>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div>

<!--            <div class="row imputaciones" style="overflow-y: auto;overflow-x:hidden ;">
                <div class="row">
                    <div class="col-md-8">
                        <?php if ($codigo != '') { ?>
                            <label><?php echo lang('resta_imputar') . ' '; ?></label>
                            <label name="res_imp" id="res_imp"></label>&nbsp&nbsp<i class="icon-refresh" name="refresh_resto"></i>
                        <?php } ?>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mySearch no-margin-top ">

                            <span class="input-icon">
                                <input  class="form-control" aria-controls="detalleCtacte" onkeyup="mySearch(this)" id="buscador">
                                <i class="icon-search blue "></i>
                            </span>

                        </div> 
                    </div>
                </div>
                <div class="row col-md-12">
                    <div class="content-tabla-ctacte">
                        <table id="detalleCtacte" cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered table-condensed">
                            <thead>
                            <th></th>
                            <th><?php echo lang('descripcion'); ?></th>
                            <th><?php echo lang('fecha_vencimiento'); ?></th>
                            <th><?php echo lang('importe'); ?></th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

            </div>-->
<!--
            <input type="hidden" value="<?php echo $codigo ?>" name="codigo">-->
            <input type="hidden" value="<?php echo $moneda['simbolo'] ?>" data-simbolo="<?php echo $moneda['simbolo'] ?>" name="moneda">

            <div class="row totales">
                <br>
                <div class="form-group col-md-9">
                    <label class="blue bigger-110 pull-left col-sm-1" id="motivo"><?php echo lang('motivo_nc') . ' '; ?></label>
                    <div class="col-sm-6">
                     <select  class="form-control" id="exampleInputEmail1" name="motivo" data-placeholder="<?php echo lang('seleccione_motivo');?>">
                        <option></option>
                        <?php foreach($motivos as $motivoValor){ ?>
                        <option value='<?php echo $motivoValor["id"] ?>'>
                            <?php echo $motivoValor["motivo"]; ?>
                        </option>
                        <?php } ?>
                    </select>
                    </div>
                </div>
                <div class="form-group col-md-3">
                    <!--<label class=""for="exampleInputFile" ><?php echo lang('importe') . ' ' . lang('total'); ?></label>-->
                    <div class="input-group">
                        <span class="input-group-addon"><?php echo $moneda['simbolo'] ?></span>
                        <input type="text" class="form-control input-mask-product" name='total_nota' readonly value='<?php
                        if ($codigo != '') {
                            echo $total_nota;
                        }
                        ?>'>
                               <?php if ($codigo == '' || ( $codigo != '' && $cobro->estado != 'confirmado' )) { ?>
                            <span class="input-group-addon" style="cursor: pointer;" name="calcularTotal">
                                <i class="icon-refresh"></i>
                            </span>
                        <?php } ?>
                    </div>

                </div>

<!--                <div class="form-group col-md-2  col-xs-12 hide">
                    <label for="exampleInputFile"><?php echo lang('total_seleccionado'); ?></label>
                    <div class="input-group ">
                        <span class="input-group-addon"><?php echo $moneda['simbolo'] ?></span>

                    </div>


                </div>-->


            </div>

        </form>



    </div>


    <div class="modal-footer no-margin-top">

<!--        <button name="imputar" class="btn btn-sm btn-primary ">
            <?php echo lang('imputar'); ?>
            <i class="icon-arrow-right"></i>

        </button>

        <button name="volver" class="btn btn-sm btn-primary">
            <i class="icon-arrow-left"></i>
            <?php echo lang('volver'); ?>

        </button>-->

        <button name = "confirmar" class = "btn btn-sm btn-success">
            <i class = "icon-ok"></i>
            <?php echo lang('guardar'); ?>
        </button>



    </div>
</div>
