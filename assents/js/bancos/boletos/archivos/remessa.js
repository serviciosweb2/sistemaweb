var tablaRetornos;
var leerRetornos;
var frmLang = '';
$(document).ready(function() {
    var claves = Array('volver','descargar_remessa','imprimir_boletos','subiendo_archivo_espere','ocurrio_error','tipo_archivo_invalido','tamaño_archivo_invalido','seleccione_arrastre_archivo');
 $.ajax({
        url: BASE_URL+'entorno/getLang',
        data: "claves=" + JSON.stringify(claves),
        dataType: 'JSON',
        type: 'POST',
        cache: false,
        async: false,
        success: function(respuesta){
            frmLang = respuesta;
        }
    });
        initRemessa();
        initRetorno();
        initEnviados();
//        $(".dataTables_length").html('<div class="col-sm-2"><a href="#" class="btn-back-message-list" onclick="volver();"><i class="icon-arrow-left blue bigger-110 middle"></i><b class="bigger-110 middle">'+frmLang.volver+'</b></a></div>');
        });
        
function initRemessa() {
        oTable = $('#achivosRemessa').dataTable({
        "bProcessing": false,
                "bServerSide": true,
                "sAjaxSource": BASE_URL + 'facturantes/getRemessasDatatable',
                "aaSorting": [[0, "desc"]],
                "sServerMethod": "POST",
                "aoColumnDefs": [
                {"bVisible": false, "aTargets": [0]}],
                "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {

                        var links = '<div class="hidden-sm hidden-xs btn-group">'+
                        '<a href=" ' + BASE_URL + 'facturantes/descargarRemessa/' + aData[0] + '"  class="btn btn-xs btn-primary" >' + 
                        ''+frmLang.descargar_remessa+' <i class = "ace-icon fa icon-cloud-download bigger-120"> </i>'+
                        '</a>'+
                        '<button class="btn btn-xs btn-success" onclick="imprimirRemesa(' + aData[0] + ')" >' + 
                        ''+frmLang.imprimir_boletos+' <i class = "ace-icon  icon-print  bigger-120" > </i>'+
                        '</button>'+
                        '</div>';

                        $('td:eq(2)', nRow).html(links);
                        id = aData[0];

                        if(aData[3] == 1){
                            $('td:eq(3)', nRow).html("<input type='checkbox' name='checkboxRemessa' value='" + id + "' checked></i>");
                        }else{
                            $('td:eq(3)', nRow).html("<input type='checkbox' name='checkboxRemessa' value='" + id + "'></i>");
                        }
                }



        });
                }






function initEnviados() {
        oTableEnviados = $('#achivosRemessaEnviados').dataTable({
        "bProcessing": false,
                "bServerSide": true,
                "sAjaxSource": BASE_URL + 'facturantes/getRemessasEnviadasDatatable',
                "aaSorting": [[0, "desc"]],
                "sServerMethod": "POST",
                "aoColumnDefs": [
                {"bVisible": false, "aTargets": [0]}],
                "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {

                        var links = '<div class="hidden-sm hidden-xs btn-group">'+
                        '<a href=" ' + BASE_URL + 'facturantes/descargarRemessa/' + aData[0] + '"  class="btn btn-xs btn-primary" >' + 
                        ''+frmLang.descargar_remessa+' <i class = "ace-icon fa icon-cloud-download bigger-120"> </i>'+
                        '</a>'+
                        '<button class="btn btn-xs btn-success" onclick="imprimirRemesa(' + aData[0] + ')" >' + 
                        ''+frmLang.imprimir_boletos+' <i class = "ace-icon  icon-print  bigger-120" > </i>'+
                        '</button>'+
                        '</div>';

                        $('td:eq(2)', nRow).html(links);
                        id = aData[0];


                }



        });
                }







