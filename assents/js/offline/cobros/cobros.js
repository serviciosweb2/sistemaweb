console.log('CONTROL DE COBROS OFFLINE CARGADO');
//window.parent.location='http://norfipc.com'
 //alert('!');
var timer='';
var db='';
var bancos=[];
var tarjetas=[];
var cajas=[];
var terminalesTarjetas = [];
var frmCobro;
var nombre;
var ultimoCobro='';
db = openDatabase( localStorage.getItem('tkoff'), "1.0", "descripcion", 5*1024*1024);

var tablaCobrosOffline='';
var tableCteCte='';
var ctacteARRAY='';

var ingresoDeUsuario = 0;

var errorIngresoUsuario=0;

function recalcularCTACTE()
{
    var total= frmCobro.total_cobrar;
    var totalFtdo = parseFloat(total.replace( BASE_SEPARADOR, "."));
    var nuevaCTACTE=[];
    $(frmCobro.checkctacte).each(function(k,seleccionada)
    {//recorre las ctas seleccionadas
        $(ctacteARRAY).each(function(i,ctacte)
        {//recorre las ctas que que hay en la base
            
            if(seleccionada == ctacte.codigo )
            {// descubre la igualdad 
                var importe = parseFloat(ctacte.importe);
                if( totalFtdo >= importe )
                {
                   // alert('puede saldar');
                    totalFtdo = totalFtdo - importe ;
                    // se elimina esta cuenta del array ya que se pudo pagar completa
                    ctacteARRAY.splice(i,1);
                }
                else
                {//si el total es menor al valor de la ctacte
                    //alert('no puede saldar');
                    var saldo = importe - totalFtdo;
                    // se debe actualizar esta cuenta corriente en el array , una parte se pudo pagar
                    ctacteARRAY[i]['importe'] = String(saldo);
                    ctacteARRAY[i]['importeformateado'] = String(saldo.replace( ".",BASE_SEPARADOR));
                    ctacteARRAY[i]['pagado']= String(totalFtdo);
                    ctacteARRAY[i]['pagadoformateado']  = String(saldo.replace( ".",BASE_SEPARADOR));
                    totalFtdo=0;
                }
            }
            
        });
    });
    console.log('NUEVAS CTACTES',ctacteARRAY);
    //return totalFtdo;
}

function imprimirRecibo()
{
    
    $.print("#recibo",{
       globalStyles : false,
        noPrintSelector : ".no-print",
        stylesheet : null,
    });
    $.fancybox.close();
}

function frmRecibo()
{
    
    $('#recibo h4').html(LANG.recibo_de_cobros+' <strong>'+frmCobro.fecha_cobro+'</strong>');
    $('.alumnoRBO').html(LANG.ALUMNO+'   <strong>'+nombre+'</strong>');
    $('.medioRBO').html(LANG.medio_pago+'   <strong>'+getMedio(frmCobro.medio_cobro)+'</strong>');
    $('.fechaRBO').html(LANG.fecha+'   <strong>'+frmCobro.fecha_cobro+'</strong>');
    $('.importeRBO').html(LANG.facturar_importe+'   <strong>'+BASE_SIMBOLO+frmCobro.total_cobrar+'</strong>');
    
    
    $.fancybox.open(['#recibo'],{
        
                maxWidth	: 1000,
		maxHeight	: 600,
		width		: '90%',
		height		: 'auto',
		autoSize	: true,
		closeClick	: false,
                padding         : '0',
		openEffect	: 'none',
		closeEffect	: 'none',
                afterClose:function(){
                    resetearFRM();
                },
                helpers: {
                            overlay: null
                        }
    });
    
    
}

function msjError() 
{


    var cadena = LANG.formato_esperado + 'XX' + BASE_SEPARADOR;

    for (var i = 0; i < 2; i++) 
    {

        cadena += 'X';
    }

    return cadena;
}

