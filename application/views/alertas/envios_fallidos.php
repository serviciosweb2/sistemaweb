<script src="<?php echo base_url('assents/js/alertas/envios_fallidos.js');?>"></script>
<div class="col-md-12 col-xs-12">
    <div class="row">
        <button name="eliminar_alertas" class="btn btn-sm btn-danger" onclick="eliminarAlertas()">
            Dar de baja Alertas
        </button>
    </div>
    <div id="areaTablas">
        
        <table id="envios_alertas_fallidos" class="table table-striped table-condensed  table-bordered" width="100%" style="width: 100%;" oncontextmenu="return false" onkeydown="return false">
            <thead>
                <tr class="success" role="row">
                    <th><label class="inline middle">
                                                        <input type="checkbox" name="seleccionar_todos" class="ace id-toggle-all" onclick="seleccionarTodos(this)">
                                                        <span class="lbl"></span>
                                                    </label>
                                                    &nbsp;
                                                    <div class="inline position-relative">
                                                        <a href="#" data-toggle="dropdown" class="dropdown-toggle">
                                                            <i class="icon-caret-down bigger-125 middle"></i>
                                                        </a>
                                                        <ul class="dropdown-menu dropdown-lighter dropdown-100">
                                                            <li>
                                                                <a class="id-select-message-all" onclick="checkTodos();">Todos</a>
                                                            </li>
                                                            <li>
                                                                <a class="id-select-message-none" onclick="desCheckTodos();">Ninguno</a>
                                                            </li>
                                                          
                                                        </ul>
                                                    </div>
                                               </th>
                    <th>Tipo Alerta</th>
                    <th>fecha</th>
                    <th>Alumnos</th>
                </tr>
            </thead>
          
            <?php foreach ($registros as $registro){ 
                $datos = json_encode(array(
                    "cod_alumno"=>$registro['cod_alumno'],
                    "cod_alerta"=>$registro['codigo']
                ));
                ?>
                
            <tr>
                <td><label class="inline middle"><input name="eliminar_alerta[]" class="ace id-toggle-all" type="checkbox" value='<?php echo $datos?>' onclick="descheckear(this);">
                        <span class="lbl"></span></label></td>
                <td><?php echo lang($registro['tipo_alerta']) ?></td>
                <td><?php echo formatearFecha_pais($registro['fecha_hora'], true) ?></td>
                <td><?php echo $registro['nombre_alumno'] ?></td>
                
            </tr>
            <?php } ?>
        </table>
           
    </div>
</div>