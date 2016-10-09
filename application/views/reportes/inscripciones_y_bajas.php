<script src="<?php echo base_url('assents/js/reportes/inscripciones_y_bajas.js'); ?>"></script>
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
                <td><?php echo lang("fecha_desde"); ?></td>
                <td><?php echo lang("fecha_hasta"); ?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td style='padding-right: 16px;'>
                    <div class="input-group ">
                        <input type="text" class="date_picker form-control" name="filtro_fecha_desde" style="width: 100px;" value="<?php echo date("d/m/Y"); ?>">
                        <span class="input-group-addon calendario-financiacion-5">
                            <i class="icon-calendar bigger-110"></i>
                        </span>
                    </div>
                </td>
                <td style="padding-right: 16px;">
                    <div class="input-group ">
                        <input type="text" class="date_picker form-control" name="filtro_fecha_hasta" style="width: 100px;" value="<?php echo date("d/m/Y"); ?>">
                        <span class="input-group-addon calendario-financiacion-5">
                            <i class="icon-calendar bigger-110"></i>
                        </span>
                    </div>
                </td>
                <td>
                    <button class="btn btn-sm btn-success" type="button" name="btnBuscar" onclick="listar();">
                        <?php echo lang("buscar"); ?>
                    </button>
                </td>
                <td style="padding-left: 16px;">
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
        <table name="table_reporte_inscripciones_y_bajas" style="width: 566px;" class="table table-striped table-condensed table-bordered dataTable no-footer">
            <thead>
                <tr>
                    <th style="text-align: center;"><?php echo lang("curso") ?></th>
                    <th style="text-align: center; width: 100px;"><?php echo lang("inscripciones") ?></th>
                    <th style="text-align: center; width: 100px;"><?php echo lang("bajas") ?></th>
                </tr>
            </thead>
            <tbody></tbody>
            <tfoot>
                <tr style="font-weight: bold;">
                    <td style="text-align: right; padding-right: 14px;"><?php echo lang("total"); ?></td>
                    <td style="text-align: center;" name="total_inscripciones">0</td>
                    <td style="text-align: center;" name="total_bajas">0</td>                    
                </tr>
            </tfoot>
        </table>
    </center>
</div>
<form name='form_exportar' method="POST" action='<?php echo base_url()."reportes/listado_inscripciones_y_bajas" ?>' target="new_target">
    <input type='hidden' name='fecha_desde' value=''>
    <input type='hidden' name='fecha_hasta' value=''>
    <input type='hidden' name='accion' value='exportar'>
    <input type='hidden' name='tipo_reporte' value=''>
</form>