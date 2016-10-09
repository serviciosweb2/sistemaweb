
var lang = BASE_LANG;

var cierreFancy=0;

var selectedDecimales = BASE_SEPARADOR;

var selectedMiles= BASE_SEPARADORMILES;


var separadores=Array(
        {id:'.',nombre:'.'},
        {id:',',nombre:','},
        {id:'*',nombre:'*'}
       );


//var claves = Array('Utilizar_en_todas_las_impresiones',
//                'Esta_impresora_se_utiliza_en_todas_las_impresiones',
//                'Imprimir_por_el_navegador',
//                'Esta_es_la_impresora_por_default',
//                'No_hay_impresoras_registradas_en_cloud_print',
//                'horario_de_entrada',
//                'del_dia',
//                'horario_de_salida',
//                'debe_ser_menor_que',
//                'datos_actualizados_correctamente',
//                'ocurrio_error',
//                'validacion_ok',
//                "fecha_desde",
//                "fecha_hasta",
//                "estado",
//                "nombre",
//                "detalle",
//                "ver_editar","validacion_ok","ocurrio_error",
//                "horadesde_horario",
//                "horaHasta_horario"
//                );
 function advertenciaLOGOUT(){
     
     $.fancybox.open(['#logOutMSJ'],{
                maxWidth	: 1000,
		maxHeight	: 600,
		width		: '90%',
		height		: 'auto',
		autoSize	: true,
		closeClick	: false,
                padding         : '0',
		openEffect	: 'none',
		closeEffect	: 'none',
                afterClose:function(){
                    logOut();
                },
                helpers: {
                                    overlay: null
                                }
    });
     
 } 
 function logOut(){
     
     window.location.href = BASE_URL+'login/logOut';
 }
 function guardarDiasCobroFilial(){
     $.ajax({
            url: BASE_URL + "configuracion/guardarDiasCobroFilial",
            data: $("#frmCobroFilial").serialize(),
            type: "POST",
            dataType: "JSON",
            async: false,
            cache: false,
            success: function(respuesta) {
                if(respuesta){
                    gritter(lang.validacion_ok,true);
                }else{
                    gritter('ocurrio_error');
                }
                
            }
        });
        return false;
 }               
                
function habilitarOffline(element)
{   
    var valor = 0;
    
    if($(element).is(':checked'))
    {
        
        $.ajax({
            url: BASE_URL+"configuracion/frm_offline",
            type: "POST",
            data: "",
            dataType:"",
            cache:false,
            success:function(respuesta){
                $.fancybox.open(respuesta,{
                    padding:0,
                afterClose:function(){
               
                },

                    helpers: {
                              overlay: null
                             }
                });
            }
        });
        
    }else{
        
         $.ajax({
            url: BASE_URL+"configuracion/guardarConfiguracionOffline",
            type: "POST",
            data: {valor:valor,nombre:'modoOffline'},
            dataType:"JSON",
            cache:false,
            success:function(respuesta){
                console.log('RESPUESTA',respuesta);
                if(respuesta.codigo==1){
                    
                    cierreFancy=0;
                    localStorage.removeItem('tkoff');
                    localStorage.removeItem('tkpin');
                    localStorage.removeItem('last_id');
                    localStorage.removeItem('ultimoId_bancos');
                    localStorage.removeItem('ultimoId_Tarjetas');
                    
                    BASE_OFFLINE.habilitado=0;
                    
                    $.fancybox.close(true);
                    
                    gritter(lang.validacion_ok); 
                    
                    advertenciaLOGOUT();
                    
                    
                    
                }
            }
        });
        
    }
    
    
    
    

    
}
function cargarSelectSeparadores(nombre,valorSeteado){
        
       $('select[name="'+nombre+'"]').empty();
        
        
        $(separadores).each(function(k,item){
    
        var s= item.id == valorSeteado ? 'selected' : '';
    
        $('select[name="'+nombre+'"]').append('<option value="'+item.id+'" '+s+'>'+item.nombre+'</option>');
    
    });
        
    };
