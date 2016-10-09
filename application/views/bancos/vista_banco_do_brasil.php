<input type="hidden" name="entidad_bancaria" name="banco_do_brasil">
<div class="row" style="margin-bottom: 4px;">
    <div class="col-md-3 col-xs-12" style="text-align: right;">
        <?php echo lang("agencia"); ?>
    </div>
    <div class="col-md-3 col-xs-12">
        <input type="text" name="cuenta_valores" id="agencia" value="<?php echo isset($cuentaDetalle['agencia']) ? $cuentaDetalle['agencia'] : '' ?>" onkeypress="return ingresarNumero(this, event);">
    </div>

    <div class="col-md-3 col-xs-12" style="text-align: right;">
        <?php echo lang("conta"); ?>
    </div>
    <div class="col-md-3 col-xs-12">
        <input type="text" name="cuenta_valores" id="conta" value="<?php echo isset($cuentaDetalle['conta']) ? $cuentaDetalle['conta'] : '' ?>" onkeypress="return ingresarNumero(this, event);">
    </div>
</div>

<div class="row" style="margin-bottom: 4px;">
    <div class="col-md-3 col-xs-12" style="text-align: right;">
        <?php echo lang("contrato"); ?>
    </div>
    <div class="col-md-3 col-xs-12">
        <input type="text" name="cuenta_valores" id="contrato" value="<?php echo isset($cuentaDetalle['contrato']) ? $cuentaDetalle['contrato'] : '' ?>" onkeypress="return ingresarNumero(this, event);">
    </div>
    <div class="col-md-3 col-xs-12" style="text-align: right;">
        <?php echo lang("formatacao_convenio"); ?>
    </div>
    <div class="col-md-3 col-xs-12">
        
           <select name="cuenta_valores" id="formatacao_convenio">
            
            <option value="6" <?php echo $cuentaDetalle['formatacao_convenio'] == "6" ? "selected" : '' ?> >6</option>
            <option value="7" <?php echo $cuentaDetalle['formatacao_convenio'] == "7" ? "selected" : '' ?>  >7</option>
            
        </select>
        
   
        
        
             <!--<input type="text" name="cuenta_valores" id="formatacao_convenio" value="<?php echo isset($cuentaDetalle['formatacao_convenio']) ? $cuentaDetalle['formatacao_convenio'] : ''?>">-->
    </div>
</div>

<div class="row" style="margin-bottom: 4px;">
    <div class="col-md-3 col-xs-12" style="text-align: right;">
        <?php echo lang("formatacao_nosso_numero"); ?>
    </div>
    <div class="col-md-3 col-xs-12">
        <select name="cuenta_valores" id="formatacao_nosso_numero">
            
            <option value="1" <?php echo $cuentaDetalle['formatacao_nosso_numero'] == "1" ? "selected" : '' ?> >1</option>
            <option value="2" <?php echo $cuentaDetalle['formatacao_nosso_numero'] == "2" ? "selected" : '' ?>  >2</option>
            
        </select>
        
        
        
        <!--<input type="text" name="cuenta_valores" id="formatacao_nosso_numero" value="<?php echo isset($cuentaDetalle['formatacao_nosso_numero']) ? $cuentaDetalle['formatacao_nosso_numero'] : '' ?>">-->
    </div>
    <div class="col-md-3 col-xs-12" style="text-align: right;">
        <?php echo lang("identificacao"); ?>
    </div>
    <div class="col-md-3 col-xs-12">
        <input type="text" name="cuenta_valores" id="identificacao" value="<?php echo isset($cuentaDetalle['identificacao']) ? $cuentaDetalle['identificacao'] : '' ?>">
    </div>
</div>

<div class="row" style="margin-bottom: 4px;">
    <div class="col-md-3 col-xs-12" style="text-align: right;">
        <?php echo lang("cod_razon_social"); ?>
    </div>
    <div class="col-md-3 col-xs-12">
        <select name="cuenta_valores" id="cod_razon_social" style="width: 170px;">
            <?php foreach ($razones_sociales as $razon_social){ ?> 
            <option value="<?php echo $razon_social['codigofacturante'] ?>"
                    <?php if (isset($cuentaDetalle['cod_razon_social']) && $cuentaDetalle['cod_razon_social'] == $razon_social['codigo']){ ?> selected="true" <?php } ?>>
                <?php echo $razon_social['razon_social'] ?>
            </option>
            <?php } ?>
        </select>
    </div>
 
</div>
