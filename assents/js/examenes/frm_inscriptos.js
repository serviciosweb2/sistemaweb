var oTableInscriptos, oTableAlumnos;
var langFRM = langFrm;
var alumnosCheck = [];
var inscripcionesCheck = [];
var fila;
var clavesFRM = Array("validacion_ok", "ocurrio_error", "error_seleccionar_inscripcion", "ERROR");
var aoColumnDefsInscriptos = function() {
    var retorno = '';
    $.ajax({
        url: BASE_URL + 'examenes/getColumnsInscriptos',
        data: '',
        dataType: 'JSON',
        type: 'POST',
        cache: false,
        async: false,
        success: function(respuesta) {
            retorno = respuesta;
        }
    });
    return retorno;
};

var aoColumnDefsAlumnos = function() {
    var valor = '';
    $.ajax({
        url: BASE_URL + 'examenes/getColumnsAlumnosInscribir',
        data: '',
        dataType: 'JSON',
        type: 'POST',
        cache: false,
        async: false,
        success: function(respuesta) {
            valor = respuesta;
        }
    });
    return valor;
};

function checkearTodosLosAlumnos(element){
    var checked = $(element).is(":checked");
    var chk = $(element).closest("table").find('[name="inscriptos[]"]');
    $.each(chk, function(key, input){
        $(input).prop("checked", checked);
        alumnosCheckeados(input);
    });
}

function alumnosCheckeados(element) {
    var codigo = $(element).val();
    if (element.checked) {
        alumnosCheck.push(codigo);
    } else {
        $(alumnosCheck).each(function(key, valor) {
            if (valor == codigo) {
                alumnosCheck.splice(key, 1);
            }
        });
    }
}

function inscripcionesCheckeadas(element) {
    var codigo = $(element).val();
    if (element.checked) {
        inscripcionesCheck.push(codigo);
    } else {
        $(inscripcionesCheck).each(function(key, valor) {
            if (valor == codigo) {
                inscripcionesCheck.splice(key, 1);
            }
        });
    }
}

function chekiarTodos(element) {
    if ($(element).is(':checked')) {
        oTableAlumnos.$('input[type="checkbox"]').prop('checked', true);
    } else {
        oTableAlumnos.$('input[type="checkbox"]').prop('checked', false);
    }
}

function imprimirConstanciaExamen() {
    var chk = $("[name=chk_inscriptos]:checked");
    if (chk.length > 0) {
        var cod_inscriptos = inscripcionesCheck.join("-");
        var param = new Array();
        param.push(cod_inscriptos);
        printers_jobs(7, param);
        cerrarVentana();
    } else {
        alert(langFRM.error_seleccionar_inscripcion);
    }
}

function imprimirListadoExamen() {
    var cod_examen = $("[name=codigo_examen]").val();
    var param = new Array();
    param.push(cod_examen);
    printers_jobs(6, param);
    cerrarVentana();
}

function generalContextMenuFancy(objMenu, e) {
    $('#menuContextualFancy').hide().fadeIn('fast').remove();
    var menuDesplegable = $('<ul/>').attr({
        id: 'menuContextualFancy',
        oncontextmenu: "return false",
        onkeydown: "return false"
    }).css({
        'position': 'fixed',
        "top": e.pageY,
        "z-index": '10000',
        "left": e.pageX

    });

    $(objMenu).each(function() {
        var habilitado = this.habilitado === "1" ? '' : 'ui-state-disabled';
        var li = $('<li/>').addClass(habilitado);
        var a = $('<a/>').text(this.text);
        var accion = this.habilitado === "1" ? this.accion : 'false';
        a.attr({accion: accion});
        li.append(a);
        menuDesplegable.append(li);
    });
    $('.fancybox-wrap').append(menuDesplegable);
    $("#menuContextualFancy").menu();
    $('.fancybox-wrap').not('table').on('click', function() {
        $('#menuContextualFancy').hide().fadeIn('fast').remove();
    });
}

function cerrarVentana() {
    $.fancybox.close(true);
}