function validarSeparador(elemento){
   
    var nombre=$(elemento).attr('name');
    
    var valor=$(elemento).val();
    
    var mensaje='';
    
    var guardar=true;
    
    var x='';
    
    switch(nombre){
        
        case 'SeparadorDecimal':
         
            x= selectedDecimales;   
            if(selectedMiles==valor){
                
               mensaje='_SEPARADOR EN USO PARA MILES ';
               guardar=false;
              
               
               
               
            }else{
                
                selectedDecimales=valor
                
            }
        break;
        
        case 'SeparadorMiles':
            
            x= selectedMiles; 
            
             if(selectedDecimales==valor){
                 
                 mensaje='_SEPARADOR EN USO PARA DECIMALES';
                 guardar=false;
                
               
            }else{
                
                
                selectedMiles=valor
                
            }
            
            
            
            
        break;
        
    }
    
    
    if(guardar==false){
        
        
       
       
       
     $('select[name="'+nombre+'"]').trigger("chosen:updated");
        $.gritter.add({
                    title: 'OK!',
                    text:mensaje,
                    //image: $path_assets+'/avatars/avatar1.png',
                    sticky: false,
                    time: '3000',
                    class_name:'gritter-error'
                });
        
         cargarSelectSeparadores(nombre,x);
         $('select[name="'+nombre+'"]').trigger("chosen:updated");
        
    }else{
        
        $.ajax({
                url: BASE_URL+"configuracion/guardarSeparadores",
                type: "POST",
                data: "nombre="+nombre+"&valor="+valor,
                dataType:"JSON",
                cache:false,
                success:function(respuesta){
                    if(respuesta.codigo==1){
                        
                        $.gritter.add({
                                    title: 'OK!',
                                    text: 'Guardado Correctamente',
                                    //image: $path_assets+'/avatars/avatar1.png',
                                    sticky: false,
                                    time: '3000',
                                    class_name:'gritter-success'
                        });
                        
                    }
                }
});
        
        
    }
    
    
    
    
};
function validarChkOffline(){
    if(localStorage.getItem("tkoff") == null && BASE_OFFLINE.token!='')
        {// si no existe el item y el token no esta vacio significa que el equipo no tiene permiso offline
            console.log('no tiene permiso offline');
            $('input[name="chkOFFLINE"]').prop('disabled',true);
        
        }
        
        
        if(localStorage.getItem("tkoff") == null && BASE_OFFLINE.token=='')
        {// si en el local no existe el item y en el server tampoco el usuario puede configurar offline
            console.log('puede configurar offline');
            $('input[name="chkOFFLINE"]').prop('disabled',false);
        
        }
        
        
        if(localStorage.getItem("tkoff") != null && BASE_OFFLINE.token!='')
        {// si existe el item en local y en la base ,valido que sean iguales
            if(localStorage.getItem("tkoff") == BASE_OFFLINE.token)
            {
                console.log('son iguales');
                $('input[name="chkOFFLINE"]').prop('disabled',false);
            }
            else
            {
                console.log('no son iguales');
                $('input[name="chkOFFLINE"]').prop('disabled',true);
            }
            
        
        }
        
        
}

function baja_receso(element){
    var codigo_receso =$(element).val();
    $.ajax({
                url: BASE_URL + "configuracion/baja_receso_filial",
                type: "POST",
                data: "cod_receso=" + codigo_receso,
                dataType:"JSON",
                cache:false,
                success: function(respuesta) {
                    if(respuesta.codigo == 1){
                        gritter(lang.validacion_ok,true);
                        dibujarTablaRecesoFilial();
                    }else{
                        gritter(lang.ocurrio_error);
                    }
                }
            });
            
            return false;
}

