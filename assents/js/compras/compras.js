var codigoSeleccionado = '';
var estadoCambiar = '';
var contaxtualCompras = new Array();
var oTable = '';
var aoColumnDefsARTICULOS = '';
var codigoSeleccionado = '';
var aoColumnDefsProveedores = '';
var titleProveedores = '';
var indices = '';
var lang = BASE_LANG;
var aoColumnDefs = columns;

function nuevoArticulo() {

    $.ajax({
        url: BASE_URL + "articulos/frm_articulos",
        type: "POST",
        data: "cod_articulo=-1",
        dataType: "",
        cache: false,
        success: function(respuesta) {

            $.fancybox.open(respuesta, {
                arrows: false,
                padding: 0,
                openEffect: 'none',
                closeEffect: 'none',
                helpers: {
                    overlay: null
                },
                beforeShow: function() {

                    //$('.fancybox-wrap #formulario').html(i);

                }
            });


        }


    });


    return false;


}

function open_nuevo_proveedor() {

    $.ajax({
        url: BASE_URL + "proveedores",
        data: '',
        type: 'POST',
        cache: false,
        async: false,
        success: function(respuesta) {
            $.fancybox.open(respuesta, {
                width: 'auto',
                height: 'auto',
                autoSize: false,
                autoResize: false,
                padding: 0,
                openEffect: 'none',
                closeEffect: 'none',
                helpers: {
                    overlay: null
                }, beforeClose: function() {
                    oTable.fnDraw();

                }
            });
        }
    });
}


function frmAgregarModificarProveedor(cod_proveedor) {
    $.ajax({
        url: BASE_URL + 'proveedores/frm_proveedores',
        type: 'POST',
        data: {
            cod_proveedor: cod_proveedor
        },
        success: function(_hmtl) {
            var fancyproveedores = $.fancybox.open(_hmtl, {
                width: "100%",
                height: "auto",
                scrolling: 'auto',
                autoSize: true,
                padding: '0',
                openEffect: 'none',
                closeEffect: 'none',
                helpers: {
                    overlay: null
                },
                beforeClose: function() {
                    //open_nuevo_cobro();
                }
            });
        }
    });
}



var keyColumnas = function() {


    var retorno = Array();
    $(aoColumnDefs).each(function(key, valor) {

        retorno[valor.sTitle] = key;

    });

    return retorno;
};

