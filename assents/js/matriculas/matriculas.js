var thead = [];
var data = '';
var oTable = '';
var lang;
var commentFile = '';
lang = BASE_LANG;
menu = menuJson;
aoColumnDefs = columns;
$(document).ready(function(){
    init();
});

function frm_baja_matricula(cod_alumno, cod_plan_academico) {
    $.ajax({
        url: BASE_URL + 'matriculas/frm_alta_matriculas',
        data: {
            cod_alumno: cod_alumno,
            cod_plan_academico: cod_plan_academico
        },
        type: 'POST',
        cache: false,
        success: function(respuesta) {
            $('.btn-detalle').popover('hide');
            $.fancybox.open(respuesta, {
                scrolling: true,
                width: '50%',
                height: 'auto',
                autoSize: false,
                padding: 0,
                openEffect: 'none',
                closeEffect: 'none',
                helpers: {
                    overlay: null
                },
                beforeClose: function() {
                    oTable.fnDraw();
                }
            });
        }
    });
}

function init() {
    $('#academicoMatriculas').on('click', ".matricula-periodo", function(){
        var periodo = $(this).attr("data");
        var codigo = $(this).attr("matricula");
        $('.btn-detalle').popover('hide');
        if ($(this).attr("estado") === "habilitada") {
            $.ajax({
                url: BASE_URL + 'matriculas/frm_baja',
                data: {
                    codigo_matricula: codigo,
                    codigo_periodo: periodo
                },
                type: 'POST',
                cache: false,
                success: function(respuesta) {
                    $.fancybox.open(respuesta, {
                        scrolling: true,
                        width: '50%',
                        height: 'auto',
                        autoSize: false,
                        padding: 0,
                        openEffect: 'none',
                        closeEffect: 'none',
                        helpers: {
                            overlay: null
                        },
                        beforeClose: function() {
                            oTable.fnDraw();
                        }
                    });
                }
            });
        } else {
            frm_alta(codigo, cod_plan_academico);
        }
    });

    indice_columnas = {
        cod_alumno: 0,
        cod_matricula: 1,
        nombre_apellido: 2,
        plan_nombre: 3,
        fecha_emision: 4,
        detalle: 5,
        periodos_matricula: 6,
        rematricular: 7,
        cod_plan_academico: 8,
        estado: 9,
        observaciones: 10
    };
    
    oTable = $('#academicoMatriculas').dataTable({
        "bServerSide": true,
        "sAjaxSource": BASE_URL + 'matriculas/listar',
        "sServerMethod": "POST",
        'aoColumnDefs': aoColumnDefs,
        "aaSorting": [[4, "desc"]],
        "fnServerData": function(sSource, aoData, fnCallback) {
            $.ajax({
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": aoData,
                "async": true,
                "success": fnCallback
            });
        },
        "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            var baja = aData[5];
            var observacion = aData[indice_columnas.observaciones];
            var estado = aData[indice_columnas.estado];            
            if (observacion != 0) {
                icono = '<i style ="cursor:pointer;" class="icon-comment grey" onclick="ver_comentarios(' + aData[indice_columnas.cod_alumno]+','+ aData[indice_columnas.cod_plan_academico] +')"></i>';
            } else {
                icono = "";
            }
            imgTag = "<a href='#' class='btn-detalle' alumno='" + aData[indice_columnas.cod_alumno] + "' cod_plan_academico='" + aData[indice_columnas.cod_plan_academico] + "'>" + BASE_LANG.detalle + "</a> " + icono;
            $('td:eq(5)', nRow).html(imgTag);
            $('td:eq(5) a', nRow).popover({
                title: BASE_LANG.detalle,
                html: true,
                trigger: 'manual',
                placement: 'left'
            });            

            var clase = "";
            var letra = "";
            switch (aData[indice_columnas.estado]) {
                case 'habilitada':
                    clase = "matricula_estado badge badge-success arrowed";
                    letra = BASE_LANG.letra_habilitada + "&nbsp";
                    break;
                    
                case 'inhabilitada':
                    clase = "matricula_estado badge badge-danger arrowed";
                    letra = BASE_LANG.letra_inhabilitada + "&nbsp";
                    break;
                    
                case 'finalizada':
                    clase = "matricula_estado badge badge-info arrowed";
                    letra = BASE_LANG.letra_finalizada + "&nbsp";
                    break;
                    
                case 'certificada':
                    clase = "matricula_estado badge badge-purple arrowed";
                    letra = BASE_LANG.letra_certificada + "&nbsp";
                    break;

                case 'prematricula':
                    clase = 'matricula_estado badge badge-yellow arrowed';
                    letra = BASE_LANG.letra_prematricula;
                    break;

                default:
                    clase = "matricula_estado badge badge-grey arrowed";
                    letra = '-';
                    break;
            }

            imgEstado = '<span title = "' + aData[indice_columnas.estado] + '" class="' + clase + '" >' + letra + '</span>' + " " + aData[indice_columnas.plan_nombre];
            $('td:eq(3)', nRow).html(imgEstado);
            return nRow;
        }
    });
    marcarTr();
    $('#academicoMatriculas').wrap('<div class="table-responsive"></div>');
    $(aoColumnDefs).each(function(){
        thead.push(this.sTitle);
    });

    function columnName(name){
        var retorno = '';
        $(thead).each(function(key, valor){
            if (valor === name) {
                retorno = key;
            }
        });
        return retorno;
    }

    var codigo = '';
    var estado = '';
    var cod_plan_academico = '';
    var rematricular = 0;
    var cantidadMatriculaciones;

    $(".dataTables_length").html(generarBotonSuperiorMenu(menu.superior, "btn-primary", "icon-bookmark"));
    if ($("[name=hd_pasar_libres]").val() == 1 || $("[name=hd_regularizar_alumnos]").val() == 1) {
        var _html = '';
        _html += '<div class="btn-group"><button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" >' + BASE_LANG.acciones + ' <i class="icon-caret-down"></i></button>';
        _html += '<ul id="mover" class="dropdown-menu " role="menu">';
        if ($("[name=hd_regularizar_alumnos]").val() == 1) {
            _html += '<li id=action_regularizar_alumnos>';
            _html += getHTMLRegularizarBTN();
            _html += '</li>';
        }
        if ($("[name=hd_pasar_libres]").val() == 1) {
            _html += '<li id="action_pasar_libres">';
            _html += getHTMLPasarLibresBTN();
            _html += '</li>';
        }
        _html += '</ul>';
        _html += '</div>';
        $(".btn-group").append(_html);
    }
    $('#academicoMatriculas_wrapper').on('mousedown', '#academicoMatriculas tbody tr', function(e){
        var sData = oTable.fnGetData(this);
        //console.log(sData);
        commentFile = this;
        if (e.button === 2){
            codigo = sData[indice_columnas.cod_alumno];
            estado = sData[columnName(BASE_LANG.estado_matricula_cabecera)];
            cod_plan_academico = sData[indice_columnas.cod_plan_academico];
            //alert(cod_plan_academico);
            
            rematricular = sData[columnName(BASE_LANG.rematricular_matricula_cabecera)];
            cantidadMatriculaciones = sData[columnName(BASE_LANG.cantmatriculaciones_matricula_cabecera)];
            
            generalContextMenu(menu.contextual, e);
            switch (rematricular){
                case 0 :
                    $('a[accion="rematricular"]').parent().hide();
                    break;
                case 1:
                    $('a[accion="rematricular"]').parent().show();
                    break;
            }
            return false;
        }
    });

    function devolverEstado(estadoMatricula, cod_alumno, cod_plan_academico){
        //console.log(BASE_LANG);
        var clase = "";
        var estado = "";
        switch (estadoMatricula) {
            case "inhabilitada":
                clase = "label label-defuult arrowed";
                estado = BASE_LANG.MATRICULA_INHABILITADA;
                break;

            case "habilitada" :
                clase = "label label-success arrowed";
                estado = BASE_LANG.MATRICULA_HABILITADA + "&nbsp";
                break;
                
            case "finalizada" :
                clase = "label label-info arrowed";
                estado = BASE_LANG.finalizada + "&nbsp";
                break;
                
            case "certificada" :
                clase = "label label-purple arrowed";
                estado = BASE_LANG.certificada + "&nbsp";
                break;

        }
        return '<span class="' + clase + ' btn-detalle"  data-placement="top" data-original-title="Periodos matriculado" alumno="' + cod_alumno + '" cod_plan_academico="' + cod_plan_academico + '" >' + estado + '</span>';
        //return imgTag;
    }

    function TablaIniciada(){
        $('#academicoMatriculas tbody tr .btn-detalle').each(function() {
            $(this).popover({
                title: '',
                html: true,
                trigger: 'click',
                placement: 'left'
            });
        });
    }
    
    function frm_baja(cod_alumno, cod_plan_academico){
        $.ajax({
            url: BASE_URL + 'matriculas/frm_baja_matriculas',
            data: {
                cod_alumno: cod_alumno,
                cod_plan_academico: cod_plan_academico
            },
            type: 'POST',
            cache: false,
            success: function(respuesta){
                $.fancybox.open(respuesta, {
                    scrolling: true,
                    width: '50%',
                    height: 'auto',
                    autoSize: false,
                    padding: 0,
                    openEffect: 'none',
                    closeEffect: 'none',
                    helpers: {
                        overlay: null
                    },
                    beforeClose: function() {
                        oTable.fnDraw();
                    }
                });
            }
        });
    }
    
    $('body').on('click', '#menu a', function(){
        var accion = $(this).attr('accion');
        $('#menu').remove();
        switch (accion) {
            case 'estado_academico_matriculas':
                $.ajax({
                    url: BASE_URL + 'matriculas/frm_cursado',
                    data: {
                        cod_alumno: codigo,
                        cod_plan_academico: cod_plan_academico
                    },
                    type: 'POST',
                    cache: false,
                    success: function(respuesta){
                        $.fancybox.open(respuesta, {
                            width: 'auto',
                            height: 'auto',
                            scrolling: 'auto',
                            autoSize: true,
                            padding: 0,
                            openEffect: 'none',
                            closeEffect: 'none',
                            helpers: {
                                overlay: null
                            }
                        });
                    }
                });
                break

            case 'reimprimir_matricula':
                $.ajax({
                    url: BASE_URL + 'matriculas/getMatriculasPeriodosCursoAlumno',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        cod_alumno: codigo,
                        cod_plan_academico: cod_plan_academico,
                        agrupar_matriculas: 1
                    },
                    success: function(_json) {
                        if (_json.length == 1) {
                            var param = new Array();
                            param.push(_json[0].cod_matricula, 1, 1, 'reimprimir');
                            printers_jobs(5, param);
                        } else {
                            $.ajax({
                                url: BASE_URL + 'matriculas/reimprimirMatriculas',
                                type: 'POST',
                                data: {
                                    cod_alumno: codigo,
                                    cod_plan_academico: cod_plan_academico
                                },
                                success: function(_html) {
                                    //console.log(_html);
                                    $.fancybox.open(_html, {
                                        width: 'auto',
                                        height: 'auto',
                                        scrolling: true,
                                        autoSize: false,
                                        autoResize: true,
                                        padding: 0,
                                        openEffect: 'none',
                                        closeEffect: 'none',
                                        helpers: {
                                            overlay: null
                                        },
                                        beforeClose: function() {
                                            oTable.fnDraw();
                                        }
                                    });
                                }
                            });
                        }
                    }
                });
                break;
                
            case 'agregar_comentarios_matriculas':
                $.ajax({
                    url: BASE_URL + 'matriculas/agregar_comentarios_matriculas',
                    data: {
                        cod_alumno: codigo,
                        cod_plan_academico: cod_plan_academico
                    },
                    type: 'POST',
                    cache: false,
                    success: function(respuesta) {
                        $.fancybox.open(respuesta, {
                           width: '50%',
                            height: 'auto',
                            autoSize: false,
                            autoResize: false,
                            padding: 0,
                            openEffect: 'none',
                            closeEffect: 'none',
                            helpers: {
                                overlay: null
                            }
                        });
                    }
                });
                break;
            case 'editar_matricula':
                $.ajax({
                    url: BASE_URL + 'matriculas/editar_matricula',
                    data: {
                        cod_alumno: codigo,
                        cod_plan_academico: cod_plan_academico
                    },
                    type: 'POST',
                    cache: false,
                    success: function(respuesta) {
                        $.fancybox.open(respuesta, {
                           width: '50%',
                            height: 'auto',
                            autoSize: false,
                            autoResize: false,
                            padding: 0,
                            openEffect: 'none',
                            closeEffect: 'none',
                            helpers: {
                                overlay: null
                            }
                        });
                    }
                });
                break;
        }
        return false;
    });

    $('body').on('click', '.dataTables_length button', function(){
        var accion = $(this).attr('accion');
        $('#menu').remove();
        switch (accion) {
            case 'nueva_matriculas':
                $.ajax({
                    url: BASE_URL + 'matriculas/frm_Matricula',
                    data: {
                        codigo: -1
                    },
                    type: 'POST',
                    cache: false,
                    success: function(respuesta) {
                        $.fancybox.open(respuesta, {
                            scrolling: 'auto',
                            width: '60%',
                            autoSize: false,
                            autoResize: true,
                            padding: 0,
                            openEffect: 'none',
                            closeEffect: 'none',
                            helpers: {
                                overlay: null
                            },
                            beforeClose: function() {
                                oTable.fnDraw();
                            }
                        });
                    }
                });
                break;
        }
    });

    $('#academicoMatriculas').on('click', '.btn-baja', function() {
        frm_baja($(this).attr("cod_alumno"), $(this).attr("cod_plan_academico"));
        $('.btn-detalle').popover('hide');
    });

    $('#academicoMatriculas').on('click', '.btn-detalle', function(){
        $('#menu').remove();
        boton = $(this);
        if ($(boton).parent().find('.popover').is(':visible')) {
            $('.btn-detalle').popover('hide');
        } else {
            $.ajax({
                url: BASE_URL + 'matriculas/getMatriculasPeriodosCursoAlumno',
                data: {
                    cod_alumno: boton.attr("alumno"),
                    cod_plan_academico: boton.attr("cod_plan_academico")
                },
                type: 'POST',
                cache: false,
                dataType: 'json',
                success: function(respuesta){
                    var tabla = '<table class="table table-striped table-bordered table-hover">';
                    tabla += "<tr><th>" + BASE_LANG.cod_matricula + "</th><th>" + BASE_LANG.periodos + "</th><th>" + BASE_LANG.fecha_alta + "</th><th>" + BASE_LANG.estado + "</th><th>" + BASE_LANG.modalidad + "</th></tr>";
                    var habilitaBaja = false;
                    var habilitaAlta = false;
                    $(respuesta).each(function(key, fila) {
                        tabla += "<tr>";
                        tabla += "<td>";
                        tabla += fila.cod_matricula;
                        tabla += "</td>";
                        tabla += "<td>";
                        tabla += fila.nombre;
                        tabla += "</td>";
                        tabla += "<td>";
                        tabla += fila.fecha_emision;
                        tabla += "</td>";
                        tabla += "<td>";
                        tabla += devolverEstado(fila.estado, boton.attr("alumno"), fila.cod_matricula_periodo);
                        tabla += "</td>";
                        tabla += "<td>";
                        tabla += fila.modalidad;
                        if (fila.modifica_modalidad == "1") 
                        {
                            var parametros = fila.cod_matricula_periodo + ",'" + fila.modalidad+"'";
                            tabla += '&nbsp<i class="icon-cambiar-estado" style="cursor: pointer" title="' + BASE_LANG.modificar_modalidad + '" onclick="modificarModalidad(' + parametros + ')"></i>';
                        }
                        tabla += "</td>";
                        if (fila.estado == "inhabilitada") {
                            tabla += "<td>";
                            tabla += '<i class="icon-print" style="cursor: pointer" title="' + BASE_LANG.imprimir_baja_matriculas + '" onclick="reimprimirBajaMatricula(' + fila.codigo_estado_historico + ')"></i>';
                            tabla += "</td>";
                        } else {
                            tabla += "&nbsp;";
                        }
                        tabla += "</tr>";
                        if (fila.estado === "habilitada") {
                            habilitaBaja = true;
                        }
                        if (fila.estado === "inhabilitada") {
                            habilitaAlta = true;
                        }
                    });
                    tabla += "</table>";
                    tabla += "<div class='row'>";
                    if (habilitaBaja) {
                        tabla += "<div class='col-md-3'><div class='btn-baja btn btn-xs btn-danger' cod_alumno='" + boton.attr("alumno") + "'  cod_plan_academico='" + boton.attr("cod_plan_academico") + "'><i class='icon-bolt bigger-110'></i>" + BASE_LANG.baja + "</div></div>";
                    }
                    if (habilitaAlta) {
                        tabla += "<div class='col-md-3'><div class='btn-alta btn btn-xs btn-success'  onclick='frm_baja_matricula(" + boton.attr("alumno") + "," + boton.attr("cod_plan_academico") + ");'><i class='icon-ok bigger-110'></i> alta</div></div>";
                    }
                    tabla += "</div>";
                    $(boton).attr('data-content', tabla);
                    $('.btn-detalle').not(boton).popover('hide');
                    $(boton).popover('show');
                }
            });
        }
        return false;
    });
}

