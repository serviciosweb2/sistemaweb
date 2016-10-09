<script src="<?php echo base_url('assents/js/bancos/boletos/archivos/remessa.js') ?>"></script>
<script src="<?php echo base_url('assents/js/impresiones.js'); ?>"></script>
<div class="row">
    <div class="col-md-11">
        <!-- #section:elements.tab -->
        <div class="tabbable">
            <ul class="nav nav-tabs" id="myTab">
                <li class="active">
                    <a data-toggle="tab" href="#remessa">
                        <i class="green ace-icon fa fa-home bigger-120"></i>
                        <?php echo lang('remesa');?>
                    </a>
                </li>

                <li class="">
                    <a data-toggle="tab" href="#retorno">
                        <?php echo lang('retorno');?>

                    </a>
                </li>

                <li class="">
                    <a data-toggle="tab" href="#enviados">
                         Enviados
                    </a>
                </li>

            </ul>

            <div class="tab-content">
                <div id="remessa" class="tab-pane fade active in">



                    <div id="areaTablas">
                        <?php
                        $tmpl = array('table_open' => '
            <table id="achivosRemessa" cellpadding="0" cellspacing="0"
            border="0" class="table table-striped table-bordered table-condensed " oncontextmenu="return false" 
            onkeydown="return false">');
                        $this->table->set_template($tmpl);
                        $this->table->set_heading(array('id', lang('convenio'), lang('fecha'), lang('links'), lang('mover')));
                        echo $this->table->generate();
                        ?>
                    </div>

                <button class="btn btn-info boton-primario" 
                    data-original-title="" 
                    title="Mover todo a remessa." 
                    onclick="moveToRemessa();">
                        Mover todo a remessa
                </button>
                </div>

                <div id="retorno" class="tab-pane fade">
                    <div class="row">
                        <div class="col-md-12 no-margin center  margin-20">
<div class='tabbable tabs-below'>
<div class='tab-content'>
<!-- Tab form -->
<div class='tab-pane active' id='subir'>
                            <div class="form-group" name='form-subir-archivos'>
                                <div class="no-padding col-xs-12">
                                    <!-- #section:custom/file-input -->


                                    <div class="row ">

                                        <div class="center" >
                                            <h3 class="header blue smaller lighter">
                                                <?php echo lang('subir_archivos_de_retorno');?>
                                            </h3>

                                            <!-- our form -->
                                            <form id="subir-retorno" method="post" action="sendRetorno">
                                                <input  id="file-retorno" type="file" name="archivoretorno[]" multiple />

                                                <div class="hr hr-12 dotted"></div>

                                                <button type="submit" class="btn btn-sm btn-primary  btn-subir-retorno"><?php echo lang('procesar_retornos');?></button>

                                            </form>
                                        </div>

                                    </div>



                                </div>


                            </div>   
</div>
<!-- Fin form -->
<!-- Tab subir-->
<div class='tab-pane' id='leer'>
                            <div class="form-group" name='form-leer-directorio'>

                                <button class="btn btn-large btn-success btn-leer-retorno" id='btn-leer-retorno'>
                                    <i class="ace-icon fa fa-fire bigger-110"></i>
                                    <span class="bigger-110 no-text-shadow">Leer retornos recibidos.</span>
                                </button>

                            </div>
</div>
<!-- Fin subir-->
</div>

<ul class="nav nav-tabs" id="myTab2">
<li class="active">
   <a data-toggle="tab" href="#subir" aria-expanded="true">Subir</a>
</li>

<li class="">
   <a data-toggle="tab" href="#leer" aria-expanded="false">Leer</a>
</li>

</ul>
</div>
                        </div>    

                    </div>

                    <div class="row tabla-retornos">

                        <div class="col-md-12">




                            <?php
                            $tmpl = array('table_open' => '
            <table id="achivosRetorno" cellpadding="0" cellspacing="0"
            border="0" class="table table-striped table-bordered table-condensed " oncontextmenu="return false" 
            onkeydown="return false">');
                            $this->table->set_template($tmpl);
                            $this->table->set_heading(array(lang('secuencia'), lang('nombre_archivo'), lang('fecha')));
                            echo $this->table->generate();
                            ?>
                        </div>
                    </div>


                    <div class="row confirmacion de confirmacion-retornos hide">
                        <div class="col-md-12">
                            <table  id="tablaConfirmacion" class="table table-striped table-bordered table-hover dataTable no-footer">
                                <thead>
                                    <tr>
                                        <th><?php echo lang('nosso_numero');?></th>
                                        <th><?php echo lang('sacado');?></th>  
                                        <th><?php echo lang('valor_titulo');?></th>  
                                        <th><?php echo lang('valor_q');?></th>
                                        <th><?php echo lang('tipo_movimiento');?></th>
                                        <th><?php echo lang('motivo_ocurrencia');?></th>
                                    </tr>
                                </thead>

                            </table>

                            <div class="row-fluid wizard-actions" >
                                <button class="btn btn-large btn-success btn-confirmar-retorno">
                                    <i class="ace-icon fa fa-fire bigger-110"></i>
                                    <span class="bigger-110 no-text-shadow"><?php echo lang('confirmar_retorno');?></span>
                                </button>
                            </div>
                        </div>
                    </div>  
                    <div class="row">
                        <div class="col-md-12 center hide proceso-correcto">

                            <h4><?php echo lang('retornos_procesados_correctamente');?></h4>
                            <i class="ace-icon fa icon-ok-circle  green" style="font-size: 200px;"></i>




                        </div>

                    </div>

                </div>

                <div id="enviados" class="tab-pane fade">



                    <div id="areaTablasSent">
                        <?php
                        $tmpl = array('table_open' => '
            <table id="achivosRemessaEnviados" cellpadding="0" cellspacing="0"
            border="0" class="table table-striped table-bordered table-condensed " oncontextmenu="return false" 
            onkeydown="return false">');
                        $this->table->set_template($tmpl);
                        $this->table->set_heading(array('id', lang('convenio'), lang('fecha'), lang('links')));
                        echo $this->table->generate();
                        ?>
                    </div>

                </div>

            </div>
        </div>

        <!-- /section:elements.tab -->
    </div><!-- /.col -->
</div>






