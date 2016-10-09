<?php
$menu = array("contextual" => array(
    "habilitado" => "1",
    "accion" => "editar_movimiento_caja",
    "text" => lang("editar_movimiento_caja")
));

$menuContaxtual = json_encode($menu);

?>
<script>
    var columns = <?php echo $columns?>;
    var cajaEnFocus = <?php echo $caja_seleccionar?>;    
    var menu = <?php echo $menuContaxtual ?>;
</script>

<script src="<?php echo base_url('assents/js/caja/vista_caja_1.js')?>"></script>

<style>
    .caja_abierta{
        font-weight: bold;
    }
    
    .caja_cerrada{
        font-weight: normal;
        color: #AAA;
    }
    
    .transferencia_disabled{
        color: #CCC !important;
    }
    
    #msgSinCajasAsignadas .page-header {
        margin: 0 0 12px;
        border-bottom: 0px !important; 
        padding-bottom: 16px;
        padding-top: 7px;
    }    
</style>

<div class="col-md-12 col-xs-12 hide"></div>
<input type="hidden" value="<?php echo $cajas_totales ?>" name="cajas_totales">
    <div class="col-md-12 col-xs-12">        
        <div class="row">
            <div class="col-md-12 col-xs-12">
                <div class="tabbable">
                    <ul class="nav nav-tabs" id="myTab">                        
                        <?php $cantCajas = count($cajas);
                            foreach($cajas as $k=>$caja){
                                $estado = '<i class="icon-unlock green"></i>';                                
                                if ($caja['estado'] == "cerrada"){
                                    $estado = '<i class="icon-lock default"></i>';
                                }                                
                                $active =  $caja['codigo'] == $caja_seleccionar ? 'active' : '';                                
                                echo '<li estado="'.$caja['estado'].'" class="'.$active.'"><a data-toggle="tab" href="#caja_'.$caja['codigo'].'" data-tabla="'.$caja['codigo'].'">'.$caja['nombre'].' '.$estado.'</a></li>';
                            } ?>                    
                    </ul>
                    <div class="tab-content">                        
                        <?php $cantCajas = count($cajas);
                            foreach($cajas as $k => $caja){                               
                                $active =  $caja['codigo'] == $caja_seleccionar ? 'active in' : '';                                 
                                echo '<div id="caja_'.$caja['codigo'].'" class="tab-pane fade '.$active.'">'; ?>                             
                        <div class="row">
                            <div class="btn-group col-md-12">
                                <?php if ($permiso_nuevo_movimiento){ ?>
                                    <button class="btn btn-primary boton-primario" name="btn_agregar_nuevo_movimiento" onclick="nuevoMovimientoCaja();" <?php echo $caja['estado'] == 'cerrada' ? 'disabled': ''?>>
                                        <i class="icon-bookmark"></i>
                                        <?php echo lang("agregar_movimiento_de_caja") ?>
                                    </button>
                                    <?php if ($permiso_transferencia){ ?>
                                    <button class="btn dropdown-toggle btn-primary" data-toggle="dropdown">
                                        <span class="icon-caret-down icon-only"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-default">
                                        <li style="cursor: pointer" name="li_transferencia"
                                            class="<?php echo ($cajas_abiertas < 1) ? "transferencia_disabled" : "transferencia_enabled"; ?>">
                                            <a accion="factura-cobro" name="a_transferencia"
                                               class="<?php echo ($cajas_abiertas < 1) ? "transferencia_disabled" : ""; ?>">
                                                <?php echo lang("transferencia_entre_cajas"); ?>
                                            </a>
                                        </li>
                                    </ul>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>                            
                        <div class="row ">
                            <div class="col-md-12 col-xs-12">
                                    <table class="table table-striped table-bordered table-condensed" id="<?php echo 'tabla-'.$caja['codigo']?>" style="width:100% !important;">
                                        <thead>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                            </div>
                        </div>                            
                        <div class="row">
                            <div class="space"></div>
                            <div class="col-md-6" id="">
                                <div class="hr hr12 dotted"></div>
                                    <div class="clearfix">
                                        <div class="grid2" id="totales-<?php echo $caja['codigo'] ?>"></div>                                                
                                        <div class="grid2 center">
                                            <?php $btnAbrir = 'style="display:none;"';
                                            $btnCerrar = 'style="display:none;"';
                                            if($caja['estado']=='cerrada'){
                                                $btnAbrir = '';
                                                $btnCerrar = 'style="display:none;"';
                                            } else {
                                                $btnAbrir = 'style="display:none;"';
                                                $btnCerrar = '';
                                            } ?>
                                            <button  class="btn btn-success btn-lg btnAbrir"  onclick="frmAbrirCajas()"  <?php echo$btnAbrir ?>>
                                                <i class="icon-unlock fa-2x icon-only"></i>
                                                <?php echo lang('frm_abrir_caja') ?>
                                            </button>
                                            <button class="btn btn-warning btn-lg btnCerrar"  onclick="frmCerrarCajas()"  <?php echo $btnCerrar?>>
                                                <i class="icon-lock fa-2x icon-only"></i>
                                                <?php echo lang('frm_cerrar_caja') ?>
                                            </button>
                                        </div>
                                    </div>
                                <div class="hr hr12 dotted"></div>
                            </div>
                        </div>            
                        <?php echo '</div>'; } ?>
                    </div>
                </div>
            </div>            
            <div class="col-md-12" id="msgSinCajasAsignadas" style="display : none ;">
                <div class="page-header center">
                    <h1>
                        <?php echo lang('no_tiene_cajas_asisgnadas')?>
                    </h1>
                    <div class="row">
                        <div class="space"></div>
                        <div class="col-md-12" id="">
                            <a href="<?php echo base_url() ?>configuracion/configFacturacion">
                                <?php echo lang("click_aqui_para_configurar_caja"); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>        
        </div>        
    </div>