 lang = BASE_LANG;

aoColumnDefs = columns;

menu = BASE_MENU_JSON;
codigo = '';
 function init()
    {
    
    oTable =$('#reserva_inscripcion').dataTable({
         
                "bServerSide": true,
                "sAjaxSource": BASE_URL+'reservas/listarReservasInscripcionesDataTable',
                "aaSorting": [[ 0, "desc" ]],
                "sServerMethod": "POST",
                'aoColumnDefs': aoColumnDefs
        
    });
    marcarTr();
    $('#reserva_inscripcion').wrap( "<div class='table-responsive'></div>" );

   $('#areaTablas').on('mouseup','#reserva_inscripcion tbody tr',function(e){
        var sData = oTable.fnGetData( this );
               
        if( e.button === 2 ) {         
            codigo=sData[0];
       
            generalContextMenu(menu.contextual,e);
        }
               
    });
    
     //FUNCION QUE TOMA CLICK EN EL MENU DESPLEGABLE:
             
             
    $('body').on('click','#menu a',function(){
    
    // SETEO VARIABLES PARA EL ENVIO
                 
        var accion=$(this).attr('accion');
                 
       
    
        $('#menu').remove();
                 
            switch(accion){
                     
               
                case 'ver_detalle_reserva':
            
                    $.ajax({
                            url:BASE_URL+'reservas/frm_detalle_reserva',
                            type:'POST',
                            data:'cod_reserva='+codigo,
                            cache:false,
                            success:function(respuesta){

                                $.fancybox.open(respuesta,
                                    {

                                        scrolling       :'auto',
                                        height:'auto',
                                        width:'auto',
                                        padding         : 0,
                                        openEffect      :'none',
                                        closeEffect     :'none',
                                        helpers:  {
                                            overlay : null
                                        }

                                     });

                                   }
                               
                           });
            
                break;
                       default:
                           $.gritter.add({
                                title: 'Upps',
                                text: ' NO TIENE PERMISO !',
                                sticky: false,
                                time: '3000',
                                class_name: 'gritter-error'
                            });

                       break;
                 }
                     
               
               
                    return false;
             });

            }

$(document).ready(function() {
       

    init();

});


