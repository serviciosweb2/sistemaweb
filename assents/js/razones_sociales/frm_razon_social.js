function isEmptyJSON(obj) {
    for (var i in obj) {
        return false;
    }
    return true;
}
langFRM = langFrm;
var oTableTelRazones = '';
var nTelRazones = 0;
var telefonos_razones = [];
function getCondicionesSociales(){
    $("[name=condicion]").find("option").remove();
    $("[name=condicion]").append("<option value='-1'>(" + langFRM.recuperando + "...)</option>");
    $("[name=condicion]").prop("disabled", true);
    $("[name=condicion]").trigger("chosen:updated");
    var tipo_identificador = $("[name=tipoIdentificacion]").val();    
    setMascaraIdentificacion( tipo_identificador ,'#generalRazon input[name="documento"]' );    
    $.ajax({
       url: BASE_URL + 'razonessociales/getCondicionesSociales',
       type: 'POST',
       dataType: 'json',
       data: {
           tipo_identificador: tipo_identificador
       }, 
       success: function(_json){
           if (_json.length > 0){
               $("[name=condicion]").find("option").remove();
               $.each(_json, function(key, value){
                    $("[name=condicion]").append("<option value=" + value.codigo + ">" + value.condicion + "</option>");
               });
               $("[name=condicion]").prop("disabled", false);
               $("[name=condicion]").trigger("chosen:updated");
           } else {
               $("[name=condicion]").find("option").remove();
               $("[name=condicion]").append("<option value=''>(" + langFRM.sin_registros + ")</option>");
               $("[name=condicion]").trigger("chosen:updated");
           }
       }
    });
}

/*--------------------------------
 * FUNCIONES TELEFONOS DE RAZONES
 * ------------------------------*/
function listarTelefonosRazones(){
    $(telefonos_razones).each(function(key, telefono){
        addTelefonosRazones(telefono);
     
    });     
    oTableTelRazones.$('select').chosen({
            'width': '100%'
    });
}

function verTelefonosRazon(){
    $('#telefonosRazones').modal();
}

function addTelefonosRazones(telefono){
    var tipoTel = function(key){
        var slt = '<select class="form-control" name="telefonos[' + key + '][tipo_telefono]">';
        $(tipo_telefono_razones).each(function(k, v) {
            var selected = telefono.tipo_telefono == v.id ? 'selected' : '';
            slt += '<option value="' + v.id + '" ' + selected + '>' + v.nombre + '</option>';
        });
        slt += '</select>';
        return slt;
    };

    var empresa = function(key){
        var slt = '<select class="form-control" name="telefonos[' + key + '][empresa]"><option></option>';
        $(empresas_tel_razones).each(function(k, v) {
            var selected = v.codigo == telefono.empresa ? 'selected' : '';
            slt += '<option value="' + v.codigo + '" ' + selected + '>' + v.nombre + '</option>';
        });
        slt += '</select>';
        return slt;
    };
    var telFull = telefono.cod_area + '' + telefono.numero;    
    var tel = [
        telefono.codigo,
        '<input type="tel" onkeypress="return ingresarNumero(this, event);"  name="telefonos[' + nTelRazones + '][numero]" class="form-control" value="' + telFull + '"><input type="hidden" name="telefonos[' + nTelRazones + '][codigo]" value="' + telefono.codigo + '" readonly>',
        empresa(nTelRazones),
        tipoTel(nTelRazones),
        '<button class="eliminarTelRazon btn btn-primary btn-xs">' + langFRM.eliminar + '</button><input type="hidden" value="' + telefono.baja + '"  name="telefonos[' + nTelRazones + '][baja]">'
    ];    
    oTableTelRazones.fnAddData(tel);    
    oTableTelRazones.$('select').chosen({
            'width': '100%',
            allow_single_deselect: true
    });
    
    var miTimer= '';    
    telInputRazonListar = oTableTelRazones.$('input[name$="[numero]"]').last();    
    telInputRazonListar.intlTelInput({
        utilsScript: BASE_URL+'assents/js/librerias/tel-master/utils.js',
        nationalMode: true,
        onlyCountries: ['ar', 'br', 'uy', 'py', 've', 'bo', 'cl', 'co', 'pa', 'us'],
        defaultCountry: paises[BASE_PAIS]
    });
   
    telInputRazonListar.on('keyup',function(){        
        clearTimeout(miTimer);        
        if ($.trim(telInputRazonListar.val())){
            if (telInputRazonListar.intlTelInput("isValidNumber")){
                telInputRazonListar.removeClass('inputError');                
                actualizarTelefonoDefaultRazones();
            } else {
                miTimer = setTimeout(function(){                   
                    telInputRazonListar.addClass('inputError');                
                },1000);
            }
        }
    });    
    nTelRazones++;
}

