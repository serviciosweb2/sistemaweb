$(document).ready(function() {
   
    //CONFIGURACION DE LA TABLA:
    
    oTable =$('#academicoAspirantes').dataTable({
        "bProcessing": false,// LO PUSE EN FALSE MOMENTANEAMENTE
        "bServerSide": false,// LO PUSE EN FALSE MOMENTANEAMENTE
        "sAjaxSource": "http://localhost/sistemasiga/aspirantes/datos",
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
    
         $('#areaTablas').on('mousedown','#academicoAspirantes tbody tr',function(e){
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
                     
                     despliegaMenu(x,y,codigo,sBrowser,sGrade);
                     return false; 
                }
               
             
             
             //FUNCION DESPLIEGA MENU:  
                
                  function despliegaMenu(x,y,codigo,sBrowser,sGrade){
                   
                    $('#desplegable').remove();
                    var contenido='<div id="desplegable" class="span2"><div class="row-fluid">';
                    contenido+='<div class="span12"><a id="'+codigo+'" accion="modificar" nombre='+sBrowser+' apellido='+sGrade +' href="#">modificar</div><div class="row-fluid"><div class="span12"><a id="'+codigo+'" accion="presupuestar" nombre='+sBrowser+' apellido='+sGrade +' href="#">presupuestar</a></div></div><div class="row-fluid"><div class="span12"><a id="'+codigo+'" accion="pasar a alumno" nombre='+sBrowser+' apellido='+sGrade +' href="#">pasar a alumno</a></div></div></div></div>';
                    $('#contenedorTablas').before(contenido);
                    $('#desplegable').css({
                        "margin-top":y,"margin-left":x-18
                      });
                  }
             });
             
             
             //FUNCION QUE TOMA CLICK EN EL MENU DESPLEGABLE:
             
             
             $('body').on('click','#desplegable a',function(){
                 // INICIO VARIABLES PARA EL ENVIO
                 var accion=$(this).attr('accion');
                 var id=$(this).attr('id');
                 var nombre=$(this).attr('nombre');
                 alert(nombre);
                 var apellido=$(this).attr('apellido');
                 var valores='nombre='+nombre+'&apellido='+apellido+'&codigo='+id;
                 $('#desplegable').remove();
                 
                 switch(accion){
                     
                     case 'modificar':
                       
                          $.ajax({
                               url: BASE_URL + 'aspirantes/form_modificar_aspirante',
                               type:'POST',
                               data:valores,
                               cache:false,
                               success:function(respuesta){
                                   $('#drag').html(respuesta);
                                   $('#drag').fadeIn();
                                   
                               }
                               
                           });
                         
                             break;
                      
                       case 'presupuestar':
                          
                           $.ajax({
                               url: BASE_URL + 'aspirantes/presupuestar_aspirante',
                               type:'POST',
                               data:valores,
                               cache:false,
                               success:function(respuesta){
                                   $('#drag').html(respuesta);
                                   $('#drag').fadeIn();
                               }
                               
                           });
                         
                         break;
                        
                          default:
                             
                               //alert(nombre+' '+id+' '+apellido) ;
                             $.ajax({
                               url: BASE_URL + 'aspirantes/form_pasar_alumno',
                               type:'POST',
                               data:valores,
                               cache:false,
                               success:function(respuesta){
                           alert(respuesta);
                                   $('#drag').html(respuesta);
                                   $('#drag').fadeIn();
                                   
                               }
                               
                           });
                       
                 }
                     
                     
                 
                 //alert('click en: '+accion+' codigo= '+id);
                 return false;
             });
             
             
             //FUNCION QUE TOMA LOS CLICK EN EL MENU FIJO EN LA CABEZERA DE LA TABLA:
             
             
             $('body').on('click','#acciones a',function(){
                
                 var accion=$(this).attr('accion');
                 //alert('click en : '+accion);
                 $.fancybox.showLoading();
                 
                 $.ajax({
                     url: BASE_URL + 'aspirantes/form_nuevo_aspirante/',
                     cache:false,
                     type:'GET',
                     success:function(respuesta){
                        
                         $.fancybox.open(respuesta);
                     }
                 });
                 return false;
             });
             
             
      
    
  } );