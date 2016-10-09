<div class="modal-content">


    <div class="modal-header">
        <h3 class="blue bigger"><?php echo lang('materias'); ?>
            <small><i class="icon-double-angle-right"></i>
                <?php echo $nombreCurso ?>
            </small></h3> 
    </div>

    <div class="modal-body overflow-visible">

        <div class="tabbable tabs-left">
            <ul class="nav nav-tabs" id="titulos">
                <?php
                $i = 0;
                foreach ($plan as $key => $datosplan) {
                    $active = $i == 0 ? 'active' : '';
                    ?>
                    <li class="<?php echo $active; ?>">
                        <a data-toggle="tab" href="<?php echo '#' . $plan[$key]['codigo'] ?>">
                            <?php
                            $estado = $plan[$key]['estado'] != 'habilitado' ? '</br><span class="red">' . lang(strtoupper($plan[$key]['estado'])) . '</span>' : ' ';
                            echo $plan[$key]['nombre'] . $estado;
                            ?>  
                        </a>
                    </li>

                    <?php
                    $i = $i + 1;
                }
                ?> 
            </ul>
            <div class="tab-content">
                <?php
                $i = 0;
                foreach ($plan as $key => $datosplan) {
                    $active = $i == 0 ? 'in active' : '';
                    ?>  

                    <div id="<?php echo $plan[$key]['codigo'] ?>" class="tab-pane <?php echo $active; ?>">

    <?php foreach ($plan[$key]['periodos'] as $k => $periodo) { ?>  
                            <label>  <?php echo lang($k) . '| ' . lang('horas_catedra') . ': ' . $periodo['periodo']['hs_catedra'] ?> </label>
                            <table class="table table-condensed table-bordered table-striped" id="listadoCursos">
                                <thead>
                                    <tr>
                                        <th><?php echo lang('materia'); ?></th><th><?php echo lang('descripcion'); ?></th>
                                    </tr>
                                </thead>


                                <?php
                                $nombreIdioma = 'nombre_' . get_Idioma();

                                foreach ($periodo["materias"] as $materia) {
                                    echo '<tr>';
                                    echo '<td>' . $materia[$nombreIdioma] . '</td>';
                                    echo '<td>' . $materia['cod_tipo_materia'] . '</td>';
                                    echo '</tr>';
                                }
                                ?>


                            </table>


                            <table class="table table-condensed table-bordered table-striped" id="modalidadCursado">
                                <thead>
                                    <tr>
                                        <th><?php echo lang('modalidad'); ?></th>
                                        <th><?php echo lang('periodo'); ?></th>
                                        <th><?php echo lang('titulo'); ?></th>
<!--                                        <th><?php echo lang('estado'); ?></th>-->
                                    </tr>
                                </thead>


                                <?php
                                $nombreIdioma = 'nombre_' . get_Idioma();

                                foreach ($periodo['periodo']['modalidades'] as $modalidad) {
                                    $titulo = $modalidad['titulo'] == ' ' || $modalidad['titulo'] == null ? $periodo['periodo']['titulo'] : $modalidad['titulo'];
                                    $nbrperiodo = $modalidad['nombre_periodo'] == ' ' || $modalidad['nombre_periodo'] == null ? lang($k) : lang($modalidad['nombre_periodo']);
                                    echo '<tr>';
                                    echo '<td>' . lang($modalidad['modalidad']) . '</td>';
                                    echo '<td>' . $nbrperiodo . '</td>';
                                    echo '<td>' . $titulo . '</td>';
                                    //echo '<td>' . lang(strtoupper($modalidad['estado'])) . '</td>';
                                    echo '</tr>';
                                }
                                ?>


                            </table>

                    <?php } ?>    

                    </div>
                    <?php
                    $i = $i + 1;
                }
                ?> 
            </div>
        </div>


    </div>
</div>

