<script src="<?php echo base_url('assents/js/librerias/ajaxchosen/lib/ajax-chosen.js') ?>"></script>
<link rel="stylesheet" href="<?php echo base_url('assents/css/datepicker3.css') ?>"/>

<style>
    .chosen-results{
        max-height: 100px !important;
    }

    #detalleCtacte tbody tr td{
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

<script>
    var CTACTE_IMPUTAR = '<?php echo $ctacte_imputar ?>';
    var COBRO_ESTADO = '<?php echo $nc->estado; ?>';
</script>

<script src="<?php echo base_url('assents/js/notas_credito/frm_imputar.js') ?>"></script>


<div class="modal-content">
    <div class="modal-header"  >
        <h4 class="blue"><?php echo lang('imputar_nc'); ?>
         <small>
                <i class="icon-double-angle-right"></i>
                <?php echo $alumno['nombre'] ?>
            </small>
        </h4>
    </div>

    <div class="modal-body overflow-visible no-margin-bottom">

        <form id="cobrar" class="form " role="form" >

            <div class="row datos">
                <div class="tabbable">
                    <ul class="nav nav-tabs" id="myTab">
                        <li  class="active">
                            <a data-toggle="tab" href="#pendientes">
                                <?php echo lang('pendientes'); ?>
                            </a>
                        </li>

                        <li>
                            <a data-toggle="tab" href="#imputaciones">
                                <?php echo lang('imputaciones'); ?>
                            </a>
                        </li>


                    </ul>

                    <div class="tab-content">

                        <div id="pendientes" class="tab-pane active" style="overflow-y: auto;overflow-x:hidden ;" >
                            <div class="row">
                                <div class="col-md-8">

                                    <label><?php echo lang('resta_imputar') . ' '; ?></label>
                                    <label name="res_imp" id="res_imp"></label>&nbsp&nbsp<i class="icon-refresh" name="refresh_resto"></i>

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
                                <div class="content-tabla-ctacte"></div>
                            </div>

                        </div>


                        <div id="imputaciones" class="tab-pane">

                            <label><?php echo lang('total_imputaciones') . ' '; ?></label>
                            <label name="tot_imp" id="tot_imp"></label>
                            <div class="table-responsive" >
                                <table  class="table table-striped table-bordered" id="tablaImputaciones">
                                    <thead>
                                        <tr><th><?php echo lang('eliminar'); ?></th>
                                            <th><?php echo lang('descripcion'); ?></th>
                                            <th><?php echo lang('fecha_vencimiento'); ?></th>
                                            <th><?php echo lang('imputado'); ?></th>
                                            <th><?php echo lang('importe'); ?></th>
                                            <th><?php echo lang('estado'); ?></th></tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>

                            </div>


                        </div>

                    </div>
                </div>

            </div>

            <input type="hidden" value="<?php echo $codigo ?>" name="codigo">
            <input type="hidden" value="<?php echo $moneda['id'] ?>" data-simbolo="<?php echo $moneda['simbolo'] ?>" name="moneda">

            <div class="row totales">
                <br>
                <div class="form-group col-md-3 col-md-offset-9 ">
                    <div class="input-group">
                        <span class="input-group-addon"><?php echo $moneda['simbolo'] ?></span>
                        <input type="text" class="form-control input-mask-product" name='total_nc' readonly value='<?php echo $total_nc; ?>'>

                    </div>

                </div>

                <div class="form-group col-md-2  col-xs-12 hide">
                    <label for="exampleInputFile"><?php echo lang('total_seleccionado'); ?></label>
                    <div class="input-group ">
                        <span class="input-group-addon"><?php echo $moneda['simbolo'] ?></span>
                        <input type="" class="form-control input-sm " name='total' value=''  readonly>

                    </div>


                </div>


            </div>

        </form>



    </div>


    <div class="modal-footer no-margin-top">

        <button name = "confirmar" class = "btn btn-sm btn-success">
            <i class = "icon-ok"></i>
            <?php echo lang('guardar'); ?>
        </button>

    </div>
</div>