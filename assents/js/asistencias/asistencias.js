var calendario = '';
var test = '';
var fechaVista = '';
var parar = '';
var n = 0;
var lang = BASE_LANG;
var oTable = '';
var fecha_seleccionada = '';
var codigoComision = '';
var materia = '';
var asistencias = '';
var cod_horario = [];
var asistencias_completas = false;

jQuery.fn.dataTableExt.oSort['string-case-asc'] = function(x, y) {
    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
};

jQuery.fn.dataTableExt.oSort['string-case-desc'] = function(x, y) {
    return ((x < y) ? 1 : ((x > y) ? -1 : 0));
};

function resetCalendario() {
    test = '';
    $('div[name="calendario-asistencia"]').datepicker("refresh");
}

function habilitarBotones(valor) {
    $('button[name="btnSiguiente"]').prop('disabled', valor);
    $('button[name="btnAnterior"]').prop('disabled', valor);
}


function fechaInfo(fecha) {
    var msj = '';
    if (fecha == '') {
        msj += 'no hay asistencias cargadas';
    } else {
        msj += moment(fecha, "yy-mm-dd").format('L');
    }
    $('#viendoFecha').html(msj);
}

function verFecha(valor) {
    var fecha = calendario.datepicker("getDate");
    fecha_seleccionada = fecha.toISOString().slice(0, 19).split('T');
    $(test).each(function(k, eachFecha) {
        if (eachFecha.dia == fecha_seleccionada[0]) {
            n = k;
            fechaVista = eachFecha.dia;
        }
    });
    if ($.fn.DataTable.fnIsDataTable(oTable)) {
        tablaDestroy();
    }
    switch (valor) {
        case 'siguiente':
            n++;
            if (n == test.length) {
                $.gritter.add({
                    text: lang.no_hay_dias_por_ver,
                    sticky: false,
                    time: '3000',
                    class_name: 'gritter-error'
                });
            } else {
                $(calendario).datepicker('setDate', test[n].dia);
                generarSelectHorarios(codigoComision, materia, test[n].dia);
            }
            break;

        case 'anterior':
            n--;
            if (0 > n) {
                $.gritter.add({
                    text: lang.no_hay_dias_por_ver,
                    sticky: false,
                    time: '3000',
                    class_name: 'gritter-error'
                });
            } else {
                $(calendario).datepicker('setDate', test[n].dia);
                generarSelectHorarios(codigoComision, materia, test[n].dia);
            }
            break;
    }
}

function generarSelectHorarios(comision, materia, dia) {
        $.ajax({
        url: BASE_URL + 'asistencias/getHorariosDiaComisionMateria',
        type: "POST",
        data: "cod_comision=" + comision + "&cod_materia=" + materia + "&dia=" + dia,
        dataType: "JSON",
        cache: false,
        success: function(respuesta) {
            var mostrar_horario = '';
            if (respuesta.length == 1) {
                cod_horario = [];
                cod_horario.push(respuesta[0].codigo);
                mostrar_horario = "<input type='hidden' id='cod_horario' value='" + cod_horario + "'>"
                mostrar_horario += '<label><h6 class="blue bigger">' + respuesta[0].horario + '</h6>';
                if (respuesta[0].profesor != 0) {
                    tablaInit();
                    $('.alert-danger').addClass('hide');
                    alumnoConAsistencias(dia, respuesta[0].codigo);
                } else {
                    $('.alert-danger').removeClass('hide');
                    tablaDestroy();
                }
                //btn asistencias web
                $('#btnAsistenciasWeb').attr('data-materia', materia).removeClass('disabled');
            } else {
                tablaDestroy();               
                mostrar_horario = '<label>' + lang.seleccione_horario_cursado + '</label>';
                mostrar_horario += '<select name="horario_cursado" data-placeholder="' + lang.seleccione_horario_cursado + '" onchange="optionElegida(this,\'' + dia + '\');">';
                mostrar_horario += '<option></option>';
                $(respuesta).each(function(key, valor) {
                    cod_horario.push(valor.codigo);
                    mostrar_horario += '<option value="' + valor.codigo + '/' + valor.profesor + '">' + valor.horario + '</option>';
                });
                mostrar_horario += '</select>';
                mostrar_horario += "<input type='hidden' id='cod_horario' value='" + cod_horario + "'>"
            }
            mostrar_horario += '<div class="space-4"></div>';
            mostrar_horario += '<button id="cargar_profesor" type="button" class="btn" onclick="abrirFancyProfesores()"> Cargar Profesor';
            mostrar_horario += '</button>';
            mostrar_horario += '<button class="btn  btn-success" id="actualizar_estadoacademico" type="button" class="btn" onclick="actualizar_estadoacademico()" style="margin-left:20px;';
            if(asistencias_completas == false)
                 mostrar_horario += 'display:none';
            mostrar_horario += '"> Actualizar estado académico';
            mostrar_horario += '</button>';
            $("#horarios").html(mostrar_horario);
            $('select[name="horario_cursado"]').chosen({width: '100%'});
            $("#horarios").effect('bounce');
        }
    });
}

