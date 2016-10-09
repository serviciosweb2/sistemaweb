<script>
    var diccionario = {};
    diccionario.totalCurso = '<?php echo lang("total"); ?> ';
    diccionario.cursoCorto = '<?php echo lang("cursos_cortos")?>'
</script>
<script src="<?php echo base_url('assents/js/reportes/cupos_abiertos_curso.js'); ?>"></script>
<style>
    .row_total_categoria td{
        font-weight: bold;
        font-size: 12px;
        background-color: #ccc !important;
    }

    .row_total_categoria_corta td{
        font-size: 12px;
        background-color: #ddd !important;
    }


    .row_header_categoria td{
        font-weight: bold;
        font-size: 14px;
        background-color: #fff !important;
    }

    .row_header_categoria_corta td{
        font-weight: bold;
        font-size: 12px;
        background-color: #eee !important;
    }
</style>
<div class="col-md-12">
    <center>
        <table style='margin-bottom: 18px;'>
            <tr>
                <td><?php echo lang("curso"); ?></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td style='padding-right: 16px;'>
                    <select name="filtro_curso" class='select_chosen' onchange='listar();' style='width: 130px;'>
                        <option value=''>
                            Todos
                        </option>
                        <?php foreach ($cursos as  $curso){ ?> 
                        <option value='<?php echo str_pad($curso['id'], 2, "0", STR_PAD_LEFT) ?>'>
                            <?php echo $curso['nombre']; ?>
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
        <table name="table_reporte_cupos_abiertos_por_curso" style="width: 566px;" class="table table-striped table-condensed table-bordered dataTable no-footer">
            <thead>
                <tr>
                    <th style="text-align: center;"><?php echo lang("comision") ?></th>
                    <th style="text-align: center;"><?php echo lang("cupo_disponible") ?></th>
                    <th style="text-align: center;"><?php echo lang("cierre_inscripcion") ?></th>
                </tr>
            </thead>
            <tbody></tbody>
            <tfoot>
                <tr style="font-weight: bold;">
                    <td />
                    <td style="text-align: right; padding-right: 14px;" colspan=2><?php echo lang("total"); ?></td>
                    <td style="text-align: center;" name="total_cupos">0</td>
                </tr>
            </tfoot>
        </table>
    </center>
</div>
<form name='form_exportar' method="POST" action='<?php echo base_url()."reportes/listar_cupos_abiertos_curso" ?>' target="new-target"> 
   <input type='hidden' name='curso' value='' id='hiddenInputCurso'>
    <input type='hidden' name='accion' value='exportar'>
    <input type='hidden' name='tipo_reporte' value=''>
</form>
