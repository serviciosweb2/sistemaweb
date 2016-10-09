console.log('toquen en la base'+BASE_OFFLINE.token+'\n token en el local '+localStorage.getItem('tkoff'));
var db='';
function error(tx,e)
{
    console.log(e.message);
    return true;
}

function success()
{
    
};

document.addEventListener('offline'); 
        
function redirectOFFLINE()
{
            
    setTimeout(function()
    {
       window.location.href = BASE_URL+'offline'; 
    },4000);
            
}

function actualizarCola(respuesta,obj)
{
    
    if(respuesta.codigo == 1)
    {
        
        db.transaction(function(tx)
        {
            
            tx.executeSql(' DELETE FROM cola WHERE cola.id = "'+obj.id+'" ',[],function()
            {
                console.log('ROW COLA DELETE');
                
            });
            
        })
        
    }
    else
    {
        
//        $.gritter.add({
//                        
//                    text: 'error al sincronizar un cobro con el servidor.La sincronizacion continuara',
//                    //image: $path_assets+'/avatars/avatar1.png',
//                    sticky: false,
//                    time: '3000',
//                    class_name:'gritter-error'
//                });
        console.log('error al volcar este cobro',obj);
        
        
            db.transaction(function(tx)
            {
            
                tx.executeSql(' DELETE FROM cola WHERE cola.id = "'+obj.id+'" ',[],function()
                {
                    console.log('ROW COLA DELETE');

                });
            
            })
        
        
        
//        db.transaction(function(tx){
//            
//            tx.executeSql("UPDATE cola SET estado= ?, msjerror= ?,  WHERE cola.id='"+obj.id+"'",['error',respuesta.msgerror],function(){
//                
//                console.log('COLA ACTUALIZADO');
//                      
//            });
//            
//        })
        
    }
    
}

function ajaxCobro(obj)
{
    console.log('OFFLINE EN TRUE');
//    obj.offline = 1;
    var decode =  JSON.parse(obj.registro);
    
    var retorno='';
    
    decode.offline = true;
    
    $.ajax({
                url: BASE_URL+"cobros/guardarCobro",
                type: "POST",
                data: decode,
                dataType:"JSON",
                cache:false,
                async: false,
                success:function(respuesta)
                {
                    retorno = respuesta;
                    
                    actualizarCola(respuesta,obj);

                }
            });
        
        
    
}

function successGUARDAR(cola)
{
    // esta funcion rutea los envios
    
    console.log('COLA PARA EL ENVIO',cola);
    
    $(cola).each(function(k,registro)
    {
        if(registro.nombre=='cobro')
        {
            ajaxCobro(registro);
        }
    })
    
    init_SINCRONIZACION();
    
}

function enviarCola()
{
    console.log('COMIENZA A SINCRONIZAR DE LOCAL A SERVER');
    var cobros=[];
    
    db.transaction(function(tx)
    {
        tx.executeSql('SELECT * FROM cola',[],function(tx,rs)
        {
            
            for(var i=0; i<rs.rows.length; i++) {
                    var row = rs.rows.item(i);
                    cobros[i] = row;
                }
        })
    
    },function(){console.log('error TRANS enviarCOLA');},function()
    {
        
       successGUARDAR(cobros);
        
    })
}