function optionElegida(element, fechita) {   
    $(".pietablaAsistencias").hide();
    if ($.fn.DataTable.fnIsDataTable(oTable)) {
        oTable.fnDestroy();
    }
    $('.table-responsive').html('<table class="table table-striped table-bordered table table-hover" id="alumnosAsistencias"><tbody></tbody></table>');
    var datos = $(element).val();
    var resultado = datos.split('/');
    var cod_horario = resultado[0];
    var profesor = resultado[1];
    if (profesor != 0) {
        alumnoConAsistencias(fechita, cod_horario);
    } else {
        tablaDestroy();
        console.log("Posible problema si hay mas de un horario para la misma comision a la misma hora");
        gritter('Cargue un profesor para este horario');
    }
}

function abrirFancyProfesores() {
    $.ajax({
        url: BASE_URL + 'asistencias/frm_profesor_horario',
        data: {cod_horario: $("#cod_horario").val()},
        type: 'POST',
        cache: false,
        success: function(respuesta) {
            $.fancybox.open(respuesta, {
                width: 'auto',
                height: 'auto',
                padding: 1,
                openEffect: 'none',
                closeEffect: 'none',
                helpers: {
                    overlay: null
                }
            });
        }
    });
}

function alumnoConAsistencias(dia, cod_horario) {
    $.ajax({
        url: BASE_URL + 'asistencias/getAlumnosAsistencias',
        type: "POST",
        data:{
            codigo: codigoComision,
            cod_materia: materia,
            fecha: dia,
            cod_horario: cod_horario
        },
        dataType: "JSON",
        async: false,
        cache: false,
        success: function(respuesta) {
            tablaDestroy();
            tablaInit();
            cargarTabla(respuesta);
        }
    });
}

function cargarTabla(registros) {
    $('#alumnosAsistencias').find('tbody').empty();
    var row = {
        codigo: '',
        nombre: '',
        asistencias: '',
        ultimas_asistencias: ''
    };
    $(".pietablaAsistencias").show();
    $("[name=btn_printer_job]").attr("disabled", false);
    $(registros).each(function(key, registro) {
        var tr = $('<tr>');
        var btn = '<a class="btn btn-xs" href="'+ registro.cod_matricula_horario  +'" data-content="area de detalle." data-original-title="" title="">'+lang.detalles+'</a>';
        row.ultimas_asistencias = '<a class="btn btn-xs" text="detalle" href="' + registro.cod_matricula_horario + '" data-content="area de detalle."></a>';
        var complemento = registro.estado_academico != 'cursando' && registro.estado_academico != 'regular' && registro.estado_academico != 'aprobado' && registro.estado_academico != 'recursa'
                ? "disabled='true'" : '';
        //// CAMBIO 18/12/2015 ARREGLAR
        if(complemento == "")
        {
            var select = '<select id="asistencias_alumno" name="alumnos[' + key + '][estado]" data-index="' + key + '" data-placeholder="' + lang.seleccionar + '" ' + complemento + '>';
            select += '<option></option>';
            $(asistencias).each(function(x, asis) {
                if(registro.estado_academico != 'cursando' && registro.estado_academico != 'regular' && registro.estado_academico != 'aprobado' && registro.estado_academico != 'recursa') {
                    registro.estado = 'ausente';
                }
                var estado = registro.estado == asis.id ? "selected" : "";

                select += '<option value="' + asis.id + '" ' + estado + '>' + asis.nombre + '<option>';
            });
            select += '</select>';
        }
        else
        {
            var i = 0;
            var select = '<select id="asistencias_alumno" name="alumnos[' + key + '][estado]" data-index="' + key + '" data-placeholder="' + lang.seleccionar + '" >';
            select += '<option></option>';
            $(asistencias).each(function(x, asis) {
                if(registro.estado_academico != 'cursando' && registro.estado_academico != 'regular' && registro.estado_academico != 'aprobado' && registro.estado_academico != 'recursa') {
                    registro.estado = 'ausente';
                }
                if(i < 1)
                {
                    var estado = registro.estado == asis.id ? "selected" : "";
                    select += '<option value="' + asis.id + '" ' + estado + '>' + asis.nombre + '<option>';
                }
                i++;
            });
            select += '</select>';
        }

        var complemento = registro.estado_academico != 'cursando' && registro.estado_academico != 'regular' && registro.estado_academico != 'aprobado' && registro.estado_academico != 'recursa'
                ? "display: none;" : '';
        var link = '<a href="#" onclick="event.preventDefault(); repetirEstado(this);">';
        link += '<span class="icon-repeat" style="margin-left:5px;' + complemento + '"></span>';
        link += '</a>';        
        var _html_matHor = "<input type='hidden' name='alumnos[" + key + "][cod_estado_academico]' value='" + registro.cod_estado_academico  + "'>";
        var _html_codMat = "<input type='hidden' name='alumnos[" + key + "][cod_matricula_horario]' value='" + registro.cod_matricula_horario + "'>";
        $('#alumnosAsistencias').dataTable().fnAddData( [
            registro.codigo_alumno + _html_matHor + _html_codMat,
            registro.cod_matricula,
            registro.nombre_apellido,
            select + link,
            registro.nombre_estado_academico,
            btn
            ] 
        );
        $('select').chosen();
    });
    tablaInit();
    $('a').popover();
}

