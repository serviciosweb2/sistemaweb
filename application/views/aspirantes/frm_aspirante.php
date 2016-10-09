<style>
    .color_form {
        background: #ecedf5 !important;
    }
    .chosen-choices {
         background: #ecedf5 !important;
    }
    #primer_fila .chosen-single{
        background: #ecedf5 !important;
    }
    
    #tipo_tel .chosen-container {
        width: 140% !important;
    }
    .segunda_fila .chosen-single{
        background: #ecedf5 !important;
    }
</style>
<link rel="stylesheet" href="<?php echo base_url('assents/css/tel-master/intlTelInput.css')?>"/>
<script src="<?php echo base_url('assents/js/librerias/tel-master/intlTelInput.js')?>"></script>
<script src="<?php echo base_url('assents/js/librerias/jquery-serialize/jquery.serializeJSON.min.js');?>"></script>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal.css')?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal-bs3patch.css')?>"/>
<script src="<?php base_url()?>assents/js/librerias/bootstrap-modal/bootstrap-modal.js"></script>
<script src="<?php base_url()?>assents/js/librerias/bootstrap-modal/bootstrap-modalmanager.js"></script>
<script src="<?php echo base_url('assents/theme/assets/js/jquery.maskedinput.min.js');?>"></script>
<script src="<?php echo base_url('assents/js/generalTelefonos.js');?>"></script>

 <?php 
 $numeroDefault = '';
 if(count($telefonos)>0){
       $numeroDefault = $telefonos[0]['cod_area'].''.$telefonos[0]['numero'];
   }
 ?>
 <script>     
    var _tel=<?php echo json_encode($telefonos)?>;
    var _empresasTel=<?php echo json_encode($empresas_tel)?>;
    var _tipoTelefonos=<?php  echo json_encode($tipo_telefonos)?>;
    var langFrm = <?php echo $langFrm; ?>;    
    var pais = '<?php echo $pais?>';
    var numeroDefault = '<?php echo $numeroDefault;?>'
 </script>  

<script src="<?php echo base_url("assents/js/aspirantes/frm_aspirantes.js")?>"></script> 
<?php 
$this->load->helper('formatearfecha'); 
$titulo='';
if($aspirante->getcodigo()=='-1'){ 
    $titulo=lang('titulo_form_NuevoAspirante');            
} else {
    $titulo=lang('titulo_form_ModificarAspirante');                 
}
?>
<!--Manuel: No se si esto es código muerto o no, pero sinceramente me sirve para resolver un ticket, así que lo voy a replicar
    de una forma medio sucia. Y sacar del medio. Si se rompió algo, es porque estaba mal resuelto el ticket 005173
<div id="telefonosAspirante" class="modal fade" data-width="80%" tabindex="-1" data-backdrop="static" data-keyboard="false">
   <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">Ã—</button>
    <h3><php echo lang('telefonos');?></h3>
  </div>    
    <div class="modal-body">      
      <div class="row">
          <div class="col-md-12 col-xs-12">
              <table id="tableTelefonos" class="table table-condensed table-bordered">                  
                  <thead>
                    <th><php echo lang('codarea');?></th>
                    <th><php echo lang('numero');?></th>
                    <th><php echo lang('empresa');?></th>
                    <th><php echo lang('tipo_telefono');?></th>
                    <th><php echo lang('default');?></th>
                    <th><php echo lang('eliminar');?></th>
                  </thead>
                  <tbody></tbody>                  
              </table>
          </div>     
      </div>  
    </div>
  <div class="modal-footer">
    <button type="button" data-dismiss="modal" id="btn-ok-cambio-estado" class="btn btn-primary" onclick="actualizarTelDefault();"><?php echo lang('continuar')?></button>
  </div>
