
function abrirChat(){
   console.log(codigo_filial);
   window.open("http://iga-la.net/panelcontrol/soporte/chat/client.php?locale="+idioma+"&style=iga&url=http://iga-la.net/panelcontrol/soporte/chat/conectar.php&referrer=&filial="+ codigo_filial, "Popup", "width=800,height=500,location=0");
}

function nuevaConsulta() { // hacer la llamada por ajax, recuperar la respuesta y copiarla al fancy
    $.ajax({
        url: BASE_URL + 'consultasweb/nueva_consulta',
        type: 'GET',
        success: function(_html) {
            $.fancybox.open(_html, {
                padding: 0,
                width: 650,
                height: 380,
                ajax: {
                    dataType: 'html',
                    headers: {'X-fancyBox': true}
                }
            });
        }
    });


}
