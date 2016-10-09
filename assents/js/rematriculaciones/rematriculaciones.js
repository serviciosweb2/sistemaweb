var thead = [];
var data = '';
var oTablas = {};
var lang;
var commentFile = '';
comisionRedibujar = undefined;
alumnoHabilitar = '';
comisionHabilitar = '';
cursoHabilitar = '';
callBackHabilitacion = function () {};
menu = BASE_MENU_JSON;
lang = BASE_LANG;
aoColumnDefs = columns;
var ctacte = [];
var checksTodos = {};
/*
    Mantengo un mapa de las matriculas checkeadas asi se recuerdan entre cambios de página de la datatable.
*/
mapaMatriculas = {};

function checkearMapa(comision, matricula){
    if(mapaMatriculas[''+comision] == undefined)
        return undefined;
    return mapaMatriculas[''+comision][''+matricula];
}


//Se usa para setear datos segun accion del usuario. Siempre impacta en el mapa.
function setearMapa(comision, matricula, estado){
    if(mapaMatriculas[''+comision] == undefined){
        mapaMatriculas[''+comision] = {};
    }
    mapaMatriculas[''+comision][''+matricula] = estado;
}


//Se usa para guardar solamente datos que no estan guardados. Impacta en el mapa solamente si no habia un valor previo.
function actualizarMapa(comision, matricula, estado) {
    var x = ''+comision;
    var y = ''+matricula;
    if(mapaMatriculas[x] == undefined){
        mapaMatriculas[x] = {};
    }
    if(mapaMatriculas[x][y] == undefined){
        mapaMatriculas[x][y] = estado;
        return estado;
    }
    return mapaMatriculas[x][y];
}

$(document).ready(function(){
    init();
});


function reimprimir(codigo){
    matriculas = []; 
    $.each(mapaMatriculas[codigo], function(matricula, estado){
        if(estado == 1)
            matriculas.push(matricula);
    });
    $.ajax({
        url: BASE_URL + 'rematriculaciones/getBoletosReimprimir',
        data:{matriculas:matriculas, desde:rangoFechas().desde, hasta:rangoFechas().hasta},
        type:'POST',
        dataType:'json',
        success:function(boletos){
            if(boletos.length > 0)
                printers_jobs(16, boletos);
            else
                $.gritter.add({
                    title: 'Upps',
                    text: lang["no_hay_boletos_imprimir"],
                    sticky: false,
                    time: '3000',
                    class_name: 'gritter-error'
                });
                
    }
    });
}

function emitir(codigo){
    var matriculas = [];
    $.each(mapaMatriculas[codigo], function(matricula, estado){
        if(estado == 1)
            matriculas.push(matricula);
    });
    comisionRedibujar = codigo;
    $.ajax({
        url: BASE_URL + 'rematriculaciones/emitir',

        data: {
            cod_comision: codigo,
            matriculas: JSON.stringify(matriculas),
            desde: rangoFechas().desde,
            hasta: rangoFechas().hasta
        },
        type: 'POST',
        cache: false,
        success: function(respuesta){
            window.boletosEmitidos = 0;
            var oldMenu = menu;
            var oldLang = lang;
            $.fancybox.open(respuesta, {
                 scrolling: 'yes',
                 width: '100%',
                 height: '100%',
                 autoSize: false,
                 padding: 0,
                 openEffect: 'none',
                 closeEffect: 'none',
                 helpers: {
                     overlay: null
                 },
                 afterClose: function() {
                    menu = oldMenu;
                    lang = oldLang;
                    var tabla = oTablas['tabla'+codigo].api();
                    tabla.ajax.reload(function () {tabla.draw(true);}, false);
                    window.boletosEmitidos = undefined;
                 }


            });
        }
    });
}


function cambiarEstado(comision, matricula){
    if($('#checkboxRematricular-' + comision + "-" + matricula).prop('checked')){
        setearMapa(comision, matricula, 1);
    } else {
        setearMapa(comision, matricula, 0);
    }
}


function rangoFechas(){
    var anio = $('#selectAnio').val();
    var trimestre = $('#selectTrimestre').val();
    var diames1, diames2;
    switch(trimestre){
        case '1':
            diames1 = '01-01';
            diames2 = '03-31';
            break;
        case'2':
            diames1 = '04-01';
            diames2 = '06-30';
            break;
        case '3':
            diames1 = '07-01';
            diames2 = '09-30';
            break;
        case '4':
            diames1 = '10-01';
            diames2 = '12-31';
            break;
        default:
            break;
    }
    var fecha1 = anio + '-' + diames1;
    var fecha2 = anio + '-' + diames2;
    return {desde:fecha1, hasta:fecha2};
}


