<script>
    $(document).ready(function(){
        $('#frmBaja select').chosen({
            width:'100%'
        });
 
        $('.fancybox-wrap ').on('submit','#frmBaja',function(){
            var dataPOST=$(this).serializeArray();
            $.ajax({
                url:BASE_URL+'facturacion/baja',
                data:dataPOST,
                type:'POST',
                cache:false,
                dataType:'json',
                success:function(respuesta){
                    switch(respuesta.codigo){
                        case 0:
                            $.gritter.add({
                                title: lang.ERROR,
                                text: respuesta.msgerror,
                                sticky: false,
                                time: '3000',
                                class_name: 'gritter-error'
                            });
                            $.fancybox.close();
                        break;
                           
                        case 1:
                            $.gritter.add({
                                title: lang.BIEN,
                                text: respuesta.texto,
                                sticky: false,
                                time: '3000',
                                class_name: 'gritter-success'
                            });
                            oTable.fnDraw();
                            $.fancybox.close();
                        break;
                }}                
            });
            return false;
        });
    });
</script>
<form id="frmBaja">
    <div class="modal-content" >
        <div class="modal-header">
            <h4 class="blue bigger"><?php echo lang('baja_factura'); ?></h4>
        </div>
        <div class="modal-body overflow-visible">
            <div class="row" >
                <div class="form-group col-md-12">
                    <label><?php echo lang('motivo'); ?></label>
                    <select  name="motivo" data-placeholder="<?php echo lang('seleccione_motivo');?>">
                        <option></option>
                        <?php
                            foreach($motivos as $motivo){
                                echo '<option value='.$motivo['id'].'>'.$motivo['motivo'].'</option>';
                            }                    
                        ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-12">
                    <label><?php echo lang('comentario'); ?></label>
                    <div>
                        <textarea  name="comentario" placeholder="<?php echo lang('comentario');?>"></textarea>
                    </div>
                </div>
            </div>
            <?php if($cobro !== ""){ ?>
            <div class="row">
                <div class="col-md-12">
                    <h3><small>Anular cobros relacionados</small></h3>                
                    <table class="table table-condensed">
                        <tr>
                            <th></th>
                            <th>Codigo</th>
                            <th>Importe</th>
                            <th>Medio de pago</th>
                        </tr>
                        <tr>
                            <td>
                                <input type="checkbox" class="ace"  value="<?php echo $cobro["cobro"]["codigo"] ?>" name="cobro_check" checked="checked">
                                <span class="lbl"></span>
                            </td>
                            <td><?php echo $cobro["cobro"]["codigo"] ?></td>
                            <td><?php echo $cobro["cobro"]["importe"] ?></td>
                            <td><?php echo $cobro["medio_pago"] ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php } ?>  
        </div>
        <div class="modal-footer">
            <input type="hidden" name="cod_factura" value="<?php echo $cod_factura?>">
            <button class="btn  btn-danger"  type="submit">     
                <?php echo lang('baja_factura'); ?>
            </button>
        </div>  
    </div>
</form>