function validarEntrada(elemento)
{
        //ingresoDeUsuario = 1;
        clearTimeout(timer);
        
       if(!elemento)
        {
            elemento=$('input[name="total_cobrar"]');
        }
        
        var resultado =true;
        
        var valorEntrada = $(elemento).val();

        //console.log('ENTRA ' + valorEntrada);

        //var valorConPunto=valorEntrada.replace("?",".");

        if (valorEntrada.length == 0) 
        {
            $(elemento).closest('.input-group').removeClass('has-error');
        } 
        else 
        {

            var pattern = new RegExp("^([0-9]{0,10})\\" + BASE_SEPARADOR + "?([0-9]{1," + BASE_DECIMALES + "})$");

            
            if (pattern.test(valorEntrada)) 
            {
                resultado=true;
                $(elemento).closest('.input-group').removeClass('has-error');
            }
            else 
            {
                
                resultado=false;
                
                $(elemento).closest('.input-group').addClass('has-error');
                
                timer = setTimeout(function()
                {
                    console.log('TIMER 500');  
                    
                    $.gritter.add({
                        title: 'Upps!',
                        text: msjError(),
                        //image: $path_assets+'/avatars/avatar1.png',
                        sticky: false,
                        time: '3000',
                        class_name: 'gritter-error'
                    });
                     
                 },500);
                


            }

        }

        return resultado;
            
      
        
        

    }

(function()
{
    
  db.transaction(function(tx)
  {
      
      tx.executeSql('SELECT * FROM bancos',[],function(tx,rs)
      {
          
          for(var i=0; i<rs.rows.length; i++) 
          {
                    var row = rs.rows.item(i);
                    bancos.push(row);
                }
          
          tx.executeSql('SELECT * FROM tarjetas',[],function(tx,rs)
          {
              
              for(var i=0; i<rs.rows.length; i++) 
              {
                    var rowTarj = rs.rows.item(i);
                    tarjetas.push(rowTarj);
                }
                
                tx.executeSql('SELECT * FROM cajas',[],function(tx,rs)
                {
                    
                    for(var i=0; i<rs.rows.length; i++) 
                    {
                        var rowCajas = rs.rows.item(i);
                        cajas.push(rowCajas);
                    }
                    
                    tx.executeSql('SELECT * FROM terminales_tarjetas',[],function(tx,rs)
                    {

                        for(var i=0; i<rs.rows.length; i++) 
                        {
                            var rowTerminales= rs.rows.item(i);
                            terminalesTarjetas.push(rowTerminales);
                        }

                        init();
                    })
                    
                })
                
               
          })       
          
      },errorQUERY);
      
  },errorSQL); 
    
}());

console.log('MEDIOPAGO',mediosPago);

function getMedio(id)
{
    var nombre='';
    $(mediosPago).each(function(k,medio)
    {
        if(medio.codigo==id){
            
            nombre=medio.medio
        }
    })
    return nombre;
};

function validarEnvio(frmCobro)
{
    
    var retorno = true;
    
    for( i in frmCobro )
    {
        
        if( i!='checkctacte' && i!='total_cobrar')
        {
            
            if(frmCobro[i]=='')
            {
                
                retorno=false;
            }
            
        }
        
        
    }
    
    
    
    
    if(retorno == false)
    {
       
       $.gritter.add({
                    
            text: LANG.campos_vacios,
            sticky: false,
            time: '3000',
            class_name:'gritter-error'
        });
        
        return retorno;
   }
   else
   {
        for( i in frmCobro )
        {
            if( i!='checkctacte' && i!='total_cobrar')
            {
                var resultadoValidacion = validarFormato(i,frmCobro[i]);
                
                if(resultadoValidacion.codigo == 0)
                {//si es incorrecto
                
                    $.gritter.add({
                    
                        text: resultadoValidacion.msg,
                        sticky: false,
                        time: '3000',
                        class_name:'gritter-error'
                    });
                    
                    return false;
                }
            }

        }
        
        return true;
   }
    
}

function validarFormato(tipo,valor)
{
    
    var retorno = {codigo:1,msg:''};
    
    switch(tipo)
    {
        case 'medio_tarjeta_autorizacion':
            
            var pattern = new RegExp("^[a-z0-9]{0,6}$");

            var test = pattern.test(valor);
            
            var terminalSeleccionada = $('select[name="pos_tarjeta"]').val();
            
            $(terminalesTarjetas).each(function(k,terminal)
            {
                if(terminal.codigo == terminalSeleccionada)
                {
                    if(terminal.cod_operador == 2)
                    {   
                        if (test != true) 
                        {// no paso el test
                            retorno.codigo = 0;
                            retorno.msg = ' El Cod.Autorizacion debe tener como maximo 6 caracteres alfanumericos ';
                        }
                    }
                }
            });
            
            break;
            
        case 'medio_tarjeta_cupon':
            
            var pattern = new RegExp("^[a-z0-9]{0,6}$");
            
            var test = pattern.test(valor);
            
            if (test != true) 
            {// no paso el test
                retorno.codigo = 0;
                retorno.msg = ' El cupon debe tener como maximo 6 caracteres alfanumericos ';
            }
            
            break;
        
        
        default:
            
            
    }
    
    return retorno;
}