function getInscriptos() {
    inscriptos_cargados = true;
    if (!$.fn.DataTable.isDataTable('#tablaInscriptos')) {
        oTableInscriptos = $('.fancybox-wrap #tablaInscriptos').DataTable({
            "autoWidth": false,
            bProcessing: false,
            bServerSide: true,
            sAjaxSource: BASE_URL + 'examenes/getInscriptosExamen',
            "lengthMenu": [5, 10, 15, 25, 50],
            sServerMethod: "POST",
            aaSorting: [[1, "asc"]],
            fnServerParams: function(aoData) {
                aoData.push({"name": 'codigo', "value": codigo});
            },
            'aoColumnDefs': aoColumnDefsInscriptos(),
            fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                var selected = '';
                
                $(inscripcionesCheck).each(function(k, value) {
                    if (value == aData[0]) {
                        selected = 'checked';
                    }
                });
                var estado = '<label><input class="ace" type="checkbox" name="chk_inscriptos" value="' + aData[0] + '" onclick="inscripcionesCheckeadas(this)"   ' + selected + '>';
                estado += '<span class="lbl" style="padding-bottom: 8px;">';
                estado += '</span>';
                estado += '&nbsp;';
                estado += '<i class="icon-trash btn-xs bigger-140 red" style="cursor: pointer;" onclick="baja_examen(' + aData[0] + ');">';
                estado += '</i></label>';
                $('td:eq(4)', nRow).html(estado);
                $('td:eq(2)', nRow).css('word-wrap', 'break-word');
                
                $('#tablaInscriptos').css('width', '100%');
            },
            fnDrawCallback: function(){
                $('#tablaInscriptos > tbody > tr > td').css('vertical-align', 'middle');
            }
        });
        
        $('#tablaInscriptos > tbody > tr > td').css('vertical-align', 'middle');
    } else {
        oTableInscriptos.draw();
    }
}

function getAlumnosInscribirExamen() {
    alumnos_cargados = true;
    if (!$.fn.DataTable.isDataTable('#tablaAlumnos')) {
        oTableAlumnos = $('.fancybox-wrap #tablaAlumnos').DataTable({
            bProcessing: false,
            bServerSide: true,
            sAjaxSource: BASE_URL + 'examenes/getInscribirAlumnosExamen',
            sServerMethod: "POST",
            "lengthMenu": [5, 10, 15, 25, 50],
            aaSorting: [[2, "asc"]],
            fnServerParams: function(aoData) {
                if (mostrar_comisiones){
                    var comision = $("[name=filtro_comision]").val();
                    aoData.push({"name": 'comision', "value": comision});
                }
                aoData.push({"name": 'codigo', "value": codigo});
                $("#tablaAlumnos").find("[name=chkAll]").prop("checked", false);
            },
            'aoColumnDefs': aoColumnDefsAlumnos(),
            fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                var checked = '';
                $(alumnosCheck).each(function(k, value) {
                    if (value == aData[0]) {
                        checked = 'checked';
                    }
                });
                var title = '';
                var disabled = '';
                if (aData[7] == 1) {
                    disabled = 'disabled="true"';
                    title += langFRM.inscripto_en_otro_examen_para_la_misma_materia;
                }
                var complemento = '';
                if (tipo_examen == 'FINAL' || tipo_examen == 'RECUPERATORIO_FINAL'){
                    if (aData[4] != 'No Regular' && aData[4] != 'Regular' && aData[4] != 'Não Regular'){
                        complemento = 'class="text-danger"';
                        disabled = 'disabled="true"';
                        if (title != ''){
                            title += '&#x000D;&#x000A;';
                        }
                        title += langFRM.solo_puede_inscribir_en_estado_regular_o_libre;
                    }
                }
                var estado = '<span ' + complemento + '>';
                estado += aData[4];
                estado += '</span>';
                
                var input = '<label>';
                input += '<input class="ace"  name="inscriptos[]"  type="checkbox" value="' + aData[0] + '" onclick="alumnosCheckeados(this);" ' + checked + ' ' + disabled + '>';
                input += '</input>';
                input += '<span class="lbl">';
                input += '</span>';
                input += '</label>';
                if (disabled != ''){
                    input += '&nbsp;';
                    input += '<i class="icon-info-sign" title="' + title + '" style=""cursor: pointer;>';
                    input += '</i>';
                }

                $('td:eq(0)', nRow).html(input);
                $('td:eq(3)', nRow).html(estado);
    
            },
            initComplete : function(settings, json) {
                
                if (mostrar_comisiones){
                
                $('#tablaAlumnos').wrap( "<div class='table-responsive'></div>" );
                $("#tablaAlumnos_filter").find("label").addClass("input-icon input-icon-right");
                $("#tablaAlumnos_filter").find("label").append('<i name="icon_filters" class="icon-caret-down grey" style="margin-right: 7px; cursor: pointer"></i>');
                $("#tablaAlumnos_filter").append($("[name=container_menu_filters_temp]").html());
                $(".select_chosen").chosen();
                $("[name=container_menu_filters_temp]").remove();
                $("[name=div_table_filters]").hide();
 
                $("[name=icon_filters]").on("click", function() {
                    $("[name=contenedorPrincipal]").toggle();
                    $("[name=div_table_filters]").toggle(300);
                    return false;
                });

                $("[name=contenedorPrincipal]").on("mousedown", function() {
                    $("[name=contenedorPrincipal]").hide();
                    $("[name=div_table_filters]").hide(300);
                });
                $("#tablaAlumnos_filter").find("label").find("input").width("45%")
                
                }
            }
        });
    } else {
        oTableAlumnos.draw();
        $("#tablaAlumnos").find("[name=chkAll]").prop("checked", false);
    }
}

