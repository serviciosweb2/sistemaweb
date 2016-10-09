<?php 
$ci=& get_instance();
$get_cached_vars=$ci->load->get_cached_vars();
$aoColumnDefs=$get_cached_vars['aoColumnDefs'];
?>
<script>
$(document).ready(function() {
var thead=[];   
var data='';
var oTable='';
    //CONFIGURACION DE LA TABLA:

var  aoColumnDefs=<?php echo $aoColumnDefs?>;
    
    oTable =$('#administracioResumenCuenta').dataTable({
        "bProcessing": false,// LO PUSE EN FALSE MOMENTANEAMENTE
        "bServerSide": true,
        "sAjaxSource": "<?php echo base_url('ctacte/listar');?>",
        "sServerMethod": "POST",
        "sPaginationType": "bootstrap",
        "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
        "aoColumnDefs":aoColumnDefs
     
    } );
    
     $(aoColumnDefs).each(function(){
                console.log(this.sTitle);
                thead.push(this.sTitle);
            });
    

    $("div.toolbar").html('<?php echo session_menu_superior('cursos','menu_superior')?>');
   
   
    
  
    
 
 
    
   function columnName(name){
       var retorno='';
        $(thead).each(function(key,valor){
            if(valor==name){

               console.log(valor);
               retorno=key;
            }
               
        });
        return retorno;
    }
    
    //var numero = headName('codigo');
    //console.log(numero);
    // CAPTURA DEL EVENTO CLICK DERECHO:
    var codigo='';
    var desactivado='';
         $('#areaTablas').on('mousedown','#administracioResumenCuenta tbody tr',function(e){
             //console.log(data.aoColumnDefs);
      
               
                 var sData = oTable.fnGetData( this );
                
              //console.log(sData);
               
                if( e.button === 2 ) { 
                    //alert('Boton derecho!'); 
                     var x=e.clientX;//CORDENADAS DEL MOUSE(averiguo la ubicacion para desplegar el menu)
                     var y=e.clientY;
                     var nTds = $('td', this);
                     //$(nTds[0]).parent().css('background','#BDBDBD');
                     var sBrowser =sData[1];
                     var sGrade = sData[4];
                      desactivado=sData[columnName('<?php echo lang('habilitado_curso');?>')];
                      //alert(desactivado);
                    
                      codigo=sData[columnName('<?php echo lang('codigo_alumno_ctacte');?>')];
                     
                     despliegaMenu(x,y,codigo);

                     return false; 
                }
               
             
             
             //FUNCION DESPLIEGA MENU:  
                
                  function despliegaMenu(x,y,codigo){
                   
                    $('#desplegable').remove();
                    var contenido='<div id="desplegable" class="span2"><?php echo session_menu_contextual('ctacte')?></div></div></div>';
                     
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
                 switch(accion){
                     
                        
                        case 'ver-ctacte':
                            
                            
                        $.ajax({
                                url:'<?php echo base_url('ctacte/frm_ctacte')?>',
                                data:'codigo='+codigo,
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
                                        padding         : 8
                                        
                                 });
                                  console.log('respuesta=  '+respuesta);
                                }
                                
                            });
                        
                        break;
                        
                        case 'verMaterias':

                                $.ajax({
                                url:'<?php echo base_url('cursos/form_materias')?>',
                                data:'codigo_curso='+codigo,
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
                                        padding         : 8
                                        
                                 });

                                }
                                
                            });        
                        break
                        case 'cambiar-comision':
                            //alert ('!');
                                $.ajax({
                                url:'<?php echo base_url('matriculas/frm_CambioComision')?>',
                                data:'codigo_matricula='+codigo,
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
                        break
                        case 'inscripcion_materias':

                                $.ajax({
                                url:'<?php echo base_url('matriculas/frm_inscribirMaterias')?>',
                                data:'codigo_matricula='+codigo,
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
                        break
                       
                       
                 }
                 return false;
             });
             
             
             
             
             
                      $('body').on('click','.toolbar a',function(){
                 var accion=$(this).attr('accion');
                 var id=$(this).attr('id');
                 $('#desplegable').remove();

                 switch(accion){
                     case 'nueva_matriculas':

                            $.ajax({
                                url:'<?=base_url('matriculas/frm_Matricula')?>',
                                data:'',
                                type:'POST',
                                cache:false,
                                success:function(respuesta){

                                      $.fancybox.open(respuesta,{
                                        
                                        scrolling       :true,
                                        width:'50%',
                                        autoSize	: false,
                                        autoResize	: true,
                                        padding         : 8
                                       
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



