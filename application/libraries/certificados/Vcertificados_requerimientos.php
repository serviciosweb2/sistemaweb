<?php

/**
* Class Vcertificados_requerimientos
*
*Class  Vcertificados_requerimientos maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vcertificados_requerimientos extends Tcertificados_requerimientos{
    
    static public $estadohabilitado = 'habilitado';
    static public $estadoinhabilitado = 'inhabilitado';

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }
    static function getEstadoHabilitado() {
        return self::$estadohabilitado;
    }

    static function getEstadoInhabilitado() {
        return self::$estadoinhabilitado;
    }

}

?>