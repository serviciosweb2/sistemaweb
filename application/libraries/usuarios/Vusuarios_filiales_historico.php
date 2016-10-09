<?php

/**
* Class Vusuarios_filiales_historico
*
*Class  Vusuarios_filiales_historico maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vusuarios_filiales_historico extends Tusuarios_filiales_historico{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

}