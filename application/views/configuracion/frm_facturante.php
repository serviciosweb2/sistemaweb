
<link rel="stylesheet" href="<?php echo base_url('assents/css/datepicker3.css')?>"/>

<script src="<?php echo base_url('assents/js/librerias/bootstrap-datetimepicker/bootstrap-datepicker.js')?>"></script>

<script src="<?php echo base_url('assents/js/configuracion/frm_facturante.js')?>"></script>

<style>
    
    .chosen-results{
        
        max-height: 100px !important;
    }
    
</style>

<?php 

isset($objRazonSocial) ? $razonsocial=$objRazonSocial->condicion : $razonsocial='';

isset($objRazonSocial) ? $tipodocumento=$objRazonSocial->tipo_documentos : $tipodocumento='';

isset($provincia) ? $provinciaFacturante=$provincia : $provinciaFacturante='';

$this->load->helper('formatearfecha');


function condiciones($condiciones,$valor){
  
    $retorno='';


    foreach($condiciones as $condicion){
        
        $selected= $condicion['codigo']== $valor ? 'selected' : '';
        
        $retorno.='<option value='.$condicion['codigo'].' '.$selected.' >'.$condicion['condicion'].'</option>';

    }
    
   return $retorno; 

    
};


function tipoDoc($tipos,$valor){

    
        
    $retorno='';


    foreach($tipos as $tipo){
        
        $selected= $tipo['codigo']== $valor ? 'selected' : '';
            
        $retorno.='<option value='.$tipo['codigo'].' '.$selected.'>'.$tipo['nombre'].'</option>';

    }
    
   return $retorno; 

    
};


function provincias($listado,$valor){
    
 $retorno='';


    foreach($listado as $provincia){

        $selected= $valor == $provincia['id'] ? 'selected' : '';
            
        $retorno.='<option value='.$provincia['id'].' '.$selected.'>'.$provincia['nombre'].'</option>';

    }
    
   return $retorno; 
    
};