</div>
-->
<div class="modal-content">
    <div class="modal-header">              
        <h4 class="blue bigger"><?php echo $titulo?></h4>
    </div>
    <form id="nuevo_aspirante">
        <div class="modal-body overflow-visible">            
            <input type="hidden" name="cod_aspirante" value="<?php echo $aspirante->getcodigo()?>">
            <input type="hidden" name="accion" value="">            
            <input type="hidden" name="telefonos" value="">
            <div class="row" id="primer_fila">
                <div class="form-group col-md-3 col-xs-12">
                    <label><?php echo lang("tipo_de_contacto"); ?>:*</label>
                    <select class="form-control" name="tipo_contacto" data-placeholder="Tipo de contacto">
                        <?php $aspirante->tipo_contacto = "PRESENCIAL" ?>
                        <option></option>
                        <option value="PRESENCIAL" <?php if ($aspirante->tipo_contacto == "PRESENCIAL"){ ?>selected="true"<?php } ?>><?php echo lang('PRESENCIAL')?></option>
                        <option value="EMAIL" <?php if ($aspirante->tipo_contacto == "EMAIL"){ ?>selected="true"<?php } ?>><?php echo lang('EMAIL')?></option>
                        <option value="TELEFONO" <?php if ($aspirante->tipo_contacto == "TELEFONO"){ ?>selected="true"<?php } ?>><?php echo lang('TELEFONO')?></option>
                        <option value="FACEBOOK" <?php if ($aspirante->tipo_contacto == "FACEBOOK"){ ?>selected="true"<?php } ?>><?php echo lang('FACEBOOK')?></option>
                    </select>
                </div>
                <div class="form-group  col-md-3 col-xs-12">
                    <label><?php echo  lang('nombre')?>:*</label>
                    <input id="nombre" name="nombre" class="form-control color_form" type="text" value="<?php echo $aspirante->nombre?>">
                </div>
                <div class="form-group col-md-3 col-xs-12">
                    <label><?php echo  lang('apellido')?>:*</label>
                    <input  name="apellido" class="form-control color_form" type="text" value="<?php echo $aspirante->apellido?>">
                </div>
                     <div class="form-group col-md-3 col-xs-12">
                    <label><?php echo  lang('email')?>:*</label>
                    <input name="email" class="form-control" type="text" value="<?php echo $aspirante->email?>">
                </div>        
               
               
                
            </div>
            <div class="row segunda_fila">
              



<div id="telefonos" data-width="80%" data-backdrop="static" data-keyboard="false">
    <div class="modal-body">      
      <div class="row">
          <div class="col-md-12 col-xs-12">
              <table id="tableTelefonos" class="table table-condensed table-bordered">                  
                  <thead>
                    <th><?php echo lang('codarea');?></th>
                    <th><?php echo lang('numero');?>*</th>
                    <th><?php echo lang('empresa');?></th>
                    <th><?php echo lang('tipo_telefono');?>*</th>
                    <th><?php echo lang('default');?></th>
                    <th><?php echo lang('eliminar');?></th>
                  </thead>
                  <tbody></tbody>                  
              </table>
          </div>     
      </div>  
    </div>
</div>

