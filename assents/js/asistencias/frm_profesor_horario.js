var langFRM = langFrm;
$('.fancybox-wrap').ready(function(){

   initFRM()
  
}); 

  function initFRM(){
      $('#horario_profesores').dataTable();
       $('.fancybox-wrap select').chosen({
        width:'100%',
        allow_single_deselect: true
    });

    $('.profesores').on('change',function(){
        var cod_horario = $(this).closest('tr').find('.horario').attr('data-codigo')
        var cod_profesor = $(this).val();
        var accion = $(this).attr('data-action');
        var comision = $("select[name='comisiones']").val();
        var materia = $("select[name='clases']").val();
        data_post = {
            "cod_horario": cod_horario,
            "cod_profesor": cod_profesor,
            "accion":accion
        }
        console.log("Guardo profesor");
        console.log("Comision:"+comision);
        console.log("Materia:"+materia);
        console.log("Dia:"+parar);
        $.ajax({
            url: BASE_URL + 'asistencias/guardarProfesorHorario',
            data: data_post,
            type: 'POST',
            dataType:"JSON",
            cache: false,
            success: function(respuesta) {
                if(respuesta.codigo == 1){
                    gritter(langFRM.validacion_ok,true);
                    $('.fancybox-close').click();
                    //generarSelectHorarios(comision, materia, parar);
                }
            }
        });
    });


    }