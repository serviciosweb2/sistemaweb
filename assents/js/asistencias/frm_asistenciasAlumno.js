var selector = new Array();

jQuery.fn.dataTableExt.oSort['uk_date-asc'] = function(a, b) {
    var ukDatea = a.split('/');
    var ukDateb = b.split('/');
    if (isNaN(parseInt(ukDatea[0]))) {
        return -1;
    }
    if (isNaN(parseInt(ukDateb[0]))) {
        return 1;
    }
    var x = (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
    var y = (ukDateb[2] + ukDateb[1] + ukDateb[0]) * 1;
    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
};

jQuery.fn.dataTableExt.oSort['uk_date-desc'] = function(a, b) {
    var ukDatea = a.split('/');
    var ukDateb = b.split('/');
    if (isNaN(parseInt(ukDatea[0]))) {
        return 1;
    }
    if (isNaN(parseInt(ukDateb[0]))) {
        return -1;
    }
    var x = (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
    var y = (ukDateb[2] + ukDateb[1] + ukDateb[0]) * 1;
    return ((x < y) ? 1 : ((x > y) ? -1 : 0));
}; 

$('.fancybox-wrap').ready(function() {
    $('.fancybox-wrap select').chosen({
        width:'100%'
    });        
    var oTableHorarios = $('#ver_horarios').DataTable({
        "aaSorting": [[0, "asc"]],
        "iDisplayLength": 5,
        "aoColumns": [{"sType": "uk_date"}, null, null, null, null] 
    });
    
    $('.fancybox-wrap .dataTables_length').closest(".row").remove();

    $('.fancybox-wrap #asistencias tr').on('focus', 'input', function() {
        $(this).prop('readonly', false);
        $('.fancybox-wrap #asistencias tr input').not(this).prop('readonly', true);
        return false;
    });

    $('select[name="matriculas"]').on('change', function() {
        oTableHorarios.clear();
        oTableHorarios.draw();
        $('select[name="materias"] option').remove();   
        getMaterias($(this).val());
        return false;
    });

    $('select[name="materias"]').on('change', function() {
        oTableHorarios.clear();
        oTableHorarios.draw();
        getHorarios($(this).val());        
        return false;
    });

    $('.fancybox-wrap').on('click', '[name=btn_guardar_asistencias]', function() {
        var alumnos = new Array();
        var cod_estado_academico = $("form#asistencias").find("[name=cod_estado_academico]").val();
        $.each(selector, function(key, value){
            if (value){
                alumnos.push({
                    estado: value,
                    cod_matricula_horario: key
                });
            }
        });        
        $.ajax({
            url: BASE_URL + 'asistencias/guardarAsistencias',            
            type: 'POST',
            dataType: "json",
            cache: false,
            data: {
                alumnos: alumnos,
                cod_estado_academico: cod_estado_academico
            },
            success: function(respuesta) {
                if (respuesta.codigo == 1) {
                    $.gritter.add({
                        title: lenguaje.BIEN,
                        text: lenguaje.registros_guardados_correctamente,
                        sticky: false,
                        time: '3000',
                        class_name: 'gritter-success'
                    });
                    $.fancybox.close();
                } else {
                    $.gritter.add({
                        title: 'Upps',
                        text: respuesta.msg,
                        sticky: false,
                        time: '3000',
                        class_name: 'gritter-error'
                    });
                }
            }
        });
        return false;
    });

    function getMaterias(cod_matricula_periodo) {
        $.ajax({
            data: 'cod_matricula_periodo=' + cod_matricula_periodo,
            url: BASE_URL + "asistencias/getMaterias",
            type: 'POST',
            dataType: 'json',
            cache: false,
            success: function(respuesta) {
                if (respuesta.length > 0) {
                    actualizarSelect('materias', respuesta);
                }
            }
        });
    }

    function actualizarSelect(name, option) {
        $('select[name="' + name + '"]').empty();
        switch (name) {
            case 'materias':
                $('select[name="' + name + '"]').append('<option value=""></option>');
                $(option).each(function() {
                    $('select[name="' + name + '"]').append("<option value='" + this.codigo + "'>" + this.nombre + "</option>");
                });
                break;
        }
        $('select[name="' + name + '"]').trigger("chosen:updated");
        if (option != '') {
            $('#' + name + '_chosen').effect("shake");
        }
    }

    function getHorarios(cod_estado_academico) {
        $.ajax({
            url: BASE_URL + "asistencias/getHorasEstadoAcademico",
            type: 'POST',
            dataType: 'json',
            cache: false,            
            data: {
                cod_estado_academico: cod_estado_academico
            },
            success: function(data){
                if (data.horarios.length > 0){
                    var complemento = '';
                    $(data.horarios).each(function(k, valor){
                        var select = "<select id='asistencia' class='asistencia' name ='alumnos[" + k + "][estado]' onchange='selector_asistencia_change(this)'>";
                        select += '<option></option>';
                        var estadoAsistencia = '';
                        $(data.estados).each(function(k2, estado){
                            if (estado.id == valor.estado){
                                complemento = "selected='true'";
                                estadoAsistencia = valor.estado;
                            } else {
                                complemento = '';
                            }
                            select += "<option value='" + estado.id + "'" + complemento + ">" + estado.nombre + "</option>";                            
                        });
                        select += "</select>";
                        select += "<input type='hidden' id='codigo_matricula_horario' name='alumnos[" + k + "][cod_matricula_horario]' value='" + valor.cod_mat_horario + "'>";
                        oTableHorarios.row.add([valor.dia, valor.horadesde, valor.horahasta, valor.nombre, select]).draw();
                        if (estadoAsistencia != ''){
                            selector[valor.cod_mat_horario] = estadoAsistencia;
                        }
                    });
                    oTableHorarios.$('select').chosen({
                        width:'100%'
                    });
                    $.fancybox.update();
                } else {
                    gritter(data.msg, false, "");
                }
            }
        });
    }
});

function selector_asistencia_change(element){
    var estado = $(element).val();
    var tr = $(element).closest("tr");
    var cod_mat_horario = $(tr).find("#codigo_matricula_horario").val();
    selector[cod_mat_horario] = estado;
}