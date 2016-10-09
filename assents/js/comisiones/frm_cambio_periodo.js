var chk_cod_comision;

$(".fancybox-wrap").ready(function(){
    chk_cod_comision = $("[name=cod_comision]");
    $(".select_chosen").chosen();
    $("#tableComisiones").dataTable({
        "bFilter": false,
        "bLengthChange": false,
        "iDisplayLength": 5,
        "aoColumns": [{"bSortable": false},
            null, 
            null,
            {"bSortable": false}]
    });
    $("[name=fecha_cambio_comision]").datepicker({
        changeMonth: false,
        changeYear: false,
        dateFormat: 'dd/mm/yy',
        minDate: "today"
    });
});

function cod_comsion_checked(element){
    var tr = $(element).closest("tr");
    var selectDestino = $(tr).find("[name=cod_comision_destino]");
    if ($(element).is(":checked")){        
        var optionDestino = $(selectDestino).find("option");
        if (optionDestino.length == 0){ // aun no se cargo por ajax
            $(selectDestino).append("<option value=-1>(" + langFrm_cambio_periodo.recuperando + "...)</option>");
            $(selectDestino).prop("disabled", true);
            $(selectDestino).trigger('chosen:updated');
            var cod_comision = $(element).val();
            $.ajax({
                url: BASE_URL + 'comisiones/getComisionesPeriodo',
                type: 'POST',
                dataType: 'json',
                data: {
                    cod_comision: cod_comision
                },
                success: function(_json){
                    if (_json.error){
                        gritter(_json.error);
                    } else {
                        $(selectDestino).find("option").remove();
                        if (_json.data.comisiones.length > 0){
                            var agrega_comisiones = false;
                            $.each(_json.data.comisiones, function(key, value){
                                if (value.codigo != cod_comision){
                                    $(selectDestino).append('<option value="' + value.codigo + '">' + value.nombre + '</option>');
                                    agrega_comisiones = true;
                                }
                            });
                            if (agrega_comisiones){
                                $(selectDestino).prop("disabled", false);
                            } else {
                                $(selectDestino).append('<option value="-1">(' + langFrm_cambio_periodo.sin_registros + ')</option>');
                                $(selectDestino).prop("disabled", true);
                            }                            
                        } else {
                            $(selectDestino).append('<option value="-1">(' + langFrm_cambio_periodo.sin_registros + ')</option>');
                            $(selectDestino).prop("disabled", true);
                        }                        
                        $(selectDestino).trigger("chosen:updated");
                    }
                }
            });
            
        } else {
            $(selectDestino).prop("disabled", false);
            $(selectDestino).trigger('chosen:updated');
        }
    } else {
        $(selectDestino).prop("disabled", true);
        $(selectDestino).trigger('chosen:updated');
    }    
}

function guardarPasajeComision(){
    var comisiones = new Array();
    var sin_comision = false;
    var fecha_desde = $("[name=fecha_cambio_comision]").val();
    $.each(chk_cod_comision, function(key, element){
        if ($(element).is(":checked")){
            var origen = $(element).val();
            var destino = $(element).closest("tr").find("[name=cod_comision_destino]").val();
            if (destino != -1){
                comisiones.push({
                    origen: origen,
                    destino: destino
                });
            } else {
                sin_comision = true;
            }
        }
    });
    if (sin_comision){
            gritter(langFrm_cambio_periodo.algunas_comisiones_seleccionadas_no_poseen_comision_de_destino);
    } else if (comisiones.length == 0){
        gritter(langFrm_cambio_periodo.debe_seleccionar_alguna_comision_para_realizar_el_pasaje);
    } else {
        $.ajax({
            url: BASE_URL + 'comisiones/pasar_comision',
            type: 'POST',
            dataType: 'json',
            data: {
                comisiones: comisiones,
                fecha_desde: fecha_desde
            },
            success: function(_json){
            	console.log(_json);
                if (_json.error){
                    var msgError = '';
                    $.each(_json.error, function(key, value){
                        msgError += value + "<br>";
                    });
                    gritter(msgError);
                } else {
                    gritter(langFrm_cambio_periodo.validacion_ok, true);
                    $.fancybox.close();
                }
            }
        });
    }
}

//Ticket 4581 -mmori- cambio de comision
function guardarCambioComision()
{
    var fecha_desde = $("[name=fecha_cambio_comision]").val();
    var comision_destino = $('#cod_comision_destino').val();
    var comision_origen = $('#comision_origen').val();
    var comisiones = new Array();
    
    comisiones.push({origen: comision_origen, destino: comision_destino});
    
    if (comision_origen.length === 0)
    {
        gritter(langFrm_cambio_periodo.debe_seleccionar_alguna_comision_para_realizar_el_pasaje);
    }
    else if(comision_destino.length === 0)
    {
        gritter(langFrm_cambio_periodo.debe_seleccionar_alguna_comision_para_realizar_el_pasaje);
    }
    else
    {
        $.ajax({
            url: BASE_URL + 'comisiones/guardar_cambios_comision',
            type: 'POST',
            dataType: 'json',
            data: {
                comisiones: comisiones,
                fecha_desde: fecha_desde
            },
            success: function(_json){
                if (_json.error){
                    var msgError = '';
                    $.each(_json.error, function(key, value){
                        msgError += value + "<br>";
                    });
                    gritter(msgError);
                } else {
                    gritter(langFrm_cambio_periodo.validacion_ok, true);
                    $.fancybox.close();
                }
            }
        });
    }    
}

function mostrar_alumnos_cursando(cod_comision){
    $("#div_listado_comisiones").hide();
    $("[name=btn_guardar]").hide();
    $("#div_listado_alumnos_cursando").html("<center>" + langFrm_cambio_periodo.recuperando + "</center>");
    $("#div_listado_alumnos_cursando").show();
    $.ajax({
        url: BASE_URL + 'comisiones/get_alumnos_cursando',
        type: 'POST',
        dataType: 'json',
        data: {
            cod_comision: cod_comision
        },
        success: function(_json){
            var _html = '';
            _html += '<table id="tableAlumnosCursando" cellpadding="0" width="100%" cellspacing="0" border="0" class="table table-striped table-bordered dataTable no-footer" role="grid">';
            _html += '<thead>';
            _html += '<tr>';
            _html += '<th>';
            _html += langFrm_cambio_periodo.alumnos_con_estado_academico_cursando;
            _html += '</th>';
            _html += '</tr>';
            _html += '</thead>';
            $.each(_json.alumnos, function(key, value){
                _html += '<tr>';
                _html += '<td>';
                _html += value.nombre_apellido;
                _html += '</td>';
                _html += '</tr>';
            });
            $("#div_listado_alumnos_cursando").html(_html);
            $("#tableAlumnosCursando").dataTable({
                bFilter: false,
                bLengthChange: false,
                iDisplayLength: 5
            });
            
            console.log(_html);
            console.log(_json);
            $("[name=btn_volver]").show();
        }
    });    
}

function volver(){
    $("[name=btn_volver]").hide();
    $("#div_listado_alumnos_cursando").hide();
    $("#div_listado_comisiones").show();
    $("[name=btn_guardar]").show();
}