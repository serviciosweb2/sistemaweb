<!--<script src="<?php echo base_url('assents/js/matriculas/frm_baja.js') ?>"></script>-->

<script>

    $(".fancybox-wrap").ready(function() {

        var claves = Array(
                "materias", "estado", "comision_origen", "comision_destino", "comision_presupuesto","BIEN","validacion_ok"
                );
        $.ajax({
            url: BASE_URL + 'entorno/getLang',
            data: "claves=" + JSON.stringify(claves),
            dataType: 'JSON',
            type: 'POST',
            cache: false,
            async: false,
            success: function(respuesta) {
                lang = respuesta;

            }
        });
        $('.chosen-select').chosen({
            width: "80%"
        });

        $("#btn-guardar").click(function() {
            $.ajax({
                url: BASE_URL + 'matriculas/cambiarModalidad',
                data: $("#frm-modalidad").serialize(),
                cache: false,
                type: 'POST',
                dataType: 'json',
                success: function(respuesta) {
                    if (respuesta.codigo === 1) {
                        $.fancybox.close();
                        $.gritter.add({
                            title: lang.BIEN,
                            text: lang.validacion_ok,
                            sticky: false,
                            time: '3000',
                            class_name: 'gritter-success'
                        });
                    }
                }
            });
            return false;

        });
               setComisionesDestinos(); 
    });
    
    function setComisionesDestinos() {
            var modalidad = $('select[name="modalidad"]').val();

            $.ajax({
                url: BASE_URL + 'matriculas/getDetalleMateriasCambioModalidad',
                type: 'POST',
                data: 'modalidad=' + modalidad + '&& codigo=' + $("#cod_matricula_periodo").val(),
                dataType: 'json',
                cache: false,
                success: function(respuesta) {
                    var tr = "<tr><th>" + lang.materias + "</th><th>" + lang.estado + "</th><th>" + lang.comision_origen + "</th><th>" + lang.comision_destino + "</th></tr>";
                    $.each(respuesta, function(key, value) {
                        tr += "<tr>";
                        tr += "<td>" + value.nombre;
                        tr += "<input name='cod_estado_academico[]' type='hidden' value='" + value.codestadoacademico + "' />";
                        tr += "</td>";
                        tr += "<td>" + value.estado + "</td>";
                        tr += "<td>" + value.nombreComision + "</td>";
                        tr += "<td>";
                        tr += "<select class='form-control chosen-select' name='comision_destino[]' data-placeholder='" + lang.comision_presupuesto + "'><option value='-1'></option>";
                        $.each(value.comisiones_destino, function(key2, value2) {
                            var cupo = '';
                            var descripcion = '';
                            if (value2.cupo <= 0) {
                                cupo = "";//"disabled='disabled'";
                            } 
                            tr += "<option value='" + value2.codigo + "'" + cupo + ">" + value2.nombre  + "</option>";
                        });
                        tr += "</select></td>";
                        tr += "</tr>";
                        $("#tablematerias").html(tr);
                        $(".chosen-select").chosen({
                            width: "80%"
                        });

                    });
                }
            });
        };

</script>

<div class="modal-content">
    <div class="modal-header">
        <h4 class="blue bigger"><?php echo lang('modificar_modalidad'); ?>
            <small>
                <i class="icon-double-angle-right"></i>
                <?php echo $nombreAlumno . ' - ' . $matricula_periodo; ?>
            </small>
        </h4>
    </div>

    <div class="modal-body">
        <form class="form" id="frm-modalidad" role="form">
            <div class="row">
                <div class="col-md-12">
                    <input name="cod_matricula_periodo"  id="cod_matricula_periodo" type="hidden" value="<?php echo $codigo ?>"/>

                    <div class="form-group">
                        <label class=" control-label" for="modalidades"><?php echo lang('modalidad'); ?></label>
                        <div>
                            <select class="width-40 chosen-default" id="modalidad" name="modalidad" data-placeholder="" onchange="setComisionesDestinos()">
                                <?php
                                foreach ($modalidades as $row) 
                                {
                                    $selected = '';
                                    if($row['modalidad'] == $modalidadActual) $selected = "selected";
                                    echo " <option value='" . $row["codigo"] . "' $selected>" . $row["modalidad"] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                </div>
            </div>


            <div class="row">
                <?php if (count($materias) > 0) { ?>
                    <table class="table table-striped table-bordered table-hover table-responsive" id="tablematerias" >

                        <tr><th><?php echo lang('materias'); ?></th><th><?php echo lang('estado'); ?></th><th><?php echo lang('comision_origen'); ?></th><th><?php echo lang('comision_destino'); ?></th></tr> 
                        <?php foreach ($materias as $materia) { ?>

                            <tr>
                                <td><?php echo $materia["nombre"]; ?>                     
                                    <input name="cod_estado_academico[]" type="hidden" value="<?php echo isset($materia["codestadoacademico"]) ? $materia["codestadoacademico"] : ""; ?>" />
                                </td>
                                <?php
                                echo "<td>" . $materia["estado"] . "</td>";
                                ?>
                                <td> 
                                    <?php echo $materia['nombreComision']; ?>
                                </td> 
                                <td>

                                    <select class="form-control chosen-select" name="comision_destino[]" data-placeholder="<?php echo lang('comision_presupuesto') ?>">
                                        <option value="-1"></option>
                                        <?php
                                        foreach ($materia["comisiones_destino"] as $comision) {

                                            $cupo = $comision["habilita"] == '0'? "disabled='disabled'" : "";
                                            //$descripcion = $comision["cupo"] <= 0 ? "Sin cupo" : $comision["cupo"];
                                            echo "<option value='" . $comision["codigo"] . "'" . $cupo . ">" . $comision["nombre"] .  "</option>";
                                        }
                                        ?>

                                    </select>

                                </td>

                            </tr>
                        <?php } ?>
                    </table>

                <?php } ?>

            </div>

        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-success"  id="btn-guardar" type="submit">
            <i class="fa fa-arrow-downbigger-110"></i>
            <?php echo lang('guardar'); ?>
        </button>
    </div>
</div>