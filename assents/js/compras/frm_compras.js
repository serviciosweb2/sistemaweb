var n=0; // se usa para numerar los rows de las tablas
var langFRM = langFrm;
var modificar = function(){
    
    var retorno=true;
    
    $(G_pagosCompra).each(function(k,pago){
        
        if(pago.modificar==false){
            
            retorno=false;
            
        }
        
       
        
    });
     return retorno;
} // esta variable valida la modificacion de pagos y compras, devuelve true o false; 

function loadFecha(){
    
      
    $('.fecha').datepicker({
        changeMonth: false,
        changeYear: false,
        

        
    });
    
}


//FUNCIONES DE  ORDEN DE COMPRA////////////////////////////////////////

function cargarImpuestos(elemento){
    
    
    $.ajax({
            url: BASE_URL+"compras/getImpuestoArticulo",
            type: "POST",
            data: 'cod_articulo='+$(elemento).val(),
            dataType:"JSON",
            async: false,
            cache:false,
            success:function(respuesta){
                
                var placeHolder="0 impuestos";
                
                var opciones="";
                
                if(respuesta.length>0){
                    
                  
                     placeHolder='seleccione';
                    
                    $(respuesta).each(function(k,item){
                        
                        
                        opciones+="<option value='"+item.cod_impuesto+"' selected>"+item.nombre+"</option>";
                        
                    });
                    
                    
                }
                
                $(elemento).closest('tr').find('select[name$="[impuestos][]"]').attr('data-placeholder',placeHolder).html(opciones).trigger('chosen:updated');
                
                
            }
});
    
    
}

function cargarProductos(elemento){
    
    $.ajax({
        url: BASE_URL+"compras/getArticulos",
        type: 'POST' ,
        data: "cod_categoria="+$(elemento).val(),
        dataType:"JSON",
        cache:false,
        success:function(respuesta){
            
            var opciones='<option></option>';
            
            var plcHolder='No hay resultados';
            
            if(respuesta.length>0){
              
                plcHolder='seleccione un producto';
               
                $(respuesta).each(function(k,item){
                     
                    opciones+='<option data-costo="'+item.costo+'" value="'+item.codigo+'">'+item.nombre+'</option>';
                    
                });
                
            }
            
           $(elemento).closest('tr').find('select[name$="[cod_articulo]"]').html(opciones).attr('data-placeholder',plcHolder).trigger('chosen:updated');
        }
    });
    
};

function actualizarTotal(){
    
    var dataPOST=tCompras.$('input, select').serialize();
    
    $.ajax({
            
            url: BASE_URL+"compras/getImportesRenglones",
            type: "POST",
            data: dataPOST,
            dataType:"JSON",
            cache:false,
            async:false,
            success:function(respuesta){
                
                tCompras.$('input[name$="[precio_total]"]').each(function(k,valor){
                    
                    $(valor).val(respuesta[k]['precio_total'] );
                    
                });
                
                
                
                tCompras.$('input[name$="[total_impuestos]"]').each(function(k,valor){
                    
                    $(valor).val(respuesta[k]['total_impuestos'] );
                    
                });
                
                
                $('input[name="total"]').val(respuesta['totales']['importe']);
                
            }
    });
    
   // tCompras.$('input[name$="[precio_unitario]"]').val('2');
    
}

function sugerirPrecio(elemento){
    
    
    var valorSugerido=$(elemento).find('option:selected').attr('data-costo');
    
    //alert(valorSugerido);
    
    $(elemento).closest('tr').find('input[name$="[precio_unitario]"]').val(valorSugerido);
    
    $(elemento).closest('tr').find('input[name$="[cantidad]"]').val('1');
    
}