function dibujarTablaRecesoFilial(){
    $.ajax({
        url: BASE_URL + "configuracion/getListadoRecesoFilial",
        type: "POST",
        data: "",
        dataType: "JSON",
        cache: false,
        async: true,
        success: function(respuesta) {
            var tabla = '<table id="tablaReceso" class="table table-striped table-bordered"><thead>';
            tabla += '<th>' + lang.nombre + '</th><th>' + lang.fecha_desde + '</th><th>' + lang.fecha_hasta + '</th><th>' + lang.horadesde_horario + '</th><th>' + lang.horaHasta_horario + '</th><th>' + lang.estado + '</th><th>' + lang.detalle + '</th></thead>';
            tabla += '<tbody>';
            $(respuesta).each(function(k, item) {
                var selected = item.estado == 'habilitada' ? 'checked' : ''; 
                tabla+= '<tr><td>' + item.nombre + '</td><td>' + item.fecha_desde +'</td><td>' + item.fecha_hasta + '<td>' + item.hora_desde + '</td><td>' + item.hora_hasta + '</td><td class="text-right"><label><input name="baja_receso" class="ace ace-switch ace-switch-6" type="checkbox" onchange="baja_receso(this);"  value="'+item.codigo+'"  '+selected+'><span class="lbl"></span></label></td><td><span class="label label-success arrowed" data-detalle="'+item.codigo+'">'+lang.ver_editar+'</span></td></tr>'; 
//                tabla += "<tr><td>" + item.nombre + "</td><td>" + item.estado + "</td><td><span class='label label-success arrowed'  data-detalle='" + JSON.stringify(data) + "'>" + lang.ver_editar + "</span></td></tr>";
            });
            tabla += '</tbody></tabla>';
            $('#widgetReceso .table-responsive').html(tabla);
        }
    });
}

function nuevo_receso(cod_receso){
    $.ajax({
                url: BASE_URL + "configuracion/frm_receso_filial",
                type: "POST",
                data: 'cod_receso=' + cod_receso,
                cache: false,
                success: function(respuesta) {
                    $.fancybox.open(respuesta, {
                        padding: 0,
                        width: 'auto',
                        openEffect: 'none',
                        closeEffect: 'none',
                        helpers: {
                            overlay: null
                        }
                    });
                }
            });
            return false;
}

$(document).ready(function() {


//    $.ajax({
//        url: BASE_URL + 'entorno/getLang',
//        data: "claves=" + JSON.stringify(claves),
//        dataType: 'JSON',
//        type: 'POST',
//        cache: false,
//        async: false,
//        success: function(respuesta) {
//            lang = respuesta;
//            init();
//            
//           if ($("#listarCloudPrint").val() == 1){
//              
//                getPrintersList($("#googleAccountFilial").val());
//           }
//               
//        }
//    });
    
    
    init();
    
    if ($("#habilitar_cloud_print").is(':checked')){
              
        getPrintersList($("#googleAccountFilial").val());
    }
    
    validarChkOffline()
    dibujarTablaRecesoFilial();
    $('#widgetReceso').on('click', '[data-detalle]', function() {
            $.ajax({
                url: BASE_URL + "configuracion/frm_receso_filial",
                type: "POST",
                data: 'cod_receso=' + $(this).attr('data-detalle'),
                cache: false,
                success: function(respuesta) {
                    $.fancybox.open(respuesta, {
                        padding: 0,
                        width: 'auto',
                        openEffect: 'none',
                        closeEffect: 'none',
                        helpers: {
                            overlay: null
                        }
                    });
                }
            });
            return false;
        });
        
   
});

function configuracionExtraImpresion(scriptId, scriptName){
    var complemento_url = 'configuracion_impresion_extra/';
    switch (scriptId){
        case 11:
            complemento_url = 'configuracion_impresion_facturacion/';
            break;
            
        case 1:
        case 5:
            complemento_url = 'configuracion_impresion_con_texto/';
            break;
    }
    
    complemento_url += scriptId + "/" + scriptName;
    
    $.ajax({
        url: BASE_URL + 'configuracion/' + complemento_url,
        type:'POST',
        cache:false,
        success:function(respuesta){
            $.fancybox.open(respuesta,
                {
                    width: 'auto',
                    height: 'auto',
                    scrolling:'auto',
                    autoSize: false,
                    padding: 1,
                    openEffect:'none',
                    closeEffect:'none',
                    helpers:  {
                        overlay : null
                    }
                }
            );
        }
    });
}

