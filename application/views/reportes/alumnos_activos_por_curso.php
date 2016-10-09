<script src="<?php echo base_url('assents/js/reportes/alumnos_activos_por_curso.js'); ?>"></script>
<style>
    .row_total_categoria td{
        font-weight: bold;
        font-size: 14px;
        background-color: #ddd !important;
    }
</style>
<div class="col-md-12">
    <center>
        <table style='margin-bottom: 18px;'>
            <tr>
                <td><?php echo lang("mes"); ?></td>
                <td><?php echo lang("año"); ?></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td style='padding-right: 16px;'>
                    <select name="filtro_mes" class='select_chosen' onchange='listar();' style='width: 130px;'>
                        <?php foreach ($meses as $nro => $mes){ ?> 
                        <option value='<?php echo str_pad($nro, 2, "0", STR_PAD_LEFT) ?>'
                                <?php if ($nro == date("m")){ ?>selected="true"<?php } ?>>
                            <?php echo $mes; ?>
                        </option>
                        <?php } ?>
                    </select>
                </td>
                <td>
                    <select name='filtro_anio' class='select_chosen' onchange="listar();" style='width: 82px;'>
                        <?php for ($i = 2010; $i <= date("Y"); $i++){ ?> 
                        <option value='<?php echo $i ?>' 
                                <?php if ($i == date("Y")){ ?>selected="true"<?php } ?>>
                            <?php echo $i; ?>
                        </option>    
                        <?php } ?>
                    </select>
                </td>
                <td style='padding-left: 10px;'>
                    <i id="imprimir_informe" class="icon-print grey" onclick="exportarReporte('pdf');" style="cursor: pointer" data-original-title="" title=""></i>
                    &nbsp;
                    <i id="exportar_informe" class="icon-external-link" onclick="exportarReporte('csv');" style="cursor: pointer" data-original-title="" title=""></i>
                </td>
            </tr>
        </table>
    </center>
</div>
<div class="col-md-12" name="area_reporte">
    <center>
        <table name="table_reporte_alumnos_activos_por_curso" style="width: 566px;" class="table table-striped table-condensed table-bordered dataTable no-footer">
            <thead>
                <tr>
                    <th style="text-align: center;"><?php echo lang("curso") ?></th>
                    <th style="text-align: center;"><?php echo lang("alumnos_activos") ?></th>
                </tr>
            </thead>
            <tbody></tbody>
            <tfoot>
                <tr style="font-weight: bold;">
                    <td style="text-align: right; padding-right: 14px;"><?php echo lang("total"); ?></td>
                    <td style="text-align: center;" name="total_activos">0</td>
                </tr>
            </tfoot>
        </table>
    </center>
</div>
<form name='form_exportar' method="POST" action='<?php echo base_url()."reportes/listar_alumnos_activos_curso" ?>' target="new_target">
    <input type='hidden' name='mes' value=''>
    <input type='hidden' name='anio' value=''>
    <input type='hidden' name='accion' value='exportar'>
    <input type='hidden' name='tipo_reporte' value=''>
</form>