function guardarFINAL(element,event)
{
   
    var frm = $('#frmpin').serializeJSON();

    
    var strMD5 = $().crypt({
                method: "md5",
                source: frm.pinACCESS
            });
            
    if( strMD5 == localStorage.tkpin )
    {
        
        db.transaction(function(tx)
        {  
            ultimoCobro++;
            //alert('guardo con id'+ultimoCobro);
                    tx.executeSql('INSERT INTO cola("id","nombre","registro","estado") VALUES(?,?,?,?)',[ultimoCobro,'cobro',JSON.stringify(frmCobro),'cola'],
                    function(tx,rs)
                    {//SUCCESS query
                        var ctacteACTUALIZADA=[];
                        console.log('registro insertado');
                        console.log('ACTUALIZAR CTACTE',ctacteARRAY);
                        console.log('ACTUALIZAR FRMCOBRO',frmCobro);
                   // el array de cuanta corriente puede estar vacio.se debe respetar el orde en que se selecciono  
                        if(ctacteARRAY.length != 0)
                        {
                           recalcularCTACTE();// modifica una variable global
                        } 
                       
                        tx.executeSql("UPDATE alumnos SET   ctacte= ?  WHERE alumnos.id='"+frmCobro.alumnos+"'",[JSON.stringify(ctacteARRAY)]);


                    },
                    function(tx,e)
                    {// error query

                         console.log('error QUERY insert',e.message);

                    })
        
        },function()
        {//ERROR TRANSACTION
            
            console.log('error TRANSACTION insert');
            
        
        },function()
        {//success TRANSACTION

            $.fancybox.close(true);
            
            $.gritter.add({
            title: 'OK!',
            text: LANG.validacion_ok,
            //image: $path_assets+'/avatars/avatar1.png',
            sticky: false,
            time: '3000',
            class_name:'gritter-success'
        });

            //listarCobrosOffline();
            getCobros_dbLocal();
            //resetearFRM();
            frmRecibo();
        });
    
        
    }
    else
    {
        
        $.gritter.add({
                                    
            text: LANG.pin_incorrecto,
            //image: $path_assets+'/avatars/avatar1.png',
            sticky: false,
            time: '3000',
            class_name:'gritter-error'
        });
        
    }
    event.preventDefault();
    
}

function guardar()
{
   
    frmCobro = $('#cobro').serializeJSON();
    
    
    if(validarEnvio(frmCobro) && validarEntrada() == true)
    {
        //alert('se puede guardar el cobro');
        frmPIN();
    }
}

function errorQUERY(tx,e)
{
    console.log('error query',e.message);
    return true
}

function errorSQL(tx,e)
{
    console.log('error transaction',e.message);
    return true;
}

function volver()
{
    
    $('#modulo1,#modulo2').removeClass('hide');
    $('.btnPaso1').removeClass('hide');
    $('.btnPaso2').addClass('hide');
    $('#modulo3').addClass('hide');
    
   $.fancybox.update();
    
}

function getOptions(valor,lista)
{
    
    var retorno='';
    
    switch(lista)
    {
        
        case 'tipoCheque':
            retorno+='<option></option>';
            $(tipoCheque).each(function(k,tipo)
            {
                retorno+='<option value="'+tipo.id+'">'+tipo.nombre+'</option>';
            });
            break;
        
        case 'tipoTarjeta':
            
            retorno+='<option></option>';
            $(tarjetas).each(function(k,option)
            {
                retorno+='<option value="'+option.codigo+'">'+option.nombre+'</option>';
            });
            
            break;
        
        case 'banco':
          
            retorno+='<option></option>';
            $(bancos).each(function(k,option)
            {
                retorno+='<option value="'+option.codigo+'">'+option.nombre+'</option>';
            });
        
            break
        
        case 'caja':
            
            retorno+='<option></option>';
            
            var medioSeleccionado = $('select[name="medio_cobro"]').val();
            
            console.log('MEDIO SELECCIONADO',medioSeleccionado);
            console.log('CAJAS',cajas);
            
            $(cajas).each(function(k,caja)
            {
                var mediosDeLacaja = JSON.parse(caja.medios);
                console.log('MEDIOS DE LA CAJA',mediosDeLacaja);
                $(mediosDeLacaja).each(function(m,medioDeLaCaja)
                {
                    if(medioSeleccionado == medioDeLaCaja.cod_medio )
                    {console.log('ACEPTA ESTE MEDIO ',mediosDeLacaja);
                        retorno+='<option value="'+caja.codigo+'">'+caja.nombre+'</option>';
                    }
                });
                
            });
            
            break;
            
        case 'pos_tarjeta':
            
            retorno+='<option></option>';
            
            $(terminalesTarjetas).each(function(k,terminalTarjeta)
            {
                retorno+='<option value="'+terminalTarjeta.codigo+'">'+terminalTarjeta.cod_interno+'['+terminalTarjeta.nombre+']</option>';
            
            });
            
            
            
            
            
            break;
        
    }
    
    return retorno;
}