function selectProductos(x){
    
  
  
    var dataPlace='No hay articulos';
    
    var options='<option></option>';
    
    if(x.length>0){
       
       
       dataPlace='seleccione articulo';
        
        $(x).each(function(k,option){
            
            
            
            var selected ='selected';
            
            var estado= modificar()== true || selected=='selected' ? '' :'disabled';
            
            options+='<option value="'+option.codigo+'" data-costo="'+option.costo+'"  '+selected+' '+estado+'>'+option.nombre+'</option>';
        
        }); 
    }
    
    
    
    return '<select name="renglones['+n+'][cod_articulo]"' +dataPlace+' onchange="sugerirPrecio(this);cargarImpuestos(this)">'+options+'</select>';
}

function selectImpuesto(i){
    
    
  
    var dataPlace='No tiene';
    
    var options='<option></option>';
    
    var selected ='';
    
    if(i.length>0){
       
        dataPlace='seleccione';
    
    }   
       
    
        $(G_impuestos).each(function(key,impuesto){ 
        
                $(i).each(function(k,option){

                     selected= option == impuesto.codigo ? 'selected' : '';   

                });
            
               
             
                var estado = modificar()== false && selected!='selected' ? 'disabled' : ''; 
            
            
                options+='<option value="'+impuesto.codigo+'"  class="'+estado+'"   '+selected+' >'+impuesto.nombre+'</option>';
        
           
        }); 
    
    
    
    
    return '<select name="renglones['+n+'][impuestos][]"  data-placeholder="'+dataPlace+'"   multiple>'+options+'</select>';
    
}

function nombCategoria(obj){
    
    var nombre='';
    
    if(obj.nombrepadre =='' || obj.nombrepadre==null ){
        
        nombre+=obj.nombre;
    
    }else{
        
        nombre+=obj.nombrepadre + ' ('+obj.nombre+')';
        
    }
    
    return nombre;
}

function selectCategorias(articulos){
    
    
    
    
    var options='<option></option>';
    
    var codCategoria='';
    
    if(articulos.length==0){
        
       
        
        codCategoria='';
        
    }else{
        
      codCategoria=articulos[0]['cod_categoria'];
        
        
    }
        
      
        
       
        $(G_categorias).each(function(k,categoria){
            
            
            
            var selected= codCategoria==categoria.codigo ? 'selected' : '';
            
           var estado= modificar()== true || selected=='selected' ? '' :'disabled';
            
            options+='<option value="'+categoria.codigo+'"  '+selected+'  '+estado+'>'+nombCategoria(categoria)+'</option>';
            
            
            
            
        });
        
        
   
   
    
    return '<select name="renglones['+n+'][categoria]"  onchange="cargarProductos(this);">'+options+'</select>';
}

function tablaCompras(filas){
    
    var estado= modificar()==false ? 'disabled' : '';
    
    var tabla='<table id="tablaCompras" class="table table-condensed table-bordered" width="100%">'
    +'<thead>'
    +'<th>'+langFRM.categoria+'</th>'
    +'<th>'+langFRM.articulos+'</th>'
    +'<th>'+langFRM.cantidad+'</th>'
    +'<th>'+langFRM.precio_unitario+'</th>'
    +'<th>'+langFRM.impuestos+'</th>'
    +'<th>'+langFRM.total_impuestos+'</th>'
    +'<th>'+langFRM.precio_total+'</th>'
    +'<th></th>'
    +'</thead><tbody>';
    
    tabla+='</tbody></table>';
    
    $('#compras .contenedor').html(tabla);
    
    tCompras=$('#tablaCompras').DataTable({
        "aoColumns": [
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            {"bSortable": false},
        ] 
    });
  
  
    $('#tablaCompras_length').html('<button class="btn btn-info editarCompra" '+estado+' onclick="agregarArticulo(event,\'\');">'+langFRM.agregar+'</button>').parent().addClass('no-padding');
    
   $('#tablaCompras').wrap('<div class="table-responsive"></div>');
    
    $(filas).each(function(k,row){
         
        agregarArticulo('',row);
        
        
    });
    
}

