var oTableCertificacionesIGA = '';
var oTableCertificacionesUCEL;
var columnas = columns;
var lang = BASE_LANG ;
var accionesPenAprobar;
var accionesFinalizado;
var oTablePendienteCertificar;

var html_filters_ucel = '';

$('body').ready(function(){
   init(); 
});

function init(){
    html_filters_ucel = $("[name=container_menu_filters_temp_ucel]").html();
    
    indices_rows = {
        cod_plan_academico: 0,
        matricula: 1,
        nombre_apellido: 2,
        documento: 3,
        nombre_curso: 4,
        comision: 5,
        fecha_desde: 6,
        fecha_hasta: 7,
        titulo: 8,
        fecha_pedido: 9,
        estado: 10,
        detalles: 11,
        usuario_que_genero_el_pedido: 12,
        entregado: 13,
        plan: 14
    };
    
    oTableCertificacionesIGA = $('#tableCertificacionesIGA').dataTable({
        serverSide: true,
        bServerSide: true,
        aaSorting: [[1, "desc"]],
        sAjaxSource: BASE_URL + "certificados/listar_certificaciones",
        sServerMethod: "POST",
        "aoColumns": [{"bSortable": false}, null, null, null, null, {"bSortable": false}, {"bSortable": false}, {"bSortable": false}, null, 
            {"bSortable": false}, {"bSortable": false}, null, null, {"bSortable": false}
        ],
        fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            //console.log(aData);            
            $('td:eq(10)', nRow).html(devolverEstado(aData[indices_rows.estado]));
            
            var value = '{"cod_matricula_periodo":"' + aData[indices_rows.cod_plan_academico] + '","cod_certificante":"1"}';
            $('td:eq(0)', nRow).html("<input value='" + value + "' class='form-control' type='checkbox' name='certificados[]' style='width: 15px;'>");
            
            var selected = aData[indices_rows.entregado] == 1 && aData[indices_rows.estado] == 'finalizado' ? 'checked="true"' : '';
            
            var disabled = aData[indices_rows.estado] != 'finalizado' ? 'disabled="true"' : '';
            $('td:eq(13)', nRow).html('<input name="chk_entregado" type="checkbox" class="form-control" value="' + aData[indices_rows.cod_plan_academico] + '" onclick="registrarEntregado(this, 1)" ' + selected + ' style="width: 15px;" ' + disabled + '>');
            
            var _html = '<div class="test">';
            _html += '<a href="#" onclick="modificarAprobacionIndividual(' + aData[indices_rows.cod_plan_academico] + ', 1);" class="btn btn-minier btn-info">';
            _html += '<i class="icon-only icon-edit">';                
            _html += '</i>';
            _html += '</a>';
            _html += '</div>';
            _html += '<div class="texto">';
            _html += aData[indices_rows.fecha_hasta] == null ? "" : aData[indices_rows.fecha_hasta];
            _html += '</div>';
            
            $('td:eq(7)', nRow).html(_html);
            
            return nRow;
        },
        fnServerData: function(sSource, aData, fnCallback) {
            var estado = $("[name=filtro_estado]").val();
            var comision = $("[name=filtro_comisiones_iga]").val();
            var curso = $("[name=filtro_cursos_iga]").val();
            aData.push({name: "estado", value: estado});
            aData.push({name: "certificante", value: 1});
            aData.push({name: "curso", value: curso});
            aData.push({name: "comision", value: comision});
            $.ajax({
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": aData,
                "async": true,
                "success": fnCallback
            });
        }
    });        
    $("#tableCertificacionesIGA_filter").find("label").addClass("input-icon input-icon-right");
    $("#tableCertificacionesIGA_filter").find("label").append('<i name="icon_filters" class="icon-caret-down grey" name="table_filters" style="margin-right: 7px; cursor: pointer"></i>');
    $("#tableCertificacionesIGA_filter").append($("[name=container_menu_filters_temp]").html());
    $("[name=container_menu_filters_temp]").remove();
    $("[name=icon_filters]").on("click", function() {
        $("[name=contenedorPrincipal]").toggle();
        $("[name=div_table_filters]").toggle(300);
            return false;
    });
    $("#tableCertificacionesIGA_filter").find(".select_chosen").chosen();
    $("[name=div_table_filters]").hide();
    $("[name=contenedorPrincipal]").on("mousedown", function() {
        $("[name=contenedorPrincipal]").hide();
        $("[name=div_table_filters]").hide(300);
        $("[name=div_table_filters_ucel]").hide(300);            
    });
        
    $('table').on('mouseover','tr',function(){
        $(this).find('.test').show();
    });

    $('table').on('mouseout','tr',function(){
        $(this).find('.test').hide();
    });
}

