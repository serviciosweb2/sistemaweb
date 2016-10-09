<?php

/**
 * Class Vciclos
 *
 * Class  Vciclos maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vciclos extends Tciclos {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    static function getCiclos(CI_DB_mysqli_driver $conexion, $cod_filial, $cod_ciclo = null, $soloHabilitado = true) {
        $conexion->select('*');
        $conexion->from('general.ciclos');
        $conexion->join('general.filiales_ciclos_academicos', 'general.filiales_ciclos_academicos.cod_ciclo = general.ciclos.codigo');
        $conexion->where('general.filiales_ciclos_academicos.cod_filial', $cod_filial);
        if ($soloHabilitado){
            $conexion->where('general.filiales_ciclos_academicos.estado', 'habilitada');
        }
        if ($cod_ciclo != null) {
            $conexion->where('general.ciclos.codigo', $cod_ciclo);
        }
        $query = $conexion->get();
        return $query->result_array();
    }

    static function getCiclosActuales(CI_DB_mysqli_driver $conexion, $cod_filial = null) {
        $conexion->select('*');
        $conexion->from('general.ciclos');
        $conexion->where('general.fecha_inicio_ciclo < curdate()');
        $conexion->where('general.fecha_fin_ciclo > curdate()');
        if ($cod_filial != null) {
            $conexion->join('general.filiales_ciclos_academicos', 'general.filiales_ciclos_academicos.cod_ciclo = general.ciclos.codigo');
            $conexion->where('general.filiales_ciclos_academicos.cod_filial', $cod_filial);
            $conexion->where('general.filiales_ciclos_academicos.estado', 'habilitada');
        }
        $query = $conexion->get();
        return $query->result_array();
    }

    static function getCiclosConComisiones(CI_DB_mysqli_driver $conexion){
        $conexion->select('ciclos.*');
        $conexion->from('comisiones');
        $conexion->join('general.ciclos', 'comisiones.ciclo = general.ciclos.codigo');
        $conexion->where('comisiones.estado', 'habilitado');
        $conexion->group_by('ciclos.codigo');
        $query = $conexion->get();
        return $query->result_array();

    }

}

?>
