var langFRMAlumnos = langFrm;
var oTableResponsable1 = '';
var responsableEnEdiccion = {responsable: {}, telefono: []};
var nAutoincremen = 0;
var oTableTel = '';
var nTelAlumnos = 0;
var oTableListarResponsables = '';
var respoSeleccionadosListar = [];
var nResponsables = 0;
var necesitaResponsable = 1;
var responsablePasarRazon = [];
var array_codigo = [];
var objResponsable = '';

function descartar_relacion_aspirante() {
    $("[name=codigoAspirante]").val("");
    $("[name=descartar_aspirante]").hide();
    $("[name=aspirantes_encontrados]").show();
}

function cargar_desde_aspirantes(codigo, element) {
    $.ajax({
        url: BASE_URL + 'aspirantes/get_aspirantes',
        type: 'POST',
        dataType: 'json',
        data: {
            codigo: codigo
        },
        success: function(_json) {
            $("[name=aspirantes_encontrados]").hide();
            $("[name=descartar_aspirante]").show();
            if (_json.data && _json.data.aspirantes && _json.data.aspirantes.length > 0) {
                var aspirante = _json.data.aspirantes[0];
                $("[name=tipoDniAlumno]").val(aspirante.tipo);
                $("[name=tipoDniAlumno]").trigger("chosen:updated");
                setMascaraIdentificacion($('select[name="tipoDniAlumno"]').val(), 'input[name="documento"]');
                $("[name=documento]").val(aspirante.documento);
                $("[name=documento]").prop("readonly", false);
                $("[name=fechanaci]").val(aspirante._fechanaci);
                $("[name=calle_alumno]").val(aspirante.calle);
                $("[name=calle_num_alumno]").val(aspirante.calle_numero);
                $("[name=complemento_alumno]").val(aspirante.calle_complemento);
                $("[name=barrio]").val(aspirante.barrio);
                $("[name=codpost]").val(aspirante.codpost);
                $("[name=email_alumno]").val(aspirante.email);
                $("[name=telefono_alumno]").val(aspirante.telefono);
                $("[name=comonosconocio]").val(aspirante.comonosconocio);
                $("[name=comonosconocio]").trigger("chosen:updated");
                $("[name=domiciProvincia]").val(aspirante.provincia_id);
                $("[name=domiciProvincia]").trigger("chosen:updated");
                $('[name=domiciLocalidad]').find("option").remove();
                $('[name=domiciLocalidad]').append("<option>(" + langFRMAlumnos.recuperando + "...)</option>");
                $('[name=domiciLocalidad]').attr("disabled", true);
                $('[name=domiciLocalidad]').trigger("chosen:updated");
                $.ajax({
                    url: 'aspirantes/getlocalidades',
                    type: 'POST',
                    dataType: 'json',
                    async: false,
                    data: {
                        provincia: aspirante.provincia_id
                    },
                    success: function(_json) {
                        $('[name=domiciLocalidad]').find("option").remove();
                        $(_json).each(function(key, value) {
                            $('[name=domiciLocalidad]').append('<option value=' + value.id + '>' + value.nombre + '</option>');
                        });
                        $('[name=domiciLocalidad]').val(aspirante.cod_localidad);
                        $('[name=domiciLocalidad]').attr("disabled", false);
                        $('[name=domiciLocalidad]').trigger("chosen:updated");
                    }
                });
                $("[name=codigoAspirante]").val(aspirante.codigo);
                var detalle = $(element).html();
                $("[name=detalle_aspirante_seleccionado]").html(detalle);
            }
        }
    });
}

function buscarAspirante() {
    $("[name=descartar_aspirante]").hide();
    $("[name=aspirantes_encontrados]").show();
    var nombre = trim($("[name=nombre]").val());
    var apellido = trim($("[name=apellido]").val());
    if (nombre != '' && apellido != '') {
        $.ajax({
            url: BASE_URL + "aspirantes/get_aspirantes",
            type: 'POST',
            dataType: 'json',
            data: {
                nombre: nombre,
                apellido: apellido
            },
            success: function(_json) {
                $("[name=aspirantes_encontrados]").html("");
                if (_json.data && _json.data.aspirantes && _json.data.aspirantes.length > 0) {
                    var _html = '<i class="icon-leaf green"></i>&nbsp;&nbsp;';
                    _html += "<span>" + langFRMAlumnos.sugerencia_de_aspirante + "</span>&nbsp;&nbsp;&nbsp;";
                    $.each(_json.data.aspirantes, function(key, aspirante) {
                        _html += '<span style="cursor: pointer;" onclick="cargar_desde_aspirantes(' + aspirante.codigo + ', this)">';
                        _html += '<b>' + aspirante._nombre + " " + aspirante._apellido + "</b>";
                        _html += ' (';
                        if (trim(aspirante.email) != '') {
                            _html += aspirante.email;
                        } else if (trim(aspirante.calle)) {
                            _html += aspirante.calle + " " + aspirante.calle_numero;
                        } else {
                            _html += aspirante._fechaalta;
                        }
                        _html += ')';
                        _html += '</span>&nbsp;&nbsp;&nbsp;&nbsp;';
                    });
                    $("[name=aspirantes_encontrados]").html(_html);
                }
            }
        });
    }
}

function alumnoEsMayorEdad(mayoria, fechaNacicmiento) {
    var dateA = moment();
    var dateB = moment(fechaNacicmiento);
    var edad = dateA.diff(dateB, 'years');
    if (edad >= mayoria) {
        return true;
    } else {
        return false;
    }
}

function esMayor() {
    var ahora = moment();
    var añosString = moment().fromNow(true);
    var year = añosString.split(' ');
    if (edadAlumno >= mayoriaEdad) {
        necesitaResponsable = 0;
    } else {
        necesitaResponsable = 1;
    }
}

function vaciarArrayRespSeleccionados() {
    respoSeleccionadosListar = [];
}

function relacionarResponsablesListar() {
    var respYaAsignados = oTableResponsable1.$('input[name="responsables[]"]').serializeJSON();
    for (var i in respoSeleccionadosListar) {
        var seleccionado = respoSeleccionadosListar[i];
        var relacionar = true;
        for (var a in respYaAsignados.responsables) {
            var respAsignado = respYaAsignados.responsables[a];
            var porAsignar = i;
            if (respAsignado == porAsignar) {
                relacionar = false;
            }
        }
        if (relacionar != false) {
            var obj = {
                responsable: {
                    codigo: i,
                    cod_razon_social: seleccionado.cod_razon_social,
                    condicion: seleccionado.condicion,
                    nombre: seleccionado.nombre,
                    tipo_doc: seleccionado.nombre_identificacion,
                    email: seleccionado.email,
                    direccion: seleccionado.direccion,
                    baja: 0
                }
            };
            addResponsable(obj);
        }
    }
    vaciarArrayRespSeleccionados();
}

function selecionarListarResponsable(element) {
    var cod_resp = $(element).val();
    if ($(element).is(':checked')) {
        var tr = oTableListarResponsables.$(element).closest('tr');
        var myData = oTableListarResponsables.row(tr).data();
        respoSeleccionadosListar[cod_resp] = {
            'cod_razon_social': myData[7],
            'nombre': myData[1],
            'nombre_identificacion': myData[2],
            'email': myData[3],
            "direccion": myData[4],
            "condicion": myData[8]
        };
        responsablePasarRazon = {
            'cod_razon_social': myData[7],
            'nombre': myData[1],
            'nombre_identificacion': myData[2],
            'email': myData[3],
            "direccion": myData[4],
            "condicion": myData[8]
        };
    } else {
        delete  respoSeleccionadosListar[cod_resp];
    }
}

function frmListarResponsable() {
    var columns = [{"sTitle": "C\u00f3digo", "sName": 0, "aTargets": [0], "bVisible": true, "bSearchable": true, "bSortable": true, "sClass": "", "mRender": null, "sWidth": null},
        {"sTitle": "Nombre y Apellido", "sName": 1, "aTargets": [1], "bVisible": true, "bSearchable": true, "bSortable": true, "sClass": "", "mRender": null, "sWidth": null},
        {"sTitle": "Tipo Doc.", "sName": 2, "aTargets": [2], "bVisible": true, "bSearchable": true, "bSortable": true, "sClass": "", "mRender": null, "sWidth": null},
        {"sTitle": "Email", "sName": 3, "aTargets": [3], "bVisible": true, "bSearchable": true, "bSortable": true, "sClass": "", "mRender": null, "sWidth": null},
        {"sTitle": "Direccion", "sName": 4, "aTargets": [4], "bVisible": true, "bSearchable": true, "bSortable": true, "sClass": "", "mRender": null, "sWidth": null},
        {"sTitle": "Estado", "sName": 5, "aTargets": [5], "bVisible": false, "bSearchable": true, "bSortable": false, "sClass": "", "mRender": null, "sWidth": null},
        {"sTitle": "Estado", "sName": 6, "aTargets": [6], "bVisible": true, "bSearchable": true, "bSortable": false, "sClass": "", "mRender": null, "sWidth": null},
        {"sTitle": "cod_razon_social", "sName": 7, "aTargets": [7], "bVisible": false, "bSearchable": false, "bSortable": false, "sClass": "", "mRender": null, "sWidth": null},
        {"sTitle": "Condicion_social", "sName": 8, "aTargets": [8], "bVisible": false, "bSearchable": false, "bSortable": false, "sClass": "", "mRender": null, "sWidth": null}];

    if (!$.fn.DataTable.isDataTable('#tabla_listar_responsables')) {
        oTableListarResponsables = $('#tabla_listar_responsables').DataTable({
            'aoColumnDefs': columns,
            "sAjaxSource": BASE_URL + 'responsables/listar',
            "sServerMethod": "POST",
            "pageLength": 5,
            "lengthChange": false,
            "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                var checked = '';
                var disabled = '';
                if (respoSeleccionadosListar[aData[0]]) {
                    checked = 'checked';
                }
                if (aData[6] == 1) {
                    disabled = 'disabled';
                }
                $('td:eq(0)', nRow).html('<input type="checkbox" onclick="selecionarListarResponsable(this)" name="responsables_relacionar[]" value="' + aData[0] + '" ' + checked + ' ' + disabled + '>');
                $('td:eq(5)', nRow).html(aData[6] == 1 ? langFRMAlumnos.INHABILITADO : langFRMAlumnos.HABILITADO);
                return nRow;
            }
        });
    } else {
        oTableListarResponsables.draw();
    }
    $('#frm_listar_responsables').modal();
}

