$(document).ready(function(){
    $(".date_picker").datepicker();
    listar();
});


function listar(){
    var fecha_desde = $("[name=filtro_fecha_desde]").val();
    var fecha_hasta = $("[name=filtro_fecha_hasta]").val();
    $.ajax({
        url: BASE_URL + 'reportes/listado_inscripciones_y_bajas',
        type: 'POST',
        dataType: 'json',
        data: {
            fecha_desde: fecha_desde,
            fecha_hasta: fecha_hasta
        },
        success: function(_json){
            armarTabla(_json);
        }
    });
}

function armarTabla(_json){
    $("[name=table_reporte_inscripciones_y_bajas]").find("tbody").find("tr").remove();
    $("[name=table_reporte_inscripciones_y_bajas]").find("tfoot").find("[name=total_inscripciones]").html("0");
    $("[name=table_reporte_inscripciones_y_bajas]").find("tfoot").find("[name=total_bajas]").html("0");
    var carreras = _json.carreras;
    var cursos_cortos = _json.cursos_cortos;
    var cocineritos = _json.cocineritos;
    var seminarios = _json.seminarios;
    //console.log(seminarios);
    var cantidad_carreras = 0;
    var cantidad_carreras_bajas = 0;
    var cantidad_cursos_cortos = 0;
    var cantidad_cursos_cortos_bajas = 0;
    var cantidad_cocineritos = 0;
    var cantidad_cocineritos_bajas = 0;
    var cantidad_seminarios = 0;
    var cantidad_seminarios_bajas = 0;
    
    var _html_carreras = '';
    var _html_cocineritos = '';
    var _html_cursos_cortos = '';
    var _html_seminarios = '';
    
    var total = 0;
    var total_bajas = 0;
    
    var fecha_desde = $("[name=filtro_fecha_desde]").val();
    var fecha_hasta = $("[name=filtro_fecha_hasta]").val();
    
    if (carreras.length > 0){
        $.each(carreras, function(key, value){
            cantidad_carreras += parseInt(value.inscriptos);
            cantidad_carreras_bajas += parseInt(value.bajas);
            total += parseInt(value.inscriptos);
            total_bajas += parseInt(value.bajas);
            _html_carreras += '<tr class="carreras">';
            _html_carreras += '<td>';
            if(value.cod_plan_academico == 58 || value.cod_plan_academico == 148){
            _html_carreras += value.titulo + " / " + value.plan;
            }else{
                _html_carreras += value.titulo;
            }
            _html_carreras += '</td>';
            _html_carreras += '<td style="text-align: center;">';
            _html_carreras += value.inscriptos;
            _html_carreras += '</td>';
            _html_carreras += '<td style="text-align: center;"><a href="'+ BASE_URL + 'reportes/bajas?fecha_desde=' + fecha_desde + 
                    '&fecha_hasta=' + fecha_hasta + 
                    '&codCurso=' + value.cod_curso + 
                    '&cod_plan_academico=' + value.cod_plan_academico + 
                    '&titulo=' + value.titulo + 
                    '&desdeInscripcionesYbajas=1">';
            _html_carreras += value.bajas;
            _html_carreras += '</a></td>';
            _html_carreras += '</tr>';
        });
        var temp = '<tr class="row_total_categoria carreras">';
        temp += '<td>';
        temp += 'Carreras';
        temp += '</td>';
        temp += '<td style="text-align: center;">';
        temp += cantidad_carreras;
        temp += '</td>';
        temp += '<td style="text-align: center;">';
        temp += cantidad_carreras_bajas;
        temp += '</td>';
        temp += '</tr>';
        _html_carreras = temp + _html_carreras;
    }
    if (cursos_cortos.length > 0){
        $.each(cursos_cortos, function(key, value){
            cantidad_cursos_cortos += parseInt(value.inscriptos);
            cantidad_cursos_cortos_bajas += parseInt(value.bajas);
            total += parseInt(value.inscriptos);
            total_bajas += parseInt(value.bajas);
            _html_cursos_cortos += '<tr class="cursos_cortos">';
            _html_cursos_cortos += '<td>';
            _html_cursos_cortos += value.titulo;
            _html_cursos_cortos += '</td>';
            _html_cursos_cortos += '<td style="text-align: center;">';
            _html_cursos_cortos += value.inscriptos;
            _html_cursos_cortos += '</td>';
            _html_cursos_cortos += '<td style="text-align: center;"><a href="'+BASE_URL + 'reportes/bajas?fecha_desde='+fecha_desde+'&fecha_hasta='+fecha_hasta+'&codCurso='+value.cod_curso+'&cod_plan_academico='+value.cod_plan_academico+'&codigo='+value.codigo+'&titulo='+value.titulo+'&cod_tipo_periodo='+value.cod_tipo_periodo+'&desdeInscripcionesYbajas=1">';
            _html_cursos_cortos += value.bajas;
            _html_cursos_cortos += '</a></td>';
            _html_cursos_cortos += '</tr>';
        });
        var temp = '<tr class="row_total_categoria cursos_cortos">';
        temp += '<td>';
        temp += 'Cursos Cortos';
        temp += '</td>';
        temp += '<td style="text-align: center;">';
        temp += cantidad_cursos_cortos;
        temp += '</td>';
        temp += '<td style="text-align: center;">';
        temp += cantidad_cursos_cortos_bajas;
        temp += '</td>';
        temp += '</tr>';
        _html_cursos_cortos = temp + _html_cursos_cortos;
    }
    if (cocineritos.length > 0){
        $.each(cocineritos, function(key, value){
            cantidad_cocineritos += parseInt(value.inscriptos);
            cantidad_cocineritos_bajas += parseInt(value.bajas);
            total += parseInt(value.inscriptos);
            total_bajas += parseInt(value.bajas);
            _html_cocineritos += '<tr class="cocineritos">';
            _html_cocineritos += '<td>';
            _html_cocineritos += value.titulo;
            _html_cocineritos += '</td>';
            _html_cocineritos += '<td style="text-align: center;">';
            _html_cocineritos += value.inscriptos;
            _html_cocineritos += '</td>';
            _html_cocineritos += '<td style="text-align: center;"><a href="'+BASE_URL + 'reportes/bajas?fecha_desde='+fecha_desde+'&fecha_hasta='+fecha_hasta+'&codCurso='+value.cod_curso+'&cod_plan_academico='+value.cod_plan_academico+'&codigo='+value.codigo+'&titulo='+value.titulo+'&cod_tipo_periodo='+value.cod_tipo_periodo+'&desdeInscripcionesYbajas=1">';
            _html_cocineritos += value.bajas;
            _html_cocineritos += '</a></td>';
            _html_cocineritos += '</tr>';
        });
        var temp = '<tr class="row_total_categoria cocineritos">';
        temp += '<td>';
        temp += 'Cocineritos';
        temp += '</td>';
        temp += '<td style="text-align: center;">';
        temp += cantidad_cocineritos;
        temp += '</td>';
        temp += '<td style="text-align: center;">';
        temp += cantidad_cocineritos_bajas;
        temp += '</td>';
        temp += '</tr>';
        _html_cocineritos = temp + _html_cocineritos;
    }
    if (seminarios.length > 0){
        $.each(seminarios, function(key, value){
            cantidad_seminarios += parseInt(value.inscriptos);
            cantidad_seminarios_bajas += parseInt(value.bajas);
            total += parseInt(value.inscriptos);
            total_bajas += parseInt(value.bajas);
            _html_seminarios += '<tr class="seminarios">';
            _html_seminarios += '<td>';
            _html_seminarios += value.titulo;
            _html_seminarios += '</td>';
            _html_seminarios += '<td style="text-align: center;">';
            _html_seminarios += value.inscriptos;
            _html_seminarios += '</td>';
            _html_seminarios += '<td style="text-align: center;"><a href="'+BASE_URL + 'reportes/bajas?fecha_desde='+fecha_desde+'&fecha_hasta='+fecha_hasta+'&codCurso='+value.cod_curso+'&cod_plan_academico='+value.cod_plan_academico+'&codigo='+value.codigo+'&titulo='+value.titulo+'&cod_tipo_periodo='+value.cod_tipo_periodo+'&desdeInscripcionesYbajas=1">';
            _html_seminarios += value.bajas;
            _html_seminarios += '</a></td>';
            _html_seminarios += '</tr>';
        });
        var temp = '<tr class="row_total_categoria seminarios">';
        temp += '<td>';
        temp += 'Seminarios';
        temp += '</td>';
        temp += '<td style="text-align: center;">';
        temp += cantidad_seminarios;
        temp += '</td>';
        temp += '<td style="text-align: center;">';
        temp += cantidad_seminarios_bajas;
        temp += '</td>';
        temp += '</tr>';
        _html_seminarios = temp + _html_seminarios;
    }
    
    
    var _tbody = '';
    if (_html_carreras != ''){
        _tbody += _html_carreras;
    }
    if (_html_cursos_cortos != ''){
        _tbody += _html_cursos_cortos;
    }
    if (_html_cocineritos != ''){
        _tbody += _html_cocineritos;
    }
    if (_html_seminarios != ''){
        _tbody += _html_seminarios;
    }
    if (_tbody == ''){
        _tbody += '<tr>';
        _tbody += '<td colspan="3">';
        _tbody += 'No hay datos para mostrar en la tabla';
        _tbody += '</td>';
        _tbody += '</tr>';
    }    
    $("[name=table_reporte_inscripciones_y_bajas]").find('tbody').append(_tbody);
    $("[name=table_reporte_inscripciones_y_bajas]").find('tfoot').find("[name=total_inscripciones]").html(total);
    $("[name=table_reporte_inscripciones_y_bajas]").find('tfoot').find("[name=total_bajas]").html(total_bajas);
}

function exportar(tipo_reporte){
    var fecha_desde = $("[name=filtro_fecha_desde]").val();
    var fecha_hasta = $("[name=filtro_fecha_hasta]").val();
    $("[name=form_exportar]").find("[name=fecha_desde]").val(fecha_desde);
    $("[name=form_exportar]").find("[name=fecha_hasta]").val(fecha_hasta);
    $("[name=form_exportar]").find("[name=tipo_reporte]").val(tipo_reporte);
    $("[name=form_exportar]").submit();
}