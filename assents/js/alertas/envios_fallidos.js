var tableAlerta = '';
  var langFRM='';
   
function eliminarAlertas(){
   var data =  tableAlerta.$('input[name="eliminar_alerta[]"]').serialize();
   $.ajax({
            url: BASE_URL+"alertas/bajaAlertaAlumnos",
            type: "POST",
            data: data,
            dataType:"JSON",
            cache:false,
            success:function(respuesta){
                if(respuesta.codigo == 1){
                    $.gritter.add({
                    title: langFRM.BIEN,
                    text: langFRM.alertas_dadas_baja,
                    sticky: false,
                    time: '3000',
                    class_name: 'gritter-success'
                });
                borrarSeleccionados();
                }else{
                    $.gritter.add({
                    title: '',
                    text: langFRM.ocurrio_error,
                    sticky: false,
                    time: '3000',
                    class_name: 'gritter-error'
                });
                }
            }
        });
          return false;
}

function borrarSeleccionados(){
    tableAlerta.$('input[name="eliminar_alerta[]"]:checked').each(function(key,input){
           var tr =  $(input).closest('tr');
           tableAlerta.fnDeleteRow(tr);
        });
}

function seleccionarTodos(element){
    var check = '';
    if($(element).is(':checked')){
       check = true;
    }else{
        check = false;
    }
    tableAlerta.$('input[name="eliminar_alerta[]"]').prop('checked',check);
}

function checkTodos(){
     tableAlerta.$('input[name="eliminar_alerta[]"]').prop('checked',true);
     $('input[name="seleccionar_todos"]').prop('checked',true);
}
function desCheckTodos(){
    tableAlerta.$('input[name="eliminar_alerta[]"]').prop('checked',false);
        $('input[name="seleccionar_todos"]').prop('checked',false);
}

function descheckear(element){
    var seleccionar = '';
    if(!$(element).is(':checked')){
        seleccionar = false;
    }else{
        seleccionar = true;
    }
    $('input[name="seleccionar_todos"]').prop('checked',seleccionar);
}

$(document).ready(function(){
    
   tableAlerta = $("#envios_alertas_fallidos").dataTable();
   
    var clavesFRM=Array("validacion_ok",'ocurrio_error','alertas_dadas_baja','BIEN');

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
    function initFRM(){

        



    } 
});
