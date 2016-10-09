$('document').ready(function (){
    $('.elegir').click(function(e){
        var cod = $(this).attr('data-filial');
        $.ajax({
            url: BASE_URL + 'usuarios/setFilial',
            data: 'filial=' + cod  ,
            type: 'POST',
            cache: false,
            //dataType: 'json',
            success: function (respuesta) {
                //alert(respuesta);
                window.location = BASE_URL +"dashboard";
            }
        });
    });
});