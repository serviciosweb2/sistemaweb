var paises={
    1:'ar',
    2:'br',
    3:'uy',
    4:'py',
    5:'ve',
    6:'bo',
    7:'cl',
    8:'co',
    9:'pa',
    10:'us'
};
function setMascaraIdentificacion(tipo_ident,elem_a_enmasc,ejecuteFunction){
    var mascara = 'h{1,30}';
    switch(tipo_ident){
        case "1":   // DNI Argentina
            mascara = ['*{2}.*{3}.*{3}','*{3}.*{3}.*{3}'];
            break;

        case "2":   // Pasaporte Argentina
            mascara = 'h{1,30}';
            break;

        case "3":   // cuit ARGENTINA
            mascara = '*{2}-*{8}-*{1}';
            break;

        case "4":   //CUIL ARGENTINA
            mascara = '*{2}-*{8}-*{1}';
            break;

        case "5":   // RG brasil //12.123.123 e 12.123.123-1
//            mascara = ['*{3}.*{3}.*{3}','*{3}.*{3}.*{3}-h{1}'];
            break;

        case "6":   // CNPJ brasil
            mascara = ['*{2}-*{3}-*{3}/*{4}-*{2}','*{3}-*{3}-*{3}/*{4}-*{2}'];
            break;

        case "7":   // CI chile
                    //mascara = ['*{2}-*{3}-*{3}-h{1}','*{3}-*{3}-*{3}-h{1}'];
            mascara = ['*{1}-*{3}-*{3}-h{1}','*{2}-*{3}-*{3}-h{1}','*{3}-*{3}-*{3}-h{1}'];
            break;

        case "8":   // DNI paraguay
            mascara = ['*{2}.*{3}.*{3}','*{3}.*{3}.*{3}'];
            break;

        case "9":// CI venezuela
            break;

        case "10":// CI bolivia
            break;

        case "11":// CI panama
            mascara = ['h{2}-*{3}-*{3}','h{3}-*{3}-*{3}'];
            break;

        case "12":// PAS brasil
            mascara = 'h{1,30}';
            break;

        case "13":// PAS uruguay
            mascara = 'h{1,30}';
            break;

        case "14":// PAS paraguay
            mascara = 'h{1,30}';
            break;

        case "15":// PAS venezuela
            mascara = 'h{1,30}';
            break;

        case "16":// PAS bolivia
            mascara = 'h{1,30}';
            break;

        case "17":// PAS chile
            mascara = 'h{1,30}';
            break;

        case "18":// PAS colombia
            mascara = 'h{1,30}';
            break;

        case "19":// PAS panama
            mascara = 'h{1,30}';
            break;

        case "20":// PAS usa
            mascara = 'h{1,30}';
            break;

        case "21":   // CPF brasil
            mascara = '*{3}-*{3}-*{3}-*{2}';
            break;

        case "23":              //RUC Paraguay
//             mascara = '*{6}-*{1}';
            mascara = 'n{1,30}';
            break;

        case "63":   // CI de Chile en Bolivia
            mascara = ['*{1}-*{3}-*{3}-h{1}','*{2}-*{3}-*{3}-h{1}','*{3}-*{3}-*{3}-h{1}'];
            break;

        case "64":   // DNI Uruguay
            mascara = '*{1}.*{3}.*{3}-h{1}';
            break;

        default:
            break;
    }

    $(elem_a_enmasc).prop('readonly',false);
    $(elem_a_enmasc).inputmask({
        mask:mascara,
        greedy: false,
        onBeforePaste: function(pastedValue) {
            //return pastedValue = pastedValue.toLowerCase(), pastedValue.replace("mailto:", "");
        },
        onKeyPress:function(){
            if(ejecuteFunction){
                //ejecuteFunction();
            }
        },
        onincomplete:function(valor){
            if(ejecuteFunction){
                ejecuteFunction();
            }
        },

        oncomplete: function(valor){
            if(ejecuteFunction){
                ejecuteFunction();
            }
        },
        'autoUnmask' : true,

        definitions: {
            "*": {
                validator: "[0-9]",
                cardinality: 1,
                casing: "lower"
            },
            'h':{
                validator: "[0-9A-Za-z]",
                cardinality: 1
            },
            'n':{
                validator: "[0-9]|-",
                cardinality: 1
            }

        }
    });
}