<!-- Tengo que reeemplazar esto. Ticket 05173.
                <div class="col-md-3 col-xs-12" style="padding-left: 2px !important;">
                    <label style="padding-left: 11px !important;"><php echo lang('telefonos');?>:*</label>
                    <br>
                    <div id="tipo_tel" class="col-md-4 col-xs-12">
                       <select id="id_tipo_telefono" name="tipo_telefono" class="form-control">
                            <php 
                                foreach($tipo_telefonos as $tipo)
                                {
                                       $check = '';
                                    if($telefonos['tipo_telefono'] == $tipo['id']){
                                        $check = 'checked';
                                    }
                                    echo '<option value="'.$tipo['id'].'" '.$check.'>'.$tipo['nombre'].'</option>';
                                }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3 col-xs-12">
                        <input name="telefono_aspirante" type="tel" class="form-control color_form" value="" style="width: 150px !important;">
                    </div>
                </div>
    -->            
                 <div class='form-group col-md-3 col-xs-12'>
                    <label><?php echo lang('como_nos_conocio')?>:*</label>
                    <select <?php echo ($aspirante->comonosconocio?'disabled':'');?> class="form-control" name="comoNosConocio" data-placeholder="<?=lang('como_nos_conocio')?>">
                        <option></option>
                        <?php foreach($comoNosCon as $value){ ?> 
                        <option value="<?php echo $value['codigo'] ?>"
                                <?php if ($value['codigo'] == $aspirante->comonosconocio){ ?>selected="true"<?php } ?>>
                            <?php echo $value['nombre'] ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
                
                <!-- siwakawa -->
                <div style="clear:both" id="opciones_cursos_aspirante">
                    <?php 
                    if(!sizeOf($cursos_interes)){ $cursos_interes[] = ''; }
                    
                    foreach ($cursos_interes as $i=>$curso){ 
                        
                    if (sizeOf($turnos) || sizeOf($modalidades) || sizeOf($periodos)){
                        $turno = $turnos[$i];
                        $modalidad = $modalidades[$i];
                        $periodo = $periodos[$i];
                    }
                    else {
                        $periodo = "0";
                        $turno = "4"; 
                    }
                    ?>
                    <div id="opciones_curso_aspirante<?php echo $i ?>" class="opciones_curso_aspirante" style="clear:both">
                        <div class="form-group col-md-3 col-xs-12">
                            <label><?php echo lang("cursos_de_interes"); if ($i>0) {?>  <a href="#" class="eliminar_curso_interes">  (Eliminar) </a> <?php } ?> </label>
                            <!--<select calss="form-control" class="color_form" name="cursos_interes[]" multiple="true" data-placeholder="<?php echo lang("seleccionar") ?>...">-->
                            <select class="form-control" name="cursos_interes[]" data-placeholder="<?php echo lang("cursos_de_interes"); ?>">
                                <option></option>
                                <?php foreach ($arrCursos as $cur){ ?> 
                                <option value="<?php echo $cur['codigo'] ?>" 
                                        <?php if ($cur['codigo'] == $curso){ ?> selected="true" <?php } ?>>
                                    <?php echo $cur["nombre"]; ?>
                                </option>
                                <?php } ?>
                            </select>
                        </div>
                        
                        <div class="form-group col-md-3 col-xs-12">
                            <label><?php echo lang("turno"); ?></label>
                            <select class="form-control" name="turnos[]" data-placeholder="Turno">
                                <?php foreach ($arrTurnos as $tur){ ?>
                                <option value="<?php echo $tur['id'] ?>"  
                                        <?php if ($tur['id'] == $turno){ ?> selected="true" <?php } ?>>
                                    <?php echo lang($tur['nombre']) ?> 
                                </option>
                                <?php } ?>
                            </select>
                        </div>
                        
                        <div class="form-group col-md-3 col-xs-12">
                            <label><?php echo lang("modalidad"); ?></label>
                            <select class="form-control" name="modalidades[]" data-placeholder="Modalidad">
                                <option value="normal" selected="true"><?php echo lang("modalidad_normal"); ?></option>
                                <option value="intensiva"><?php echo lang("modalidad_intensiva"); ?></option>
                            </select>
                        </div>
                        
                        <div class="form-group col-md-3 col-xs-12" style="<?php if ($pais < 3) {echo "visibility:hidden";} ?> ">
                            <label><?php echo lang("periodos"); ?></label>
                            <select class="form-control" name="periodos[]" data-placeholder="Periodos">
                                <option value="0" <?php if( $periodo == "0"){ ?> selected="true" <?php } ?> > Completo </option>
                                <?php 
                                for($j=1; $j<4 ; $j++) { ?>
                                <option value="<?php echo $j ?>" <?php if( $periodo == $j){ ?> selected="true" <?php } ?> ><?php echo $j .'º'. lang('periodo') ?> </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <?php $i++; } ?>
                </div>
                
                <div class="col-md-4">
                    <a href="#" id="agregar_curso"> <?php echo lang("agregar")." +" ?> </a>
                </div>
                
            </div>
            <div class="space-10"></div>
            <div class="row">
                <div class=" form-group col-md-4 col-xs-12">
                    <label><?php echo lang('domicilio')?>:</label>
                    <input type="text" class="form-control" name="calle" value="<?php echo $aspirante->calle?>">
                </div>                                
                <div class=" form-group col-md-2 col-xs-12">
                    <label><?php echo lang('calle_numero')?>:</label>
                    <input type="text" class="form-control" name="calle_numero" value="<?php echo $aspirante->calle_numero?>">
                </div>
                   <div class="form-group col-md-2 col-xs-12">
                    <label class=""><?php echo lang('tipo_documento')?>:</label>
                    <select class="width-100" name="tipo" data-placeholder="<?php echo lang('seleccionar_tipo')?>">
                        <option></option>
                    <?php foreach ($tipo_dni as $value){ ?> 
                        <option value="<?php echo $value['codigo'] ?>"
                                <?php if ($value['codigo'] == $aspirante->tipo){ ?> selected="true" <?php } ?>>
                            <?php echo $value['nombre'] ?>
                        </option>
                    <?php } ?>
                    </select>
                </div>
                <div class="form-group col-md-2 col-xs-12">
                    <label><?php echo lang('numero')?>:</label>
                    <input type="text" class="form-control" name="documento" id="documento" value='<?php echo trim($aspirante->documento); ?>'>
                </div>
                <div class=" form-group col-md-2 col-xs-12">
                    <label><?php echo lang('fecha_nacimiento')?>:</label>
                    <input type="text" class="form-control"  name="fechanaci" value="<?php echo $aspirante->getCodigo()==-1 || $aspirante->fechanaci == '' ? '': formatearFecha_pais($aspirante->fechanaci)?>">
                </div>     
            </div>
            <div class="row">
                <div class="form-group col-md-3 col-xs-12">
                    <label ><?php echo lang('provincia')?>:</label>
                    <select class="form-control" name="prov_muni" id="prov_muni" data-placeholder="<?php echo lang('provincia')?>">
                        <option></option>
                        <?php foreach($provincias as $value){ ?> 
                        <option value="<?php echo $value['id'] ?>" 
                            <?php if (isset($provincia_aspirante) && $value['id'] == $provincia_aspirante){ ?> selected="true" <?php } ?>>
                            <?php echo $value['nombre'] ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-md-3 col-xs-12">                                    
                    <label><?php echo lang('localidad')?>:</label>
                    <select class="form-control " name="cod_localidad" id="cod_localidad" data-placeholder="<?php echo lang('localidad')?>">
                        <option></option>
                        <?php foreach($localidades as $value){ ?>
                        <option value="<?php echo $value['id'] ?>" 
                            <?php if($value['id'] == $aspirante->cod_localidad){ ?> selected="true" <?php } ?>>
                            <?php echo $value['nombre'] ?>
                        </option>                                    
                        <?php } ?>
                    </select>
                </div>
                
                                <div class=" form-group col-md-2 col-xs-12">
                    <label><?php echo lang('calle_complemento')?>:</label>
                    <input type="text" name="calle_complemento" class="form-control" value="<?php echo $aspirante->calle_complemento?>">
                </div>  
                <div class="form-group col-md-2 col-xs-12">
                    <label><?php echo lang('codigo_postal')?>:</label>
                    <input type="text" name="codpost" class="form-control" value="<?php echo $aspirante->codpost?>">
                </div>
                <div class="form-group col-md-2 col-xs-12">
                    <label><?php echo lang("datos_barrio"); ?>:</label>
                    <input id="barrio" type="text" name="barrio" class="form-control" value="">
                </div>                      
            </div>
            <div class="row">
                <div class='form-group col-md-12  col-xs-12'>
                    <label><?php echo lang( 'observaciones')?>:</label>
                    <textarea class="form-control" name="observaciones" style="height: 68px;"><?php echo $aspirante->observaciones?></textarea>
                </div>                            
            </div>            
        </div>
    </form>
    <div class="modal-footer">
        <?php if($aspirante->getcodigo()=='-1'){ ?>
        <button class="btn btn-sm btn-primary submit" name="guardarYpresupuestar">
            <i class="icon-ok"></i>
               <?php echo lang('boton_guardar_presu_as_aspirante'); ?>
        </button>
        <?php } ?>
        <button class="btn btn-sm btn-primary submit" name='guardar'>
            <i class="icon-ok"></i>
            <?php echo lang('guardar')?>
        </button>
    </div>
</div>                                            
