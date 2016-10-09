var sData = '';
var codigo = '';
var estado = '';
var oTableUsuarios ='';
var menu = menuJson;
var lang = BASE_LANG;
function cambiarEstado() {
    var motivo = 'Alta de usuario';
    $.ajax({
        url: BASE_URL + 'usuarios/cambioEstadoUsuario',
        data: 'cod_usuario=' + codigo+ '&motivo='+ motivo,
        type: 'POST',
        cache: false,
        dataType: 'json',
        success: function(respuesta) {


            if (respuesta.codigo === 1) {
                $.gritter.add({
                    title: lang.BIEN,
                    text: lang.MATRICULA_HABILITADA,
                    sticky: false,
                    time: '3000',
                    class_name: 'gritter-success'
                });

                oTableUsuarios.fnDraw();

            } else {

                gritter(lang.ocurrio_error);


            }



        }

    });


}
$(document).ready(function(){
    
    oTableUsuarios='';
//TRADUCCIONES
var claves = Array('cambio_estado_usuario',"codigo_cobro","mediopago_cobro","habilitar-factura","deshabilitar-factura","MATRICULA_HABILITADA","facturacion_codigo","facturacion_estado","facturacion_anular","HABILITADO","INHABILITADO", "caja_default","INHABILITAR","HABILITAR");
$.ajax({
    url:BASE_URL+'entorno/getLang',
    data:"claves=" + JSON.stringify(claves),
    dataType:'JSON',
    type:'POST',
    cache:false,
    async:false,
   success:function(respuesta){
       lang=respuesta;
       


//MENU'S
//        $.ajax({
//            url:BASE_URL+'entorno/getMenuJSON',
//            data:'seccion=configuracion',
//            dataType:'JSON',
//            type:'POST',
//            cache:false,
//            async:false,
//            success:function(respuesta){
//                //console.log(respuesta);
//                menu=respuesta;
//                
// //NOMBRE Y ORDEN DE COLUMNAS               
//                    $.ajax({
//                        url:BASE_URL+'usuarios/getColumns',
//                        data:'',
//                        dataType:'JSON',
//                        type:'POST',
//                        cache:false,
//                        async:false,
//                        success:function(respuesta){
//                            //console.log(respuesta);
//                            aoColumnDefs=respuesta;
//                            init();
//
//                }
//              });
//               
//            }
//          });
          
        $.ajax({
               url:BASE_URL+'usuarios/getColumns',
               data:'',
               dataType:'JSON',
               type:'POST',
               cache:false,
               async:false,
               success:function(respuesta){
                   //console.log(respuesta);
                   aoColumnDefs=respuesta;
                   init();

       }
     });

        }
  }); 
 
 
 
function init(){
     
     
function getEstado(valor){
    mensaje=lang.HABILITADO;
    clase='label-success';
    
    
   if(valor!=false){
       
     mensaje=lang.INHABILITADO;
     clase='label-danger';
   }
    
 return '<span class="label '+clase+' arrowed">'+mensaje+'</span>';
}
    

function renderUsuarios(){
    
    oTableUsuarios=$('#tablaUsuarios').dataTable({
        
        "bProcessing": false,
        "bFilter": true,
        "aaSorting":[[ 0, "desc" ]],
        "bServerSide": true,
        "sAjaxSource": BASE_URL+"usuarios/listarUsuarios",
        "sServerMethod": "POST",
        'aoColumnDefs':aoColumnDefs,
        "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            
            var  imgTag = getEstado(aData[5]);
           
            $(nRow).find('td').eq(5).html(imgTag);
            
            return nRow;
        }
    });
    
    
    $(".dataTables_length").html(generarBotonSuperiorMenu(menu.superior,'btn-info',''));
    
}
     
     
   renderUsuarios();  
   console.log(menu);
   
   
     $('#usuarios').on('mousedown','#tablaUsuarios tbody tr',function(e){
         sData = oTableUsuarios.fnGetData( this );
         estado = sData[5];
         codigo = sData[0];
       // var nTds = $('td', this);
                
        codigo=sData[0];
            
        if( e.button === 2 ) { 
            
            generalContextMenu(menu.contextual,e);
            switch (estado) {

                    case '0' :
                       
                        $('a[accion="cambio_estado_usuario"]').text(lang.INHABILITAR);

                        break;

                    case '1':
                        
                        $('a[accion="cambio_estado_usuario"]').text(lang.HABILITAR);

                        break;
                        
                }
            
             return false; 
        }
               
             
             
    
      } );
     
     
 }
 
 
 $('body').on('click','#menu a',function(){
     
     var accion=$(this).attr('accion');
     console.log(accion);
     $('#menu').remove();
     switch(accion){
         
         case'modificar_usuario':
         
            var frm=$('<form>',{
                id:'modificar',
                method:'post',
                action:BASE_URL+'configuracion/frm_usuarios'
            }).append('<input type="hidden" value="'+codigo+'" name="cod_usuario">');
            
            $('body').append(frm);
            
            $('#modificar').submit();
         
             break;
             
            case 'cambio_estado_usuario':
            
            if(estado == 0){
                $.ajax({
                url:BASE_URL+'usuarios/frm_baja',
                type:"POST",
                data:"cod_usuario="+codigo,
                dataType:"",
                cache:false,
                success:function(respuesta){
               
                      $.fancybox.open(respuesta,{
                                      
                                        scrolling       :'auto',
                                        autoSize	: false,
                                        width   	: 'auto',
                                        height      	: 'auto',
                                        padding         : 1,
                                        openEffect      :'none',
                                        closeEffect     :'none',
                                        helpers:  {
                                                    overlay : null,
                                                   
                                                    }                                     
                                 });
                }
            });
            }else{
                cambiarEstado();
            }
             
            
            
            break;
         
         
     }
     
     return false;
     
 });



$('#usuarios').on('click','#tablaUsuarios_length .boton-primario',function(){
        
    var accion = $(this).attr('accion');
    var formu = null;

    switch(accion){
        
        case 'nuevo_usuario':
            
             formu = $('<form>',{
                id:'modificar',
                method:'post',
                action:BASE_URL+'configuracion/frm_usuarios'
            }).append('<input type="hidden" value="-1" name="cod_usuario">');

            $(this).append(formu);
            $(formu).submit();

            
        break;
        
       
        
        
    }
 
    
    return false;
    
});

$('#usuarios').on('click','#tablaUsuarios_length .dropdown-menu a',function()
{
    var accion = $(this).attr('accion');
    var formu = null;
    
    switch(accion)
    {
        case 'perfil':
            
            //window.location.href = BASE_URL+'configuracion/frm_usuario';
            
            formu = $('<form>',{
                id:'modificar',
                method:'post',
                action:BASE_URL+'configuracion/frm_usuarios'
            }).append('<input type="hidden" value="'+cod_usuario_logiado+'" name="cod_usuario">');

            $(this).append(formu);
            $(formu).submit();
            
            break;
    }
    
    return false;
});


/*@USUARIOS*/
    
    
   
    
    
});