function reimprimirBajaMatricula(codMatriculaEstadoHistorico) {
    var param = new Array();
    param.push(codMatriculaEstadoHistorico);
    printers_jobs(2, param);
}

function modificarModalidad(codMatriculaPeriodo, modalidad) {
     $.ajax({
        url: BASE_URL + 'matriculas/frm_cambiarModalidad',
        data: {
            codigo: codMatriculaPeriodo,
            modalidad: modalidad
        },
        type: 'POST',
        cache: false,
        success: function(respuesta) {
            $('.btn-detalle').popover('hide');
            $.fancybox.open(respuesta, {
                scrolling: 'yes',
                width: '50%',
                height: 'auto',
                autoSize: false,
                padding: 0,
                openEffect: 'none',
                closeEffect: 'none',
                helpers: {
                    overlay: null
                },
                beforeClose: function() {
                }
            });
        }
    });
}

function getHTMLPasarLibresBTN() {
    var _html = '';
    _html += '<a href="#" onclick="document.location.href=\'' + BASE_URL + 'matriculas/frm_pasar_a_libres' + '\'">';
    _html += BASE_LANG.pasar_libres;
    _html += '</a>';
    return _html;
}

function getHTMLRegularizarBTN() {
    var _html = '';
    _html += '<a href="#" onclick="document.location.href=\'' + BASE_URL + 'matriculas/frm_pasar_a_regular' + '\'">';
    _html += BASE_LANG.regularizar_alumnos;
    _html += '</a>';
    return _html;
}

function ver_comentarios(cod_alumno, cod_plan_academico){   
     $.ajax({
        url: BASE_URL + 'matriculas/agregar_comentarios_matriculas',
        data: {
            cod_alumno: cod_alumno,
            cod_plan_academico: cod_plan_academico
        },
        type: 'POST',
        cache: false,
        success: function(respuesta) {
            $.fancybox.open(respuesta, {
               width: '50%',
                height: 'auto',
                autoSize: false,
                autoResize: false,
                padding: 0,
                openEffect: 'none',
                closeEffect: 'none',
                helpers: {
                    overlay: null
                }
            });
        }
    });
}
