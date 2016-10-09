<script>
    var langFrm = <?php echo $langFrm ?>;
</script>
<script src="<?php echo base_url('assents/js/configuracion/frm_moras_cursos_cortos.js')?>"></script>
<?php

//echo '<pre>';
//print_r($objMora);
//echo '</pre>';

$unidades=array(
    array('codigo'=>1,'simbolo'=>'%'),
    array('codigo'=>0,'simbolo'=>'$')
);

$moracodigo='-1';
$unidadSetiada='-1';
$mora='';
$diariamente='';
$checkedBaja='';
$dia_desde='';
$dia_hasta='';

if (isset($objMora)){
    $moracodigo=$dia_desde=$objMora->getCodigo();
    $dia_desde=$objMora->dia_desde;
    $dia_hasta=$objMora->dia_hasta;
    $unidadSetiada= $objMora->es_porcentaje ;
    $mora= $objMora->mora;
    $diariamente=$objMora->diariamente;
    $checkedBaja=$objMora->baja;
    $tipo_mora = $objMora->tipo;
}




?>



<div class="modal-content">
    <form id="frmMorasCursosCortos">
        <div class="modal-header">
            <h4 class="blue bigger"><?php echo lang('nueva_mora');?></h4>
        </div>

        <div class="modal-body overflow-visible">
            <div class="row">

                <div class="form-group col-md-6 col-xs-12">
                    <label><?php echo lang('dia_desde');?></label>
                    <input class="form-control" value="<?php echo $dia_desde?>" name="dia_desde">
                </div>

                <div class="form-group col-md-6 col-xs-12">
                    <label><?php echo lang('dia_hasta');?></label>
                    <input class="form-control" value="<?php echo $dia_hasta?>" name="dia_hasta">
                </div>




            </div>
            <div class="row">

                <div class="form-group col-md-6 col-xs-12">
                    <label><?php echo lang('unidad');?></label>
                    <select data-placeholder="<?php echo lang('seleccione_unidad');?>" name="es_porcentaje">
                        <option></option>
                        <?php

                        foreach($unidades as $unidad){

                            $selected= $unidadSetiada==$unidad['codigo'] ? 'selected' : '';

                            echo '<option value="'.$unidad['codigo'].'" '.$selected.'>'.$unidad['simbolo'].'</option>';

                        }
                        ?>
                    </select>
                </div>
                <div class="form-group col-md-6 col-xs-12">
                    <label><?php echo lang('valor');?></label>
                    <input class="form-control" value="<?php echo $mora?>" name="mora">
                </div>


            </div>
            <div class="row">
                <div class="form-group col-md-6 col-xs-12">
                    <label><?php echo lang('tipo_mora');?></label>
                    <select id="tipo_mora" data-placeholder="<?php echo lang('seleccione');?>" name="tipo_mora">

                        <?php

                        echo '<option></option>';
                        foreach($tipo_moras as $key=>$tipoMora){
                            $selected = '';
                            if($tipo_mora == $key){
                                $selected = 'selected';
                            }
                            echo '<option value='.$key.' '.$selected.'>'.$tipoMora.'</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6 col-xs-12">
                    <label>
                        <input name="diariamente" class="ace ace-switch ace-switch-6" type="checkbox" <?php echo  $diariamente==1 ? 'checked' : ''?>>
                        <span class="lbl"> &nbsp; <?php echo lang('diariamente');?></span>
                    </label>
                </div>
                <div class="form-group col-md-6 col-xs-12">

                    <label>
                        <input name="baja" class="ace ace-switch ace-switch-6" type="checkbox" <?php echo $checkedBaja==0 ? 'checked' : '' ?>>
                        <span class="lbl"> &nbsp; <?php echo lang('HABILITADA');?></span>
                    </label>

                </div>
            </div>
        </div>

        <div class="modal-footer">
            <input name="codigo" value="<?php echo $moracodigo?>" type="hidden">
            <button class="btn btn-sm btn-primary" type="submit">
                <i class="icon-ok"></i>
                <?php echo lang('guardar')?>
            </button>
        </div>
    </form>
</div>