function deleteTelefonoRazones(row){    
    oTableTelRazones.fnDeleteRow(row);
}

function actualizarTelefonoDefaultRazones(){
    if($('#telefonosRazones').is(':visible')){
        $('input[name="telefono_default_razon"]').val('');
        var listado = oTableTelRazones.$('input,select').serializeJSON();

        if(listado.telefonos.length > 0){   
            var vuelta = 0;            
            for(var h in listado.telefonos){
                if(vuelta == 0){
                    var primerTel = listado.telefonos[h];                    
                    var selectTipo = $('input[name="telefono_default_razon"]').closest('.row').find('select');
                    var telParsiado = limpiarTelefono(selectTipo,primerTel.numero);
                    $('input[name="telefono_default_razon"]').intlTelInput("setNumber",telParsiado[0]+''+telParsiado[1]);
                    selectTipo.find('option').each(function(){   
                        var option = this;                     
                        $(option).prop('selected',$(option).val() == primerTel.tipo_telefono);
                    });                    
                    selectTipo.trigger('chosen:updated');
/*
                    var selectEmpresa = $(row).find('select[name$="[empresa]"]');
                    $(selectEmpresa).find('option').each(function(o, option) {
                        $(option).prop('selected', $(option).val() == empresa_tel);
                    });
                    $(selectEmpresa).trigger('chosen:updated')
                    */
                }
                vuelta++;
            }
        }    
    } else {        
        var numerosDeTel = oTableTelRazones.$('input[name$="[numero]"]');
        var telefonoEnForm = $('input[name="telefono_default_razon"]').val();
        var selectTipoEnForm = $('input[name="telefono_default_razon"]').closest('.row').find('select');
        var empresa_tel = $('#id_empresa_telefono_razon').val();

        if(numerosDeTel.length > 0){
            var vuelta=0;
            $(numerosDeTel).each(function(){
                var miNumero = $(this);
                if(vuelta==0){
                    var telParsiado = limpiarTelefono(selectTipoEnForm,telefonoEnForm);
                    miNumero.removeClass('inputError');
                    miNumero.intlTelInput("setNumber",telParsiado[0]+''+telParsiado[1]);
                    var selectEnListar = miNumero.closest('tr').find('select[name$="[tipo_telefono]"]');
                    $(selectEnListar).find('option').each(function(){
                        var opcion = $(this);
                        opcion.prop('selected', opcion.val() == selectTipoEnForm.val());
                    });
                    selectEnListar.trigger('chosen:updated');

                    var selectEmpresa = miNumero.closest('tr').find('select[name$="[empresa]"]');
                    $(selectEmpresa).find('option').each(function(o, option) {
                        $(option).prop('selected', $(option).val() == empresa_tel);
                    });
                    $(selectEmpresa).trigger('chosen:updated');
                }
                vuelta++;
            });
        } else {
           var telParsiado = limpiarTelefono(selectTipoEnForm.val(),telefonoEnForm);            
            addTelefonosRazones({
                codigo:'-1',
                cod_area:telParsiado[0],
                numero:telParsiado[1],
                tipo_telefono:selectTipoEnForm.val(),
                empresa: empresa_tel,//'',
                baja:'0'
            });
        }
    }
}

