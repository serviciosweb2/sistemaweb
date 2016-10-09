var oTable = '';
var langfrm_libres;
var claves = Array("BIEN", "ERROR", "validacion_ok", "debe_seleccionar_un_alumno", "volver", "pasar_libres");
var seleccionados = [];
langfrm_libres = langFrm ;

$(document).ready(function()
{
//    $.ajax({
//        url: BASE_URL + 'entorno/getLang',
//        data: "claves=" + JSON.stringify(claves),
//        dataType: 'JSON',
//        type: 'POST',
//        cache: false,
//        async: false,
//        success: function(respuesta) 
//        {
//        //    langfrm_libres = respuesta;
//        }
//    });
    init();
});

function init(){
    console.time('datatable');
    oTable = $('#academicoMatriculas_pasar_a_libres').dataTable({
        "bServerSide": true,
        "sAjaxSource": BASE_URL + 'matriculas/listar_pasar_a_libres',
        "sServerMethod": "POST",
        "aaSorting": [[0, "desc"]],
        "aoColumns": [{"bSortable": false},
         null, 
         null,
         null,
         null
         ],
        "fnServerData": function(sSource, aoData, fnCallback) {
            $("#menuMover").hide();
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
                var complemento = "";
                
                var idx = seleccionados.indexOf(aData[0]); // Localizamos el indice del elemento en array
                if(idx!=-1){
                    complemento ='checked';
                }else{
                    $("[name=seleccionar_todos]").prop("checked",false);
                }
                
            var _html = '';
            _html += '<label class="inline">';
            _html += '<input class="ace" type="checkbox" value="' + aData[0] + '" name="cod_estado_academico" onclick="desactiverSeleccionarTodos(this);" '+complemento+'>';
            _html += '<span class="lbl"></span>';
            _html += '</label>';
            $('td:eq(0)', nRow).html(_html);
            return nRow;
        }
    });
    
    $('.dataTables_length').parent().addClass('no-padding')
   
    var _html = '<div class="btn-group">';
    _html += '<button class="btn btn-primary boton-primario" onclick="document.location.href=\'' + BASE_URL + "matriculas/" + '\'">';
    _html += '<i class="icon-reply"></i>';
    _html += langfrm_libres.volver;
    _html += '</button>';
//    _html += '<div class="inline position-relative align-left" style="float: right;" id="btn_pasar_seleccion">';
//    _html += '<a id="menuMover" class="btn-message btn btn-xs dropdown-toggle" href="#" data-toggle="dropdown" onclick="pasarSeleccionadosALibres();" style="display: none;">';
//    _html += '<span class="bigger-110">';
//    _html += langfrm_libres.pasar_libres;
//    _html += '</span>';
//    _html += '</a>';

    _html += '<div class="btn-group padding3" id="btn_pasar_seleccion" ><button id="menuMover" class="btn btn-primary boton-primario" href="#" data-toggle="dropdown" onclick="pasarSeleccionadosALibres();" style="display: none;">'+langfrm_libres.pasar_libres+'</button></div>';
    
    _html += '</div></div>';
    $("#academicoMatriculas_pasar_a_libres_length").html(_html);
}

function desactiverSeleccionarTodos(element){
   var valor = $(element).val();
    if($(element).is(':checked')){
        seleccionados.push(valor);
    }else{
       var idx = seleccionados.indexOf(valor); // Localizamos el indice del elemento en array
        if(idx!=-1) seleccionados.splice(idx, 1); // Lo borramos definitivamente 
    }
    if (seleccionados.length > 0){
        $("#menuMover").show();
    } else {
        $("#menuMover").hide();
    }
    console.log('final',seleccionados);
}

function checkAllEstadoAcademico(){
    //oTable.$("[name=cod_estado_academico]").prop("checked", $("[name=seleccionar_todos]").prop("checked"));
    oTable.$("[name=cod_estado_academico]").trigger('click');
    console.log('seleccionados',seleccionados);
     //seleccionados = oTable.$("[name=cod_estado_academico]:checked");
    if (seleccionados.length > 0){
        $("#menuMover").show();
    } else {
        $("#menuMover").hide();
    }
}

function pasarSeleccionadosALibres(){
     seleccionados = oTable.$("[name=cod_estado_academico]:checked");
    if (seleccionados.length > 0){
        var estadosacademicos = new Array();
        for (var i = 0; i < seleccionados.length; i++){
            estadosacademicos.push(seleccionados[i].value);
        }
        $.ajax({
            url: BASE_URL + 'matriculas/pasar_a_libres',
            type: 'POST',
            dataType: 'json',
            data: {
                estadosacademicos: estadosacademicos
            },
            success: function(_json){
                if (_json.success){
                    gritter(langfrm_libres.validacion_ok, true, langfrm_libres.BIEN);
                    oTable.fnDraw();
                } else {
                     gritter("", false, langfrm_libres.ERROR);
                }
            }
        });
    } else {
        gritter(langfrm_libres.debe_seleccionar_un_alumno, false, langfrm_libres.ERROR);
    }    
}