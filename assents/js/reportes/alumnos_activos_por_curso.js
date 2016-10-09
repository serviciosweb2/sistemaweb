$(document).ready(function(){
    $(".select_chosen").chosen();
    listar();
});


function listar(){
    var mes = $("[name=filtro_mes]").val();
    var anio = $("[name=filtro_anio]").val();
    $("[name=table_reporte_alumnos_activos_por_curso]").find('tbody').find("tr").remove();
    $("[name=table_reporte_alumnos_activos_por_curso]").find('tfoot').find("[name=total_activos]").html("0");
    $.ajax({
        url: BASE_URL + 'reportes/listar_alumnos_activos_curso',
        type: 'POST',
        dataType: 'json',
        data:{
            mes: mes,
            anio: anio
        },
        success: function(_json){
            armarTabla(_json);
        }
    });
}

function armarTabla(_json){
    $("[name=table_reporte_alumnos_activos_por_curso]").find('tbody').find("tr").remove();
    $("[name=table_reporte_alumnos_activos_por_curso]").find('tfoot').find("[name=total_activos]").html("0");
    var carreras = _json.carreras;
    var cursos_cortos = _json.cursos_cortos;
    var seminarios = _json.seminarios;
    var cocineritos = _json.cocineritos;
    var cantidad_carreras = 0;
    var cantidad_cursos_cortos = 0;
    var cantidad_seminarios = 0;
    var cantidad_cocineritos = 0;
    var _html_carreras = '';
    var _html_cocineritos = '';
    var _html_seminarios = '';
    var _html_cursos_cortos = '';
    var total = 0;
    if (carreras.length > 0){
        $.each(carreras, function(key, value){
            cantidad_carreras += parseInt(value.cantidad);
            total += parseInt(value.cantidad);
            _html_carreras += '<tr>';
            _html_carreras += '<td>';
            _html_carreras += value.titulo;
            _html_carreras += '</td>';
            _html_carreras += '<td style="text-align: center;">';
            _html_carreras += value.cantidad;
            _html_carreras += '</td>';
            _html_carreras += '</tr>';
        });
        var temp = '<tr class="row_total_categoria">';
        temp += '<td>';
        temp += 'Carreras';
        temp += '</td>';
        temp += '<td style="text-align: center;">';
        temp += cantidad_carreras;
        temp += '</td>';
        temp += '</tr>';
        _html_carreras = temp + _html_carreras;
    }
    if (cursos_cortos.length > 0){
        $.each(cursos_cortos, function(key, value){
            cantidad_cursos_cortos += parseInt(value.cantidad);
            total += parseInt(value.cantidad);
            _html_cursos_cortos += '<tr>';
            _html_cursos_cortos += '<td>';
            _html_cursos_cortos += value.titulo;
            _html_cursos_cortos += '</td>';
            _html_cursos_cortos += '<td style="text-align: center;">';
            _html_cursos_cortos += value.cantidad;
            _html_cursos_cortos += '</td>';
            _html_cursos_cortos += '</tr>';
        });
        var temp = '<tr class="row_total_categoria">';
        temp += '<td>';
        temp += 'Cursos Cortos';
        temp += '</td>';
        temp += '<td style="text-align: center;">';
        temp += cantidad_cursos_cortos;
        temp += '</td>';
        temp += '</tr>';
        _html_cursos_cortos = temp + _html_cursos_cortos;
    }
    if (seminarios.length > 0){
        $.each(seminarios, function(key, value){
            cantidad_seminarios += parseInt(value.cantidad);
            total += parseInt(value.cantidad);
            _html_seminarios += '<tr>';
            _html_seminarios += '<td>';
            _html_seminarios += value.titulo;
            _html_seminarios += '</td>';
            _html_seminarios += '<td style="text-align: center;">';
            _html_seminarios += value.cantidad;
            _html_seminarios += '</td>';
            _html_seminarios += '</tr>';
        });
        var temp = '<tr class="row_total_categoria">';
        temp += '<td>';
        temp += 'Seminarios';
        temp += '</td>';
        temp += '<td style="text-align: center;">';
        temp += cantidad_seminarios;
        temp += '</td>';
        temp += '</tr>';
        _html_seminarios = temp + _html_seminarios;
    }
    if (cocineritos.length > 0){
        $.each(cocineritos, function(key, value){
            cantidad_cocineritos += parseInt(value.cantidad);
            total += parseInt(value.cantidad);
            _html_cocineritos += '<tr>';
            _html_cocineritos += '<td>';
            _html_cocineritos += value.titulo;
            _html_cocineritos += '</td>';
            _html_cocineritos += '<td style="text-align: center;">';
            _html_cocineritos += value.cantidad;
            _html_cocineritos += '</td>';
            _html_cocineritos += '</tr>';
        });
        var temp = '<tr class="row_total_categoria">';
        temp += '<td>';
        temp += 'Cocineritos';
        temp += '</td>';
        temp += '<td style="text-align: center;">';
        temp += cantidad_cocineritos;
        temp += '</td>';
        temp += '</tr>';
        _html_cocineritos = temp + _html_cocineritos;
    }
    var _tbody = '';
    if (_html_carreras != ''){
        _tbody += _html_carreras;
    }
    if (_html_cursos_cortos != ''){
        _tbody += _html_cursos_cortos;
    }
    if (_html_seminarios != ''){
        _tbody += _html_seminarios;
    }
    if (_html_cocineritos != ''){
        _tbody += _html_cocineritos;
    }
    if (_tbody == ''){
        _tbody += '<tr>';
        _tbody += '<td colspan="2">';
        _tbody += 'No hay datos para mostrar en la tabla';
        _tbody += '</td>';
        _tbody += '</tr>';
    }
    $("[name=table_reporte_alumnos_activos_por_curso]").find('tbody').append(_tbody);
    $("[name=table_reporte_alumnos_activos_por_curso]").find('tfoot').find("[name=total_activos]").html(total);
}

function exportarReporte(tipo_reporte){
    var anio = $("[name=filtro_anio]").val();
    var mes = $("[name=filtro_mes]").val();
    $("[name=form_exportar]").find("[name=tipo_reporte]").val(tipo_reporte);
    $("[name=form_exportar]").find("[name=mes]").val(mes);
    $("[name=form_exportar]").find("[name=anio]").val(anio);
    $("[name=form_exportar]").submit();
}