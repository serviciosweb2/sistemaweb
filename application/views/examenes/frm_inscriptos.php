<script>
    var alumnos_cargados = false;
    var inscriptos_cargados = false;
    var langFrm = <?php echo $langFrm?>;
    var tipo_examen = "<?php echo $examen->tipoexamen; ?>";
    var mostrar_comisiones = <?php echo ($examen->tipoexamen== 'PARCIAL' || $examen->tipoexamen == 'RECUPERATORIO_PARCIAL') ? 'false' : 'true' ?>;
</script>
<script src="<?php echo base_url('assents/js/examenes/frm_inscriptos.js')?>"></script>
<script src="<?php echo base_url('assents/js/impresiones.js') ?>"></script>
<style>
 .table_filter{
    -moz-border-bottom-colors: none;
    -moz-border-left-colors: none;
    -moz-border-right-colors: none;
    -moz-border-top-colors: none;
    background-color: #ffffff;
    border-bottom: 1px solid #afafb6;
    border-image: none;
    border-radius: 4px;
    border-top: 1px solid #afafb6;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
    cursor: default;
    padding: 8px 8px;
    position: absolute;
    right: 16px;
    text-align: left;
    top: 18px;
    z-index: 1000;
}
</style> 

<div class="modal-header">
    <h4 class="blue bigger"><?php echo lang('inscripciones');?> <small><i class="icon-double-angle-right"></i><?php echo ' '.$cod_examen;?></small></h4>
</div>   
<input type="hidden" name="codigo_examen" value="<?php echo $cod_examen ?>">

<div name="container_menu_filters_temp" style="display:none">
    <div id="div_table_filters" class="table_filter" name="div_table_filters">
        <table style="width: 100%; height:180px; width: 180px">
            <tr>
                <td>
                    <table style="width: 100%;">
                        <tr>                    
                            <td><?php echo lang("comisiones") ?></td>
                        </tr>
                        <tr>
                            <td>
                                <select name="filtro_comision" class="select_chosen" style="width: 175px;">
                                    <option value="0"><?php echo lang("todas") ?> </option>
                                    <?php foreach ($comisiones as $comision){ ?>
                                        <option value="<?php echo $comision['codigo']?>"> <?php echo $comision['nombre']?> </option>
                                    <?php } ?>
                                </select>
                            </td>                            
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr>
                <td colspan="6">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;">
                    <button class="btn btn-sm btn-success" type="button" name="btnBuscar" onclick="listar();"><?php echo lang("buscar"); ?></button>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="page-content">          
    <ul class="nav nav-tabs">
        <li class="active"><a href="#home" data-toggle="tab"><?php echo lang('inscriptos'); ?></a></li>  
        <li style="<?php /*echo ($examen->tipoexamen== 'PARCIAL' || $examen->tipoexamen == 'RECUPERATORIO_PARCIAL') && $examen->fecha <= date('Y-m-d') ? 'display:none;' : ''*/ ?>"><a href="#profile" data-toggle="tab"><?php echo lang('alumnos'); ?></a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="home" >
            <div class="table-responsive">            
                <table id="tablaInscriptos" class="table table-striped table-bordered table-hover" oncontextmenu="return false" onkeydown="return false" style="table-layout: fixed; width: 100%;">
                    <thead>
                    </thead>
                    <tbody>
                    </tbody>                
                </table>            
            </div>   
            <div class="modal-footer">
                <button class="btn btn-success" onclick="imprimirConstanciaExamen();"><?php echo lang("imprimir_constancia"); ?>
                    <i class="icon-print bigger-160"></i>
                </button>
                <button class="btn btn-success" onclick="imprimirListadoExamen();"><?php echo lang("imprimir_listado"); ?>
                    <i class="icon-print bigger-160"></i>
                </button>
            </div>
        </div>
        <div class="tab-pane" id="profile">        
            <form id="inscribirAlumnos">
                <div class="col-md-12">   
                    <div class="table-responsive">
                        <table id="tablaAlumnos" class="table table-striped table-bordered table-hover"  oncontextmenu="return false" onkeydown="return false">
                            <thead>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row"></div>
            </form>  
             <div class="clearfix form-actions">
                <div class="col-md-offset-2 col-md-9 text-right">
                    <?php if($examen->baja == 0){?>
                    <button class="btn btn-info" type="submit">
                        <i class="icon-ok bigger-110"></i>
                        <?php echo lang('guardar'); ?>
                    </button>
                    <?php }?>
                </div>
            </div>
        </div>  
    </div>
</div>