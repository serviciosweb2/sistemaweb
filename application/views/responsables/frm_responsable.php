    <?php 
    $filial = $this->session->userdata('filial');
    $codigoFilial = $filial['codigo'];
//    $visible = $codigoFilial == 79 ? 'none' : '';
    $visible = ''; // ver porque en la linea superior se excluia la filial 79 ¿solo la 79?
    
    ?>
    <div class="row">

        <form id="form_responsable">
            
            <!--PRIMER MODULO-->
            
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label><?php echo lang('tipo_documento')?></label>
                        <select name="tipo_documento_responsable" class="form-control" onchange="getCondicionesSocialesResponsable();">
                            <option value="1"></option>
                            <?php
                            foreach ($tipo_identificacion as $tipo)
                            {
                                echo '<option value="'.$tipo['codigo'].'">'.$tipo['nombre'].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label><?php echo lang('numero')?></label>
                        <input name="numero_documento_responsable" class="form-control input-sm">
                    </div> 
                    <div class="col-md-4 form-group">
                        <label><?php echo lang('fecha_nacimiento')?>*</label>
                        <input name="fecha_nacimiento_responsable" class="form-control input-sm">
                    </div>
                    
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label><?php echo lang('nombre')?>*</label>
                        <input name="nombre_responsable" class="form-control input-sm" value="">
                    </div>
                    <div class="col-md-6 form-group">
                        <label><?php echo lang('apellido')?>*</label>
                        <input name="apellido_responsable" class="form-control input-sm" value="">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-6 form-group">
                        <label><?php echo lang('email')?></label>
                        <input name="email_responsable" class="form-control input-sm" value="">
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-6">
                            <div class="col-md-4 no-padding  form-group">
                                <label> &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</label>
                                <select class="form-control"  onchange="actualizarTelDefaultResponsable();">
                                    <?php
                                    foreach($tipo_telefono as $tipo)
                                    {
                                        echo '<option value="'.$tipo['id'].'">'.$tipo['nombre'].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-7  no-padding form-group">
                                <label><?php echo lang('telefono')?></label>
                                <input type="tel" name="telefono_default_responsable" class="form-control input-sm" value="">
                            </div>

                            <div class="col-md-1 no-padding form-group pull-right">
                                <label> &nbsp;&nbsp;&nbsp; </label>
                                <a href="javascript:void(0)" class="btn btn-sm btn-primary pull-right" style="height: 30px !important;" onclick="frmTelefonosResponsable();">+</a>
                            </div>
                            <?php if(isset($filial['pais']) && $filial['pais'] == 2){ ?>
                            <div class="col-md-12 ">
                                <div class="row">
                                    <div style="display: none;" class="col-md-12 no-padding-right select_empresa_telefono_responsable form-group"><!-- style="display: none;">-->
                                        <label><?php echo lang('empresa_celular') ?></label>
                                        <select id="id_empresa_telefono_responsable" class="form-control" onchange="actualizarTelDefaultResponsable();">
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
                        </div>
                    </div>

                </div>
            </div>
            
            <!--SEGUNDO MODULO-->
            
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label><?php echo lang('domicilio')?>*</label>
                        <input name="domicilio_responsable" class="form-control input-sm">
                    </div>
                    <div class="col-md-4 form-group">
                        <label><?php echo lang('numero')?>*</label>
                        <input name="numero_domicilio_responsable" class="form-control input-sm">
                    </div> 
                    <div class="col-md-4 form-group">
                        <label><?php echo lang('calle_complemento')?></label>
                        <input name="calle_complemento_responsable" class="form-control input-sm">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label><?php echo lang('provincia')?>*</label>
                        <select name="domiciProvincia_responsable" data-placeholder="<?php echo lang('seleccionar_provincia');?>">
                                <option></option>

                           <?php foreach($prov as $list_prov){ 
                               $selected =''; //$list_prov['id']==$provincia_alumno ? 'selected' : ''; 

                               echo '<option value="'.$list_prov['id']. '" '.$selected. '>'.$list_prov[ 'nombre']. '</option>'; 

                           } 
                           ?>
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label><?php echo lang('localidad')?>*</label>
                        <select name="domiciLocalidad_responsable"></select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label><?php echo lang('datos_barrio')?></label>
                        <input name="barrio_responsable" class="form-control input-sm">
                    </div> 
                     
                </div>
                <div class="row">
                    <?php
                    if($visible != 'none'){
                    ?>
                            <div class="col-md-3 form-group">



                                <label><?php echo lang('razon_condicion');?>*</label>
                                <select name="condicion_fiscal_responsable" class="form-control" value="">
                                    <option></option>
                                    <?php
                                        foreach ($condicion as $value)
                                        {
                                            echo '<option value="'.$value['codigo'].'">'.$value['condicion'].'</option>';
                                        }
                                    ?>
                                </select>

                            </div>
                    <?php
                    }
                    ?>
                    <div class="col-md-6 form-group">
                        
                        <label><?php echo lang('relacion_alumno')?>*</label>
                        
                        <select name="relacion_alumno_responsable" class="form-control" value="">
                            <option></option>
                            <?php 
                            foreach($relacion_alumno as $key=>$relacion)
                            {
                                echo '<option value="'.$key.'">'.$relacion.'</option>';
                            }
                            ?>
                        </select>
                    
                    </div>
                    <div class="col-md-3 form-group">
                        <label><?php echo lang('codigo_postal')?></label>
                        <input name="codigo_postal_responsable" class="form-control input-sm">
                    </div>
                </div>
            </div>
           
            <input type="hidden" name="codigo_responsable" value="" >
           
        </form>
    
    </div>

