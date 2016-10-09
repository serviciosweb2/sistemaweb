
$(".fancybox-wrap").ready(function() {
    
      $('.chosen-select').chosen({
          width:"100%"
    });

    
    $("#btn-baja").click(function(){
                $.ajax({
            url: BASE_URL + 'matriculas/bajaMatriculasPeriodos',
            data: $("#frm-baja").serialize(),
            cache: false,
            type: 'POST',
            dataType: 'json',
            success: function(respuesta) {
               
                
                
                switch (respuesta.codigo){
                    case 1:
                                         $.fancybox.close();                
                    $.gritter.add({
                         title: lang.BIEN,
                        text: lang.validacion_ok ,
                         sticky: false,
                         time: '3000',
                         class_name: 'gritter-success'
                    });
                    console.log('RESPUESTA',respuesta);
                    var param = new Array();
                    param.push(respuesta.custom.codigo_historico);
                    printers_jobs(2, param); 
                        break;
                            case 0:
                $.gritter.add({
                                                title: lang.ERROR,
                                                text: respuesta.respuesta ,
                                                sticky: false,
                                                time: '3000',
                                                class_name: 'gritter-error'
                                            });
           
                        break;
                    case "2":
                          $.fancybox.close();  
                      $.ajax({
                    url: BASE_URL + 'ctacte/frm_refinanciar',
                    data: respuesta.custom,
                    type: 'POST',
                    cache: false,
                    success: function(respuesta) {
                        $.fancybox.open(respuesta, {
                            scrolling: true,
                            width: '60%',
                            autoSize: false,
                            autoResize: true,
                            padding: 0,
                            openEffect: 'none',
                            closeEffect: 'none',
                            helpers: {
                                overlay: null
                            }, 
                            beforeClose: function() {
                                oTable.fnDraw();
                            }
                        });
                      
                    }
                });
                        break;
                }
                
                
          
                
            }
        });
        return false;
        
    });
    
});