function habilitarTarjetas()
{
    var terminalSeleccionada = $('select[name="pos_tarjeta"]').val();
    var retorno='';
    
    $(terminalesTarjetas).each(function(t,terminal)
    {// recorre las terminales
        if(terminal.codigo == terminalSeleccionada)
        {// encuentra la que seleccionaron
            $(JSON.parse(terminal.tarjetas)).each(function(i,tarjeta)
            {// recorre las tarjetas que acepta la terminal seleccionada

                $(tarjetas).each(function(a,tarjeta_de_la_lista)
                {// recorre las tarjetas del sistema
                    if(tarjeta.cod_tipo == tarjeta_de_la_lista.codigo)
                    {// si encuentra  tarjetas que acepte la terminal seleccionada las manda a vista
                        console.log('tarjetas aceptadas',tarjeta.cod_tipo );
                        retorno+='<option value="'+tarjeta_de_la_lista.codigo+'">'+tarjeta_de_la_lista.nombre+'</option>';
                    }
                });


            })
        }
    });
        
   $('select[name="tarjetas"]').html(retorno);
   $('select[name="tarjetas"]').trigger('chosen:updated');
   
}

function calcular_Total()
{
    
    var total=0;
    
    var valores = [];
    
    var alumno='';
    
    var cod_alumnos=$('select[name="alumnos"]').val();
    
    tableCteCte.$('input[name="checkctacte[]"]:checked').each(function(k,element){
        
        valores.push($(element).val());
        
    });
    console.log('VALORES',valores);
    $(ctacteARRAY).each(function(k,cuenta){
        
        $(valores).each(function(i,valor){
          
            if(cuenta.codigo == valor){
                
                total = ( parseFloat(total) + parseFloat(cuenta.importe) );
            }
        })
        
    });
    //var formateado = String(total.replace(".",BASE_SEPARADOR));
    //alert(total);
    $('input[name="total_cobrar"]').val(String(total).replace(".",BASE_SEPARADOR))
    
}

function resetearFRM()
{
    
    volver();
    
    $('select[name="medio_cobro"]').val('').trigger('chosen:updated');
    $('select[name="alumnos"]').val('').trigger('chosen:updated');
    $('input[name="total_cobrar"]').val('');
    tableCteCte.clear().draw();
    $('input[name="pinACCESS"]').val('');
    
    
}

