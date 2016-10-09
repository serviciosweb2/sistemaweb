$(document).ready(function(){
    $("#estadosAcademicos").dataTable();
});

function cambiar_estado(cod_estado_academico, estado, element){
    $(element).prop("disabled", true);
    $.ajax({
        url: BASE_URL + 'matriculas/cambiar_estado_academico',
        type: 'POST',
        dataType: 'json',
        data: {
            cod_estado_academico: cod_estado_academico,
            estado: estado
        },
        success: function(_json){
            if (_json.error){
                gritter(_json.error);
                $(element).prop("disabled", false);
            } else {
                $(element).closest("tr").hide();
            }
        }
    });
}