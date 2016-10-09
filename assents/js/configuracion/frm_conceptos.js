var clavesFRM = Array("validacion_ok");

var langFRM = langFrm;

$('.fancybox-wrap').ready(function() {
 $("[name='impuestos_asignados[]']").chosen({width:'100%'});        
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
initFRM();

});

function initFRM() {
    $('.btn-guardar').on('click', function() {

        $.ajax({
            url: BASE_URL + "configuracion/guardarConcepto",
            data: $("#frmConcepto").serialize(),
            type: "POST",
            dataType: "JSON",
            async: false,
            cache: false,
            success: function(respuesta) {
                console.log(respuesta);
                
                switch (respuesta.codigo) {
                    case 0:
                        $.gritter.add({
                            title: 'Upss!',
                            text: respuesta.msgerror,
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

                        tablaConceptos();

                        break;
                }
            }
        });
    });
}