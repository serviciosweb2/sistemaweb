$($("[name=adv_cajas_cerradas]")).ready(function(){
    $("[name=btnCancelar]").click(function(){
        cerrarFancy();
    });
    $("[name=btnAceptar]").click(function(){
        showFormAbrirCaja();
    });
});

function cerrarFancy(){
    $.fancybox.close();
}
function showFrmCompras(element){
    
   // alert($(element).attr('data-idCompra'));
    
    
     $.ajax({
            url:BASE_URL+"compras/frm_compras",
            data:'codigo='+$(element).attr('data-idCompra')+'&continuar='+true,
            type:'POST',
            cache:false,
            success:function(respuesta){
                $.fancybox.open(respuesta, {

                    scrolling: 'auto',
                    padding: '0',
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
    
    
}

function showFormAbrirCaja(){
    var ejecutar_script = $("[name=ejecutar_script]").val();
    $.ajax({
        url: BASE_URL + 'caja/frm_abrir_caja',
        type: 'POST',
        data: {
            ejecutar_script: ejecutar_script
        },
        success: function(_html){
            $.fancybox.open(_html, {
                width: 'auto',
                height: 'auto',
                scrolling: true,
                padding: 0,
                openEffect: 'none',
                closeEffect: 'none',
                helpers: {
                    overlay: null
                }
            });
        }
    });
}