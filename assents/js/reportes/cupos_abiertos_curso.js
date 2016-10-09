
$(document).ready(function(){
    $(".select_chosen").chosen();
    listar();
});

var unaGlobal;
function listar(){
    var curso = $("[name=filtro_curso]").val();
    if(curso != ''){
    }
    $("[name=table_reporte_cupos_abiertos_por_curso]").find('tbody').find("tr").remove();
    $("[name=table_reporte_cupos_abiertos_por_curso]").find('tfoot').find("[name=total_cupos]").html("0");
    $.ajax({
        url: BASE_URL + 'reportes/listar_cupos_abiertos_curso',
        type: 'POST',
        dataType: 'json',
        data:{
            curso: curso
        },
        success: function(_json){
            armarTabla(_json);
        },
        error: function(a){
            console.log(a);
        }
    });
}



function armarTabla(response_json){
    $("[name=table_reporte_cupos_abiertos_por_curso]").find('tbody').find("tr").remove();
    $("[name=table_reporte_cupos_abiertos_por_curso]").find('tfoot').find("[name=total_cupos]").html("0");
    var cupoGeneral = 0;
    var linea = '';
    var cupoCursosCortos = 0;
    var flagCursosCortos = false;
    $.each(response_json, function(clave, datosCarrera){
        var carrera = datosCarrera.curso;
        var cupoCarrera = 0;
        if(datosCarrera.tipo_curso == 'curso'){
           linea += '<tr class="row_header_categoria"><td colspan=3>' + carrera + '</td></tr>';
        } else {
           if(!flagCursosCortos)
               linea += '<tr class="row_header_categoria"><td colspan=3>' + diccionario.cursoCorto + '</td></tr>';
           flagCursosCortos = true;
           linea += '<tr class="row_header_categoria_corta"><td style="padding-left:20px;" colspan=3>' + carrera + '</td></tr>';
        }
        $.each(datosCarrera.comisiones, function (clave, datosComision){
            cupoGeneral += parseInt(datosComision.cupo);
            cupoCarrera += parseInt(datosComision.cupo);
            if(datosCarrera.tipo_curso != 'curso'){
                cupoCursosCortos += parseInt(datosComision.cupo);
            }
            var fechaInicio = Date.parse(datosComision.inicio);
            linea += '<tr>';
            linea += '<td>';
            linea += datosComision.nombre;
            linea += '</td>';
            linea += '<td style="text-align:center">';
            linea += datosComision.cupo;
            linea += '</td>';
            linea += '<td style="text-align:center">';
            linea += datosComision.cierre;
            linea += '</td>';
            linea += '</tr>';
        });
        var temp = '';
        if(datosCarrera.tipo_curso == 'curso'){
            temp = '<tr class="row_total_categoria">';
            temp += '<td/>'
            temp += '<td colspan=2 style="text-align:right;">';
            temp += diccionario.totalCurso + ' ' + carrera;
            temp += '</td>';
            temp += '<td style="text-align:center;">' + cupoCarrera + '</td>';
            temp += '</tr>';
        }
        else {
            temp = '<tr class="row_total_categoria_corta" style="background-color:#000;">';
            temp += '<td style="text-align:left;padding-left:30px;">';
            temp += diccionario.totalCurso + ' ' + carrera;
            temp += '</td>';
            temp += '<td style="text-align:center;">' + cupoCarrera + '</td><td></td>';
            temp += '</tr>';
        }
        linea += temp;
    });
    var temp = '';
    if(cupoCursosCortos > 0)
    {
            temp = '<tr class="row_total_categoria">';
            temp += '<td/>'
            temp += '<td colspan=2 style="text-align:right;">';
            temp += "Total " + diccionario.cursoCorto;
            temp += '</td>';
            temp += '<td style="text-align:center;">' + cupoCursosCortos + '</td>';
            temp += '</tr>';
    }
    linea += temp;
    $("[name=table_reporte_cupos_abiertos_por_curso]").find('tbody').append(linea);
    $("[name=table_reporte_cupos_abiertos_por_curso]").find('tfoot').find("[name=total_cupos]").html(cupoGeneral);
}

function exportarReporte(tipo_reporte){
    var curso = $("[name=filtro_curso]").val();
    $("[name=form_exportar]").find("[name=tipo_reporte]").val(tipo_reporte);
    $("[name=form_exportar]").find("[name=curso]").val(curso);
    $("[name=form_exportar]").submit();
}