function verDetallesMedios()
{
    
    var opciones='';
    
    if(validarPaso1())
    {
        
        if( $('input[name="total_cobrar"]').val() == '')
        {
            calcular_Total();
        }
        
        var x= $('select[name="medio_cobro"]').val();// toma el valor de medio
    
        var cargarSelect=[];
        
        $('#modulo1,#modulo2').addClass('hide');
        $('.btnPaso1').addClass('hide');
        $('.btnPaso2').removeClass('hide');
        //$('#modulo3').empty().removeClass('hide');
        
        $('#modulo3 .dinamico').remove();

        switch(x)
        {

            case '1'://EFECTIVO
                opciones+='<div class="col-md-2 form-group dinamico">';
                opciones+='<label>'+LANG.medio_caja_factura+'</label><select class="form-control" name="caja" data-placeholder="'+LANG.SELECCIONE_UNA_OPCION+'">'+getOptions('caja','caja')+'</select>';
                opciones+='</div>';


                break;

            case '3'://tarjeta
                opciones+='<div class="col-md-4 form-group dinamico">';
                opciones+='<label>'+LANG.medio_caja_factura+'</label><select class="form-control" name="caja" data-placeholder="'+LANG.SELECCIONE_UNA_OPCION+'">'+getOptions('caja','caja')+'</select>';
                opciones+='</div>';

//                opciones+='<div class="col-md-4 form-group">';
//                opciones+='<label>banco</label><select class="form-control" name="medio_tarjeta_banco" data-placeholder="'+LANG.SELECCIONE_UNA_OPCION+'">'+getOptions('medio_tarjeta_banco','banco')+'</select>';
//                opciones+='</div>';

                opciones+='<div class="col-md-4 form-group dinamico">';
                opciones+='<label>'+LANG.terminales+'</label><select class="form-control" name="pos_tarjeta" data-placeholder="'+LANG.SELECCIONE_UNA_OPCION+'" onchange="habilitarTarjetas();">'+getOptions('','pos_tarjeta')+'</select>';
                opciones+='</div>';
                
                opciones+='<div class="col-md-4 form-group dinamico">';
                opciones+='<label>'+LANG.tipo_tarjeta+'</label><select class="form-control" name="tarjetas" data-placeholder="'+LANG.SELECCIONE_UNA_OPCION+'">'+getOptions('medio_tarjeta_tipo','tipoTarjeta')+'</select>';
                opciones+='</div>';

                opciones+='<div class="col-md-4 form-group dinamico">';
                opciones+='<label>'+LANG.codigo_cupon+'</label><input class="form-control" name="medio_tarjeta_cupon">';
                opciones+='</div>';
                
                opciones+='<div class="col-md-4 form-group dinamico">';
                opciones+='<label>'+LANG.codigo_autorizacion+'</label><input class="form-control" name="medio_tarjeta_autorizacion">';
                opciones+='</div>';


                break;

            case '4'://cheque
                opciones+='<div class="col-md-4 form-group dinamico">';
                opciones+='<label>'+LANG.medio_caja_factura+'</label><select class="form-control" name="caja" data-placeholder="'+LANG.SELECCIONE_UNA_OPCION+'">'+getOptions('caja','caja')+'</select>';
                opciones+='</div>';

                opciones+='<div class="col-md-4 form-group dinamico">';
                opciones+='<label>'+LANG['medio-tajeta-banco-factura']+'</label><select class="form-control" name="medio_cheque_banco" data-placeholder="'+LANG.SELECCIONE_UNA_OPCION+'">'+getOptions('medio_cheque_banco','banco')+'</select>';
                opciones+='</div>';

                opciones+='<div class="col-md-4 form-group dinamico">';
                opciones+='<label>'+LANG.tipo_cheque+'</label><select class="form-control" name="medio_cheque_tipo" data-placeholder="'+LANG.SELECCIONE_UNA_OPCION+'">'+getOptions('medio_cheque_tipo','tipoCheque')+'</select>';
                opciones+='</div>';

                opciones+='<div class="col-md-4 form-group dinamico">';
                opciones+='<label>'+LANG.fecha+'</label><input class="form-control fechaCOBRO" name="medio_cheque_fecha">';
                opciones+='</div>';

                opciones+='<div class="col-md-4 form-group dinamico">';
                opciones+='<label>'+LANG['medio_cheque_numero_factura']+'</label><input class="form-control" name="medio_cheque_numero">';
                opciones+='</div>';

                opciones+='<div class="col-md-4 form-group dinamico">';
                opciones+='<label>'+LANG['medio_cheque_emisor_factura']+'</label><input class="form-control" name="medio_cheque_emisor">';
                opciones+='</div>';


                break;

            case '6'://deposito bancario
                opciones+='<div class="col-md-4 form-group dinamico">';
                opciones+='<label>'+LANG.medio_caja_factura+'</label><select class="form-control" name="caja" data-placeholder="'+LANG.SELECCIONE_UNA_OPCION+'">'+getOptions('caja','caja')+'</select>';
                opciones+='</div>';

                opciones+='<div class="col-md-4 form-group dinamico">';
                opciones+='<label>'+LANG['medio-deposito-banco-factura']+'</label><select class="form-control" name="medio_deposito_banco" data-placeholder="'+LANG.SELECCIONE_UNA_OPCION+'">'+getOptions('medio_deposito_banco','banco')+'</select>';
                opciones+='</div>';

                opciones+='<div class="col-md-4 form-group dinamico">';
                opciones+='<label>'+LANG.fecha+'</label><input class="form-control fechaCOBRO" name="medio_deposito_fecha">';
                opciones+='</div>';

                opciones+='<div class="col-md-4 form-group dinamico">';
                opciones+='<label>'+LANG['medio-tranferencia-nro-transaccion-factura']+'</label><input class="form-control" name="medio_deposito_transaccion">';
                opciones+='</div>';

                opciones+='<div class="col-md-4 form-group dinamico">';
                opciones+='<label>'+LANG['medio_deposito_cuenta_factura']+'</label><input class="form-control" name="medio_deposito_cuenta">';
                opciones+='</div>';


                break;

            case '7'://transferencia
                opciones+='<div class="col-md-4 form-group dinamico">';
                opciones+='<label>'+LANG.medio_caja_factura+'</label><select class="form-control" name="caja" data-placeholder="'+LANG.SELECCIONE_UNA_OPCION+'">'+getOptions('caja','caja')+'</select>';
                opciones+='</div>';

                opciones+='<div class="col-md-4 form-group dinamico">';
                opciones+='<label>'+LANG['medio-tranferencia-banco-factura']+'</label><select class="form-control" name="medio_transferencia_banco" data-placeholder="'+LANG.SELECCIONE_UNA_OPCION+'">'+getOptions('medio_transferencia_banco','banco')+'</select>';
                opciones+='</div>';

                opciones+='<div class="col-md-4 form-group dinamico">';
                opciones+='<label>'+LANG.fecha+'</label><input class="form-control fechaCOBRO" name="medio_transferencia_fecha">';
                opciones+='</div>';

                opciones+='<div class="col-md-4 form-group dinamico">';
                opciones+='<label>'+LANG['medio-tranferencia-nro-transaccion-factura']+'</label><input class="form-control" name="medio_transferencia_numero">';
                opciones+='</div>';

                opciones+='<div class="col-md-4 form-group dinamico">';
                opciones+='<label>'+LANG['medio_deposito_cuenta_factura']+'</label><input class="form-control" name="medio_transferencia_cuenta">';
                opciones+='</div>';




                break;
        }


        //$('#modulo3').html(opciones);
        
        $('#modulo3').append(opciones);
        
//        $('#modulo3').find('select').chosen({
//            'width':'100%',
//        });

        $('#modulo3').find('.dinamico select').chosen({
            'width':'100%',
        });

        $('.fechaCOBRO').datepicker();
        $('.fechaCOBRO').datepicker('setDate','now');
        $.fancybox.update();
        
    }
    
    
    
};