function tablaDestroy() {
    $(".pietablaAsistencias").hide();
    if ($.fn.DataTable.fnIsDataTable(oTable))
        oTable.fnDestroy();
    $('.table-responsive').html('<table class="table table-striped table-bordered table table-hover" id="alumnosAsistencias"><tbody></tbody></table>');
}

function repetirEstado(element) {
    var indexTr = $(element).parent().parent().index();
    var element = $(element).parent().find('select');
    var estado = $(element).parent().find('select').val();
    $('#alumnosAsistencias select').each(function(key, select) {
        if (key > indexTr && !$(select).prop("disabled")) {
            $(select).find('option[value="' + estado + '"]').prop('selected', true);
            $(select).trigger("chosen:updated");            
        }
    });
    return false;
}

function tablaInit() {
    if ($.fn.dataTable.isDataTable('#alumnosAsistencias')) {
        oTable = $('#alumnosAsistencias').dataTable();
    } else {
        oTable = $('#alumnosAsistencias').dataTable({
            aaSorting: [[2, 'asc']],
            bPaginate: false,
            aoColumns: [
                {sTitle: lang.codigo, sClass: "center", width: "8%" },
                {sTitle: lang.matricula, sClass: "center", width: "8%"},
                {sTitle: lang.nombre, sClass: "center"},
                {sTitle: lang.asistencias, sClass: "center",width: "20%"},
                {sTitle: lang.estado_academico_matriculas, sClass: "center", width: "15%"},
                {sTitle: lang.ultimasAsistencias, sClass: "center",width: "15%"}
            ],
            fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) { 
                var g = $(nRow).find('a:last').html();
                $(nRow).find('a:last').popover({
                    html: true,
                    placement: 'left',
                    trigger: 'manual'
                });
            }, 
            aoColumnDefs: []
        });
    }
    $('.dataTables_info').closest('.row').remove();
    getMenuAcciones(accionesAsistencia);
    var c = '<div class="form-group">';
    c += '<select id="form-field-1" class="form-control width-90"  data-placeholder="' + lang.marcar_todos_como + ' "><option></option></select>';
    c += '</div>';
    $('#asistencias select ').chosen();
}

function getMenuAcciones(_obj) {
    var menu = '<div class="btn-group">';
    menu += '<button data-toggle="dropdown" class="btn btn-primary dropdown-toggle">';
    menu += 'ver';
    menu += '<i class="icon-angle-down icon-on-right"></i>';
    menu += '</button>';
    menu += '<ul class="dropdown-menu">';
    $(_obj).each(function(k, obj) {
        menu += '<li><a href="javascript:void(0)" onClick="' + obj.accion + ';">' + obj.lang + '</a></li>';
    });
    menu += '</ul>';
    menu += '</div>';
    return menu;
}