function initRetorno() {
        var table = $('#tablaConfirmacion').DataTable({
            "iDisplayLength": 25
        });
        tablaRetornos = $('#achivosRetorno').dataTable({
            "bProcessing": false,
            "bServerSide": true,
            "sAjaxSource": BASE_URL + 'facturantes/getRetornosDatable',
            "aaSorting": [[0, "desc"]],
            "sServerMethod": "POST",
            "aoColumnDefs": [],
            "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
               if(aData[3] == 1){
                    $('td:eq(3)', nRow).html("<i class=' icon-check'></i>");
               }else{
                
                   $('td:eq(3)', nRow).html("<i class=' icon-check-empty'></i>");
                
               }
            }
        });
        $("#subir-retorno").submit(function() {
            return false;
        });
        $('.btn-subir-retorno').click(function() {
            table.clear().draw();
            var formData = new FormData($("#subir-retorno")[0]);
            $.ajax({
                    url: BASE_URL + 'facturantes/confirmarcionRetorno',
                    type: 'POST',
                    data: formData,
                    mimeType: "multipart/form-data",
                    dataType: 'json',
                    async: true,
                    //necesario para subir archivos via ajax
                    cache: false,
                    contentType: false,
                    processData: false,
                    //mientras enviamos el archivo
                    beforeSend: function() {
                        message = $("<span class='before'>"+frmLang.subiendo_archivo_espere+"</span>");
                        showMessage(message);
                    },
                    //una vez finalizado correctamente
    
                    success: function(resultado) {
                        showMessage("");
                        $(".proceso-correcto").addClass("hide");
                        var boletos = Array();
                        $(resultado).each(function(key, fila){
                            ($(".file-name")).each(function(key2, fila2) {
                                if (key2 === key) {
                                    $(fila2).attr("data-title", $(fila2).attr("data-title") + " (" + fila.respuesta + ")");
                                }
                            });
                            if (fila.codigo === 1) {
                                $(fila.boletos).each(function(key, fila) {
                                table.row.add([fila.nosso_numero,
                                fila.sacado_nombre,
                                fila.valor_titulo,
                                fila.valor_titulo_pago,
                                fila.motivo,
                                fila.descripcion]).draw().node();
                            });
                            }
                        });
                        $(".confirmacion-retornos").removeClass("hide");
                        $(".tabla-retornos").addClass("hide");
                    },
                    //si ha ocurrido un error
                    error: function() {
                        message = $("<span class='error'>"+frmLang.ocurrio_error+"</span>");
                        showMessage(message);
                    }
            });
        });
        $('.btn-confirmar-retorno').click(function() {
table.clear().draw();
        var formData = new FormData($("#subir-retorno")[0]);
        $.ajax({
        url: BASE_URL + 'facturantes/sendRetorno',
                type: 'POST',
                data: formData,
                mimeType: "multipart/form-data",
                dataType: 'json',
                async: true,
                //necesario para subir archivos via ajax
                cache: false,
                contentType: false,
                processData: false,
                //mientras enviamos el archivo
                beforeSend: function() {
                message = $("<span class='before'>"+frmLang.subiendo_archivo_espere+"</span>");
                        showMessage(message);
                },
                //una vez finalizado correctamente
                success: function(data) {


                showMessage("");
                        $(".proceso-correcto").show();
                        $(".proceso-correcto").removeClass("hide");
                        $(".tablaConfirmacion").addClass("hide");
                        $(".confirmacion-retornos").addClass("hide");
                        window.setTimeout(resetCorrecto, 2000);
                },
                //si ha ocurrido un error
                error: function() {
                message = $("<span class='error'>"+frmLang.ocurrio_error+"</span>");
                        showMessage(message);
                }
        });
});
        $('#subir-retorno').on('click', '.remove', function() {
resetAll();
});

    $('.btn-leer-retorno').click(function () {
    $.ajax(
        {

                url: BASE_URL + 'facturantes/FTPtoRetorno',
                type: 'POST',
                dataType: 'json',
                async: true,
                //mientras enviamos el archivo
                beforeSend: function() {
                    message = $("<span class='before'>"+frmLang.leyendo_archivos_espere+"</span>");
                    showMessage(message);
                },
                //una vez finalizado correctamente
                success: function(resultado) {
                    showMessage("");
                    $(".proceso-correcto").addClass("hide");
                    var boletos = Array();
                    $(resultado).each(function(key, fila){
                        ($(".file-name")).each(function(key2, fila2) {
                            if (key2 === key) {
                                $(fila2).attr("data-title", $(fila2).attr("data-title") + " (" + fila.respuesta + ")");
                            }
                        });
                        if (fila.codigo === 1) {
                            $(fila.boletos).each(function(key, fila) {
                            table.row.add([fila.nosso_numero,
                            fila.sacado_nombre,
                            fila.valor_titulo,
                            fila.valor_titulo_pago,
                            fila.motivo,
                            fila.descripcion]).draw().node();
                        });
                        }
                    });
                    $(".confirmacion-retornos").removeClass("hide");
                    $(".tabla-retornos").addClass("hide");
                },
                //si ha ocurrido un error
                error: function() {
                    message = $("<span class='error'>"+frmLang.ocurrio_error+"</span>");
                    showMessage(message);
                }
        }
    );
});

        }