function getResponsable(codigo) {
    var retorno = '';
    $.ajax({
        url: BASE_URL + "responsables/getResponsable",
        type: "POST",
        data: {'codigo': codigo},
        dataType: "",
        cache: false,
        async: false,
        success: function(respuesta) {
            var respuestaJSON = JSON.parse(respuesta);
            retorno = respuestaJSON;
        }
    });
    return retorno;
}
;

function resetearFormResponsable() {
    $('input[name="nombre_responsable"]').val('');
    $('input[name="email_responsable"]').val('');
    $('input[name="apellido_responsable"]').val('');
    $('input[name="fecha_nacimiento_responsable"]').val('');
    $('input[name="domicilio_responsable"]').val('');
    $('input[name="barrio_responsable"]').val('');
    $('input[name="numero_domicilio_responsable"]').val('');
    $('input[name="calle_complemento_responsable"]').val('');
    $('input[name="codigo_postal_responsable"]').val('');
    $('input[name="codigo_responsable"]').val('-1');
    $('input[name="codigo_postal_responsable"]').val('');
    $('input[name="numero_documento_responsable"]').val('');
    oTableTelResp.fnClearTable();
    $('input[name="telefono_default_responsable"]').intlTelInput("setNumber", '');
}

function getCondicionesSocialesResponsable() {
    $("[name=condicion_fiscal_responsable]").find("option").remove();
    $("[name=condicion_fiscal_responsable]").append("<option value='-1'>(" + langFRMAlumnos.RECUPERANDO + "...)</option>");
    $("[name=condicion_fiscal_responsable]").prop("disabled", true);
    $("[name=condicion_fiscal_responsable]").trigger("chosen:updated");
    var tipo_identificador = $("[name='tipo_documento_responsable']").val();
    setMascaraIdentificacion(tipo_identificador, $('input[name="numero_documento_responsable"]'));
    $.ajax({
        url: BASE_URL + 'razonessociales/getCondicionesSociales',
        type: 'POST',
        dataType: 'json',
        data: {
            tipo_identificador: tipo_identificador
        },
        success: function(_json) {
            if (_json.length > 0) {
                $("[name=condicion_fiscal_responsable]").find("option").remove();
                $.each(_json, function(key, value) {
                    $("[name=condicion_fiscal_responsable]").append("<option value=" + value.codigo + ">" + value.condicion + "</option>");
                });
                $("[name=condicion_fiscal_responsable]").prop("disabled", false);
                $("[name=condicion_fiscal_responsable]").trigger("chosen:updated");
            } else {
                $("[name=condicion_fiscal_responsable]").find("option").remove();
                $("[name=condicion_fiscal_responsable]").append("<option value=''>(" + 'sin registros' + ")</option>");
                $("[name=condicion_fiscal_responsable]").trigger("chosen:updated");
            }
            $('input[name="numero_documento_responsable"]').prop('disabled', false);
        }
    });
}

function deshabilitarFormResponsable(valor) {
    var input = $('select[name="tipo_documento_responsable"]').closest('.form-group').find('input');
    $('#form_responsable input,select,buttton').not('select[name="tipo_documento_responsable"]').not(input).prop('disabled', valor);
    $('#form_responsable select').trigger('chosen:updated');
}

function addResponsable(obj, i) {
    objResponsable = obj;
    var x = [];
    x.telefono = [];
    var tooltipDefault = getTelDefaulResponsable(x);
    var key = nResponsables;
    var tel = [];
    var telefono = '';
    var selectTipo = function(key) {
        var slt = '?';
        $(tipo_identificacion).each(function(k, v) {
            var selected = '';
            if (v.codigo == key) {
                slt = v.nombre;
            }
        });
        return slt;
    };

    var condicion = function(key) {
        var slt = '';
        $(condiciones).each(function(k, v) {
            if (v.codigo == key) {
                slt += v.condicion;
            }
        });
        return slt;
    };

    var relacion = function(key) {
        var slt = '<select class="form-control" name="responsable_relacion[' + key + ']">';
        for (var id in relacion_alumno) {
            var selected = obj.responsable.relacion_alumno == id ? 'selected' : '';
            slt += '<option value="' + id + '" ' + selected + '>' + relacion_alumno[id] + '</option>';
        }
        slt += '</select>';
        return slt;
    };

    var codigo_razon_social = obj.responsable.cod_razon_social != '' ? obj.responsable.cod_razon_social : responsablePasarRazon.cod_razon_social;
  
    var respARRAY = [
        obj.responsable.codigo,
        obj.responsable.razon_social ? obj.responsable.razon_social : obj.responsable.nombre,
        obj.responsable.nombre_identificacion ? obj.responsable.nombre_identificacion: obj.responsable.tipo_doc, //selectTipo(key),
        obj.responsable.direccion,
        obj.responsable.email,
        relacion(key),
        '<button  class="eliminarResponsable btn btn-info  btn-xs"  value="' + obj.baja + '" data-row-obj="' + i + '">' + langFRMAlumnos.eliminar + '</button><input type="hidden" name="responsables[]" value="' + obj.responsable.codigo + '">',
        '<button type="button" class="boton_razon_responsable btn btn-info btn-xs" name="razon' + codigo_razon_social + '"value="" onclick="convertirRazonDeResponsable(' + codigo_razon_social + ')">Convertir Razon Social</button>'
    ];

    var condicion = obj.responsable.condicion != '' ? obj.responsable.condicion : responsablePasarRazon.condicion;
    var nombre_identificacion = obj.responsable.tipo_doc != '' ? obj.responsable.tipo_doc : responsablePasarRazon.nombre_identificacion;
    var razon_social = obj.responsable.nombre != '' ? obj.responsable.nombre : responsablePasarRazon.nombre;
    var direccion = obj.responsable.direccion != '' ? obj.responsable.direccion : responsablePasarRazon.direccion;
    var email = obj.responsable.email != '' ? obj.responsable.email : responsablePasarRazon.email;
    var campoDefault = '<input type="radio" value="" class="chk_razones_default" disabled>';
    var campoDefaultF = '<input type="radio" value="1" class="chk_razones_default_facturacion" name="razones[1][default_facturacion]">';
    var boton = '<button  value="0"  class="eliminarRazon btn btn-info  btn-xs">' + langFRMAlumnos.eliminar + '</button>';
    var nombre = condicion + '<input type="hidden" id="cod_razon" name="razones[1][codigo]" value = ' + codigo_razon_social + '>';
    var array_resp = [
        nombre,
        nombre_identificacion,
        razon_social,
        direccion,
        email,
        campoDefault,
        campoDefaultF,
        boton
    ];
    var json_responsable = JSON.stringify(array_resp);
    oTableResponsable1.fnAddData(respARRAY);
    $('button[name="razon' + codigo_razon_social + '"]').val(json_responsable);     
    oTableResponsable1.$('select').chosen({
        "width": '100%'
    });
    nResponsables++;
}

function convertirRazonDeResponsable(codigo) {
    if (inArray(codigo, array_codigo)) {
        gritter('Ya ah seleccionado la razon del responsable');
    } else {
        var array_responsable = JSON.parse($('button[name="razon' + codigo + '"]').val());
        oTableRazones.fnAddData(array_responsable);
        gritter('Razon social Relacionada', true);
    }
    array_codigo.push(codigo);
}

function inArray(needle, haystack) {
    var length = haystack.length;
    for (var i = 0; i < length; i++) {
        if (haystack[i] == needle)
            return true;
    }
    return false;
}

function listarResponsables() {
    oTableResponsable1.fnClearTable();
    for (var i in responsables) {
        if (responsables[i].responsable.baja != 1) {
            addResponsable(responsables[i], i);
        }
    }
}

function limpiarDocumento(documento) {
    var pattern = new RegExp("^([0-9]{1})$");
    var retorno = '';
    for (var i in documento) {
        if (pattern.test(documento[i])) {
            retorno += documento[i];
        }
    }
    return retorno;
}