function init() {
    $('.table-responsive').html('<table class="table table-striped table-bordered table table-hover" id="alumnosAsistencias"></table>');
    asistencias = $.ajax({
        url: BASE_URL + 'asistencias/getEstadoAsistencias',
        type: "POST",
        data: "",
        dataType: "json",
        cache: false,
        success: function(respuesta) {
            asistencias = respuesta;
        }
    });
    var nombreCurso = $('input[name="nombreCurso"]').val();

    function setDetalle(detalle, element) {
        var table = $('<table>', {class: 'table'});
        var thead = $('<thead>');
        var tbody = $('<tbody>');
        thead.append($('<th>', {text: 'dia'}));
        thead.append($('<th>', {text: 'estado'}));
        $(detalle).each(function(i, dtll) {
            var tr = $('<tr>');
            tr.append($('<td>', {text: dtll.dia}));
            tr.append($('<td>', {text: dtll.estado}));
            tbody.append(tr);
        });
        table.append(thead);
        table.append(tbody);
        $(element).attr('data-content', table[0].innerHTML).popover('show');
    }    

    function cargarSelect(option, nombre) {
        $('select[name="' + nombre + '"]').empty().append($('<option>'));
        switch (nombre) {
            case'comisiones':
                $(option).each(function() {
                    var opt = $('<option>').val(this.codigo).text(this.nombre);
                    $('select[name="comisiones"]').append(opt);
                });
                break;

            case 'clases':
                $(option).each(function() {
                    var opt = $('<option>').val(this.codigo).text(this[nombreCurso]);
                    $('select[name="clases"]').append(opt);
                });
                break;
        }
        $('select[name="' + nombre + '"]').trigger("chosen:updated").
            parent().find('.chosen-container').effect('bounce');
    }

    $('select').chosen({width: '100%'});
    var nowTemp = new Date();
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
    calendario = $('div[name="calendario-asistencia"]').datepicker({
        dateFormat: 'yy-mm-dd',
        beforeShowDay: function(date) {
            var celda = 0;
            var asistencia = 0;
            if (test.length != 0) {
                var d = false;
                $(test).each(function(key, day) {
                    var fechaStrn = day.dia;
                    var x = fechaStrn.split("-");
                    var diaCursado = new Date(x[0], x[1] - 1, x[2]);
                    if (date.valueOf() == diaCursado.valueOf()) {
                        d = '';
                        celda = 1;
                        asistencia = day.asistencia;
                    }
                });
                if (celda){
                    if (asistencia == 1)
                        return [celda, "presente"];
                    return [celda, "ausente"];
                }
                return [celda];
            } else {
                return [celda];
            }
        },
        onSelect: function(dateText, Object) {
            if ($.fn.DataTable.fnIsDataTable(oTable)) {
                tablaDestroy();
            }
            generarSelectHorarios(codigoComision, materia, dateText);
        }
    });

    $('.page-content').on('change', 'select[name="cursos"]', function() {
        var valor = $(this).val();
        fechaVista = '';
        $('#btnAsistenciasWeb').addClass('disabled');
        $.ajax({
            url: BASE_URL + 'asistencias/getComisionesCurso',
            type: "POST",
            data: "codigo=" + valor,
            dataType: "JSON",
            cache: false,
            success: function(respuesta) {
                cargarSelect(respuesta, 'comisiones');
                $("[name=btn_printer_job]").attr("disabled", true);
            }
        });
    });

    $('.page-content').on('change', 'select[name="comisiones"]', function() {
        var valor = $(this).val();
        codigoComision = valor;
        fechaVista = '';
        //btn asistencias web
        $('#btnAsistenciasWeb').addClass('disabled');
        $('#btnAsistenciasWeb').attr('data-comision', valor);
        $.ajax({
            url: BASE_URL + 'asistencias/getMateriasComision',
            type: "POST",
            data: "codigo=" + valor,
            dataType: "JSON",
            cache: false,
            success: function(respuesta) {
                cargarSelect(respuesta, 'clases');
                $("[name=btn_printer_job]").attr("disabled", true);
            }
        });
    });

    $('.page-content').on('change', 'select[name="clases"]', function() {
        materia = $(this).val();
        fechaCursado(codigoComision, materia);
    });

    $('.page-content').on('click', '.form-actions button', function() {
        $('#asistencias').submit();
        return false;
    });

    $('.page-content').on('click', '#asistencias tr .btn', function() {
        var a = $(this);
        if ($(this).attr('class') == 'btn btn-success') {
            alert('!');
        }
        var fecha_asistencia_detalle = calendario.datepicker("getDate");
        var fecha_detalle_asistencia = fecha_asistencia_detalle.toISOString().slice(0, 19).split('T');
        if (a.parent().find('.popover').is(':visible')) {
            $(a).popover('hide');
        } else {
            var dataPOST = $(this).attr('href');
            $.ajax({
                url: BASE_URL + 'asistencias/getDetallesAsistencias',
                type: "POST",
                data: 'codigo=' + dataPOST + "&fecha=" + fecha_detalle_asistencia[0],
                dataType: "json",
                cache: false,
                success: function(respuesta) {
                    setDetalle(respuesta, a);
                }
            });
        }
        return false;
    });
}

