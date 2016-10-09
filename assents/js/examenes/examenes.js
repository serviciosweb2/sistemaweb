var tablaParciales='';
var tablaFinales='';    
var  aoColumnDefs = function(tipo){    
    var retorno='';  
    if(tipo == 'parciales'){
        retorno = columnsParciales;
    } else {
        retorno = columnsFinales;
    }        
    return retorno;
};   

var codigo='';
var thead=[];   
var data='';
var claves = Array("nuevo-examen-final","codigo","HABILITADO","INHABILITADO", "ocurrio_error", "validacion_ok","INHABILITAR","HABILITAR");
var lang = BASE_LANG;
var menuARRAY = menuJson ;

$(document).ready(function(){
    init();
});  
  
function init(){
    function devolverEstado(estadoMatricula) {
        var clase ="";
        var estado = "";
        switch (estadoMatricula) {
            case "inhabilitada":
                clase = "label label-danger arrowed";
                estado = lang.INHABILITADA;
                break;

            case "habilitada" :
                clase = "label label-success arrowed";
                estado = lang.HABILITADA + "&nbsp";
                break;
        }
        imgTag = '<span class="' + clase + '">' + estado + '</span>';
        return imgTag;
    }    
    
    function columnName(instancia,lang){       
        var retorno='';       
        var cabezeras=instancia.tabla.columns().header();
            $.each(cabezeras,function(k,th){
                if(lang==th.textContent){
                    retorno=k;
                }
            });        
        return retorno;
    } 
    
    function Tabla(id,tipo){        
        this.id=id;        
        this.tipo=tipo;
        Tabla.prototype.generar=function(){            
            var tipo=this.tipo;
            this.tabla=$(this.id).DataTable({
                bProcessing: false,
                bServerSide: true,
                sAjaxSource: BASE_URL+'examenes/listar',
                sServerMethod: "POST",
                aaSorting: [[ 0,"desc"]],
                fnServerParams: function ( aoData ) {
                    aoData.push( { "name": 'tipoExamen', "value":tipo } );
                },
                'aoColumnDefs': aoColumnDefs(tipo),
                fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull){
                    if (tipo == 'parciales'){                                        
                        var estado= aData[10]==1 ? '<span class="label label-danger arrowed">'+lang.INHABILITADO+'</span>' : '<span class="label label-success arrowed">'+lang.HABILITADO+'</span>';
                        estado += '<input type="hidden" id="hd_estado_' + aData[0] + '" value="' + aData[10] + '">';
                        $('td:eq(9)', nRow).html(estado);                                        
                    } else {                                        
                        var estado= aData[10]==1 ? '<span class="label label-default arrowed">'+lang.INHABILITADO+'</span>' : '<span class="label label-success arrowed">'+lang.HABILITADO+'</span>';
                        estado += '<input type="hidden" id="hd_estado_' + aData[0] + '" value="' + aData[10] + '">';    
                        $('td:eq(8)', nRow).html(estado); 
                    }
                    return nRow;
                },
                oTableTools: {
                    sRowSelect: "single"						
                }
            });            
        };        
        Tabla.prototype.refresh=function(){
                this.tabla.draw();
        };
    }
        
    tablaParciales = new Tabla('#academicoExamenesParciales','parciales');
    tablaParciales.generar();     
    tablaFinales = new Tabla('#academicoExamenesFinales','finales');
    tablaFinales.generar();   
    marcarTr();  
    $('.dataTables_filter input').addClass('form-control').attr('placeholder');   
    $("#Finales .dataTables_wrapper .col-sm-6:first").html(generarBotonSuperiorMenu(menuARRAY.superior[0],'btn-success'));    
    $("#Parciales .dataTables_wrapper .col-sm-6:first").html(generarBotonSuperiorMenu(menuARRAY.superior[1],'btn-success'));    
    $('body').not('table').on('click',function(){       
        $('#menu').hide().fadeIn('fast').remove();
    });
   
    var desactivado='';         
    $('#Parciales').on('mousedown','table tbody tr',function(e){
        var sData =tablaParciales.tabla.row(this).data();     
        desactivado = sData[10];         
        if( e.button === 2 ){
            codigo = sData[columnName(tablaParciales,lang.codigo)];
            $('#menu').hide().fadeIn('fast').remove();                    
            generalContextMenu(menuARRAY.contextual,e);
            if(desactivado == 1){
               $('#menu a[accion="cambiar-estado-examen"]').text(lang.HABILITAR);              
            } else {                
                $('#menu a[accion="cambiar-estado-examen"]').text(lang.INHABILITAR);               
            }
            $('#menu').find('a[accion="baja-inscripcion-examen"]').closest('li').hide();
            return false;
        }
    });
    
    $('#Finales').on('mousedown','table tbody tr',function(e){
        var sData = tablaFinales.tabla.row(this).data();
        var estado = '';
        estado = sData[10];
        if( e.button === 2 ) {                   
            codigo=sData[columnName(tablaFinales,lang.codigo)];                 
            generalContextMenu(menuARRAY.contextual,e);         
            if(estado == 1){
               $('#menu a[accion="cambiar-estado-examen"]').text(lang.HABILITAR);
            } else {
                $('#menu a[accion="cambiar-estado-examen"]').text(lang.INHABILITAR);
            }
            $('#menu').find('a[accion="baja-inscripcion-examen"]').closest('li').hide();
            return false;
        }
    });

    $('body').on('click','.dataTables_wrapper .boton-primario',function(){    
        var accion=$(this).attr('accion');    
        switch(accion){    
            case 'nuevo-examen-final':             
                $.ajax({
                    url:BASE_URL+'examenes/frm_examen_final',
                    type:'POST',
                    cache:false,
                    success:function(respuesta){                           
                        $.fancybox.open(respuesta, {
                            scrolling       :'auto',
                            width   	: 'auto',
                            height      	: 'auto',
                            autoSize	: false,
                            padding         : 1,
                            openEffect      :'none',
                            closeEffect     :'none',
                            helpers: {
                                overlay :null
                            }                                       
                        });                            
                    }                        
                });
                break;

            case 'nuevo-examen-parcial':
                $.ajax({
                    url:BASE_URL+'examenes/frm_examen_parcial',
                    type:'POST',
                    cache:false,
                    success:function(respuesta){
                         $.fancybox.open(respuesta, {
                            scrolling       :'auto',
                            width   	: 'auto',
                            height      	: 'auto',
                            padding         : 1,
                            openEffect      :'none',
                            closeEffect     :'none',
                            helpers:  {
                                overlay :null
                            }
                        });
                    }
                });
                break;
        }
    });

   $('body').on('click','#menu a',function(){               
        $('#menu').hide().fadeIn('fast').remove();
        var accion=$(this).attr('accion');              
        switch(accion){
            case "acta_volante":
                var param = new Array();
                param.push(codigo);
                printers_jobs(8, param);
                break;
                    
            case'ver-inscriptos':
                $.ajax({
                    url:BASE_URL+'examenes/frm_inscriptos',
                    type:'POST',
                    data:'codigo='+codigo,
                    cache:false,
                    success:function(respuesta){
                        $.fancybox.open(respuesta, {
                            scrolling:'auto',
                            width: '50%',
                            height: 'auto',
                            autoSize: false,
                            padding: 1,
                            openEffect:'none',
                            closeEffect:'none',
                            helpers:  {
                                overlay :null
                            }
                        });
                    }
                });
                break;
            
           case 'cargar-nota-examen':
                $.ajax({
                    url:BASE_URL+'examenes/frm_cargarNotasExamen',
                    type:'POST',
                    data:'codigo='+codigo,
                    cache:false,
                    success:function(respuesta){                           
                        $.fancybox.open(respuesta, {
                            scrolling       :'auto',
                            width   	: 'auto',
                            height      	: 'auto',
                            autoSize	: false,
                            padding         : 1,
                            openEffect      :'none',
                            closeEffect     :'none',                                         
                            helpers:  {
                                overlay :null
                            }                                       
                        });                            
                    }                        
                });
                break;
                    
                    
            case 'cambiar-estado-examen':
                var estado = $("#hd_estado_" + codigo).val();
                if (estado == 1){
                    $.ajax({
                        url: BASE_URL + 'examenes/cambiarEstado',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            codigo: codigo
                        },
                        success: function(_json){
                            if (_json.codigo == 1){
                                gritter(lang.validacion_ok, true);
                                tablaFinales.refresh();
                                tablaParciales.refresh();
                            } else {
                                gritter("", false, "ocurrio_error");
                            }
                        }
                    });
                } else {
                    $.ajax({
                        url:BASE_URL + 'examenes/frm_baja_examen',
                        type:'POST',
                        data:'codigo='+codigo,
                        cache:false,
                        success:function(respuesta){                           
                            $.fancybox.open(respuesta, {
                                scrolling:'auto',
                                autoSize: false,
                                autoResize: false,
                                padding: 1,
                                width: '40%',
                                height: 'auto',
                                openEffect: 'none',
                                closeEffect: 'none',
                                helpers:{
                                    overlay :null
                                }                                       
                            });                            
                        }                        
                    });
                }
                break;                
                
            case 'modificar_examen':
                $.ajax({
                    url:BASE_URL+'examenes/modificarExamen',
                    type:'POST',
                    data:'codigo='+codigo,
                    cache:false,
                    success:function(respuesta){                           
                        $.fancybox.open(respuesta, {                                        
                            scrolling: 'auto',
                            padding: 1,
                            width: 'auto',
                            height: 'auto',
                            openEffect: 'none',
                            closeEffect: 'none',
                            helpers:  {
                                overlay :null
                            }
                        });
                    }
                });
                break;
                     
            default:
                $.gritter.add({
                    title: 'Upss!',
                    text: 'NO TIENE PERMISO',
                    sticky: false,
                    time: '3000',
                    class_name:'gritter-error'
                });
                break;                   
        }
        return false;
    });
}