function guardarResponsable() {
    var telefonosResponsables = {telefono: []};
    var serializeDeTelefonos = oTableTelResp.$('input,select').serializeJSON();
    if (serializeDeTelefonos.telefono) {
        var telefonosInvalidosEnListar = oTableTelResp.$('.inputError').serializeJSON();
        if (telefonosInvalidosEnListar.telefono) {
            $.gritter.add({
                text: langFRMAlumnos.tel_formato_invalido,
                sticky: false,
                time: '3000',
                class_name: 'gritter-error'
            });
            return false;
        }
        telefonosResponsables = serializeDeTelefonos;
    }
    var telDefaultRes = $('#form_responsable').find('.inputError').serializeArray();
    if (telDefaultRes.length > 0) {
        $.gritter.add({
            text: langFRMAlumnos.tel_default_invalido,
            sticky: false,
            time: '3000',
            class_name: 'gritter-error'
        });
        return false;
    }
    for (var i in telefonosResponsables.telefono) {
        var tel = telefonosResponsables.telefono[i];
        var telParsiado = limpiarTelefono(tel.tipo_telefono, tel.numero);
        telefonosResponsables.telefono[i].cod_area = telParsiado[0];
        telefonosResponsables.telefono[i].numero = telParsiado[1];
    }

    var responsable = $('#form_responsable').serializeJSON();
    var myResponsable = {};
    myResponsable.codigo = responsable.codigo_responsable;
    myResponsable.tipo_doc = responsable.tipo_documento_responsable;
    myResponsable.documento = limpiarDocumento(responsable.numero_documento_responsable);
    myResponsable.nombre = responsable.nombre_responsable;
    myResponsable.apellido = responsable.apellido_responsable;
    myResponsable.email = responsable.email_responsable;
    myResponsable.calle = responsable.domicilio_responsable;
    myResponsable.calle_numero = responsable.numero_domicilio_responsable;
    myResponsable.calle_complemento = responsable.calle_complemento_responsable;
    myResponsable.condicion = responsable.condicion_fiscal_responsable;
    myResponsable.baja = 0;
    myResponsable.barrio = responsable.barrio_responsable;
    myResponsable.cod_postal = responsable.codigo_postal_responsable;
    myResponsable.domiciProvincia_responsable = responsable.domiciProvincia_responsable;
    myResponsable.domiciLocalidad_responsable = responsable.domiciLocalidad_responsable;
    myResponsable.relacion_alumno = responsable.relacion_alumno_responsable;
    myResponsable.fecha_naci = responsable.fecha_nacimiento_responsable;
    myResponsable.telefonos = JSON.stringify(telefonosResponsables.telefono);
    $.ajax({
        url: BASE_URL + "responsables/guardarResponsable",
        type: "POST",
        data: myResponsable,
        dataType: "",
        cache: false,
        success: function(respuesta) {
            var respuestaJSON = JSON.parse(respuesta);
            if (respuestaJSON.codigo == 1) {
                var nuevoResponsable = getResponsable(respuestaJSON.cod_responsable);
                var row = {
                    responsable: {
                        'codigo': nuevoResponsable.cod_responsable,
                        'nombre': nuevoResponsable.razon_social,
                        'tipo_doc': nuevoResponsable.nombre_identificacion + ' ' + nuevoResponsable.documento,
                        'direccion': nuevoResponsable.direccion,
                        'email': nuevoResponsable.email,
                        'relacion_alumno': myResponsable.relacion_alumno,
                        'baja': nuevoResponsable.baja_responsable,
                        'cod_razon_social': respuestaJSON.razones_sociales_responsable.cod_razon_social,
                        'condicion': nuevoResponsable.nombre_condicion
                    }
                };
                addResponsable(row);
                resetearFormResponsable();
                $('#frm_responsable').modal('hide');
                var campoDefault = '<input type="radio" value="" class="chk_razones_default" disabled>';
                var campoDefaultF = '<input type="radio" value="1" class="chk_razones_default_facturacion" name="razones[1][default_facturacion]" >';
                var boton = '<button  value="0"  class="eliminarRazon btn btn-info  btn-xs">' + langFRMAlumnos.eliminar + '</button>';
                var nombre = respuestaJSON.razones_sociales_responsable.condicion + '<input type="hidden" id="cod_razon" name="razones[1][codigo]" value = ' + respuestaJSON.razones_sociales_responsable.cod_razon_social + '>';
                var responsable_array = [
                    nombre,
                    respuestaJSON.razones_sociales_responsable.documento,
                    respuestaJSON.razones_sociales_responsable.razon_social,
                    respuestaJSON.razones_sociales_responsable.direccion_calle,
                    respuestaJSON.razones_sociales_responsable.email,
                    campoDefault,
                    campoDefaultF,
                    boton,
                    respuestaJSON.razones_sociales_responsable.cod_razon_social
                ];
                var json_responsable = JSON.stringify(responsable_array);
                $('.boton_razon_responsable').val(json_responsable);
            } else {
                $.gritter.add({
                    text: respuestaJSON.respuesta,
                    sticky: false,
                    time: '3000',
                    class_name: 'gritter-error'
                });
            }
        }
    });
}

function deleteResponsable(row) {
    oTableResponsable1.fnDeleteRow(row);
}

function frmResponsable(cod_responsable) {
    if (cod_responsable) {
    } else {
        $('#form_responsable').find('input[name="codigo_responsable"]').val('-1');
    }
    $('#frm_responsable').modal();
}

function cargarResponsable() {
    var numIdentiLimpio = '';
    var pattern = new RegExp("^([0-9]{1})$");
    var numIdentificacion = $('input[name="numero_documento_responsable"]').val();
    var tipoIdentificacion = $('select[name="tipo_documento_responsable"]').val();
    for (var i in numIdentificacion) {
        if (pattern.test(numIdentificacion[i])) {
            numIdentiLimpio += numIdentificacion[i];
        }
    }
    $.ajax({
        url: BASE_URL + "responsables/getResponsable",
        type: "POST",
        data: {tipo_identificacion: tipoIdentificacion, numero_identificacion: numIdentiLimpio},
        dataType: "",
        cache: false,
        success: function(respuesta) {
            var respuestaJSON = JSON.parse(respuesta);
            responsableEnEdiccion = respuestaJSON;
            if (respuestaJSON.responsable.length != 0) {
                var form = responsableEnEdiccion.responsable;
                $('input[name="nombre_responsable"]').val(form.nombre);
                $('input[name="email_responsable"]').val(form.email);
                $('input[name="apellido_responsable"]').val(form.apellido);
                $('input[name="fecha_nacimiento_responsable"]').val(form.inicio_actividades);
                $('input[name="domicilio_responsable"]').val(form.direccion_calle);
                $('input[name="barrio_responsable"]').val('?');
                $('input[name="numero_domicilio_responsable"]').val(form.direccion_numero);
                $('input[name="calle_complemento_responsable"]').val(form.direccion_complemento);
                $('input[name="codigo_postal_responsable"]').val(form.codigo_postal);
                $('input[name="codigo_responsable"]').val(form.codigo);
                $('input[name="codigo_postal_responsable"]').val(form.codigo_postal);
                mostarDefaultEnForm();
            }
            deshabilitarFormResponsable(false);
        }
    });
}

//TELEFONOS RESPONABLE
function cerrarFrmTelResponsables() {
    var validacion = validacionIngresoTelefonos(oTableTelResp);
    if (validacion.codigo) {
        $('#telefonosResponsable').modal('hide');
    } else {
        $.gritter.add({
            text: validacion.respuesta,
            sticky: false,
            time: '3000',
            class_name: 'gritter-error'
        });
    }
}

function setTelefonoDefaultResponsableEnArray() {
    var tel = $('#frm_responsable input[name="telefono_default_responsable"]').val();
    var tipo = $('input[name="telefono_default_responsable"]').closest('.row').find('select').val();
    var empresa_tel = $('#id_empresa_telefono_responsable').val();
    var part = limpiarTelefono(tipo, tel);
    var radioDefault = oTableTelResp.$('input[name$="[default]"]');
    if (radioDefault.length > 0) {
        radioDefault.each(function(a, radio) {
            if ($(radio).val() == 1) {
                $(radio).closest('tr').find('input[name$="[numero]"]').removeClass('inputError');
                $(radio).closest('tr').find('input[name$="[numero]"]').intlTelInput("setNumber", part[0] + '' + part[1]);
                var selectTipo = $(radio).closest('tr').find('select[name$="[tipo_telefono]"]');
                selectTipo.find('option').each(function() {
                    var option = this;
                    $(option).prop('selected', tipo == $(option).val());
                    selectTipo.trigger('chosen:updated');
                });


                var selectEmpresa = $(radio).closest('tr').find('select[name$="[empresa]"]');
                $(selectEmpresa).find('option').each(function(o, option) {
                    $(option).prop('selected', $(option).val() == empresa_tel);
                });
                $(selectEmpresa).trigger('chosen:updated');

            }
        });
    } else {
        var nuevoDefault = {
            cod_area: part[0],
            baja: 0,
            codigo: "-1",
            'default': 1,
            empresa: empresa_tel,//"",
            numero: part[1],
            tipo_telefono: tipo
        };
        addTelResp(nuevoDefault);
    }
}

function getTelDefaulResponsable(responsable) {
    var retorno = langFRMAlumnos.no_tiene_telefono_default;
    $(responsable.telefono).each(function(k, T) {
        if (T.default == 1) {
            retorno = T.cod_area + '-' + T.numero;
        }
    });
    return retorno;
}

function mostarDefaultEnForm() {
    var radiosDefault = oTableTelResp.$('input[name$="[default]"]');
    $(radiosDefault).each(function(k, radio) {
        if ($(radio).val() == 1) {
            var numero = $(radio).closest('tr').find('input[name$="[numero]"]').val();
            var tipo = $(radio).closest('tr').find('select[name$="[tipo_telefono]"]').val();
            numero = limpiarTelefono(tipo, numero);
            telInputResponsable.intlTelInput('setNumber', numero[0] + '' + numero[1]);
            var selectTipo = $("input[name='telefono_default_responsable']").closest('.row').find('select');
            selectTipo.find('option').prop('selected', false);
            selectTipo.find('option[value="' + tipo + '"]').prop('selected', true);
            $(selectTipo).trigger('chosen:updated');
        }
    });
}

function updateTelefonosResponsable() {
    mostarDefaultEnForm();
}

function actualizarTelDefaultResponsable() {
    if ($('#frmTelefonosResponsables').is(':visible')) {
        mostarDefaultEnForm();
    } else {
        setTelefonoDefaultResponsableEnArray();
    }
}

