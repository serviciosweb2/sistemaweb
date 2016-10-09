<script>
    var langFrm = <?php echo $langFrm ?>;
    var columnsFacturacionSerie = <?php echo $columnsFacturacionSerie?>;
</script>
<script src="<?php echo base_url('assents/js/facturacion/frm_facturar_serie.js') ?>"></script>
<div class="modal-content">
    <?php  if ($validaciones == ''){ ?>
    <form  id="frm-facturar-serie">
        <div class="modal-header">
            <h4 class="blue bigger"><?php echo lang("facturacion_serie"); ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="form-group col-md-6 col-xs-12">
                    <label><?php echo lang('facturante');?></label>
                    <select id="facturante-serie" name="facturante-serie"  class="form-control select_chosen" data-placeholder="<?php echo lang('seleccione_facturate');?>">
                        <option></option>
                        <?php foreach ($facturantes as $row) { ?>
                        <option value="<?php echo $row["codigo"] ?>">
                            <?php echo $row["razon_social"] ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-md-6 col-xs-12">
                    <label><?php echo lang('tipo_de_factura');?></label>
                     <select id="tipo-factura" name="tipo-factura"  class="form-control select_chosen" data-placeholder="<?php echo lang('seleccione_tipo_factura');?>"
                        <?php if ($pais == 2){ ?> multiple="true" <?php } ?>>
                     </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-xs-12">
                    <?php $tmpl=array ('table_open' =>
                        '<table id="facturacionSerie" style="width: 100%" cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered ">');
                        $this->table->set_template($tmpl);
                        $this->table->set_heading('','','','','','','','', '', '', '');
                        echo $this->table->generate(); ?>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button name="guardar_fact_series" class="btn btn-sm btn-success" type="submit">
                <i class="icon-ok"></i>
                <?php echo lang("facturar_seleccionadas"); ?>
            </button>
        </div>
    </form>
    <div name="container_menu_filters_temp">
        <div id="div_table_filters" class="table_filter" name="div_table_filters" style="display: none">
            <div class="row" style="padding-top: 0px; padding-bottom: 0px;">
                <div class="col-md-12">
                    <?php echo lang('fecha_desde')?>
                     <input class="form-control fecha_filtro_factura" value="<?php echo $fecha_inicio ?>" id="fecha-inicio" />
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?php echo lang('fecha_hasta')?>
                    <input class="form-control fecha_filtro_factura" value="<?php echo $fecha_fin ?>" id="fecha-fin" />
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?php echo lang("medio_pago"); ?>
                    <select id="medio-pago" class="form-control" style="margin-left: 0px; margin-right: 0px;">
                        <option value="-1">(<?php echo lang('todos'); ?>)</option>
                        <?php foreach($medios_pago as $medio){ ?>
                        <option value="<?php echo $medio['codigo'] ?>">
                            <?php echo $medio['medio']; ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="row" style="background-color: white; border: none; padding-bottom: 0px;">
                <div class="col-md-12" style="display: none;">
                    <?php echo lang('cobradas_nofacturadas');?>
                    <div class="col-md-6">
                        <input type="checkbox" id="facturadas_no_cobradas" value="" checked="true">
                    </div>
                </div>
                <div class="col-md-12">
                    <center>
                        <button type="button" id="filtrar" class="btn btn-detalle btn-sm" onclick="redibujar();"><?php echo lang('filtrar');?></button>
                    </center>
                </div>
            </div>
        </div>
        <div style="bottom: 0px; height: 100%; left: 0px; position: fixed; width: 100%; z-index: 20; display: none;" name="contenedorPrincipal"></div>
    </div>
    <?php } else { ?>
    <div class="error-container">
        <div class="well-sm">
            <h1 class="grey lighter smaller">
                <span class="blue bigger-125">
                    <i class="icon-random"></i>
                    <?php echo lang('configuracion'); ?>
                </span>
                <?php echo lang('accion_requerida'); ?>
            </h1>
            <hr>
            <h3 class="lighter smaller">
                <?php echo lang('ocurrieron_errores_configuracion'); ?>
                <i class="icon-wrench icon-animated-wrench bigger-125"></i>
            </h3>
            <h6 class="lighter smaller">
                <?php echo $validaciones ?>
            </h6>
            <div class="space"></div>
            <div>
                <h4 class="lighter smaller"><?php  echo lang('necesita_ayuda');?></h4>
                <ul class="list-unstyled spaced inline bigger-110 margin-15">
                    <li>
                        <i class="icon-hand-right blue"></i>
                        <span class="blue bigger-125"><?php echo lang('contactese_con_casa_central');?></span>
                    </li>
                </ul>
            </div>
            <hr>
        </div>
    </div>
    <?php } ?>
</div>
<style>
    #ui-datepicker-div{
        z-index: 10010 !important;
    }
</style>