function cerrarFrmTelefonos(){
    var validacion = validacionIngresoTelRazones(oTableTelRazones);    
    if(validacion.codigo){
        $('#telefonosRazones').modal('hide');
    } else {
        $.gritter.add({
            text: validacion.respuesta,
            sticky: false,
            time: '3000',
            class_name:'gritter-error'
        });
    }
}
$('.fancybox-wrap').ready(function(){    
    if(codRazonSocial == -1){
        $('#generalRazon input[name="documento"]').prop('readonly',true);
    } else {
        setMascaraIdentificacion( $('select[name="tipoIdentificacion"]').val(),'#generalRazon input[name="documento"]' );
    }
    
    var miTimer = '';    
    telInputRazon = $('input[name="telefono_default_razon"]');    
    telInputRazon.intlTelInput({
        utilsScript: BASE_URL+'assents/js/librerias/tel-master/utils.js',
        nationalMode: true,
        onlyCountries: ['ar', 'br', 'uy', 'py', 've', 'bo', 'cl', 'co', 'pa', 'us'],
        defaultCountry: paises[BASE_PAIS]
    });
   
    telInputRazon.on('keyup',function(){
        clearTimeout(miTimer);        
        if ($.trim(telInputRazon.val())){
            if (telInputRazon.intlTelInput("isValidNumber")){
                telInputRazon.removeClass('inputError');
                 actualizarTelefonoDefaultRazones();                
            } else {                    
                miTimer = setTimeout(function(){
                    telInputRazon.addClass('inputError');                 
                },1000);
            }
        }
    });
    
    $('body').on('keyup', function(e) {
        if (e.which == 27) {
            $('.modal').modal('hide');
        }        
    });

    var fancyW = $('#div_fancy_wrap');    
    $('input[name="inicio_actividades"]').datepicker({
        yearRange: "1920:2014",
        changeMonth: true,
        changeYear: true
    });

    $("#ui-datepicker-div").css("zIndex", "12000");
    fancyW.find('table').on('focus', '.inputTable', function() {
        $(this).prop('readonly', false);
        $('.fancybox-wrap table tr .inputTable').not(this).prop('readonly', true);
        return false;
    });

    var empresas_tel = JSON.parse(fancyW.find('input[name="empresas_tel"]').val());
    var tipo_telefono = JSON.parse(fancyW.find('input[name="tipo_telefono"]').val());
    telefonos_razones = JSON.parse(fancyW.find('input[name="telefonos_razones"]').val());
    var tipo_identificacion = JSON.parse(fancyW.find('input[name="tipo_identificacion"]').val());
    var condiciones = JSON.parse(fancyW.find('input[name="condicion"]').val());
    var objEmpresas = function() {
        var x = new Object();
        $(empresas_tel_razones).each(function(key, value) {
            x[value.codigo] = value.nombre;
        });
        return x;
    };

    var objTipo = function() {
        var x = new Object();
        $(tipo_telefono_razones).each(function(key, value) {
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

    function mostrarTel() {       //ver
        var todos = oTableTelRazones.$('input[name$="[numero]"]');
        var telefono = '';
        if (todos.length < 1) {
            telefono = langFRM.sinTelefono;
        } else {
            $.each(todos, function(k, tel) {
                var cod_area = $(tel).closest('tr').find('input[name$="[cod_area]"]').val();
                var numero = $(tel).closest('tr').find('input[name$="[numero]"]').val();
                telefono += cod_area + ' ' + numero + ' .- ';
                //------------------------
                if (k == 0) {
                    var cod_area = $(tel).closest('tr').find('input[name$="[cod_area]"]').val();
                    var numero = $(tel).closest('tr').find('input[name$="[numero]"]').val();
                    var tipo_tel = $(tel).closest('tr').find('select[name$="[tipo_telefono]"]').val();
                    var empresa = $(tel).closest('tr').find('select[name$="[empresa]"]').val();
                    var telLimpio = limpiarTelefono(tipo_tel, numero);
                    telefonoDefault = telLimpio[0] + '' + telLimpio[1];
                    $('input[name="telefono_default_razon"]').intlTelInput("setNumber", telefonoDefault);
                    var selectTipo = $('input[name="telefono_default_razon"]').closest('.row').find('select');
                    $(selectTipo).find('option').each(function() {
                        $(this).prop('selected', $(this).val() == tipo_tel);
                    });
                    $(selectTipo).trigger('chosen:updated');
                    var selectEmpresa = $('#id_empresa_telefono_razon');
                    $(selectEmpresa).find('option').each(function() {
                        $(this).prop('selected', $(this).val() == empresa);
                    });
                    $(selectEmpresa).trigger('chosen:updated');
                }
                //----------------------
            });
        }
        $('span[data-title]').attr('data-title', telefono); //telefono_default_razon
    }    

    function dataTel() {
        var retorno = [];
        $(telefonos).each(function(key, telefono) {
            retorno.push(tel);
        });
        return retorno;
    }

    function renderTel() {
        fancyW.find('#telefonosRazones .table-responsive').html('<table id="tablaTelefonosRazones" class="table table-bordered table-condensed"></table>');
        oTableTelRazones = fancyW.find('#tablaTelefonosRazones').dataTable({
            "bSort": false,
            "bFilter": false,
            "aoColumns": [
                {"sTitle": langFRM.codigo, "sClass": "center", "bVisible": false},
                {"sTitle": langFRM.numero, "sClass": "center"},
                {"sTitle": langFRM.datos_empresa, "sClass": "center"},
                {"sTitle": langFRM.tipo_telefono, "sClass": "center"},
                {"sTitle": langFRM.baja, "sClass": "center"}
            ],
            "iDisplayLength": 4,
            "bInfo": false,
            "sDom": "Rlfrtip"
        });        
        listarTelefonosRazones();        
        $('#tablaTelefonosRazones_length').html('<button type="button" style="margin-bottom:1%;" name="nuevo"  id="" class="btn btn-primary">' + langFRM.nuevo_tel + '</button>');
        $('#tablaTelefonosRazones').wrap("<form id='frmTelefonosRazones'></form>");
    }

    function addTel() {
        var key = oTableTelRazones.fnSettings().aoData.length;
        var tipoTel = function(key) {
            var slt = '<select class="form-control" name="telefonos[' + key + '][tipo_telefono]">';
            $(tipo_telefono_razones).each(function(k, v) {
                var selected = '';
                slt += '<option value="' + v.id + '" ' + selected + '>' + v.nombre + '</option>';
            });
            slt += '</select>';
            return slt;
        };

        var empresa = function(key) {
            var slt = '<select class="form-control" name="telefonos[' + key + '][empresa]">';
            $(empresas_tel_razones).each(function(k, v) {
                var selected = '';
                slt += '<option value="' + v.codigo + '" ' + selected + '>' + v.nombre + '</option>';
            });
            slt += '</select>';
            return slt;
        };

        var tel = [
            '-1',
            '<input onkeypress="return ingresarNumero(this, event)"  name="telefonos[' + key + '][cod_area]" class="form-control inputTable no-margin" value="" readonly><input value="-1" type="hidden" name="telefonos[' + key + '][codigo]" class="form-control inputTable" readonly>',
            '<input onkeypress="return ingresarNumero(this, event)"  name="telefonos[' + key + '][numero]" class="form-control inputTable no-margin" value="" readonly>',
            empresa(key),
            tipoTel(key),
            '<button class="eliminarTelRazon btn btn-primary btn-xs">' + langFRM.eliminar + '</button><input type="hidden" value="0"   name="telefonos[' + key + '][baja]">'
        ];

        var a = $('#tablaTelefonosRazones').dataTable().fnAddData(tel);
        oTableTelRazones.$('select').chosen({
            'width': '100%'
        });
    }
    
    function deleteTelRazon(row){
        oTableTelRazones.fnDeleteRow(row);
    }    
    renderTel();
    mostrarTel();

    fancyW.find('select').chosen({
        "width": '100%'
    });
    $('.dataTables_length').parent().addClass('no-padding');    
    fancyW.find('input[type="text"]').addClass('input-sm');    
    fancyW.find('.btn, li').on('click', function(){
        $.fancybox.update();
    });

    fancyW.find('#generalRazon').on('change', 'select[name="domiciProvincia"]', function() {
        var valor = $(this).val();
        $.ajax({
            url: BASE_URL + 'razonessociales/getlocalidades',
            data: 'idprovincia=' + valor,
            cache: false,
            type: 'POST',
            dataType: 'json',
            success: function(respuesta) {
                $('select[name="domiciLocalidad"]').empty();
                $(respuesta).each(function() {
                    $('select[name="domiciLocalidad"]').append('<option value=' + this.id + '>' + this.nombre + '</option>');
                });
                $("select[name='domiciLocalidad']").trigger("chosen:updated");
            }
        });
    });

    fancyW.on('click', '.ace-file-input', function() {
        $('#telefonosRazones').modal();
        return false;
    });


    fancyW.find('.modal').on('click', 'button[name="nuevo"]', function(){
        addTelefonosRazones({
            codigo:'-1',
            cod_area:'',
            numero:'',
            tipo_telefono:'',
            baja:'0'
        });
        return false;
    });
    fancyW.find('#tablaTelefonosRazones').on('focus', '.inputTable', function() {
        $(this).prop('readonly', false);
        $('#tablaTelefonosRazones tr .inputTable').not(this).prop('readonly', true);
        return false;
    });

    fancyW.find('#tablaTelefonosRazones').on('click', 'input[type="radio"]', function() {
        $('#tablaTelefonosRazones input[type="radio"]').prop('checked', false).val(0);
        $(this).prop('checked', true).val(1);
    });

    fancyW.find('#tablaTelefonosRazones').on('click', '.eliminarTelRazon', function(){
        var row = $(this).closest('tr');
        deleteTelRazon(row);
        actualizarTelefonoDefaultRazones();
        return false;
    });

    fancyW.find('button[name="enviarForm"]').on('click', function() {        
        var envio = '';        
        var envio_TEL = oTableTelRazones.$('input,select').serializeJSON();        
        for(var a in envio_TEL.telefonos){
            var telefono = envio_TEL.telefonos[a];
            telefono = limpiarTelefono(telefono.tipo_telefono,telefono.numero);            
            envio_TEL.telefonos[a]['cod_area'] = telefono[0];
            envio_TEL.telefonos[a]['numero'] = telefono[1];
        }
       
        envio += $.param($('#div_fancy_wrap').find("#generalRazon").serializeJSON());
        if (isEmptyJSON(envio_TEL) == false) {
            envio += '&' + $.param(envio_TEL);
        }        
        $.ajax({
            url: BASE_URL + 'razonessociales/guardar',
            type: "POST",
            data: envio,
            dataType: "json",
            cache: false,
            success: function(respuesta) {
                if (respuesta.codigo == 0) {
                    $.gritter.add({
                        title: 'Upss!',
                        text: respuesta.msgerror,
                        sticky: false,
                        time: '5000',
                        class_name: 'gritter-error'
                    });
                } else {
                    $.gritter.add({
                        title: 'OK!',
                        text: langFRM.validacion_ok,
                        sticky: false,
                        time: '3000',
                        class_name: 'gritter-success'
                    });
                    var modo_llamada = $("[name=modo_llamada]").val();
                    switch (modo_llamada){
                        case 'llamada_desde_alumno':                            
                            cerrarFancyDesdeRazonesSociales(respuesta.custom.codigo_razon_social, respuesta.custom.nombre_razon_social);
                            break;                            
                        default:
                            oTable.fnDraw();
                            $.fancybox.close(true);
                            break;
                    }                    
                }
            }
        });
        return false;
    });

    $("#telefonosRazones").on("click", "#btn-ok-cambio-estado", function(){
        cerrarFrmTelefonos();
        return false;
    });
});