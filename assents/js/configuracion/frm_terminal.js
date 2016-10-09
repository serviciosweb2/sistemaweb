
var  langFRM = langFrm;
$('.fancybox-wrap').ready(function() {
initFRM();
//    var clavesFRM = Array("validacion_ok");
//
//    $.ajax({
//        url: BASE_URL + 'entorno/getLang',
//        data: "claves=" + JSON.stringify(clavesFRM),
//        type: "POST",
//        dataType: "JSON",
//        async: false,
//        cache: false,
//        success: function(respuesta) {
//            langFRM = respuesta;
//            initFRM();
//        }
//    });

    

});
function initFRM() {
        console.log('my lang',langFRM);
        $('.fancybox-wrap select').chosen({
            width: '100%',
        });

        $('#fecha_contrato').datepicker({
        });

        $('.btn-guardar').on('click', function() {

            $.ajax({
                url: BASE_URL + "configuracion/guardarTerminal",
                data: $("#frmProveedorPos").serialize(),
                type: "POST",
                dataType: "JSON",
                async: false,
                cache: false,
                success: function(respuesta) {
                    
                    switch (respuesta.codigo) {
                        case 0:
                            $.gritter.add({
                                title: 'Upss!',
                                text: respuesta.msgError,
                                sticky: false,
                                time: '3000',
                                class_name: 'gritter-error'
                            });

                            break;

                        case 1:
                            $.gritter.add({
                                title: 'Ok',
                                text: langFRM.validacion_ok,
                                sticky: false,
                                time: '3000',
                                class_name: 'gritter-success'
                            });

                            $.fancybox.close(true);

                            tablaTerminalesPos();

                            break;
                    }
                }
            });



        });
    }