function clearTelResp(obj) {
    var telDefault = $(obj).find('input[type="radio"]').val();
    if (telDefault == 1) {
        $.gritter.add({
            title: 'upss!!',
            text: langFRMAlumnos.no_puede_borrar_el_telefono_predeterminado,
            sticky: false,
            time: '3000',
            class_name: 'gritter-error'
        });
    } else {
        var row = $(obj).find('input[type="radio"]').attr('data-row-obj');
        oTableTelResp.$(obj).find('input[type="hidden"]').eq(1).val('1');
        var sData = oTableTelResp.fnGetData(row);
        oTableTelResp.fnDeleteRow(obj);
    }
}

function addTelResp(obj) {
    var key = oTableTelResp.fnSettings().aoData.length;
    if (!obj) {
        var obj = {
            cod_area: "",
            baja: "",
            codigo: "-1",
            'default': key == 0 ? '1' : 0,
            empresa: "",
            numero: "",
            tipo_telefono: ""
        };
    }
    var i = nAutoincremen;
    var tipoTel = function(tipo, key) {
        var slt = '<select class="form-control" name="telefono[' + key + '][tipo_telefono]" onchange="actualizarTelDefaultResponsable();">';
        $(tipo_telefono).each(function(k, v) {
            var selected = tipo == v.id ? 'selected' : '';
            slt += '<option value="' + v.id + '" ' + selected + '>' + v.nombre + '</option>';
        });
        slt += '</select>';
        return slt;
    };

    var empresa = function(empresa, key) {
        var slt = '<select class="form-control" name="telefono[' + key + '][empresa]"><option></option>';
        $(empresas_tel).each(function(k, v) {
            var selected = empresa == v.codigo ? 'selected' : '';
            slt += '<option value="' + v.codigo + '" ' + selected + '>' + v.nombre + '</option>';
        });
        slt += '</select>';
        return slt;
    };
    var checked = 'checked';
    if (obj.default) {
        checked = 'checked';
    } else {
        checked = '';
    }

    var valueDefault = checked == 'checked' ? 1 : 0;
    var telFull = obj.cod_area + '' + obj.numero;
    var retorno = [
        i,
        '<input type="hidden"  value="' + obj.codigo + '" name="telefono[' + i + '][codigo]" readonly>' +
                '<input  class="form-control no-margin" value="' + telFull + '" name="telefono[' + i + '][numero]">',
        empresa(obj.empresa, i),
        tipoTel(obj.tipo_telefono, i),
        '<input  type="radio" value="' + valueDefault + '" data-row-obj="' + i + '"  name="telefono[' + i + '][default]" ' + checked + '>',
        '<button class="eliminarTelResponsable btn btn-primary btn-xs" data-row-obj="' + i + '">' + langFRMAlumnos.eliminar + '</button><input class="form-control" type="hidden" name="telefono[' + i + '][baja]"  value="0">'
    ];

    var a = oTableTelResp.fnAddData([retorno]);
    oTableTelResp.$('select').chosen({
        "width": '100%',
        'allow_single_deselect': true
    });
    nAutoincremen++;
    oTableTelResp.$('input[name$="[numero]"]').last().intlTelInput({
        utilsScript: BASE_URL + 'assents/js/librerias/tel-master/utils.js',
        nationalMode: true,
        onlyCountries: ['ar', 'br', 'uy', 'py', 've', 'bo', 'cl', 'co', 'pa', 'us'],
        defaultCountry: paises[BASE_PAIS]
    });
    $('input[name$="[numero]"]').attr('placeholder', '');
    oTableTelResp.$('input[name$="[numero]"]').last().on('keyup', function() {
        var miInput = $(this);
        if ($.trim(miInput.val())) {
            if (paises[BASE_PAIS] == "bo" || miInput.intlTelInput("isValidNumber")) {
                miInput.removeClass('inputError');
                actualizarTelDefaultResponsable();
            } else {
                miInput.addClass('inputError');
            }
        }
    });
}

function listarTelefonosResponsable() {
    oTableTelResp.fnClearTable();
    for (var i in responsableEnEdiccion.telefono) {
        addTelResp(responsableEnEdiccion.telefono[i]);
    }
}

function renderTelResponsable() {
    $('#telefonosResponsable .responsiveResponsable').html('<form id="frmTelefonosResponsables"><table id="tablaTelResponsable" class="table table-condensed table-bordered table-striped"><tbody></tbody></table></form>');
    oTableTelResp = $('#tablaTelResponsable').dataTable({
        bFilter: true,
        "aoColumns": [
            {"sTitle": langFRMAlumnos.codigo, "sClass": "center", "bVisible": false},
            {"sTitle": langFRMAlumnos.numero, "sClass": "center", "bVisible": true},
            {"sTitle": langFRMAlumnos.datos_empresa, "sClass": "center", "bVisible": true},
            {"sTitle": langFRMAlumnos.tipo_telefono, "sClass": "center", "bVisible": true},
            {"sTitle": langFRMAlumnos.default, "sClass": "center", "bVisible": true},
            {"sTitle": langFRMAlumnos.eliminar, "sClass": "center", "bVisible": true}
        ],
        "iDisplayLength": 5,
        "bInfo": false,
        "sDom": "Rlfrtip"
    });
    $('#tablaTelResponsable_filter').hide();
    $('#tablaTelResponsable_length').html('<button style="margin-bottom:1%;" type="button" name="nuevoTelResponsable"  id="" class="btn btn-primary">' + langFRMAlumnos.nuevo_tel + '</button>');
    listarTelefonosResponsable();
}

function frmTelefonosResponsable() {
    $('#telefonosResponsable').modal();
}

function aplicarMascaraListarTelAlumnos(pais = null) {
    var tel = oTableTel.$('input[name$="[numero]"]').last();
    var miTimer = '';

    if(pais == null){
        pais = paises[BASE_PAIS];
    }

    tel.intlTelInput({
        utilsScript: BASE_URL + 'assents/js/librerias/tel-master/utils.js',
        nationalMode: true,
        onlyCountries: ['ar', 'br', 'uy', 'py', 've', 'bo', 'cl', 'co', 'pa', 'us'],
        defaultCountry: pais
    });
    $('input[name$="[numero]"]').attr('placeholder', '');
    tel.on('keyup', function() {
        clearTimeout(miTimer);
        if ($.trim(tel.val())) {
            if (paises[BASE_PAIS] == "bo" || tel.intlTelInput("isValidNumber")) {
                tel.removeClass('inputError');
                actualizarTelDefaultEnLista();
            } else {
                miTimer = setTimeout(function() {
                    tel.addClass('inputError');
                }, 1000);
            }
        }
    });
}

function addTelAlumnos(telefono) {
    var key = nTelAlumnos;
    var check = telefono.default == 1 ? 'checked="checked"' : '';
    var tipoTel = function(key) {
        var slt = '<select class="form-control" name="telefonos[' + key + '][tipo_telefono]" onchange="actualizarTelDefaultEnLista();">';
        $(tipo_telefono).each(function(k, v) {
            var selected = telefono.tipo_telefono == v.id ? 'selected' : '';
            slt += '<option value="' + v.id + '" ' + selected + '>' + v.nombre + '</option>';
        });
        slt += '</select>';
        return slt;
    };

    var empresa = function(key) {
        var slt = '<select class="form-control" name="telefonos[' + key + '][empresa]"><option></option>';
        $(empresas_tel).each(function(k, v) {
            var selected = v.codigo == telefono.empresa ? 'selected' : '';
            slt += '<option value="' + v.codigo + '" ' + selected + '>' + v.nombre + '</option>';
        });
        slt += '</select>';
        return slt;
    };
    var telFull = telefono.cod_area + '' + telefono.numero;
    var tel = [
        telefono.codigo,
        '<input type="hidden" name="telefonos[' + key + '][codigo]" value="' + telefono.codigo + '" readonly><input type="tel" name="telefonos[' + key + '][numero]" class="form-control no-margin" value="' + telFull + '" >',
        empresa(key),
        tipoTel(key),
        '<input type="radio" value="' + telefono.default + '" name="telefonos[' + key + '][default]" ' + check + '>',
        '<button class="eliminarTelAlumno btn btn-primary btn-xs">' + langFRMAlumnos.eliminar + '</button><input type="hidden" value="' + telefono.baja + '"  name="telefonos[' + key + '][baja]">',
        telefono.pais
    ];

    oTableTel.fnAddData(tel);
    nTelAlumnos++;
    oTableTel.$('select').chosen({
        width: '100%',
        'allow_single_deselect': true
    });
    aplicarMascaraListarTelAlumnos(telefono.pais);
}

function actualizarTelDefaultEnLista() {
    if ($('#frmTelefonosAlumno').is(':visible')) {
        mostrarTelDefault();
    } else {
        var tel = $('input[name="telefono_alumno"]').val();
        var tipo_tel = $('input[name="telefono_alumno"]').closest('.row').find('select').val();
        var empresa_tel = $('#id_empresa_telefono').val();
        var myTel = limpiarTelefono(tipo_tel, tel);
        var inputDefault = oTableTel.$('input[name$="[default]"]');
        if (inputDefault.length > 0) {
            $(inputDefault).each(function() {
                if ($(this).val() == 1) {
                    var row = $(this).closest('tr');
                    $(row).find('input[name$="[numero]"]').removeClass('inputError');
                    $(row).find('input[name$="[numero]"]').intlTelInput("setNumber", myTel[0] + '' + myTel[1]);
                    var selectTipo = $(row).find('select[name$="[tipo_telefono]"]');
                    $(selectTipo).find('option').each(function(o, option) {
                        $(option).prop('selected', $(option).val() == tipo_tel);
                    });
                    $(selectTipo).trigger('chosen:updated');


                    var selectEmpresa = $(row).find('select[name$="[empresa]"]');
                    $(selectEmpresa).find('option').each(function(o, option) {
                        $(option).prop('selected', $(option).val() == empresa_tel);
                    });
                    $(selectEmpresa).trigger('chosen:updated');

                }
            });
        } else {
            var newTel = {
                'cod_area': myTel[0],
                'codigo': '-1',
                'default': 1,
                'numero': myTel[1],
                'tipo_telefono': tipo_tel,
                'empresa': empresa_tel,//'',
                'baja': 0,
                'pais': ''
            };
            addTelAlumnos(newTel);
        }
    }
}