$(document).ready(function() {
    $(".pietablaAsistencias").hide();
    accionesAsistencia = [{
            'accion': 'verFecha(\'siguiente\')',
            'lang': lang.siguiente
        }, {
            'accion': 'verFecha(\'anterior\')',
            'lang': lang.anterior
        }, {
            'accion': 'verFecha(\'anterior\')',
            'lang': lang.ultima
        }];
    init();
});

function imprimirAsistencias(tipoPlanilla, visualizar) {
    var curso = $("[name=cursos]").val();
    var comision = $("[name=comisiones]").val();
    var clase = $("[name=clases]").val();
    var fecha = calendario.datepicker("getDate");
    var fecha_impresion = fecha.toISOString().slice(0, 19).split('T');
    var seleccion = curso + "/" + comision + "/" + clase + "/" + fecha_impresion[0];
    
    if (tipoPlanilla == 'vacia') {
        seleccion += "/vacia";
    } else {
        seleccion += "/nada";
    }
    if (visualizar == 'horizontalmente') {
        seleccion += "/horizontalmente";
    } else {
        seleccion += "/nada";
    }
    
    //Ticket 4621 -mmori- modifico para agregar el periodo de impresion
    var periodo = $("#periodo").val();
    if (periodo != null) {
        seleccion += "/"+periodo;
    } else {
        seleccion += "/nada";
    }    

    var cod_horario = $("#cod_horario").val();
    if (cod_horario != null) {
        seleccion += "/"+cod_horario;
    } else {
        seleccion += "/";
    }    
                
    var param = new Array();
    param.push(seleccion);
    printers_jobs(9, param);
}

function cerrarVentana() {    
    generarSelectHorarios();
    $.fancybox.close(true);
}

//siwakawa
function actualizar_estadoacademico(){
    var dataPOST = $('#alumnosAsistencias input,select').serialize();
    $.ajax({
        url: BASE_URL + 'asistencias/actualizar_estadoacademico',
        type: "POST",
        data: dataPOST,
        dataType: "JSON",
        cache: false,
        success: function(respuesta) {
            if (respuesta.codigo == 1) {
                gritter(lang.validacion_ok, true);
                setTimeout(function () {window.location.href = BASE_URL + 'asistencias';}, 2000); //Al menos así ven el mensaje de ok. No se por que se recarga la pagina.
            } else {
                gritter(respuesta.msgerror);
            }
        }
    });
    return false;
}

function guardar_asistencias(){
    var fecha = $('div[name="calendario-asistencia"]').val();
        var dataPOST = $('#alumnosAsistencias input,select').serialize();
        $.ajax({
        url: BASE_URL + 'asistencias/guardarAsistencias',
        type: "POST",
        data: dataPOST + '&fecha=' + fecha,
        dataType: "JSON",
        cache: false,
        success: function(respuesta) {
            if (respuesta.codigo == 1) {
                gritter(lang.validacion_ok, true);
                fechaCursado(codigoComision, materia);
            } else {
                gritter(respuesta.msgerror);
            }
        }
    });
    return false;
}

function fechaCursado(comision, materia) {
    $.ajax({
        url: BASE_URL + 'asistencias/getDiasCursadoComision',
        type: "POST",
        data: "codigo=" + comision + "&cod_materia=" + materia,
        dataType: "JSON",
        cache: false,
        async: false,
        success: function(respuesta) {
            //alfonso fechacursado
            //debugger;
            cargarTabla("");
            test = respuesta.dias;
            //siwakawa : en caso de que esten las asistencias de todo el curso tomadas
            //agregar boton Actualizar estado académico
            asistencias_completas = true;
            $(test).each(function (key, day) {
               if (day.asistencia == 0) 
                   asistencias_completas = false; 
            });
            parar = respuesta.dia_parar;
            $("[name=btn_printer_job]").attr("disabled", true);
            $('#cargar_profesor').attr('disabled', true);
            if (test.length == 0) {
                if ($.fn.DataTable.fnIsDataTable(oTable)) {
                    tablaDestroy();
                }
                resetCalendario();
                $.gritter.add({
                    text: lang.no_tiene_dias_cargados,
                    sticky: false,
                    time: '3000',
                    class_name: 'gritter-error'
                });
            } else {
                habilitarBotones(false);
                $('div[name="calendario-asistencia"]').datepicker('setDate', parar);
                $('#cargar_profesor').attr('disabled', false);
                generarSelectHorarios(comision, materia, parar);
            }
        }
    });
}
    