function addCobro(cobro)
{
    console.log('listado este cobro',cobro);
    
   //ultimoCobro = cobro.codigo;
    tablaCobrosOffline.row.add([
                      cobro.codigo,
                      cobro.nombre,
                      cobro.importe,
                      cobro.saldo,
                      getMedio(cobro.medio),
                      cobro.fecha,
                      cobro.estado
                    ]).draw();
    
}

function addCobros(cobros)
{
    
   //ultimoCobro = cobro.codigo;
    tablaCobrosOffline.rows.add(cobros).draw();
    

}

function listarCobrosOffline()
{
    console.log('entra al select listarOFFLINE');
    
    db.transaction(function(tx)
    {
        var cobros=[];
        var cobroDECODE=[];
        var cobrosID=[];
        var cobroAlumno=[];
        var idCobro=[];
        
        tx.executeSql('SELECT cola.* FROM cola WHERE cola.nombre = "cobro"',[],function(tx,rs)
        {
         
                for(var i=0; i<rs.rows.length; i++)
                {
                   //alert('listar');
                     cobros[i]= rs.rows.item(i);
                     cobroDECODE[i] = JSON.parse(cobros[i].registro);
                     var id = cobroDECODE[i]['alumnos'];
                     cobrosID[i] = id;
                     cobroAlumno[id]=cobroDECODE[i];
                     
                }
                console.log('cobrosID',cobrosID);
//                console.log('COBROSID',cobrosID);
                
                tx.executeSql('SELECT alumnos.* FROM alumnos WHERE alumnos.id IN ('+cobrosID+')',[],function(tx,result)
                {
                        
                         
                            
                            for(var a=0; a < result.rows.length; a++)
                            {
                                //console.log('id alumnos',result.rows.item(a));
                               //nombre = result.rows.item(a)['nombre'];
                                //alert(cobros[a].id);
                                
                               
                                $(cobros).each(function(k,valor)
                                {
                                    ultimoCobro = cobros[k].id;
                                    var decode= JSON.parse(valor.registro);
                                    
                                    console.log('valor',result.rows.item(a));
                                    
                                    if(decode.alumnos == result.rows.item(a)['id'])
                                    
                                    {
                                        
                                          addCobro({
                                            codigo:cobros[k].id,
                                            nombre:result.rows.item(a)['nombre'],
                                            importe:BASE_SIMBOLO+decode.total_cobrar,
                                            saldo:'',
                                            medio: decode.medio_cobro,
                                            fecha:decode.fecha_cobro,
                                            estado:cobros[k].estado
                                        })
                                        
                                    }
                                });
                                
                                console.log('item',result.rows.item(a));
                                console.log('cobro',cobros);
                                console.log('---------------------------------');
                                
//                                addCobro({
//                                    codigo:cobros[a].id,
//                                    nombre:result.rows.item(a)['nombre'],
//                                    importe:BASE_SIMBOLO+cobroAlumno[result.rows.item(a)['id']]['total_cobrar'],
//                                    saldo:'',
//                                    medio:cobroAlumno[result.rows.item(a)['id']]['medio_cobro'],
//                                    fecha:cobroAlumno[result.rows.item(a)['id']]['fecha_cobro'],
//                                    estado:cobros[a].estado
//                                });
                            }
                        //alert(ultimoCobro);
                    
                    },errorSQL);
          
        },errorSQL);
           
         
    })
}

