<?php

/**
 * Class Vcondiciones_sociales
 *
 * Class  Vcondiciones_sociales maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vcondiciones_sociales extends Tcondiciones_sociales {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    function getCondicionSocial($pais) {
        $conexion = $this->oConnection;
        $conexion->select('codigo');
        $conexion->from($this->nombreTabla);
        $conexion->where('cod_pais', $pais);
        $conexion->where('default', 1);
        $query = $conexion->get();
        return $query->result_array();
    }
}

