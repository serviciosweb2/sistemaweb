<script>
    $(document).ready(function() {
//VARIABLES DE CONFIGURACION:
var menu='aspirantes';
var claves = Array("codigo_aspirante_cabecera","facturacion_anular");
///////
$.ajax({
    url:BASE_URL+'entorno/getLang',
    data:"claves=" + JSON.stringify(claves),
    dataType:'JSON',
    type:'POST',
    cache:false,
    async:false,
   success:function(respuesta){
       lang=respuesta;
      /// console.log(respuesta);


//MENU'S
        $.ajax({
            url:BASE_URL+'entorno/getMenu',
            data:'seccion=aspirantes',
            dataType:'JSON',
            type:'POST',
            cache:false,
            async:false,
            success:function(respuesta){
                //console.log(respuesta);
                menu=respuesta;
                
 //NOMBRE Y ORDEN DE COLUMNAS               
                    $.ajax({
                        url:BASE_URL+'aspirantes/getColumns',
                        data:'',
                        dataType:'JSON',
                        type:'POST',
                        cache:false,
                        async:false,
                        success:function(respuesta){
                            
                            aoColumnDefs=respuesta;
                            init();

                }
              });
               
            }
          });

        }
  }); 
  
   console.log(menu);
  
    //CONFIGURACION DE LA TABLA:
    function init(){
    oTable =$('#academicoAspirantes').dataTable({
        "bProcessing": false,// LO PUSE EN FALSE MOMENTANEAMENTE
        "bServerSide": true,// LO PUSE EN FALSE MOMENTANEAMENTE
        "sAjaxSource": BASE_URL+'aspirantes/listar',
        "sServerMethod": "POST",
        "sPaginationType": "bs_full",
        'aoColumnDefs': aoColumnDefs
        
    } );
    
    $('#academicoAspirantes').each(function(){
				var datatable = $(this);
				// SEARCH - Add the placeholder for Search and Turn this into in-line form control
				var search_input = datatable.closest('.dataTables_wrapper').find('div[id$=_filter] input');
				search_input.attr('placeholder', 'Search');
				search_input.addClass('form-control input-sm');
				// LENGTH - Inline-Form control
				var length_sel = datatable.closest('.dataTables_wrapper').find('div[id$=_length] select');
				length_sel.addClass('form-control input-sm');
			});
    thead=[];
    $(aoColumnDefs).each(function(){
        console.log(this.sTitle);
        thead.push(this.sTitle);
    });
    
     function columnName(name){
       var retorno='';
        $(thead).each(function(key,valor){
            if(valor==name){

               console.log('vA'+valor);
               retorno=key;
            }
               
        });
        return retorno;
    }
    $(".dataTables_length").html(menu.superior);
    $('#academicoAspirantes').on('click','tr', function () {
       
    
  } );
  
 
    
    // CAPTURA DEL EVENTO CLICK DERECHO:
           var codigo='';
         $('#areaTablas').on('mousedown','#academicoAspirantes tbody tr',function(e){
            
               var sData = oTable.fnGetData( this );
               
                if( e.button === 2 ) { 
                    //alert('Boton derecho!'); 
                     var x=e.clientX;//CORDENADAS DEL MOUSE(averiguo la ubicacion para desplegar el menu)
                     var y=e.clientY;
                   
                     var sBrowser =sData[1];
                     var sGrade = sData[4];
                        codigo=sData[columnName(lang.codigo_aspirante_cabecera)];
                        //alert(codigo);
                        despliegaMenu(x,y,codigo,sBrowser,sGrade);
                     return false; 
                }
               
             
             
             //FUNCION DESPLIEGA MENU:  
                
                  function despliegaMenu(x,y,codigo){
                   
                    $('#desplegable').remove();
                    var contenido='<div id="desplegable" class="span2">'+menu.contextual+'</div></div></div>';
                    
                    
                    
                    $('#contenedorTablas').before(contenido);
                   
                    $('#desplegable').css({
                        "margin-top":y,
                        "margin-left":x-18,
                        "position":"fixed",
                        "z-index":'5000',
                         "display":"none"
                      }).show();
                      
                   
                      
                  }
             });
             
             
             //FUNCION QUE TOMA CLICK EN EL MENU DESPLEGABLE:
             
             
             $('body').on('click','#desplegable a',function(){
                 // SETEO VARIABLES PARA EL ENVIO
                 
                 var accion=$(this).attr('accion');
                 
                 var valor='codigo='+codigo;
               // alert(valor);
                 $('#desplegable').remove();
                 
                 switch(accion){
                     
                    case 'modificar_aspirante':
                       $.fancybox.showLoading();
                          $.ajax({
                               url:'<?=base_url('aspirantes/form_aspirante')?>',
                               type:'POST',
                               data:valor,
                               cache:false,
                               success:function(respuesta){
                                   $.fancybox.open(respuesta,
                                    {
                                        maxWidth	: 1000,
                                        maxHeight	: 1000,
                                        width   	: '65%',
                                        height      	: '65%',
                                        scrolling        :'no',
                                        autoSize	: true,
                                        padding         : 4,
                                        openEffect      :'none',
                                        closeEffect     :'none',
                                         helpers:  {
                                                    overlay : null
                                                    }
                                          }
    
                                        );
                                    $('.fancybox-wrap').draggable(); 
                               }
                               
                           });
                         
                    break;
                      
                    case 'presupuestar_aspirante':
                          $.fancybox.showLoading();
                           $.ajax({
                               url:'<?=base_url('aspirantes/presupuestar_aspirante')?>',
                               type:'POST',
                               data:valor,
                               cache:false,
                                success:function(respuesta){
                                   $.fancybox.open(respuesta,
                                    {
                                        maxWidth	: 1000,
                                        maxHeight	: 1000,
                                        width   	: 'auto',
                                        height          :'auto',
                                        scrolling        :'no',
                                        autoSize	: true,
                                        padding         : 4 ,
                                        wrapCSS :'fancy_custom'
                                          }
    
                                        );
                                   
                               }
                               
                           });
                         
                    break;
                        
                    default:// pasar a alumno
                            
                           $.fancybox.showLoading();
                           
                             $.ajax({
                               url:'<?=base_url('alumnos/form_alumnos')?>',
                               type:'POST',
                               data:'codigo_aspirante='+codigo,
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
                       
                 }
                     
                  //alert('click en: '+accion+' codigo= '+id);
                    return false;
             });
             
             //FUNCION QUE TOMA LOS CLICK EN EL MENU FIJO EN LA CABEZERA DE LA TABLA:
             $('body').on('click','.acciones',function(){
                
                 var accion=$(this).attr('accion');
                //alert(accion);
                alert('nuevo');
                 $.fancybox.showLoading();
                
                 
                 $.ajax({
                     url:'<?=base_url('aspirantes/form_aspirante')?>',
                     cache:false,
                     type:'POST',
                     data:'codigo=-1',
                     success:function(respuesta){
                // alert(respuesta);
                        
                         $.fancybox.open(respuesta,{
                        maxWidth	: 1000,
                        maxHeight	: 1000,
                        scrolling        :'no',
                        width   	: '65%',
                        height      	: '65%',
                        autoSize	: true,
                        padding         : 4
                         });
                     }
                 });
                 return false;
             });

            
            }
  });
    </script>