function init_BASE()
{
   console.log('CREANDO BASE Y TABLAS');
   db = openDatabase( localStorage.getItem('tkoff') , "1.0", "descripcion", 5*1024*1024);

// creamos las tablas
   db.transaction(function (tx) {
       
    //tx.executeSql('CREATE TABLE IF NOT EXISTS cobros_detalle_medio (id INTEGER PRIMARY KEY AUTOINCREMENT,cod_cobro varchar(30),clave varchar(30),valor varchar(30))',[],success('cobros_detalle_medio'),error);
    tx.executeSql('CREATE TABLE IF NOT EXISTS cobros (codigo INTEGER(12),cod_alumno varchar(12), importe varchar(30),saldo varchar(30),medio varchar(30),fecha varchar(30),estado varchar(30))',[],success('cobros'),error);
    tx.executeSql('CREATE TABLE IF NOT EXISTS alumnos (id INTEGER(12),nombre varchar(30),ctacte varchar(1000))',[],success('alumnos'),error);
    tx.executeSql('CREATE TABLE IF NOT EXISTS cola (id INTEGER(12),nombre varchar(30),registro varchar(1000),estado varchar(50),msjerror varchar(50))',[],success('alumnos'),error);
    
    tx.executeSql('CREATE TABLE IF NOT EXISTS bancos (codigo  INTEGER(12),nombre varchar(30),banco_asociado varchar(1000))',[],success('bancos'),error);
    tx.executeSql('CREATE TABLE IF NOT EXISTS tarjetas (codigo INTEGER(12),nombre varchar(30),codigo_pais varchar(1000))',[],success('tarjetas'),error);
    tx.executeSql('CREATE TABLE IF NOT EXISTS cheques (id INTEGER(12),nombre varchar(30),codigo_pais varchar(1000))',[],success('cheques'),error);
    tx.executeSql('CREATE TABLE IF NOT EXISTS cajas (codigo INTEGER(12),nombre varchar(30), estado varchar(200), desactivada varchar(200), medios varchar(1000))',[],success('cajas'),error);
    tx.executeSql('CREATE TABLE IF NOT EXISTS terminales_tarjetas (codigo INTEGER(12),cod_punto_venta  INTEGER(30), cod_interno INTEGER(30), estado varchar(200), tipo_captura varchar(200), nombre  varchar(1000), tarjetas varchar(1000), cod_operador INTEGER(12))',[],success('cajas'),error);
   });
}

function lastId(obj,last_id,k){
    
    if(k == obj.length-1){
                        
        localStorage.last_id = last_id;
        
    }
    
}

function successTRANSACTION(ultimoid, ultimoId_server, ultimoId_bancos, ultimoId_server_bancos , ultimoId_Tarjetas, ultimoId_server_Tarjetas)
{
    var cerrarNotificacion = true;
    
    
    if(ultimoid != ultimoId_server)
    {
        cerrarNotificacion = false;
    }
    
    
    
    if(ultimoId_bancos != ultimoId_server_bancos)
    {
        cerrarNotificacion = false;
    }
    
    
    
    if(ultimoId_Tarjetas != ultimoId_server_Tarjetas)
    {
         cerrarNotificacion = false;
    }
    
    
    
    
    
    if(cerrarNotificacion)
    {
        console.log('llama al timer y cierro la notyficacion');
        $('#notifySINCRO').hide();
        setTimeout(function(){ init_SINCRONIZACION()},120000);
    }
    else
    {
        console.log('dejo la notyficacion en vista, aun hay registros');
        //init_SINCRONIZACION();
        enviarCola();
    }
    
    
    
    
//    if(ultimoid == ultimoId_server)
//    {
//        console.log('llama al timer y cierro la notyficacion');
//        $('#notifySINCRO').hide();
//        setTimeout(function(){ init_SINCRONIZACION()},120000);
//        
//    }
//    else
//    {
//        console.log('dejo la notyficacion en vista, aun hay registros');
//        //init_SINCRONIZACION();
//        enviarCola();
//    }
    
    localStorage.last_id = ultimoid;
    localStorage.ultimoId_bancos = ultimoId_bancos;
    localStorage.ultimoId_Tarjetas = ultimoId_Tarjetas;
}

