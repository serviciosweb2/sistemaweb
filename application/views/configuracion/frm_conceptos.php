<script>
    var langFrm = <?php echo $langFrm ?>;
</script>
<script src="<?php echo base_url('assents/js/configuracion/frm_conceptos.js') ?>"></script>
<style>

    .checkbox{

        padding: 0px !important;
        margin: 0px !important;
    }

</style>
<div class="modal-content">
    <div class="modal-header">
        <h4 class="blue bigger">
            <?php echo lang('nuevo_concepto'); ?>

        </h4>
    </div>
    <form id="frmConcepto">
        <input type="hidden" name="codigo" value='<?php echo $concepto->getCodigo() ?>' />
        <div class="modal-body overflow-visible">
            <div class="row">

                <div class="col-md-12 col-xs-12">   
                    <div class="row">
                        <div class="col-md-12 form-group">

                            <label><?php echo lang('nombre') ?></label>
                            <input class="form-control" name="nombre"  value='<?php echo $concepto->key ?>' />
                            <br>
                            <label><?php echo lang('impuestos');?></label><br>
                            <select name="impuestos_asignados[]" multiple="true" data-placeholder="Asigne Impuestos al Concepto">
                                <option></option>
                                <?php
                                foreach ($impuestos as $impuesto){
                                    $selected = '';
                                    foreach($impuestosAsignados as $impAsignado){
                                        if($impuesto['codigo'] == $impAsignado['cod_impuesto']) {
                                            $selected = 'selected';
                                            break;
                                        }
                                    }
                                    echo '<option value='.$impuesto['codigo'].' '.$selected.'>'.$impuesto['nombre'].' '.$impuesto['valor'].'</option>';
                                }
                                ?>
                            </select>
                        </div>

                    </div>

                </div>    
            </div>
        </div>
    </form>
    <div class="modal-footer">


        <button class="btn btn-sm btn-primary btn-guardar ">
            <i class="icon-ok"></i>
            <?php echo lang('guardar') ?>
        </button>
    </div>
</div>
