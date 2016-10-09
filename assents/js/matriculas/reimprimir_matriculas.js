var claves = Array("BIEN", "ERROR", "seleccione_una_matricula_para_imprimir");
var frm_reimprimir_matricula = '';
$("#reimprimir_matriculas").ready(function(){
    $.ajax({
    url: BASE_URL + 'entorno/getLang',
        data: "claves=" + JSON.stringify(claves),
        dataType: 'JSON',
        type: 'POST',
        cache: false,
        async: false,
        success: function(respuesta) {
            frm_reimprimir_matricula = respuesta;
            init();
        }
    });    
});

function init(){
    $("[name=btn_reimrpimir]").on("click", function(){
        
        var codigo_matricula = $("[name=codigo_matricula]:checked").val();
        if (codigo_matricula){
            var param = new Array();
            param.push(codigo_matricula, 1, 1,"reimprimir");
            printers_jobs(5, param);
        } else {
            gritter(frm_reimprimir_matricula.seleccione_una_matricula_para_imprimir, false, frm_reimprimir_matricula.ERROR);
        }
    });
}