function limpiar_asistencias(){
    $('#alumnosAsistencias select').each(function(key, select) {
        if (!$(select).prop("disabled")) {
            $(select).prop('selectedIndex',0);
            $(select).trigger("chosen:updated");
        }
    });
    return false;
}

//

function getAsistenciasWeb(comision, materia) {
    $.ajax({
        url: BASE_URL + 'asistencias/get_asistencias_web_alumno',
        type: "POST",
        data: {
            "cod_materia" : materia,
            "cod_comision": comision
        },
        //dataType: "JSON",
        cache: false,
        //async: false,
        success: function(respuesta) {
            if(respuesta != ''){
                $.fancybox.open(respuesta, {
                    width: 'auto',
                    height: 'auto',
                    padding: 1,
                    openEffect: 'none',
                    closeEffect: 'none',
                    overlayShow: true,
                });
            } else {
                $.gritter.add({
                    text: 'No existen asistencias web para esta materia',
                    sticky: false,
                    time: '3000',
                    class_name: 'gritter-error'
                });
            }
        }
    });
}

$('.page-content').on('click', '#btnAsistenciasWeb', function() {
    var comisionSeleccionada = $(this).attr('data-comision');
    var materiaSeleccionada = $(this).attr('data-materia');
    getAsistenciasWeb(comisionSeleccionada, materiaSeleccionada);
    return false;
});

function exportar_informe(tipo_reporte){
    var frm_exportar = $("[name=frm_exportar]");
    var iSortCol_0 = aTable.fnSettings().aaSorting[0][0];
    var sSortDir_0 = aTable.fnSettings().aaSorting[0][1];
    var iDisplayLength = aTable.fnSettings()._iDisplayLength;
    var iDisplayStart = aTable.fnSettings()._iDisplayStart;
    var sSearch = $("#asistencias_web_filter").find("input[type=search]").val();
    var comision = null;
    var materia = null;

    comision = $('#btnAsistenciasWeb').attr('data-comision');
    materia = $('#btnAsistenciasWeb').attr('data-materia');

    //Modificar para el reporte!!
   /* $('#asistencias_web').find('tbody').find('tr').each(function(key, value) {
        $(this).find('td:first-child').each(function () {
            alumnos[key] = {
                'id_alumno' : $(this).find("[name=id_usuaro]").val(),
                'nombre' : $(this).text()
            }
        });
    });*/

    /*if(frm_exportar.find("[name=alumnos]").length == 0) {
        $.each(alumnos, function (key, value) {
            frm_exportar.append('<input type="hidden" name="alumnos['+key+'][id_alumno]" value="' + alumnos[key]['id_alumno'] + '" />');
            frm_exportar.append('<input type="hidden" name="alumnos['+key+'][nombre]" value="' + alumnos[key]['nombre'] + '" />');
        });
    }*/

    frm_exportar.find("[name=cod_comision]").val(comision);
    frm_exportar.find("[name=cod_materia]").val(materia);
     /*frm_exportar.find("[name=iSortCol_0]").val(iSortCol_0);
    frm_exportar.find("[name=sSortDir_0]").val(sSortDir_0);
    frm_exportar.find("[name=iDisplayLength]").val(iDisplayLength);
    frm_exportar.find("[name=iDisplayStart]").val(iDisplayStart);
    frm_exportar.find("[name=sSearch]").val(sSearch);*/
    frm_exportar.find("[name=tipo_reporte]").val(tipo_reporte);
    frm_exportar.submit();
    //frm_exportar.find( "input[name^='alumnos']" ).remove();
}

$('[data-rel=tooltip]').tooltip();
var tooltip = $( "#btnAsistenciasWeb" ).tooltip({
    show: null,
    position: {
        my: "left top",
        at: "left bottom"
    },
    open: function( event, ui ) {
        ui.tooltip.animate({ top: ui.tooltip.position().top + 10 }, "fast" );
    }
});