function init(){
    cargarSelectAnios();
    $('#selectAnio').val(''+anio);
    cargarSelectCursos();
    $('#selectCurso').val(curso);
    cargarSelectComisiones();
    $('#selectCurso').change(function (){
        cargarSelectComisiones();
    });

    $('#selectTrimestre').change(function (){
        cargarSelectCursos();
        cargarSelectComisiones();
    });
    $('#selectAnio').change(function (){
        cargarSelectCursos();
        cargarSelectComisiones();
    });
    $('#selectComision').val(comision);
    cursos.forEach(function(curso){
        curso.comisiones.forEach(function(comi){
            var comision = comi.codigo;
            oTablas['tabla'+comision] = $('#tablaRematriculas-' + comision).dataTable({
                "bServerSide": true,
                "sAjaxSource": BASE_URL + 'rematriculaciones/listar',
                "sServerMethod": "POST",
                "aoColumnDefs": aoColumnDefs,
                "aaSorting": [[2, "desc"]],
                "fnServerData": function(sSource, aoData, fnCallback) {
                    aoData.push({name:'comision', value:""+comision});
                    aoData.push({name:'fechaDesde', value:rangoFechas().desde});
                    aoData.push({name:'fechaHasta', value:rangoFechas().hasta});
                    $.ajax({
                        "dataType": 'json',
                        "type": "POST",
                        "url": sSource,
                        "data": aoData,
                        "async": true,
                        "success": fnCallback
                    });
                    aoData.push({name:'requestType', value:"full"});
                    $.ajax({
                        "url": sSource, 
                        "data": aoData,
                        "type":"POST",
                        "dataType": 'json', 
                        "success" : function (data){
                            var alumnos = data.aaData;
                            alumnos.forEach(function (alumno) {
                                actualizarMapa(comision, alumno[1], alumno[0]);
                            });
                        }
                    });
                },
                "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                    var matricula = aData[1];
                    var rematricular = actualizarMapa(comision, matricula, aData[0]);
                    var id = "checkboxRematricular-" + comision + "-" + matricula;
                    var checked = (rematricular == 1)?"checked":((rematricular == 0)?'':"disabled='true'");
                    var handler = 'cambiarEstado('+comision+', '+matricula+')';
                    $('td:eq(0)', nRow).html("<input type='checkbox' id='" + id +"' " + checked + " onclick='" + handler + "' value='"+aData[1]+"'>");
                    if(rematricular == -1)
                        $(nRow).addClass('disabledRow');
                    if(rematricular == 2)
                        $(nRow).addClass('greyRow');
                    return nRow;
                },
                "fnHeaderCallback": function( settings ) {
                        $('#tablaRematriculas-' + comision).find('[name^=checkBoxGeneral]').prop('id', 'checkBoxGeneral-'+comision);
                        $('#checkBoxGeneral-' + comision).change(function(){
                            checksTodos[''+comision] = $('#checkBoxGeneral-' + comision).prop('checked')?'1':'0';
                            checkearTabla(comision);
                        });
                        if(checksTodos[''+comision] == '1' || checksTodos[''+comision] == undefined)
                            $('#checkBoxGeneral-' + comision).prop('checked', true);
                }
            });
            marcarTr();
            $('#areaTablas-' + comision).on('mousedown', '#tablaRematriculas-' + comision +' tbody tr', function(e) {
                $('#menu').hide().fadeIn('fast').remove();
                var sData = oTablas['tabla'+comision].fnGetData(this);
                if(sData[0] == '2')
                    return;
                alumnoHabilitar =  sData;
                comisionHabilitar = comision;
                cursoHabilitar = curso.codigo;
                var menuContextual = menu.contextual;
                if(sData[8] == 'Habilitada'){
                    menuContextual = {accion: "deshabilitar_rematriculacion",
                    habilitado: "1",
                    text: "Desativar"}
                }
                if (e.button === 2) {
                   generalContextMenu(menuContextual, e);
                    return false;
                }
            });
            $("#tablaRematriculas-" + comision + "_filter #exportar_informe").remove();
            $("#tablaRematriculas-" + comision + "_filter").append('<i id="exportar_informe" class="icon-print grey" onclick="exportar_informe(\'pdf\', comision);" style="cursor: pointer; margin-left: 6px;" data-original-title="" title="exportar"></i>');
            $("#tablaRematriculas-" + comision + "_filter").append('<i id="exportar_informe" class="icon-external-link" onclick="exportar_informe(\'csv\', comision);" style="cursor: pointer; margin-left: 6px;" data-original-title="" title="exportar"></i>');

        });
    });
    
    $('body').on('click', '#menu a', function() {
        var accion = $(this).attr('accion');
        var id = $(this).attr('id');
        $('#menu').remove();
        switch (accion) {
            case 'habilitar_rematriculacion':
                $.ajax({
                    url: BASE_URL + 'rematriculaciones/frm_habilitar',
                    data: 'matricula=' + alumnoHabilitar[1] + '&fechaDesde=' + rangoFechas()['desde'] + '&fechaHasta=' + rangoFechas()['hasta'] + '&cod_curso=' + cursoHabilitar + '&cod_comision=' + comisionHabilitar + '&tipo=Habilitar',
                    type: 'POST',
                    cache: false,
                    success: function(respuesta) {
                        $.fancybox.open(respuesta, {
                            scrolling: 'auto',
                            autoSize: false,
                            width: 'auto',
                            height: 'auto',
                            padding: 1,
                            openEffect: 'none',
                            closeEffect: 'none',
                            helpers: {
                                overlay: null
                            },
                            afterClose: function () {
                                if(window.habilitacionok == 'si'){
                                    setearMapa(comisionHabilitar, alumnoHabilitar[1], 1);
                                }
                                var tabla = oTablas['tabla'+comisionHabilitar].api();
                                tabla.ajax.reload(function () {tabla.draw(true);}, false);
                                window.habilitacionok = undefined;
                            }
                        });
                    }
                });
                break;

    
            case 'deshabilitar_rematriculacion':
                $.ajax({
                    url: BASE_URL + 'rematriculaciones/frm_habilitar',
                    data: 'matricula=' + alumnoHabilitar[1] + '&fechaDesde=' + rangoFechas()['desde'] + '&fechaHasta=' + rangoFechas()['hasta'] + '&cod_curso=' + cursoHabilitar + '&cod_comision=' + comisionHabilitar + '&tipo=Deshabilitar',
                    type: 'POST',
                    cache: false,
                    success: function(respuesta) {
                        $.fancybox.open(respuesta, {
                            scrolling: 'auto',
                            autoSize: false,
                            width: 'auto',
                            height: 'auto',
                            padding: 1,
                            openEffect: 'none',
                            closeEffect: 'none',
                            helpers: {
                                overlay: null
                            },
                            afterClose: function () {
                                if(window.habilitacionok == 'si'){
                                    setearMapa(comisionHabilitar, alumnoHabilitar[1], -1);
                                }
                                var tabla = oTablas['tabla'+comisionHabilitar].api();
                                tabla.ajax.reload(function () {tabla.draw(true);}, false);
                                window.habilitacionok = undefined;
                            }
                        });
                    }
                });
                break;
            default:
                $.gritter.add({
                    title: 'Upps',
                    text: ' NO TIENE PERMISO !',
                    sticky: false,
                    time: '3000',
                    class_name: 'gritter-error'
                });
                break;
        }
        return false;
    });


}


