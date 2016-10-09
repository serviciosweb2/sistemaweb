function replicarATodas(element){
    var comision = $(element).closest("td").find("#comision_destino").val();
    if (comision == -1){
        comision = $(element).closest("td").find("#comsion_destino_codigo").val();
    }
    var table = $(element).closest("tr").closest("table");
    var selects = $(table).find("[name='comision_destino[]']");
    $(selects).selectedIndex = -1;
    $(selects).find("option[value='" + comision + "']").prop("selected", true);
    $(selects).trigger("chosen:updated");
}

function imprimirEstadoAcademico(cod_matricula_periodo){
    var cod_alumno = $("input[name='cod_alumno']").val();
    var cod_plan_academico = $("input[name='cod_plan_academico']").val();
    var param = new Array();
    if (cod_matricula_periodo == null){
        param[0] = JSON.stringify({'cod_alumno':cod_alumno,'cod_plan_academico':cod_plan_academico});
    } else {
        param[0] = JSON.stringify({'cod_matricula_periodo':cod_matricula_periodo});
    }
    printers_jobs(4,param);
    cerrarVentana();
}

function cerrarVentana(){
    $.fancybox.close(true);
}

$('.fancybox-wrap').ready(function(){
    var clavesFRM=Array("validacion_ok");
    var langFRM='';
    $.ajax({
        url:BASE_URL+'entorno/getLang',
        data:"claves=" + JSON.stringify(clavesFRM),
        type:"POST",
        dataType:"JSON",
        async:false,
        cache:false,
        success:function(respuesta){
            langFRM=respuesta;
            initFRM();
        }
    });
    
    function initFRM(){
        $('.fancybox-wrap select').chosen({
            width:'100%',
            allow_single_deselect: true
        });
        $(".btn-cambio-estado").click(function(){
            $.ajax({
                url: BASE_URL + 'matriculas/cambiarEstadoMateria',
                data:  $("#cambiarEstado").serialize(),
                type: 'POST',
                cache: false,
                dataType: 'json',
                success: function(respuesta) {
                    if(respuesta.codigo ==="0"){
                        $(".alert").removeClass("hide");
                        $(".alert").html(respuesta.respuesta);
                    } else {
                        var str = $("#cambiarEstado").serialize();
                        var res = str.split("&");
                        var id = res[0].split("=");
                        var estado = res[1].split("=");
                        
                        switch (estado[1])
                        {
                            case "no_curso":
                                $("#"+id[1]).html("No Cursa");
                                break;
                            case "homologado":
                                $("#"+id[1]).html("Homologada");
                                break;
                            case "libre":
                                $("#"+id[1]).html("No Regular");
                                break;
                            default:
                                $("#"+id[1]).html(estado[1]);
                                break;
                        }
                        
                        $(".alert").addClass("hide");
                        $('#stack1').modal('hide');
                        
                        
                        //$.fancybox.update();
                    }
                }
            });
        });
        $(".cambiarestado").click(function(){
            var codigoEstadoAcademico = $(this).attr("data");
            $.ajax({
                url: BASE_URL + 'matriculas/frm_cambioEstadoMateria',
                data: 'codigo=' + codigoEstadoAcademico,
                type: 'POST',
                cache: false,
                success: function(respuesta){
                    $(".cambio-body").html(respuesta);
                    $('#stack1').modal('show');
                }
            });
        });
        $("#btn-guardar").click(function(){
            $.ajax({
                url: BASE_URL + 'matriculas/guardarCursado',
                data: $("#cursado").serialize(),
                cache: false,
                type: 'POST',
                dataType: 'json',
                success: function(respuesta) {
                    if(respuesta.codigo === 1){
                        gritter(langFRM.validacion_ok, true);
                        $.fancybox.close();
                    }
					else {
                        respuesta = JSON.parse(respuesta);
                        gritter(respuesta.error, false);
                    }
                }
            });
            return false;
        });
        
        
        $('select[name="comision_destino[]"]').on('change',function(){
            $.fancybox.update();
        });
    
        $('#myTab a').click(function(e){
           $.fancybox.update();
        });
    }
});