function mostrarFrmTelAlumno() {
    $('#telefonosAlumno').modal();
}

function mostrarTelDefault() {
    var defaults = oTableTel.$('input[name$="[default]"]');
    var telefonoDefault = langFRMAlumnos.sinTelefono;
    $.each(defaults, function(k, tel) {
        if ($(tel).val() == 1) {
            var cod_area = $(tel).closest('tr').find('input[name$="[cod_area]"]').val();
            var numero = $(tel).closest('tr').find('input[name$="[numero]"]').val();
            var tipo_tel = $(tel).closest('tr').find('select[name$="[tipo_telefono]"]').val();
            var empresa = $(tel).closest('tr').find('select[name$="[empresa]"]').val();
            var pais = $(tel).closest('tr').find('select[name$="[pais]"]').val();

            var ee = "telefonos["+ k +"][numero]";
            var TelInput = $('input[name="' + ee + '"]').intlTelInput("getSelectedCountryData");
            pais_cod = TelInput['iso2'];
            if(pais_cod == null){
                pais_cod = paises[BASE_PAIS];
            }
            var telLimpio = limpiarTelefono(tipo_tel, numero, pais_cod);

            $('input[name="telefono_alumno"]').intlTelInput({
                utilsScript: BASE_URL + 'assents/js/librerias/tel-master/utils.js',
                nationalMode: true,
                onlyCountries: ['ar', 'br', 'uy', 'py', 've', 'bo', 'cl', 'co', 'pa', 'us'],
                defaultCountry: pais_cod
            });

            telefonoDefault = telLimpio[0] + '' + telLimpio[1];
            $('input[name="telefono_alumno"]').intlTelInput("setNumber", telefonoDefault);
            $('input[name="telefono_alumno"]').intlTelInput("selectCountry", pais_cod);

            var selectTipo = $('input[name="telefono_alumno"]').closest('.row').find('select');
            if (tipo_tel == 'celular'){
                $(".select_empresa_telefono").show();
            }
            console.log(tipo_tel);
            $(selectTipo).find('option').each(function() {
                $(this).prop('selected', $(this).val() == tipo_tel);
            });
            $(selectTipo).trigger('chosen:updated');
            var selectEmpresa = $('#id_empresa_telefono');
            $(selectEmpresa).find('option').each(function() {
                $(this).prop('selected', $(this).val() == empresa);
            });
            $(selectEmpresa).trigger('chosen:updated');
        }
    });
    if (defaults.length == 0) {
        var tipo = $('input[name="telefono_alumno"]').closest('.row').find('select').val();
    }
}

function isEmptyJSON(obj) {
    for (var i in obj) {
        return false;
    }
    return true;
}

function nueva_razon_social() {
    $.ajax({
        url: BASE_URL + 'razonessociales/frm_razones_sociales',
        type: 'POST',
        data: {
            codigo: '',
            modo: 'llamada_desde_alumno'
        },
        success: function(_html) {
            $("[name=area_razones_sociales_temporal]").html(_html);
            $('[name=area_razones_sociales_temporal]').modal();
        }
    });
}

function cerrarFancyDesdeRazonesSociales(codigo_razon_social_obtenido, razon_social_obtenido) {
    $("[name=area_razones_sociales_temporal]").html("");
    $('[name=area_razones_sociales_temporal]').modal("hide");
    $("#selectrazones").find("option").remove();
    $("#selectrazones").append("<option value='" + codigo_razon_social_obtenido + "'>" + razon_social_obtenido + "</option>");
    $("#selectrazones").trigger("chosen:updated");
}

function deleteTelAlumno(row) {
    var telDefault = $(row).closest('tr').find('input[type="radio"]').val();
    if (telDefault == 1) {
        $.gritter.add({
            title: 'upss!!',
            text: langFRMAlumnos.no_puede_borrar_el_telefono_predeterminado,
            sticky: false,
            time: '3000',
            class_name: 'gritter-error'
        });
    } else {
        var telDelete = $(row).closest('tr').find('input[type="hidden"]').eq(0).val();
        $(row).parent().find('input').val(1);
        telDelete == -1 ? $(row).closest('tr').find('input, select').prop('readonly', false).prop('disabled', true) : '';
        $(row).closest('tr').hide();
    }
}

function cerrarFrmTelAlumnos() {
    var validacion = validacionIngresoTelefonos(oTableTel);
    if (validacion.codigo) {
        mostrarTelDefault();
        $('#telefonosAlumno').modal('hide');
    } else {
        $.gritter.add({
            text: validacion.respuesta,
            sticky: false,
            time: '3000',
            class_name: 'gritter-error'
        });
    }
}

