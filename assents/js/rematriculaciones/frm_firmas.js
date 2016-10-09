
var cod_alum ="";
var al_documento="";

$(".fancybox-wrap").ready(function() {
    init();

});

function init() {
    $("#nombre_alumno").ajaxChosen({
            minLength: 0,
            queryLimit: 10,
            delay: 100,
            chosenOptions: {width: '100%', max_selected_options: 1},
            searchingText: "Buscando...",
            noresultsText: "No hay resultados.",
            initialQuery: true
    },
    function(options, response) {
        $.ajax({
            url: BASE_URL + 'alumnos/getAlumnosHabilitadosSelect',
            type: "POST",
            data: {buscar: options.term, estado: 'habilitada'},
            dataType: "JSON",
            cache: false,
            success: function(respuesta) {
                var terms = {};
                $.each(respuesta, function(i, val) {
                    terms[val.codigo] = val.nombreapellido;
                });
                response(terms);
            }
        });
    });

    $('select#nombre_alumno').on('change',function(){
        cod_alum = $(this).val();
        actualizaMatricula();
    });

    $('select#nombre_alumno').blur(function(){
        actualizaMatricula();
    });
}

function actualizaMatricula() {
    document.getElementById("matricula").innerHTML = "";
        $.ajax({
            url: BASE_URL + 'matriculas/getMatriculasAlumnosSelect',
            type: "POST",
            data: {cod_alumno: cod_alum},
            dataType: "JSON",
            cache: false,
            success: function (respuesta) {
                var terms = {};
                $.each(respuesta, function (i, val) {
                    var agregar = document.getElementById("matricula");
                    var option = document.createElement("option");
                    terms[val.codigo] = val.codigo;
                    option.text = val.codigo;
                    agregar.add(option);
                });
            }
        });
}

$('#nuevaComision').on('submit',function(){
    if( $("[name=nombre_alumno_firma]").val() == "" || $("[name=matricula_firma]").val() == "" ) {
        $.gritter.add({
            title: ':-(',
            text: 'Verifique Dados!!!',
            sticky: false,
            time: '3000',
            class_name: 'gritter-error'
        });
    }else{
        $.ajax({
            url: BASE_URL + 'rematriculaciones/guardarFirma',
            type: 'POST',
            data: {
                cod_matricula: $("[name=matricula_firma]").val(),
                firmo: $("[name=firmo_firma]").val(),
                trimestre: $("[name=trimestre_firma]").val(),
                ano: $("[name=anio_firma]").val()
            },
            dataType: 'json',
            cache: false,
            success: function (respuesta) {
                if (respuesta.codigo === '0') {
                    $("#errores").html(respuesta.respuesta).fadeIn();
                    $.fancybox.update();
                } else {
                    $.gritter.add({
                        title: 'OK',
                        text: 'Salvada!!!',
                        sticky: false,
                        time: '3000',
                        class_name: 'gritter-success'
                    });
                    $.fancybox.close();
                }
            }
        });
    }
    return false;
});