function guardarHorarioFilial(){
    $("#btnGuardarHorariosAtencion").attr("disabled", true);
    $("#msg_error3").html("");
    var especifica_horario = $("[name=form-field-radio]:checked").val() == "especifica" ? 1 : 0;
    var doble_horario = $("[name=indicar_dos_horarios]").is(":checked") ? 1 : 0;
    var dias_atencion = $("[name=dias_de_atencion]");
    var mensaje = '';
    for (var i = 0; i < dias_atencion.length; i++){
        var nombre_dia = dias_atencion[i].value;
        if (!$("#cerrado_" + nombre_dia).is(":checked")){                
            var h1 = $("#" + nombre_dia + "_e1").val();
            var h2 = $("#" + nombre_dia + "_s1").val();
            var h3 = $("#" + nombre_dia + "_e2").val();
            var h4 = $("#" + nombre_dia + "_s2").val();
            if (h1 >= h2 ) mensaje += lang.horario_de_entrada + " 1 " + lang.del_dia + " " + nombre_dia + " " + lang.debe_ser_menor_que + "  " + lang.horario_de_salida + " 1<br>";
            if (doble_horario){
                if (h3 != h4){
                    if (h3 >= h4) mensaje += lang.horario_de_entrada + " 2 " + lang.del_dia + " " + nombre_dia + " " + lang.debe_ser_menor_que + " " + lang.horario_de_salida + " 2<br>";
                    if (h1 >= h3) mensaje += lang.horario_de_entrada + " 1 " + lang.del_dia + " " + nombre_dia + " " + lang.debe_ser_menor_que + " " + lang.horario_de_entrada + " 2<br>";
                }
            }
        }
    }
    
    if (mensaje != ''){
        $("#msg_error3").html("<div class='alert alert-danger' style='margin-bottom: 0px; padding: 6px 15px'>" + mensaje + "</div>");
        $("#btnGuardarHorariosAtencion").attr("disabled", false);
    } else {    
        var lunes = {
                        e1: $("#lunes_e1").val(),
                        s1: $("#lunes_s1").val(),
                        e2: $("#lunes_e2").val(),
                        s2: $("#lunes_s2").val(),
                        cerrado: $("#cerrado_lunes").is(":checked") ? 1 : 0
                    };
        var martes = {
                        e1: $("#martes_e1").val(),
                        s1: $("#martes_s1").val(),
                        e2: $("#martes_e2").val(),
                        s2: $("#martes_s2").val(),
                        cerrado: $("#cerrado_martes").is(":checked") ? 1 : 0
                    };
        var miercoles = {
                        e1: $("#miercoles_e1").val(),
                        s1: $("#miercoles_s1").val(),
                        e2: $("#miercoles_e2").val(),
                        s2: $("#miercoles_s2").val(),
                        cerrado: $("#cerrado_miercoles").is(":checked") ? 1 : 0
                    };
        var jueves = {
                        e1: $("#jueves_e1").val(),
                        s1: $("#jueves_s1").val(),
                        e2: $("#jueves_e2").val(),
                        s2: $("#jueves_s2").val(),
                        cerrado: $("#cerrado_jueves").is(":checked") ? 1 : 0
                    };
        var viernes = {
                        e1: $("#viernes_e1").val(),
                        s1: $("#viernes_s1").val(),
                        e2: $("#viernes_e2").val(),
                        s2: $("#viernes_s2").val(),
                        cerrado: $("#cerrado_viernes").is(":checked") ? 1 : 0
                    };
        var sabado = {
                        e1: $("#sabado_e1").val(),
                        s1: $("#sabado_s1").val(),
                        e2: $("#sabado_e2").val(),
                        s2: $("#sabado_s2").val(),
                        cerrado: $("#cerrado_sabado").is(":checked") ? 1 : 0
                    };
        var domingo = {
                        e1: $("#domingo_e1").val(),
                        s1: $("#domingo_s1").val(),
                        e2: $("#domingo_e2").val(),
                        s2: $("#domingo_s2").val(),
                        cerrado: $("#cerrado_domingo").is(":checked") ? 1 : 0
                    };
                
        $.ajax({
            url: BASE_URL+'configuracion/guardar_horarios_laborales',
            type: 'POST',
            dataType: 'json',
            data: {
                lunes: lunes,
                martes: martes,
                miercoles: miercoles,
                jueves: jueves,
                viernes: viernes,
                sabado: sabado,
                domingo: domingo,
                doble_horario: doble_horario,
                especifica_horario: especifica_horario
            },
            success: function(_json){
                if (_json.error){
                    $("#msg_error3").html("<div class='alert alert-danger' style='margin-bottom: 0px; padding: 6px 15px'>" + _json.error + "</div>");
                } else {
                    $("#msg_error3").html("<div class='alert alert-block alert-success' style='margin-bottom: 0px; padding: 6px 15px'>" + _json.success + "</div>");
                }
                $("#btnGuardarHorariosAtencion").attr("disabled", false);
            }
        });
    }
}