$(document).ready(function() {
    menu = menuJson;
    marcarTr();
    var thead = [];
    var data = '';
//    var claves = Array("habilitar-factura",
//            "deshabilitar-factura",
//            "codigo",
//            "facturacion_estado",
//            "facturacion_anular",
//            "habilitada",
//            "inhabilitada",
//            "descripcion",
//            "importe",
//            "iva",
//            "INHABILITAR",
//            "HABILITAR",
//            "HABILITADO",
//            "INHABILITADO",
//            "error_habilitar_proveedor",
//            "errir_inhabilitar_proveedor",
//            "nuevo_articulo",
//            "compras",
//            "modificar_articulo",
//            "eliminar_articulo",
//            "nuevo_proveedor",
//            "validacion_ok",
//            "BIEN",
//            "ocurrio_error",
//            "ERROR"
//            );
    $.each(menuJson.contextual, function(key, value) {
                        if (key == "0" || key == "3") {
                            contaxtualCompras.push(value);
                        }
                    });
//    console.log('lang',lang);
//    $.ajax({
//        url: BASE_URL + 'entorno/getLang',
//        data: "claves=" + JSON.stringify(claves),
//        dataType: 'JSON',
//        type: 'POST',
//        cache: false,
//        async: false,
//        success: function(respuesta) {
//            //lang = respuesta;
//            $.ajax({
//                url: BASE_URL + 'entorno/getMenuJSON',
//                data: 'seccion=compras',
//                dataType: 'JSON',
//                type: 'POST',
//                cache: false,
//                async: false,
//                success: function(respuesta) {
//                    //menu = respuesta;
//                    console.log('MI MENU',menuJson);
//                    $.each(menuJson.contextual, function(key, value) {
//                        if (key == "0" || key == "3") {
//                            //contaxtualCompras.push(value);
//                        }
//                    });
//                    $.ajax({
//                        url: BASE_URL + 'compras/getColumns',
//                        data: '',
//                        dataType: 'JSON',
//                        type: 'POST',
//                        cache: false,
//                        async: false,
//                        success: function(respuesta) {
//                            aoColumnDefs = respuesta;
//
//                            $.ajax({
//                                url: BASE_URL + 'articulos/getColumns',
//                                data: '',
//                                dataType: 'JSON',
//                                type: 'POST',
//                                cache: false,
//                                async: false,
//                                success: function(respuesta) {
//
//                                    aoColumnDefsARTICULOS = respuesta;
//
//                                    
//
//                                    init();
//
//
//                                }
//                            });
//
//
//                        }
//                    });
//                }
//            });
//        }
//    });

$.ajax({
                                url: BASE_URL + 'articulos/getColumns',
                                data: '',
                                dataType: 'JSON',
                                type: 'POST',
                                cache: false,
                                async: false,
                                success: function(respuesta) {

                                    aoColumnDefsARTICULOS = respuesta;

                                    indices = keyColumnas();

                                    init();


                                }
                            });



    /* Table initialisation */
    function init() {


        var codigo = '';

        function ejson(string) {
            try {
                JSON.parse(string);
            } catch (e) {
                return false;
            }
            return true;
        }

        var nCloneTd = document.createElement('td');

        nCloneTd.innerHTML = 'link';

        nCloneTd.className = "center";

        oTable = $('#administracionCompras').dataTable({
            "bProcessing": false,
            "bServerSide": true,
            "aaSorting": [[0, "desc"]],
            "sAjaxSource": BASE_URL + "compras/listar",
            "sServerMethod": "POST",
            "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                // console.log(aData);
                var estado = aData[5];
                var btn = "";
//                alert(estado);
                switch (estado) {
                    case "anulada":
                        btn = "<span class='label label-default arrowed'>" + lang.inhabilitada + "</span>";
                        break;
                    case "confirmada":
                        btn = "<span class='label label-success arrowed'>" + lang.habilitada + "</span>";
                        break;
                }
                $('td:eq(4)', nRow).html(btn).addClass('text-center');
            },
            "fnServerData": function(sSource, aData, fnCallback) {

                $.ajax({
                    "dataType": 'json',
                    "type": "POST",
                    "url": sSource,
                    "data": aData,
                    "async": false,
                    "success": fnCallback
                });
                $('#administracionCompras tbody tr .btn').each(function() {
                    $(this).popover({
                        placement: 'left',
                        title: 'Detalle',
                        html: true,
                        trigger: 'manual'
                    });
                });
            },
            "aoColumnDefs": aoColumnDefs
        });

        $(aoColumnDefs).each(function() {
            thead.push(this.sTitle);
        });


        function columnName(name) {
            var retorno = '';
            $(thead).each(function(key, valor) {
                if (valor == name) {
                    retorno = key;
                }
            });
            return retorno;
        }
//TABLA DE COMPRAS////////////////////////////////////////////////////////////////////////
        $("#administracionCompras_length").html(generarBotonSuperiorMenu(menu.superior, "btn-primary", "icon-bookmark"));

        var baja = '';

        $('.page-content').on('mousedown', '#administracionCompras tbody tr', function(e) {
            var sData = oTable.fnGetData(this);

            var estado = sData[5];
            codigo = sData[0];
            // anular = sData[columnName(lang['facturacion_anular'])];
            if (e.button === 2) {
                generalContextMenu(contaxtualCompras, e);
                if (estado == 'confirmada') {
                    $('a[accion="cambiar_estado_compra"]').text(lang['INHABILITAR']);
                } else {
                    $('a[accion="cambiar_estado_compra"]').text(lang['HABILITAR']);
                }
//                if (anular == 0) {
//                    $('a[accion="cambiar-estado-facturas"]').addClass('deshabilitado').attr('accion', '');
//                }
                return false;
            }
        });

        $('#administracionCompras').on('click', '.btn', function() {
            boton = this;
            if ($(boton).parent().find('.popover').is(':visible')) {
                $('.btn').popover('hide');
            } else {
                $.ajax({
                    url: BASE_URL + 'facturacion/getRenglonesDescripcion',
                    type: 'POST',
                    data: 'cod_factura=' + codigo,
                    dataType: 'json',
                    cache: false,
                    success: function(respuesta) {
                        tablaDetalle = '<div class="row"><div class="col-md-12"><table class="table table-striped table-bordered"><thead>';
                        tablaDetalle += '<th>' + lang.descripcion + '</th><th>' + lang.importe + '</th><th>' + lang.iva + '</th>';
                        tablaDetalle += '<tbody>';
                        $(respuesta).each(function(key, fila) {
                            tablaDetalle += '<tr><td>' + fila.descripcion + '</td><td>' + fila.importe + '</td><td>' + fila.iva + '</td></tr>';
                        });
                        tablaDetalle += '<tbody></table></div></div>';
                        $(boton).attr('data-content', tablaDetalle);
                        $('.btn').not(boton).popover('hide');
                        $(boton).popover('show');
                    }
                });
            }
            return false;
        });



//TABLA ARTICULOS/////////////////////////////////////////////////////////////////////////////////
        var x = indices[lang.codigo];

        oTableARTICULOS = $('#articulos').dataTable({
            "bServerSide": true,
            "sServerMethod": "POST",
            "sAjaxSource": BASE_URL + "articulos/listar",
            "aoColumnDefs": aoColumnDefsARTICULOS,
            "aaSorting": [[0, 'desc']],
            "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                var estado = aData[7];

                var btn = "";
                switch (estado) {
                    case "habilitado":
                        btn = "<span class='label label-success arrowed'>" + lang.HABILITADO + "</span>";
                        break;
                    case "inhabilitado":
                        btn = "<span class='label label-default arrowed'>" + lang.INHABILITADO + "</span>";
                        break;
                }
                $('td:last', nRow).html(btn);
            }
        });




        var menuS = [
            {
                accion: "nuevo-articulo",
                habilitado: "1",
                text: lang.nuevo_articulo
            },
            {
                accion: "compras",
                habilitado: "1",
                text: lang.compras
            },
            {
                accion: "nuevo_proveedor",
                habilitado: "1",
                text: lang.nuevo_proveedor
            }

        ];


        $('#articulos').wrap('<div class="table-responsive"></div>');

        $('#articulos_length').html(generarBotonSuperiorMenu(menuS, "btn-primary", "icon-bookmark")).parent().addClass('no-padding');

        var codigoART = '';

        $('.page-content').on('mousedown', '#articulos tbody tr', function(e) {

            var sDataART = oTableARTICULOS.fnGetData(this);

            codigoART = sDataART[x];

            if (e.button === 2) {


                var menuC = [
                    {
                        accion: "modificar_articulo",
                        habilitado: "1",
                        text: lang.modificar_articulo
                    }, {
                        accion: "baja_articulo",
                        habilitado: "1",
                        text: lang.eliminar_articulo
                    }];

                generalContextMenu(menuC, e);
                var estado_articulo = sDataART[7]
                if (estado_articulo == "habilitado") {

                    $("a[accion='baja_articulo']").text(lang.INHABILITAR);
                    estadoCambiar = 1;
                } else {
                    $("a[accion='baja_articulo']").text(lang.HABILITAR);
                    estadoCambiar = 0;
                }



                return false;
            }
        });


        $('body').on('click', '#menu a', function() {

            $('#menu').remove();

            switch ($(this).attr('accion')) {

                case'modificar_articulo':



                    $.ajax({
                        url: BASE_URL + "articulos/frm_articulos",
                        type: "POST",
                        data: "cod_articulo=" + codigoART,
                        dataType: "",
                        cache: false,
                        success: function(respuesta) {
                            $.fancybox.open(respuesta, {
                                arrows: false,
                                padding: 0,
                                openEffect: 'none',
                                closeEffect: 'none',
                                helpers: {
                                    overlay: null
                                },
                                beforeShow: function() {



                                }
                            });
                        }
                    });


                    break;

                case 'baja_articulo':

                    $.ajax({
                        url: BASE_URL + "articulos/cambiarEstado",
                        type: "POST",
                        data: "cod_articulo=" + codigoART,
                        dataType: "JSON",
                        cache: false,
                        success: function(respuesta) {
                            if (respuesta.codigo == 1) {
                                gritter(lang.validacion_ok, true);
                                oTableARTICULOS.fnDraw();
                            } else {
                                gritter(respuesta.msgError);
                            }

                        }
                    });

                    break;

            }
            ;



            return false;

        });


        //CONTROLADOR DE VISTAS
//        $('body').on('click','.dropdown-menu li a',function(){  
//           
//            $(".btn-group").removeClass("open");
//            
//            var accion=$(this).attr('accion');
//            
//            switch(accion){
//                
//                case 'compras':
//                    
//                    $('.vistaCompras').show();
//                       
//                       
//                    $('.vistaArticulos').hide();
//                    
//                    break;
//
//                
//            }
//            
//            return false;
//        });



        ////////////////TABLA PROVEEDORES/////////////////////////////



        oTableProveedores = $("#tableProveedores").dataTable({
            "bServerSide": true,
            "aaSorting": [[0, "desc"]],
            "sAjaxSource": BASE_URL + "proveedores/listar",
            "sServerMethod": "POST",
            "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                var estado = aData[7];
                var btn = "";
                switch (estado) {
                    case "1":
                        btn = "<span class='label label-default arrowed'>" + lang.INHABILITADO + "</span>";
                        break;
                    case "0":
                        btn = "<span class='label label-success arrowed'>" + lang.HABILITADO + "</span>";
                        break;
                }
                $('td:last', nRow).html(btn);
            },
        });



        var menuS = [
            {
                accion: "nuevo-proveedor",
                habilitado: "1",
                text: lang.nuevo_proveedor
            },
            {
                accion: "compras",
                habilitado: "1",
                text: lang.compras
            },
            {
                accion: "nuevo_articulo",
                habilitado: "1",
                text: lang.nuevo_articulo
            },
        ];


        $('#articulos').wrap('<div class="table-responsive"></div>');

        $('#tableProveedores_length').html(generarBotonSuperiorMenu(menuS, "btn-primary", "icon-bookmark")).parent().addClass('no-padding');

        $('#tableProveedores').on('mousedown', 'tbody tr', function(e) {
            //console.log('primer mouse down', e);
            var sDataP = oTableProveedores.fnGetData(this);

            if (e.button === 2) {

                contextualProvedores = [];
                $.each(menu.contextual, function(key, value) {
                    if (key == 1 || key == 2) { // solo buscamos los items del menu que nos interesa
                        contextualProvedores.push(value);
                    }
                });

                generalContextMenu(contextualProvedores, e);

                codigoSeleccionado = sDataP[0];

                var estado = sDataP[7];


                if (estado == 0) {

                    $("a[accion='cambiar_estado_proveedor']").text(lang.INHABILITAR);
                    estadoCambiar = 1;
                } else {
                    $("a[accion='cambiar_estado_proveedor']").text(lang.HABILITAR);
                    estadoCambiar = 0;
                }
                return false;
            }
        });

