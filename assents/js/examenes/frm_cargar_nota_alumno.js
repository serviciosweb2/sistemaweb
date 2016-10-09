 var langFRM='';
 var tablaNotas = '';
$('.fancybox-wrap').ready(function(){
    var clavesFRM=Array("validacion_ok","alumno_ausente","notas_guardadas");
   
    $.ajax({
        url:BASE_URL+'entorno/getLang',
        data:"claves=" + JSON.stringify(clavesFRM),
        type:"POST",
        dataType:"JSON",
        async:false,
        cache:false,
        success:function(respuesta){
            langFRM=respuesta;
            initFRM();
        }
    });
 });


function initFRM(){
    tablaNotas = $('#notas').dataTable();
    tablaNotas.$('select').chosen({
           width : '100 %'
        });
    $('.fancybox-wrap #cargarNotas').on('click', 'input[type="checkbox"][name^="examenes"]', function(){
       
            if($(this).is(':checked')){
               $(this).closest('tr').find("input[name*='examenes']:not(type='hidden')").not(this).val('').prop('readonly',true);
            } else {
                $(this).closest('tr').find("input[name*='examenes']").not(this).prop('readonly',false);
            }
        });
    

    
    $('.fancybox-wrap').on('submit','#cargarNotas',function(){
        
      
        var dataPOST=$(this).serialize();
        //alert(dataPOST);
        $.ajax({
            url:BASE_URL+'examenes/guardarNotaAlumno',
            data:dataPOST+'&codigo='+codigo,
            type:'POST',
            dataType:"JSON",
            cache:false,
            success:function(respuesta){
                    console.log('respuesta',respuesta);
                    if(respuesta.codigo == 1){
                        gritter(langFRM.notas_guardadas, true);
                        $.fancybox.close();
                    } else {
                         gritter(respuesta.msgerror);
                    }
                
            }
        });
        return false;
    });
}