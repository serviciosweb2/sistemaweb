var cantidad_telefonos = 0;
var cantidad_razones = 0;
var frm_proveedores = langFrm;

$($("#frm_proveedores")).ready(function(){
   
    console.log(frm_proveedores);
    
    initialize();
    
    $('#inicio_actividad').datepicker({yearRange: "1920:2014",
        changeMonth: true,
        changeYear: true});
});

function initialize(){
    
    $('select').chosen({
        width:'100%'
    });
    
    cantidad_telefonos = $("[name=hidden_telefono]").val();
    cantidad_razones = $("[name=hidden_razones]").val();
    
    $('#tablaTelefonosProveedores').on('click', 'input[type="radio"]',function(){
        $('#tablaTelefonosProveedores input[type="radio"]').prop('checked',false).val(0);
        $(this).prop('checked',true).val(1);
    });
    
    $('#tablaRazonesProveedores').on('click', 'input[type="radio"]',function(){
        $('#tablaRazonesProveedores input[type="radio"]').prop('checked',false).val(0);
        $(this).prop('checked',true).val(1);
    });
    
    $("#tablaTelefonosProveedores").on('click', '.eliminarTelefonoProveedor', function(){
        borrarTelefono(this);
        return false;
    });
    
    $("#tablaRazonesProveedores").on('click', '.elimiarRazonSocialProveedor', function(){
        borrarTelefono(this);
    });
    
    $("[name=nuevo_telefono]").on("click", function(){        
        var str = '<tr>';
        str += '<input type="hidden" name="telefonos[' + cantidad_telefonos + '][telefono_codigo]" value="-1">';
        str += '<td>';
        str += '<input value="" class="form-control inputTable no-margin" name="telefonos[' + cantidad_telefonos + '][cod_area]">';
        str += '</td>';
        str += '<td>';
        str += '<input value="" class="form-control inputTable no-margin" name="telefonos[' + cantidad_telefonos + '][numero]">';
        str += '</td>';
        str += '<td>';
        str += '<select class="form-control" name="telefonos[' + cantidad_telefonos + '][empresa]">';        
        $("#empresas_tel_hd option").each(function(){
            str += '<option value="' + $(this).attr('value') + '">';
            str += $(this).text();
            str += '</option>';
         });        
        str += '</select>';
        str += '</td>';
        str += '<td>';
        str += '<select class="form-control" name="telefonos[' + cantidad_telefonos + '][tipo_telefono]">';        
        $("#tipo_telefonos_hd option").each(function(){
            str += '<option value="' + $(this).attr("value") + '">';
            str += $(this).text();
            str += '</option>';
        });        
        str += '</select>';
        str += '</td>';
        str += '<td>';
        str += '<input type="radio" name="telefonos[' + cantidad_telefonos + '][default]" value="-1">';
        str += '</td>';
        str += '<td>';
        str += '<button name="eliminar_telefono_proveedor" class="eliminarTelefonoProveedor btn btn-primary btn-xs">Eliminar</button>';
        str += '</td>';
        str += '</tr>';        
        $("#tbody_telefonos").append(str);
        cantidad_telefonos ++;
    });
    
    
    $("[name=nueva_razon]").on("click", function(){
        var str = '<tr>';
        str += '<input type="hidden" name="razones[' + cantidad_razones + '][razon_codigo]" value="-1">';
        str += '<tr>';
        str += '<td>';
        str += '<select class="form-control" name="razones[' + cantidad_razones + '][condicion]">';
        $("#condiciones_facturacion_hd option").each(function(){
            str += '<option value="' + $(this).attr("value") + '">';
            str += $(this).text();
            str += '</option>';
        });
        str += '</select>';
        str += '</td>';
        str += '<td>';
        str += '<select class="form-control" name="razones[' + cantidad_razones + '][tipo_doc]">';
        $("#tipo_identificadores_hd option").each(function(){
            str += '<option value="' + $(this).attr("value") + '">';
            str += $(this).text();
            str += '</option>';
        });
        str += '</select>';
        str += '</td>';
        str += '<td>';
        str += '<input value="" class="form-control inputTable no-margin" name="razones[' + cantidad_razones + '][documento]">';
        str += '</td>';
        str += '<td>';
        str += '<input value="" class="form-control inputTable no-margin" name="razones[' + cantidad_razones + '][razon_social]">';
        str += '</td>';
        str += '<td>';
        str += '<input type="radio" name=razones[' + cantidad_razones + '][default]" value="">';
        str += '</td>';
        str += '<td>';
        str += '<button name="eliminar_razon_social" class="elimiarRazonSocialProveedor btn btn-primary btn-xs">Eliminar</button>';
        str += '</td>';
        str += '</tr>';
        $("#tbody_razones").append(str);
        cantidad_razones++;
    });
    
    $("[name=enviarForm]").on("click", function(){
        $.ajax({
            url: BASE_URL + 'proveedores/guardar',
            type: 'POST',
            dataType: 'json',
            data: $("#form_general, #form_telefonos, #form_razones_sociales").serialize(),
            success: function(_json){
                if (_json.codigo == 0){
                    gritter(_json.msgerror);
                } else {
                    gritter(frm_proveedores.proveedor_guardado_correctamente, true);
                    $.fancybox.close(true);
                    oTableProveedores.fnDraw();
                }
            }
        });
    });
    
    $("[name=provincia]").on("change", function(){
        $("[name=cod_localidad]").empty();
        $("[name=cod_localidad]").append('<option value="-1">(' + frm_proveedores.recuperando + ')</option>');
        $("[name=cod_localidad]").attr("disabled", true);
        var idprovincia = $("[name=provincia]").val();
        $.ajax({
            url: BASE_URL + 'alumnos/getlocalidades',
            type: 'POST',
            dataType: 'json',
            data: {
                idprovincia: idprovincia
            },
            success: function(_json){
                $("[name=cod_localidad]").empty();
                $.each(_json, function(key, value){
                     $("[name=cod_localidad]").append('<option value="' + value.id + '">' + value.nombre + '</option>');
                });
               $("[name=cod_localidad]").attr("disabled", false);
               $("[name=cod_localidad]").trigger("chosen:updated");
            }
        });
    });    
}

function borrarTelefono(tr){           
    if ($(tr).closest('tr').find('input[type="radio"]').attr("checked")){
        gritter(frm_proveedores.no_puede_borrar_el_telefono_predeterminado);
    } else {    
        $(tr).closest('tr').empty();
    }       
};