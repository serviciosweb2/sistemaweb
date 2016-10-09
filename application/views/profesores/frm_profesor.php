<?php
$this->load->helper('datepicker');
$this->load->helper('formatearfecha');

function setTelDefault($telefonos,$empresas_tel,$tipo_telefono){
    $empresaNombre='';
    $tipoNombre='';
    if(count($telefonos) ==0){
        return lang('sinTelefono');
    } else {
        foreach($telefonos as $telefono){
            if($telefono['default']==1){
                foreach($tipo_telefono as $tipo){
                    if($telefono['tipo_telefono']==$tipo['id']){
                        $tipoNombre=$tipo['nombre'];
                    }
                }
                
                foreach($empresas_tel as $empresa){
                    if($telefono['empresa']==$empresa['codigo']){
                        $empresaNombre=$empresa['nombre'];
                    }
                }
                $tel=$telefono['cod_area'].'-'.$telefono['numero'].'  '.$tipoNombre;
                return $tel;
            }
        }
    }
} ?>
<style>
    input[readonly].inputTable{

    }
    
    #ui-datepicker-div{
        z-index: 15000 !important;
    }
    
    .chosen-results{
        max-height: 100px !important;
    }
</style>

<script src="<?php echo base_url()?>assents/js/jquery.validate.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url('assents/css/profesores/frm_profesor.css')?>"/>
<script>
    var langFrm = <?php echo $langFrm?>
</script>
<script src="<?php echo base_url('assents/js/profesores/frm_profesor.js')?>"></script>

<div id="detalleTelefonos" class="modal fade" data-width="80%" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-body">
        <div class="row">
            <div class="table-responsive contenedorTabla">
                <table id="tablaTelefonos" class="table table-bordered">
                    <thead>
                    <th><?php echo lang('codarea')?></th>
                    <th><?php echo lang('numero')?></th>
                    <th><?php echo lang('empresa')?></th>
                    <th><?php echo lang('tipo_telefono')?></th>
                    <th><?php echo lang('default')?></th>
                    <th><?php echo lang('baja'); ?></th>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" id="btn-ok-cambio-estado" class="btn btn-primary" onclick="setTelefono();">Ok</button>
    </div>
</div>

