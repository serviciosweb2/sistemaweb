var aoColumnDefs = '';
var claves = Array("validacion_ok","codigo");
var lang = '';
var indices = '';
var menu = '';
var oTable = '';

var keyColumnas = function () {
    var retorno = Array();
    $(aoColumnDefs).each(function(key, valor) {
        retorno[valor.sTitle] = key;
    });
    return retorno;
};
     
function nuevoArticulo(){            
    $.ajax({
        url: BASE_URL + "articulos/frm_articulos",
        type: "POST",
        data: "cod_articulo=-1",
        dataType: "",
        cache: false,
        success: function(respuesta){
            $.fancybox.open(respuesta,{
                arrows: false,
                padding: 0,
                openEffect: 'none',
                closeEffect: 'none',
                helpers: {
                    overlay: null
                },
                beforeShow:function(){ }
            });
        }
    });
    return false;
}

function compras(e){
  window.location.href = BASE_URL + "compras";
    e.preventDefault();
}

$(document).ready(function(){
    $.ajax({
        url: BASE_URL + 'entorno/getLang',
        data: "claves=" + JSON.stringify(claves),
        type: "POST",
        dataType: "JSON",
        async: false,
        cache: false,
        success: function(respuesta){
            lang = respuesta;
            $.ajax({
                url: BASE_URL + 'entorno/getMenuJSON',
                data: 'seccion=compras',
                dataType: 'JSON',
                type: 'POST',
                cache: false,
                async: false,
                success:function(respuesta){
                    menu = respuesta;
                    $.ajax({
                        url: BASE_URL + 'articulos/getColumns',
                        data: '',
                        dataType: 'JSON',
                        type: 'POST',
                        cache: false,
                        async: false,
                        success: function(respuesta) {
                            aoColumnDefs = respuesta;
                            indices = keyColumnas();
                            init();
                        }
                    });
                }
            });
        }
    });
    
    function init(){
        var x = indices[lang.codigo];
        oTable = $('#articulos').dataTable({
            "bServerSide": true,
            "sServerMethod": "POST",
            "sAjaxSource": BASE_URL+"articulos/listar",
            "aoColumnDefs": aoColumnDefs
        });
        $('#articulos').wrap('<div class="table-responsive"></div>');
        $('.dataTables_length').html('<button accion="compras" class="btn btn-primary no-margin" onclick="compras(event);">_Compras</button><button accion="nuevo-articulo" class="btn btn-primary no-margin" onclick="nuevoArticulo();">_Nuevo articulo</button>').parent().addClass('no-padding');
        $('.page-content').on('mousedown','#articulos tbody tr',function(e){
            var sData = oTable.fnGetData( this );
            codigo = sData[x];
            if (e.button === 2) {
                var menuC = [{
                        accion: "modificar_articulo",
                        habilitado: "1",
                        text: "_Modificar articulo"
                    },{
                        accion: "baja_articulo",
                        habilitado: "1",
                        text: "_Eliminar articulo"
                }];
                generalContextMenu(menuC, e);
                return false;
            }
        });
        
        $('body').on('click', '#menu a', function(){
            $('#menu').remove();
            switch($(this).attr('accion')){
                case'modificar_articulo':
                    $.ajax({
                        url:BASE_URL+"articulos/frm_articulos",
                        type: "POST",
                        data: "cod_articulo=" + codigo,
                        dataType: "",
                        cache: false,
                        success: function(respuesta){
                            $.fancybox.open(respuesta,{
                                arrows: false,
                                padding: 0,
                                openEffect: 'none',
                                closeEffect: 'none',
                                helpers: {
                                    overlay: null
                                },
                                beforeShow: function(){}
                            });
                        }
                    });
                    break;
                
                case 'baja_articulo':
                    break;
            };
            return false;
        });
    }
});