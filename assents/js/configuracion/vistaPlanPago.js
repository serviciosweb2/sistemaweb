var lang = BASE_LANG;
var periodicidad = '';
alertAJAXSTART = 0;

//configuracion/guardarConfiguracionDescuentos

function guardarConfiguracionDescuentos(nombre) {


    //var nombre = $(element).attr('name');

    var valor = 0;
    if ($('#descuentosCondicionados').is(':checked')) {

        valor = 1;
        $('#requisitos').removeClass('hide');

    } else {
        $('#requisitos').addClass('hide');
    }

    var dias = $('select[name="dias_prorroga"]').val();
    $.ajax({
        url: BASE_URL + "configuracion/guardarConfiguracionDescuentos",
        type: "POST",
        data: "nombre=" + nombre + "&valor=" + valor + "&dias_prorroga=" + dias,
        dataType: "JSON",
        cache: false,
        success: function(respuesta) {

            if (respuesta.codigo == 1) {

                $.gritter.add({
                    text: lang.validacion_ok,
                    image: '',
                    sticky: false,
                    time: '1500',
                    class_name: 'gritter-success'
                });
                
                if ($('#descuentosCondicionados').is(':checked')) {

                    $('#requisitos').removeClass('hide');

                } else {
                    $('#requisitos').addClass('hide');
                }

            } else {

                gritter('_ocurrio un error');

            }
        }
    });


}

function guardarValorPeriodicidad(element) {

    //alert($(element).attr('name'));

    var nombre = $(element).attr('name');

    var valor = $(element).val();

    var baja = 0;

    var type = $(element).attr('type');



    if (!$(element).is(':checked') && type == 'checkbox') {

        baja = 1;
    }


    $.ajax({
        url: BASE_URL + "planespago/guardarConfiguracionPeriodicidad",
        type: "POST",
        data: "codigo=" + valor + '&baja=' + baja + '&valor=',
        dataType: "JSON",
        cache: false,
        success: function(respuesta) {
            if (respuesta.codigo == 1) {

                $.gritter.add({
                    text: lang.validacion_ok,
                    image: '',
                    sticky: false,
                    time: '1500',
                    class_name: 'gritter-success'
                });

                crearTablaPeriodicidad();
            } else {

                $.gritter.add({
                    text: respuesta.msgerror,
                    image: '',
                    sticky: false,
                    time: '1500',
                    class_name: 'gritter-error'
                });

            }
        }
    });


}

function crearTablaPeriodicidad() {

    $.ajax({
        url: BASE_URL + "configuracion/periodicidadPago",
        type: "POST",
        data: "",
        dataType: "JSON",
        cache: false,
        success: function(respuesta) {

            periodicidad = respuesta;

            var tabla = '<table id="tablaperiodicidad" class="table table-striped"><thead><th>' + lang.planpago_periodo + '</th><th></th></thead>';

            tabla += '<tbody>';

            var i = 0;

            $(respuesta).each(function(k, elemento) {

                var selected = elemento.baja == 0 ? 'checked' : '';

                tabla += '<tr><td>' + elemento.valor + ' ' + elemento.traducido + '</td><td class="text-right"><label><input name="descuentosCondicionados" class="ace ace-switch ace-switch-6" type="checkbox" onchange="guardarValorPeriodicidad(this);"  value="' + elemento.codigo + '"  ' + selected + '><span class="lbl"></span></label></td></tr>';

                i++;
            });

            tabla += '</tbody></table>';

            $('#periodicidad').html(tabla);

        }
    });


}

function guardarValorCuentaCorriente(element) {
    //alertAJAXSTART=1;
    var nombre = $(element).attr('name');

    var valor = $(element).val();



    var type = $(element).attr('type');



    if (!$(element).is(':checked') && type == 'checkbox') {

        valor = 0;
    }

    $.ajax({
        url: BASE_URL + "ctacte/guardarConfiguracionCtaCte",
        type: "POST",
        data: "nombre=" + nombre + '&valor=' + valor,
        dataType: "JSON",
        cache: false,
        success: function(respuesta) {
            if (respuesta.codigo == 1) {

                $.gritter.add({
                    title: '_OK!',
                    text: lang.validacion_ok,
                    //image: $path_assets+'/avatars/avatar1.png',
                    sticky: false,
                    time: '1500',
                    class_name: 'gritter-success'
                });



            } else {

                $.gritter.add({
                    title: '_Upps!',
                    text: respuesta.msgerror,
                    //image: $path_assets+'/avatars/avatar1.png',
                    sticky: false,
                    time: '1500',
                    class_name: 'gritter-error'
                });

            }
        }
    });



}

function initFRM() {


    $('#areaTablas select').chosen({
        width: '100%',
        allow_single_deselect: true
    });

    crearTablaPeriodicidad();



//        $('body').on( 'click', '#tablaperiodicidad tbody tr', function () {
//            
//        if ( $(this).hasClass('warning') ) {
//            //$(this).removeClass('danger');
//        }
//        else {
//           
//            $('#tablaperiodicidad tbody').find('tr.warning').removeClass('warning');
//            $(this).addClass('warning');
//        }
//    } );





    $('button[name="nuevaPeriodicidad"]').on('click', function() {

        $.ajax({
            url: BASE_URL + "configuracion/frm_periodicidad",
            type: "POST",
            data: "codigo=-1",
            dataType: "",
            cache: false,
            success: function(respuesta) {
                $.fancybox.open(respuesta, {
                    padding: 0,
                    width: 'auto',
                    openEffect: 'none',
                    closeEffect: 'none',
                    helpers: {
                        overlay: null
                    }

                });
            }
        });


    });


    $('#periodicidad').on('click', '[data-detalle]', function() {




        $.ajax({
            url: BASE_URL + "configuracion/frm_periodicidad",
            type: "POST",
            data: "codigo=" + $(this).attr('data-detalle'),
            dataType: "",
            cache: false,
            success: function(respuesta) {
                $.fancybox.open(respuesta, {
                    padding: 0,
                    width: 'auto',
                    openEffect: 'none',
                    closeEffect: 'none',
                    helpers: {
                        overlay: null
                    }

                });
            }
        });


    });


    $('#frmPlanesPago').on('submit', function() {//REVISAR SI ESTA FUNCION ESTA EN USO

        $.ajax({
            url: BASE_URL + "planespago/guardarConfigPlanPago",
            type: "POST",
            data: $(this).serialize(),
            dataType: "",
            cache: false,
            success: function(respuesta) {
                alert(respuesta);
            }
        });
        return false;
    });


    $('#descripcion').on('submit', function() {//REVISAR SI ESTA FUNCION ESTA EN USO
        var element = $('textarea[name="piePresupuesto"]');

        guardarValorCuentaCorriente(element);

        return false;



    });

}



$('#areaTablas').ready(function() {

//    var clavesFRM=Array("validacion_ok","ver_editar","planpago_periodo","activo");


//    $.ajax({
//            url:BASE_URL+'entorno/getLang',
//            data:"claves=" + JSON.stringify(clavesFRM),
//            type:"POST",
//            dataType:"JSON",
//            async:false,
//            cache:false,
//            success:function(respuesta){
//                lang=respuesta;
//                initFRM();
//            }
//    });


    initFRM();

});


