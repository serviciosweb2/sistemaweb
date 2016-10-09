<center>
    <div class="modal-header">
        <h4 class="blue bigger"><?php echo lang("seleccion_de_la_impresion") ?></h4>
    </div>
    <table style="margin: 20px 35px 0px 35px;">
        <tr>
            <td><?php echo lang("seleccione_impresora"); ?></td>
        </tr>
        <tr>
            <td>
                <select name="impresora_imprimir" id="impresora_imprimir" class="chosen-select" style="width: 320px;">
                    <option value="-1" <?php if ($printer_default == '' || $printer_default == -1){ ?> selected="true" <?php } ?>>
                        <?php echo lang("Imprimir_por_el_navegador"); ?>
                    </option>
                    <?php foreach ($arrImpresoras as $impresora){ ?> 
                    <option value="<?php echo $impresora['printer_id'] ?>"
                            <?php if ($impresora['printer_id'] == $printer_default){ ?> selected="true" <?php } ?>>
                        <?php echo $impresora['display'] ?>
                    </option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <table style="width: 100%">
                    <tr>
                        <td>
                            <?php echo lang("cantidad_de_copias"); ?>&nbsp;
                        </td>
                        <td>
                            <select id="impresion_cantidad_copias" class="form-control" style="width: 68px; float: right;">
                                <?php for ($i = 1; $i <= 5; $i++){ ?> 
                                <option value="<?php echo $i ?>"
                                        <?php if (isset($cantidad_copias) && $cantidad_copias == $i){ ?> selected="true" <?php } ?>>
                                    <?php echo $i; ?>
                                </option>    
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <?php if ($id_script_inicio == 8){ ?>
        <tr>
            <td>
                <label>
                    <input id="imprimir_notas_parciales" class="ace ace-checkbox-2" type="checkbox" name="imprimir_notas_parciales">
                    <span class="lbl" style="font-size: 12px;"><?php echo lang("imprimir_notas_parciales"); ?></span>                        
                </label>
            </td>
        </tr>
        <tr>
            <td>
                <label>
                    <input id="imprimir_estado_deuda" class="ace ace-checkbox-2" type="checkbox" name="imprimir_estado_deuda">
                    <span class="lbl" style="font-size: 12px;"><?php echo lang("imprimir_estado_deuda"); ?></span>
                </label>
            </td>
        </tr>
        <?php } ?>
        
        <?php if ($id_script_inicio == 5){ ?>
        <tr>
            <td><hr></td>
        </tr>
        <?php if (isset($param) && is_array($param) && isset($param[3]) && $param[3] == 'imprimir_recibo_cobro_matricula'){ ?>
        <tr>
            <td>
                <label>
                    <input id="imprimir_recibo_cobro" class="ace ace-checkbox-2" type="checkbox" checked="true" name="imprimir_recibo_cobro">
                    <span class="lbl" style="font-size: 12px;"><?php echo lang("imprimir_recibo"); ?></span>
                </label>
            </td>
        </tr>
        <?php } ?>
        <tr>
            <td>
                <label>
                    <input id="imprimir_matricula" class="ace ace-checkbox-2" type="checkbox" checked="true" name="imprimir_matricula">
                    <span class="lbl" style="font-size: 12px;"><?php echo lang("imprimir_matricula"); ?></span>                        
                </label>
            </td>
        </tr>
        <?php foreach ($reglamentos as $reglamento) { ?>     
        <tr>
            <td>
                <label>
                    <input id="imprimir_reglamento" value = "<?php echo $reglamento['id']; ?>" class="ace ace-checkbox-2" type="checkbox" checked="true" name="imprimir_reglamento[]">
                    <span class="lbl" style="font-size: 12px;"><?php echo lang("imprimir"). ' ' .lang($reglamento['nombre']); ?></span>                        
                </label>
            </td>
        </tr>
        <?php } ?>
        <!--
        <tr>
            <td>
                <label>
                    <input id="imprimir_observaciones" class="ace ace-checkbox-2" type="checkbox" checked="true" name="imprimir_observaciones">
                    <span class="lbl" style="font-size: 12px;"><?php echo lang("imprimir_observaciones"); ?></span>                        
                </label>
            </td>
        </tr>
        -->
        <?php if (isset($param[3]) && $param[3] == "reimprimir"){ ?>
        <!--
        <tr>
            <td>
                <label>
                    <input id="imprimir_resumen_cuenta" class="ace ace-checkbox-2" type="hidden" checked="false" name="imprimir_resumen_cuenta">
                    <span class="lbl" style="font-size: 12px;"><?php echo lang("imprimir_resumen_de_cuenta_corriente"); ?></span>
                </label>
            </td>
        </tr>
        -->
        <?php } 
        } ?>
        <tr>
            <td><hr></td>
        </tr>
        <?php if ($id_script_inicio == 9){ ?>        
        <tr>
            <td>
                <div class="row">
                    <div class="col-md-8">
                    <?php echo lang("_como_lo_desea_imprimir_"); ?>
                    </div>
                    <div class="col-md-4">
                        <select class="from-control" style="width: 100%;" name="filtrar" placeholder="Seleccione" onchange="mostrarFiltrosAsistencia(this);">
                            <option value="1" name="todas"><?php echo lang("todas"); ?></option>
                            <option value="2" name="filtrar_por"><?php echo lang("filtrar_por"); ?></option>
                        </select>
                    </div>
                </div>
                <div id="contenedor_fechas" class="hide">
                    <div class="row">
                        <div class="col-md-6">
                            <?php echo lang("fecha_desde") ?>
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="fecha_desde" value="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?php echo lang("fecha_hasta"); ?>
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="fecha_hasta" value="">
                        </div>
                    </div>
                </div>
            </td>
        </tr>
        <?php } ?>
        <tr>
            <td><hr></td>
        </tr>
    </table>
    <div id="area_mensajes" value="" style="height: 20px;"></div>
    <div class="modal-footer">
        <table style="float: right; margin-right: 16px;">
            <tr>
                <td>
                    <button id="btnImprimir" class="btn btn-success btn-save" data-last="Finish" onclick="seleccionImpresionImprimir();"><?php echo lang("imprimir"); ?></button>
                </td>
            </tr>
        </table>
    </div>
</center>
<?php 
if (is_array($param)){

    if (is_array($param[0]) && array_key_exists("custom", $param[0])){
        $valorHidden = $param[0]['custom'];
    } else if (is_array($param[0]) && array_key_exists("bajas", $param[0])){
        $valorHidden = $param[0]['bajas'][0];
    } else if(is_array($param[0])){// en este if cae la impresion de estado academico
        $valorHidden = json_encode($param[0]);
    }else if(in_array('reimprimir',$param)){// reimprimir matricula
        $valorHidden = json_encode($param);
    }else if($id_script_inicio != 16) {// impresiones donde la posicion $param[0] no es un array , ejemplo "imprimir matricula"
        $valorHidden = $param[0];
    } else {
        $valorHidden = json_encode($param);
    }
} else {
    $valorHidden = $param;
} ?>

<input type='hidden' id='param0' value='<?php echo $valorHidden ?>'>
<input type="hidden" value="<?php echo $id_script_inicio ?>" id="id_script_inicio">
<script>
    $(".chosen-select").chosen();
</script>