function deleteArticulo(elemento){
    
    if(modificar()==false){
        
        gritter(langFRM.no_se_puede_borrar_compra_articulo);
        
    
    
    }else{
        
        var valor=$(elemento).attr('data-codigo');
    
        tCompras.rows($( elemento).closest('tr') ).remove().draw();
    
        if(valor!=-1){

             $('#bajas').append('<input name="renglonesBajas[]" value="'+valor+'" type="hidden">');
        }
   
    
        $.fancybox.reposition();
        
    }
    
    
}

function agregarArticulo(event ,fila){
    
   
    
    if(fila ==''){
       
        
        fila={};
        fila.articulos=[];
        fila.baja=0;
        fila.cantidad= "";
        fila.cod_articulo="";
        fila.total_impuestos='',
        fila.precio_total="";
        fila.precio_unitario="";
        fila.codigo='-1';
        fila.impuestos=[];
       
    }
    
    var estado= modificar()==false && fila.codigo!='-1' ? 'readonly' : '';
   
    var claseEliminar= modificar()==false && fila.codigo!='-1' ? 'icon-lock' : 'icon-trash';
    
    tCompras.row.add( [ 
        
        selectCategorias(fila.articulos),
        
        selectProductos(fila.articulos)+'<input type="hidden" name="renglones['+n+'][codigo]"   value="'+fila.codigo+'">', 
        
        '<input class="form-control editarCompra" name="renglones['+n+'][cantidad]" value="'+fila.cantidad+'" '+estado+'>',
        
        '<input class="form-control editarCompra" name="renglones['+n+'][precio_unitario]" '+estado+' value="'+fila.precio_unitario+'">', 
        
        selectImpuesto(fila.impuestos),
        
        '<input class="form-control" name="renglones['+n+'][total_impuestos]" value="'+fila.total_impuestos+'" readonly>' ,
        
        '<input class="form-control" name="renglones['+n+'][precio_total]" value="'+fila.precio_total+'" readonly>' ,
        
        '<a href="javascript:void(0)"      data-codigo="'+fila.codigo+'" name="renglones['+n+'][baja]" onclick="deleteArticulo(this);"><i class="'+claseEliminar+'"></i></a>'] 
    
    ).draw();
   
  
   
  
    
    $('#tablaCompras select').chosen({width:'100%'});
    
    if(modificar()==false){
        
        $('#tablaCompras .disabled').prop('disabled',true);
        
        $('#tablaCompras select').trigger('chosen:updated');
        
        $('#tablaCompras .search-choice-close').hide();
    }
    
    
    $.fancybox.reposition();
    
    n++;
    
    event=='' ? '' : event.preventDefault();


}



// FUNCIONES DE COMPROBANTES ////////////////////////////////////////

function selectTipoComprobantes(comprobante){
   
   
    
    var dataPlace='No hay articulos';
    
    var options='<option></option>';
    
    
    
    
    if(G_comprobrantes.length>0){
       
       
        dataPlace='seleccione comprobante';
        
        
        $(G_comprobrantes).each(function(k,option){
            
            
                var selected = option.id == comprobante.cod_comprobante ? 'selected' : '';



                if(option.id==2){// si se trata de una factura se generan los tipos de facturas



                            
                            $(G_tiposFacturas).each(function(key,tipo){

                                    var seleted2=  comprobante.tipo  == tipo.factura ? 'selected' : '' ;

                                    var dataPOST={'tipoComprobante':option.id,'tipoFactura':tipo.codigo};

                                    options+="<option value='"+JSON.stringify(dataPOST)+"'   tipoFactura='"+tipo.codigo+"'   "+seleted2+">factura ("+tipo.factura+")</option>";

                            });





                }else{


                                
                                    var dataPOST={'tipoComprobante':option.id,'tipoFactura':''};

                                    options+="<option value='"+JSON.stringify(dataPOST)+"' "+selected+">"+option.nombre+"</option>";

                }
            
           
        
        }); 
    }
    
    
    return '<select name="comprobante['+n+'][tipo_comprobante]" data-placeholder="'+dataPlace+'">'+options+'</select>';
    
}

