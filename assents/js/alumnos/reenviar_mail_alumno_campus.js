oTableDetalleReenviarEmail = '';
var langFRM = langFrm;
$('.fancybox-wrap').ready(function(){
    oTableDetalleReenviarEmail = $('#detalle_mail_enviados').DataTable({
        "bLengthChange":false,
        "searching": false
    });
    initFRM();
});

function initFRM(){
    $('#reenviar_mail').on('click',function(){
        var cod_alumno = $('#cod_alumno').val();
        $.ajax({
            url: BASE_URL + 'alumnos/reenviar_email_campus',
            data: 'cod_alumno=' + cod_alumno,
            type: 'POST',
            dataType:"JSON",
            cache:false,
            success: function(respuesta) {
               if(respuesta.codigo == 1){

                   $('#password').html('<center></br><h3>' + langFRM.password + ': <b>' + respuesta.custom + '</h3></b></br></br> </center>');
                   $('#reenviar_mail').hide();
                   console.log(respuesta);
                   gritter(langFRM.bien_se_envio_mail_cuenta,true);

               }else {
                   $.gritter.add({
                       title: 'Error!',
                       text: respuesta.custom,
                       sticky: false,
                       time: '3000',
                       class_name: 'gritter-error'
                   });

               }
            }
        });
    });


    $('#regenerar_password').on('click',function(){
        var cod_alumno = $('#cod_alumno').val();
        $.ajax({
            url: BASE_URL + 'alumnos/regenerar_password',
            data: 'cod_alumno=' + cod_alumno,
            type: 'POST',
            dataType:"JSON",
            cache:false,
            success: function(respuesta) {
                if(respuesta.codigo == 1){

                    $('#password').html('<center></br><h3>' + langFRM.password + ': <b>' + respuesta.custom + '</h3></b></br></br> </center>');
                    $('#reenviar_mail').hide();
                    console.log(respuesta);
                    //gritter(langFRM.bien_se_envio_mail_cuenta,true);

                }
            }
        });
    });


}