function repetirHorario(){
    var horario1 = $("#lunes_e1").val();
    var horario2 = $("#lunes_s1").val();
    var horario3 = $("#lunes_e2").val();
    var horario4 = $("#lunes_s2").val();    
    $("[name=horaFilial_1]:enabled").val(horario1);
    $("[name=horaFilial_2]:enabled").val(horario2);
    $("[name=horaFilial_3]:enabled").val(horario3);
    $("[name=horaFilial_4]:enabled").val(horario4);
    
}

function diaCerrado(dia){
    $("#" + dia + "_e1").attr("disabled", $("#cerrado_" + dia).is(":checked"));
    $("#" + dia + "_s1").attr("disabled", $("#cerrado_" + dia).is(":checked"));
    $("#" + dia + "_e2").attr("disabled", $("#cerrado_" + dia).is(":checked"));
    $("#" + dia + "_s2").attr("disabled", $("#cerrado_" + dia).is(":checked"));    
}

function verOcultarDobleHorario(){
    if ($("[name=form-field-radio]:checked").val() == "especifica"){
        $("#descripcion_horario_atencion").show();
    } else {
        $("#descripcion_horario_atencion").hide();
    }
}

function verOcultarDosHorarios(){
    if ($("[name=indicar_dos_horarios]").is(":checked")){
        $("[name=horario_cortado]").show();
    } else {
        $("[name=horario_cortado]").hide();
    }
}

function saveAdvancedSetting(id_filial){
    $("#btnSaveAdvanced").attr("disabled", true);
    $("#msg_error").html("");
    $("#msg_error2").html("");
    var scripts = $("[name=impresoras_scripts]");
    var datos = new Array();
    for (var i = 0; i < scripts.length; i++){
        var id = scripts[i].id;
        var script_id = $("[name=" + id + "]").val();
        var printer_id = scripts[i].value;
        var name = $("#name_" + printer_id).val();
        var display_name = $("#displayName_" + printer_id).val();
        var proxy = $("#proxy_" + printer_id).val();
        var metodo = $("#impresoras_scripts_forma_impresion_" + script_id).val();
        datos.push ({script: script_id, 
            printer_id: printer_id,
            name: name,
            display_name: display_name,
            proxy: proxy,
            id_filial: id_filial,
            metodo: metodo
        });
    }
    $.ajax({
        url: BASE_URL + 'configuracion/saveAdvancedSetting',
        type: 'POST',
        dataType: 'json',
        data: {
            settings: datos
        },
        success: function(_json){
            if (_json.error){
                $("#msg_error2").html("<div class='alert alert-danger' style='margin-bottom: 0px; padding: 6px 15px'>" + _json.error + "</div>");
            } else {
                $("#msg_error2").html("<div class='alert alert-block alert-success' style='margin-bottom: 0px; padding: 6px 15px'>" + _json.successMSG + "</div>");
            }
            $("#btnSaveAdvanced").attr("disabled", false);
        }
    });
}

function selectPrinterScriptChange(id_filial){
    var printers = $("[name=tabla_printers]");                
    for (var i = 0; i < printers.length; i++){
        var id = printers[i].id;
        $("[name=btn_" + id + "]").html("<button class='btn btn-minier btn-purple' onclick='setDefaultPrinter(" + id_filial + ", \"" + id  + "\")'>" + lang.Utilizar_en_todas_las_impresiones + "</button>");
        $("#img_printer_" + id).removeClass("green");
    }
}

