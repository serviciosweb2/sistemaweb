var chekiados = [];
var tablaDeudores;
var langFRM = langFrm;
var paginasCheckAll = [];

function recordarchekiados(element) 
{
    var decode = JSON.parse($(element).val());
    
    var x = decode[1];

    if ($(element).is(':checked')) 
    {
        chekiados[x] = $(element).val();
    } 
    else 
    {
        delete chekiados[x];
    }

//    console.log('CHEKIADOS',chekiados);
}

function seleccionarTodo(element) {



    var p = tablaDeudores.page();

    // console.log('viendo',p);

    if (paginasCheckAll[p]) {

        delete paginasCheckAll[p];

    } else {

        paginasCheckAll[p] = true;
    }

    // console.log('TABLA',paginasCheckAll);

    tablaDeudores.$('input[name="ctacte[]"]').trigger('click');

}
function devolverInput(registro) {
        var disabled = registro[7] == 1 || registro[8] == '0' ? disabled = "disabled" : '';

        var x = registro[1];
        var retorno = '';
        var check = chekiados[x] ? 'checked=true' : '';
        console.log('check',x+check);
        if (registro[7] == 1 || registro[8] == '0') {
            retorno = "<label><input name='ctacte[]' onclick='recordarchekiados(this)' class='ace ace-checkbox-2' type='checkbox' value='" + JSON.stringify(registro) + ' disabled="disabled"' + check + "><span class='lbl'></span></label>";
        } else {
            retorno = "<label><input name='ctacte[]' onclick='recordarchekiados(this)' class='ace ace-checkbox-2' type='checkbox' value='" + JSON.stringify(registro) + "'  "+check+"><span class='lbl'></span></label>";
        }

        return retorno;
    }
function devolverBoton(registro) {

        var retorno = '<button class="btn btn-info btn-xs boton-primario botonDetalle" value="' + registro[2] + '" ><i></i>' + langFRM.ver_detalle + '</button>';
        return retorno;
    }
    
$('.fancybox-wrap').ready(function() {

    

//    var clavesFRM = Array("descripcion", "importe", "saldo", "ver_detalle", "fecha_vencimiento", "alertar", "enviar_alerta", "enviado_correctamente", "ok", "upps");

    //var langFRM = '';

//    $.ajax({
//        url: BASE_URL + 'entorno/getLang',
//        data: "claves=" + JSON.stringify(clavesFRM),
//        type: "POST",
//        dataType: "JSON",
//        async: false,
//        cache: false,
//        success: function(respuesta) {
//            langFRM = respuesta;
//            initFRM();
//        }
//    });
    initFRM();
   
});

function initFRM() {

        var c = JSON.parse(columnas);

        c[0].sTitle = "<label><input name='seleccionar' onclick='seleccionarTodo(this)' class='ace ace-checkbox-2' type='checkbox'><span class='lbl'></span></label>";

        tablaDeudores = $('#tablaAvisoDeudores').DataTable({
            "aoColumns": c,
            "iDisplayLength": 4,
            "order": [1, 'des'],
            "bLengthChange": true,
            "serverSide": true,
            "sAjaxSource": BASE_URL + "ctacte/listarAvisoDeudores",
            "sServerMethod": "POST",
            "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                var checkbox = devolverInput(aData);

                $('td:eq(0)', nRow).html(checkbox);

                var boton = devolverBoton(aData);

                $('td:eq(4)', nRow).html(boton);
                
                return nRow;
            }

        });

        $('#tablaAvisoDeudores').on('draw.dt', function() {
            var p = tablaDeudores.page();

            if (paginasCheckAll[p]) {

                $('input[name="seleccionar"]').prop('checked', true);

            } else {

                $('input[name="seleccionar"]').prop('checked', false);

            }


            //$('input[name="seleccionar"]').trigger('click');

            $.fancybox.update();

        });


        $('#tablaAvisoDeudores').on('length.dt', function(e, settings, len) {

            chekiados = [];


            paginasCheckAll = [];



        });

        $('#tablaAvisoDeudores').wrap('<form id="frmAvisoDeudores"></form>');



        function generarTablaDetalle(rows) {

            //alert(rows.deudoresCtaCte[0].nombre_apellido);

            var nombre = '<i class="icon-double-angle-right"></i>';

            nombre += rows.deudoresCtaCte.length > 0 ? rows.deudoresCtaCte[0].nombre_apellido : '';



            $('#detalleAvisoDeudores h4 small').html(nombre);

            var tabla = '<table id="tablaDetalleDeudor" class="table table-bordered"><thead>';
            tabla += '<th>' + langFRM.descripcion + '</th><th>' + langFRM.importe + '</th><th>' + langFRM.saldo + '</th><th>' + langFRM.fecha_vencimiento + '</th><th>' + langFRM.alertar + '</th>';
            tabla += '</thead><tbody>';

            $(rows.deudoresCtaCte).each(function(k, row) {

                var rowMaxAviso = row.alertar == 1 ? '' : 'danger';
                var textoAlertar = row.alertar == 1 ? '' + langFRM.enviar_alerta : 'supero la cantidad de avisos(' + row.CantidadAlertado + ')no se alertara';

                tabla += '<tr class="' + rowMaxAviso + '"><td>' + row.descripcion + '</td><td>' + row.importeformateado + '</td><td>' + row.saldoformateado + '</td><td>' + row.fechavenc + '</td><td>' + textoAlertar + '</td></tr>';

            });

            tabla += '</tbody></table>';
            $('#detalleAvisoDeudores').find('.contenedorTabla').empty().html(tabla);
            $('#tablaDetalleDeudor').DataTable({
                "iDisplayLength": 4
            });
            $('#detalleAvisoDeudores').modal();




        }

        $('#tablaAvisoDeudores').on('click', '.botonDetalle', function() {

            var idCtaCte = $(this).val();



            var dataPOST = 'cod_alumno=' + idCtaCte + '&mostrar=0';

            $.ajax({
                url: BASE_URL + 'ctacte/frmCtaCteAlumno',
                type: "POST",
                data: dataPOST,
                dataType: "JSON",
                cache: false,
                success: function(respuesta) {

                    //console.log('CTACTE',respuesta);

                    generarTablaDetalle(respuesta);
                }
            });

            return false;
        });


        $('button[name="enviarAvisos"]').on('click', function() {

            //var dataPOST = tablaDeudores.$('input[type="checkbox"]').serialize();


            var c = [];

            for (a in chekiados) {

                c.push(chekiados[a]);
            }

            // console.log(c);

            var dataPOST = {ctacte: c};

            $.ajax({
                url: BASE_URL + "ctacte/GuardarAlertasDeudoresGeneral",
                type: "POST",
                data: dataPOST ? dataPOST : 'ctacte=',
                dataType: "JSON",
                cache: false,
                success: function(respuesta) {
                    if (respuesta.codigo == 1) {
                        $.gritter.add({
                            title: langFRM.ok,
                            text: langFRM.enviado_correctamente,
                            //image: $path_assets+'/avatars/avatar1.png',
                            sticky: false,
                            time: '3000',
                            class_name: 'gritter-success'
                        });

                        $.fancybox.close(true);

                    } else {
                        //console.log(respuesta);
                        $.gritter.add({
                            title: langFRM.upps,
                            text: respuesta.msgerror,
                            //image: $path_assets+'/avatars/avatar1.png',
                            sticky: false,
                            time: '3000',
                            class_name: 'gritter-error'
                        });
                    }
                }
            });


            return false;
        });


    }