function devolverEstado(id){
    var clase='';
    if (id == 'en_proceso'){
        id = 'pendiente_impresion';
    }
    switch(id){        
        case 'cancelado':
            clase='default';
            break;
        
        case 'finalizado':
            clase='success';
            break;
        
        case 'en_proceso':
            clase='info';
            break;
       
        default:
            clase='warning';
            break;
    }    
    return '<span class="label label-' + clase + ' arrowed-in arrowed-in-right">' + lang[id] + '</span>';
}

function registrarRecibido(element, certificante){
    var cod_matricula_periodo = $(element).val();
    var estado = $(element).prop("checked") ? 1 : 0;
    $.ajax({
        url: BASE_URL + 'certificados/cambiar_estado_recibido',
        type: 'POST',
        dataType: 'json',
        data: {
            cod_matricula_periodo: cod_matricula_periodo,
            cod_certificante: certificante,
            estado: estado
        },
        success: function(_json){
            if (_json.error){
                gritter(_json.msg, false, '');
            } else {
                var chk_entregado = $(element).closest("tr").find("[name=chk_entregado]");
                if (estado == 1){
                    $(chk_entregado).prop("disabled", false);
                } else {
                    $(chk_entregado).prop("disabled", true);
                }
            }
        }
    });
}

function registrarEntregado(element, certificante){
    var cod_matricula_periodo = $(element).val();
    var estado = $(element).prop("checked") ? 1 : 0;
    $.ajax({
        url: BASE_URL + 'certificados/cambiar_estado_entregado',
        type: 'POST',
        dataType: 'json',
        data: {
            cod_matricula_periodo: cod_matricula_periodo,
            cod_certificante: certificante,
            estado: estado
        },
        success: function(_json){
            if (_json.error){
                gritter(_json.msg, false, '');
            }
        }
    });
}

function listar_certificacion_iga(){
    $("[name=contenedorPrincipal]").hide();
    $("[name=div_table_filters]").hide(300);
    $("[name=div_table_filters_ucel]").hide(300);
    oTableCertificacionesIGA.fnDraw();
}


function habilitarCertificadosCancelados(certificaciones){
    var dataPOST;
    var chk = new Array();
    if (certificaciones){
        dataPOST = oTableCertificacionesUCEL.$('input[type="checkbox"]').serialize();
        chk = $("#tableCertificacoinesUCEL").find("[name='certificados[]']:checked");
    } else {
        dataPOST = oTableCertificacionesIGA.$('input[type="checkbox"]').serialize();
        dataPOST += '&certificacion=IGA';
        chk = $("#tableCertificacionesIGA").find("[name='certificados[]']:checked");        
    }
    if(chk.length == 0){
        gritter(lang.seleccione_certificado);
    } else {
        $.ajax({
            url: BASE_URL + "certificados/habilitarCertificadosCancelados",
            type: "POST",
            data: dataPOST,
            dataType: "json",
            cache: false,
            success: function(_json){
                if (_json.codigo == 1){
                    if (certificaciones){
                        listar_certificacion_ucel();
                    } else {
                        listar_certificacion_iga();
                    }
                } else {
                    gritter(_json.respuesta);
                }
            }
        });
    }
}

function aprobar(certificaciones){
    var dataPOST = '';
    var chk = new Array();
    if (certificaciones){
        dataPOST = oTableCertificacionesUCEL.$('input[type="checkbox"]').serialize();
        chk = $("#tableCertificacoinesUCEL").find("[name='certificados[]']:checked");
    } else {
        dataPOST = oTableCertificacionesIGA.$('input[type="checkbox"]').serialize();
        chk = $("#tableCertificacionesIGA").find("[name='certificados[]']:checked");
    }
    if(chk.length == 0){
        gritter(lang.seleccione_certificado);
    } else {
        $.ajax({
            url: BASE_URL + "certificados/aprobarCertificados",
            type: "POST",
            data: dataPOST,
            dataType:"JSON",
            cache:false,
            success:function(respuesta){
                if(respuesta.codigo == 1){
                    gritter(lang.validacion_ok,true);
                    if (certificaciones){
                        listar_certificacion_ucel();
                    } else {
                        listar_certificacion_iga();
                    }
                } else {
                    gritter(respuesta.msg);
                }
            }
        });
    }
}

