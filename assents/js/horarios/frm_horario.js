var dataPOST = '';

$(".fancybox-wrap").ready(function() {
    $('#errores').hide();
    $('select').chosen({
        width: "100%"
    });
    
    var fecha = ['horaDesde', 'horaHasta'];
    $(fecha).each(function() {
        $('#' + this).timepicker({
            'timeFormat': 'H:i:s',
            'step': 15
        });
    });
    
    $('.date-picker').datepicker({autoclose: true, changeMonth: false,
        changeYear: false,
        dateFormat: 'dd/mm/yy'}).next().on(ace.click_event, function() {
        $(this).prev().focus();
    });

    function actualizarSelect(nombre, options) {


        var str = idioma;
        var arr_idioma = str.split("-");
        var cod_idioma = arr_idioma[0];
        if (cod_idioma == "sp" ) cod_idioma = "es";   // FIx para materias que vengan con el idioma correspondiente.

        var nuevasOptions = '<option></option>';
        switch (nombre) {
            case 'cod_materia':
                $(options).each(function() {
                    nuevasOptions += '<option value="' + this.codigo + '">' + this["nombre_"+cod_idioma] + '</option>';
                });
                break
                
            case'profesores[]':
                $(options).each(function() {
                    nuevasOptions += '<option value="' + this.codigo + '">' + this.nombre + '</option>';
                });
                break;
        }
        $('select[name="' + nombre + '"]').html(nuevasOptions).trigger('chosen:updated').
                parent().find('.chosen-container').effect('bounce');
    }
    
    $('#nuevoEvento').on('change', 'select[name="cod_comision"]', function() {
        var cod_comision = $(this).val();
        $.ajax({
            url: BASE_URL + 'horarios/getMateriasComision',
            type: 'POST',
            data: 'cod_comision=' + cod_comision,
            dataType: 'json',
            success: function(respuesta) {
                actualizarSelect('cod_materia', respuesta);
            }
        });
    });

    $('#nuevoEvento').on('change', 'select[name="cod_materia"]', function() {
        var cod_materia = $(this).val();
        $.ajax({
            url: BASE_URL + 'horarios/getProfesores',
            type: 'POST',
            data: 'cod_materia=' + cod_materia,
            dataType: 'json',
            success: function(respuesta) {
                actualizarSelect('profesores[]', respuesta);
            }
        });
    });

    $('#nuevoEvento').on('click', 'input[type="radio"]', function() {
        var nombre = $(this).attr('data-input');
        $('.' + nombre).attr('disabled', false);
        $('input[name="finalizacion"]').not('.' + nombre).attr('disabled', true);
    });

    $('#nuevoEvento').on('change', 'select[name="tipoRepeticion"]', function() {
        var valor = $(this).val();
        if (valor != '0') {
            $('.estado').not('input[type="text"]').attr('disabled', false);
            $('.estado').trigger('chosen:updated');
            $('.repetir').fadeIn();
        } else {
            $('.estado').not('input[type="text"]').attr('disabled', true);
            $('.estado').trigger('chosen:updated');
            $('.repetir').fadeOut();
        }
        $.fancybox.update();
    });

    $('a[href="#cancelar"]').click(function() {
        $.fancybox.close();
        return false;
    });
});

function enviar_horarios(){    
    var form = $("#nuevoEvento");
    dataPOST = $(form).serialize();
    accion = $(form).attr('name');
    var accionModificar = $('input[name="codigo_horario"]').val();
    if (accionModificar != '-1') {
        var ver = $('input[name="vista_botones"]').val();
        if (ver == '') {
            guardar_frm(dataPOST + '&modifica_serie=false');
        } else {
            $.fancybox.close();
            $("#repeticion").modal("show");
        }
    } else {
        guardar_frm(dataPOST);
    }
}


function guardar_repeticion(element){
    accion = $(element).attr("name");
    var modificaSerie = '';
    var datta = '';
    if (accion == 'soloEste') {
        modificaSerie = false;
        var postModificado = dataPOST.replace("&tipoRepeticion=1", "&tipoRepeticion=0");
        datta = postModificado + '&modifica_serie=' + modificaSerie;
    } else {
        modificaSerie = true;
        datta = dataPOST + '&modifica_serie=' + modificaSerie;
    }
    guardar_frm(datta);
    $("#repeticion").modal("hide");
}

function guardar_frm(datosEnvio) {
    $.ajax({
        url: BASE_URL + 'horarios/guardarHorario',
        data: datosEnvio,
        type: 'POST',
        dataType: 'json',
        cache: false,
        success: function(respuesta) {
            if (respuesta.codigo === 1) {
                gritter(langFRM.HORARIO_GUARDADO_CORRECTAMENTE, true, langFRM.BIEN);
                var nuevo = respuesta.custom.nuevo;
                //Ticket 4374 -mmori- se modifica la siguiente llamada porque la anterior duplicaba la vista del evento.
                $('#calendar').fullCalendar('addEvent', nuevo);
                var unset = respuesta.custom.unset;
                $(unset).each(function() {
                    $('#calendar').fullCalendar('removeEvents', this.id);
                });
                //Ticket 4374 -mmori- ahora es necesaria la siguiente llamada para visualizar el evento
                $('#calendar').fullCalendar('refetchEvents');
                $.fancybox.close();
            }
            if (respuesta.codigo == 2) {
                var horaC = respuesta.custom.dia.split(' ');
                var horaF = respuesta.custom.dia2.split(' ');
                var msj = '' + langFRM.no_puede_modificarse + '<br>' + langFRM.superpone_con_horario_comision + ' ' + horaC[0] + ' ' + langFRM.que_comienza + ' ' + horaC[1] + ' ' + langFRM.y_finaliza + ' ' + horaF[1];
                gritter(msj, false, langFRM.ERROR);
            }
            if (respuesta.codigo === 3) {
                var msj = '' + langFRM.no_puede_modificarse + '<br>' + langFRM.tiene_asistencias_cargadas + '';
                gritter(msj, false, langFRM.ERROR);
                revertFunc();
            }
            if (respuesta.codigo == 0) {
                gritter(respuesta.respuesta, false, langFRM.ERROR);
            }
        }
    });
}