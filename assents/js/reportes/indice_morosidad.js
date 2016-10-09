$(document).ready(function(){
    $(".select_chosen").chosen();
    seleccionar_periodo();
});


function seleccionar_periodo(){
    var year = $("[name=filtro_año]").val();

    if(year == null)
    {
        year = 2016;
    }
    $.ajax({
        url: BASE_URL + 'reportes/listar_indice_morosidad',
        type: 'POST',
        dataType: 'json',
        data: {
            year: year
        },
        success: function(_json){
            armarTabla(_json);
        }
    });
}


function armarTabla(_json){
    $("[name=table_reporte_indice_morosidad]").find("tbody").find("tr").remove();

    var importe = _json.importe;

    console.log(_json);
    console.log(_json.importe);

    var _html_importe = '';

    for (var key in _json) {

        _html_importe += '<tr class="importe">';

        _html_importe += '<td>';
        _html_importe += _json[key]["mes"];
        _html_importe += '</td>';

        _html_importe += '<td>';
        _html_importe += _json[key]["importe"];
        _html_importe += '</td>';

        _html_importe += '<td>';
        _html_importe += _json[key]["imputado"];
        _html_importe += '</td>';

        _html_importe += '<td>';
        _html_importe += _json[key]["saldo"];
        _html_importe += '</td>';

        _html_importe += '<td>';
        _html_importe += _json[key]["morosidad"];
        _html_importe += '</td>';

        _html_importe += '<td>';
        _html_importe += _json[key]["imputado_total"];
        _html_importe += '</td>';

        _html_importe += '<td>';
        _html_importe += _json[key]["morosidad_total"];
        _html_importe += '</td>';

        _html_importe += '</tr>';
    }

    var _tbody = '';
    if (_html_importe != ''){
        _tbody += _html_importe;
    }
    if (_tbody == ''){
        _tbody += '<tr>';
        _tbody += '<td colspan="3">';
        _tbody += 'No hay datos para mostrar en la tabla';
        _tbody += '</td>';
        _tbody += '</tr>';
    }
    $("[name=table_reporte_indice_morosidad]").find('tbody').append(_tbody);

}


function exportar(tipo_reporte){

    var year = $("[name=filtro_año]").val();

    $("[name=form_exportar]").find("[name=year]").val(year);
    $("[name=form_exportar]").find("[name=tipo_reporte]").val(tipo_reporte);
    $("[name=form_exportar]").submit();
}