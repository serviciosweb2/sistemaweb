<script src="<?php echo base_url('assents/js/librerias/ajaxchosen/lib/ajax-chosen.js') ?>"></script>
<link rel="stylesheet" href="<?php echo base_url('assents/css/datepicker3.css') ?>"/>

<style>
    .chosen-results{
        max-height: 100px !important;
    }

    #detalleCtacte tbody tr td{
        padding: 1px !important;
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

    }

    .totales {
        margin-top: 25px;
    }
</style>

<script>
    var CTACTE_IMPUTAR = '<?php echo $ctacte_imputar ?>';
    var MEDIO_COBRO = '<?php echo $medio_cobro ?>';
    var COBRO_ESTADO = '<?php if ($codigo != '') {
    echo $cobro->estado;
} else {
    '';
} ?>';
    var permite_editar_medios = <?php echo $permite_editar_medios ? "true" : "false"; ?>
</script>
<script src="<?php echo base_url('assents/js/cobros/frm_cobros.js') ?>"></script>
<div class="modal-content">
    <?php if ($codigo == '') {
        $titulo = lang('nuevo-cobro');
    } else {
        $titulo = lang('modificar_cobro');
    } ?>
    <div class="modal-header">
        <h4 class="blue"><?php echo $titulo; ?></h4>
    </div>
    <div class="modal-body overflow-visible no-margin-bottom">
        <form id="cobrar" class="form " role="form" >
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
                        ?>" id="fecha_cobro" name="fecha_cobro" type="text" data-date-format="dd-mm-yyyy">
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
                        <?php if ($codigo != '') {
                            echo '<option value="' . $alumno['codigo'] . '" selected >' . $alumno['nombre'] . '</option>';
                        } ?>
                    </select>
                </div>
            </div>
            <div class="row datos">
                <div class="tabbable">
                    <ul class="nav nav-tabs" id="myTab">
                        <li  class="active">
                            <a data-toggle="tab" href="#pendientes">
                        <?php echo lang('pendientes'); ?>
                            </a>
                        </li>
                        <?php if ($codigo != '') { ?>
                        <li>
                            <a data-toggle="tab" href="#imputaciones">
                        <?php echo lang('imputaciones'); ?>
                            </a>
                        </li>
                        <?php } ?>
                    </ul>
                    <div class="tab-content">
                        <div id="pendientes" class="tab-pane active" style="overflow-y: auto;overflow-x:hidden ;" >
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
                                <div class="content-tabla-ctacte"></div>
                            </div>
                        </div>
                        <?php if ($codigo != '') { ?>
                        <div id="imputaciones" class="tab-pane">
                            <label><?php echo lang('total_imputaciones') . ' '; ?></label>
                            <label name="tot_imp" id="tot_imp"></label>
                            <div class="table-responsive" >
                                <table  class="table table-striped table-bordered" id="tablaImputaciones">
                                    <thead>
                                        <tr>
                                            <th><?php echo lang('eliminar'); ?></th>
                                            <th><?php echo lang('descripcion'); ?></th>
                                            <th><?php echo lang('fecha_vencimiento'); ?></th>
                                            <th><?php echo lang('imputado'); ?></th>
                                            <th><?php echo lang('importe'); ?></th>
                                            <th><?php echo lang('estado'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="row pago">
                <div class="form-group  col-md-4 Mpago">
                    <label for="exampleInputFile"><?php echo lang('medios_de_pago'); ?></label>
                    <select class="form-control "  data-live-search="true" name="medio_cobro" data-placeholder="<?php echo lang('seleccione_medio_pago'); ?>"
                            <?php if (!$permite_editar_medios){ ?>disabled="true"<?php } ?>>
                        <option value=""></option>
                        <?php foreach ($mediosPago as $medio) {
                            $select = $codigo != '' && $cobro->medio_pago == $medio['codigo'] ? "selected" : '';
                            echo '<option value="' . $medio['codigo'] . '"' . $select . '>' . $medio["medio"] . '</option>';
                        } ?>
                    </select>
                </div>
                <div class="form-group  col-md-4 Mpago">
                    <label for="exampleInputFile"><?php echo lang('caja'); ?></label>
                    <select class="form-control "  data-live-search="true" name="caja" data-placeholder="<?php echo lang('caja'); ?>"
                            <?php if (!$permite_editar_medios){ ?>disabled="true"<?php } ?>>
                        <option value=""></option>
                    </select>
                </div>
            </div>
            <input type="hidden" value="<?php echo $codigo != '' ? $cobro->cod_caja : '' ?>" name="cajaCobro">
            <input type="hidden" value="<?php echo $codigo ?>" name="codigo">
            <input type="hidden" value="<?php echo $moneda['id'] ?>" data-simbolo="<?php echo $moneda['simbolo'] ?>" name="moneda">
            <div class="row totales">
                <div class="form-group col-md-3 col-md-offset-9 ">
                    <div class="input-group">
                        <span class="input-group-addon"><?php echo $moneda['simbolo'] ?></span>
                        <input type="text" class="form-control input-mask-product" name='total_cobrar' value='<?php
                               if ($codigo != '') {
                                   echo $total_cobro;
                               } ?>'>
                        <?php if ($codigo == '' || ( $codigo != '' && $cobro->estado != 'confirmado' )) { ?>
                            <span class="input-group-addon" style="cursor: pointer;" name="calcularTotal">
                                <i class="icon-refresh"></i>
                            </span>
                        <?php } ?>
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
        <button name="cobrar" class="btn btn-sm btn-primary guardarCobro">
            <?php echo lang('continuar'); ?>
            <i class="icon-arrow-right"></i>
        </button>
        <button name="volver" class="btn btn-sm btn-primary">
            <i class="icon-arrow-left"></i>
            <?php echo lang('volver'); ?>
        </button>
        <?php if ($codigo == '' || ( $codigo != '' && $cobro->estado != 'confirmado' )) { ?>
            <button name = "confirmar" class = "btn btn-sm btn-success">
                <i class = "icon-ok"></i>
                <?php echo lang('guardar'); ?>
            </button>
        <?php }
        if ($codigo != '' && $cobro->estado == 'confirmado') { ?>
            <button name = "confirmar_imp" class = "btn btn-sm btn-success">
                <i class = "icon-ok"></i>
                <?php echo lang('guardar'); ?>
            </button>
        <?php } ?>
    </div>
</div>