function initFRM() {
    $('#tablaInscriptos').on('draw.dt', function() {
        $.fancybox.reposition();
    });
    $('#tablaAlumnos').on('draw.dt', function() {
        $.fancybox.reposition();
    });
    marcarTr();
    getInscriptos();
    $('.menuSuperior').html('<input class="btn btn-default" value="btn">');
    $('.nav nav-tabs a').click(function(e) {
        e.preventDefault();
        $(this).tab('show');
    });
        
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        if (e.target.hash === '#home') {
            if (!inscriptos_cargados){
                getInscriptos();
            }
        } else {
            if (!alumnos_cargados){
                getAlumnosInscribirExamen();
            }
        }
        $.fancybox.update();
        e.target;
        e.relatedTarget;
        $.fancybox.update();
    });

    $('.fancybox-wrap').on('mousedown', '#tablaInscriptos tbody tr', function(e) {
        fila = this;
        var sData = oTableInscriptos.row(this).data();
        if (e.button === 2) {
            fCodigo = sData[0];
            fTipo = '';
            return false;
        }
    });

    $('.fancybox-wrap ').on('click', '#menuContextualFancy a', function() {
        var codigo_examen = $('input[name="codigo_examen"]').val();
        var dataPOST = 'codigo=' + fCodigo + '&cod_examen=' + codigo_examen;
        $.ajax({
            url: BASE_URL + "examenes/bajaMatriculaExamen",
            type: "POST",
            data: dataPOST,
            cache: false,
            dataType: "JSON",
            success: function(respuesta) {
                if (respuesta.codigo == 1) {
                    gritter(langFRM.validacion_ok, true);
                    getInscriptos();
                    tablaParciales.refresh();
                    tablaFinales.refresh();
                    alumnos_cargados = false;
                } else {
                    gritter(respuesta.msgerror, false);
                }
            }
        });
        return false;
    });

    $('.fancybox-wrap').on('click', 'button[type="submit"]', function() {
        $('.fancybox-wrap form').submit();
        return false;
    });

    $('.fancybox-wrap').on('submit', 'form', function() {
        var alumnosInscrip = '';
        alumnosInscrip = alumnosCheck;
        var codExamen = $('input[name="codigo_examen"]').serialize();
        var dataPOST = 'inscriptos=' + JSON.stringify(alumnosInscrip) + '&' + codExamen;
        $.ajax({
            url: BASE_URL + 'examenes/guardarInscripcionesExamen',
            data: dataPOST + '&codigo=' + codigo,
            type: 'POST',
            cache: false,
            dataType: 'JSON',
            success: function(respuesta) {
                if (respuesta.codigo == 1) {
                    $.gritter.add({
                        text: langFRM.validacion_ok,
                        sticky: false,
                        time: '3000',
                        class_name: 'gritter-success'
                    });
                    alumnosCheck = [];
                    getAlumnosInscribirExamen();
                    tablaParciales.refresh();
                    tablaFinales.refresh();
                    inscriptos_cargados = false;
                } else {
                    $.gritter.add({
                        text: respuesta.msgerror,
                        image: '',
                        sticky: false,
                        time: '3000',
                        class_name: 'gritter-error'
                    });
                }
            }
        });
        return false;
    });

}

function listar(){
    oTableAlumnos.draw();
}

$('.fancybox-wrap').ready(function() {    
    initFRM();  
});

function baja_examen(codigo_inscripcion){
    $.ajax({
        url: BASE_URL + "examenes/bajaMatriculaExamen",
        type: "POST",
        data: {
            codigo: codigo_inscripcion
        },
        cache: false,
        dataType: "JSON",
        success: function(respuesta) {
            if (respuesta.codigo == 1) {
                gritter(langFRM.validacion_ok, true);
                getInscriptos();
                tablaParciales.refresh();
                tablaFinales.refresh();
                alumnos_cargados = false;
            } else {
                gritter(respuesta.msgerror, false);
            }
        }
    });
}