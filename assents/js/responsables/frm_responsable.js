function getResponsable(codigo)
{
    var retorno = '';
    $.ajax({
        url: BASE_URL+"responsables/getResponsable",
        type: "POST",
        data: {'codigo':codigo},
        dataType:"",
        cache:false,
        async: false,
        success:function(respuesta)
        {   
            console.log('RESPONSABLES POR AJAX',respuesta);
            var respuestaJSON = JSON.parse(respuesta);
            retorno = respuestaJSON
            
        }
    });
    
    return retorno;
};

function vaciarArrayRespSeleccionados()
{
    respoSeleccionadosListar=[];
}

function relacionarResponsablesListar()
{
    var respYaAsignados = oTableResponsable1.$('input[name="responsables[]"]').serializeJSON();
    
    console.log('RESPONSABLES ASIGNADOS',respYaAsignados);
    
    for(var i in respoSeleccionadosListar)
    {
        var seleccionado = respoSeleccionadosListar[i];
        var relacionar = true;
        
        for(var a in respYaAsignados.responsables)
        {
            var respAsignado= respYaAsignados.responsables[a];
           
            var porAsignar = i;
            
            if(respAsignado == porAsignar)
            {
                relacionar = false;
            }
        }
        
        
        if(relacionar!=false)
        {
             var obj = 
            {
                responsable:{
                    codigo:i,
                    nombre:seleccionado.nombre,
                    'tipo_doc':seleccionado.nombre_identificacion,
                    'documento':seleccionado.documento,
                    'email':seleccionado.email,
                    'baja':0
                }
            }
            addResponsable(obj);
        }else
        {
            //alert('ya existe '+seleccionado.nombre);
        }
       
        
    }
    vaciarArrayRespSeleccionados();
   
}

function selecionarListarResponsable(element)
{
    var cod_resp = $(element).val();
    
    if($(element).is(':checked'))
    {
        var tr = oTableListarResponsables.$(element).closest('tr');
        
        var myData = oTableListarResponsables.row( tr ).data(); 
        
        respoSeleccionadosListar[cod_resp] = 
        {
            'nombre':myData[1],
            'nombre_identificacion':myData[2],
            'documento':myData[3],
            'email':myData[4]
        };
    }
    else
    {
       delete  respoSeleccionadosListar[cod_resp];
    }
    
    console.log('SALIDA',respoSeleccionadosListar);
}

function frmListarResponsable()
{   
    var columns= [{"sTitle":"C\u00f3digo","sName":0,"aTargets":[0],"bVisible":true,"bSearchable":true,"bSortable":true,"sClass":"","mRender":null,"sWidth":null},
        {"sTitle":"Nombre y Apellido","sName":1,"aTargets":[1],"bVisible":true,"bSearchable":true,"bSortable":true,"sClass":"","mRender":null,"sWidth":null},
        {"sTitle":"Tipo Doc.","sName":2,"aTargets":[2],"bVisible":true,"bSearchable":true,"bSortable":true,"sClass":"","mRender":null,"sWidth":null},
        {"sTitle":"Nro Documento","sName":3,"aTargets":[3],"bVisible":true,"bSearchable":true,"bSortable":true,"sClass":"","mRender":null,"sWidth":null},
        {"sTitle":"Email","sName":4,"aTargets":[4],"bVisible":true,"bSearchable":true,"bSortable":true,"sClass":"","mRender":null,"sWidth":null},
        {"sTitle":"Tipo Doc.","sName":5,"aTargets":[5],"bVisible":false,"bSearchable":true,"bSortable":false,"sClass":"","mRender":null,"sWidth":null},
        {"sTitle":"Estado","sName":6,"aTargets":[6],"bVisible":true,"bSearchable":true,"bSortable":false,"sClass":"","mRender":null,"sWidth":null},
        {"sTitle":"Estado","sName":7,"aTargets":[7],"bVisible":false,"bSearchable":true,"bSortable":false,"sClass":"","mRender":null,"sWidth":null}]
    
    
    if ( ! $.fn.DataTable.isDataTable( '#tabla_listar_responsables' ) ) 
    {
            oTableListarResponsables = $('#tabla_listar_responsables').DataTable(
            {
            'aoColumnDefs': columns,
            "sAjaxSource": BASE_URL + 'responsables/listar',
            "sServerMethod": "POST",
            "pageLength": 5,
            "lengthChange": false,
            "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) 
            {

                //var estado = aData[6];

                console.log(aData[7]);

                //imgTag = devolverEstado(estado);
                var checked = '';
                if(respoSeleccionadosListar[aData[0]])
                {
                    checked='checked';
                }
                $('td:eq(0)', nRow).html('<input type="checkbox" onclick="selecionarListarResponsable(this)" name="responsables_relacionar[]" value="'+aData[0]+'" '+checked+'>');
                $('td:eq(5)', nRow).html(aData[7] == 1 ? 'inhabilitado' : 'habilitado');

               return nRow;
            },
        });
    }else
    {
       oTableListarResponsables.draw(); 
    }
    
    $('#frm_listar_responsables').modal();
}

