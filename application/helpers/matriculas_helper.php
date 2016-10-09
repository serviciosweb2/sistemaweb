<script>
$(document).ready(function() {

    //CONFIGURACION DE LA TABLA:
    
    oTable =$('#academicoMatriculas').dataTable({
        "bProcessing": false,// LO PUSE EN FALSE MOMENTANEAMENTE
        "bServerSide": false,// LO PUSE EN FALSE MOMENTANEAMENTE
        "sAjaxSource": "<?=base_url('matriculas/listar')?>",
        "sServerMethod": "POST",
        "bJQueryUI": true,
        "sPaginationType": "full_numbers",
        "sDom": '<"toolbar">frtip',
         "aoColumns": [ 
                                    /*codigo */{ "bVisible": false,
                                                "bSearchable": false,
                                                "bSortable":false},
			
                        /* Nombre Apellido */   {"bSearchable": true,
                                                "bSortable":true},
                        
                                /* Telefono */  {"bSearchable": false,
                                                "bSortable":false},
                                    
                                    /* email */ { "bSearchable": false,
                                                "bVisible":    true ,
                                                "bSortable":false},
			
                               /* Localidad */  { "bSearchable": false,
                                                   "bSortable":false 
                                                },
                            
                            /* Fecha alta */    {"bSearchable": false,
                                                 "bSortable":true},
                            /* descativado */    {"bSearchable": false,
                                                    "bVisible": false,
                            
                                                 "bSortable":true}
                                           
                    ]
        
    } );
    $("div.toolbar").html('<?=session_menu_superior('matriculas','menu_superior')?>');
   
    
    // CAPTURA DEL EVENTO CLICK DERECHO:
    var codigo='';
    var desactivado='';
         $('#areaTablas').on('mousedown','#academicoMatriculas tbody tr',function(e){
               //var valor=$(this).attr('oncontextmenu');
               //alert(valor);
               
                 var sData = oTable.fnGetData( this );
                if( e.button === 2 ) { 
                    //alert('Boton derecho!'); 
                     var x=e.clientX;//CORDENADAS DEL MOUSE(averiguo la ubicacion para desplegar el menu)
                     var y=e.clientY;
                     var nTds = $('td', this);
                     //$(nTds[0]).parent().css('background','#BDBDBD');
                     var sBrowser =sData[1];
                     var sGrade = sData[4];
                      desactivado=sData[6];
                     //alert(desactivado);
                      codigo=sData[0];
                     
                     despliegaMenu(x,y,codigo);
                     if(desactivado==1){// si el alumno esta desactivado
                         $('a[accion="eliminar_alumnos"]').css({
                         'color':'gainsboro',
                         'cursor':'not-allowed'
                         }).attr('accion','');
                         
                       }
                      $('#desplegable').hide().fadeIn('fast');
                     return false; 
                }
               
             
             
             //FUNCION DESPLIEGA MENU:  
                
                  function despliegaMenu(x,y,codigo){
                   
                    $('#desplegable').remove();
                   // var contenido='<div id="desplegable" class="span2"><div class="row-fluid">';
                    //contenido+='<div class="span12"><a id="'+codigo+'" accion="modificar" href="#">modificar</div><div class="row-fluid"><div class="span12"><a id="'+codigo+'" accion="presupuestar" href="#">presupuestar</a></div></div><div class="row-fluid"><div class="span12"><a id="'+codigo+'" accion="pasar a alumno" href="#">pasar a alumno</a></div></div></div></div>';
                    var contenido='<div id="desplegable" class="span2"><?=session_menu_contextual('matriculas')?></div></div></div>';
            
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
                // alert(codigo);
                 //alert('click en: '+accion+' codigo= '+codigo);
                 switch(accion){
                     case 'modificar_alumnos':
                            $.ajax({
                                url:'<?=base_url('alumnos/form_alumnos')?>',
                                data:'codigo='+codigo+'&persona=1',
                                type:'POST',
                                cache:false,
                                success:function(respuesta){
                                      $.fancybox.open(respuesta,{
                                        maxWidth	: 1000,
                                        maxHeight	: 1000,
                                        scrolling       :false,
                                        
                                        width   	: '100%',
                                        height      	: '100%',
                                        autoSize	: true,
                                        padding         : 8,
                                        wrapCSS :'fancy_custom'
                                 });
                                }
                                
                            });
                        break;
                        
                        case 'eliminar_alumnos':
                            //alert ('entro');
                                $.ajax({
                                url:'<?=base_url('alumnos/desactivar')?>',
                                data:'codigo='+codigo+'&persona=1',
                                type:'POST',
                                cache:false,
                                success:function(respuesta){
//                                      $.fancybox.open(respuesta,{
//                                        maxWidth	: 1000,
//                                        maxHeight	: 1000,
//                                        scrolling       :false,
//                                        
//                                        width   	: '100%',
//                                        height      	: '100%',
//                                        autoSize	: true,
//                                        padding         : 8,
//                                        wrapCSS :'fancy_custom'
//                                 });
                                    alert('RESPUESTA:\n'+respuesta);
                                }
                                
                            });        
                        break
                        
                        
                 }
                 return false;
             });
             
             
             
             
             
                      $('body').on('click','.toolbar a',function(){
                 var accion=$(this).attr('accion');
                 var id=$(this).attr('id');
                 $('#desplegable').remove();
                // alert(codigo);
                 //alert('click en: '+accion+' codigo= '+codigo);
                 switch(accion){
                     case 'nuevo_alumnos':
                            $.ajax({
                                url:'<?=base_url('alumnos/form_alumnos')?>',
                                data:'codigo=-1'+codigo+'&persona=2',
                                type:'POST',
                                cache:false,
                                success:function(respuesta){
                                      $.fancybox.open(respuesta,{
                                        maxWidth	: 1000,
                                        maxHeight	: 1000,
                                        scrolling       :false,
                                        
                                        width   	: '100%',
                                        height      	: '100%',
                                        autoSize	: true,
                                        padding         : 8,
                                        wrapCSS :'fancy_custom'
                                 });
                                }
                                
                            });
                        break;
                        
                        
                        
                 }
                 return false;
             });
             
             //FUNCION QUE TOMA LOS CLICK EN EL MENU FIJO EN LA CABEZERA DE LA TABLA:
             
             $('body').on('click','#acciones a',function(){
                 var accion=$(this).attr('accion');
                 alert('click en : '+accion);
                 return false;
             });
    
  } );



</script>