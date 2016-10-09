<?php
    $filial = $this->session->userdata('filial');
    $this->load->helper('datepicker');
    $this->load->helper('formatearfecha');    
    $nacimientoAlumno = $alumno->fechanaci==' ' ? '' : $alumno->fechanaci;
    $verTabResponsables = 0; 
    if($nacimientoAlumno != ''){
        $dias = explode('-',$nacimientoAlumno,3);
        $dias = mktime(0,0,0,$dias[2],$dias[1],$dias[0]);
        $edad = (int)((time()-$dias)/31556926 );
        if($edad >= $anios_mayoria_edad){
            $verTabResponsables = 0;
        } else {
            $verTabResponsables = 1;
        }
    } else {
       $edad = 0; 
    } ?>
<script src="<?php echo base_url('assents/js/librerias/jquery-serialize/jquery.serializeJSON.min.js');?>"></script>
<script src="<?php echo base_url('assents/js/librerias/jquery-serialize/jquery.serializeJSON.min.js');?>"></script>
<script src="<?php echo base_url('assents/js/chosen.jquery.js');?>"></script>
<script src="<?php echo base_url('assents/js/librerias/datatables/ColReorderWithResize.js');?>"></script>
<script src="<?php echo base_url('assents/theme/assets/js/jquery.maskedinput.min.js');?>"></script>
<script src="<?php echo base_url('assents/js/generalTelefonos.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assents/js/webcam/jquery.webcam.min.js'); ?>"></script>
<script>
    var langFrm = <?php echo $langFrm?>;
    var responsables = <?php echo json_encode($responsables)?>;
    var condiciones = <?php echo json_encode($condicion)?>;
    var tipo_identificacion = <?php echo json_encode($tipo_identificacion)?>;
    var empresas_tel = <?php echo json_encode($empresas_tel)?>;
    var tipo_telefono = <?php echo json_encode($tipo_telefono) ?>;
    var telefonos = <?php echo json_encode($telefonos)?>;
    var relacion_alumno = <?php echo json_encode($relacion_alumno)?>;
    var verTabResponsables = <?php echo $verTabResponsables; ?>;
    var mayoriaEdad = <?php echo $anios_mayoria_edad ?>;
    var codigoAlumno = <?php echo $alumno->getCodigo() ?>;
    var edadAlumno = <?php echo $edad ?>;
    var pais = '<?php echo $pais?>'; 
</script>