function actualizarBaseLocal(respuesta)
{
    
   
    var result = [];
    var resultCobros = [];
    var resultBancos = [];
    
    var alumnos=respuesta.alumnos;
    var cobros= respuesta.cobros;
    var bancos= respuesta.bancos;
    var tarjetas= respuesta.tipos_tarjeta;
    var cajas = respuesta.cajas;
    var terminales_tarjetas = respuesta.terminales_tarjetas;
    
        db.transaction(function(tx)
        {
            /*------------------------------------------------
             * ALUMNOS
             ------------------------------------------------*/
            console.log('LLEGAN ESTOS ALUMNOS',respuesta.alumnos);
            if(alumnos.length > 0)
            {
                 $('#notifySINCRO').show();
                $(respuesta.alumnos).each(function(k,alumno){
           
                    tx.executeSql('SELECT alumnos.id from alumnos where alumnos.id = ?',[alumno.id],function(tx,rs){
                    
                    for(var i=0; i<rs.rows.length; i++) {
                       var row = rs.rows.item(i);
                       result[i] = { id:row['id']};
                   }
                    
                    
                    
                    if(result.length>0){

                        
                        console.log('EN LA BASE  EXISTE');
                        //UPDATE
                        tx.executeSql("UPDATE alumnos SET id= ?, nombre= ?, ctacte= ?  WHERE alumnos.id='"+alumno.id+"'",[alumno.id,alumno.nombre,JSON.stringify(alumno.ctacte)],function(){
                        console.log('ALUMNO ACTUALIZADO');
                        
                         //lastId(alumnos,respuesta.ultimoId,k);// cada vez que hay success chekea si se trata del ultimo indice para guardar el last_id  
                        
                        },error);
                        

                    }else{
                        // INSERT
                        tx.executeSql("INSERT INTO alumnos ('id','nombre','ctacte') values(?,?,?)",[alumno.id,alumno.nombre,JSON.stringify(alumno.ctacte)],function(){
                        console.log('ALUMNO INSERTADO');
                        
                        //lastId(alumnos,respuesta.ultimoId,k); 
                        
                        },error);

                    }
          
                },error);
           
         
            }); 
        
            }
            
            
            
            
            
            /*------------------------------------------------
             * COBROS
             ------------------------------------------------*/
            if(cobros.length > 0)
            {
              
            $(cobros).each(function(k,cobro){
                 $('#notifySINCRO').show();
           
                    tx.executeSql('SELECT cobros.codigo from cobros where cobros.codigo = ?',[cobro.codigo],function(tx,rs){
         
                    for(var i=0; i<rs.rows.length; i++) {
                       var row = rs.rows.item(i);
                       resultCobros[i] = { id:row['id']};
                   }
                   
                   
                   
                    if(resultCobros.length > 0){

                        //insertAlumnos(tx,alumno);
                        console.log('ESTE COBRO  '+cobro.codigo+' EXISTE EN LA BASE');
                        tx.executeSql("UPDATE cobros SET codigo= ?, cod_alumno= ?, importe= ?, saldo= ?,medio= ?,fecha= ?,estado= ?  WHERE cobros.codigo='"+cobro.codigo+"'",[cobro.codigo,cobro.cod_alumno,cobro.importe,cobro.saldo,cobro.medio_pago,cobro.fecha,cobro.estado],function(){
                        console.log('COBRO ACTUALIZADO');
                        
                             //lastId(cobros,respuesta.ultimoId,k); 
                        
                        },error);

                    }else{

                        tx.executeSql("INSERT INTO cobros ('codigo','cod_alumno','importe','saldo','medio','fecha','estado') values(?,?,?,?,?,?,?)",[cobro.codigo,cobro.cod_alumno,cobro.importe,cobro.saldo,cobro.medio_pago,cobro.fecha,cobro.estado],function(){
                        
                        console.log('COBRO INSERTADO') ;
                        
                        //lastId(cobros,respuesta.ultimoId,k); 
                        
                        },error);

                    }
          
                },error);
           
         
            }); 
              
          }
          
            
            
            
            
            /*------------------------------------------------
             * BANCOS
             ------------------------------------------------*/
            if(bancos.length > 0)
            {
             $('#notifySINCRO').show();
                $(respuesta.bancos).each(function(k,banco)
                {
           
                    tx.executeSql('SELECT bancos.codigo from bancos where bancos.codigo = ?',[banco.codigo],function(tx,rs){
                    
                    for(var i=0; i<rs.rows.length; i++) 
                    {
                       var row = rs.rows.item(i);
                       resultBancos[i] = { id:row['codigo']};
                    }
                    
                    
                    
                    if(resultBancos.length>0){

                        
                        console.log('EL BANCO EN LA BASE  EXISTE');
                        //UPDATE
                        tx.executeSql("UPDATE bancos SET codigo= ?, nombre= ?   WHERE bancos.codigo='"+banco.codigo+"'",[banco.codigo,banco.nombre],function(){
                        console.log('---------------------');
                        console.log('BANCO ACTUALIZADO',banco);
                        console.log('---------------------');
                        
                         //lastId(alumnos,respuesta.ultimoId,k);// cada vez que hay success chekea si se trata del ultimo indice para guardar el last_id  
                        
                        },error);
                        

                    }
                    else
                    {
                        console.log('EL BANCO EN LA BASE  NO EXISTE');
                        // INSERT
                        tx.executeSql("INSERT INTO bancos ('codigo','nombre') values(?,?)",[banco.codigo,banco.nombre],function(){
                        console.log('---------------------');
                        console.log('BANCO INSERTADO',banco);
                        console.log('---------------------');
                        
                        //lastId(alumnos,respuesta.ultimoId,k); 
                        
                        },error);

                    }
          
                },error);
           
         
            }); 
        
            }
            
            
            
            
            /*------------------------------------------------
             * TARJETAS
             ------------------------------------------------*/
            var resultTarjetas =[];
            
            if(tarjetas.length > 0)
            {
                $('#notifySINCRO').show();
                $(tarjetas).each(function(k,tarjeta){
           
                    tx.executeSql('SELECT tarjetas.codigo from tarjetas where tarjetas.codigo = ?',[tarjeta.codigo],function(tx,rs){
                    
                    for(var i=0; i<rs.rows.length; i++) {
                       var row = rs.rows.item(i);
                       resultTarjetas[i] = { id:row['codigo']};
                   }
                    
                    
                    
                    if(resultTarjetas.length>0){

                        
                        console.log('EN LA BASE  EXISTE');
                        //UPDATE
                        tx.executeSql("UPDATE tarjetas SET codigo= ?, nombre= ?   WHERE tarjetas.codigo='"+tarjeta.codigo+"'",[tarjeta.codigo,tarjeta.nombre],function(){
                        console.log('tarjetas ACTUALIZADO');
                        
                         //lastId(alumnos,respuesta.ultimoId,k);// cada vez que hay success chekea si se trata del ultimo indice para guardar el last_id  
                        
                        },error);
                        

                    }else{
                        // INSERT
                        tx.executeSql("INSERT INTO tarjetas ('codigo','nombre') values(?,?)",[tarjeta.codigo,tarjeta.nombre],function(){
                        console.log('tarjetas INSERTADO') ;
                        
                        //lastId(alumnos,respuesta.ultimoId,k); 
                        
                        },error);

                    }
          
                },error);
           
         
            }); 
        
            }
            
            
            
            
            /*------------------------------------------------
             * CAJAS
             ------------------------------------------------*/
            var resultCajas=[];
            if(cajas.length > 0)
            {
                $('#notifySINCRO').show();
                $(cajas).each(function(k,caja){
           
                    tx.executeSql('SELECT cajas.codigo from cajas where cajas.codigo = ?',[caja.codigo],function(tx,rs){
                    
                    for(var i=0; i<rs.rows.length; i++) {
                       var row = rs.rows.item(i);
                       resultCajas[i] = { id:row['codigo']};
                   }
                    
                    
                    
                    if(resultCajas.length>0){

                        
                        console.log('---------------------------------');
                        console.log('MI CAJA->',caja);
                        console.log('---------------------------------');
                        
                        console.log('EN LA BASE LA CAJA  EXISTE');
                        //UPDATE
                        tx.executeSql("UPDATE cajas SET  nombre= ?, estado= ?, desactivada= ?, medios= ?   WHERE cajas.codigo='"+caja.codigo+"'",[caja.nombre,caja.estado,caja.desactivada,caja.medios],function(){
                        console.log('CAJA ACTUALIZADA');
                        
                         //lastId(alumnos,respuesta.ultimoId,k);// cada vez que hay success chekea si se trata del ultimo indice para guardar el last_id  
                        
                        },error);
                        

                    }else{
                        // INSERT
                        tx.executeSql("INSERT INTO cajas ('codigo','nombre','estado','desactivada') values(?,?,?,?)",[caja.codigo,caja.nombre,caja.estado,caja.desactivada],function(){
                        console.log('caja INSERTADA') ;
                        
                        //lastId(alumnos,respuesta.ultimoId,k); 
                        
                        },error);

                    }
          
                },error);
           
         
            }); 
        
            }
            
            
            
            
            /*------------------------------------------------
             * TERMINALES
             ------------------------------------------------*/
            var resultTerminalesTarjetas=[];
            if(terminales_tarjetas.length > 0)
            {
                $('#notifySINCRO').show();
                $(terminales_tarjetas).each(function(k,terminal_tarjeta){
           
                    tx.executeSql('SELECT terminales_tarjetas.codigo from terminales_tarjetas where terminales_tarjetas.codigo = ?',[terminal_tarjeta.codigo],function(tx,rs){
                    
                    for(var i=0; i<rs.rows.length; i++) {
                       var row = rs.rows.item(i);
                       resultTerminalesTarjetas[i] = { id:row['codigo']};
                   }
                    
                    
                    
                    if(resultTerminalesTarjetas.length>0){

                        
                        console.log('---------------------------------');
                        console.log('MI TERMINAL->',terminal_tarjeta);
                        console.log('---------------------------------');
                        
                        console.log('|_EN LA BASE LA TERMINAL EXISTE');
                        //UPDATE
                        tx.executeSql("UPDATE terminales_tarjetas SET  cod_punto_venta= ?, cod_interno= ?, estado = ?, tipo_captura = ?, nombre = ?, tarjetas = ?, cod_operador = ? WHERE terminales_tarjetas.codigo='"+terminal_tarjeta.codigo+"'",[terminal_tarjeta.cod_punto_venta, terminal_tarjeta.cod_interno, terminal_tarjeta.estado, terminal_tarjeta.tipo_captura, terminal_tarjeta.nombre, terminal_tarjeta.tarjetas, terminal_tarjeta.cod_operador ],function(){
                        console.log('|_TERMINAL ACTUALIZADA ');
                        
                         //lastId(alumnos,respuesta.ultimoId,k);// cada vez que hay success chekea si se trata del ultimo indice para guardar el last_id  
                        
                        },error);
                        

                    }
                    else
                    {
                        // INSERT
                        tx.executeSql("INSERT INTO terminales_tarjetas ('codigo','cod_punto_venta','cod_interno','estado','tipo_captura','nombre','tarjetas','cod_operador') values(?,?,?,?,?,?,?,?)",[terminal_tarjeta.codigo, terminal_tarjeta.cod_punto_venta, terminal_tarjeta.cod_interno, terminal_tarjeta.estado, terminal_tarjeta.tipo_captura, terminal_tarjeta.nombre, terminal_tarjeta.tarjetas, terminal_tarjeta.cod_operador ],function(){
                        console.log('|_TERMINAL INSERTADA') ;
                        
                        //lastId(alumnos,respuesta.ultimoId,k); 
                        
                        },error);

                    }
          
                },error);
           
         
            }); 
        
            }
        
        
        },function(e)
        {//error
            console.log('error transaccion para actualizar o insertar en base local',e); 
        
            return true
        
        },function()
        {//success
            successTRANSACTION(respuesta.ultimoId,respuesta.ultimoId_server, respuesta.ultimoId_bancos, respuesta.ultimoId_server_bancos , respuesta.ultimoId_Tarjetas, respuesta.ultimoId_server_Tarjetas);
        
        }); 
  
    
    

    
    
     
}