function getCobros_dbLocal()
{

    var listaCobros=[];
    tablaCobrosOffline.clear().draw();
//    db.transaction(function(tx){
//      
//       tx.executeSql('SELECT cobros.*,alumnos.nombre FROM cobros INNER JOIN alumnos ON cobros.cod_alumno = alumnos.id',[],function(tx,rs){
//                var misCobros = [];
//                for(var i=0; i<rs.rows.length; i++) {
//                    var row = rs.rows.item(i);
//                    console.log('modelando cobro ->',row);
//                    misCobros.push([
//                      row.codigo,
//                      row.nombre,
//                      row.importe,
//                      row.saldo,
//                      getMedio(row.medio),
//                      row.fecha,
//                      row.estado
//                    ]);
//                    //addCobro(row);
//                    //ultimoCobro = row.codigo;
//                     
//                }
//                
//                addCobros(misCobros);
//          
//        },errorSQL);
//           
//         
//    },function(tx,e){console.log('error',e.message)},function(){
//        
//        listarCobrosOffline();
//        
//    }); 
    
    listarCobrosOffline();
}

function validarPaso1()
{
    
    var fecha = $('input[name="fecha_cobro"]').val();
    var alumno = $('select[name="alumnos"]').val();
    var medio = $('select[name="medio_cobro"]').val();
    var msj='';
    var pasarApado2=true;
    if(fecha=='')
    {
        msj+= LANG.seleccione_fecha+'<br>';
        pasarApado2=false;
        
    }
    
    
    if(alumno == null)
    {
        msj+= LANG.seleccione_alumno+'<br>';
        pasarApado2=false;
            
    }
    
//    if(medio=='')
//    {
//        
//        msj+= LANG.seleccione_medio_pago+'<br>';
//        pasarApado2=false;
//        
//    }
    
    if(pasarApado2==false)
    {
        
        $.gritter.add({

            text: msj,
            sticky: false,
            time: '3000',
            class_name:'gritter-error'
        });
        
        return pasarApado2;
    }
    
    return true;
    
}

function addCtaCte(c)
{
    console.log('llama',c);
    var deudaPasiva = '';
    if(c.habilitado == 2)
    {
        deudaPasiva = '<i class="icon-ok icon-info-sign" title="undefined"></i>';
    }
    
     tableCteCte.row.add([
                    '<input type="checkbox" name="checkctacte[]" value="'+c.codigo+'" data-saldo="'+c.saldo+'">',
                    c.descripcion + deudaPasiva,
                    c.fechavenc,
                    BASE_SIMBOLO + c.saldocobrar
                    ]).draw();
}