//        $('.page-content').on('mousedown','#tableProveedores tbody tr',function(e){
//             console.log('segundo mouse down',e);
//            if( e.button === 2 ) {  
//                contextualProvedores=[];
//                $.each(menu.contextual, function(key, value){
//                        if (key == 1 || key == 2){ // solo buscamos los items del menu que nos interesa
//                            contextualProvedores.push(value);
//                        }
//                    });
//                
//                generalContextMenu(contextualProvedores, e);
//                
//                return false; 
//            }
//        });


        //////////////////////////////////CONTROLADORES//////////////////////////// 

        //CONTROLADOR CONTEXTUAL
        $('body').on('click', '#menu a', function() {
            var accion = $(this).attr('accion');
            $('#menu').remove();
            switch (accion) {
                case 'modificar_compra':
                    $.ajax({
                        url: BASE_URL + "compras/frm_compras",
                        data: 'codigo=' + codigo,
                        type: 'POST',
                        cache: false,
                        success: function(respuesta) {
                            $.fancybox.open(respuesta, {
                                autoSize: false,
                                width: '100%',
                                height: 'auto',
                                padding: '0',
                                openEffect: 'none',
                                closeEffect: 'none',
                                helpers: {
                                    overlay: null
                                },
                                afterLoad: function() {

                                },
                                beforeClose: function() {
                                    oTable.fnDraw();
                                }
                            });
                        }
                    });
                    break;

                case 'modificar_proveedor':
                    frmAgregarModificarProveedor(codigoSeleccionado);
                    break;

                case 'cambiar_estado_proveedor':
                    $.ajax({
                        url: BASE_URL + 'proveedores/cambiarEstado',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            cod_proveedor: codigoSeleccionado
                        },
                        success: function(_json) {
                            if (_json.codigo == 1) {
                                var complemento = estadoCambiar == 0 ? lang.HABILITADO : lang.INHABILITADO;
                                gritter(complemento, true);
                                oTableProveedores.fnDraw();
                            } else {
                                var complemento = estadoCambiar == 0 ? lang.error_habilitar_proveedor : lang.error_inhabilitar_proveedor;
                                gritter(complemento, false);
                            }
                        }
                    });
                    break;
                case 'cambiar_estado_compra':
                    $.ajax({
                        url: BASE_URL + 'compras/cambiarEstado',
                        data: 'codigo=' + codigo,
                        type: 'POST',
                        cache: false,
                        dataType: 'json',
                        success: function(respuesta) {

                            if (respuesta.codigo === 1) {

                                $('#msgComfirmacion').modal('hide');

                                $.gritter.add({
                                    title: lang.BIEN,
                                    text: '',
                                    sticky: false,
                                    time: '3000',
                                    class_name: 'gritter-success'
                                });

                                oTable.fnDraw();

                            } else {
                                $.gritter.add({
                                    title: lang.ERROR,
                                    text: respuesta.errors,
                                    sticky: false,
                                    time: '3000',
                                    class_name: 'gritter-error'
                                });

                            }

                        }
                    });
                    break;
            }
            return false;
        });

        //CONTROLADOR BOTON PRIMARIO
        $('body').on('click', '.boton-primario', function() {

            var accion = $(this).attr('accion');

            $('#desplegable').remove();

            switch (accion) {
                case 'nueva_compra':
                    $.ajax({
                        url: BASE_URL + "compras/frm_compras",
                        data: 'codigo=-1' + '&continuar=' + false,
                        type: 'POST',
                        cache: false,
                        success: function(respuesta) {
                            $.fancybox.open(respuesta, {
                                autoSize: false,
                                width: '100%',
                                height: 'auto',
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
                    break;
                case 'nuevo-articulo':

                    $.ajax({
                        url: BASE_URL + "articulos/frm_articulos",
                        type: "POST",
                        data: "cod_articulo=-1",
                        dataType: "",
                        cache: false,
                        success: function(respuesta) {
                            $.fancybox.open(respuesta, {
                                arrows: false,
                                padding: 0,
                                openEffect: 'none',
                                closeEffect: 'none',
                                helpers: {
                                    overlay: null
                                },
                                beforeShow: function() {

                                    //$('.fancybox-wrap #formulario').html(i);

                                }
                            });
                        }
                    });

                    break;

                case'nuevo-proveedor':
                    frmAgregarModificarProveedor(-1);
                    break;
            }
        });


        //CONTROLADOR DE VISTAS
        $('.modulo').on('click', '.dropdown-menu li a', function() {

            $(".btn-group").removeClass("open");

            $('.modulo').hide();

            var accion = $(this).attr('accion');

            switch (accion) {
                case 'compras':

                    $('.vistaCompras').show();

                    break;


                case 'nuevo_proveedor':

                    //open_nuevo_proveedor();

                    $('.vistaProvedores').show();

                    break;

                case 'nuevo_articulo':


                    $('.vistaArticulos').show();

                    break;
            }

            return false;
        });

    }

});

