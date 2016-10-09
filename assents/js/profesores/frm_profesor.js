var formProf = false;
var fromRaz = false;
var tab = "#home";
var vista = 0;
var langFRM = langFrm;
var oTableRazones='';
var oTableTelefonos='';
var claves = Array("error_fecha", "error_requerido", "error_max_100", "error_numeros", "error_email", "error_max_50", "error_fecha",
    "eliminar", "nuevo_tel", "nueva_razon", "sinTelefono", "no_se_puede_eliminar_un_telefono_default");

function validar(elemento){
    var validacion=$(elemento).val();
    if (validacion.match(/^[a-z]+$/i)){
        $(elemento).parent().removeClass('has-error');
    } else {
       $(elemento).parent().addClass('has-error');
    }
};

function setTelefono(){
    var tr,cod_area,numero;
    var defaultTel=langFRM.sinTelefono;
    $(oTableTelefonos.$('input[name="telefonos[default]"]')).each(function(k,element){
        if($(element).is(':checked') && $(element).is(':visible')){
            tr = $(element).closest('tr');
            cod_area = $(tr).find('input[name$="[cod_area]"]').val();
            numero = $(tr).find('input[name$="[numero]"]').val();
            defaultTel = cod_area+'-'+numero;
       }
    });
    $('.file-name').attr('data-title',defaultTel);
}
    
$('.fancybox-wrap').ready(function(){
    init();
});

