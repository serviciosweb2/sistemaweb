var oTableProveedores = '';
var claves = Array("INHABILITADO", "HABILITADO", "HABILITAR", "INHABILITAR");
var listadoproveedores = '';
var menucontextual = new Array();

$('.fancybox-wrap').ready(function(){
     $.ajax({
        url: BASE_URL + 'entorno/getLang',
        data: "claves=" + JSON.stringify(claves),
        dataType: 'JSON',
        type: 'POST',
        cache: false,
        async: false,
        success: function(respuesta) {
            listadoproveedores = respuesta;
            $.ajax({
                url: BASE_URL + 'entorno/getMenuJSON',
                data: 'seccion=compras',
                dataType: 'JSON',
                type: 'POST',
                cache: false,
                async: false,
                success: function(respuesta) {
                    $.each(respuesta.contextual, function(key, value){
                        if (key == 1 || key == 2){ // solo buscamos los items del menu que nos interesa
                            menucontextual.push(value);
                        }
                    });
                    initialize();
                }
            });
        }
    });
});


function initialize(){
    
    oTableProveedores = $("#tableProveedores").dataTable({
        "bServerSide": true,
        "aaSorting": [[ 0, "desc" ]],
        "sAjaxSource": BASE_URL + "proveedores/listar",
        
        "sServerMethod": "POST",
        "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                var estado = aData[6];
                var btn = "";
                switch (estado){
                    case "1":
                     btn = "<span class='label label-danger arrowed'>" + listadoproveedores.INHABILITADO + "</span>";
                        break;
                    case "0":
                     btn = "<span class='label label-success arrowed'>" + listadoproveedores.HABILITADO + "</span>";
                        break;
                }
                $('td:last', nRow).html(btn);
            }
    });
    
    
    
    $("[name=nuevo_proveedor]").on("click", function(){
        frmAgregarModificarProveedor(-1);
    });
    $.fancybox.update();
    $('.modal-content').on('mousedown', '#tableProveedores tbody tr', function(e){
        var sData = oTableProveedores.fnGetData(this);
        if (e.button === 2){
            generalContextMenu(menucontextual, e);
            codigoSeleccionado = sData[0];
            var estado = sData[6];
            if (estado == "0"){
                $("a[accion=cambiar_estado_proveedor]").text(listadoproveedores.INHABILITAR);
                estadoCambiar = 1;
            } else {
                $("a[accion=cambiar_estado_proveedor]").text(listadoproveedores.HABILITAR);
                estadoCambiar = 0;
            }
            return false;
        }
    });
}

function frmAgregarModificarProveedor(cod_proveedor){
    $.ajax({
        url: BASE_URL + 'proveedores/frm_proveedores',
        type: 'POST',
        data:{
            cod_proveedor: cod_proveedor
        },
        success: function(_hmtl){
            var fancyproveedores = $.fancybox.open(_hmtl, {
                width:"100%",  
                height:"auto",
                scrolling: 'auto',
                autoSize: true,                           
                padding: '0',
                openEffect: 'none',
                closeEffect: 'none',
                helpers: {
                    overlay: null
                },
                beforeClose: function() {
                    open_nuevo_cobro();
                }
            });
        }
    });
}