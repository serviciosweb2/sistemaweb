<?php

/**
* Class Vseguimiento_abrasf
*
*Class  Vseguimiento_abrasf maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vseguimiento_abrasf extends Tseguimiento_abrasf{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    static function getFacturasPendientesVerificar(CI_DB_mysqli_driver $conexion, $puntoVenta){
        $codFilial = $conexion->database;
        $estado = Vfacturas::getEstadoEnviado();
        $conexion->select("general.seguimiento_abrasf.*", false);
        $conexion->from("facturas");
        $conexion->join("general.seguimiento_abrasf", "general.seguimiento_abrasf.cod_factura = facturas.codigo AND general.seguimiento_abrasf.cod_filial = $codFilial AND general.seguimiento_abrasf.estado = '$estado'");
        $conexion->where("facturas.estado", $estado);
        $conexion->where("facturas.punto_venta", $puntoVenta);
        $conexion->group_by("general.seguimiento_abrasf.protocolo DESC");
        $query = $conexion->get();
        return $query->result_array();
    }    
    
    static function getNumeroSeguimineto(CI_DB_mysqli_driver $conexion, $codigoFactura){
        $conexion->select("numero");
        $conexion->from("general.seguimiento_abrasf");
        $conexion->where("cod_factura", $codigoFactura);
        $conexion->where("cod_filial", $conexion->database);
        $conexion->where("estado", vFacturas::getEstadoEnviado());
        $conexion->order_by("id", "desc");
        $query = $conexion->get();
        $arrResp = $query->result_array();
        return count($arrResp) > 0 ? $arrResp[0]['numero'] : null;
    }
    
    static function getInfoFactura(CI_DB_mysqli_driver $conexion, $codFactura, $codFilial){
        $conexion->select("numero");
        $conexion->select("protocolo");
        $conexion->from("general.seguimiento_abrasf");
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
        $conexion->select("mensaje");
        $conexion->from("general.seguimiento_abrasf");
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