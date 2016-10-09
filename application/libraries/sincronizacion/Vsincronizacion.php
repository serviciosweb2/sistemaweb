<?php

/**
 * Class Vsincronizacion
 *
 * Class  Vsincronizacion maneja todos los aspectos de la sincronizacion con el panel de control
 *
 * @package  SistemaIGA
 * @subpackage sincronizacion
 * @author  Vane
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vsincronizacion {

    static function getVersionesSincronizacion(CI_DB_mysqli_driver $conexion) {
        $conexion->select("general.version_sincronizacion.*", false);
        $conexion->from("general.version_sincronizacion");
        $query = $conexion->get();
        return $query->result_array();
    }

    static function setUltimaSincronizacion(CI_DB_mysqli_driver $conexion, $base_datos, $codigo) {
        $datos = array('cod_sincronizacion' => $codigo);
        $conexion->where('general.version_sincronizacion.base_datos', $base_datos);
        $conexion->update('general.version_sincronizacion', $datos);
    }

}