function tablaComprobantes(rows){
    
        

        var tabla='<table id="tablaComprobantes" class="table table-condensed table-bordered" width="100%">'
        +'<thead>'
        +'<th>'+langFRM.punto_venta+'</th>'
        +'<th>'+langFRM.nro_comprobante+'</th>'
        +'<th>'+langFRM.fecha+'</th>'
        +'<th>'+langFRM.tipo_comprobante+'</th>'
        +'<th>'+langFRM.total+'</th>'
        +'<th></th>'
        +'</thead>';

        tabla+='</tbody></table>';

        $('#comprobantes .contenedor').html(tabla);

        tablaComprobantes=$('#tablaComprobantes').DataTable({
             "aoColumns": [
                null,
                null,
                null,
                null,
                null,
                {"bSortable": false},] 
        });

        $('#tablaComprobantes_length').html('<button class="btn btn-info" onclick="agregarComprobante(event,\'\');">'+langFRM.agregar+'</button>').parent().addClass('no-padding');

        $('#tablaComprobantes').wrap('<div class="table-responsive"></div>');
        
        $(rows).each(function(k,row){

           

            agregarComprobante('',row);


        });
    
}

function agregarComprobante(event,comprobante){
    
   
    
    if(comprobante==''){
        
        comprobante={};
        comprobante.punto_venta='';
        comprobante.cod_comprobante='-1';
        comprobante.codigo='-1';
        comprobante.nro_comprobante='';
        comprobante.fecha_comprobante='';
        comprobante.total='';
    };
    
    tablaComprobantes.row.add( [ 
        '<input class="form-control" name="comprobante['+n+'][punto_venta]" value="'+comprobante.punto_venta+'">',
        '<input class="form-control" name="comprobante['+n+'][nro_comprobante]" value="'+comprobante.nro_comprobante+'"><input type="hidden" name="comprobante['+n+'][cod_comprobante]" value="'+comprobante.cod_comprobante+'"><input type="hidden" name="comprobante['+n+'][codigo]" value="'+comprobante.codigo+'">', 
        '<input class="form-control fecha" name="comprobante['+n+'][fecha_comprobante]" value="'+comprobante.fecha_comprobante+'">', 
        selectTipoComprobantes(comprobante), 
        '<input class="form-control" name="comprobante['+n+'][precio_total]" value="'+comprobante.total+'">' ,
        '<a href="javascript:void(0)" name="comprobante['+n+'][baja]" data-codigo="'+comprobante.codigo+'"  onclick="deleteComprobante(this);"><i class="icon-trash"></i></a>'] 
    
    ).draw();

    loadFecha();
    
    $('#tablaComprobantes select').chosen({width:'100%'});
    
    $.fancybox.reposition();
    
    n++;
    event=='' ? '' :event.preventDefault();
    
}

function deleteComprobante(elemento){
    
   tablaComprobantes.rows($( elemento).closest('tr') ).remove().draw();
    
    var valor= $(elemento).attr('data-codigo');
    
    if( valor!=-1){
        
         $('#bajas').append('<input name="comprobanteBajas[]" value="'+valor+'" type="hidden">');
        
    }
    
    $.fancybox.reposition();
    
    
}



// FUNCIONES DE PAGOS //////////////////////////////////////////////////

function bloquiarMedioPagos(valor,codigoMedio){
    
    var retorno='';
    
    if( valor.modificar==false  &&  valor.medio_pago!=codigoMedio ){
        
        
        retorno='disabled';
        
    }
    
   return retorno;
   
    
    
    
}

function bloquiarPago(valor){
    
    if(valor!='-1' && modificar()==false){
        
        return false;
    }else{
        
        return true;
    }
    
    
};//si el codigo es distinto de menos uno

function selectConcepto(){
    
    return '<select name="tipo_comprobante[]"><option>select concepto</option></select>';
    
}

