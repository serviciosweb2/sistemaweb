var thead = [];
var data = '';
var oTable = '';
var lang = BASE_LANG;
var menu = menuJson;
var aoColumnDefs = columns;
var baja = '';
var uso = false;

$(document).ready(function() {
    init();
});

function init() {
    oTable = $('#academicoCursos').dataTable({
        "bServerSide": true,
        "sAjaxSource": BASE_URL + 'cursos/listar',
        "sServerMethod": "POST",
        'aoColumnDefs': aoColumnDefs,
        "aaSorting": [[4, "desc"]],
        "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            baja = aData[4];
            uso = aData[6];
            var clase = "";
            var estado = "";
            if (baja === "0") {
                clase = "label label-danger arrowed";
                estado = lang.INHABILITADO;
            } else {
                clase = "label label-success arrowed";
                estado = lang.HABILITADO + "&nbsp";
            }
            if (!uso) {
                clase = "label label-inverse arrowed";
                estado = lang.desuso + "&nbsp";
            }
            var imgTag = '<span class="' + clase + '">' + estado + '</span>';
            $('td:eq(4)', nRow).html(imgTag);
            var curso = "";
            if (aData[2] === "1") {
                curso = lang.SI;
            } else {
                curso = lang.NO;
            }
            $('td:eq(2)', nRow).html(curso);
            return nRow;
        }
    });

    $('#academicoCursos').wrap('<div class="table-responsive"></div>');
    $(aoColumnDefs).each(function() {
        thead.push(this.sTitle);
    });
    marcarTr();


    function columnName(name) {
        var retorno = '';
        $(thead).each(function(key, valor) {
            if (valor == name) {
                retorno = key;
            }
        });
        return retorno;
    }
    var codigo = '';
    var desactivado = '';
    var uso = '';
    $('#academicoCursos_wrapper').on('mousedown', '#academicoCursos tbody tr', function(e) {
        var sData = oTable.fnGetData(this);
        if (e.button === 2) {
            desactivado = sData[columnName(lang.estado)];
            uso = sData[7];
            codigo = sData[columnName(lang.codigo)];
            generalContextMenu(menu.contextual, e);
            /*se agrega linea por pedido de agustina (se se puede modificar la abreviatura de un curso)*/
            $('#menu a[accion="modificar_abreviatura"]').closest('li').hide();
            /**/
            if (desactivado == '1') {
                $('#menu a[accion="cambiar-estado-cursos"]').text(lang.INHABILITAR);
            } else {
                $('#menu a[accion="modificar_abreviatura"]').closest('li').hide();
                $('#menu a[accion="cambiar-estado-cursos"]').text(lang.HABILITAR);
            }
            if (!uso) {
                $('#menu a[accion="cambiar-estado-cursos"]').closest('li').hide();
            }
            return false;
        }

        //FUNCION DESPLIEGA MENU:  

        function despliegaMenu(x, y, codigo) {
            $('#desplegable').remove();
            var contenido = '<div id="desplegable" class="span2">' + menu.contextual + '</div></div></div>';
            $('#contenedorTablas').before(contenido);
            $('#desplegable').css({
                "margin-top": y, "margin-left": x - 18
            });
        }
    });


    //FUNCION QUE TOMA CLICK EN EL MENU DESPLEGABLE:

    $('body').on('click', '#menu a', function() {
        var accion = $(this).attr('accion');
        var id = $(this).attr('id');
        $('#menu').remove();
        switch (accion) {
            case 'cambiar-estado-cursos':

                bootbox.confirm(lang.confirmar_cambiar_estado_curso + '?', function(resultado) {
                    if (resultado === true) {
                        $.ajax({
                            url: BASE_URL + 'cursos/habilitarCurso',
                            data: 'codigo=' + codigo,
                            type: 'POST',
                            cache: false,
                            success: function(respuesta) {
                                oTable.fnDraw();
                            }
                        });
                    }
                });

                break;

            case 'verMaterias':
                $.ajax({
                    url: BASE_URL + 'cursos/form_materias',
                    data: 'codigo_curso=' + codigo,
                    type: 'POST',
                    cache: false,
                    success: function(respuesta) {
                        $.fancybox.open(respuesta, {
                            scrolling: 'auto',
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

            case 'cambiar-comision':
                $.ajax({
                    url: BASE_URL + 'matriculas/frm_CambioComision',
                    data: 'codigo_matricula=' + codigo,
                    type: 'POST',
                    cache: false,
                    success: function(respuesta) {
                        $.fancybox.open(respuesta, {
                            maxWidth: 1000,
                            maxHeight: 1000,
                            scrolling: false,
                            width: '100%',
                            height: '100%',
                            autoSize: true,
                            padding: 0,
                            wrapCSS: 'fancy_custom',
                            openEffect: 'none',
                            closeEffect: 'none',
                            helpers: {
                                overlay: null
                            }
                        });
                    }
                });
                break;

            case 'inscripcion_materias':
                $.ajax({
                    url: BASE_URL + 'matriculas/frm_inscribirMaterias',
                    data: 'codigo_matricula=' + codigo,
                    type: 'POST',
                    cache: false,
                    success: function(respuesta) {
                        $.fancybox.open(respuesta, {
                            scrolling: 'auto',
                            width: '100%',
                            height: '100%',
                            padding: 1,
                            openEffect: 'none',
                            closeEffect: 'none',
                            helpers: {
                                overlay: null
                            }
                        });
                    }
                });
                break;

            case 'modificar_abreviatura':
                $.ajax({
                    url: BASE_URL + 'cursos/frm_abreviatura',
                    data: 'codigo=' + codigo,
                    type: 'POST',
                    cache: false,
                    success: function(respuesta) {
                        $.fancybox.open(respuesta, {
                            scrolling: 'auto',
                            width: '100%',
                            height: '100%',
                            padding: 1,
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

    $('body').on('click', '.dataTables_length a', function() {
        var accion = $(this).attr('accion');
        var id = $(this).attr('id');
        $('#desplegable').remove();
        switch (accion) {

        }
        return false;
    });

    //FUNCION QUE TOMA LOS CLICK EN EL MENU FIJO EN LA CABEZERA DE LA TABLA:

    $('body').on('click', '#acciones a', function() {
        var accion = $(this).attr('accion');
        alert('click en : ' + accion);
        return false;
    });
}