function modificarAprobacionIndividual(cod_matricula_periodo, cod_certificante){
    var certificacion = cod_certificante == 1 ? 'IGA' : 'UCEL';
    $.ajax({
        url: BASE_URL + "certificados/frm_cambiar_detalles",
        type: "POST",
        data: {
            'certificados[]': '{"cod_matricula_periodo":"' + cod_matricula_periodo + '","cod_certificante":"' + cod_certificante + '"}',
            certificacion: certificacion
        },
        dataType: "",
        cache: false,
        success:function(respuesta){
            $.fancybox.open(respuesta,{
                autoSize: false,
                width: '50%',
                height: 'auto',
                scrolling: 'auto',
                padding: 0,
                openEffect: 'none',
                closeEffect: 'none',
                helpers:  {
                    overlay : null
                }
            });
        }
    });
}

function modificarAprobacion(certificaciones){
    var dataPOST;
    var chk = new Array();
    if (certificaciones){
        dataPOST = oTableCertificacionesUCEL.$('input[type="checkbox"]').serialize();
        chk = $("#tableCertificacoinesUCEL").find("[name='certificados[]']:checked");
    } else {
        dataPOST = oTableCertificacionesIGA.$('input[type="checkbox"]').serialize();
        dataPOST += '&certificacion=IGA';
        chk = $("#tableCertificacionesIGA").find("[name='certificados[]']:checked");
    }
    //console.log(chk);
     if(chk.length == 0){
         gritter(lang.seleccione_certificado);
     } else {
        $.ajax({
            url: BASE_URL + "certificados/frm_cambiar_detalles",
            type: "POST",
            data: dataPOST,
            dataType: "",
            cache: false,
            success:function(respuesta){
                $.fancybox.open(respuesta,{
                    autoSize: false,
                    width: '50%',
                    height: 'auto',
                    scrolling: 'auto',
                    padding: 0,
                    openEffect: 'none',
                    closeEffect: 'none',
                    helpers:  {
                        overlay : null
                    }
                });
            }
        });        
    }
}

function cancelarCertificados(certificaciones){
    var dataPOST = '';
    var chk = new Array();
    if (certificaciones){
        dataPOST = oTableCertificacionesUCEL.$('input[type="checkbox"]').serialize();
        chk = $("#tableCertificacoinesUCEL").find("[name='certificados[]']:checked");
    } else {
        dataPOST = oTableCertificacionesIGA.$('input[type="checkbox"]').serialize();
        chk = $("#tableCertificacionesIGA").find("[name='certificados[]']:checked");
    }
    if(chk.length == 0){
        gritter(lang.seleccione_certificado);
    } else {
        $.ajax({
            url: BASE_URL + "certificados/cancelarCertificados",
            type: "POST",
            data: dataPOST,
            dataType:"JSON",
            cache:false,
            success:function(respuesta){
                if(respuesta.codigo==1){
                    gritter(lang.validacion_ok,true);
                    if (certificaciones){
                        listar_certificacion_ucel();
                    } else {
                        listar_certificacion_iga();
                    }
                } else {
                    gritter(respuesta.respuesta);
                }
            }
        });
    }
}
function revertirCertificados(certificaciones){
    var dataPOST = '';
    var chk = new Array();
    if (certificaciones){
        dataPOST = oTableCertificacionesUCEL.$('input[type="checkbox"]').serialize();
        chk = $("#tableCertificacoinesUCEL").find("[name='certificados[]']:checked");
    } else {
        dataPOST = oTableCertificacionesIGA.$('input[type="checkbox"]').serialize();
        chk = $("#tableCertificacionesIGA").find("[name='certificados[]']:checked");
    }
    if(chk.length == 0){
        gritter(lang.seleccione_certificado);
    } else {
        $.ajax({
            url: BASE_URL + "certificados/revertirCertificados",
            type: "POST",
            data: dataPOST,
            dataType:"JSON",
            cache:false,
            success:function(respuesta){
                if(respuesta.codigo==1){
                    gritter(lang.validacion_ok,true);
                    if (certificaciones){
                        listar_certificacion_ucel();
                    } else {
                        listar_certificacion_iga();
                    }
                } else {
                    gritter(respuesta.respuesta);
                }
            }
        });
    }
}

