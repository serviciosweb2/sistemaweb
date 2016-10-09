var aoColumnDefs = columns;

//var claves = Array('estado_razon', 'codigo', "BIEN", "HABILITAR", "INHABILITAR", "HABILITADO", "INHABILITADO", 'ocurrio_error');

var menuARRAY = '';

var codigo = '';

var oTable = '';

var estado = '';

lang = BASE_LANG;

menu = menuJson;

$(document).ready(function() 
{

//    $.ajax({
//        url: BASE_URL + 'entorno/getMenuJson',
//        type: "POST",
//        data: "seccion=razonessociales",
//        dataType: "JSON",
//        cache: false,
//        success: function(respuesta) {
//
//            menuARRAY = respuesta;
//
//        }
//    });

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
//                data: 'seccion=razonessociales',
//                dataType: 'JSON',
//                type: 'POST',
//                cache: false,
//                async: false,
//                success: function(respuesta) {
//
//                    //menu = respuesta;
//                    $.ajax({
//                        url: BASE_URL + 'razonessociales/getColumns',
//                        data: '',
//                        dataType: 'JSON',
//                        type: 'POST',
//                        cache: false,
//                        async: false,
//                        success: function(respuesta) {
//                            aoColumnDefs = respuesta;
//                            init();
//
//                        }
//                    });
//
//
//
//
//                }});
//
//
//        }
//
//        
//    });

    init();
   
});

 function init() {

        //CONFIGURACION DE LA TABLA:
        oTable = $('#razonesSociales').dataTable({
            "bServerSide": true,
            "sAjaxSource": BASE_URL + 'razonessociales/listar',
            "aaSorting": [[0, "desc"]],
            "sServerMethod": "POST",
            "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                var estado = aData[8];

                imgTag = devolverEstado(estado);

                $('td:eq(7)', nRow).html(imgTag);

                return nRow;
            },
            'aoColumnDefs': aoColumnDefs
        });
        marcarTr();
        $('#razonesSociales').wrap('<div class="table-responsive"></div>');

        thead = [];

        $(aoColumnDefs).each(function() {

            thead.push(this.sTitle);

        });

        $(".dataTables_length").html(generarBotonSuperiorMenu(menu.superior, "btn-primary", "icon-user"));


        function columnName(name) {

            var retorno = '';
            $(thead).each(function(key, valor) {
                if (valor == name) {
                    retorno = key;
                }

            });
            return retorno;
        }


        function devolverEstado(estadoA) {
            var clase = "";
            var estado = "";

            switch (estadoA) {
                case '1':
                    clase = "label label-default arrowed";
                    estado = lang.INHABILITADO;

                    break;

                case '0':
                    clase = "label label-success arrowed";
                    estado = lang.HABILITADO + "&nbsp";
                    break;

            }

            imgTag = '<span class="' + clase + '">' + estado + '</span>';
            return imgTag;
        }

        // CAPTURA DEL EVENTO CLICK DERECHO:

        var desactivado = '';


        $('body').not('table').on('click', function() {

            $('#menu').hide().fadeIn('fast').remove();
        });


        $('#areaTablas').on('mousedown', '#razonesSociales tbody tr', function(e) {

            $('#menu').hide().fadeIn('fast').remove();

            var sData = oTable.fnGetData(this);

            if (e.button === 2) {

                estado = sData[columnName(lang.estado_razon)];
                codigo = sData[columnName(lang.codigo)];

                generalContextMenu(menu.contextual, e);

                switch (estado) {

                    case "0" :

                        $('a[accion="cambiar_estado_razon"]').text(lang.INHABILITAR);

                        break

                    case "1":

                        $('a[accion="cambiar_estado_razon"]').text(lang.HABILITAR);

                        break
                }

                return false;
            }

        });


        //FUNCION QUE TOMA CLICK EN EL MENU DESPLEGABLE:


        $('body').on('click', '#menu a', function() {
            //$('#menu').hide().fadeIn('fast').remove();

            var accion = $(this).attr('accion');

            var id = $(this).attr('id');

            $('#menu').remove();

            switch (accion) {

                case 'modificar_razon_social':

                    $.ajax({
                        url: BASE_URL + 'razonessociales/frm_razones_sociales',
                        data: 'codigo=' + codigo,
                        type: 'POST',
                        cache: false,
                        success: function(respuesta) {
//                            $.fancybox.open(respuesta, {
//                                scrolling: 'auto',
//                                autoSize: false,
//                                width: 'auto',
//                                height: 'auto',
//                                padding: 1,
//                                openEffect: 'none',
//                                closeEffect: 'none',
//                                helpers: {
//                                    overlay: null
//                                }                        
//                            });
                            
                            $.fancybox.open(respuesta, {
                                autoSize: false,
                                width: '100%',
                                height: 'auto',
                                scrolling: 'auto',
                                padding: 0,
                                openEffect: 'none',
                                closeEffect: 'none',
                                helpers: {
                                    overlay: null

                                }


                            });
                        }

                    });
                    break;


                case 'cambiar_estado_razon':

                    cambiarEstado();

                    break;

                default:

                    $.gritter.add({
                        title: 'Upps',
                        text: ' NO TIENE PERMISO !',
                        sticky: false,
                        time: '3000',
                        class_name: 'gritter-error'
                    });


                    break;

            }
            return false;
        });


        $('body').on('click', '.dataTables_length button', function() {

            var accion = $(this).attr('accion');

            var id = $(this).attr('id');

            $('#menu').remove();


            switch (accion) {

                case 'nueva_razon_social':

                    $.ajax({
                        url: BASE_URL + 'razonessociales/frm_razones_sociales',
                        data: 'codigo=',
                        type: 'POST',
                        cache: false,
                        success: function(respuesta) {
                            $.fancybox.open(respuesta, {
                                autoSize: false,
                                width: '100%',
                                height: 'auto',
                                scrolling: 'auto',
                                padding: 0,
                                openEffect: 'none',
                                closeEffect: 'none',
                                helpers: {
                                    overlay: null

                                }


                            });
                            $(".fancybox-wrap").find("[name=nombre]").focus();


                        }

                    });
                    break;


            }

            return false;
        });


        function cambiarEstado() {

            $.ajax({
                url: BASE_URL + 'razonessociales/cambiarEstado',
                data: 'codigo=' + codigo,
                type: 'POST',
                cache: false,
                dataType: 'json',
                success: function(respuesta) {


                    if (respuesta.codigo === 1) {

                        $('#msgComfirmacion').modal('hide');

                        $.gritter.add({
                            title: lang.BIEN,
                            text: lang.MATRICULA_HABILITADA,
                            sticky: false,
                            time: '3000',
                            class_name: 'gritter-success'
                        });


                        oTable.fnDraw();

                    } else {

                        gritter(lang.ocurrio_error);


                    }



                }

            });


        }

    }