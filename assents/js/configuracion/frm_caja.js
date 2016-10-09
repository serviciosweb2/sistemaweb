//var clavesFRM = Array("validacion_ok");

var langFRM = langFrm;

$('.fancybox-wrap').ready(function() {



//    $.ajax({
//        url: BASE_URL + 'entorno/getLang',
//        data: "claves=" + JSON.stringify(clavesFRM),
//        type: "POST",
//        dataType: "JSON",
//        async: false,
//        cache: false,
//        success: function(respuesta) {
//            //langFRM = respuesta;
//            initFRM();
//        }
//    });

    initFRM();

});


function initFRM() {
    $('.btn-guardar').on('click', function() {

        $.ajax({
            url: BASE_URL + "caja/guardarCaja",
            data: $("#frmCaja").serialize(),
            type: "POST",
            dataType: "JSON",
            async: false,
            cache: false,
            success: function(respuesta) {
                switch (respuesta.codigo) {
                    case '0':
                        $.gritter.add({
                            title: 'Upss!',
                            text: respuesta.respuesta,
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


                        $.fancybox.close();

                        tablaCajas('activo');
                        tablaCajas('inactivo');

                        break;
                }
            }
        });



    });


}

function habilitarMedio(dato) {
    $('#tablaMedios select').each(function(k, item) {
        if ($(item).attr('medio') == $(dato).attr('medio')) {
            if ($(item).attr('disabled')) {
                $(item).attr('disabled', false);
            } else {
                $(item).attr('disabled', true);
            }
        }
    });

    $('#tablaMedios .confir').each(function(k, item2) {
        if ($(item2).attr('medio') == $(dato).attr('medio')) {
            if ($(item2).attr('disabled')) {
                $(item2).attr('disabled', false);
            } else {
                $(item2).attr('disabled', true);
            }
        }
    });


}
