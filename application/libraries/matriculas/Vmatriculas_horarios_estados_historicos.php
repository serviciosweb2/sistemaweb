<?php

/**
* Class Vmatriculas_horarios_estados_historicos
*
*Class  Vmatriculas_horarios_estados_historicos maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vmatriculas_horarios_estados_historicos extends Tmatriculas_horarios_estados_historicos{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

}

?>