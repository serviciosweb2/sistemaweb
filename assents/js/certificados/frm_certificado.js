 
var langFRM = langFrm;    
    
var oTableCertificados = '';
    var   cod_alumno = '';

function actualizarSelect(nombre,options){
    var nuevasOpciones='<option></option>';
    var  pHolder;
    switch(nombre){
        case 'certificados[]':
            pHolder= options.certificados.length > 0 ? 'seleccione' : 'no hay resultados';
            $(options.certificados).each(function(k,certificado){
                nuevasOpciones+="<option value='"+JSON.stringify(certificado)+"'>"+certificado.descripcion+"</option>";
            });
            break;
    }
    $('select[name="'+nombre+'"]').empty().attr('data-placeholder',pHolder).html(nuevasOpciones).trigger('chosen:updated');
}

function initFRM(){
    $('select[name="certificados[]"]').chosen({
       width:'100%'
    });

    $('select[name="alumno"]').ajaxChosen({
        minLength: 0,
        queryLimit: 10,
        delay: 100,
        chosenOptions: {width:'100%',max_selected_options: 1},
        searchingText: langFRM.buscando,
        noresultsText: langFRM.no_hay_resultados,
        initialQuery: true
    },

    function (options, response){
        $.ajax({
            url: BASE_URL+'alumnos/getAlumnosHabilitadosSelect',
            type: "POST",
            data:{buscar:options.term,estado:'habilitada'},
            dataType:"JSON",
            cache:false,
            success:function(respuesta){
                var terms = {};
                $.each(respuesta, function(i, val) {
                    terms[val.codigo]=val.nombreapellido;
                });
                response(terms);
            }
        });
    });

    $('select[name="alumno"]').on('change',function(){
        cod_alumno=$(this).val();
        $.ajax({
            url: BASE_URL+"certificados/getCertificados",
            type: "POST",
            data:  'cod_alumno='+cod_alumno,
            dataType:"JSON",
            cache:false,
            success:function(respuesta){
                actualizarSelect('certificados[]',respuesta);
                $.fancybox.update(); 
            }
        });
    });


    $('#frmNuevoPedido').on('submit',function(){
        $.ajax({
            url:  BASE_URL+"certificados/guardarCertificado",
            type: "POST",
            data: "cod_alumno="+cod_alumno+'&'+$('select[name="certificados[]"]').serialize(),
            dataType:"JSON",
            cache:false,
            async: false,
            success:function(respuesta){
                if(respuesta.codigo==1){
                    $.fancybox.close(true);
                    $('a[href="#pendiente"]').trigger('click');
                    refrescarTabla('pendiente');
                    gritter(lang.validacion_ok,true);
                }else{
                    gritter(respuesta.respuesta);
                }
            }
        });
        return false;
    });
}

$('.fancybox-wrap').ready(function(){
    initFRM();
});