function resetearFormResponsable()
{
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
    $('input[name="numero_documento_responsable"]').inputmask('remove');
    
    $('input[name="telefono_default_responsable"]').val('').inputmask('remove');
    
    //deshabilitarFormResponsable(true);
}

function getCondicionesSocialesResponsable()
{
    $("[name=condicion_fiscal_responsable]").find("option").remove();
    $("[name=condicion_fiscal_responsable]").append("<option value='-1'>(" + 'RECUPERANDO' + "...)</option>");
    $("[name=condicion_fiscal_responsable]").prop("disabled", true);
    $("[name=condicion_fiscal_responsable]").trigger("chosen:updated");
    var tipo_identificador = $("[name='tipo_documento_responsable']").val();
    $.ajax({
       url: BASE_URL + 'razonessociales/getCondicionesSociales',
       type: 'POST',
       dataType: 'json',
       data: {
           tipo_identificador: tipo_identificador
       }, 
       success: function(_json){
           if (_json.length > 0){
               $("[name=condicion_fiscal_responsable]").find("option").remove();
               $.each(_json, function(key, value){
                    $("[name=condicion_fiscal_responsable]").append("<option value=" + value.codigo + ">" + value.condicion + "</option>");
               });
               $("[name=condicion_fiscal_responsable]").prop("disabled", false);
               $("[name=condicion_fiscal_responsable]").trigger("chosen:updated");
           } else {
               $("[name=condicion_fiscal_responsable]").find("option").remove();
               $("[name=condicion_fiscal_responsable]").append("<option value=''>(" + 'sin registros' + ")</option>");
               $("[name=condicion_fiscal_responsable]").trigger("chosen:updated");
           }
           $('input[name="numero_documento_responsable"]').prop('disabled',false);
       }
    });
}

function deshabilitarFormResponsable(valor)
{
    var input = $('select[name="tipo_documento_responsable"]').closest('.form-group').find('input');
    $('#form_responsable input,select,buttton').not('select[name="tipo_documento_responsable"]').not(input).prop('disabled',valor);
    $('#form_responsable select').trigger('chosen:updated');
}

function guardarResponsable()
{
    
    var responsable = $('#form_responsable').serializeJSON();
    
    var myResponsable ={};
    
    myResponsable.codigo = responsable.codigo_responsable;
    
    myResponsable.tipo_doc = responsable.tipo_documento_responsable;
    myResponsable.documento = limpiarDocumento(responsable.numero_documento_responsable) ;
    
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
    myResponsable.relacion_alumno = responsable.relacion_alumno_responsable;
    myResponsable.fecha_naci = responsable.fecha_nacimiento_responsable;
    
    
    myResponsable.telefonos = JSON.stringify(responsableEnEdiccion.telefono);

    
    console.log(myResponsable);
    
    
    $.ajax({
            url: BASE_URL+"responsables/guardarResponsable",
            type: "POST",
            data:myResponsable,
            dataType:"",
            cache:false,
            success:function(respuesta)
            {
                console.log('VALIDACION RESPONSABLE',respuesta);
                
                var respuestaJSON = JSON.parse(respuesta);
                
                if(respuestaJSON.codigo == 1)
                {
                    //actualizar la lista de responsable
                    var nuevoResponsable = getResponsable(respuestaJSON.cod_responsable);
                    console.log('SACO LOS DATOS DE ESTE OBJETO',nuevoResponsable);
                    var row = {
                        responsable:{
                        
                        'codigo':nuevoResponsable.cod_responsable,
                        'nombre':nuevoResponsable.razon_social ,
                        'tipo_doc':nuevoResponsable.nombre_identificacion,
                        'documento':nuevoResponsable.documento,
                        'email':nuevoResponsable.email,
                        'baja':nuevoResponsable.baja_responsable
                        
                            
                    }
                };
                    
                    addResponsable(row);
                    resetearFormResponsable();
                }
                else
                {
                    //mostrar errores
                    console.log('error al guardar reponsable');
                }
            }
        });
    
    
    
}

function deleteResponsable(row) 
{
//    for(var i in responsables)
//    {
//        if(i == row)
//        {// encuentra el responsable
//            
//            if(responsables[i].responsable.codigo == '-1')
//            {
//                // se quita del array;
//                console.log('BORRAR ESTE VALOR',responsables[i]);
//                delete responsables[i];
//                
//            }
//            else
//            {// se cambia "baja" y se deja en el array para postear
//                
//                responsables[i].responsable.baja = 1;
//            }
//           
//           listarResponsables();
//        }
//    }
    oTableResponsable1.fnDeleteRow(row);
    
}

function setMascara(element)
{
   var tipo = $(element).val();
   
   $('input[name="telefono_default_responsable"]').val('');
   
   aplicarMascaraTelefono(BASE_PAIS,tipo,'input[name="telefono_default_responsable"]',setTelefonoDefaultResponsableEnArray);

};

function getTelDefaulResponsable(responsable)
{
    var retorno = langFRMAlumnos.no_tiene_telefono_default;
    $(responsable.telefono).each(function(k, T) {
        if (T.default == 1) {
            retorno = T.cod_area + '-' + T.numero;
        }
    });
    return retorno;
}


