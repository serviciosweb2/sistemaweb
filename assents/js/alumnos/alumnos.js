var aoColumnDefs = '';
var menuARRAY = '';
var codigo = '';
var email = '';
var oTable = '';
var estado = '';
var lang = BASE_LANG;
menu = BASE_MENU_JSON;
aoColumnDefs = columns;

var pos = 0;
            var ctx = null;
            var cam = null;
            var image = null;
            var filter_on = false;
            var filter_id = 0;
            
            function changeFilter() {
                if (filter_on) {
                    filter_id = (filter_id + 1) & 7;
                }
            }

            function toggleFilter(obj) {
                if (filter_on =! filter_on) {
                    obj.parentNode.style.borderColor = "#c00";
                } else {
                    obj.parentNode.style.borderColor = "#333";
                }
            }

function msgAdvertencia() {
    $.ajax({
        url: BASE_URL + "alumnos/frm_baja",
        type: "POST",
        data: "cod_alumno=" + codigo,
        dataType: "JSON",
        cache: false,
        async: false,
        success: function(respuesta) {
            $('#textoMsg').html(respuesta.respuesta);
        }
    });
    $('#msgComfirmacion').modal();
}


function cambiarEstado() {
    $.ajax({
        url: BASE_URL + 'alumnos/cambiarEstado',
        data: 'codigo_alumno=' + codigo,
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


function init() {
    oTable = $('#academicoAlumnos').dataTable({
        "bServerSide": true,
        "sAjaxSource": BASE_URL + 'alumnos/listar',
        "aaSorting": [[0, "desc"]],
        "sServerMethod": "POST",
        //modificacion franco ticket 5053->
        "bAutoWidth": false,
        "aoColumns": [
        { "sWidth": "8.3%" },//codigo
        { "sWidth": "8.3%" },//nombre y apellido
        { "sWidth": "8.3%" },//fecha nac
        { "sWidth": "8.3%" },//documento
        { "sWidth": "8.3%" },//localidad       
        { "sWidth": "8.3%" },//domicilio
        { "sWidth": "8.3%" },//como nos conocio
        { "sWidth": "8.3%" },//email
        { "sWidth": "8.3%" },//id fiscal
        { "sWidth": "8.3%" },//talle
        { "sWidth": "8.3%" },//fecha alta
        { "sWidth": "8.3%" }//estado
        ],
        //<-modificacion franco ticket 5053
        "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            //modificacion franco ticket 5053-> (cambie los num de indice pq es donde se muestra la imagen jscript)
            var estado = aData[11];
            imgTag = devolverEstado(estado);
            $('td:eq(11)', nRow).html(imgTag);
            return nRow;
        },
        //<-modificacion franco ticket 5053
        'aoColumnDefs': aoColumnDefs,
        fnServerData: function(sSource, aData, fnCallback){
            var provincia = $("[name=filtro_provincia]").val();
            var medio = $("[name=filtro_localidad]").val();
            var curso_interes = $("[name=filtro_como_nos_conocio]").val();
            var turno = $("[name=filtro_estado]").val();
            var talle = $("[name=filtro_talle]").val();
            var fecha_desde = $("[name=filtro_fecha_alta_desde]").val();
            var fecha_hasta = $("[name=filtro_fecha_alta_hasta]").val();
            
            aData.push({name: 'provincia', value: provincia});
            aData.push({name: 'localidad', value: medio});
            aData.push({name: 'como_nos_conocio', value: curso_interes});
            aData.push({name: 'estado', value: turno});
            aData.push({name: "talle", value: talle});
            aData.push({name: "fecha_alta_desde", value: fecha_desde});
            aData.push({name: "fecha_alta_hasta", value: fecha_hasta});
            $.ajax({
                dataType: 'json',
                type: "POST",
                url: sSource,
                data: aData,
                async: true,
                success: fnCallback
            });
        }
    });
    marcarTr();
    console.log(aoColumnDefs);
    $('#academicoAlumnos').wrap('<div class="table-responsive"></div>');
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
            case "inhabilitada":
                clase = "label label-danger arrowed";
                estado = lang.INHABILITADO;
                break;

            case "habilitada" :
                clase = "label label-success arrowed";
                estado = lang.HABILITADO + "&nbsp";
                break;
        }
        imgTag = '<span class="' + clase + '">' + estado + '</span>';
        return imgTag;
    }

    var desactivado = '';
    $('body').not('table').on('click', function() {
        $('#menu').hide().fadeIn('fast').remove();
    });

    $('#areaTablas').on('mousedown', '#academicoAlumnos tbody tr', function(e) {
        $('#menu').hide().fadeIn('fast').remove();
        var sData = oTable.fnGetData(this);
        if (e.button === 2) {
            estado = sData[11];
            codigo = sData[columnName(lang.codigo)];
            email = sData[2];
            //modificacion franco ticket 5053->(cambie los num de indice pq es donde se muestra el dato de reenviar email)
            reenviar_mail = sData[13];
            //<-modificacion franco ticket 5053
            generalContextMenu(menu.contextual, e);
            switch (estado) {                    
                case "habilitada" :
                    $('a[accion="cambiar_estado_alumno"]').text(lang.INHABILITAR);
                    break

                case "inhabilitada":
                    $('a[accion="cambiar_estado_alumno"]').text(lang.HABILITAR);
                    break
            }
            if(reenviar_mail != 0){
                $(menu.contextual).each(function(k, option) {
                    if (option.accion == 'reenviar_mail_campus_alumno'){
                      //  $("#menu a[accion=reenviar_mail_campus_alumno]").closest("li").hide();
                    }

                });
            }
            return false;
        }
    });

    $("#academicoAlumnos_filter").find("label").addClass("input-icon input-icon-right");
    $("#academicoAlumnos_filter").find("label").append('<i name="icon_filters" class="icon-caret-down grey" style="margin-right: 7px; cursor: pointer"></i>');
    $("#academicoAlumnos_filter").append($("[name=container_menu_filters_temp]").html());
    $(".date-picker").datepicker();
    $(".select_chosen").chosen();
    $("[name=container_menu_filters_temp]").remove();
    $("[name=div_table_filters]").hide();
    
    $("#academicoAlumnos_filter").append('<i id="exportar_informe" class="icon-print grey" onclick="exportar_informe(\'pdf\');" style="cursor: pointer; margin-left: 6px;" data-original-title="" title="exportar"></i>');
    $("#academicoAlumnos_filter").append('<i id="exportar_informe" class="icon-external-link" onclick="exportar_informe(\'csv\');" style="cursor: pointer; margin-left: 6px;" data-original-title="" title="exportar"></i>');
        
    $("[name=icon_filters]").on("click", function() {
        $("[name=contenedorPrincipal]").toggle();
        $("[name=div_table_filters]").toggle(300);
        return false;
    });

    $("[name=contenedorPrincipal]").on("mousedown", function() {
        $("[name=contenedorPrincipal]").hide();
        $("[name=div_table_filters]").hide(300);
    });

    $('body').on('click', '#menu a', function() {
        var accion = $(this).attr('accion');
        var id = $(this).attr('id');
        $('#menu').remove();
        switch (accion) {
            case 'modificar_alumno':
                $.ajax({
                    url: BASE_URL + 'alumnos/form_alumnos',
                    data: 'codigo=' + codigo,
                    type: 'POST',
                    cache: false,
                    success: function(respuesta) {
                        $.fancybox.open(respuesta, {
                            scrolling: 'auto',
                            autoSize: false,
                            width: 'auto',
                            height: 'auto',
                            padding: 1,
                            openEffect: 'none',
                            closeEffect: 'none',
                            helpers: {
                                overlay: null
                            }                                 
                        });
                    }
                });
                break;

            case 'pasar_alumno_aspirante':
                $.ajax({
                    url: BASE_URL + 'alumnos/form_alumnos',
                    data: 'codigo=' + codigo + '&persona=1',
                    type: 'POST',
                    cache: false,
                    success: function(respuesta) {
                        $.fancybox.open(respuesta, {
                            maxWidth: 1000,
                            maxHeight: 1000,
                            scrolling: false,
                            width: '100%',
                            height: '100%',
                            autoSize: true,
                            padding: 8,
                            wrapCSS: 'fancy_custom'
                        });
                    }
                });
                break;

            case 'cambiar_estado_alumno':
                if (estado == 'habilitada') {
                    msgAdvertencia();
                } else {
                    cambiarEstado();
                }
                break

            case 'cargar-nota':
                $.ajax({
                    url: BASE_URL + 'examenes/frm_cargarNotaAlumno',
                    data: 'codigo=' + codigo,
                    type: 'POST',
                    cache: false,
                    success: function(respuesta) {
                        $.fancybox.open(respuesta,{
                            maxWidth: 1000,
                            maxHeight: 1000,
                            scrolling: 'auto',
                            width: 'auto',
                            height: 'auto',
                            autoSize: false,
                            padding: 1
                        });
                    }
                });
                break;

            case 'ver_facturas':
                $.ajax({
                    url: BASE_URL + 'alumnos/ver_facturas_alumno',
                    data: 'cod_alumno=' + codigo,
                    type: 'POST',
                    cache: false,
                    success: function(respuesta) {
                        $.fancybox.open(respuesta, {
                            autoSize: false,
                            width: '60%',
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

            case 'asistencia-alumno':
                $.ajax({
                    url: BASE_URL + 'asistencias/frm_asistenciasAlumno',
                    data: 'codigo=' + codigo,
                    type: 'POST',
                    cache: false,
                    success: function(respuesta) {
                        $.fancybox.open(respuesta,{
                            autoSize: false,
                            width: '70%',
                            height: 'auto',
                            scrolling: 'auto',
                            padding: 1
                        });
                    }
                });
                break;

            case 'reenviar_mail_campus_alumno':
                if(email != ''){
                    $.ajax({
                        url: BASE_URL + 'alumnos/reenviar_mail_campus_alumno',
                        data: 'cod_alumno=' + codigo,
                        type: 'POST',
                        cache: false,
                        success: function(respuesta) {
                            $.fancybox.open(respuesta,{
                                autoSize: true,
                                width: 'auto',
                                height: 'auto',
                                scrolling: 'auto',
                                padding: 1,
                                helpers:  {
                                    overlay : null
                                },
                                wrapCSS: 'fancy_custom'
                            });
                        }
                    });
                }else{
                    gritter(lang.no_puede_reenviar_mail_al_alumno);
                }                    
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
            case 'nuevo_alumnos':
                $.ajax({
                    url: BASE_URL + 'alumnos/form_alumnos',
                    data: 'codigo_aspirante='+codigo_aspirante,
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

    if(ver_fancy){
        $('.boton-primario').trigger('click');
    }
}


function getPageSize(){
                var xScroll, yScroll;
                if (window.innerHeight && window.scrollMaxY) {
                    xScroll = window.innerWidth + window.scrollMaxX;
                    yScroll = window.innerHeight + window.scrollMaxY;
                } else if (document.body.scrollHeight > document.body.offsetHeight){
                    xScroll = document.body.scrollWidth;
                    yScroll = document.body.scrollHeight;
                } else {
                    xScroll = document.body.offsetWidth;
                    yScroll = document.body.offsetHeight;
                }
                var windowWidth, windowHeight;
                if (self.innerHeight) {
                    if(document.documentElement.clientWidth){
                    windowWidth = document.documentElement.clientWidth;
                } else {
                    windowWidth = self.innerWidth;
                }
                    windowHeight = self.innerHeight;
                } else if (document.documentElement && document.documentElement.clientHeight) {
                    windowWidth = document.documentElement.clientWidth;
                    windowHeight = document.documentElement.clientHeight;
                } else if (document.body) {
                    windowWidth = document.body.clientWidth;
                    windowHeight = document.body.clientHeight;
                }
                if(yScroll < windowHeight){
                    pageHeight = windowHeight;
                } else {
                    pageHeight = yScroll;
                }
                if(xScroll < windowWidth){
                    pageWidth = xScroll;
                } else {
                    pageWidth = windowWidth;
                }
                return [pageWidth, pageHeight];
            }



$(document).ready(function(){
    init();
    $("#filtro_prov").on('change', function(){
    var codprov= $("#filtro_prov").val();
        $.ajax({
            dataType: 'json',
            url: BASE_URL + 'alumnos/getLocalidades',
            type: 'POST',
            data: { idprovincia : codprov},
            cache: false,
            success:function(respuesta){
                var i = 0;
                $('select[name="filtro_localidad"]').empty();
                $('select[name="filtro_localidad"]').append("<option value=\"-1\">Todos</option>");
                for(i = 0; i < respuesta.length; i++){
                    $('#filtro_loc').append("<option value=\""+respuesta[i]["id"]+"\">"+respuesta[i]["nombre"]+"</option>");
                }                
                $('select[name="filtro_localidad"]').trigger("chosen:updated");
            }
        });
    });
});




    window.addEventListener("load", function() {
        jQuery("body").append("<div id=\"flash\"></div>");
        var canvas = document.getElementById("canvas");
        if (canvas){
            if (canvas.getContext) {
                    ctx = document.getElementById("canvas").getContext("2d");
                    ctx.clearRect(0, 0, 320, 240);
                    var img = new Image();
                    img.onload = function() {
                        ctx.drawImage(img, 129, 89);
                    };
                    image = ctx.getImageData(0, 0, 320, 240);
            }
            var pageSize = getPageSize();
            jQuery("#flash").css({ height: pageSize[1] + "px" });
        }
    }, false);

    window.addEventListener("resize", function() {
        var pageSize = getPageSize();
        jQuery("#flash").css({ height: pageSize[1] + "px" });
    }, false);

//modificacion franco ticket 5053->

function listar(){
    oTable.fnDraw();
}

function exportar_informe(tipo_reporte){
        var iSortCol_0 = oTable.fnSettings().aaSorting[0][0];
        var sSortDir_0 = oTable.fnSettings().aaSorting[0][1];
        var iDisplayLength = oTable.fnSettings()._iDisplayLength;
        var iDisplayStart = oTable.fnSettings()._iDisplayStart;
        var sSearch = $("#academicoAlumnos_filter").find("input[type=search]").val();
        //var tipo_contacto = $("[name=filtro_tipo_contacto]").val();
        var localidad = $("[name=filtro_localidad]").val();
        var como_nos_conocio = $("[name=filtro_como_nos_conocio]").val();
        var estado = $("[name=filtro_estado]").val();
        var talle = $("[name=filtro_talle]").val();
        var fecha_alta_desde = $("[name=filtro_fecha_alta_desde]").val();
        var fecha_alta_hasta = $("[name=filtro_fecha_alta_hasta]").val();
        $("[name=frm_exportar]").find("[name=iSortCol_0]").val(iSortCol_0);
        $("[name=frm_exportar]").find("[name=sSortDir_0]").val(sSortDir_0);
        $("[name=frm_exportar]").find("[name=iDisplayLength]").val(iDisplayLength);
        $("[name=frm_exportar]").find("[name=iDisplayStart]").val(iDisplayStart);
        $("[name=frm_exportar]").find("[name=sSearch]").val(sSearch);
        //$("[name=frm_exportar]").find("[name=tipo_contacto]").val(tipo_contacto);
        $("[name=frm_exportar]").find("[name=localidad]").val(localidad); //era medio
        $("[name=frm_exportar]").find("[name=como_nos_conocio]").val(como_nos_conocio); //era curso_interes
        $("[name=frm_exportar]").find("[name=estado]").val(estado); //era turno
        $("[name=frm_exportar]").find("[name=talle]").val(talle); //era es_alumno
        $("[name=frm_exportar]").find("[name=fecha_alta_desde]").val(fecha_alta_desde);
        $("[name=frm_exportar]").find("[name=fecha_alta_hasta]").val(fecha_alta_hasta);
        $("[name=frm_exportar]").find("[name=tipo_reporte]").val(tipo_reporte);
        $("[name=frm_exportar]").submit();
    }
    
    
//<-modificacion franco ticket 5053
