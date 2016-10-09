var idioma = BASE_IDIOMA;
if (typeof fechaFormato == 'function') {
    function fechaFormato(idioma) {
        if (idioma == 'en') {
            return 'MM/DD/YYYY';
        } else {
            return 'DD/MM/YYYY';
        }
    }
}

function fechaFormato(idioma) {
    var idioma = BASE_IDIOMA;
    if (idioma == 'en') {
        return 'MM/DD/YYYY';
    } else {
        return 'DD/MM/YYYY';
    }
}

function abrirCalendario(element){    
    $(element).find('input[name="fecha"]').focus();    
};

function initFRM(){
    $('.tablaAlumnos').hide();
    $('#examen select').chosen({width:"100%"});     
    $('.horaInput').timepicker({
        minuteStep: 1,
        showSeconds: true,
        showMeridian: false
     }).next().on(ace.click_event, function(){
        $(this).prev().focus();
    });   
    var day = new Date();
//    if (tieneNotasCargadas){
//        $('input[name="fecha"]').datepicker({
//            autoclose: true,
//            maxDate: new Date(day.getUTCFullYear(), day.getUTCMonth(), day.getUTCDate())
//        });
//    } else {
//        $('input[name="fecha"]').datepicker({
//            autoclose: true
//        });
//    }
    $('input[name="fecha"]').datepicker({
        autoclose: true
    });
    oTableDetalle=$('#tablaDetallesAlumnos').dataTable();
    var estado= $('input[name="codigo"]').val() != -1 ? false : true;     
    $('.detalle').prop('disabled',estado);
    var containerCurso=$('select[name="Curso"]').parent().parent();
    var containerComision=$('select[name="Comision"]').parent().parent();
    
	$('select[name="tipoExamen"]').on('change',function(){
        var valor=$(this).val();
		
		if (valor == 'RECUPERATORIO_PARCIAL') {
			$("#row_examen_padre").show();
		}
		else
		{
			$("#row_examen_padre").hide();
		}
    });    
    
    $('select[name="Curso"]').on('change',function(){
        var valor=$(this).val();
        $.ajax({
            url:BASE_URL+'examenes/getComisionesCurso',
            data:'codigo='+valor,
            type:'POST',
            dataType:'JSON',
            cache:false,
            success:function(respuesta){
                $('select[name="Comision"]').empty();
                $('select[name="Comision"]').append('<option value=""></option>');
                $('select[name="materia"]').empty();
                $('select[name="examen_padre"]').empty();
                $(respuesta).each(function(k,obj){
                    $('select[name="Comision"]').append('<option value="'+obj.codigo+'">'+obj.nombre+'</option>');
                });
                $('select[name="Comision"]').trigger('chosen:updated');
                $('select[name="materia"]').trigger('chosen:updated');
                $('select[name="examen_padre"]').trigger('chosen:updated');
            }
        });
    });
    
    $('select[name="Comision"]').on('change',function(){
        var valor=$(this).val();       
        codComision=valor;
        var nombreMateria=$('input[name="nombreMateria"]').val();
        $.ajax({
            url:BASE_URL+'examenes/getMateriasComision',
            data:'codigo='+valor,
            type:'POST',
            dataType:'JSON',
            cache:false,
            success:function(respuesta){              
                $('select[name="materia"]').empty().append('<option></option>');           
                var dataPlaceholder=!respuesta[0] ? 'NO HAY RESULTADOS' : 'SELECCIONE MATERIA';
                $('select[name="materia"]').attr('data-placeholder',dataPlaceholder);                
                $(respuesta).each(function(k,obj){
                    $('select[name="materia"]').append('<option value="'+obj.codigo+'">'+obj.nombre_es+'</option>');
                });                
                $('select[name="materia"]').trigger('chosen:updated');     
            }
        });
    });
    
    
    $('select[name="tipoExamen"]').on('change',function(){
        var valor=$(this).val();
        var materia=$('select[name="materia"]').val();
        var codComision=$('select[name="Comision"]').val();
        if (materia != null){
            alumnos=[];
            $.ajax({
                url:BASE_URL+'examenes/getInscriptosComisionMaterias',
                data:'cod_materia='+materia+'&codigo='+codComision+'&tipoExamen='+valor,
                type:'POST',
                dataType:'JSON',
                cache:false,
                success:function(respuesta){
                    $('#verInscriptos').prop('disabled',false).parent().effect( "shake",'slow');
                    oTableDetalle.fnDestroy();              
                    $('#tablaDetallesAlumnos tbody').empty();
                    $(respuesta).each(function(){              
                        alumnos.push(this.codigo);                  
                        var tr="<tr><td><input type='hidden' name='alumnos[]' value='"+JSON.stringify(this)+"'>"+this.nombre_apellido+"</td></tr>";
                        $('#tablaDetallesAlumnos').append(tr);
                    });
                    var x =alumnos.length == 0 ? true : false;
                    $('.detalle').prop('disabled',x);                
                    oTableDetalle=$('#tablaDetallesAlumnos').dataTable();
                    $('#tablaDetallesAlumnos').css('width','100%');
                }          
            });
        }
    });

    function updateAlumnosAInscribirEnParcial(tipo_examen, cod_materia, cod_comision, cod_examen_padre) {
        var valor = cod_materia;
        var codComision = cod_comision;
        var tipoExamen = tipo_examen;

        var data = 'cod_materia='+valor+'&codigo='+codComision+'&tipoExamen='+tipoExamen;

        if (cod_examen_padre) {
            data += '&cod_examen_padre='+cod_examen_padre;
        }

        $.ajax({
            url:BASE_URL+'examenes/getInscriptosComisionMaterias',
            data: data,
            type: 'POST',
            dataType:'JSON',
            cache: false,
            success: function(respuesta){
                $('#verInscriptos').prop('disabled',false).parent().effect( "shake",'slow');
                oTableDetalle.fnDestroy();              
                $('#tablaDetallesAlumnos tbody').empty();
                $(respuesta).each(function(){              
                    alumnos.push(this.codigo);                  
                    var tr="<tr><td><input type='hidden' name='alumnos[]' value='"+JSON.stringify(this)+"'>"+this.nombre_apellido+"</td></tr>";
                    $('#tablaDetallesAlumnos').append(tr);
                });
                var x =alumnos.length == 0 ? true : false;
                $('.detalle').prop('disabled',x);                
                oTableDetalle=$('#tablaDetallesAlumnos').dataTable();
                $('#tablaDetallesAlumnos').css('width','100%');
            }
        });
    }
    
    alumnos=[];
    $('select[name="materia"]').on('change',function(){
        var valor=$(this).val();
        var tipoExamen=$('select[name="tipoExamen"]').val();
        var codComision=$('select[name="Comision"]').val();
        var cod_materia = $('select[name="materia"]').val();
        alumnos=[];

        if (tipoExamen !== 'RECUPERATORIO_PARCIAL') {
            updateAlumnosAInscribirEnParcial(tipoExamen, cod_materia, codComision);
        }
        else
        {
            $.ajax({
                url: BASE_URL+'examenes/getParcialesPasadosDeMateriaParaComision',
                data: 'cod_materia='+cod_materia+'&cod_comision='+codComision,
                type: 'POST',
                dataType: 'JSON',
                cache: false,
                success: function(respuesta) {
                  //  console.log(respuesta);
                    $('select[name="examen_padre"]').empty().append('<option></option>');           
                    dataPlaceholder = (!respuesta[0]) ? 'NO HAY RESULTADOS' : 'SELECCIONE EXAMEN';
                    $('select[name="examen_padre"]').attr('data-placeholder', dataPlaceholder);

                    materiaSeleccionada = $('select[name="materia"]').val();
                    materiaSeleccionada = $('select[name="materia"]>option[value="'+materiaSeleccionada+'"').text();

                    $(respuesta).each(function(k,obj) {
                        //Se quita el formato de fecha porque da error
                        //tmp_date = obj.fecha;
                        tmp_date = moment(obj.fecha).format(fechaFormato(idioma));
                        console.log(obj.fecha);
                        console.log(idioma);

                        $('select[name="examen_padre"]').append('<option value="'+obj.codigo+'">'+materiaSeleccionada+' - ' + tmp_date +'</option>');

                    });
                    
                    $('select[name="examen_padre"]').trigger('chosen:updated');
                }
            });
        }
    });

    $('select[name="examen_padre"]').on('change', function() {
        var tipoExamen=$('select[name="tipoExamen"]').val();
        var codComision=$('select[name="Comision"]').val();
        var cod_materia = $('select[name="materia"]').val();
        var cod_examen_padre = $('select[name="examen_padre"]').val();

        updateAlumnosAInscribirEnParcial(tipoExamen, cod_materia, codComision, cod_examen_padre);
    });
    
    $('.fancybox-wrap').on('click','.detalle',function(){        
        $('#stack1').modal('show');        
        return false;
    });
    
    $('.fancybox-wrap').on('click','button[name="guardar"]',function(){        
        $('#examen').submit();
        return false;
    });

    $('.fancybox-wrap').on('submit','#examen',function(){      
        //var dataPOST=$('#examen').serialize();

        var form_examen = $('#examen');

        // se habilitan temporalmente los campos para obtener los valores de los select disabled
        var disabled = form_examen.find(':input:disabled').removeAttr('disabled');

        // serializacion del formulario
        var dataPOST = form_examen.serialize();

        // se deshabilitan los campos
        disabled.attr('disabled','disabled');

        $.ajax({
            url:BASE_URL+'examenes/guardarExamen',
            data:dataPOST,
            type:'POST',
            dataType:'JSON',
            cache:false,
            success:function(respuesta){
                if(respuesta.codigo==0){
                    $.gritter.add({                                    
                        text: respuesta.msgerror,
                        sticky: false,
                        time: '3000',
                        class_name:'gritter-error'
                    });                  
                } else {
                    $.gritter.add({                                   
                        text: langFRM.validacion_ok,
                        sticky: false,
                        time: '3000',
                        class_name:'gritter-success'
                    });                  
                    $.fancybox.close();
                    tablaParciales.refresh();
                }
            }
        });
        return false;
    });
}

var langFRM = langFrm;
$('.fancybox-wrap').ready(function(){
    initFRM();
});