function listarCTACTE(element)
{
    //alert('!');
   var idAlumno = $(element).val();
   nombre = $(element).find('option:selected').text();
   tableCteCte.clear().draw();
   db.transaction(function(tx){
       var ctacte='';
       tx.executeSql('SELECT alumnos.ctacte FROM alumnos where alumnos.id = "'+idAlumno+'" ',[],function(tx,rs){
           
           for(var i=0; i<rs.rows.length; i++) {
                    ctacte = rs.rows.item(i);
                }
            
            if(ctacte!=''){
                
                ctacteARRAY= JSON.parse(ctacte.ctacte);
                console.log('CTACTEARRAY =',ctacteARRAY);
                $(ctacteARRAY).each(function(k,cuenta){
                    
                    if(cuenta.habilitado == 1 || cuenta.habilitado == 2 )
                    {
                       
                       addCtaCte(cuenta); 
                    }
                    
                    
                });
            
           }
           
           $.fancybox.update();
               
       })
       
   })
    
}

function frmPIN()
{
    
    $.fancybox.open({href:'#confirmarpin'},{
                maxWidth	: 1000,
		maxHeight	: 600,
		width		: '90%',
		height		: 'auto',
		autoSize	: true,
		closeClick	: false,
                padding         : '0',
		openEffect	: 'none',
		closeEffect	: 'none',
                afterClose:function(){
                    resetearFRM();
                },
                helpers: {
                                    overlay: null
                                }
    });
}

function init()
{
    
    $(document).ready(function()
    {
        console.log('TERMINALES RECUPERADAS',terminalesTarjetas);    
        var breadcrumb = '<li><i class="icon-home home-icon"></i><a href="#">'+LANG.home+'</a></li><li><a href="#">'+LANG.cobros+'</a></li>';

        $('.breadcrumb').html(breadcrumb);


        console.log('BANCOS',bancos);
        console.log('TARJETAS',tarjetas);

        $("select[name='alumnos']").ajaxChosen({
        minLength: 0,
        queryLimit: 10,
        delay: 100,
        chosenOptions: {width:'100%',max_selected_options: 1},
        searchingText: LANG.buscando,
        noresultsText: LANG.no_hay_resultados,
        initialQuery: true
    },            
        function (options, response) {   

        db.transaction(function(tx){
            var terms={};

            tx.executeSql('SELECT alumnos.* from alumnos WHERE alumnos.nombre LIKE "%'+options.term+'%" LIMIT 10',[],function(tx,rs){

                for(var i=0; i<rs.rows.length; i++) {
                    var row = rs.rows.item(i);

                    terms[row.id]=row.nombre;

                }

               response(terms);

        },errorSQL);

        });


    });

        tablaCobrosOffline = $('#cobrosOffline').DataTable({"bLengthChange":false});
        tablaCobrosOffline.order( [ 0, 'desc' ]);
        tablaCobrosOffline.column([3]).visible(false);
        
        tableCteCte = $('#detalleCtacte').DataTable({
            "iDisplayLength": 3,
            "bLengthChange": false,
        });

        $(".various").fancybox({
                    maxWidth	: 1000,
                    maxHeight	: 600,
                    width		: '90%',
                    height		: 'auto',
                    autoSize	: true,
                    closeClick	: false,
                    padding         : '0',
                    openEffect	: 'none',
                    closeEffect	: 'none',
                    afterClose:function(){
                        resetearFRM();
                    },
                    helpers: {
                                        overlay: null
                                    }
            });

        $('select').chosen({'width':'100%'});

        $("input[name='fecha_cobro']").datepicker();

        $("input[name='fecha_cobro']").datepicker("setDate", "now");

        $('.pagination').on('click',function()
        {
            $.fancybox.update();
        });

        getCobros_dbLocal();
    
    
});
    
}

function irSegundoPaso()
{
    if(validarPaso1())
    {
        if( $('input[name="total_cobrar"]').val() == '')
        {// calcula el total si todavia no fue calculado
            calcular_Total();
        }
        
        // se econden los modulos y botones que no se necesitan
        //se remueven elementos en caso de que existan y no se necesiten
        // se muesra el modulo que se requiere
        $('#modulo1,#modulo2').addClass('hide');
        $('.btnPaso1').addClass('hide');
        $('.btnPaso2').removeClass('hide');
        $('#modulo3.dinamico').remove();
        $('#modulo3').removeClass('hide');
        
        // se actualiza fancybox
        $.fancybox.update();
        
    }
}