function setDefaultPrinter(id_filial, printer_id){
    $("table#" + printer_id + " tbody tr td#boton_accion button").attr("disabled", true);
    $("#msg_error").html("");
    $("#msg_error2").html("");
    var displayName = $("#displayName_" + printer_id).val();
    var proxy = $("#proxy_" + printer_id).val();
    var name = $("#name_" + printer_id).val();
    $.ajax({
        url: BASE_URL + 'configuracion/addDefaultPrinter',
        type: 'POST',
        dataType: 'json',
        data: {
            id_filial: id_filial,
            printer_id: printer_id,
            displayName: displayName,
            name: name,
            proxy: proxy
        },
        success: function(_json){
            if (_json.error){
                $("table#" + printer_id + " tbody tr td#boton_accion button").html("Error");
            } else {
                var printers = $("[name=tabla_printers]");                
                for (var i = 0; i < printers.length; i++){
                    var id = printers[i].id;
                    $("[name=btn_" + id + "]").html("<button class='btn btn-minier btn-purple' onclick='setDefaultPrinter(" + id_filial + ", \"" + id  + "\")'>" + lang.Utilizar_en_todas_las_impresiones + "</button>");
                    $("#img_printer_" + id).removeClass("green");
                }
                $("[name=btn_" + printer_id + "]").html("<span style='color: #999; size: 9px'>" + lang.Esta_impresora_se_utiliza_en_todas_las_impresiones + "</span>");
                $("#img_printer_" + printer_id).addClass("green");
                $("[name=impresoras_scripts]").val(printer_id);
                $("[name=impresoras_scripts] option[id='" + printer_id + "']").attr("selected", true);
                $("[name=impresoras_scripts]").trigger("chosen:updated");
            }
        }
    });
}

function scriptsFormasChange(idScript){
    var forma = $("#impresoras_scripts_forma_impresion_" + idScript).val();
    $("#impresora_script_" + idScript).attr("disabled", forma == "no_imprimir");
    $("#impresora_script_" + idScript).trigger("chosen:updated");
}

