function guardar(cod,tipo,estado){
    var url = '';
    if (tipo == 'activarplan'){
        url = BASE_URL+'comisiones/guardarPlanes';
    } else {
       url = BASE_URL+'comisiones/mostrarPlanWeb';
    }
    $.ajax({
        url:url,
        data:{
            cod_comision: codigo,
            cod_plan_pago: cod,
            accion: estado
        },
        dataType:'JSON',
        type:'POST',
        cache:false,
        async:false,
        success:function(respuesta){
            if(respuesta.codigo == 1){
                $.gritter.add({
                    title: langFRM.BIEN,
                    text: langFRM.validacion_ok ,
                    sticky: false,
                    time: '3000',
                    class_name: 'gritter-success'
                });
            } else {
                $('input[name="mostrar_web"]').prop('checked',false);
                $.gritter.add({
                    text: respuesta.msgError ,
                    sticky: false,
                    time: '3000',
                    class_name: 'gritter-error'
                });
            }
        }
    });
    return false;
}

function activarPlan(element){
    var cod_plan = $(element).val();
    if ($(element).is(':checked')){
        var estado = 'checked';
    } else {
        var estado = 'deschecked';
    }
    var cod_comision = cod_plan;
    guardar(cod_plan,'activarplan',estado);
}

function validarCheckearOtro(element){
    var input = $('.habilitar_web').not(element);
    var checkear = true;
    $(input).each(function(key,valor){
        if($(valor).is(':checked')){
             checkear = false;
        }
    });
    return checkear;
}

function validarContraColumnaActivo(element){
    if($(element).closest('tr').find('.activar_plan').not(element).is(':checked')){
        return true;
    } else {
        return false;
    }
}

function mostrarWeb(element,event){
    var cod_plan = $(element).val();
    if (validarCheckearOtro(element)){
        if (validarContraColumnaActivo(element)){
            var tr = $(element).closest("tr");
            var estado = '';
            if ($(element).is(':checked')){
                $(tr).find("[name=mostrar_financiacion_web]").prop("disabled", false);
                $(tr).find(".habilitar_dias").prop('disabled', false);
                estado = 'checked';
            } else {
                $(tr).find("[name=mostrar_financiacion_web]").prop("disabled", true);
                $(tr).find(".habilitar_dias").prop('disabled', true);
                estado = 'deschecked';
            }
            guardar(cod_plan,'mostrarweb',estado);
        } else {
            $.gritter.add({
                text: langFRM.no_activar_mostrar_web,
                sticky: false,
                time: '3000',
                class_name: 'gritter-error'
            });
            event.preventDefault();
        }
    } else {
        $.gritter.add({
            text: langFRM.no_habilitar_mostrar_web_2_comision,
            sticky: false,
            time: '3000',
            class_name: 'gritter-error'
        });
        event.preventDefault();
    }
    return false;
}

function activarHabilitar_Dias(element,event){
    var elemeto_checkeado =  $(element).closest('tr').find('.habilitar_web').is(':checked');
    if (elemeto_checkeado){

    } else {
       gritter(langFRM.debe_activar_mostrar_web);
       event.preventDefault();
    }
}

var langFRM = langFrm;