function limpiarTelefono(tipo,tel,pais=null){
    var pattern = new RegExp("^([0-9]{1})$");
    var partes = [];

    switch(pais) {
        case "ar":
            cod_area = 1;
            break;
        case "br":
            cod_area = 2;
            break;
        case "uy":
            cod_area = 3;
            break;
        case "py":
            cod_area = 4;
            break;
        case "ve":
            cod_area = 5;
            break;
        case "bo":
            cod_area = 6;
            break;
        case "cl":
            cod_area = 7;
            break;
        case "co":
            cod_area = 8;
            break;
        case "pa":
            cod_area = 9;
            break;
        case "us":
            cod_area = 10;
            break;
        default:
            cod_area = BASE_PAIS;
    }

    switch(cod_area){
        case 1://ARGENTINA
            if(tipo=='fijo'){
                var partes = tel.split(" ");
            } else {
                var partes = tel.split(" ");
            }
            break;

        case 2://BRASIL
            if(tipo == 'fijo'){
                partes = tel.split(" ");
            } else {
                partes = tel.split(" ");
            }            
            break;

        case 3://URUGUAY            
            //  var preModelado = [];
            partes=['',tel];
            break;

        case 4:// PARAGUAY
            if(tipo == 'fijo'){
                partes = tel.split(" ");
            } else {
                partes = tel.split(" ");
            }
            break;

        case 5:// venezuela
            if(tipo == 'fijo'){
                partes = tel.split("-");
            } else {
                partes = tel.split("-");
            }

            break;
        case 6 ://bolivia
            partes=['',tel];
            break;

        case 7 :// chile
            break;

        case 8:// colombia
            if(tipo == 'fijo'){
                partes = tel.split(" ");
            } else {
                partes = tel.split(' ');
            }
            break;

        case 9: //Panama
            if(tipo == 'fijo'){
                partes = tel.split(")");
            } else {
                partes = tel.split('-');
            }
            break;

        case 10:// usa
            if(tipo == 'fijo'){
                partes = tel.split(")");
            } else {
                partes = tel.split(')');
            }
            break;
    }

    var obj = {'0':'','1':''};
    for(var e in partes){
        for( var i in partes[e]){
            var caracter = partes[e][i];
            if(pattern.test(caracter)){
                obj[e]+=caracter;
            }
        }
    }
    return obj;
}

function  validacionIngresoTelefonos(instacia){
    var retorno = true;
    /*----VALIDACION DE TELEFONOS INVALIDOS---*/
    var telInvalidos = instacia.$('.inputError');
    var telDefault = instacia.$('input[name$="[default]"][value="1"]');
    if( telInvalidos.length > 0 ){
        retorno = false;
        return {codigo:retorno,respuesta:langFRMAlumnos.tel_formato_invalido};
    }
    /*----VALIDACION DE TELEFONO DEFAULT VACIO---*/
    if(telDefault.length > 0){
        var valorDefault = $(telDefault).closest('tr').find('input[name$="[numero]"]').val();
        if(valorDefault == ''){
            retorno = false;
            return {codigo:retorno,respuesta:langFRMAlumnos.telefono_default_vacio};
        }
    }
    return {codigo:retorno};
}

function validacionIngresoTelRazones(instacia){
    var retorno = true;
    /*----VALIDACION DE TELEFONOS INVALIDOS---*/
    var telInvalidos = instacia.$('.inputError');
    var telDefault = instacia.$('input[name$="[numero]"]').eq(0);
    if( telInvalidos.length > 0 ){
        retorno = false;
        return {
            codigo:retorno,
            respuesta:langFRM.tel_formato_invalido
        };
    }
    /*----VALIDACION DE TELEFONO DEFAULT VACIO---*/
    if(telDefault.length > 0){
        var valorDefault = $(telDefault).closest('tr').find('input[name$="[numero]"]').val();
        if(valorDefault == ''){
            retorno = false;
            return {codigo:retorno,respuesta:langFRM.completar_primer_telefono};
        }
    }
    return {codigo:retorno};
}

function setMascaraFecha(pais,element_mascarar,ejecuteFunction){
    var  mascara = '*{2}/*{2}/*{4}';
    switch (pais){
        case "1":   // DNI Argentina
            mascara = '*{2}/*{2}/*{4}';
            break;
    }
    $(element_mascarar).inputmask({
        mask:mascara,
        greedy: false,
        onBeforePaste: function(pastedValue) {
            //return pastedValue = pastedValue.toLowerCase(), pastedValue.replace("mailto:", "");
        },
        onKeyPress:function(){
            if(ejecuteFunction){
                //ejecuteFunction();
            }
        },
        onincomplete:function(valor){
            if(ejecuteFunction){
                ejecuteFunction();
            }
        },

        oncomplete: function(valor){
            if(ejecuteFunction){
                ejecuteFunction();
            }
        },
        'autoUnmask' : true,
        definitions: {
            "*": {
                validator: "[0-9]",
                cardinality: 1,
                casing: "lower"
            },
            'h':{
                validator: "[0-9]",
                cardinality: 1
            }
        }
    });
}
