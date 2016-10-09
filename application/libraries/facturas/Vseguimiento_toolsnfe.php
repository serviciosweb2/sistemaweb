<?php

/**
* Class Vseguimiento_toolsnfe
*
*Class  Vseguimiento_toolsnfe maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vseguimiento_toolsnfe extends Tseguimiento_toolsnfe{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    static function getInfoFactura(CI_DB_mysqli_driver $conexion, $codFactura, $codFilial){
        $conexion->select("nfe");
        $conexion->select("nRec");
        $conexion->select("nProt");
        $conexion->from("general.seguimiento_toolsnfe");
        $conexion->where("cod_factura", $codFactura);
        $conexion->where("cod_filial", $codFilial);
        $conexion->where("estado", Vfacturas::getEstadoHabilitado());
        $conexion->order_by("id", "desc");
        $conexion->limit(1, 0);
        $query = $conexion->get();
        $arrResp = $query->result_array();
        return (count($arrResp) > 0) ? $arrResp[0] : null;
    }
    
    static function getErrorFactura(CI_DB_mysqli_driver $conexion, $codFactura, $codFilial){
        $conexion->select("xMotivo");
        $conexion->from("general.seguimiento_toolsnfe");
        $conexion->where("cod_factura", $codFactura);
        $conexion->where("cod_filial", $codFilial);
        $conexion->where("estado", Vfacturas::getEstadoError());
        $conexion->order_by("id", "desc");
        $conexion->limit(1, 0);
        $query = $conexion->get();
        $arrResp = $query->result_array();
        return count($arrResp) > 0 ? $arrResp[0]['xMotivo'] : null;
    }
    
    static function getFacturasConsultar(CI_DB_mysqli_driver $conexionFilial, $codFilial){
        $conexionFilial->select("general.seguimiento_toolsnfe.*", false);
        $conexionFilial->from("general.seguimiento_toolsnfe");
        $conexionFilial->join("facturas", "facturas.codigo = general.seguimiento_toolsnfe.cod_factura AND general.seguimiento_toolsnfe.estado = '".Vfacturas::getEstadoEnviado()."'");
        $conexionFilial->where("general.seguimiento_toolsnfe.cod_filial", $codFilial);
        $conexionFilial->where("facturas.estado", Vfacturas::getEstadoEnviado());
        $conexionFilial->group_by("general.seguimiento_toolsnfe.nRec DESC");
        $query = $conexionFilial->get();
        return $query->result_array();
    }    
}