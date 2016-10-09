<?php

function databaseExists(CI_DB_mysqli_driver $conexion, $database) {
    $result = $conexion->query('SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '.$database);
    if($result->num_rows() > 0) {
        return true;
    }
    
    return false;
}