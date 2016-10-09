
$(".fancybox-wrap").ready(function() {
    
      $('.chosen-select').chosen({
          width:"100%"
    });

    
    $("#btn-baja").click(function(){
                $.ajax({
            url: BASE_URL + 'matriculas/cambiarEstado',
            data: $("#frm-baja").serialize(),
            cache: false,
            type: 'POST',
            dataType: 'json',
            success: function(respuesta) {
                if(respuesta.codigo === 1){
                    $.fancybox.close();                
                    $.gritter.add({
                         title: lang.BIEN,
                        text: lang.validacion_ok ,
                         sticky: false,
                         time: '3000',
                         class_name: 'gritter-success'
                    });
                    var param = new Array();
                    param.push(respuesta.custom);
                    printers_jobs(2, param); 
                }
            }
        });
        return false;
        
    });
    
});