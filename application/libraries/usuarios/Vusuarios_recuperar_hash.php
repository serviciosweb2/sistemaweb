<?php

/**
* Class Vusuarios_recuperar_hash
*
*Class  Vusuarios_recuperar_hash maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vusuarios_recuperar_hash extends Tusuarios_recuperar_hash{

    function __construct(CI_DB_mysqli_driver $conexion, $hash) {
        parent::__construct($conexion, $hash);
    }

}

?>