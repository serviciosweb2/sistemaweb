<script>
    var columns = <?php echo $columns?>;
</script>
<script src="<?php echo base_url('assents/js/caja/vista_caja.js')?>"></script>
<style>
    .caja_abierta{
        font-weight: bold;
    }
    
    .caja_cerrada{
        font-weight: normal;
        color: #AAA;
    }
    
    .transferencia_disabled{
        color: #CCC !important;
    }
    
    
</style>
    <div class="col-md-12 col-xs-12">
        <div class="row">        
            <div class="btn-group col-md-9" style="margin-top: 16px;">
                <?php if ($permiso_nuevo_movimiento){ ?>
                    <button class="btn btn-primary boton-primario" name="btn_agregar_nuevo_movimiento" onclick="nuevoMovimientoCaja();">
                        <i class="icon-bookmark"></i>
                        <?php echo lang("agregar_movimiento_de_caja") ?>
                    </button>
                    <?php if ($permiso_transferencia){ ?>
                    <button class="btn dropdown-toggle btn-primary" data-toggle="dropdown">
                        <span class="icon-caret-down icon-only"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-default" style="width: 250px; margin-left: 12px;">
                        <li style="cursor: pointer" name="li_transferencia"
                            class="<?php echo $cajas_abiertas < 2 ? "transferencia_disabled" : "transferencia_enabled" ?>">
                            <a accion="factura-cobro" name="a_transferencia"
                               class="<?php echo $cajas_abiertas < 2 ? "transferencia_disabled" : "" ?>">
                                <?php echo lang("transferencia_entre_cajas"); ?>
                            </a>
                        </li>
                    </ul>
                    <?php } ?>
                <?php } ?>
            </div>
            <div class="col-md-2 form-group">
                <label><?php echo lang("cajas_abiertas") ?></label><br>
                <select name="cajas_abiertas" class="select-chosen" style="width: 100%;" onchange="checkButtons(); recargarTabla();">
                    <?php foreach ($cajas as $caja){ ?> 
                    <option value="<?php echo $caja['codigo'] ?>" <?php if ($caja['codigo'] == $caja_seleccionar){ ?> selected="true" <?php } ?>
                            class="caja_<?php echo $caja['estado'] ?>" id="<?php echo $caja['estado'] ?>">
                        <?php echo $caja['nombre'];
                        if ($caja['estado'] == "cerrada"){
                            echo "(".lang("cerrada").")";
                        }
                        ?>
                    </option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-1 form-group">
                <?php if ($permiso_abrir_caja){ ?>
                <button class="btn btn-app btn-light btn-xs" name="btn_abrir_caja" onclick="frmAbrirCajas();"
                    <?php if ($caja_seleccionar <> -1){ ?> style="display: none;" <?php } ?>>
                    <i class="icon-folder-open-alt"></i>
                    <?php echo lang("abrir"); ?>
                </button>
                <?php } ?>
                <?php if ($permiso_cerrar_caja){ ?>
                <button class="btn btn-app btn-light btn-xs" name="btn_cerrar_caja" onclick="frmCerrarCajas();"
                    <?php if ($caja_seleccionar == -1){ ?> style="display: none;" <?php } ?>>
                    <i class="icon-folder-close-alt"></i>
                    <?php echo lang("cerrar") ?>
                </button>
                <?php } ?>
            </div>
        </div> 
    </div>
    <div class="col-md-12 col-xs-12">
        <div class="row datos">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed" id="detalle_caja">
                        <thead>
<!--                            <th style="width: 160px;"><?php echo lang("fecha") ?></th>-->
<!--                            <th><?php echo lang("descripcion"); ?></th>
                            <th style="width: 130px;"><?php echo lang("entrada_caja_cabecera"); ?></th>
                            <th style="width: 130px;"><?php echo lang("salida_caja_cabecera"); ?></th>
                            <th style="width: 150px;"><?php echo lang("medio"); ?></th>
                            <th style="width: 190px;"><?php echo lang("usuario_caja_cabecera;"); ?></th>-->
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12 col-xs-12">
        <div name="div_detalle_saldos" style="margin-top: 26px;" class=""></div>
    </div>
<input type="hidden" value="<?php echo $cajas_totales ?>" name="cajas_totales">