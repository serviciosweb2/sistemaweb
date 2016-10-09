<?php

/**
* Class Vtipos_unidades_medida
*
*Class  Vtipos_unidades_medida maneja todos los aspectos de las unidades de medida
*
* @package  SistemaIGA
* @subpackage tipos_unidades_medida
* @author   Vane
* @version  $Revision: 1.1 $
* @access   private
*/
class Vtipos_unidades_medida extends Ttipos_unidades_medida{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

}