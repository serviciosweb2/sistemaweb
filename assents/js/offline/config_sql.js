//var db = openDatabase("sistemasiga", "1.0", "descripcion", 5*1024*1024);
//
//// creamos las tablas
//   db.transaction(function (tx) {
//       
//    tx.executeSql('CREATE TABLE IF NOT EXISTS cobros_detalle_medio (id INTEGER PRIMARY KEY AUTOINCREMENT,cod_cobro varchar(30),clave varchar(30),valor varchar(30))',[],success('cobros_detalle_medio'),error);
//    tx.executeSql('CREATE TABLE IF NOT EXISTS cobros (codigo INTEGER(12),cod_alumno varchar(12), importe varchar(30),saldo varchar(30),medio varchar(30),fecha varchar(30),estado varchar(30))',[],success('cobros'),rollback);
//    tx.executeSql('CREATE TABLE IF NOT EXISTS alumnos (id INTEGER(12),nombre varchar(30),ctacte varchar(1000))',[],success('alumnos'),rollback);
//    
//    tx.executeSql('CREATE TABLE IF NOT EXISTS bancos (codigo  INTEGER(12),nombre varchar(30),banco_asociado(1000))',[],success('bancos'),rollback);
//    tx.executeSql('CREATE TABLE IF NOT EXISTS tarjetas (codigo INTEGER(12),nombre varchar(30),codigo_pais(1000))',[],success('tarjetas'),rollback);
//    tx.executeSql('CREATE TABLE IF NOT EXISTS cheques (id INTEGER(12),nombre varchar(30),codigo_pais(1000))',[],success('tarjetas'),rollback);
//   });
//
//
//
//// Función 'callback' de error de transacción
//    function rollback(tx,e) {
//        console.log(e);
//        return true; // el retorno en true  causa el rollback
//    }
//
//    // Función 'callback' de transacción satisfactoria
//    
//    function success(string) {
//        console.log('CREADA: ',string);
//    } 