<?php

/**
* Class Vseguimiento_ginfes
*
*Class  Vseguimiento_ginfes maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vseguimiento_ginfes extends Tseguimiento_ginfes{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    static function getFacturasPendientesVerificar(CI_DB_mysqli_driver $conexion, $puntoVenta){
        $codFilial = $conexion->database;
        $estado = Vfacturas::getEstadoEnviado();
        $conexion->select("general.seguimiento_ginfes.*", false);
        $conexion->from("facturas");
        $conexion->join("general.seguimiento_ginfes", "general.seguimiento_ginfes.cod_factura = facturas.codigo AND general.seguimiento_ginfes.cod_filial = $codFilial AND general.seguimiento_ginfes.estado = '$estado'");
        $conexion->where("facturas.estado", $estado);
        $conexion->where("facturas.punto_venta", $puntoVenta);
        $conexion->group_by("general.seguimiento_ginfes.protocolo");
        $query = $conexion->get();
        return $query->result_array();
    }
    
    static function getInfoFactura(CI_DB_mysqli_driver $conexion, $codFactura, $codFilial){
        $conexion->select("numero_nfse");
        $conexion->select("numero_lote");        
        $conexion->select("protocolo");
        $conexion->from("general.seguimiento_ginfes");
        $conexion->where("cod_factura", $codFactura);
        $conexion->where("cod_filial", $codFilial);
        $conexion->where("estado", Vfacturas::getEstadoEnviado());
        $conexion->order_by("id", "desc");
        $conexion->limit(1, 0);
        $query = $conexion->get();
        $arrTemp = $query->result_array();
        if (count($arrTemp) > 0){
            $arrResp = $arrTemp[0];
            $conexion->select("codigo_verificacion");
            $conexion->from("general.seguimiento_ginfes");
            $conexion->where("cod_factura", $codFactura);
            $conexion->where("cod_filial", $codFilial);
            $conexion->where("estado", Vfacturas::getEstadoHabilitado());
            $conexion->order_by("id", "desc");
            $conexion->limit(1, 0);
            $query = $conexion->get();
            $arrTemp = $query->result_array();
            $arrResp['codigo_verificacion'] = count($arrTemp) > 0 ? $arrTemp[0]['codigo_verificacion'] : "" ;
            return $arrResp;
        } else {
            return null;
        }
    }
    
    static function getErrorFactura(CI_DB_mysqli_driver $conexion, $codFactura, $codFilial){
        $conexion->select("mensaje");
        $conexion->from("general.seguimiento_ginfes");
        $conexion->where("cod_factura", $codFactura);
        $conexion->where("cod_filial", $codFilial);
        $conexion->where("estado", Vfacturas::getEstadoError());
        $conexion->order_by("id", "desc");
        $conexion->limit(1, 0);
        $query = $conexion->get();
        $arrResp = $query->result_array();
        return count($arrResp) > 0 ? $arrResp[0]['mensaje'] : null;
    }
}

?>