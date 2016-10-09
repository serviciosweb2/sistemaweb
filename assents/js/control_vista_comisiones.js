$(document).ready(function() {
   
    //CONFIGURACION DE LA TABLA:
    
    oTable =$('#academicoComisiones').dataTable({
        "bProcessing": false,// LO PUSE EN FALSE MOMENTANEAMENTE
        "bServerSide": false,// LO PUSE EN FALSE MOMENTANEAMENTE
        "sAjaxSource": "http://localhost/sistemasiga/comisiones/datos",
        "sServerMethod": "POST",
        "bJQueryUI": true,
        "sPaginationType": "full_numbers",
        "aoColumns": [ 
                        /*codigo */{ "bVisible": true,
                        "bSearchable": true},
			/* Engine */   null,
                        
			/* Browser */  null,
			/* Platform */ { "bSearchable": false,
			                 "bVisible":    false },
			/* Version */  { "bVisible":    true },
			/* Grade */    null
		]
        
    } );
    
   
    
    // CAPTURA DEL EVENTO CLICK DERECHO:
    
         $('#areaTablas').on('mousedown','#academicoComisiones tbody tr',function(e){
               //var valor=$(this).attr('oncontextmenu');
               //alert(valor);
                if( e.button === 2 ) { 
                    //alert('Boton derecho!'); 
                     var x=e.clientX;//CORDENADAS DEL MOUSE(averiguo la ubicacion para desplegar el menu)
                     var y=e.clientY;
                     var nTds = $('td', this);
                     //$(nTds[0]).parent().css('background','#BDBDBD');
                     var sBrowser = $(nTds[1]).text();
                     var sGrade = $(nTds[4]).text();
                     var codigo=$(nTds[0]).text();
                     
                     despliegaMenu(x,y,codigo);
                     return false; 
                }
               
             
             
             //FUNCION DESPLIEGA MENU:  
                
                  function despliegaMenu(x,y,codigo){
                   
                    $('#desplegable').remove();
                    var contenido='<div id="desplegable" class="span2"><div class="row-fluid">';
                    contenido+='<div class="span12"><a id="'+codigo+'" accion="modificar" href="#">modificar</div><div class="row-fluid"><div class="span12"><a id="'+codigo+'" accion="presupuestar" href="#">presupuestar</a></div></div><div class="row-fluid"><div class="span12"><a id="'+codigo+'" accion="pasar a alumno" href="#">pasar a alumno</a></div></div></div></div>';
                    $('#contenedorTablas').before(contenido);
                    $('#desplegable').css({
                        "margin-top":y,"margin-left":x-18
                      });
                  }
             });
             
             
             //FUNCION QUE TOMA CLICK EN EL MENU DESPLEGABLE:
             
             
             $('body').on('click','#desplegable a',function(){
                 var accion=$(this).attr('accion');
                 var id=$(this).attr('id');
                 $('#desplegable').remove();
                 alert('click en: '+accion+' codigo= '+id);
                 return false;
             });
             
             
             //FUNCION QUE TOMA LOS CLICK EN EL MENU FIJO EN LA CABEZERA DE LA TABLA:
             
             $('body').on('click','#acciones a',function(){
                 var accion=$(this).attr('accion');
                 alert('click en : '+accion);
                 return false;
             });
    
  } );