function tienePermisoOffline()
{
    
    if(localStorage.getItem('tkoff')!=null){
            
            if(localStorage.getItem('tkoff') == BASE_OFFLINE.token){
                
                return true;
                
            }
            
        }
        
    return false;
    
}

function init_SINCRONIZACION()
{
   
   console.log('COMIENZA A SINCRONIZAR de server  a local');
   

// Función 'callback' de error de transacción
    function rollback(tx,e) {
        console.log(e);
        return true; // el retorno en true  causa el rollback
    }

    // Función 'callback' de transacción satisfactoria
    
    function success(string) {
        console.log('CREADA: ',string);
    } 
   
   
   if(tienePermisoOffline())
   {
        //alert('tiene permiso');
        var lastID = localStorage.getItem('last_id');
        var ultimo_id_bancos = localStorage.getItem('ultimoId_bancos');
        var ultimo_id_tarjetas = localStorage.getItem('ultimoId_Tarjetas');
        
        
        console.log('ULTIMO ID ES',lastID);
        console.log('ULTIMO ID BANCOS ES',ultimo_id_bancos);
        console.log('ULTIMO ID TARJETA ES',ultimo_id_tarjetas );
        
        $.ajax({
                url: BASE_URL+"offline/sincronizar",
                type: "POST",
                data: {
                    ultimo_id:lastID,
                    'ultimo_id_bancos':ultimo_id_bancos,
                    'ultimo_id_tarjetas':ultimo_id_tarjetas
                },
                dataType:"JSON",
                cache:false,
                success:function(respuesta)
                {
                    console.log('LLEGAN REGISTROS',respuesta);
                    actualizarBaseLocal(respuesta);
                }
            }); 
        
   }
        
       
       
  
    
    
        
    
}


 
$(document).ready(function()
{
   
   $('#notifySINCRO').hide();
   
//    $( "#progressbar" ).progressbar({
//        value: 37,
//        create: function( event, ui ) {
//            $(this).addClass('progress progress-striped active')
//                       .children(0).addClass('progress-bar progress-bar-success');
//        },
//        //value: false,
//        change: function() {
////          progressLabel.text( progressbar.progressbar( "value" ) + "%" );
//        },
//        complete: function() {
////          progressLabel.text( "Complete!" );
//        }
//    });
    
    
    init_BASE();
    
    enviarCola();
    
})


