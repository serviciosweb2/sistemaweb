$(document).ready(function(){
    $('#categorias').change(function(){
       eval('var sub = subcategoria'+$(this).val());
       html = '';
       $.each(sub,function( index, value ) {
           html += '<option> '+value+ '</option>';
        });
        $('#subcategorias').html(html);
    });
   
    $('#enviar').click(function(){
        
        var datos = $('#nuevo_ticket').serialize();
        var cat = $('#categorias option:selected').text();
        datos += '&categoria='+cat;
        $.ajax({
            url: BASE_URL + 'tickets/agregar_ticket',
            type: 'POST',
            data: datos,
            cache: false,
            dataType: 'json',
            success: function (respuesta){                
                var codigo = String(respuesta.codigo);
                
                switch (codigo) {
                    case '0':
                        $.gritter.add({
                            title: langFrm.ERROR,
                            text: respuesta.msgerror,
                            sticky: false,
                            time: '3000',
                            class_name:'gritter-error'
                        });
                        break;
                    case '1':
                        $.gritter.add({
                            title: langFrm.BIEN,
                            text: langFrm.validacion_ok,
                            sticky: false,
                            time: '3000',
                            class_name:'gritter-success'
                        }); 
                        $('.fancybox-close').click();
                        listar();
                        break;
                }
            }
        });
        return false;
    });
});