function listarCertificacionesUCEL(){
    indices_rows = {
        cod_plan_academico: 0,
        matricula: 1,
        nombre_apellido: 2,
        documento: 3,
        nombre_curso: 4,
        comision: 5,
        fecha_desde: 6,
        fecha_hasta: 7,
        titulo: 8,
        fecha_pedido: 9,
        estado: 10,
        usuario_que_genero_el_pedido: 11,
        entregado: 12
    };
    
    if (! $.fn.DataTable.isDataTable('#tableCertificacoinesUCEL')){
        oTableCertificacionesUCEL = $('#tableCertificacoinesUCEL').dataTable({
            serverSide: true,
            bServerSide: true,
            aaSorting: [[1, "desc"]],
            sAjaxSource: BASE_URL + "certificados/listar_certificaciones",
            sServerMethod: "POST",
            "aoColumns": [{"bSortable": false}, null, null, null, null, {"bSortable": false}, {"bSortable": false}, {"bSortable": false}, null, 
                {"bSortable": false}, {"bSortable": false}, null, {"bSortable": false}
            ],
            fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                //console.log(aData);
                
                $('td:eq('+indices_rows.estado+')', nRow).html(devolverEstado(aData[indices_rows.estado]));
                
                var value = '{"cod_matricula_periodo":"' + aData[indices_rows.cod_plan_academico] + '","cod_certificante":"2"}';
                $('td:eq('+indices_rows.cod_plan_academico+')', nRow).html("<input value='" + value + "' class='form-control' type='checkbox' name='certificados[]' style='width: 15px;' " + disabled + ">");
                
                var selected = aData[indices_rows.entregado] == 1 ? 'checked="true"' : '';
                var disabled = aData[indices_rows.estado] != 'finalizado' ? 'disabled="true"' : '';
                $('td:eq('+indices_rows.entregado+')', nRow).html('<input name="chk_entregado" type="checkbox" class="form-control" value="' + aData[indices_rows.cod_plan_academico] + '" onclick="registrarEntregado(this, 2)" ' + selected + ' style="width: 15px;" ' + disabled + '>');
                
                var _html = '<div class="test">';
                _html += '<a href="#" onclick="modificarAprobacionIndividual(' + aData[indices_rows.cod_plan_academico] + ', 2);" class="btn btn-minier btn-info">';
                _html += '<i class="icon-only icon-edit">';                
                _html += '</i>';
                _html += '</a>';
                _html += '</div>';
                _html += '<div class="texto">';
                _html += aData[indices_rows.fecha_hasta] == null ? '' : aData[indices_rows.fecha_hasta];
                _html += '</div>';
                $('td:eq('+indices_rows.fecha_hasta+')', nRow).html(_html);
                
                return nRow;                
            },
            fnServerData: function(sSource, aData, fnCallback) {
                var estado = $("[name=filtro_estado_ucel]").val();
                var comision = $("[name=filtro_comisiones_ucel]").val();
                var curso = $("[name=filtro_cursos_ucel]").val();
                aData.push({name: "estado", value: estado});
                aData.push({name: "certificante", value: 2});
                aData.push({name: "curso", value: curso});
                aData.push({name: "comision", value: comision});
                
                $.ajax({
                    "dataType": 'json',
                    "type": "POST",
                    "url": sSource,
                    "data": aData,
                    "async": true,
                    "success": fnCallback
                });
            }
        });
        $("#tableCertificacoinesUCEL_filter").find("label").addClass("input-icon input-icon-right");
        $("#tableCertificacoinesUCEL_filter").find("label").append('<i name="icon_filters" class="icon-caret-down grey" name="table_filters" style="margin-right: 7px; cursor: pointer"></i>');
        $("#tableCertificacoinesUCEL_filter").append($("[name=container_menu_filters_temp_ucel]").html());
        $("[name=container_menu_filters_temp_ucel]").remove();
        $("#tableCertificacoinesUCEL_filter").find(".select_chosen").chosen();
        $("#tableCertificacoinesUCEL_filter").find(".chosen-container").css({"width": "302px"});
        $("[name=icon_filters]").on("click", function(){
            $("[name=contenedorPrincipal]").toggle();
            $("[name=div_table_filters_ucel]").toggle(300);
                return false;
        });
        
    }
}

function listar_certificacion_ucel(){
    $("[name=contenedorPrincipal]").hide();
    $("[name=div_table_filters]").hide(300);
    $("[name=div_table_filters_ucel]").hide(300);
    oTableCertificacionesUCEL.fnDraw();
}

function filtro_comisiones_iga_change(){
    var comision = $("[name=filtro_comisiones_iga]").val();
    $("[name=filtro_cursos_iga]").prop("disabled", comision != -1);
    $("[name=filtro_cursos_iga]").trigger("chosen:updated");
}

function filtro_comisiones_ucel_change(){
    var comision = $("[name=filtro_comisiones_ucel]").val();
    $("[name=filtro_cursos_ucel]").prop("disabled", comision != -1);
    $("[name=filtro_cursos_ucel]").trigger("chosen:updated");
}