function checkearTabla(comision){
    var estado = $('#checkBoxGeneral-' + comision).prop('checked');
    var mapa = mapaMatriculas["" + comision];
    $.each(mapa, function(index, elemento){
        if(elemento != -1 && elemento != 2){
            if(estado)
                mapa[index] = 1;
            else
                mapa[index] = 0;
        }
    });
    $('[id^=checkboxRematricular-'+comision+']').each(function(arg1, arg2, arg3){
        var val = mapa[$(arg2).val()];
        if(val != -1 && val != 2){
            if(estado){
                $(arg2).prop('checked', true);
            } else {
                $(arg2).prop('checked', false);
            }
        }
    });
}

function recargar(){
    var anio = $('#selectAnio').val();
    var trimestre = $('#selectTrimestre').val();
    var curso = $('#selectCurso').val();
    var comision = $('#selectComision').val();
    window.location.href = BASE_URL + 'rematriculaciones?anio=' + anio + '&trimestre=' + trimestre + '&curso=' + curso + '&comision=' + comision;
}


function cargarSelectAnios(){
    var minAnio, maxAnio;
    comisiones.forEach(function(comision){
        var inicio = comision.fecha_inicio_ciclo.split('-')[0];
        var fin = comision.fecha_fin_ciclo.split('-')[0];
        if(minAnio == undefined || inicio < minAnio)
            minAnio = inicio;
        if(maxAnio == undefined || fin > maxAnio)
            maxAnio = fin;

    });
    var html = '';
    $('#selectAnio').html('');
    var anio;
    for(var anio = minAnio; anio <= maxAnio;anio++){
        $('#selectAnio').append($('<option></option>').attr('value', ''+anio).text(''+anio));
    }
}