<div class="modal-content" style="height: 100% !important;">
    <input type="hidden" name="razonSociales" value='<?php echo json_encode($razonSociales)?>'>
    <input type="hidden" name="empresas_tel" value='<?php echo json_encode($empresas_tel)?>'>
    <input type="hidden" name="telefonos" value='<?php echo json_encode($telefonos)?>'>
    <input type="hidden" name="condiciones" value='<?php echo json_encode($condiciones)?>'>
    <input type="hidden" name="tipoTelefonos" value='<?php echo json_encode($tipoTelefonos)?>'>
    <input type="hidden" name="tipo_dni" value='<?php echo json_encode($tipo_dni)?>'>    
    <div class="modal-header">
        <h4 class="blue bigger"><?php echo lang('nuevo_profesor')?></h4>
    </div>
    <div class="modal-body overflow-visible">
        <div class="tabbable tabs-left">
            <ul class="nav nav-tabs" id="myTab">
                <li class="active"><a data-toggle="tab" href="#home"><i class="pink icon-dashboard bigger-110"></i><?php echo lang('tab_datos')?></a></li>
                <li>
                    <a data-toggle="tab" href="#profile"><?php echo lang('razon_social')?></a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="home">
                    <form id="formProfesores">
                        <input type="hidden" name="fechaalta" value='<?php echo $profesor->fechaalta ?>'>
                        <div class="col-md-6 col-xs-12">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label><?php echo lang('nombre')?>*:</label>
                                    <input type="text" class="form-control input-sm" name="nombre" value="<?php echo $codigo=='-1' ? '' : $profesor->nombre?>" onblur="validar(this);">
                                    <input type="hidden" value='<?php echo $codigo?>' name='codigo'>
                                </div>
                                <div class="form-group col-md-6">
                                    <label><?php echo lang('apellido')?>*:</label>
                                    <input type="text" class="form-control input-sm" name="apellido" value="<?php echo $codigo=='-1' ? '' : $profesor->apellido?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4 col-xs-12">
                                    <label>
                                        <?php echo lang('tipo_documento')?>*
                                    </label>
                                    <select name="tipoProfesor" class="form-control input-sm" data-placeholder="Seleccione tipo">
                                        <option></option>
                                        <?php foreach($tipo_dni as $tipo){
                                        $selected=$tipo[ 'codigo']==$profesor->tipodocumento ? 'selected' : '';
                                        echo '<option value="'.$tipo['codigo'].'" '.$selected.'>'.$tipo['nombre'].'</option>'; } ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label> <?php echo lang('numero')?>*</label>
                                    <input type="text" class="form-control input-sm" name="documento"value="<?php echo $codigo=='-1' ? '' : $profesor->nrodocumento ?>">
                                </div>
                                <div class="form-group col-md-4">
                                    <label><?php echo lang('fecha_nacimiento')?>*</label>
                                    <input type="text" class="form-control input-sm" name="fechanac" id="datepicker" value='<?php echo $codigo=='-1' ? '' : formatearFecha_pais($profesor->fechanac) ?>'>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label><?php echo lang('email')?>*</label>
                                    <input class="form-control input-sm" type="text" name="mail" value='<?php echo $codigo==' -1 ' ? ' ' : $profesor->mail ?>'>
                                </div>
                                <div class="col-md-6 col-xs-12 form-group">
                                    <label><?php echo lang('telefono')?>*</label>
                                    <div class="ace-file-input" style="height:0px;">
                                        <input id="id-input-file-2" type="file">
                                        <label class="file-label" data-title="<?php echo lang('ver_editar');?>">
                                            <span class="file-name" data-title="<?php echo setTelDefault($telefonos,$empresas_tel,$tipoTelefonos)?>">
                                                <i class="icon-phone"></i>
                                            </span>
                                        </label>
                                        <a class="remove" href="#"></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label><?php echo lang('provincia')?>*</label>
                                    <?php $provinciaProfesor=isset($provincia_profesor)? $provincia_profesor : ''; ?>
                                    <select class="form-control input-sm" name="provincia" data-placeholder="Seleccione provincia">
                                        <option></option>
                                        <?php
                                        foreach($provincias as $provincia){
                                            $selected=$provinciaProfesor==$provincia['id'] ? 'selected' : '';
                                            echo '<option value="'.$provincia[ 'id']. '"  '.$selected.'>'.$provincia[ 'nombre']. '</option>';
                                        } ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label><?php echo lang('localidad')?>*</label>
                                    <select name="cod_localidad" class="form-control input-sm" data-placeholder='Seleccione localidad'>
                                        <?php
                                        if(isset($localidades)){
                                            foreach($localidades as $localidad){
                                                $selected= $localidad['id'] == $profesor->cod_localidad ? 'selected' : '';
                                                echo '<option value="'.$localidad['id'].'" '.$selected.'>'.$localidad['nombre'].'</option>';
                                            }
                                        } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label><?php echo lang('domicilio')?>*</label>
                                    <input type="text" class="form-control input-sm" name="calle" value='<?php echo $codigo==' -1 ' ? ' ' : $profesor->calle?>'>
                                </div>
                                <div class="form-group col-md-4">
                                    <label><?php echo lang('calle_numero')?>*</label>
                                    <input type="text" class="form-control input-sm" name="calle_numero" value='<?php echo $codigo==' -1 ' ? ' ' : $profesor->numero?>'>
                                </div>
                                <div class="form-group col-md-4">
                                    <label><?php echo lang('calle_complemento')?></label>
                                    <input type="text" class="form-control input-sm" name="calle_complemento" value='<?php echo $codigo==' -1 ' ? ' ' : $profesor->complemento?>'>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label><?php echo lang('datos_barrio')?></label>
                                    <input type="text" class="form-control input-sm" name="barrio" value='<?php echo $codigo==' -1 ' ? ' ' : $profesor->barrio?>'>
                                </div>
                                <div class="form-group col-md-6">
                                    <label> <?php echo lang('codigo_postal')?>*</label>
                                    <input type="text" class="form-control input-sm" name="codigopostal" value='<?php echo $codigo==' -1 ' ? ' ' : $profesor->codigopostal?>'>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label><?php echo lang('observaciones')?></label>
                            <textarea class="form-control col-md-12" name="observaciones"><?php echo $codigo=='-1' ? '' : $profesor->observaciones?></textarea>
                        </div>
                </div>
                <div class="tab-pane" id="profile">
                    <div class="col-md-12">
                        <div class='row'>
                            <div class='table-responsive'>
                                <table id="tablaRazon" class='table table-striped table-bordered table-hover table-condensed'>
                                    <thead>
                                        <th><?php echo lang('razon_condicion')?></th>
                                        <th><?php echo lang('tipo_identificacion')?></th>
                                        <th><?php echo lang('numero')?> </th>
                                        <th><?php echo lang('razon_social')?></th>
                                        <th><?php echo lang('default')?></th>
                                        <th><?php echo lang('eliminar')?></th>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                                <input name='razonesSociales' type='hidden' value='<?php echo json_encode($razonSociales)?>'>
                    </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn  btn-success" id="btn-guardar">
            <?php echo lang('guardar')?>
            <i class="icon-arrow-right icon-on-right bigger-110"></i>
        </button>
    </div>
</div>