<link rel="stylesheet" href="<?php echo base_url('assents/css/alumnos/form_alumno.css');?>"/>
<script src="<?php echo base_url('assents/js/alumnos/frm_alumno.js');?>"></script>
<input type='hidden' name='codigo_alumno' value='<?php echo $alumno->getcodigo(); ?>'>
<input type='hidden' name='tipo_telefono' value='<?php echo json_encode($tipo_telefono); ?>'>    
<input type='hidden' name='telefonos' value='<?php echo json_encode($telefonos); ?>'>    
<input type='hidden' name='razones' value='<?php echo json_encode($razones); ?>'>  
<?php function setTelDefault($telefonos,$empresas_tel,$tipo_telefono){        
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
<div name="area_razones_sociales_temporal" class="modal fade" data-width="80%" tabindex="-1" data-backdrop="static" data-keyboard="false"></div>
<div id="telefonosAlumno" class="modal fade" data-width="80%" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-header">
        <h3><?php echo lang("telefonos"); ?></h3>
    </div>
    <div class="modal-body">      
        <div class="row">
            <div id="test" class="" style="display: none">
                <form id="editTel">
                    <div class="row">
                        <div class="form-group col-xs-12">
                            <label><?php echo lang('datos_empresa')?></label>
                            <select class="form-control"></select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12">
                            <label><?php echo lang('tipo_telefono')?></label>
                            <select class="form-control"></select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12">
                            <label><?php echo lang('codarea')?>-</label>
                            <input class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12">
                            <label><?php echo lang('numero')?></label>
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
        <button type="button" id="btn-ok-cambio-estado" onclick="cerrarFrmTelAlumnos();" class="btn btn-primary"><?php echo lang('continuar')?></button>
    </div>
</div>

<!--DETALLE TALLE-->
<div id="detalle_talle_alumno" class="modal fade" data-width="50%" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3><?php echo lang("detalle"); ?></h3>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-6">
                    <img id="uniforme_talle" src="<?php echo base_url('assents/img/Talle.jpg')?>">
                </div>
                <div class="col-md-6">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <th><?php echo lang('datos_talle');?></th>
                                <th><?php echo lang('ancho');?> (A)</th>
                                <th><?php echo lang('largo');?> (B)</th>
                                <th><?php echo lang('equivalente');?></th>
                            </thead>                    
                            <tbody>
                                <?php foreach($tallesPais as $key => $talle){ ?> 
                                <tr>
                                    <td>
                                        <button class="btn btn-link" onclick="asignar_talle(<?php echo $key ?>)">
                                            <?php echo $talle['talle'] ?>
                                        </button>
                                        </td>
                                    <td><?php echo $talle['propiedad']['ancho'] ?></td>
                                    <td><?php echo $talle['propiedad']['largo'] ?></td>
                                    <td><?php echo $talle['propiedad']['equivalente'] ?></td>
                                </tr>                                        
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" id="btn-ok-cambio-estado" class="btn btn-primary"><?php echo lang('continuar')?></button>
    </div>
</div>

<!--FORM TELEFONOS RESPONSABLES-->
<div id="telefonosResponsable" class="modal fade" data-width="50%" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-header">
        <h3><?php echo lang("telefonos"); ?></h3>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="table-responsive responsiveResponsable">          
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button"  id="btn-ok-cambio-estado" class="btn btn-primary" onclick="cerrarFrmTelResponsables();"><?php echo lang('continuar')?></button>
    </div>
</div>

<!--FORM RESPONSABLE-->
<div id="frm_responsable" class="modal fade" data-width="90%" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3><?php echo lang("responsables"); ?></h3>
    </div>
    <div class="modal-body">
        <?php  $this->load->view('responsables/frm_responsable')?>
    </div>
    <div class="modal-footer">
        <button type="button" id="btn-guardar-responsable" onclick="guardarResponsable();" class="btn btn-primary" >Relacionar</button>
    </div>
</div>

<!--FORM LISTAR RESPONSABLES-->
<div id="frm_listar_responsables" class="modal fade" data-width="90%" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="vaciarArrayRespSeleccionados();">×</button>
        <h3><?php echo lang("responsables"); ?></h3>
    </div>
    <div class="modal-body">
        <div class="row">
            <div clas="col-md-12">
                <table id="tabla_listar_responsables" class="table table-striped table-condensed  table-bordered">
                    <thead>                        
                    </thead>
                    <tbody>                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" id="btn-listar-relacionar-responsables" data-dismiss="modal" onclick="relacionarResponsablesListar();" class="btn btn-primary" > <?php echo lang('relacionar');?> </button>
    </div>
</div>
<div class="modal-content" style="min-width: 100% !important">
    <div class="modal-header">
        <?php $textoHeader = lang('modificar_alumno');
            if($alumno->getcodigo() == -1){
                $textoHeader = lang('nuevo_alumno');
            } ?>
        <h4 class="blue bigger"><?php echo $textoHeader;?></h4>
    </div>
    <div class="modal-body overflow-visible" style="padding-bottom: 0px;">
        <div class="row">
            <div class="tabbable">
                <ul class="nav nav-tabs" id="mitab">
                    <li class="active">
                        <a href="#tab1" data-toggle="tab"><?php echo lang('tab_datos')?></a>
                    </li>
                    <li>
                        <a href="#tab2" data-toggle="tab"><?php echo lang('tab_responsable')?></a>
                    </li>
                    <li>
                        <a href="#tab3" data-toggle="tab"><?php echo lang('razon_social')?></a>
                    </li>
                    <li style="display:none !important">
                        <a href="#tab4" data-toggle="tab"><?php echo lang('tab_datosAdicionales')?></a>
                    </li>
                    <li>
                        <a href="#tab5" data-toggle="tab">Foto</a>
                    </li>
                </ul>
                <div class="tab-content" >                    
                    <!--ALUMNO-->
                    <div class="tab-pane active " id="tab1">
                        
                        <form id="general">
                            <div class="row">
                                <div class="col-md-6 col-xs-12 ">
                                    <div class="row">
                                        <div class="form-group col-md-4 col-xs-12"> 
                                            <label><?php echo lang('nombre')?>*</label>
                                            <input id="element_focus" class="form-control input-sm color_form" type="text" name="nombre" value="<?php echo isset($alumno->nombre) ? $alumno->nombre : '';?>" tabindex="1" onchange="buscarAspirante();">
                                        </div>
                                        <div class="form-group col-md-4 col-xs-12"> 
                                            <label><?php echo lang('apellido')?>*</label>
                                            <input class="form-control input-sm color_form" type="text" name="apellido" value="<?php echo isset($alumno->apellido) ? $alumno->apellido : '';?>" tabindex="2" onchange="buscarAspirante();">
                                        </div>
                                        <div class="form-group col-xs-12 col-md-4 sexo">
                                            <label><?php echo lang('sexo')?>*</label>
                                            <select class="form-control" name="sexo" data-placeholder="<?php echo lang('seleccionar_sexo');?>" tabindex="3">
                                                <option></option>
                                                <?php foreach($sexo as $valor){
                                                    $selected= $valor['id'] == $alumno->sexo ? 'selected' : '';
                                                    $nombreSexo= $valor['id']=='Masculino' ? lang('masculino'): lang('femenino');
                                                    echo '<option value="'.$valor['id'].'" '.$selected.'>'.$nombreSexo.'</option>';
                                                } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row fila_2_izquierda">
                                        <div class="col-md-3 form-group">
                                            <label><?php echo lang("pais"); ?></label>
                                            <select class="form-control input-sm" name="domicilioPais">
                                                <?php foreach ($paises as $pais){ ?> 
                                                <option value="<?php echo $pais['id'] ?>"
                                                        <?php if ($pais['id'] == $pais_seleccionar){ ?>selected="true"<?php } ?>>
                                                    <?php echo $pais['pais'] ?>
                                                </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3 form-group"> 
                                            <label><?php echo lang('provincia');?>*</label>
                                            <select class="form-control input-sm" name="domiciProvincia" data-placeholder="<?php echo lang('seleccionar_provincia');?>" tabindex="7">
                                                <option></option>
                                                <?php foreach($prov as $list_prov){ ?>
                                                <option value="<?php echo $list_prov['id'] ?>"
                                                        <?php if ($list_prov['id'] == $provincia_alumno){ ?>selected="true"<?php } ?>>
                                                    <?php echo $list_prov['nombre']; ?>
                                                </option>
                                                <?php } ?>
                                            </select>
                                        </div>                                        
                                        <div class="col-md-3 form-group"> 
                                            <label><?php echo lang('localidad')?>*</label>
                                            <?php
                                            $selectLocalidad = '';
                                            if($alumno->getcodigo() == -1){
                                                $selectLocalidad = 'disabled';
                                            } ?>
                                            <select class="form-control input-sm" name="domiciLocalidad" data-placeholder="<?php echo lang('seleccionar_localidad');?>" tabindex="8" 
                                                <?php if ($alumno->getCodigo() == -1 && 
                                                ($cod_aspirante == null || $$cod_aspirante < 0)){ ?>disabled="true"<?php } ?>>
                                                <option></option>
                                                <?php foreach($localidades as $lista_loc){ ?>
                                                <option value="<?php echo $lista_loc['id']; ?>"
                                                        <?php if ($lista_loc[ 'id'] == $alumno->id_localidad){ ?>selected="true"<?php } ?>>
                                                    <?php echo $lista_loc['nombre']; ?>
                                                </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3 form-group"> 
                                            <label><?php echo lang('domicilio')?>*</label>
                                            <input class="form-control input-sm color_form" type="text" name="calle_alumno" value="<?php echo $alumno->calle=='' ? '' : $alumno->calle; ?>" tabindex="9">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-xs-12 form-group"> 
                                            <label><?php echo lang('email')?>*</label>
                                            <input class="form-control input-sm color_form" name="email_alumno"  value="<?php echo $alumno->email?>"tabindex="14" type="text">
                                        </div>                                    
                                        <div class="col-md-8 col-xs-12 form-group">
                                            <div class="row fila_telefono">                                            
                                                <div class="col-md-4 no-padding-right  form-group">
                                                    <label> &nbsp;  &nbsp; &nbsp;</label>
                                                    <select id="id_tipo_telefono" class="form-control" onchange="actualizarTelDefaultEnLista();">
                                                    <?php foreach($tipo_telefono as $tipo){ ?> 
                                                        <option value="<?php echo $tipo['id'] ?>">
                                                        <?php echo $tipo['nombre']; ?>
                                                        </option>
                                                    <?php } ?>
                                                    </select>
                                                    <span class="text_ayuda help-inline" >
                                                        <span class="celular smaller hide"><?php echo lang('celular_sin_15');?></span>
                                                        <span class="fijo smaller"><?php echo lang('fijo_con_o_sin_0'); ?></span>
                                                    </span>
                                                </div>

                                                <div class="col-md-6  no-padding form-group">
                                                    <label><?php echo lang('telefono')?>
                                                        <span class="help-button hide" data-rel="popover" data-trigger="hover" data-placement="left" data-content=" " style="height: 18px; width: 18px; line-height: 15px;">?</span>
                                                    </label>
                                                    <input name="telefono_alumno" type="tel" class="form-control input-sm color_form" value="" style="width: 218px !important;">
                                                </div>
                                                <div class="col-md-1 form-group pull-right">
                                                    <label> &nbsp; </label>
                                                    <a href="javascript:void(0)" class="btn btn-sm btn-primary pull-right" style="height: 30px !important;" onclick="mostrarFrmTelAlumno();">+</a>
                                                </div>

                                                <?php if(isset($filial['pais']) && $filial['pais'] == 2){ ?>
                                                <div class="col-md-12 ">
                                                    <div class="row">
                                                        <div class="col-md-7 no-padding-right select_empresa_telefono form-group" style="display: none;"><!-- style="display: none;">-->
                                                            <label><?php echo lang('empresa_celular') ?></label>
                                                            <select id="id_empresa_telefono" class="form-control" onchange="actualizarTelDefaultEnLista();">
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
                                <div class="col-md-6 col-xs-12 ">
                                    <div class="row fila_documento">
                                         <div class="col-md-4 col-xs-12 form-group"> 
                                            <label><?php echo lang('tipo_documento')?>*</label>
                                            <select class="form-control input-sm" data-placeholder="<?php echo lang('seleccionar_tipo_doc');?>" name="tipoDniAlumno" tabindex="4">
                                                <option></option>
                                                <?php foreach($tipo_dni as $valor){ ?>
                                                <option value="<?php echo $valor['codigo']; ?>" 
                                                    <?php if ($valor['codigo'] == $alumno->tipo){ ?>selected="true"<?php } ?>>
                                                    <?php echo $valor['nombre']; ?>
                                                </option>
                                                <?php } ?>
                                            </select>
                                        </div>                                    
                                        <div class="col-md-4 col-xs-12 form-group"> 
                                            <label><?php echo lang('numero')?>*</label>
                                            <input class="form-control input-sm color_form" type="text" name="documento" value="<?php echo ($alumno->documento=='') ? '' : $alumno->documento ?>" tabindex="5">
                                        </div>
                                        <div class="col-md-4 col-xs-12 form-group"> 
                                            <label ><?php echo lang('fecha_nacimiento')?>*</label>
                                            <input type="text" class="form-control input-sm color_form" name="fechanaci"  value="<?php echo ($alumno->fechanaci==' ' || $alumno->fechanaci == '')  ? '' : formatearFecha_pais($alumno->fechanaci); ?>" tabindex="6">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-2 form-group"> 
                                            <label><?php echo lang('calle_numero')?>*</label>
                                            <input class="form-control input-sm color_form" name="calle_num_alumno" type="text" value="<?php echo $alumno->calle_numero=='' ? '' : $alumno->calle_numero ;?>" tabindex="10">
                                        </div>
                                        <div class="col-md-4 form-group"> 
                                            <label><?php echo lang('calle_complemento')?></label>
                                            <input class="form-control input-sm" name="complemento_alumno" type="text" value="<?php echo $alumno->calle_complemento=='' ? '' : $alumno->calle_complemento  ?>" tabindex="11">
                                        </div>
                                        <div class="col-md-4 form-group"> 
                                            <label>
                                                <?php if($filial['pais'] != 2)
                                                {
                                                    echo lang('datos_barrio');
                                                }
                                                else
                                                {
                                                    echo lang('datos_barrio')."*";
                                                }
                                                ?>
                                            </label>
                                            <input type="hidden" id="pais" value="<?php echo $filial['pais'] ?>">
                                            <input type="text" id="barrio" name="barrio" class="form-control input-sm" value="<?php echo $alumno->barrio=='' ? '' : $alumno->barrio; ?>" tabindex="13">
                                        </div>
                                        <div class="col-sm-2 form-group"> 
                                            <label><?php echo lang('codigo_postal')?>*</label>
                                            <?php ?>
                                            <input type="text" name='codpost' class="form-control input-sm color_form" value="<?php echo $alumno->codpost=='' ? '' : $alumno->codpost; ?>" tabindex="12">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3 col-xs-12 form-group"> 
                                            <label><?php echo lang('datos_estadoCivil')?></label>
                                            <select class="form-control input-sm" name="estado_civil" data-placeholder="<?php echo lang('seleccionar_estado_civil');?>">
                                                <option></option>
                                                <?php foreach($estado_c as $valor){ ?> 
                                                <option value="<?php echo $valor['id'] ?>"
                                                        <?php if ($valor[ 'id'] == $alumno->estado_civil){ ?>selected="true"<?php } ?>>
                                                    <?php echo $valor['nombre']; ?>
                                                </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4 col-xs-12 form-group chosenDropUp fila_chosen_requerido">
                                            <label><?php echo lang('como_nos_conocio')?>*</label>
                                            <select <?php echo ($alumno->comonosconocio?'disabled':'');?> class="form-control" name="comonosconocio" data-placeholder="<?php echo lang('seleccionar_medio');?>" tabindex="16">
                                                <option></option>
                                                <?php foreach($comonoscon as $lista){ ?>
                                                <option value="<?php echo $lista['codigo']; ?>"
                                                        <?php if ($lista[ 'codigo']==$alumno->comonosconocio){ ?>selected="true"<?php } ?>>
                                                    <?php echo $lista['nombre']; ?>
                                                </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3 col-xs-12 form-group fila_chosen_requerido"> 
                                            <label><?php echo lang('datos_talle')?>*</label>
                                            <select name="talle" class="form-control input-sm" data-placeholder="<?php echo lang('seleccionar_talle');?>">
                                                <option></option>
                                                <?php foreach($tallesPais as $id_talle => $valor){ ?>
                                                <option value="<?php echo $id_talle ?>"
                                                        <?php if ($id_talle == $alumno->id_talle){ ?>selected="true"<?php } ?>>
                                                    <?php echo $valor["talle"]; ?>
                                                </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-2 col-xs-12 form-group">
                                            <br>
                                            <div>
                                                <a id="detalle_talles_alumno" href="javascript:void(0)"><?php echo lang('ver_detalle');?></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>                    
                    <!--*RESPONSABLES-->
                    <div class="tab-pane" id="tab2">
                        <div class="row">
                            <div class="col-md-2">
                                <?php if ($permiso_nuevo_responsable){ ?>
                                <button id="nuevoResponsable"  style="margin-bottom:3%" class="btn btn-primary"><?php echo lang('nuevo_responsable')?></button>
                                <?php } ?>
                            </div>
                            <div class="col-md-4 col-md-offset-2">
                                <button id="utilizarUnResponsableExistente"  onclick="frmListarResponsable();" style="margin-bottom:3%" class="btn btn-primary"> <?php echo lang('seleccionar_responsable');?> </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <form id="listadoResponsables">
                                    <div class="table-responsive">
                                        <table id="tablaresponsables" class="table table-bordered" style="width: 1000px !Important;">
                                            <thead></thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </form> 
                            </div>
                        </div>
                    </div>
                    <!--*RAZONES SOCIALES-->
                    <div class="tab-pane" id="tab3">
                        <div class="table-responsive mainContentRazones"></div>
                    </div>                    
                    <!--*DATOS ADICIONALES-->
                    <div class="tab-pane" id="tab4">
                        <form id="datosAdicionales">                            
                            <div class="row">
                                <div class="col-md-3 form-group"> 
                                    <label><?php echo lang('provincia_de_nacimiento')?>*</label>
                                    <select class="form-control input-sm" name="prov" id="prov" data-placeholder="<?php echo lang('seleccionar_provincia');?>">
                                        <option></option>
                                        <?php foreach($prov as $list_prov){ ?>
                                        <option value="<?php echo $list_prov['id'] ?>"
                                                <?php if ($list_prov['id']==$provincia_nacimiento){ ?>selected="true"<?php } ?>>
                                            <?php echo $list_prov[ 'nombre']; ?>
                                        </option>   
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-3 form-group"> 
                                    <label><?php echo lang('localidad_de_nacimiento')?>*</label>
                                    <select class="form-control input-sm" name='localidad' data-placeholder="<?php echo lang('seleccionar_localidad');?>">
                                        <option></option>
                                        <?php if($localidades_nacimiento!='' ){ 
                                                foreach($localidades_nacimiento as $lista_loc){ ?>
                                        <option value="<?php echo $lista_loc['id'] ?>"
                                                <?php if ($lista_loc[ 'id'] == $alumno->id_lugar_nacimiento){ ?>selected="true"<?php } ?>>
                                            <?php echo $lista_loc['nombre']; ?>
                                        </option>                                                    
                                        <?php } } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">                                
                                <div class="col-md-12 form-group"> 
                                    <label><?php echo lang('observaciones')?></label>
                                    <textarea name="observaciones" class="form-control"><?php echo $alumno->observaciones;?></textarea>
                                    <input type="hidden" name="codigoAspirante" value="<?php echo $cod_aspirante;?>">
                                    <input type="hidden" name="codigo" value="<?php echo $alumno->getcodigo();?>">
                                </div>
                            </div>
                        </form>
                    </div> 
                    <div class="tab-pane" id="tab5">
                        <center>                            
                            <table>
                                <tr>
                                    <td style="text-align: center;">WEBCAM</td>
                                    <td style="text-align: center;">PREVIEW</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: middle;">
                                        <div id="webcam"></div>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <input type="hidden" value="" name="imagen_base64">
                                        <img src="<?php echo str_replace(' ', '+', $imagen_alumno); ?>" width="320" height="240" id="imagen_preview">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: center;">
                                        <a href="javascript:webcam.capture();changeFilter();void(0);">Capturar</a>
                                    </td>
                                    <td>&nbsp;</td>
                                </tr>
                            </table>
                        </center>
                    </div>
                </div>            
            </div>
        </div>
    </div>
    <div style="padding-left: 20px; margin-top: 6px; font-size: 10px; color: red; height: 20px; vertical-align: middle; width: 100%;" name="aspirantes_encontrados"></div>
    <div style="margin-top: 6px; font-size: 10px; height: 20px; vertical-align: middle; width: 100%; text-align: center; display: none;" name="descartar_aspirante">
        <span style="cursor: pointer;" onclick="descartar_relacion_aspirante();">
            <b><?php echo lang('descartar_seleccion_de_aspirante'); ?></b>
            <label name="detalle_aspirante_seleccionado" style="font-size: 10px; font-weight: normal; padding-top: 4px;"></label>
        </span>
    </div>
    <div class="modal-footer">
        <button name="enviarForm" class="btn btn-sm btn-primary" type="">
            <i class="icon-ok"></i>
            <?php echo lang('guardar')?>
        </button>
    </div>
</div>
