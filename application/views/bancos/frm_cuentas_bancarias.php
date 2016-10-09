<script src="<?php echo base_url('assents/js/configuracion/frm_cuentas_bancarias.js')?>"></script>
<?php
$arrsoloNumeros = array("agencia", "conta", "formatacao_convenio", "contrato", "formatacao_nosso_numero", "digito_cuenta",
        "digito_agencia", "numero_secuencia", "cantidad_copias", "convenio", "carteira", "variacao_carteira");
?>
<div class="modal-content" name="frm_cuentas_bancarias">
    <div class="modal-header">

        <h4 class="blue bigger">
            <?php if (isset($registro_nuevo) && $registro_nuevo){ 
                echo lang('nueva_cuenta_bancaria');
                } else {
                    echo lang('modificar_cuenta_bancaria');
                } ?>
        </h4>
    </div>
    <div class="modal-body overflow-visible" style="margin-right: 16px;">
        <div class="row">
            <div class="col-md-12 col-xs-12">
                <?php foreach ($cuenta as $codBanco => $cuentaBancaria){ ?>
                    <?php if (isset($registro_nuevo) && $registro_nuevo){ ?> 
                    <div class="row" style="margin-bottom: 10px;">
                        <div class="col-md-3 col-xs-12" style="text-align: right;">
                            <?php echo lang("entidad_bancaria"); ?>
                        </div>
                        <div class="col-md-9 col-xs-12">
                            <select name="codigo_banco" style="width: 350px;">
                                <?php foreach ($bancos as $banco){ ?> 
                                <option value="<?php echo $banco['codigo'] ?>">
                                    <?php echo $banco['nombre'] ?>
                                </option>    
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <?php } else { ?>
                    <input type="hidden" value="<?php echo $codBanco ?>" name="codigo_banco">

                    <div class="row">
                        <div class="col-md-12 col-xs-12">
                            <h4>
                            <?php echo $cuentaBancaria['nombre'] ?>
                            </h4>
                        </div>
                    </div>

                    <?php } ?>  
                </div>
            </div>
            <div id="div_area_cuenta_bancaria" class="row">
                <div class="col-md-12 col-xs-12">
                <?php foreach ($cuentaBancaria['cuentas'] as $codigoCuenta => $cuentaDetalle){
                    ?> <input type="hidden" value="<?php echo $codigoCuenta ?>" name="codigo_cuenta"> <?php
                    include 'vista_banco_do_brasil.php';
                    $arrBoletoBancario = $cuentaDetalle['boletos_bancarios'][0];                    
                }
            } ?>      
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-xs-12">
                <div class="row" style="margin-bottom: 8px;">
                    <div class="col-md-12 col-xs-12">
                        <hr>
                        <h5><?php echo lang("boleto_bancario"); ?></h5>
                    </div>
                </div>
                <div id="div_area_boleto_bancario">
                    <?php $col = 0; ?> 
                    <div class="row" style="margin-bottom: 4px;">                        
                    <?php foreach ($arrBoletoBancario as $key => $value){ ?> 
                            <?php if ($key <> 'cod_banco' && $key <> 'cod_cuenta'){ ?>
                        <div class="col-md-3 col-xs-12" style="text-align: right;">
                            <?php echo lang($key); ?>
                        </div>
                        <div class="col-md-3 col-xs-12">
                            <input type="text" name="boleto_valores" id="<?php echo $key ?>" value="<?php echo $value ?>"
                                   <?php if (($key == 'carteira' || $key == 'convenio') && (!isset($registro_nuevo) || $registro_nuevo == false )){ ?> disabled="true" <?php } ?>
                                   <?php if (in_array($key, $arrsoloNumeros)){ ?> onkeypress="return ingresarNumero(this, event);"<?php } ?>>
                        </div>                          
                             <?php $col++;
                            if ($col == 2){
                                $col = 0;
                                ?> </div><div class="row" style="margin-bottom: 4px;"> <?php 
                            }
                        }
                    }
                    if ($col < 2) { ?> </div> <?php } ?>
                </div>
            </div>
        </div>        
    </div>
    <div class="modal-footer">
        <button class="btn btn-sm btn-primary" name="btn_guardar">
            <i class="icon-ok"></i>
            <?php echo lang('guardar')?>
        </button>
    </div>    
</div>