function initFRM(){
    $('.fecha').datepicker();
    $('#modificarDetalle').on('submit',function(){
        if( $('input[name="fecha_inicio"]').val()==='' || $('input[name="fecha_fin"]').val()===''){
            gritter(langFRM.algun_campo_vacio);
        } else {
            $.ajax({
                url: BASE_URL+"certificados/guardarDetalles",
                type: "POST",
                data: $(this).serialize(),
                dataType:"JSON",
                cache:false,
                async: false,
                success:function(respuesta){
                    if(respuesta.codigo == 1){
                        var certificacion = $("[name=certificacion]").val();
                        if (certificacion == 'IGA'){
                            listar_certificacion_iga();
                        } else {
                            listar_certificacion_ucel();
                        }
                        $.fancybox.close(true);
                        gritter(langFRM.validacion_ok, true);
                    } else {
                        gritter(respuesta.respuesta);
                    }
                }
            });
        }
        return false;
    });
}

var langFRM = langFrm ;

$('.fancybox-wrap').ready(function(){
    initFRM();
});