var thead = [];
var aoColumnDefs = columns;
lang = BASE_LANG;
menu = menuJson;
$(document).ready(function(){
    init();
});

function devolverEstado(baja){   
    if (baja === "inhabilitado"){
        clase = "label label-default arrowed";
        estado = lang.INHABILITADO;
    } else {
        clase = "label label-success arrowed";
        estado = lang.HABILITADO + "&nbsp";
    }
    imgTag = '<span class="' + clase + '">' + estado + '</span>';
    return imgTag;
}

function init(){    
    oTable = $('#academicoProfesores').dataTable({       
        "bServerSide": true,
        "sAjaxSource": BASE_URL + 'profesores/listar',
        "sServerMethod": "POST",
        'aoColumnDefs': aoColumnDefs,
        "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            var baja = aData[6];
            var imgTag = "";
            imgTag = devolverEstado(baja);
            $('td:eq(5)', nRow).html(imgTag);
            return nRow;
        }
    });    
    marcarTr();
    $('#academicoProfesores').wrap('<div class="table-responsive"></div>');    
    $(".dataTables_length").html(generarBotonSuperiorMenu(menu.superior, "btn-primary", " icon-book"));    
    $(aoColumnDefs).each(function() {
        thead.push(this.sTitle);
    });
    
    function columnName(name) {
        var retorno = '';
        $(thead).each(function(key, valor) {
            if (valor == name) {
                retorno = key;
            }
        });
        return retorno;
    }

    $('#areaTablas').on('mousedown', '#academicoProfesores tbody tr', function(e) {
        var sData = oTable.fnGetData(this);
        if (e.button === 2) {
            codigo = sData[columnName(lang.codigo)];
            baja = sData[columnName(lang.estado_profesor_cabecera)];
            console.log(columnName(lang.estado_profesor_cabecera));
            generalContextMenu(menu.contextual, e);            
            switch (baja) {
                case "inhabilitado":
                    $('a[accion="cambiarEstadoProfesores"]').text(lang.HABILITAR);
                    break;
                case "habilitado":
                    $('a[accion="cambiarEstadoProfesores"]').text(lang.INHABILITAR);
                    break;
            }
            return false;
        }
    });

    $('body').on('click', '#menu a', function() {
        var accion = $(this).attr('accion');
        $('#menu').remove();
        var dataPOST = '';
        var dataURL = '';
        var setFancy = {
            scrolling: 'auto',
            width: 'auto',
            height: 'auto',
            autoSize: false,
            padding: 0

        };
        switch (accion) {            
            case'modificar_profesores':
                dataURL = BASE_URL + 'profesores/frm_profesores';
                dataPOST = 'codigo=' + codigo;
                $.ajax({
                    url: dataURL,
                    data: dataPOST,
                    type: 'POST',
                    cache: false,
                    success: function(respuesta){              
                        $.fancybox.open(respuesta,{
                            scrolling   :'auto',
                            autoSize	: false,
                            width   	: '90%',
                            height      : 'auto',
                            padding     : 1,
                            openEffect  :'none',
                            closeEffect :'none',
                            helpers:  {
                                overlay : null
                            }
                        });              
                    }
                });
                break;

            case 'cambiarEstadoProfesores':
                if (baja === "inhabilitado") {
                    dataURL = BASE_URL + 'profesores/cambioEstado';
                    dataPOST = 'codigo=' + codigo;
                } else {
                    dataURL = BASE_URL + 'profesores/frm_baja';
                    dataPOST = 'codigo_profesor=' + codigo;
                }

                $.ajax({
                    url: dataURL,
                    data: dataPOST,
                    type: 'POST',
                    cache: false,
                    success: function(respuesta) {
                        if (baja === "habilitado") {
                            $.fancybox.open(respuesta, setFancy);
                        } else {
                            oTable.fnDraw();
                            $.gritter.add({
                                title: lang.BIEN,
                                text: lang.PROFESOR_HABILITADO_CORRECTAMENTE,
                                sticky: false,
                                time: '3000',
                                class_name: 'gritter-success'
                            });
                        }
                    }
                });
                break;
        }
        return false;
    });

    $('#areaTablas').on('click', '.dataTables_length button', function() {
        var accion = $(this).attr('accion');
        var setFancy = {
            scrolling   :'auto',
            autoSize	: false,
            width   	: '90%',
            height      : 'auto',
            padding     : 1,
            openEffect  :'none',
            closeEffect :'none',
            helpers:  {
                overlay : null
            }
        };
        var dataPOST = '';
        var dataURL = '';
        switch (accion) {
            case'nuevo_profesor':
                dataURL = BASE_URL + 'profesores/frm_profesores';
                dataPOST = 'codigo=-1';
                break;
        }
        $.ajax({
            url: dataURL,
            data: dataPOST,
            type: 'POST',
            cache: false,
            success: function(respuesta) {
                $.fancybox.open(respuesta, setFancy);
            }
        });
        return false;
    });
}