?>
<input type="hidden" name="codLocalidad" value="<?php echo isset($objRazonSocial) ? $objRazonSocial->cod_localidad: ''?>">
<div class="modal-content">
    <form id="frmFacturante">
        
        <input type="hidden" name="cod_facturante" value="<?php echo isset($objFacturantes) ? $objFacturantes->getCodigo(): '-1'?>">
        <input type="hidden" name="cod_razon_social" value="<?php echo isset($objRazonSocial) ? $objRazonSocial->getCodigo() : '-1' ?>">
        
        <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="blue bigger">
                    
                    <?php 
                    
                        if($objFacturantes->getCodigo()!=-1){
                            
                            
                            echo lang('modificar_facturante');
                        }else{
                            
                            echo lang('nuevo_facturante');
                            
                            
                        }
                    
                    ?>
                    
                </h4>
        </div>

        <div class="modal-body overflow-visible">
                <div class="row">
                    
                    <div class="col-md-6 col-xs-12">
                        
                        <div class="row">
                            <div class="col-md-4 form-group">
                                
                                <label><?php echo lang('condicion_social')?></label>
                                <select class="form-control" name="condicion" data-placeholder="<?php echo lang('condicion_social');?>">
                                
                                    <option></option>
                                    <?php echo condiciones($condiciones,$razonsocial)?>
                                </select>
                            </div>
                            
                            <div class="col-md-4 form-group">
                                
                                <label><?php echo lang('tipo_identificacion');?></label>
                                <select class="form-control" name="tipo_doc" data-placeholder="<?php echo lang('tipo_identificacion');?>">
                                    <option></option>
                                    <?php echo tipoDoc($tipoDni,$tipodocumento)?>
                                </select>
                            </div>
                            
                            <div class="col-md-4 form-group">
                                
                                <label><?php echo lang('numero_identificacion')?></label>
                                <input class="form-control" name="numero_Doc" value="<?php echo isset($objRazonSocial) ? $objRazonSocial->documento : '' ?>">
                            </div>
                            
                            
                            
                        </div>
                        
                        <div class="row">
                            
                            <div class="col-md-6 form-group">
                                
                                <label><?php echo lang('razon_social')?></label>
                                <input class="form-control" name="razon" value="<?php echo isset($objRazonSocial) ? $objRazonSocial->razon_social : '' ?>">
                            </div>
                            
                            <div class="col-md-6 form-group">
                                
                                <label><?php echo lang('inicio_de_actividades')?></label>
                                <input class="form-control" value="<?php echo isset($objFacturantes) ?  formatearFecha_pais($objFacturantes->inicio_actividades) : '' ?>" name="inicioActividad">
                            
                            </div>
                            
                            
                            
                        </div>
                        
                        
                    </div>
                    
                    
                    
                    <div class="col-md-6 col-xs-12">
                        
                        <div class="row">
                            <div class="col-md-4 form-group">
                                
                                <label><?php echo lang('provincia')?></label>
                                <select class="form-control" name="provincia" data-placeholder="<?php echo lang('seleccionar_provincia');?>">
                                    <option></option>
                                    <?php echo provincias($provincias,$provinciaFacturante)?>
                                </select>
                            </div>
                            
                            <div class="col-md-4 form-group">
                                
                                <label><?php echo lang('localidad')?></label>
                                <select class="form-control" name="localidad" data-placeholder="<?php echo lang('seleccionar_localidad');?>"><option></option></select>
                            </div>
                            
                            <div class="col-md-4 form-group">
                                
                                <label><?php echo lang('domicilio')?></label>
                                <input class="form-control" name="direccion" value="<?php echo isset($objRazonSocial) ? $objRazonSocial->direccion_calle : '' ?>">
                            </div>
                            
                            
                            
                        </div>
                        
                        <div class="row">
                            
                            <div class="col-md-6 form-group">
                                
                                <label><?php echo lang('calle_numero')?></label>
                                <input class="form-control" name="numero" value="<?php echo isset($objRazonSocial) ? $objRazonSocial->direccion_numero : '' ?>">
                            </div>
                            
                            <div class="col-md-6 form-group">
                                
                                <label><?php echo lang('calle_complemento')?></label>
                                <input class="form-control" name="complemento" value="<?php echo isset($objRazonSocial) ? $objRazonSocial->direccion_complemento : '' ?>">
                            
                            </div>
                            
                            
                            
                        </div>
                        
                        
                    </div>
                
                
                </div>
            <div class="row">
                
                <div class="col-md-2 form-group">
                        <label><?php echo lang('empresa')?></label>
                            <select  class ="form-control" name ="empresa_tel"
                                     <option></option>
                             <?php
                             $selectedTel ='';
                                foreach($empresaTel as $empresa){
                                     if(count($razonTelefono) >0){
                                         $selectedTel = $razonTelefono[0]['empresa'] == $empresa['codigo'] ? 'selected' : '';
                                         
                                     }
                                    echo '<option value="'.$empresa['codigo'].'" '.$selectedTel.'>'.$empresa['nombre'].'</option>';
                                }
                             ?> ></select>
                 </div>
                <div class="col-md-2 form-group">
                    <label><?php echo lang('codarea');?></label>
                       <?php 
                       $valor = '';
                       if(count($razonTelefono) >0){
                           $valor= $razonTelefono[0]['cod_area'] == ''? '':$razonTelefono[0]['cod_area'];
                       }
                       ?> 
                    <input name ="cod_area"class="form-control" type="text" id="cod_area" value="<?php echo $valor?>">
                </div>
                 <div class="col-md-2 form-group">
                      <?php 
                       $value = '';
                       $codigo = -1;
                       if(count($razonTelefono) >0){
                           $value= $razonTelefono[0]['numero'] == ''? '':$razonTelefono[0]['numero'];
                           $codigo = $razonTelefono[0]['codigo'];
                       }
                       ?> 
                    <label><?php echo lang('numero');?></label>
                    <input name="numero_tel" class="form-control" type="text" id="cod_area" value="<?php echo $value?>">
                    <input name="codigo_tel" type="hidden" value="<?php echo $codigo;?>">
                </div>
            </div>
        </div>

        <div class="modal-footer">


                <button class="btn btn-sm btn-primary">
                        <i class="icon-ok"></i>
                        <?php echo lang('guardar')?>
                </button>
        </div>
    </form>
</div>
	

