<style>
    #ui-datepicker-div {
        z-index: 10000 !important;
    }
    
</style>
<script>
    var langFrm = <?php echo $langFrm ?>;
</script>

<script src="<?php echo base_url('assents/js/proveedores/frm_proveedores.js')?>"></script>

<?php 
//echo '<pre>';
//print_r($arrProveedor);
//echo '<pre>';
?>
<div class="modal-content" id="frm_proveedores">
    <div class="modal-header">
        <!--<button class="close" data-dismiss="modal" type="button">×</button>-->
        <h4 class="blue bigger"><?php echo $objProveedor->getCodigo() == -1 ? lang('nuevo_proveedor') : lang('modificar_proveedor'); ?></h4>
    </div>

    <div class="modal-body overflow-visible">
        <div class="tabbable tabs-left">
            <ul id="mitab" class="nav nav-tabs">
                <li class="active">
                    <a data-toggle="tab" href="#tab1"><?php echo lang('proveedor') ?></a>
                </li>
                <li>
                    <a data-toggle="tab" href="#tab2"><?php echo lang('telefonos'); ?></a>
                </li>
               
            </ul>
            <div class="tab-content" style="min-height: 400px;">                
                <div id="tab1" class="tab-pane active">
                    <form id="form_general">
                        <input type="hidden" name="cod_proveedor" value="<?php echo $objProveedor->getCodigo(); ?>">
                        <div class="row">
                            <div class="col-md-12 col-xs-12">
                                <div class="form-group col-md-6 col-xs-12">
                                    <label><?php echo lang('nombre'); ?></label>
                                    <input class="form-control input-sm" type="text" value="<?php echo isset($arrProveedor[0]['razon_social']) ? $arrProveedor[0]['razon_social']:'';?>" name="nombre">
                                </div>
                                <div class="form-group col-md-6 col-xs-12">
                                    <label><?php echo lang('listadoResponsables_email'); ?></label>
                                    <input class="form-control input-sm" type="text" value="<?php echo isset($arrProveedor[0]['email']) ? $arrProveedor[0]['email'] : ''; ?>" name="email">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-xs-12">
                                <div class="form-group col-md-12 col-xs-12">
                                    <label><?php echo lang('razon_condicion'); ?></label>
                                <select id="condicion_proveedor" name="condicion" class="form-control input-sm" style="display: none;">
                                    <?php foreach ($condiciones as $condicion){ ?> 
                                        <option value="<?php echo $condicion['codigo'] ?>">
                                        <?php echo $condicion['condicion'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                             <div class="col-md-6 col-xs-12">
                                <div class="form-group col-md-12 col-xs-12">
                                    <label><?php echo lang('inicio_actividades'); ?></label>
                                    <div class="input-group">
                                        <input type="text" id="inicio_actividad" class="form-control input-sm" name="inicio_activad" value="<?php echo isset($arrProveedor[0]['inicio_actividades']) ? formatearFecha_pais($arrProveedor[0]['inicio_actividades']) : ''; ?>">
                                        <span class="input-group-addon">
                                            <i class="icon-calendar bigger-110"></i>
                                        </span>
                                    </div>
                            </div>
                        </div>
                        </div> 
                        <div class="row">
                            <div class="col-md-12 col-xs-12">
                                <div class="form-group col-md-6 col-xs-12">
                                    <label><?php echo lang('tipo_identificacion')?>*</label>
                                        <select class="form-control input-sm" data-placeholder="<?php echo lang('seleccionar_tipo_doc');?>" name="tipo_identificacion_proveedor">
                                               <?php
                                             
                                                foreach($tipo_documentos as $tipo_doc){
                                                    $selected = '';
                                                    if(isset($arrProveedor[0]['tipo_documentos'])){
                                                         if($tipo_doc['codigo'] === $arrProveedor[0]['tipo_documentos']){
                                                            $selected = 'selected';
                                                        }
                                                    }
                                                   
                                                    echo '<option value='.$tipo_doc['codigo'].' '.$selected.'>'.$tipo_doc['nombre'].'</option>';
                                                }
                                               ?>
                                       </select>
                                </div>
                                <div class="form-group col-md-6 col-xs-12">
                                   
                                    <label><?php echo lang('numero_identificacion');?>*</label>
                                    <input class="form-control input-sm" type="text" name="numero_identificacion" value="<?php echo isset($arrProveedor[0]['documento']) ? $arrProveedor[0]['documento'] : ''; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-xs-12">
                                <div class="form-group col-md-4 col-xs-12">
                                    <label><?php echo lang('domicilio'); ?></label>
                                    <input class="form-control input-sm" type="text" value="<?php echo isset($arrProveedor[0]['direccion_calle']) ? $arrProveedor[0]['direccion_calle'] : ''; ?>" name="calle">
                                </div>
                                <div class="form-group col-md-4 col-xs-12">
                                    <label><?php echo lang('listadoResponsables_calleNumero'); ?></label>
                                    <input class="form-control input-sm" type="text" value="<?php echo isset($arrProveedor[0]['direccion_numero']) ? $arrProveedor[0]['direccion_numero'] : ''; ?>" name="calle_numero">
                                </div>
                                <div class="form-group col-md-4 col-xs-12">
                                    <label><?php echo lang('calle_complemento'); ?></label>
                                    <input class="form-control input-sm" type="text" value="<?php echo isset($arrProveedor[0]['direccion_complemento']) ? $arrProveedor[0]['direccion_complemento'] : ''; ?>" name="complemento">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-xs-12">
                            <div class="form-group col-md-4 col-xs-12">
                                <label><?php echo lang('provincia'); ?></label>
                                <select class="form-control input-sm select-chosen" data-placeholder="Seleccionar Provincia" name="provincia">
                                    <option></option>
                                    <?php foreach ($provincias as $provincia){ ?> 
                                    <option value="<?php echo $provincia['id'] ?>" <?php if ($provincia_sel == $provincia['id']){ ?> selected="true" <?php } ?>>
                                        <?php echo $provincia['nombre'] ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group col-md-4 col-xs-12">
                                <label><?php echo lang('localidad'); ?></label>
                                <?php
                                $locProv = isset($arrProveedor[0]['cod_localidad']) ? $arrProveedor[0]['cod_localidad'] : '';
                                ?>
                                <select class="form-control input-sm select-chosen" data-placeholder="Seleccionar Localidad" name="cod_localidad">
                                    <option></option>
                                    <?php  foreach ($localidades as $localidad){ ?> 
                                    <option value="<?php echo $localidad['id'] ?>" <?php if ($localidad['id'] == $locProv){ ?> selected="true" <?php } ?>>
                                        <?php echo $localidad['nombre'] ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group col-md-4 col-xs-12">
                                <label><?php echo lang('codigo_postal'); ?></label>
                                <input class="form-control input-sm" type="text" value="<?php echo $objProveedor->cod_postal ?>" name="codpost">
                            </div>
                        </div>
                      </div>
                        <div class="row">
                            <div class="col-md-6">
                            <div class="form-group col-md-6">
                                <label><?php echo lang('facturar_descripcion'); ?></label>
                                <textarea name="descripcion"><?php echo $objProveedor->descripcion ?></textarea>
                            </div>
                        </div>
                        <input type="hidden" name="codigo" value="<?php echo $objProveedor->getCodigo(); ?>"/>
                    </form>
                    </div>
                </div>
                <div id="tab2" class="tab-pane">
                    <form id="form_telefonos">
                        <div class="row">
                            <div class="col-md-12 col-xs-12">
                                <button id="" class="btn btn-primary" name="nuevo_telefono" style="margin-bottom:1%;" type="button"><?php echo lang('nuevo_tel') ?></button>
                            </div>
                        </div>
                        <table id="tablaTelefonosProveedores" class="table table-bordered dataTable no-footer" role="grid" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="sorting_disabled center" style="cursor: col-resize;"><?php echo lang("codarea") ?></th>
                                    <th class="sorting_disabled center" style="cursor: pointer;"><?php echo lang('numero'); ?></th>
                                    <th class="sorting_disabled center" style="cursor: pointer;"><?php echo lang('empresa'); ?></th>
                                    <th class="sorting_disabled center"><?php echo lang('tipo_telefono'); ?></th>
                                    <th class="sorting_disabled center" style="cursor: pointer;"><?php echo lang('default') ?></th>
                                    <th class="sorting_disabled center"><?php echo lang('eliminar'); ?></th>
                                </tr>
                            </thead>
                            <tbody id="tbody_telefonos">
                                <?php foreach ($telefonosProveedores as $key => $telefono){ ?>
                                <input type="hidden" name="telefonos[<?php echo $key ?>][telefono_codigo]" value="<?php echo $telefono['codigo'] ?>">
                                <tr>
                                    <td>                                    
                                        <input value="<?php echo $telefono['cod_area'] ?>" class="form-control inputTable no-margin" name="telefonos[<?php echo $key ?>][cod_area]">
                                    </td>
                                    <td>
                                        <input value="<?php echo $telefono['numero'] ?>" class="form-control inputTable no-margin" name="telefonos[<?php echo $key ?>][numero]">
                                    </td>
                                    <td>
                                        <select class="form-control" name="telefonos[<?php echo $key ?>][empresa]">
                                            <?php foreach ($empresas_tel as $empresaTel){ ?> 
                                            <option value="<?php echo $empresaTel['codigo'] ?>"
                                                    <?php if ($empresaTel['codigo'] == $telefono['empresa']){ ?> selected="true" <?php } ?>>
                                                <?php echo $empresaTel['nombre'] ?>
                                            </option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control" name="telefonos[<?php echo $key ?>][tipo_telefono]">
                                            <?php foreach ($tipo_telefono as $tipoTelefono){ ?> 
                                            <option value="<?php echo $tipoTelefono['id'] ?>"
                                                    <?php if ($tipoTelefono['id'] == $telefono['tipo_telefono']){ ?> selected="true" <?php } ?>>
                                              <?php echo $tipoTelefono['nombre'] ?>
                                            </option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="radio" name="telefonos[<?php echo $key ?>][default]" value="<?php echo $telefono['default'] == 1 ? 1 : 0?>"
                                               <?php if ($telefono['default'] == 1){ ?> checked="true" <?php } ?>>
                                    </td>
                                    <td>
                                        <button name="eliminar_telefono_proveedor" class="eliminarTelefonoProveedor btn btn-primary btn-xs"><?php echo lang('eliminar') ?></button>
                                    </td>
                                </tr>    
                                <?php } ?>
                            </tbody>
                        </table>
                        <input type='hidden' name='hidden_telefono' value='<?php echo count($telefonosProveedores); ?>'>
                    </form>
                </div>
                
            </div>
        </div>
    </div>    
    <div class="modal-footer">
        <button class="btn btn-sm btn-primary" type="" name="enviarForm">
            <i class="icon-ok"></i>
            <?php echo lang('guardar');?>
        </button>
    </div>    
</div>
<div style="display: none">
    <select id="empresas_tel_hd" style="display: none">
        <?php foreach ($empresas_tel as $empresaTel){ ?> 
        <option value="<?php echo $empresaTel['codigo'] ?>">
            <?php echo $empresaTel['nombre'] ?>
        </option>
        <?php } ?>
    </select>
    <select id="tipo_telefonos_hd" style="display: none;">
        <?php foreach ($tipo_telefono as $tipoTelefono){ ?> 
        <option value="<?php echo $tipoTelefono['id'] ?>">
          <?php echo $tipoTelefono['nombre'] ?>
        </option>
        <?php } ?>
    </select>
   
</div>