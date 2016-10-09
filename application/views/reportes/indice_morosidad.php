<style>
    #gritter-notice-wrapper{
        z-index: 90000  !Important;
    }

    table .texto {
        /*position: absolute !important;*/
        display: block !important;
    }

    table .test {
        position: absolute !important;
        z-index: 30 !important;
        display: none;
    }

    .popover{
        max-width: none!important;
    }

    .table_filter{
        -moz-border-bottom-colors: none;
        -moz-border-left-colors: none;
        -moz-border-right-colors: none;
        -moz-border-top-colors: none;
        background-color: #ffffff;
        border-bottom: 1px solid #afafb6;
        border-image: none;
        border-radius: 4px;
        border-top: 1px solid #afafb6;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
        cursor: default;
        padding: 8px 0;
        position: absolute;
        right: 20px;
        text-align: left;
        top: 18px;
        z-index: 1000;
    }
</style>

<?php $años = array(2016, 2015, 2014);?>

<div class="col-md-12">
    <center>
        <table style='margin-bottom: 18px;'>
            <tr>
                <td><?php echo lang("año"); ?></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td style='padding-right: 16px;'>
                    <select name="filtro_año" class='select_chosen' onchange='seleccionar_periodo();' style='width: 130px;'>
                        <?php foreach ($años as $año){?>
                            <option value=<?php echo $año;?>><?php echo $año;?></option>
                        <?php }?>
                    </select>
                </td>
                <td style='padding-left: 10px;'>
                    <i id="imprimir_informe" class="icon-print grey" onclick="exportar('pdf');" style="cursor: pointer" data-original-title="" title=""></i>
                    &nbsp;
                    <i id="exportar_informe" class="icon-external-link" onclick="exportar('csv');" style="cursor: pointer" data-original-title="" title=""></i>
                </td>
            </tr>
        </table>
    </center>
</div>

<div class="col-md-12" name="area_reporte">
    <center>

        <table name="table_reporte_indice_morosidad" style="width: 820px;"  class="table table-striped table-condensed table-bordered dataTable no-footer">
            <thead>
            <tr>
                <th style="text-align: center;"><?php echo lang("mes") ?></th>
                <th style="text-align: center;"><?php echo lang("importe") ?></th>
                <th style="text-align: center;"><?php echo lang("imputado") ?></th>
                <th style="text-align: center;"><?php echo lang("saldo") ?></th>
                <th style="text-align: center;"><?php echo lang("morosidad") ?></th>
                <th style="text-align: center;"><strong><?php echo lang("imputado_total") ?></strong></th>
                <th style="text-align: center;"><strong><?php echo lang("morosidad_total") ?></strong></th>
            </tr>
            </thead>
            <tbody></tbody>
            <tfoot>
            <!--<tr style="font-weight: bold;">
                <td />
                <td style="text-align: right; padding-right: 14px;" colspan=2><?php echo lang("total"); ?></td>
                <td style="text-align: center;" name="total_cupos">0</td>
            </tr>-->
            </tfoot>
        </table>
    </center>
</div>

<form name='form_exportar' method="POST" action='<?php echo base_url()."reportes/exportar_indice_morosidad" ?>' target="new-target">
    <input type='hidden' name='accion' value='exportar'>
    <input type='hidden' name='year' value='year'>
    <input type='hidden' name='tipo_reporte' value=''>
</form>

<script src="<?php echo base_url('assents/js/reportes/indice_morosidad.js'); ?>"></script>