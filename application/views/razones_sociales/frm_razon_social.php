<script>
    var langFrm = <?php echo $langFrm ?>;
    var codRazonSocial = <?php echo $razon_social->getCodigo()?>;
    var empresas_tel_razones = <?php echo json_encode($empresas_tel) ?>;
    var tipo_telefono_razones = <?php echo json_encode($tipo_telefono)?>;
</script>
<script src="<?php base_url() ?>assents/js/librerias/jquery-serialize/jquery.serializeJSON.min.js"></script>
<script src="<?php echo base_url('assents/js/librerias/jquery-serialize/jquery.serializeJSON.min.js') ?>"></script>
<script src="<?php base_url() ?>assents/js/chosen.jquery.js"></script>
<script src="<?php echo base_url('assents/js/librerias/datatables/ColReorderWithResize.js') ?>"></script>
<script src="<?php base_url() ?>assents/js/razones_sociales/frm_razon_social.js"></script>
<?php
$this->load->helper('datepicker');
$this->load->helper('formatearfecha'); 

?>
<style>
    #ui-datepicker-div{
        z-index: 12000 !important;
    }
</style>
<div id="div_fancy_wrap">
    <input type='hidden' name='empresas_tel' value='<?php echo json_encode($empresas_tel) ?>'>
    <input type='hidden' name='tipo_telefono' value='<?php echo json_encode($tipo_telefono) ?>'>
    <input type='hidden' name='telefonos_razones' value='<?php echo json_encode($telefonos_razones) ?>'>
    <input type='hidden' name='tipo_identificacion' value='<?php echo json_encode($tipo_identificacion)?>'>
    <input type='hidden' name='condicion' value='<?php echo json_encode($condicion) ?>'>
    <?php
    function setTel($telefonos_razones, $empresas_tel, $tipo_telefono) {
        $empresaNombre = '';
        $tipoNombre = '';
        $tel = '';
        if (count($telefonos_razones) == 0) {
            return lang('sinTelefono');
        } else {
            foreach ($telefonos as $telefono) {
                foreach ($tipo_telefono as $tipo) {
                    if ($telefono['tipo_telefono'] == $tipo['id']) {
                        $tipoNombre = $tipo['nombre'];
                    }
                }
                foreach ($empresas_tel as $empresa) {
                    if ($telefono['empresa'] == $empresa['codigo']) {
                        $empresaNombre = $empresa['nombre'];
                    }
                }
                $tel.= $telefono['cod_area'] . '-' . $telefono['numero'] . '  ' . $tipoNombre . ' .- ';
            }
            return $tel;
        }
    }
    ?>
    <div id="telefonosRazones" class="modal fade" data-width="80%" tabindex="-1" data-backdrop="static" data-keyboard="false">
        <div class="modal-header">
            <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>-->
            <h3><?php echo lang("telefonos"); ?></h3>
        </div>
        <div class="modal-body">
            <div class="row">
                <div id="test" class="" style="display: none">
                    <form id="editTel">
                        <div class="row">
                            <div class="form-group col-xs-12">
                                <label><?php echo lang('datos_empresa') ?></label>
                                <select class="form-control"></select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-xs-12">
                                <label><?php echo lang('tipo_telefono') ?></label>
                                <select class="form-control"></select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-xs-12">
                                <label><?php echo lang('codarea') ?>-</label>
                                <input class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-xs-12">
                                <label><?php echo lang('numero') ?></label>
                                <input class="form-control">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive contenedorTabla">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <!--<button type="button" data-dismiss="modal" id="btn-ok-cambio-estado" class="btn btn-primary"><?php echo lang('continuar') ?></button>-->
            <button type="button" id="btn-ok-cambio-estado" class="btn btn-primary"><?php echo lang('continuar') ?></button>
        </div>
    </div>

    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="blue bigger">
                <?php echo lang('razon_social'); ?>
            </h4>
        </div>

        <div class="modal-body overflow-visible">
            <div class="row">
                <div class="tabbable">
                    <ul class="nav nav-tabs" id="mitab">
                        <li class="active">
                            <a href="#tab1" data-toggle="tab"><?php echo lang('tab_datos') ?></a>
                        </li>
                    </ul>
                    <div class="tab-content" >
                        <div class="tab-pane active " id="tab1">
                            <form id="generalRazon">                                
                                <div class="row">                                    
                                    <div class="col-md-2 col-xs-12 form-group">
                                        <label><?php echo lang('tipo_identificacion') ?>*</label>
                                        <select class="form-control input-sm" data-placeholder="<?php echo lang('seleccionar_tipo'); ?>" name="tipoIdentificacion" tabindex="1" onchange="getCondicionesSociales();">
                                            <option></option>
                                            <?php
                                            foreach ($tipo_identificacion as $valor){ ?>
                                            <option value="<?php echo $valor['codigo'] ?>"
                                                    <?php if ($valor['codigo'] == $razon_social->tipo_documentos){ ?> selected="true" <?php } ?>>
                                                <?php echo $valor['nombre']; ?>
                                            </option>
                                            <?php 
                                            
                                                    } 
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2 col-xs-12 form-group">
                                        <label><?php echo lang('numero') ?>*</label>
                                        <input class="form-control input-sm" type="text" name="documento" value="<?php echo ($razon_social->documento == '') ? '' : $razon_social->documento ?>" tabindex="2">
                                    </div>
                                    <div class="col-md-4 col-xs-12 form-group">
                                        <input type="hidden" name="codigo" value="<?php echo $razon_social->getcodigo(); ?>">
                                        <label><?php echo lang('razon_social') ?>*</label>
                                        <input id="element_focus" class="form-control input-sm" type="text" name="nombre" value="<?php echo isset($razon_social->razon_social) ? $razon_social->razon_social : ''; ?>" tabindex="3">
                                    </div>
                                    <div class="col-md-2 col-xs-12 form-group">
                                        <label><?php echo lang('razon_condicion'); ?>*</label>
                                        <select class="form-control input-sm" name="condicion" data-placeholder="<?php echo lang('seleccionar_condicion'); ?>" tabindex="4">
                                            <option></option>
                                            <?php foreach ($condicion as $list_cond) {
                                                $selected = $list_cond['codigo'] == $razon_social->condicion ? 'selected' : '';
                                                echo '<option value="' . $list_cond['codigo'] . '" ' . $selected . '>' . $list_cond['condicion'] . '</option>';
                                            } ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2 col-xs-12 form-group">
                                        <label><?php echo lang("inicio_actividades"); ?>*</label>
                                        <input  class="form-control input-sm" name="inicio_actividades" value="<?php echo ($razon_social->inicio_actividades == '' || $razon_social->inicio_actividades == '0000-00-00') ? '' : formatearFecha_pais($razon_social->inicio_actividades); ?>" placeholder="<?php echo lang('fechanaci_alumno');?>" tabindex="5">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-3 col-xs-12 form-group">
                                        <label><?php echo lang('domicilio') ?>*</label>
                                        <input class="form-control input-sm" type="text" name="calle_razon" value="<?php echo $razon_social->direccion_calle == '' ? '' : $razon_social->direccion_calle; ?>" tabindex="6">
                                    </div>
                                    <div class="col-md-3 col-xs-12 form-group">
                                        <div class="row">
                                            <div class="col-md-6 col-xs-12 form-group">
                                                <label><?php echo lang('numero') ?>*</label>
                                                <input class="form-control input-sm" name="calle_num_razon" type="text" value="<?php echo $razon_social->direccion_numero == '' ? '' : $razon_social->direccion_numero; ?>" tabindex="7">
                                            </div>
                                            <div class="col-md-6 col-xs-12 form-group">
                                                <label><?php echo lang('calle_complemento') ?></label>
                                                <input class="form-control input-sm" name="complemento_razon" type="text" value="<?php echo $razon_social->direccion_complemento == '' ? '' : $razon_social->direccion_complemento ?>" tabindex="8">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xs-12 form-group">
                                        <div class="row">
                                            <div class="col-md-2 col-xs-12 form-group">
                                                <label><?php echo lang('codigo_postal') ?></label>
                                                <?php ?>
                                                <input type="text" name='codpost' class="form-control input-sm" value="<?php echo $razon_social->codigo_postal == '' ? '' : $razon_social->codigo_postal; ?>" tabindex="9">
                                            </div>
                                            <div class="col-md-5 col-xs-12 form-group">
                                                <label><?php echo lang('provincia'); ?>*</label>
                                                <select class="form-control input-sm" name="domiciProvincia" data-placeholder="<?php echo lang('seleccionar_provincia'); ?>" tabindex="10">
                                                    <option></option>
                                                    <?php foreach ($provincias as $list_prov){ ?> 
                                                    <option value="<?php echo $list_prov['id'] ?>" 
                                                        <?php if ($list_prov['id'] == $provincia){ ?> selected="true" <?php } ?>>
                                                        <?php echo $list_prov['nombre'] ?>
                                                    </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-md-5 col-xs-12 form-group">
                                                <label><?php echo lang('localidad') ?>*</label>
                                                <select class="form-control input-sm" name="domiciLocalidad" data-placeholder="<?php echo lang('seleccionar_localidad'); ?>" tabindex="11">
                                                    <option></option>
                                                    <?php foreach ($localidades as $lista_loc){ ?> 
                                                    <option value="<?php echo $lista_loc['id'] ?>"
                                                        <?php if ($lista_loc['id'] == $razon_social->cod_localidad){ ?> selected="true" <?php } ?>>
                                                        <?php echo $lista_loc['nombre'] ?>
                                                    </option>
                                                        <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
<!--                                    <div class="col-md-3 col-xs-12 form-group">
                                        
                                    </div>-->
                                    <div class="col-md-3 col-xs-12 form-group">
                                        <label><?php echo lang('email') ?></label>
                                        <input class="form-control input-sm" name="email_razon"  value="<?php echo $razon_social->email ?>"tabindex="12">
                                    </div>
                                    
                                    
                                    
                                    <div class="col-md-7 col-xs-12 form-group">
                                        
                                        <div class="col-md-2 no-padding-right  form-group">
                                            <label> &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</label>
                                            <select class="form-control">
                                                <?php 
                                                    foreach($tipo_telefono as $tipo)
                                                    {
                                                        echo '<option value="'.$tipo['id'].'">'.$tipo['nombre'].'</option>';
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-3 no-padding form-group">
                                            <label><?php echo lang('telefono')?></label>
                                            <input type="tel" name="telefono_default_razon" class="form-control input-sm" value="">
                                        </div>
                    
                                        <div class="col-md-1 form-group ">
                                            <label> &nbsp; </label>
                                            <a href="javascript:void(0)" class="btn btn-sm btn-primary pull-right" style="height: 30px !important;" onclick="verTelefonosRazon();">+</a>
                                        </div>

                                        <?php if(isset($filial['pais']) && $filial['pais'] == 2){ ?>
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div style="display: none;" class="col-md-7 no-padding-right select_empresa_telefono_razon form-group"><!-- style="display: none;">-->
                                                    <label><?php echo lang('empresa_celular') ?></label>
                                                    <select id="id_empresa_telefono_razon" class="form-control" onchange="actualizarTelefonoDefaultRazones();">
                                                        <?php foreach($empresas_tel as $emp){ ?>
                                                            <?php if($emp['tipo'] == 'MOVIL'){ ?>
                                                                <option></option>
                                                                <option value="<?php echo $emp['codigo'] ?>">
                                                                    <?php echo $emp['nombre']; ?>
                                                                </option>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        
                                        
<!--                                        <label><?php echo lang('telefono') ?></label>
                                        <div class="ace-file-input" style="height:0px;">
                                            <input id="id-input-file-2" type="file"tabindex="13"></input>
                                            <label class="file-label" data-title=<?php echo lang('ver_editar') ?>>
                                                <span id="telefono_razones_mostrar" class="file-name" data-title="<?php echo setTel($telefonos_razones, $empresas_tel, $tipo_telefono) ?>">
                                                    <i class="icon-phone"></i>
                                                </span>
                                            </label>
                                            <a class="remove" href="#"></a>
                                        </div>-->
                                    </div>
                                    
<!--                                    <div class="col-md-3 col-xs-12 form-group">
                                        
                                    </div>-->
                                
                                </div>                                
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button name="enviarForm" type="" 
            <?php if ($esdefault) { ?>
                    class="btn btn-sm btn-alert" disabled="true">
                        <?php echo lang('no_se_puede_modificar'); } else { ?> 
                class="btn btn-sm btn-primary"><i class="icon-ok"></i>
                    <?php echo lang('guardar'); } ?>
            </button>
        </div>
    </div>
</div>
<input type="hidden" name="modo_llamada" value="<?php echo $modo ?>">