function cargarSelectCursos(def){
    var cursos = [];
    var anio = $('#selectAnio').val();
    var fechaDesde = rangoFechas().desde;
    var fechaHasta = rangoFechas().hasta;
    comisiones.forEach(function(comision){
        if(comision.fecha_inicio_ciclo >= fechaHasta || comision.fecha_fin_ciclo <= fechaDesde){
            return;
        }
        var cod_curso = comision.cod_curso;
        var nombre_curso = comision.nombre_curso;
        var esta = cursos.reduce(function(acumulado, elemento){
            return ( (elemento.cod_curso == cod_curso) || acumulado)
        }, false);
        if(!esta){
            cursos.push({"cod_curso":cod_curso, "nombre_curso":nombre_curso});
        }
    });
    $('#selectCurso').html('');
    $('#selectCurso').append($('<option></option>').attr('value', '-1').text('Todos'));
    cursos.forEach(function(curso){
       $('#selectCurso').append($('<option></option>').attr('value', ''+curso.cod_curso).text(curso.nombre_curso));
    });
    if(def)
       $('#selectCurso').val(def);
    else
       $('#selectCurso').val(-1);
}

function cargarSelectComisiones(def){
    var cod_curso = $('#selectCurso').val();
    $('#selectComision').html('');
    $('#selectComision').append($('<option></option>').attr('value', '-1').text('Todos'));
    var fechaDesde = rangoFechas().desde;
    var fechaHasta = rangoFechas().hasta;
    comisiones.forEach(function(comision){
        if(cod_curso == -1 || cod_curso.lastIndexOf(comision.cod_curso) >= 0
            && comision.fecha_fin_ciclo >= fechaHasta && comision.fecha_inicio_ciclo <= fechaDesde
        ){
            $('#selectComision').append($('<option></option>').attr('value', '' + comision.codigo).text(comision.nombre));
        }
    });
    if(def)
       $('#selectComision').val(def);
    else
       $('#selectComision').val(-1);
}



function exportar_informe(tipo_reporte, comision){
        var iSortCol_0 = oTablas['tabla' + comision].fnSettings().aaSorting[0][0];
        var sSortDir_0 = oTablas['tabla' + comision].fnSettings().aaSorting[0][1];
        var iDisplayLength = oTablas['tabla' + comision].fnSettings()._iDisplayLength;
        var iDisplayStart = oTablas['tabla' + comision].fnSettings()._iDisplayStart;
        var sSearch = $("#tablaRematriculas-" + comision + "_filter").find("input[type=search]").val();
        var fechaDesde = rangoFechas().desde;
        var fechaHasta = rangoFechas().hasta;
        $("[name=frm_exportar]").find("[name=iSortCol_0]").val(iSortCol_0);
        $("[name=frm_exportar]").find("[name=sSortDir_0]").val(sSortDir_0);
        $("[name=frm_exportar]").find("[name=iDisplayLength]").val(iDisplayLength);
        $("[name=frm_exportar]").find("[name=iDisplayStart]").val(iDisplayStart);
        $("[name=frm_exportar]").find("[name=sSearch]").val(sSearch);        
        $("[name=frm_exportar]").find("[name=comision]").val(comision);
        $("[name=frm_exportar]").find("[name=fechaDesde]").val(fechaDesde);
        $("[name=frm_exportar]").find("[name=fechaHasta]").val(fechaHasta); 
        $("[name=frm_exportar]").find("[name=tipo_reporte]").val(tipo_reporte); 
        $("[name=frm_exportar]").submit();
    }

