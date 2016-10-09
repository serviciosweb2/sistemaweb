<script src="<?php echo base_url('assents/js/resumendecuenta/frm_detalleDeudor.js')?>"></script>
<?php 
//echo '<pre>'; 
//echo print_r($deudoresCtaCte);
//echo '</pre>';

function getBody($registros){
    $retorno='';
    foreach ($registros as $registro){
 $texto='supero el maximo de alertas';
 $claseEstado='label-danger';
 $disabled='disabled';
 
 
 
 if ($registro['alertar']== 1){
        $texto='enviar alerta';
        $claseEstado='label-success';
        $disabled='';
 }
 $valores= json_encode($registro);
echo <<<eot
<tr>
    <td><div class="checkbox"><label><input name="ctacte[]" class="ace ace-checkbox-2" type="checkbox" value='$valores' $disabled><span class="lbl"></span></label></div></td>
    <td>$registro[descripcion]</td>
    <td>$registro[importe]</td>
    <td>$registro[saldoformateado]</td>
    <td>$registro[fechavenc]</td>
    <td>$texto</td>
</tr>
eot;
 
// $retorno.='<tr>';
// $retorno.='<td><div class="checkbox"><label><input name="ctacte[]" class="ace ace-checkbox-2" type="checkbox" value="'.$valores.'" '.$disabled.'><span class="lbl"></span></label></div></td>';
// $retorno.='<td>'.$registro["descripcion"].'</td>';
// $retorno.='<td>'.$registro["importe"].'</td>'  ;
// $retorno.='<td>'.$registro["saldoformateado"].'</td>';
// $retorno.='<td>'.$registro["fechavenc"].'</td>';
// $retorno.='<td>'.$texto.'</td>';
// $retorno.='</tr>';

        
    }
    //echo $retorno;
}

?>

<div class="modal-content">
                <div class="modal-header">
                        <!--<button type="button" class="close" data-dismiss="modal">&times;</button>-->
                        <h4 class="blue bigger"><?php echo lang('deudas');?><small>
                            <i class="icon-double-angle-right"></i>
                            <?php echo isset($deudoresCtaCte[0]['nombre_apellido']) ? $deudoresCtaCte[0]['nombre_apellido']:'';?>
                            </small>
                        </h4>
                </div>

                <div class="modal-body overflow-visible">
                        <div class="row">
                         
                            <div class="table-responsive">
                                <form id="enviarAviso">
                                <table id="detalleDeudor" class="table table-bordered">
                                    <thead>
                                    <th><?php echo lang('seleccionar');?></th>
                                    <th><?php echo lang('descripcion');?></th>
                                    <th><?php echo lang('importe');?></th>
                                    <th><?php echo lang('saldo');?></th>
                                    <th><?php echo lang('fecha_vencimiento');?></th>
                                    <th><?php echo lang('alertar');?></th>
                                    </thead>
                                    <tbody>
                                       
                                      <?php 
                                      
                                       getBody($deudoresCtaCte);
                                      
                                      ?>  
                                    </tbody>
                                </table>
                                </form>
                            </div>

                                
                        </div>
                </div>

                <div class="modal-footer">


                        <button class="btn btn-sm btn-primary" name="submit">
                                <i class="icon-ok"></i>
                                <?php echo lang('enviar');?>
                        </button>
                </div>
        </div>
 