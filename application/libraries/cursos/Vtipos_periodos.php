<?php

/**
 * Class Vtipos_periodos
 *
 * Class  Vtipos_periodos maneja todos los aspectos de los periodos de cursos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   vane
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vtipos_periodos extends Ttipos_periodos {

    

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    static function getNombre(CI_DB_mysqli_driver $conexion, $codigo) {
        $condiciones = array('codigo' => $codigo);
        $valueperiodos = Vtipos_periodos::listarTipos_periodos($conexion, $condiciones);
        
        return $valueperiodos[0]['nombre'];
    }
    
    

}