function getPrintersList(id_filial){
    $("#div_cloud_print_devices").show();
    //$("#cloud_printer_list").html("<center><img src='assents/img/loader.gif'></center>");    
    $("[name=impresoras_scripts]").empty();
    $("[name=impresoras_scripts]").append("<option value='-2'>(recuperando impresoras...)</option>");
    $("[name=impresoras_scripts]").attr("disabled", true);
    $("[name=impresoras_scripts]").trigger("chosen:updated");
    $.ajax({
        url: BASE_URL+'configuracion/getPrintersList',
        type: 'POST',
        dataType: 'json',
        data: {
            id_filial: id_filial
        },
        success: function(_json){
            if (_json.error){
                $("#cloud_printer_list").html("<div class='alert alert-danger' style='margin-bottom: 0px; padding: 6px 15px'>" + _json.error + "</div>");
                $("[name=impresoras_scripts]").attr("disabled", false);
                $("#msg_error").html("");
                $("#msg_error2").html("");
                unsetPrinters();
            } else {
                if (_json.length == 0){
                    $("#cloud_printer_list").html("<div class='alert alert-warning' style='margin-bottom: 0px;' padding: 6px 15px>" + lang.No_hay_impresoras_registradas_en_cloud_print + "</div>");
                    $("[name=impresoras_scripts]").attr("disabled", false);
                    $("#msg_error").html("");
                    $("#msg_error2").html("");
                    unsetPrinters();
                } else {
                    $("#cloud_printer_list").html("");
                    var id_default = -1;
                    $("[name=impresoras_scripts]").empty();
                    $("[name=impresoras_scripts]").append("<option value='-1'>" + lang.Imprimir_por_el_navegador + "</option>");
                    $.each(_json, function(index, value){
                        
                        var color = value.default && value.default == 1 ? " green" : "";
                        var title = value.default && value.default == 1 ? "title='" + lang.Esta_es_la_impresora_por_default + "'" : "";
                        if (value.default && value.default == 1){
                            id_default = value.id;
                        }
                        var html1 = '';
                        html1 += "<table style='margin-bottom: 10px;' id='" + value.id + "' name='tabla_printers'>";
                        html1 += "<tr>";
                        html1 += "<td style='padding-right: 20px;'" + title + "><i id='img_printer_" + value.id + "' class='icon-print bigger-230" + color + "'></i><td>";
                        html1 += "<td>";
                        html1 += "<table style='width: 310px;'>";
                        html1 += "<tr>";
                        html1 += "<td>" + value.displayName + "</td>";
                        html1 += "</tr>";
                        html1 += "<tr>";
                        html1 += "<td style='color: #999'>" + value.id + "</td>";
                        html1 += "</tr>";
                        html1 += "</table>";
                        html1 += "</td>";
                        html1 += "<td style='width: 160px;'>";                        
                        if (value.connectionStatus == "ONLINE"){
                            html1 += "<span class='label label-sm label-success arrowed'>ONLINE</span>";
                        } else if (value.connectionStatus == "OFFLINE"){
                            html1 += "<span class='label label-sm label-danger arrowed-in'>OFFLINE</span>";
                        } else {
                            html1 += "<span class='label label-sm label-info arrowed-in-right arrowed'>" + value.connectionStatus + "</span>";
                        }                        
                        html1 += "</td>";
                        html1 += "<td id='boton_accion' name='btn_" + value.id + "'>";
                        if (!value.default || value.default != 1){
                            html1 += "<button class='btn btn-minier btn-purple' onclick='setDefaultPrinter(" + id_filial + ", \"" + value.id  + "\")'>" + lang.Utilizar_en_todas_las_impresiones + "</button>";
                        } else {
                            html1 += "<span style='color: #999; size: 9px'>" + lang.Esta_impresora_se_utiliza_en_todas_las_impresiones + "</span>";
                        }
                        html1 += "</td>";
                        html1 += "</tr>";
                        html1 += "</table>";
                        html1 += "<input type='hidden' value='" + value.name + "' id='name_" + value.id + "'>";
                        html1 += "<input type='hidden' value='" + value.displayName + "' id='displayName_" + value.id + "'>";
                        html1 += "<input type='hidden' value='" + value.proxy + "' id='proxy_" + value.id + "'>";                        
                        $("#cloud_printer_list").append(html1);
                        $("[name=impresoras_scripts]").append("<option value='" + value.id + "' id='" + value.id + "'>" + value.displayName + "</option>");
                    });                    
                    var selected = $("[name=printers_scripts_selected]");
                    for (var i = 0; i < selected.length; i++){
                        var id_script = selected[i].value;
                        var printer_id = id_default;
                        if (printer_id == -1){
                            printer_id = $("#printers_script_" + id_script) ? $("#printers_scripts_" + id_script).val() : -1;
                        }
                        $("#impresora_script_" + id_script + " option[id='" + printer_id + "']").attr("selected", true);
                        scriptsFormasChange(id_script);
                    }
                    $("#msg_error").html("");
                    $("#msg_error2").html("");
                }
            }
        }
    });
}

function unsetPrinters(){
    $("[name=impresoras_scripts]").empty();
    $("[name=impresoras_scripts]").append("<option value=-1>" + lang.Imprimir_por_el_navegador + "</option>");
    $("[name=impresoras_scripts]").trigger("chosen:updated");
}


function guardarConfiguracionHojaMembretada(){
    $("#btnConfiguracionPapel").attr("disabled", true);
    $("#msg_error_config_hojas").html('');
    var agregar_pie = $("[name=agregar_pie_en_hojas_membretadas]").is(":checked") ? 1 : 0;
    var repetir_encabezado_informes = $("[name=agregar_encabezado_en_informes]").is(":checked") ? 1 :0;
    $.ajax({
        url: BASE_URL + 'configuracion/guardarConfiguracionPapel',
        type: 'POST',
        dataType: 'json',
        data: {
            agregar_pie: agregar_pie,
            repetir_encabezado_informes: repetir_encabezado_informes
        },
        success: function(_json){
            if (_json.codigo == 0){
                $("#msg_error_config_hojas").html("<div class='alert alert-danger' style='margin-bottom: 0px; padding: 6px 15px'>" + lang.ocurrio_error + "</div>");
            } else {
                $("#msg_error_config_hojas").html("<div class='alert alert-block alert-success' style='margin-bottom: 0px; padding: 6px 15px'>" + lang.datos_actualizados_correctamente + "</div>");
            }
            $("#btnConfiguracionPapel").attr("disabled", false);
        }
    });
}