function selectMedioPago(pago){
    
    var dataPlace='Sin medios';
    
    var options='<option></option>';
    
    if(G_mediosPago.length>0){
       
       dataPlace='seleccione medio';
        
        $(G_mediosPago).each(function(k,option){
            
            var selected = option.codigo == pago.medio_pago ? 'selected' : '';
            
            var clase="";
            
            
            
            options+='<option value="'+option.codigo+'" '+selected+' '+bloquiarMedioPagos(pago,option.codigo)+' >'+option.medio+'</option>';
        
        }); 
       
    
    }
    
    
    return '<select id="select_medio_pago" name="pago['+n+'][medio_pago]" data-placeholder="'+dataPlace+'" onchange="cargarSelectCaja(this,event);">'+options+'</select>';
    
    
    
}

function cargarSelectCaja(element, event){
  var select_caja = $(element).closest('tr').find('select[name*="cod_caja"]');
  var medio_pago = $(element).val();
  var cod_compra = $('input[name="cod_compra"]').val()
  
  
     $.ajax({
                
                url: BASE_URL+"compras/getCajasCompras",
                type: "POST",
                data: 'medio_pago='+ medio_pago +'&cod_compra=' + cod_compra,
                dataType:"JSON",
                cache:false,
                success:function(respuesta){
                    var option = '';
                    
                    $(respuesta).each(function(key,valor){
                        option +='<option value="'+valor.codigo+'">'+valor.nombre+'</option>';
                    });
                    $(select_caja).html(option);
                      $(select_caja).trigger('chosen:updated');
                }
            
        });
  
 
}

function tablaPagos(rows){
    
    
   
    
  
    var tabla='<table id="tablaPagos" class="table table-condensed table-bordered" width="100%">'
    +'<thead>'
    +'<th>'+langFRM.fecha+'</th>'
    +'<th>'+langFRM.medio_de_pago+'</th>'
    +'<th>'+langFRM.caja+'</th>'
    +'<th>'+langFRM.total+'</th>'
    +'<th></th>'
    +'</thead>';
    
    tabla+='</tbody></table>';
    
    $('#pagos .contenedor').html(tabla);
    
    tablaPagos=$('#tablaPagos').DataTable({
         "aoColumns": [
                null,
                null,
                null,
                null,
                {"bSortable": false},] 
    });
    
    $('#tablaPagos_length').html('<button class="btn btn-info" onclick="agregarPago(event,\'\');">'+langFRM.agregar+'</button>').parent().addClass('no-padding');
    
    $('#tablaPagos').wrap('<div class="table-responsive"></div>');
    
    $(rows).each(function(k,row){
        
        
        
        agregarPago('',row);
        
        
    });
    
    
}

function generarSelectCaja(pago){
    var cajaPlace = langFRM.seleccione_caja;
    var options='<option></option>';
    $(G_cajas).each(function(key,valor){
        var selected = valor.codigo == pago.cod_caja ? 'selected' : '';
        var disabled = valor.codigo == pago.cod_caja ? '' : 'disabled';
        options+='<option value="'+valor.codigo+'" '+selected+' '+disabled+'>'+valor.nombre+'</option>';
    });
    return '<select name="pago['+n+'][cod_caja]" data-placeholder="'+cajaPlace+'">'+options+'</select>';
}

function agregarPago(event,pago){
    
   if(pago==''){
      
        pago={};
        pago.medio_pago='-1';
        pago.fecha_pago='';
        pago.importe='';
        pago.cod_pago='-1';
        pago.codigo='-1';
        pago.modificar='true';
        
    }
    
    var estado= bloquiarPago(pago.cod_pago)==false ? 'readonly' : '';
    
    var claseEliminar= modificar()==false && pago.codigo!='-1' ? 'icon-lock' : 'icon-trash';
    var readonly = pago.codigo == -1 ? '' : 'readonly';
    tablaPagos.row.add( [ 
        
        '<input class="form-control fecha" name="pago['+n+'][fecha_pago]" value="'+pago.fecha_pago+'" '+estado+'><input type="hidden" name="pago['+n+'][cod_pago]" value="'+pago.cod_pago+'"><input type="hidden" name="pago['+n+'][codigo]" value="'+pago.codigo+'">', 
        
        selectMedioPago(pago), 
        
        generarSelectCaja(pago),
        
        
         '<input class="form-control"    name="pago['+n+'][precio_total]" value="'+pago.importe+'" '+estado+' '+readonly+'>' ,
        
        '<a href="javascript:void(0)"   data-modificarPago="'+pago.modificar+'" data-codigo="'+pago.codigo+'" onclick="deletePago(this);"><i class="'+claseEliminar+'"></i></a>'] 
    
    ).draw();
    
    $('#tablaPagos select').chosen({width:'100%'});
     
    loadFecha();
    
    
    
    n++;
     
   $.fancybox.reposition();
    
    event=='' ? '' :event.preventDefault();
    
   
    
}

