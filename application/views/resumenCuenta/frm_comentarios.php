<script src="<?php echo base_url('assents/theme/assets/js/jquery.slimscroll.min.js')?>"></script>
<script>
    
    var DATACOMENTARIOS = JSON.parse('<?php echo json_encode($comentarios)?>');
    var langFrm = <?php echo $langFrm ?>;
    
</script>
<script src="<?php echo base_url('assents/js/resumendecuenta/frm_comentarios.js')?>"></script>
<?php
//echo '<pre>'; 
//print_r($comentarios);
//echo '</pre>';

?>
<style>
    .fotoMSJ{
    padding-top: 10.8% !important;
}

.textoMSJ{
    
    color: #c8c8c8;
    font-size: 20pt ;
}
</style>
<div class="widget-box ">
        <div class="widget-header">
                <h4 class="lighter smaller">
                        <i class="icon-comment blue"></i>
                        <?php echo lang('agregar_comentario')?>
                </h4>
        </div>

        <div class="widget-body">
                <div class="widget-main no-padding">
                      
                            <div class="dialogs">
                                    
<!--                                

                                    <div class="itemdiv dialogdiv">
                                        <div class="user">
                                                <img alt="Alexa's Avatar" src="">
                                        </div>

                                        <div class="body">
                                                <div class="time">
                                                        <i class="icon-time"></i>
                                                        <span class="green">4 sec</span>
                                                </div>

                                                <div class="name">
                                                        <a href="#">Alexa</a>
                                                </div>
                                                <div class="text">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque commodo massa sed ipsum porttitor facilisis.</div>

                                                <div class="tools">
                                                        <a href="#" class="btn btn-minier btn-danger">
                                                                <i class="icon-trash"></i>
                                                        </a>
                                                </div>
                                        </div>
                                </div>
                                
                                <div class="itemdiv dialogdiv">
                                        <div class="user">
                                                <img alt="John's Avatar" src="">
                                        </div>

                                        <div class="body">
                                                <div class="time">
                                                        <i class="icon-time"></i>
                                                        <span class="blue">38 sec</span>
                                                </div>

                                                <div class="name">
                                                        <a href="#">John</a>
                                                </div>
                                                <div class="text">Raw denim you probably haven't heard of them jean shorts Austin.</div>

                                                <div class="tools">
                                                        <a href="#" class="btn btn-minier btn-info">
                                                                <i class="icon-only icon-share-alt"></i>
                                                        </a>
                                                </div>
                                        </div>
                                </div>-->
                            </div>
                          
                                
                        

                        <form id="nuevo_comentario">
                                <div class="form-actions">
                                    
                                    <input type="hidden" value="<?php echo $codigo_ctacte;?>" name="cod_ctacte">
                                    <input type="hidden" value=<?php echo $this->session->userdata('codigo_usuario'); ?> name="cod_usuario">
                                    
                                        
                                    <div type="hidden" class="input-group">
                                        <input placeholder="<?php echo lang('agregar_comentario')?>" type="text" class="form-control" name="comentario">
                                                <span class="input-group-btn">
                                                        <button class="btn btn-sm btn-info no-radius" type="submit">
                                                                <i class="icon-share-alt"></i>
                                                                <?php echo lang('enviar')?>
                                                        </button>
                                                </span>
                                        </div>
                                </div>
                        </form>
                </div><!-- /widget-main -->
        </div><!-- /widget-body -->
    </div>

