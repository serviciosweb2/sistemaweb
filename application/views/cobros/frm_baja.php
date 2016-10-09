<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-select.min.css')?>"/>
<script src="<?php echo base_url()?>assents/js/bootstrap-select.min.js"></script>
<script>
    
    var frm_baja_lang = <?php echo $frm_baja_lang ?>;
    
    $('.fancybox-wrap').ready(function(){        
        $('select').chosen({
            width:'100%'
        });
        
        $('button[type="submit"]').on('click',function(){      
            $('#baja').submit();
        });
    
        $('button[name="enviar"]').on('click',function(){
            $('#baja').submit();
            return false;
        });
    
        $('#baja').on('submit',function(){
            var motivo = $("[name=motivo]").val();
            if (motivo == ''){
                gritter(frm_baja_lang.motivo, false, "");
            } else {
                var cod_cobro = $("[name=cod_cobro]").val();            
                var comentario = $("[name=comentario]").val();
                var facturasTemp = $("[name=ck_factura]:checked");
                var facturas_anuladas = new Array();
                $.each(facturasTemp, function(key, element){
                    facturas_anuladas.push($(element).val());
                });
                $.ajax({
                    url: BASE_URL + 'cobros/cambiarEstado',
                    type: 'POST',
                    data: {
                        cod_cobro: cod_cobro,
                        comentario: comentario,
                        motivo: motivo,
                        facturas_anuladas: facturas_anuladas
                    },
                    cache: false,
                    dataType: 'json',
                    success: function(respuesta){                    
                        if (respuesta.codigo == '1'){                        
                            gritter(lang.validacion_ok,true);                        
                            oTable.fnDraw();                        
                            setTimeout(function(){ 
                                    $.fancybox.close(true); 
                                }, 1500 
                            );
                        } else {                            
                            gritter(respuesta.errors);                            
                        }
                    }
                });
            }
            return false;
        });    
     });
</script>

<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="blue bigger">
            <?php echo lang("anular_cobro"); ?>
            <small>
                <i class="icon-double-angle-right"></i>  
                <?php echo $cod_cobro.', '.$alumnoformateado ?>
            </small>
        </h4>
    </div>
    <div class="modal-body overflow-visible">
        <form  id="baja">
            <div class="row">
                <input type="hidden" name="cod_cobro" value="<?php echo $cod_cobro?>">
                <div class="form-group col-xs-6">
                    <label><?php echo lang('motivo'); ?></label>
                    <select  class="form-control" id="exampleInputEmail1" name="motivo" data-placeholder="<?php echo lang('seleccione_motivo');?>">
                        <option></option>
                        <?php foreach($movitos as $motivoValor){ ?>
                        <option value='<?php echo $motivoValor["id"] ?>'>
                            <?php echo $motivoValor["motivo"]; ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>        
            </div>
            <div class="row">
                <div class="form-group col-xs-12">
                    <label for="exampleInputPassword1"><?php echo lang("comentario"); ?></label>
                    <textarea class="form-control" name="comentario" id="exampleInputPassword1" placeholder="<?php echo lang('comentario');?>"></textarea>
                </div>
            </div>
            <?php if (count($facturas_asociadas) > 0){ ?>
            <div class="row">
                <div class="col-md-12">
                    <h3>
                        <small><?php echo lang("anular_facturas_relacionadas") ?></small>
                    </h3>                
                    <table class="table table-condensed">
                        <thead>
                            <tr>
                                <th>&nbsp;</th>
                                <th><?php echo lang('factura'); ?></th>
                                <th><?php echo lang('numero'); ?></th>
                                <th><?php echo lang("punto_venta"); ?></th>
                                <th><?php echo lang("importe"); ?></th>
                                <th><?php echo lang("fecha"); ?></th>
                            </tr>
                        </thead>
                        <?php foreach ($facturas_asociadas as $factura){ ?>
                        <tr>
                            <td>
                                <input type="checkbox" class="ace" value="<?php echo $factura['codigo'] ?>" name="ck_factura" checked="true">
                                <span class="lbl"></span>
                            </td>
                            <td><?php echo $factura['factura'] ?></td>
                            <td><?php echo $factura['numero_factura'] ?></td>
                            <td><?php echo $factura['prefijo'] ?></td>
                            <td><?php echo formatearImporte($factura['total'], true); ?></td>
                            <td><?php echo formatearFecha_pais($factura['fecha']); ?></td>
                        </tr>
                        <?php } ?>
                    </table>
            <?php } ?>
                </div>
            </div>
        </form> 
    </div>
    <div class="modal-footer">
        <button class="btn btn-sm btn-primary" name="enviar">
            <i class="icon-ok"></i>
            <?php echo lang("guardar"); ?>
        </button>
    </div>
</div>