function deletePago(elemento){
    
    var valor=$(elemento).attr('data-codigo');
   
    if($(elemento).attr('data-modificarPago')!='false'){
        
        tablaPagos.rows($( elemento).closest('tr') ).remove().draw();
    
    
        if(valor!=-1){

           $('#bajas').append('<input name="pagoBajas[]" value="'+valor+'" type="hidden">');

       }
        
    
    }else{
        
        
        gritter('No puede quitar este pago');
        
        
    }
    
    
    
    
    $.fancybox.reposition();
    
    
}






//// READY!! /////////////////////////////////////////////////////////

$('.fancybox-wrap').ready(function(){
    
  
$('select[name="cod_proveedor"]').chosen({width:'100%'});
    $('.fecha').datepicker({
        changeMonth: false,
        changeYear: false,
        

        
    });
    
//    var clavesFRM=Array("validacion_ok","categoria","producto","cantidad","precio_unitario","impuestos","total_impuestos","precio_total","agregar",
//                            "punto_venta","nro_comprobante","fecha","tipo_comprobante","total","medio_de_pago","caja","seleccione_caja",'no_se_puede_borrar_compra_articulo'
//                        );
    
    
    
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
    
    function initFRM(){
        
        $('.fancybox-wrap select').chosen({
            width: "100%",
            //allow_single_deselect: true,
            no_results_text: "_No hay datos",
            //disable_search_threshold: 10
    });
    
   
    
        loadFecha();
    
  
    }
    
    tablaCompras(G_compras);
    
    tablaPagos(G_pagosCompra);
    
    tablaComprobantes(G_comprobantesCompra);
    
    if(G_compras.length!=0){
        
         actualizarTotal();
        
    };
    
   
    
 $('#compra').on('submit',function(){
     
       
        
        var dataPOST=$('#bajass').serialize()+'&'
                    +tCompras.$('select, input').serialize()+'&'
                    +tablaPagos.$('select, input').serialize()+'&'
                    +tablaComprobantes.$('select, input').serialize()+'&'
                    +'cod_compra='+$('input[name="cod_compra"]').val()+'&'
//                    +'cod_caja='+$('select[name="cod_caja"]').val()+'&'
                    +'cod_proveedor='+$('select[name="cod_proveedor"]').val()+'&'
                    +'fecha='+$('input[name="fecha"]').val();
        
        $.ajax({
                
                url: BASE_URL+"compras/guardarCompra",
                type: "POST",
                data: dataPOST,
                dataType:"JSON",
                cache:false,
                success:function(respuesta){
                    //alert(respuesta);
                    
                    if(respuesta.codigo == 1){
                            
                        $.gritter.add({
                                             
                                             text: langFRM.validacion_ok,
                                             sticky: false,
                                             time: '3000',
                                             class_name:'gritter-success'
                            });
                        $.fancybox.close(true);
                        oTable.fnDraw();
                   
               }else{
                    
                        $.gritter.add({
                                    title: 'Uppss',
                                    text: respuesta.msgerror,
                                    sticky: false,
                                    time: '3000',
                                    class_name:'gritter-error'
                    });
                   
                   
               }
                }
            
        });
        
        return false;
    });
    
});