function imprimirRemesa(codigo_remesa){

var param = new Array();
        param.push(codigo_remesa);
        printers_jobs(13, param);
        }

function showMessage(mensaje) {
$(".repuesta").html("");
        $(".repuesta").html(mensaje);
        }

function volver() {
location.href = BASE_URL + "boletos";
        }

function resetCorrecto(){
    $(".proceso-correcto").fadeOut(800, function(){
    $('#file-retorno').ace_file_input('reset_input');
            $(".tabla-retornos").removeClass("hide");
            tablaRetornos.fnDraw();
    });
}

function resetAll() {
$(".confirmacion-retornos").addClass("hide");
        $(".proceso-correcto").fadeOut(100);
        $(".tablaConfirmacion").removeClass("hide");
        $('#file-retorno').ace_file_input('reset_input');
        $(".tabla-retornos").removeClass("hide");
}

var moveToRemessa = function() {
    var values = [];
    $("[name=checkboxRemessa]").each(function(index, obj){
        if(this.checked){
            values.push($(this).val());
        }
    });
    $.ajax({
        url: BASE_URL + 'facturantes/remessaToFTP',
        type: 'POST',
        dataType: 'json',
        data: {
            ids:values
        },
        success: function(data) {
            if(data.estado){
                $.gritter.add({
                     title: 'Ok',
                     text: 'Remesas enviadas.',
                     sticky: false,
                     time: '3000',
                     class_name:'gritter-success'
                });
                debugger;
                oTable.DataTable().ajax.reload();
                oTableEnviados.DataTable().ajax.reload();
            } else {
                $.gritter.add({
                     title: 'Error!',
                     text: 'Error al enviar las remesas',
                     sticky: false,
                     time: '3000',
                     class_name:'gritter-error'
                });
            }
        }
    });
}




jQuery(function($) {
var $form = $('#subir-retorno');
        var file_input = $form.find('input[type=file]');
        var upload_in_progress = false;
        file_input.ace_file_input({
                style: 'well',
                btn_choose: frmLang.seleccione_arrastre_archivo,
                btn_change: null,
                droppable: true,
                thumbnail: 'large',
                maxSize: 110000, //bytes
                allowExt: ["rem"],
                allowMime: ["text/plain"],
                before_remove: function() {
                if (upload_in_progress)
                    return false; //if we are in the middle of uploading a file, don't allow resetting file input
                    return true;
                },
                preview_error: function(filename, code) {
                //code = 1 means file load error
                //code = 2 image load error (possibly file is not an image)
                //code = 3 preview failed
                }
        })
        file_input.on('file.error.ace', function(ev, info) {
        if (info.error_count['ext'] || info.error_count['mime'])
                alert(frmLang.tipo_archivo_invalido);
                if (info.error_count['size'])
                alert(frmLang.tamaño_archivo_invalido);
        });
        });


