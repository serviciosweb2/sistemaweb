<?php

/**
 * Class Vcertificados_plan_filial
 *
 * Class  Vcertificados_plan_filial maneja todos los aspectos de los tipos de certificados
 *
 * @package  SistemaIGA
 * @subpackage Certificados_plan_filial
 * @author   Vane
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vcertificados_plan_filial extends Tcertificados_plan_filial {

    static public $estadohabilitada = 'habilitada';
    static public $estadoinhabilitada = 'inhabilitada';

    function __construct(CI_DB_mysqli_driver $conexion, $cod_filial, $cod_plan_academico, $cod_tipo_periodo, $cod_certificante) {
        parent::__construct($conexion, $cod_filial, $cod_plan_academico, $cod_tipo_periodo, $cod_certificante);
    }

    static function getEstadoHabilitada() {
        return self::$estadohabilitada;
    }

    static function getEstadoInhabilitada() {
        return self::$estadoinhabilitada;
    }

    function getPropiedadesNuevoCertificado() {
        $arrkey = array('costo');
        $this->oConnection->select('*');
        $this->oConnection->from('general.certificados_propiedades');
        $this->oConnection->where('cod_filial', $this->cod_filial);
        $this->oConnection->where('cod_certificante', $this->cod_certificante);
        $this->oConnection->where('cod_tipo_periodo', $this->cod_tipo_periodo);
        $this->oConnection->where('cod_plan_academico', $this->cod_plan_academico);
        $this->oConnection->where_in('key', $arrkey);
        $this->oConnection->where('fecha_inicio <=', 'CURDATE()');
        $this->oConnection->where('fecha_fin >=', 'CURDATE()');
        $query = $this->oConnection->get();
        $respuesta = $query->result_array();
        return $respuesta;
    }

    function getRequerimientosHabilitadosAprobarCertificado() {
        $this->oConnection->select('*');
        $this->oConnection->from('general.certificados_requerimientos');
        $this->oConnection->where('cod_filial', $this->cod_filial);
        $this->oConnection->where('cod_certificante', $this->cod_certificante);
        $this->oConnection->where('cod_tipo_periodo', $this->cod_tipo_periodo);
        $this->oConnection->where('cod_plan_academico', $this->cod_plan_academico);
        $this->oConnection->where('estado', Vcertificados_requerimientos::getEstadoHabilitado());
        $query = $this->oConnection->get();
        $respuesta = $query->result_array();
        return $respuesta;
    }

    function getPropiedadesImprimirCertificado($fecha = null) {
        $arrkey = array('fecha_inicio', 'fecha_fin');
        $this->oConnection->select('*');
        $this->oConnection->from('general.certificados_propiedades');
        $this->oConnection->where('cod_filial', $this->cod_filial);
        $this->oConnection->where('cod_certificante', $this->cod_certificante);
        $this->oConnection->where('cod_tipo_periodo', $this->cod_tipo_periodo);
        $this->oConnection->where('cod_plan_academico', $this->cod_plan_academico);
        $this->oConnection->where_in('key', $arrkey);
        if ($fecha != null) {
            $this->oConnection->where("fecha_inicio <= '$fecha'");
            $this->oConnection->where("fecha_fin >= '$fecha'");
        } else {
            $this->oConnection->where('fecha_inicio <=', 'CURDATE()', false);
            $this->oConnection->where('fecha_fin >=', 'CURDATE()', false);
        }

        $query = $this->oConnection->get();
        $respuesta = $query->result_array();
        return $respuesta;
    }

}