function initFRM(){
    var oTablePlanes = $('#planes_comision').DataTable({
        "iDisplayLength": 6,
        "searching": true,
        "bSearchable":true,
        "lengthChange": false,
        "aoColumnDefs": [
            {"bSearchable": true, "aTargets": [ 0 ]},
            {"bSearchable": false, "aTargets": [ 1, 2, 3 ]},
        ]
    });
    
    oTablePlanes.$(".mostrar_tooltip").tooltip({
        show: null,
        position: {
            my: "left top",
            at: "left bottom"
        },
        open: function( event, ui ) {
            ui.tooltip.animate({ top: ui.tooltip.position().top + 10 }, "fast" );
        }
    });
    
    oTablePlanes.$('.input-mini').ace_spinner({
        value:dias_prorroga,
        min:1,
        max:200,
        step:1,
        btn_up_class:'btn-info',
        btn_down_class:'btn-info'
    }).on('change', function(){
        var cod_comision =  $('input[name="cod_comision"]').val();
        var valor = $(this).val();
        var data = {
            'cod_comision':cod_comision,
            'valor': valor
        };
        guardarDiasProrroga(data);
    });
    
    oTablePlanes.$('select ').chosen({
        width:'100%'
    });
    $('#planes_comision').wrap('<div class="table-responsive"></div>');
    $('select ').on('chosen:showing_dropdown',function(evt,param){
        $.fancybox.update();
    });
    $('.activar_plan').on('click',function(){
        if(fecha_inicio_comision != 'no_tiene_horarios'){
            $(this).closest('tr').find('.habilitar_web').prop('disabled',!$(this).is(':checked'));
        }
    });

    $('.habilitar_dias').on('click',function(){
        if ($(this).is(':checked')){
            $(this).closest('tr').find('.spinner_dias').removeClass('hide');
        } else {
            $(this).closest('tr').find('.spinner_dias').addClass('hide');

            var cod_comision =  $('input[name="cod_comision"]').val();
            var valor = '';
            var data = {
                'cod_comision':cod_comision,
                'valor': valor
            };
            guardarDiasProrroga(data);
        }
    });
    
    $('.input-mini').on('keypress',function(){
        var cod_comision =  $('input[name="cod_comision"]').val();
        var valor = $(this).val();
        var data = {
            'cod_comision':cod_comision,
            'valor': valor
        };
        guardarDiasProrroga(data);
    });
    $.fancybox.update();
    if (dias_prorroga == '' ){
        $( ".spinner_dias" ).addClass( "hide" );
        $('.habilitar_dias').attr('checked', false);
    }
}

/*
function initCampoBusqueda() {
    $("#planes_de_pago_search_input").bind("keyup", function () {
        if ($("#planes_de_pago_search_input").val() != "") {
            search_value = $("#planes_de_pago_search_input").val()
            console.log(search_value);
            
            tableRows = $('#planes_comision > tbody > tr');
            
            
            console.log(tableRows);
            
            for (currentTableRow in tableRows) {
                console.log(currentTableRow);
            }
            if (node.indexOf(search_value) > -1) {
                
            }
        }
        else
        {
            alert("queda vacio!!!");
        }
    });
}*/

function initCampoBusqueda() {
    
}

$('.fancybox-wrap').ready(function(){
    initFRM();
    initCampoBusqueda();
});

var timerGuardar = '';
function guardarDiasProrroga(dataPost){
    clearTimeout(timerGuardar);
    timerGuardar = setTimeout(function(){
        $.ajax({
            url: BASE_URL+'comisiones/guardarPeriodoProrroga',
            data: dataPost,
            dataType:'JSON',
            type:'POST',
            cache:false,
            async:false,
            success:function(respuesta){
                if(respuesta.codigo == 1){
                    $.gritter.add({
                        title: langFRM.BIEN,
                        text: langFRM.PLANES_COMISION_GUARDADA ,
                        sticky: false,
                        time: '3000',
                        class_name: 'gritter-success'
                    });
                } else {
                    $.gritter.add({
                        text: respuesta.msgerror ,
                        sticky: false,
                        time: '3000',
                        class_name: 'gritter-error'
                    });
                }
            }
        });
    },1500);
}

function mostrar_ocultar_financiacion_web(element){
    var cod_plan = $(element).val();
    var activo = $(element).is(":checked") ? 1 : 0;
    var cod_comision = $("[name=cod_comision]").val();
    $.ajax({
        url: BASE_URL + "comisiones/setMostrarFinanciacionWeb",
        type: 'POST',
        dataType: 'json',
        data: {
            cod_plan: cod_plan,
            cod_comision: cod_comision,
            activo: activo
        },
        success: function(_json){
            if (_json.error){
                gritter(_json.msgError, false);
            } else {
                gritter(langFRM.validacion_ok, true, " ");
            }
        }
    });
}