function saveGoogleAccount(id_filial){
    $("#btnSave").attr("disabled", true);
    $("#msg_error").html("");
    $("#msg_error2").html("");
    var user_name = $("#cloud_print_user").val();
    var user_pass = $("#cloud_print_pass").val();
    var utiliza_cloud = $("#habilitar_cloud_print").is(":checked") ? 1 : 0;
    $.ajax({
        url: BASE_URL + 'configuracion/guardarGoogleAccount/',
        type: 'POST',
        dataType: 'json',
        data: {
            user_name: user_name,
            user_pass: user_pass,
            id_filial: id_filial,
            utiliza_cloud: utiliza_cloud
        },
        success: function(_json){
            if (_json.error){
                $("#msg_error").html("<div class='alert alert-danger' style='margin-bottom: 0px; padding: 6px 15px'>" + _json.error + "</div>");               
            } else {
                $("#msg_error").html("<div class='alert alert-block alert-success' style='margin-bottom: 0px; padding: 6px 15px'>" + _json.successMSG + "</div>");
                if (utiliza_cloud == 1){
                    getPrintersList(id_filial);
                } else {
                    unsetPrinters();
                    saveAdvancedSetting(id_filial);
                }
            }
            $("#btnSave").attr("disabled", false);
        }
    });
}



function init(){
    
    
    
    
    
    cargarSelectSeparadores('SeparadorDecimal',selectedDecimales);
    cargarSelectSeparadores('SeparadorMiles',selectedMiles);
    
//    $(separadores).each(function(k,item){
//    
//    var s= item.id == selectedDecimales ? 'selected' : '';
//    
//    $('select[name="separadorDecimales"]').append('<option value="'+item.id+'" '+s+'>'+item.nombre+'</option>');
//    
//});



//    $(separadores).each(function(k,item){
//    
//    var s= item.id == selectedMiles ? 'selected' : '';
//    
//    $('select[name="separadorMiles"]').append('<option value="'+item.id+'" '+s+'>'+item.nombre+'</option>');
//    
//    });
    
    

    $("#habilitar_cloud_print").click(function(){
        $("#msg_error").html("");
        $("#msg_error2").html("");
        if ($("#habilitar_cloud_print").is(":checked")){
            if ($("#cloud_printer_list").html() != ''){
                $("#div_cloud_print_devices").show();
            }
            $("#div_cloud_print").show(500);
        } else {        
            $("#div_cloud_print_devices").hide();
            $("#div_cloud_print").hide(200);
        }
    });


    $('#validation-form').validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        rules: {
                email: {
                    required: true,
                    email:true
                },
                password: {
                    required: true,
                    minlength: 5
                }
        },
        messages: {
            email: {
                    required: "Please provide a valid email.",
                    email: "Please provide a valid email."
            },
            password: {
                    required: "Please specify a password.",
                    minlength: "Please specify a secure password."
            }
        },

        invalidHandler: function (event, validator) { //display error alert on form submit   
            $('.alert-danger', $('.login-form')).show();
        },

        highlight: function (e) {
            $(e).closest('.form-group').removeClass('has-info').addClass('has-error');
        },

        success: function (e) {
            $(e).closest('.form-group').removeClass('has-error').addClass('has-info');
            $(e).remove();
        },

        errorPlacement: function (error, element) {
            if(element.is(':checkbox') || element.is(':radio')) {
                    var controls = element.closest('div[class*="col-"]');
                    if(controls.find(':checkbox,:radio').length > 1) controls.append(error);
                    else error.insertAfter(element.nextAll('.lbl:eq(0)').eq(0));
            }
            else if(element.is('.select2')) {
                    error.insertAfter(element.siblings('[class*="select2-container"]:eq(0)'));
            }
            else if(element.is('.chosen-select')) {
                    error.insertAfter(element.siblings('[class*="chosen-container"]:eq(0)'));
            }
            else error.insertAfter(element.parent());
        },

        submitHandler: function (form) {
        }
    });
    
    
    
    
    
    
    
    
    
    
    
}