$('.fancybox-wrap').ready(function() {
    if (telefonos.length > 0) {
        var tablaTel = '<table>';
        $(telefonos).each(function(k, telefono) {
            if (telefono.cod_area_old != null && telefono.numero_old != null) {
                $('[data-rel=popover]').removeClass('hide');
                tablaTel += '<tr>';
                tablaTel += '<td>';
                tablaTel += telefono.cod_area_old + '-' + telefono.numero_old;
                tablaTel += '</td>';
                tablaTel += '</tr>';
            }
        });
        tablaTel += '</table>';
        $('[data-rel=popover]').attr('data-content', tablaTel);
    }
    $('[data-rel=popover]').popover({html: true, container: 'body'});
    $('.fancybox-wrap').on('click', '.diabledTab', function() {
        return false;
    });
    telInput = $('input[name="telefono_alumno"]');
    telInputResponsable = $('input[name="telefono_default_responsable"]');
    setTimeout(function() {
        $('input[name="telefono_alumno"]').attr('placeholder', "");
        $('input[name="telefono_default_responsable"]').attr('placeholder', '');
    }, 600);

    if (codigoAlumno == '-1') {
        $('input[name="documento"]').prop('readonly', true);
    } else {
        setMascaraIdentificacion($('select[name="tipoDniAlumno"]').val(), 'input[name="documento"]');
    }
    $('input[name="numero_documento_responsable"]').prop('readonly', true);
    setMascaraFecha(pais, 'input[name="fechanaci"]');
    setMascaraFecha(pais, 'input[name="fecha_nacimiento_responsable"]');
    $('#id_tipo_telefono').on('change', function() {
        if ($(this).val() == 'celular') {
            $('.celular').removeClass('hide');
            $('.fijo').addClass('hide');
            $('.select_empresa_telefono').show();
        } else {
            $('.fijo').removeClass('hide');
            $('.celular').addClass('hide');
            $('.select_empresa_telefono').hide();
        }
    });
    var timerAlumnos = '';

    $(telefonos).each(function(key, telefono) {
            return false;
    });


    telInput.intlTelInput({
        utilsScript: BASE_URL + 'assents/js/librerias/tel-master/utils.js',
        nationalMode: true,
        onlyCountries: ['ar', 'br', 'uy', 'py', 've', 'bo', 'cl', 'co', 'pa', 'us'],
        defaultCountry: paises[BASE_PAIS]
    });

    telInput.on('keyup', function() {
        clearTimeout(timerAlumnos);
        if ($.trim(telInput.val())) {
            if (paises[BASE_PAIS] == "bo" || telInput.intlTelInput("isValidNumber")) {
                telInput.removeClass('inputError');
                actualizarTelDefaultEnLista();
            } else {
                timerAlumnos = setTimeout(function() {
                    telInput.addClass('inputError');
                }, 1000);
            }
        }
    });

    var timerResponsable = '';
    telInputResponsable.intlTelInput({
        utilsScript: BASE_URL + 'assents/js/librerias/tel-master/utils.js',
        nationalMode: true,
        onlyCountries: ['ar', 'br', 'uy', 'py', 've', 'bo', 'cl', 'co', 'pa', 'us'],
        defaultCountry: paises[BASE_PAIS]
    });

    telInputResponsable.on('keyup', function() {
        clearTimeout(timerResponsable);
        if ($.trim(telInputResponsable.val())) {
            if (paises[BASE_PAIS] == "bo" || telInputResponsable.intlTelInput("isValidNumber")) {
                setTelefonoDefaultResponsableEnArray();
                telInputResponsable.removeClass('inputError');
            } else {
                timerResponsable = setTimeout(function() {
                    telInputResponsable.addClass('inputError');
                }, 1000);
            }
        }
    });

    $('input[name="fecha_nacimiento_responsable"]').datepicker({
        yearRange: "1920:2014",
        changeMonth: true,
        changeYear: true
    });

    $('select[name="tipoDniAlumno"]').on('change', function() {
        setMascaraIdentificacion($(this).val(), 'input[name="documento"]');
    });

    $('body').on('keyup', function(e) {
        if (e.which == 27) {
            $('.modal').modal('hide');
        }
    });

    var ediccionResp = '';
    var fancyW = $('.fancybox-wrap');
    var anio_actual = new Date().getFullYear();
    var rango = '1920:' + anio_actual;
    $('.fancybox-wrap input[name="fechanaci"]').datepicker({
        yearRange: rango,
        changeMonth: true,
        changeYear: true
    });

    $('.fancybox-wrap input[name="fechanaci"]').on('change', function() {
        var edadAlumno = $(this).datepicker("getDate") == null ? moment() : $(this).datepicker("getDate");
        if (alumnoEsMayorEdad(mayoriaEdad, edadAlumno)) {
            necesitaResponsable = 0;
        } else {
            necesitaResponsable = 1;
        }
    });

    fancyW.find('select').chosen({
        width: '100%'
    });

    fancyW.find('table').on('focus', '.inputTable', function() {
        $(this).prop('readonly', false);
        $('.fancybox-wrap table tr .inputTable').not(this).prop('readonly', true);
        return false;
    });

    fancyW.find('#telefonosResponsable').on('focus', '.inputTable', function() {
        $(this).prop('readonly', false);
        $('#telefonosResponsable tr .inputTable').not(this).prop('readonly', true);
        return false;
    });

    fancyW.find('#telefonosResponsable').on('click', 'input[type="radio"]', function()
    {
        $('#telefonosResponsable input[type="radio"]').prop('checked', false).val(0);
        $(this).prop('checked', true).val(1);
        actualizarTelDefaultResponsable();
    });

    fancyW.find('#telefonosResponsable').on('click', 'input[type="checkbox"]', function() {
        if ($(this).is(':checked')) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
    });

    fancyW.find('#listadoResponsables').on('click', '.eliminarResponsable', function() {
        deleteResponsable($(this).closest('tr'));
        return false;
    });

    var razones = JSON.parse(fancyW.find('input[name="razones"]').val());
    var razonesSociales = '';
    var objEmpresas = function() {
        var x = new Object();
        $(empresas_tel).each(function(key, value) {
            x[value.codigo] = value.nombre;
        });
        return x;
    };

    var objTipo = function() {
        var x = new Object();
        $(tipo_telefono).each(function(key, value) {
            x[value.id] = value.nombre;
        });
        return x;
    };

    var objCondicion = function() {
        var x = new Object();
        $(condiciones).each(function(key, value) {
            x[value.codigo] = value.condicion;
        });
        return x;
    };

    var objTipoIdenti = function() {
        var x = new Object();
        $(tipo_identificacion).each(function(key, value) {
            x[value.codigo] = value.nombre;
        });
        return x;
    };

    function dataTel() {
        var retorno = [];
        $(telefonos).each(function(key, telefono) {
            var check = telefono.default == 1 ? 'checked="checked"' : '';
            var tipoTel = function(key) {
                var slt = '<select class="form-control" name="telefonos[' + key + '][tipo_telefono]">';
                $(tipo_telefono).each(function(k, v) {
                    var selected = telefono.tipo_telefono == v.id ? 'selected' : '';
                    slt += '<option value="' + v.id + '" ' + selected + '>' + v.nombre + '</option>';
                });
                slt += '</select>';
                return slt;
            };

            var empresa = function(key) {
                var slt = '<select class="form-control" name="telefonos[' + key + '][empresa]">';
                $(empresas_tel).each(function(k, v) {
                    var selected = v.codigo == telefono.empresa ? 'selected' : '';
                    slt += '<option value="' + v.codigo + '" ' + selected + '>' + v.nombre + '</option>';
                });
                slt += '</select>';
                return slt;
            };

            var tel = [
                telefono.codigo,
                '<input onkeypress="return ingresarNumero(this, event);" name="telefonos[' + key + '][cod_area]" class="form-control inputTable no-margin" value="' + telefono.cod_area + '" readonly><input type="hidden" name="telefonos[' + key + '][codigo]" value="' + telefono.codigo + '" readonly>',
                '<input onkeypress="return ingresarNumero(this, event);"  name="telefonos[' + key + '][numero]" class="form-control inputTable no-margin" value="' + telefono.numero + '" readonly>',
                empresa(key),
                tipoTel(key),
                '<input type="radio" value="' + telefono.default + '" name="telefonos[' + key + '][default]" ' + check + '>',
                '<button class="eliminarTelAlumno btn btn-primary btn-xs">' + langFRMAlumnos.eliminar + '</button><input type="hidden" value="' + telefono.baja + '"  name="telefonos[' + key + '][baja]">',
                telefono.pais

            ];
            retorno.push(tel);
        });
        return retorno;
    }

    function renderTel() {
        fancyW.find('#telefonosAlumno .table-responsive').html('<table id="tablaTelefonosAlumnos" class="table table-bordered table-condensed"></table>');
        oTableTel = fancyW.find('#tablaTelefonosAlumnos').dataTable({
            "bSort": false,
            "bFilter": false,
            "aoColumns": [
                {"sTitle": langFRMAlumnos.codigo, "sClass": "center", "bVisible": false},
                {"sTitle": langFRMAlumnos.numero, "sClass": ""},
                {"sTitle": langFRMAlumnos.datos_empresa, "sClass": "center"},
                {"sTitle": langFRMAlumnos.tipo_telefono, "sClass": "center"},
                {"sTitle": langFRMAlumnos.default, "sClass": "center"},
                {"sTitle": langFRMAlumnos.baja, "sClass": "center"}
            ],
            "iDisplayLength": 5,
            "bInfo": false,
            "sDom": "Rlfrtip"
        });
        $(telefonos).each(function(key, telefono) {
            addTelAlumnos(telefono, key);
        });
        oTableTel.$('select').chosen({
            'width': '100%'
        });
        $('#tablaTelefonosAlumnos_length').html('<button type="button" style="margin-bottom:1%;" name="nuevo"  id="" class="btn btn-primary">' + langFRMAlumnos.nuevo_tel + '</button>');
        $('#tablaTelefonosAlumnos').wrap("<form id='frmTelefonosAlumno'></form>");
    }

    function renderResponsable() {
        oTableResponsable1 = $('#tablaresponsables').dataTable({
            "aoColumns": [
                {"sTitle": langFRMAlumnos.responsable_codigo, "sClass": "center", "bVisible": false},
                {"sTitle": langFRMAlumnos.nombre, "sClass": "center"},
                {"sTitle": langFRMAlumnos.tipo_documento, "sClass": "center"},
                {"sTitle": langFRMAlumnos.domicilio, "sClass": "center"},
                {"sTitle": langFRMAlumnos.email, "sClass": "center"},
                {"sTitle": langFRMAlumnos.relacion, "sClass": "center"},
                {"sTitle": langFRMAlumnos.eliminar, "sClass": "center"},
                {"sTitle": "", "sClass": "center"}
            ],
            "bFilter": false,
            "iDisplayLength": 5,
            "bInfo": false,
            "sDom": "Rlfrtip"
        });
        listarResponsables();
        $('#tab2 .dataTables_length').remove();
    }

    function aaDataRazon() {
        var retorno = [];
        $(razones).each(function(key, razon) {
            var disabled = '';
            var checkedDefault = '';
            var checkedDefaultFacturacion = '';
            disabled = razon.default == 1 ? 'disabled="disabled"' : '';
            checkedDefault = razon.default == 1 ? 'checked' : '';
            var df = razon.default_facturacion != '0' && razon.default_facturacion != '1' ? '0' : razon.default_facturacion;
            checkedDefaultFacturacion = razon.default_facturacion == 1 ? "checked" : '';
            var direccion = '';
            if (razon.direccion_calle != null) {
                direccion += razon.direccion_calle + ' ';
                if (razon.direccion_numero != null) {
                    direccion += razon.direccion_numero + ' ';
                    if (razon.direccion_complemento != null) {
                        direccion += razon.direccion_complemento;
                    }
                }
            }
            var nombre = razon.razon_social + '<input type="hidden" id="cod_razon" name="razones[' + key + '][codigo]" value = ' + razon.codigo + '>' + '<input type="hidden" value="' + razon.default + '" name="razones[' + key + '][default]" >';
            var campoDefault = '<input type="radio" value="' + razon.default + '" class="chk_razones_default"' + checkedDefault + ' disabled>';
            var campoDefaultF = '<input type="radio" value="' + df + '" class="chk_razones_default_facturacion" name="razones[' + key + '][default_facturacion]" ' + checkedDefaultFacturacion + ' >';
            var boton = '<button  value="0"  class="eliminarRazon btn btn-info  btn-xs" ' + disabled + '>' + langFRMAlumnos.eliminar + '</button>';
            var respARRAY = [razon.nombrecondicion, razon.tipoid + ' ' + razon.documento, nombre, direccion, razon.email, campoDefault, campoDefaultF, boton];
            retorno.push(respARRAY);
        });

        fancyW.find('#tablaRazonsocial').on('click', '.eliminarRazon ', function() {
            deleteRazon(this);
            return false;
        });
        fancyW.find('#tablaRazonsocial').on('click', '.chk_razones_default_facturacion', function() {
            $("#tablaRazonsocial .chk_razones_default_facturacion").prop('checked', false).val(0);
            $(this).prop('checked', true).val(1);
        });
        return retorno;
    }

    function renderRazones() {
        $('.mainContentRazones').html('<table class="table table-bordered" id="tablaRazonsocial"></table>');
        oTableRazones = $('#tablaRazonsocial').dataTable({
            bFilter: false,
            "aoColumns": [
                {"sTitle": langFRMAlumnos.razon_condicion, "sClass": "center", "bVisible": true},
                {"sTitle": langFRMAlumnos.identificacion, "sClass": "center", "bVisible": true},
                {"sTitle": langFRMAlumnos.razon_social, "sClass": "center", "bVisible": true},
                {"sTitle": langFRMAlumnos.domicilio, "sClass": "center", "bVisible": true},
                {"sTitle": langFRMAlumnos.email, "sClass": "center", "bVisible": true},
                {"sTitle": langFRMAlumnos.default, "sClass": "center", "bVisible": true},
                {"sTitle": langFRMAlumnos.default_facturacion, "sClass": "center", "bVisible": true},
                {"sTitle": langFRMAlumnos.eliminar, "sClass": "center", "bVisible": true}
            ],
            "aaData": aaDataRazon(),
            "iDisplayLength": 5,
            "bInfo": false,
            "sDom": "Rlfrtip"
        });
        var _html = '';
        _html += '<button id="agregarRazon" style="margin-bottom:1%" class="btn btn-primary">';
        _html += langFRMAlumnos.agregar_razon;
        _html += '</button>';
        _html += ' &nbsp ';
        _html += '<select id="selectrazones" name="selectRazones" class="form-control input-sm"  data-placeholder="' + langFRMAlumnos.seleccione_razon + '">';
        _html += '<option></option>';
        _html += '</select>';
        _html += '&nbsp &nbsp';
        _html += '<button value="0" id="agregarazon" name="agregarazon" class=" btn btn-success btn-sm">';
        _html += langFRMAlumnos.vincular_seleccion;
        _html += '&nbsp;&nbsp;';
        _html += '<i class="icon-ok"></i>';
        _html += '</button>';
        _html += ' &nbsp; ';
        _html += '<button name="btn_nueva_razon_social" style="margin-bottom:1%;" class="btn btn-primary" onclick="nueva_razon_social();">';
        _html += langFRMAlumnos.crear_nueva_razon;
        _html += '</button>';
        $('#tab3 .dataTables_length').html(_html);
        $('#tablaRazonsocial').wrap("<form id='frmRazonSocial'></form>");
    }

    function cargarSelectRazones() {
        var agregar = true;
        $.ajax({
            url: BASE_URL + 'alumnos/getRazonesSociales',
            data: 'codigo=' + +$('input[name="codigo_alumno"]').val(),
            cache: false,
            type: 'POST',
            dataType: 'json',
            success: function(respuesta) {
                $('select[name="selectRazones"]').empty();
                $('select[name="selectRazones"]').append('<option></option>');
                $(respuesta).each(function(k, valor) {
                    agregar = true;
                    $(razones).each(function(key, razon) {
                        if (razon.codigo === valor.codigo) {
                            agregar = false;
                        }
                    });
                    if (agregar) {
                        $('select[name="selectRazones"]').append('<option value=' + valor.codigo + '>' + valor.razon_social + '</option>');
                    }
                });
                $("select[name='selectRazones']").trigger("chosen:updated");
            }
        });
    }

    function deleteRazon(row) {
        $(row).parent().find('input').val(1);
        var codRazon = $(row).closest('tr').find('input[id="cod_razon"]').val();
        $(row).closest('tr').hide();
        var indice = '';
        var razones2 = [];
        $(razones).each(function(key, razon) {
            if (razon.codigo != codRazon) {
                razones2.push(razon);
            }
        });
        razones = razones2;
        renderRazones();
        fancyW.find('select').chosen({
            "width": '30%'
        });
        $('#selectrazones_chosen').hide();
        $('#agregarazon').hide();
        $('[name=btn_nueva_razon_social]').hide();
    }

    renderTel();
    mostrarTelDefault();
    renderResponsable();
    renderRazones();
    renderTelResponsable();
    fancyW.find('select').chosen({
        "width": '30%'
    });

    $('#selectrazones_chosen').hide();
    $('#agregarazon').hide();
    $("[name=btn_nueva_razon_social]").hide();
    $('.dataTables_length').parent().addClass('no-padding');
    fancyW.find('input[type="text"]').addClass('input-sm');
    fancyW.find('.btn, li').on('click', function() {
        $.fancybox.update();
    });

    fancyW.find('#general').on('change', 'select[name="domicilioPais"]', function(){
        var valor = $(this).val();
        $.ajax({
            url: BASE_URL + 'alumnos/getProvincias',
            cache: false,
            type: 'POST',
            dataType: 'json',
            data: {
                id_pais: valor
            },
            success: function(_json){
                $('select[name="domiciProvincia"]').empty();
                $('select[name="domiciProvincia"]').prop('disabled', false);
                $(_json).each(function(){
                    $('select[name="domiciProvincia"]').append('<option value=' + this.id + '>' + this.nombre + '</option>');
                });
                $("select[name='domiciProvincia']").trigger("chosen:updated");
                $('select[name="domiciProvincia"]').change();
            }
        });
    });

    fancyW.find('#general').on('change', 'select[name="domiciProvincia"]', function() {
        var valor = $(this).val();
        $.ajax({
            url: BASE_URL + 'alumnos/getlocalidades',
            data: 'idprovincia=' + valor,
            cache: false,
            type: 'POST',
            dataType: 'json',
            success: function(respuesta) {
                $('select[name="domiciLocalidad"]').empty();
                $('select[name="domiciLocalidad"]').prop('disabled', false);
                $(respuesta).each(function() {
                    $('select[name="domiciLocalidad"]').append('<option value=' + this.id + '>' + this.nombre + '</option>');
                });
                $("select[name='domiciLocalidad']").trigger("chosen:updated");
            }
        });
    });

    fancyW.on('click', '#detalle_talles_alumno', function() {
        $('#detalle_talle_alumno').modal();
        return false;
    });

    fancyW.find('.modal').on('click', 'button[name="nuevo"]', function() {
        var teldefault = oTableTel.$('input[type="radio"]:checked');
        addTelAlumnos({
            codigo: '-1',
            empresa: '',
            tipo_telefono: '',
            numero: '',
            cod_area: '',
            baja: '0',
            pais: '',
            'default': teldefault.length > 0 ? 0 : 1

        });
    });

    fancyW.find('#tablaTelefonosAlumnos').on('focus', '.inputTable', function() {
        $(this).prop('readonly', false);
        $('#tablaTelefonosAlumnos tr .inputTable').not(this).prop('readonly', true);
        return false;
    });

    fancyW.find('#tablaTelefonosAlumnos').on('click', 'input[type="radio"]', function() {
        oTableTel.$('input[type="radio"]').prop('checked', false).val(0);
        $(this).prop('checked', true).val(1);
        actualizarTelDefaultEnLista();
    });

    fancyW.find('#tablaTelefonosAlumnos').on('click', '.eliminarTelAlumno', function() {
        deleteTelAlumno(this);
        return false;
    });

    $('#tablaresponsables button[name="btnTelResponsable"]').popover({
        trigger: 'hover',
        html: true,
        title: langFRMAlumnos.telefono_default
    });

    fancyW.find('#nuevoResponsable').on('click', function() {
        frmResponsable();
        return false;
    });

    fancyW.find('#tablaresponsables').on('click', 'button[name="btnTelResponsable"]', function() {
        return false;
    });

    fancyW.find('#telefonosResponsable').on('click', 'button[name="nuevoTelResponsable"]', function() {
        addTelResp();
        return false;
    });

    fancyW.find('#telefonosResponsable').on('click', '.eliminarTelResponsable', function() {
        clearTelResp($(this).closest('tr'));
        return false;
    });

    fancyW.find('#tablaRazonsocial').on('click', '.chk_razones_default', function() {
        $('#tablaRazonsocial .chk_razones_default').prop('checked', false).val(0);
        $(this).prop('checked', true).val(1);
    });

    fancyW.find('#tablaRazonsocial').on('click', '.chk_razones_default_facturacion', function() {
        $("#tablaRazonsocial .chk_razones_default_facturacion").prop('checked', false).val(0);
        $(this).prop('checked', true).val(1);
    });

    fancyW.find('#tablaRazonsocial').on('click', '.eliminarRazon ', function() {
        deleteRazon(this);
        return false;
    });

    fancyW.find('#tab3').on('click', '#agregarRazon', function() {
        $('#selectrazones_chosen').show();
        $('#agregarazon').show();
        $("[name=btn_nueva_razon_social]").show();
        cargarSelectRazones();
        return false;
    });

    fancyW.find('#datosAdicionales').on('change', 'select[name="prov"]', function() {
        var valor = $(this).val();
        $.ajax({
            url: BASE_URL + 'alumnos/getlocalidades',
            data: 'idprovincia=' + valor,
            cache: false,
            type: 'POST',
            dataType: 'json',
            success: function(respuesta) {
                $('select[name="localidad"]').empty();
                $(respuesta).each(function() {
                    $('select[name="localidad"]').append('<option value=' + this.id + '>' + this.nombre + '</option>');
                });
                $("select[name='localidad']").trigger("chosen:updated");
            }
        });
    });

    fancyW.find('#tab3').on('click', 'button[name="agregarazon"]', function() {
        var valor = $('select[name="selectRazones"]').val();
        if (valor != '') {
            $.ajax({
                url: BASE_URL + 'razonessociales/getRazonSocial',
                data: {
                    codigo: valor
                },
                cache: false,
                type: 'POST',
                dataType: 'json',
                success: function(razon) {
                    razones.push(razon);
                    renderRazones();
                    fancyW.find('select').chosen({
                        "width": '30%'
                    });
                    $('#selectrazones_chosen').hide();
                    $('[name=btn_nueva_razon_social]').hide();
                    $('#agregarazon').hide();
                }
            });
            oTableRazones.$('select').chosen({
                "width": '100%'
            });
        }
    });

    $('select[name="domiciProvincia_responsable"]').on('change', function() {
        var valor = $(this).val();
        $.ajax({
            url: BASE_URL + 'alumnos/getlocalidades',
            data: 'idprovincia=' + valor,
            cache: false,
            type: 'POST',
            dataType: 'json',
            success: function(respuesta){
                $('select[name="domiciLocalidad_responsable"]').empty();
                $(respuesta).each(function() {
                    $('select[name="domiciLocalidad_responsable"]').append('<option value=' + this.id + '>' + this.nombre + '</option>');
                });
                $("select[name='domiciLocalidad_responsable']").trigger("chosen:updated");
            }
        });
    });

    fancyW.find('button[name="enviarForm"]').on('click', function() {
        var apellido_alumno = $('input[name="apellido"]').val();
        var apellidoAlu = '';
        var envio = '';
        var envio_TEL_ALUMNO = oTableTel.$('input,select').serializeJSON();
        var envio_RESPONSABLES = oTableResponsable1.$('input,select').serializeJSON();
        var envio_RAZONES = oTableRazones.$('input,select').serializeJSON();        
        if($('#pais').val() == 2 && $('#barrio').val() == ''){
            $.gritter.add({
                text: "bairro é obrigatório",
                sticky: false,
                time: '3000',
                class_name: 'gritter-error'
            });
            return false;
        }
        var disabledFields = $('#general,#datosAdicionales').find(':input:disabled').removeAttr('disabled');
        envio += $.param($('#general,#datosAdicionales').serializeJSON());
        disabledFields.attr('disabled','disabled');
        var alumnoTelInvalidos = oTableTel.$('.inputError').serializeJSON();
        if (alumnoTelInvalidos.telefonos) {
            $.gritter.add({
                text: langFRMAlumnos.tel_formato_invalido,
                sticky: false,
                time: '3000',
                class_name: 'gritter-error'
            });
            return false;
        }

        var telDefaultInvalido = $('#general .inputError').serializeArray();
        if (telDefaultInvalido.length > 0) {
            $.gritter.add({
                text: langFRMAlumnos.tel_default_invalido,
                sticky: false,
                time: '3000',
                class_name: 'gritter-error'
            });
            return false;
        }

        var telefono_default = $('input[name="telefono_alumno"]').val();
        var country_default = $('input[name="telefono_alumno"]').intlTelInput("getSelectedCountryData");
        pais_default = country_default['iso2'];

        for (var i in envio_TEL_ALUMNO.telefonos) {
            var key = "telefonos["+ i +"][numero]";
            var pais_intlTelInput = $('input[name="' + key + '"]').intlTelInput("getSelectedCountryData");
            pais_cod = pais_intlTelInput['iso2'];

            var tel = envio_TEL_ALUMNO.telefonos[i];

            if (tel.default == 1) {
                var telParsiado = limpiarTelefono(tel['tipo_telefono'], telefono_default, pais_default);
                envio_TEL_ALUMNO.telefonos[i]['pais'] = pais_default;
            } else {
                var telParsiado = limpiarTelefono(tel['tipo_telefono'], tel['numero'], pais_cod);
                envio_TEL_ALUMNO.telefonos[i]['pais'] = pais_cod;
            }

            envio_TEL_ALUMNO.telefonos[i]['cod_area'] = telParsiado['0'];
            envio_TEL_ALUMNO.telefonos[i]['numero'] = telParsiado['1'];

        }

        if (necesitaResponsable) {
            if ($('#general input[name="fechanaci"]').val() != '') {
                if (isEmptyJSON(envio_RESPONSABLES) == false) {
                    envio += '&' + $.param(envio_RESPONSABLES);
                } else {
                    $.gritter.add({
                        text: langFRMAlumnos.cargar_responsable_alumno,
                        sticky: false,
                        time: '3000',
                        class_name: 'gritter-error'
                    });
                    return false;
                }
            }
        }
        if (isEmptyJSON(envio_TEL_ALUMNO) == false) {
            envio += '&' + $.param(envio_TEL_ALUMNO);
        }

        if (isEmptyJSON(envio_RAZONES) == false) {
            envio += '&' + $.param(envio_RAZONES);
        }

        if (isEmptyJSON(envio_RESPONSABLES) == true) {
            envio += '&' + $.param(oTableResponsable1.$('input,select').serializeJSON());
        }

        if (apellidoAlu != '') {
            data_json.apellido = apellidoAlu;
        }
        envio += '&imagen=' + $("[name=imagen_base64]").val();
        $.ajax({
            url: BASE_URL + 'alumnos/guardar',
            type: "POST",
            data: envio,
            dataType: "json",
            cache: false,
            success: function(respuesta) {
                if (respuesta.codigo == 0) {
                    $.gritter.add({
                        title: 'Upss!',
                        text: respuesta.respuesta,
                        sticky: false,
                        time: '5000',
                        class_name: 'gritter-error'
                    });
                } else {
                    $.gritter.add({
                        title: 'OK!',
                        text: langFRMAlumnos.validacion_ok,
                        sticky: false,
                        time: '3000',
                        class_name: 'gritter-success'
                    });
                    oTable.fnDraw();
                    $.fancybox.close(true);
                }
            }
        });
        return false;
    });

    var fechaNacimientoAlumno = $('input[name="fechanaci"]').datepicker("getDate") == null ? moment() : $('input[name="fechanaci"]').datepicker("getDate");
    if (alumnoEsMayorEdad(mayoriaEdad, fechaNacimientoAlumno)) {
        necesitaResponsable = 0;
    } else {
        necesitaResponsable = 1;
    }   
                
    jQuery("#webcam").webcam({
        width: 320,
        height: 240,
        mode: "callback",
        swffile: "assents/js/webcam/jscam_canvas_only.swf",
        onTick: function(remain) {
            if (0 == remain) {
//              jQuery("#status").text("Cheese!"); // mensajes de debug
            } else {
//              jQuery("#status").text(remain + " seconds remaining..."); // mensajes de debub
            }
        },
        onSave: function(data) {
            var col = data.split(";");
            var img = image;
            if (false == filter_on) {
                for(var i = 0; i < 320; i++) {
                    var tmp = parseInt(col[i]);
                    img.data[pos + 0] = (tmp >> 16) & 0xff;
                    img.data[pos + 1] = (tmp >> 8) & 0xff;
                    img.data[pos + 2] = tmp & 0xff;
                    img.data[pos + 3] = 0xff;
                    pos+= 4;
                }
            } else {
                var id = filter_id;
                var r,g,b;
                var r1 = Math.floor(Math.random() * 255);
                var r2 = Math.floor(Math.random() * 255);
                var r3 = Math.floor(Math.random() * 255);
                for(var i = 0; i < 320; i++) {
                    var tmp = parseInt(col[i]);
                    if (id == 0) {
                        r = (tmp >> 16) & 0xff;
                        g = 0xff;
                        b = 0xff;
                    } else if (id == 1) {
                        r = 0xff;
                        g = (tmp >> 8) & 0xff;
                        b = 0xff;
                    } else if (id == 2) {
                        r = 0xff;
                        g = 0xff;
                        b = tmp & 0xff;
                    } else if (id == 3) {
                        r = 0xff ^ ((tmp >> 16) & 0xff);
                        g = 0xff ^ ((tmp >> 8) & 0xff);
                        b = 0xff ^ (tmp & 0xff);
                    } else if (id == 4) {
                        r = (tmp >> 16) & 0xff;
                        g = (tmp >> 8) & 0xff;
                        b = tmp & 0xff;
                        var v = Math.min(Math.floor(.35 + 13 * (r + g + b) / 60), 255);
                        r = v;
                        g = v;
                        b = v;
                    } else if (id == 5) {
                        r = (tmp >> 16) & 0xff;
                        g = (tmp >> 8) & 0xff;
                        b = tmp & 0xff;
                        if ((r += 32) < 0) r = 0;
                        if ((g += 32) < 0) g = 0;
                        if ((b += 32) < 0) b = 0;
                    } else if (id == 6) {
                        r = (tmp >> 16) & 0xff;
                        g = (tmp >> 8) & 0xff;
                        b = tmp & 0xff;
                        if ((r -= 32) < 0) r = 0;
                        if ((g -= 32) < 0) g = 0;
                        if ((b -= 32) < 0) b = 0;
                    } else if (id == 7) {
                        r = (tmp >> 16) & 0xff;
                        g = (tmp >> 8) & 0xff;
                        b = tmp & 0xff;
                        r = Math.floor(r / 255 * r1);
                        g = Math.floor(g / 255 * r2);
                        b = Math.floor(b / 255 * r3);
                    }
                    img.data[pos + 0] = r;
                    img.data[pos + 1] = g;
                    img.data[pos + 2] = b;
                    img.data[pos + 3] = 0xff;
                    pos+= 4;
                }
            }
            if (pos >= 0x4B000) {
                ctx.putImageData(img, 0, 0);
                var canvas = document.getElementById("canvas");
                var img = canvas.toDataURL("image/png"); // el dato a enviar para guardar la imagen
                $("#imagen_preview").attr("src", img);
                $("[name=imagen_base64]").val(img);
                console.log(img.length);
                pos = 0;
            }
        },
        onCapture: function () {
            webcam.save();
            jQuery("#flash").css("display", "block");
            jQuery("#flash").fadeOut(100, function () {
                jQuery("#flash").css("opacity", 1);
            });
        },
        debug: function (type, string) {
//          jQuery("#status").html(type + ": " + string); // mensaje de debug
        },
        onLoad: function () {
            var cams = webcam.getCameraList();
            for(var i in cams) {
//              jQuery("#cams").append("<li>" + cams[i] + "</li>"); // lista las camaras
            }
        }
    });    
});

function asignar_talle(id_talle){
    $("[name=talle]").val("");
    $("[name=talle]").val(id_talle);
    $("[name=talle]").trigger("chosen:updated");
    $("#detalle_talle_alumno").find("#btn-ok-cambio-estado").click();    
}