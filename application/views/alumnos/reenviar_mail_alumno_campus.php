<script>
    var langFrm = <?php echo $langFrm?>;
</script>

<script src="<?php echo base_url('assents/js/alumnos/reenviar_mail_alumno_campus.js')?>"></script>
<div class="modal-content">
    <div class="modal-header">
            <h4 class="blue bigger"><?php echo lang('reenviar_mail_campus_alumno');?></h4>
    </div>

    <div class="modal-body overflow-visible">
        <div class="row">
            <div class="col-md-12">
                <center><label>Email:</label>
                <label><?php echo $objAlumno->email?></label></center>
            </div>

            <input id="cod_alumno" type="hidden" value="<?php echo $objAlumno->getCodigo();?>">
            <div id="password"></div><br></br>
            <div class="col-md-6"><center><button id="reenviar_mail" type="button" class="btn btn-sm btn-success"><?php echo lang('reenviar_mail');?></button></div>
            <div class="col-md-6"><center><button id="regenerar_password" type="button" class="btn btn-sm btn-success">Regenerar</button></div>

        </div>



       <!--<center><button id="ok" type="button" class="btn btn-sm btn-success" onclick="javascript:$.fancybox.close(true);"><?php echo lang('cerrar');?></button></center>!-->

            <!--div class="row">
                <div id="areaTablas">
                    <table id="detalle_mail_enviados" class='table table-bordered table-condensed table-responsive'>
                        <thead>
                            <th>Fecha Hora Envio</th>
                            <th>Estado</th>
                        </thead>
                        <tbody>
                            <?php
                                /*foreach($detalle_mails_enviados as $detalle){
                                    $clase = '';
                                    $estado='';
                                    switch ($detalle['estado']) {
                                        case 'cancelado':
                                            $clase = 'label label-info arrowed';
                                            $estado = lang('cancelado');
                                            break;
                                        case 'noenviado':
                                            $clase = 'label label-warning arrowed';
                                            $estado = lang('noenviado');
                                            break;
                                         case 'error':
                                            $clase = 'label label-danger arrowed';
                                              $estado = lang('error');
                                            break;

                                        default:
                                            $clase = 'label label-success arrowed';
                                             $estado = lang('enviado');
                                            break;
                                    }
                                    echo '<tr>';
                                    echo '<td>';
                                    echo $detalle['fecha_hora'];
                                    echo '</td>';
                                    echo '<td>';
                                    echo '<span class="'.$clase.'">'.$estado.'</span>';
                                    echo '</td>';
                                    echo '</tr>';
                                }
                            */?>
                        </tbody>
                    </table>
                </div-->


            </div>
        
    </div>

</div> 