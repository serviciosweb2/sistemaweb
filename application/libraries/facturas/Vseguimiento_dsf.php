<?php

/**
* Class Vseguimiento_dsf
*
*Class  Vseguimiento_dsf maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vseguimiento_dsf extends Tseguimiento_dsf{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }
    
    static function getFacturasPendientesVerificar(CI_DB_mysqli_driver $conexion, $puntoVenta){
        $codFilial = $conexion->database;
        $estado = Vfacturas::getEstadoEnviado();
        $conexion->select("general.seguimiento_dsf.*", false);
        $conexion->from("facturas");
        $conexion->join("general.seguimiento_dsf", "general.seguimiento_dsf.cod_factura = facturas.codigo AND general.seguimiento_dsf.cod_filial = $codFilial AND general.seguimiento_dsf.estado = '$estado'");
        $conexion->where("facturas.estado", $estado);
        $conexion->where("facturas.punto_venta", $puntoVenta);
        $conexion->group_by("general.seguimiento_dsf.numero_lote");
        $query = $conexion->get();
        return $query->result_array();
    }
    
    static function getInfoFactura(CI_DB_mysqli_driver $conexion, $codFactura, $codFilial){
        $conexion->select("numero_rps");
        $conexion->select("numero_lote");
        $conexion->from("general.seguimiento_dsf");
        $conexion->where("cod_factura", $codFactura);
        $conexion->where("cod_filial", $codFilial);
        $conexion->where("estado", Vfacturas::getEstadoEnviado());
        $conexion->order_by("id", "desc");
        $conexion->limit(1, 0);
        $query = $conexion->get();
        $arrResp = $query->result_array();
        return (count($arrResp) > 0) ? $arrResp[0] : null;
    }
    
    static function getErrorFactura(CI_DB_mysqli_driver $conexion, $codFactura, $codFilial){
        $conexion->select("respuesta");
        $conexion->from("general.seguimiento_dsf");
        $conexion->where("cod_factura", $codFactura);
        $conexion->where("cod_filial", $codFilial);
        $conexion->where("estado", Vfacturas::getEstadoError());
        $conexion->order_by("id", "desc");
        $conexion->limit(1, 0);
        $query = $conexion->get();
        $arrResp = $query->result_array();
        return count($arrResp) > 0 ? $arrResp[0]['respuesta'] : null;
    }
}

?>