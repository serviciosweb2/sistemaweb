<link rel="stylesheet" href="<?php echo base_url('assents/css/colorpicker/bootstrap-colorpicker.min.css')?>"/>
<script src="<?php echo base_url('assents/js/librerias/colorpicker/bootstrap-colorpicker.js');?>"></script>
<link rel="stylesheet" href="<?php echo base_url('assents/theme/assets/css/colorpicker.css')?>"/>
<script>
$('.fancybox-wrap').ready(function()
{
     
   $('.alert').hide();
    var valor=$('input[name="color"]').val();  
//    var objColor=$('.demo2').colorpicker({
//        color:valor,
//        format:'hex'
//    });   
    $('#simple-colorpicker-1').ace_colorpicker();
    $('.fancybox-wrap').on('submit','#nuevoSalon',function(){
        var dataPOST=$(this).serialize();
        $.ajax({
            url:BASE_URL+'horarios/guardarSalon',
            data:dataPOST,
            type:'POST',
            cache:false,
            dataType:'JSON',
            success:function(respuesta){
                if(respuesta.codigo===1){                            
                    $.fancybox.close(true);
                    $.gritter.add({
                        title: langFRM.BIEN,
                        text: langFRM.SALON_GUARDADO_CORRECTAMENTE ,
                        sticky: false,
                        time: '3000',
                        class_name: 'gritter-success'
                    });
                    location.reload();
                } else {                        
                    $('.alert').html(respuesta.msgerror).fadeIn();                        
                    $.fancybox.update();
                }                
            }            
        });
        return false;
    });
    
    $('.fancybox-wrap').on('change','select[name="tipo"]',function(){
        var tipo = $(this).val();
        $.ajax({
            url:BASE_URL+'horarios/retornoColorNuevoSalon',
            data:'tipo='+tipo,
            type:'POST',
            cache:false,
            dataType:'JSON',
            success:function(respuesta){
                  $('.btn-colorpicker').css('background-color',respuesta);           
            }            
        });
    });
    
    $('#nuevoSalon').on('click','.dropdown-colorpicker',function(){
        return false;
    });
});
</script>
<div class="modal-content">
    <div class="modal-header">
        <h3 class="blue bigger"><?php echo $salones->getCodigo()==-1 ? lang('nuevo-salon') : lang('modificar-salon');?>
            <small></small>
        </h3>
    </div>
    <form id="nuevoSalon">
        <div class="modal-body overflow-visible">
            <input type="hidden"  name="codigo" value="<?php echo $salones->getCodigo() ?>">
            <div class="form-group">
                <label for="exampleInputEmail1"><?php echo lang('nombre');?>:</label>
                <input type="" class="form-control" name="salon" placeholder="<?php echo lang('nombre');?>" value="<?php echo isset ($salones) ? $salones->salon : ''?>">
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1"><?php echo lang('cupo');?>:</label>
                <input type="" class="form-control" name="cupo" placeholder="<?php echo lang('cupo');?>" value="<?php echo isset($salones) ? $salones->cupo :''?>">
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1"><?php echo lang('tipo_salon');?>:</label>
                <select  class="form-control" name="tipo" data-placeholder="Selecione Salon">
                    <option></option>
                    <?php 
                    $objTipo=isset ($salones) ? $salones->tipo : '';
                    foreach($tipos_salones as $tipo){
                        $selected= $objTipo==$tipo["codigo"] ? 'selected' :'';
                        echo '<option value="'.$tipo["codigo"].'"  '.$selected.' >'.$tipo["nombre"].'</option>';
                    } ?>
                </select>
            </div>
<!--            <div class="form-group">
                <label for="exampleInputEmail1"><?php echo lang('color');?>:</label>
                <div class="input-group demo2">
                    <input type="text"  class="form-control" value="<?php echo isset($salones) ? $salones->color  :''?>" name="color" placeholder="<?php echo lang('elija_color')?>">
                    <span class="input-group-addon"><i></i></span>
                </div>
            </div>-->
                                        <div>
                                            <label for="simple-colorpicker-1"><?php echo lang('color');?></label>

                                                    <select name="color" id="simple-colorpicker-1" class="hide">
                                                            <?php 
                                                             
                                                                foreach($colores as $key=>$color){
                                                                    $selected = '';
                                                                    if($salones->getCodigo() != -1 && $salones->color ==$key){
                                                                        $selected = 'selected';
                                                                    }    
                                                                    echo '<option value='.$key.' '.$selected.'>'.$color.'</option>';
                                                                }
                                                            ?>

                                                    </select>
                                        </div>
<br>
            <div class="row">
                <div class="alert alert-danger"></div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn  btn-success" type="submit"  value="guardar">
                <i class="icon-ok bigger-110"></i>
                <?php echo lang('guardar');?>
            </button>
        </div>
    </form>
</div>