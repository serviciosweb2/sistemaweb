<script>

    var langFrm = <?php echo $langFrm ?>;

</script>
<script src="<?php echo base_url('assents/js/configuracion/frm_caja.js') ?>"></script>
<style>

    .checkbox{

        padding: 0px !important;
        margin: 0px !important;
    }

</style>
<div class="modal-content">
    <div class="modal-header">
        <h4 class="blue bigger">
            <?php if ($caja->getCodigo() == -1) {
                echo lang('nueva_caja');
            } else {
                echo lang('modificar_caja');
            } ?>

        </h4>
    </div>
    <form id="frmCaja">
        <input type="hidden" name="codigo" value='<?php echo $caja->getCodigo() ?>' />
        <div class="modal-body overflow-visible">
            <div class="row">

                <div class="col-md-12 col-xs-8">   
                    <div class="row">
                        <div class="col-md-8 form-group">
                            <label><?php echo lang('nombre') ?></label>
                            <input class="form-control" name="nombre"  value='<?php echo $caja->nombre ?>' />
                        </div>                    
                        <div class="col-md-4 form-group">
                            <label><?php echo lang("moneda"); ?></label>
                            <select name="cod_moneda" class="form-control" 
                                <?php if ($caja->estado == 'abierta') { ?>  <?php } ?>> <!--disabled="true"-->
                                <?php foreach ($arrCotizaciones as $cotizacion){ ?> 
                                <option value="<?php echo $cotizacion['id'] ?>"
                                        <?php if ($cotizacion['id'] == $moneda_default){ ?>selected="true"<?php } ?>>
                                    <?php echo $cotizacion['moneda']." ".$cotizacion['simbolo'] ?>
                                </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    
                    
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <div class="row">
                                <table name="tablaMedios" class="table table-striped table-bordered" id="tablaMedios">
                                    <thead>
                                    <th><?php echo lang('medio') ?></th>
                                    <th><?php echo lang('HABILITADO') ?></th>
                                    <th><?php echo lang('movimientos_en_caja') ?></th>
                                    <th><?php echo lang('confirmacion_automatica_cobro') ?></th>
                                    </thead>    
                                    <tbody>
                                    <?php foreach ($medios as $key => $medio) { ?>
                                            <tr>
                                                <td><?php echo $medio['medio'] ?></td>
                                                <td>
                                                    <label>
                                                        <input  name="medio_habilitado[<?php echo $key ?>]" medio="<?php echo $medio["codigo"] ?>" 
                                                                class="ace ace-switch ace-switch-6" type="checkbox" <?php echo $medio['habilitado'] == 1 ? "checked" : ""; ?> 
                                                                value="<?php echo $medio["codigo"] ?>" onchange="habilitarMedio(this);"
                                                                <?php if ($caja->estado == 'abierta') { ?>  <?php } ?>> <!--disabled="true"-->
                                                        <span class="lbl"></span>
                                                    </label>
                                                </td>
                                                <td><?php echo $caja->estado; ?>
                                                    <select  name="medio_entsal[<?php echo $key ?>]" medio="<?php echo $medio["codigo"] ?>" 
                                                        <?php echo $medio['habilitado'] <> 1 || $caja->estado == 'abierta' ? '' : ''; ?>> <!--disabled = "disabled"-->
                                                        <option <?php echo $medio['ent_sal'] != 1 ? "selected" : ""; ?> value="e"><?php echo lang('entrada_caja_cabecera') ?></option>
                                                        <option <?php echo $medio['ent_sal'] == 1 ? "selected" : ""; ?> value="e_s"><?php echo lang('entrada_caja_cabecera') . '/' . lang('salida_caja_cabecera') ?></option>        
                                                    </select>
                                                </td>
                                                <td>
                                                    <label>
                                                        <input id="conf_auto" name="medio_confir_auto[<?php echo $key ?>]" value="true" class="ace ace-switch ace-switch-6 confir" 
                                                               medio="<?php echo $medio["codigo"] ?>" type="checkbox" <?php echo $medio['conf_auto'] == 1 ? "checked" : ""; ?> 
                                                                   <?php echo $medio['habilitado'] <> 1 || $caja->estado == 'abierta' ? '' : ''; ?> > <!--disabled = "disabled"-->
                                                        <span class="lbl"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>  
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label><?php echo lang('usuarios') ?></label>
                            <div class="row">
                                <?php foreach ($usuarios as $usuario) { ?>
                                    <div class="col-md-4   ">
                                        <div class="checkbox " >
                                            <label>
                                                <input name="form-usuario-checkbox[]" class="ace ace-checkbox-2" type="checkbox"  value="<?php echo $usuario["codigo"] ?>"  <?php echo in_array($usuario, $usuariosAsignados) ? "checked" : ""; ?>>
                                                <span class="lbl"><?php echo $usuario['nombre'] ?></span>
                                            </label>
                                        </div>
                                    </div>
                            <?php } ?>
                            </div>
                        </div>

                    </div>  
                    <row>
                        <div class="col-md-6 col-xs-12 form-group">
                            <label> <?php echo lang('HABILITADO') ?></label>
                            <br>
                            <input name="habilitado" class="ace ace-switch ace-switch-6" type="checkbox"  accesskey=""
                                   accept=""<?php if ($caja->getCodigo() == -1 || $caja->desactivada == "0") { ?> checked="true" <?php } ?>
                                   <?php if ($caja->estado == 'abierta') { ?>  <?php } ?>> <!--disabled="true"-->
                            <span class="lbl"></span>
                        </div>
                    </row>
                </div>
            </div>
        </div>
    </form>
    <div class="modal-footer">
        <button class="btn btn-sm btn-primary btn-guardar " <?php echo lang('guardar') ?>>
            <i class="icon-ok"></i>
            <?php echo lang('guardar') ?>
        </button>
    </div>
</div>