function init() {
    $('select').chosen({
        width:'100%'
    });
    $('#myTab li').click(function(e){
        $.fancybox.update();
    });
    $('select[name="provincia"]').change(function(){
        var valor = $(this).val();
        $.ajax({
            url: BASE_URL + 'profesores/getlocalidades',
            data: {
                idprovincia: valor
            },
            type: 'POST',
            dataType: 'json',
            cache: false,
            success: function(respuesta){
                $('select[name="cod_localidad"]').empty();
                $(respuesta).each(function() {
                    $('select[name="cod_localidad"]').append('<option value="' + this.id + '">' + this.nombre + '</option>');
                });
                $('select[name="cod_localidad"]').trigger("chosen:updated");
            }
        });
    });
    var razonSociales = JSON.parse($('input[name="razonSociales"]').val());
    var empresas_tel = JSON.parse($('input[name="empresas_tel"]').val());
    var telefonos = JSON.parse($('input[name="telefonos"]').val());
    var condiciones = JSON.parse($('input[name="condiciones"]').val());
    var tipoTelefonos = JSON.parse($('input[name="tipoTelefonos"]').val());
    var tipo_dni = JSON.parse($('input[name="tipo_dni"]').val());
    
    function tablaRazonInit(){
        oTableRazones = $('#tablaRazon').dataTable({
            "aaData": aaDataRazones(),
            "iDisplayLength": 5,
            "bInfo": false
        });
        $('#tablaRazon_length').html('<button class="btn btn-primary nuevaRazon">' + langFRM.nueva_razon + '</button>');
        $('#tablaRazon_length').parent().addClass('no-padding');
    }
    
    function addRazon(){
        var key = oTableRazones.fnSettings().aoData.length;
        var retorno = [];
        var selectCondicion = function(key){
            var slt = '<select class="form-control" name="razonesSociales[razonesSociales][' + key + '][condicion]">';
            $(condiciones).each(function(k,condicion){
                var selected = '';
                slt += '<option value="' + condicion.codigo + '" ' + selected + '>' + condicion.condicion + '</option>';
            });
            slt += '</select>';
            return slt;
        };
        var selectIdentificacion=function(key){
            var slt = '<select class="form-control" name="razonesSociales[razonesSociales][' + key + '][tipo_documento]">';
            $(tipo_dni).each(function(k,doc){
                var selected = '';
                slt += '<option value="' + doc.codigo + '" '+ selected + '>' + doc.nombre + '</option>';
            });
            slt += '</select>';
            return slt;
        };
        var razon = [
            selectCondicion(key) + '<input type="hidden" name="razonesSociales[razonesSociales][' + key + '][codigo]" value="-1" readonly>',
            selectIdentificacion(key),
            '<input name="razonesSociales[razonesSociales][' + key + '][documento]" class="form-control inputTable no-margin" value="" readonly>',
            '<input name="razonesSociales[razonesSociales][' + key + '][razon_social]" class="form-control inputTable no-margin" value="" readonly>',
            '<input type="radio" class="razonDefault" value="' + key + '" name="razonesSociales[default]">',
            '<button class="eliminarRazon btn btn-primary btn-xs">' + langFRM.eliminar + '</button><input type="hidden" value="0" name="razonesSociales[razonesSociales]['+key+'][baja]">'
        ];
        oTableRazones.fnAddData(razon);
        retorno.push(razon);
        $('select').chosen({
            width:'100%'
        });
        return retorno;
    }
    
    function aaDataRazones(){
        var retorno=[];
        $(razonSociales).each(function(key, razon){
            var checked = razon.default == 1 ? 'checked' : '';
            var editable = razon.default == 1 ? '' : 'inputTable';
            var optionDisabled = razon.default == 1 ? 'disabled' : '';
            var selectCondicion = function(key){
                var slt='<select class="form-control" name="razonesSociales[razonesSociales]['+key+'][condicion]">';
                $(condiciones).each(function(k,condicion){
                    var selected = razon.condicion == condicion.codigo ? 'selected'  :'';
                    var x = razon.condicion == condicion.codigo ? '' : optionDisabled;
                    slt += '<option value="' + condicion.codigo + '" '+selected + ' ' + x + '>' + condicion.condicion + '</option>';
                });
                slt += '</select>';
                return slt;
            };
            var selectIdentificacion = function(key){
                var slt = '<select class="form-control" name="razonesSociales[razonesSociales][' + key + '][tipo_documento]">';
                $(tipo_dni).each(function(k,doc){
                    var x = doc.codigo == razon.tipo_documentos ? '' : optionDisabled;
                    var selected= doc.codigo == razon.tipo_documentos ? 'selected' : '';
                    slt += '<option value="' + doc.codigo + '" '+selected + ' ' + x + '>' + doc.nombre + '</option>';
                });
                slt += '</select>';
                return slt;
            };
            var razon=[
                selectCondicion(key) + '<input type="hidden" name="razonesSociales[razonesSociales][' + key + '][codigo]" value="' + razon.codigo + '" readonly>',
                selectIdentificacion(key),
                '<input name="razonesSociales[razonesSociales][' + key + '][documento]" class="form-control ' + editable + ' no-margin" value="' + razon.documento + '" readonly>',
                '<input name="razonesSociales[razonesSociales][' + key + '][razon_social]" class="form-control ' + editable + ' no-margin" value="' + razon.razon_social + '" readonly>',
                '<input type="radio" class="razonDefault" value="' + key + '" name="razonesSociales[default]" ' + checked + '>',
                '<button class="eliminarRazon btn btn-primary btn-xs" ' + optionDisabled + '>' + langFRM.eliminar + '</button><input  type="hidden" value="' + razon.baja + '" name="razonesSociales[razonesSociales][' + key + '][baja]">'
            ];
            retorno.push(razon);
        });
        return retorno;
    }
    
    function deleteRazones(tr){
        $(tr).parent().find('input[type="hidden"]').val(1);
        var codRazon=$(tr).closest('tr').find('input[type="hidden"]').eq(0).val();
        codRazon == -1 ? $(tr).closest('tr').find('input, select').prop('readonly',false).prop('disabled',true) : '';
        $(tr).closest('tr').hide();
    }

    function aaDataTel(){
        var retorno=[];
        $(telefonos).each(function(key,telefono){
            var checked= telefono.default== 1 ? 'checked' : '';
            var optionDisabled='';
            var selectTipo=function(key){
                var slt='<select class="form-control" name="telefonos[telefonos]['+key+'][tipo]">';
                $(tipoTelefonos).each(function(k,tipo){
                    var selected= tipo.id == telefono.tipo_telefono ? 'selected'  :'';
                    var x= '';
                    slt+='<option value="'+tipo.id+'" '+selected+' '+x+'>'+tipo.nombre+'</option>';
                });
                slt+='</select>';
                return slt;
            };
            var selectNombreEmpresa=function(key){
                var slt='<select class="form-control" name="telefonos[telefonos]['+key+'][empresa]">';
                $(empresas_tel).each(function(k,empresa){
                    var x= '';
                    var selected= empresa.codigo == telefono.empresa? 'selected' : '';
                    slt+='<option value="'+empresa.codigo+'" '+selected+' '+x+'>'+empresa.nombre+'</option>';
                });
                slt+='</select>';
                return slt;
            };
            var tel=[
                '<input  name="telefonos[telefonos]['+key+'][cod_area]" class="form-control inputTable no-margin" value="'+telefono.cod_area+'" readonly="readonly"><input name="telefonos[telefonos]['+key+'][codigo]" type="hidden" value="'+telefono.codigo+'">',
                '<input  name="telefonos[telefonos]['+key+'][numero]" class="form-control inputTable no-margin" value="'+telefono.numero+'" readonly="readonly">',
                selectNombreEmpresa(key),
                selectTipo(key),
                '<input type="radio" name="telefonos[default]" value="'+key+'" '+checked+'>',
                '<button class="eliminarTelefono btn btn-primary btn-xs" '+optionDisabled+'>Eliminar</button><input  type="hidden" value="'+telefono.baja+'" name="telefonos[telefonos]['+key+'][baja]">'
            ];
            retorno.push(tel);
        });
        return retorno;
    }

    function tablaTelInit(){
        oTableTelefonos=$('#tablaTelefonos').dataTable({
            "aaData":aaDataTel(),
            "iDisplayLength": 5,
            "bInfo": false
        });
        $('#tablaTelefonos_length').parent().addClass('no-padding');
        $('#tablaTelefonos_length').html('<button class="btn btn-primary nuevoTel">'+langFRM.nuevo_tel+'</button>');
    }

    function deleteTelefono(element){
        $(element).parent().find('input[type="hidden"]').val(1);
        var codTel= $(element).closest('tr').find('input[type="hidden"]').eq(0).val();
        codTel == -1 ? $(element).closest('tr').find('input, select').prop('readonly',false).prop('disabled',true) : '' ;
        $(element).closest('tr').hide();
    }
    
    function addTelefono(){
        var retorno=[];
        var key=oTableTelefonos.fnSettings().aoData.length;
        var checked='';
        var editable= '';
        var optionDisabled= '';
        var selectTipo=function(key){
            var slt='<select class="form-control" name="telefonos[telefonos]['+key+'][tipo]">';
            $(tipoTelefonos).each(function(k,tipo){
                var selected= '';
                var x= '';
                slt+='<option value="'+tipo.id+'" '+selected+' '+x+'>'+tipo.nombre+'</option>';
            });
            slt+='</select>';
            return slt;
        };
        var selectNombreEmpresa=function(key){
            var slt='<select class="form-control" name="telefonos[telefonos]['+key+'][empresa]">';
            $(empresas_tel).each(function(k,empresa){
                var x= '';
                var selected= '';
                slt+='<option value="'+empresa.codigo+'" '+selected+' '+x+'>'+empresa.nombre+'</option>';
            });
            slt+='</select>';
            return slt;
        };
        var checked='checked';
        if (oTableTelefonos.find('input[type="radio"]').is(':visible')){
            checked='';
        }
        var tel=[
            "<input value='' name='telefonos[telefonos]["+key+"][cod_area]' class='form-control inputTable no-margin'  readonly='readonly'><input name='telefonos[telefonos]["+key+"][codigo]' type='hidden' value='-1'>",
            '<input value="" name="telefonos[telefonos]['+key+'][numero]" class="form-control inputTable no-margin"  readonly="readonly">',
            selectNombreEmpresa(key),
            selectTipo(key),
            '<input type="radio" name="telefonos[default]" value="'+key+'" '+checked+'>',
            '<button class="eliminarTelefono btn btn-primary btn-xs" '+optionDisabled+'>'+langFRM.eliminar+'</button><input  type="hidden" value="0" name="telefonos[telefonos]['+key+'][baja]">'
        ];
        oTableTelefonos.fnAddData(tel);
        $('select').chosen({
            width:'100%'
        });
    }

    tablaRazonInit();
    tablaTelInit();
    $('#tablaTelefonos').wrap('<form id="telefonosProfesor"></form>');
    $('#tablaRazon').wrap('<form id="razonesProfesor"></form>');
    $('#tablaRazon').on('click','.eliminarRazon',function(){
        deleteRazones(this);
        return false;
    });
    $('.fancybox-wrap').on('click','.nuevaRazon',function(){
        addRazon();
        return false;
    });
    
    $('#razonesProfesor').on('focus','.inputTable',function(){
        $(this).prop('readonly',false);
        $('#razonesProfesor .inputTable').not(this).prop('readonly',true);
        return false;
    });
    
    $('.fancybox-wrap').on('click','.razonDefault',function(){
        $(this).prop('checked',false);
        return false;
    });

    $('#tablaTelefonos').on('click','input[type="radio"]',function(){
        if($(this).is(':checked')){
            oTableTelefonos.$('input[name="telefonos[default]"]').not(this).prop('checked',false);
        }
    });
    
    $('#tablaTelefonos').on('focus','.inputTable',function(){
        $(this).prop('readonly',false);
        $('#tablaTelefonos .inputTable').not(this).prop('readonly',true);
        return false;
    });
    
    $('#tablaTelefonos').on('click','.eliminarTelefono',function(){
        var radio = $(this).closest('tr').find('input[name="telefonos[default]"]');
        if (radio.is(':checked')){
            if ($(this).closest('tr').find('input[name$="[codigo]"]').val() == -1){
                deleteTelefono(this);
            } else {
                $.gritter.add({
                    text: langFRM.no_se_puede_eliminar_un_telefono_default,
                    sticky: false,
                    time: '3000',
                    class_name:'gritter-error'
                });
            }
        } else {
           deleteTelefono(this);
        }
        return false;
    });
    
    $('#tablaTelefonos_wrapper').on('click','.nuevoTel',function(){
        addTelefono();
        return false;
    });
    
    $('.ace-file-input').on('click','',function(){
        $('#detalleTelefonos').modal();
        return false;
    });
    
    $('.fancybox-wrap').on('click','button[type="submit"]',function(){
        $.ajax({
            url:BASE_URL+'profesores/guardar',
            type:"POST",
            data:$('#telefonosProfesor, #formProfesores, #razonesProfesor').serialize(),
            cache:false,
            dataType:"JSON",
            success:function(respuesta){
                if(respuesta.codigo==1){
                    $.gritter.add({
                        title: 'OK!',
                        text: 'Guardado Correctamente',
                        sticky: false,
                        time: '3000',
                        class_name:'gritter-success'
                    });
                    $.fancybox.close(true);
                    oTable.fnDraw();
                } else {
                    $.gritter.add({
                        title: 'Upps!',
                        text: respuesta.msgerror,
                        sticky: false,
                        time: '3000',
                        class_name:'gritter-error'
                    });
                }
            }
        });
        return false;
    });
}