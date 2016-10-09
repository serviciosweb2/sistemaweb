
//function guardarCobrosCola(cobros){
//        
//        console.log('guardarCobrosCola',cobros);
//        
//        $.ajax({
//                url: BASE_URL+"cobros/guardarCobrosCola",
//                type: "POST",
//                data: {'cobros':cobros},
//                dataType:"",
//                cache:false,
//                success:function(respuesta){
//                 console.log(respuesta);
//                 // cuando hay success  modifico la marca en el storage para que el sistema no vuelva a actualizar
//                  sessionStorage.setItem("cobros_sincronizado",'1');
//                }
//        });
//        
//    }
//
//function sincronizarCobros(){
//        var envio=[];
//        if(sessionStorage.getItem("cobros_sincronizado")==null)
//        {// si no existe el item quiere decir que es la primera vez que se llama a la funcion
//            
//            sessionStorage.setItem("cobros_sincronizado", "0");
//        
//        }
//        
//         
//        if(sessionStorage.getItem("cobros_sincronizado")==0)
//        {// si no esta sincronizado  traer los ultimos 5 cobros
//            //poner en proceso los que esten en cola
//           
//            db.transaction(function(tx)
//            {// selecciono los que esten en cola y los envio
//                
//                tx.executeSql('SELECT * FROM cobros join cobros_mediospago on cobros_mediospago.cod_cobro=cobros.codigo WHERE cobros.estado="cola"',[],function(tx,results){
//                     var len=results.rows.length;
//                     var i;
//                     
//                          for(i=0; i<len; i++) {
//                            
//                                envio.push( results.rows.item(i));
//                            
//                          }
//                    //console.log('ENVIO',envio);        
//                    guardarCobrosCola(envio);
//                    
//                    
//                })
//                
//                
//            })
//           
//        }
//          
//         
//        
//    }
//
//if(window.navigator.onLine)
//{
//    sincronizarCobros();
//}




 

 