var nuevo_impuesto ='';
var langFRM = langFrm ;
function editar(elemento,valor){
        
            $(elemento).prop('readonly',valor);
            
    }
$('.fancybox-wrap').ready(function(){
    
//    var clavesFRM=Array("validacion_ok","codigo","nombre","detalle","activo","concepto","valor","impuesto_sin_detalles","ocurrio_error");
    
    
    
    var numerador='';
    
//    $.ajax({
//            url:BASE_URL+'entorno/getLang',
//            data:"claves=" + JSON.stringify(clavesFRM),
//            type:"POST",
//            dataType:"JSON",
//            async:false,
//            cache:false,
//            success:function(respuesta){
//                //langFRM=respuesta;
//                initFRM();
//            }
//    });
    
    initFRM();
    
    function getSelect(conceptos,item,k){
        console.log('KEY_ENTRADA');
        console.log(k);
       
        var select='<select name="concepto['+k+'][cod_concepto]" data-placeholder="_seleccione concepto"></option></option>';
       
        $(conceptos).each(function(k,concepto){
           
           select+='<option value="'+concepto.codigo+'">'+concepto.conceptoTraducido+'</option>';
           
       });
       
       select+="</select>";
       
       return select; 
    }
    
//    function validar(){
//        
//        var retorno=true;
//        
//        $('#impuesto .editValor').each(function(){
//           
//            if($(this).val()===''){
//               
//                retorno=false;
//               
//                $.gritter.add({
//                                title: '_Upss',
//                                text: 'alguno de los conceptos no tiene un valor asignado',
//                                //image: $path_assets+'/avatars/avatar1.png',
//                                sticky: false,
//                                time: '3000',
//                                class_name:'gritter-error'
//                        });
//               
//            
//            }
//        
//        });
//        
//       return retorno;
//       
//    }
//    
//    function getInput(valor,k){
//        var input='<div class="input-group"><input class="form-control editValor" name="concepto['+k+'][valor]" value="'+valor.replace('%','')+'" readonly onfocus="editar(this,false);" onfocusout="editar(this,true);"><span class="input-group-addon">%</span></div>';
//        //var input='<span class="input-icon"><input class="form-control editValor" name="concepto['+k+'][valor]" value="'+valor+'" readonly onfocus="editar(this,false);" onfocusout="editar(this,true);"><i class="icon-pencil"></i></span>';
//        
//        return input;
//        
//    }
    
   function getEstado(estado,codigo,k){
        
        
        checked='checked';
        
//        if(estado==1){
//            
//            var checked='';
//          
//        }
        
        var estado='<label><input name="concepto['+k+'][activo]" class="ace ace-switch ace-switch-6" type="checkbox" '+checked+'><span class="lbl"></span></label>';
        
        return estado;
    }
   function nuevoConceptoImpuesto(k){
      
       var nuevo ='<label><input type="hidden" name="concepto['+k+'][nuevo_concepto_impuesto]" class="ace ace-switch ace-switch-6" value="'+nuevo_impuesto+'"><span class="lbl"></span></label>'
       return nuevo;
   }
   function crearTabla(listado){
        
        console.log('LLAMADO A CREAR TABLA:');
        console.log(listado);
        
        //alert(listado);
        var tabla='<table id="tablaDetalle" class="table table-striped"><thead>';
        tabla+='<th>'+langFRM.concepto+'</th><th>'+langFRM.nombre+'</th><th>'+langFRM.activo+'</th></thead>';
        tabla+='<tbody>';
        tabla+='</tbody></table>';
        
        
        $('.fancybox-wrap .table-responsive').html(tabla);
        
        
        
        if(listado.length!=0){
            
        
        
        $(listado).each(function(k,item){
            
            numerador=k;
           
            $('#tablaDetalle').append('<tr><td>'+item.nom_concepto+'<input type="hidden" name="concepto['+k+'][cod_concepto]" value="'+item.cod_concepto+'"></td><td>'+item.nombre+nuevoConceptoImpuesto(k)+'</td><td>'+getEstado(item.baja,item.cod_concepto,k)+'</td></tr>');
            
        });
        
        
        
        }else{
            
           
        $('#tablaDetalle').hide();
        $('.fancybox-wrap .table-responsive').parent().after('<div class="row"><div class="col-md-12 mensajeListadoVacio">'+langFRM.impuesto_sin_detalles+'</div></div>'); 
            
            
        }
        
    }
   function initFRM(){
        
            
        var listado= JSON.parse($('input[name="listado"]').val());
        
        cod_impuesto=$('input[name="cod_impuesto"]').val();
        
        nombreImpuesto=$('input[name="cod_impuesto"]').attr('data-nombre');
        
        conceptos=JSON.parse($('input[name="conceptos"]').val());
        
        crearTabla(listado);
        
        $('.fancybox-wrap select').chosen({
             width:'100%',
             allow_single_deselect: true
         });
         
         
         $('#nuevoImpuesto').on('click',function(){
             nuevo_impuesto = -1;
             $('#tablaDetalle').show();
             
             $('.mensajeListadoVacio').hide();
             
             
             
             numerador=parseInt(numerador)+1;
             
             
             
             var tr='<tr><td>'+getSelect(conceptos,'',numerador)+'</td><td>'+nombreImpuesto+nuevoConceptoImpuesto(numerador)+'</td><td>'+getEstado(1,'-1',numerador)+'</td></tr>';
             
             $('#tablaDetalle').append(tr);
             
             $.fancybox.update();
             
             return false;
         });
         
         $('#impuesto').on('submit',function(){
            
            
             
                $.ajax({
                       url: BASE_URL+"impuestos/guardarConceptoImpuesto",
                       type: "POST",
                       data: $(this).serialize(),
                       dataType:"JSON",
                       cache:false,
                       success:function(respuesta){
                           console.log(respuesta)
                           if(respuesta.codigo == 1){
                               gritter(langFRM.validacion_ok,true);
                               $.fancybox.close();
                               tablaImpuestos(); 
                           }else{
                               gritter(langFRM.ocurrio_error)
                           }
                       }
               });
           
             return false;
         });
    
    }
    
   
    
    
});

