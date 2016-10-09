<?php

/**
 * Class Vseguimiento_paulistana
 *
 *Class  Vseguimiento_paulistana maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vseguimiento_paulistana extends Tseguimiento_paulistana{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    static function getFacturasPendientesVerificar(CI_DB_mysqli_driver $conexion, $puntoVenta){
        $codFilial = $conexion->database;
        $estado = Vfacturas::getEstadoEnviado();
        $conexion->select("general.seguimiento_paulistana.*", false);
        $conexion->from("facturas");
        $conexion->join("general.seguimiento_paulistana", "general.seguimiento_paulistana.cod_factura = facturas.codigo AND general.seguimiento_paulistana.cod_filial = $codFilial AND general.seguimiento_paulistana.estado = '$estado'");
        $conexion->where("facturas.estado", $estado);
        $conexion->where("facturas.punto_venta", $puntoVenta);
        $conexion->group_by("general.seguimiento_paulistana.protocolo");
        $query = $conexion->get();
        return $query->result_array();
    }

    static function getInfoFactura(CI_DB_mysqli_driver $conexion, $codFactura, $codFilial){

        $conexion->select("numero_nfse");
        $conexion->select("numero_lote");
        $conexion->from("general.seguimiento_paulistana");
        $conexion->where("cod_factura", $codFactura);
        $conexion->where("cod_filial", $codFilial);
        $conexion->order_by("id", "desc");
        $conexion->limit(1, 0);
        $query = $conexion->get();
        $arrTemp = $query->result_array();

        if (count($arrTemp) > 0){
            $arrResp = $arrTemp[0];
            $conexion->select("codigo_verificacion");
            $conexion->from("general.seguimiento_paulistana");
            $conexion->where("cod_factura", $codFactura);
            $conexion->where("cod_filial", $codFilial);
            $conexion->order_by("id", "desc");
            $conexion->limit(1, 0);
            $query = $conexion->get();

            $arrTemp = $query->result_array();
            $arrResp['codigo_verificacion'] = count($arrTemp) > 0 ? $arrTemp[0]['codigo_verificacion'] : "" ;

            if ($arrResp['numero_nfse'] == null && $arrResp['numero_lote'] == null && $arrResp['codigo_verificacion'] == null){
                $arrResp = null;
            }

            return $arrResp;
        } else {
            return null;
        }
    }

    static function getAllDataFactura(CI_DB_mysqli_driver $conexion, $codFactura, $codFilial) {

        $conexion->select("numero_nfse");
        $conexion->select("numero_lote");
        $conexion->from("general.seguimiento_paulistana");
        $conexion->where("cod_factura", $codFactura);
        $conexion->where("cod_filial", $codFilial);
        $conexion->order_by("id", "desc");
        $conexion->limit(1, 0);
        $query = $conexion->get();
        $arrTemp = $query->result_array();

        if (count($arrTemp) > 0){
            $arrResp = $arrTemp[0];
            $conexion->select("codigo_verificacion");
            $conexion->select("fecha_envio");
            $conexion->from("general.seguimiento_paulistana");
            $conexion->where("cod_factura", $codFactura);
            $conexion->where("cod_filial", $codFilial);
            $conexion->order_by("id", "desc");
            $conexion->limit(1, 0);
            $query = $conexion->get();

            $arrTemp = $query->result_array();
            $arrResp['codigo_verificacion'] = count($arrTemp) > 0 ? $arrTemp[0]['codigo_verificacion'] : "" ;
            $arrResp['fecha_envio'] = count($arrTemp) > 0 ? $arrTemp[0]['fecha_envio'] : "" ;

            if ($arrResp['numero_nfse'] == null && $arrResp['numero_lote'] == null && $arrResp['codigo_verificacion'] == null && $arrResp['fecha_envio'] == null){
                $arrResp = null;
            }

            return $arrResp;
        } else {
            return null;
        }
    }

    static function getErrorFactura(CI_DB_mysqli_driver $conexion, $codFactura, $codFilial){
        $conexion->select("mensaje");
        $conexion->from("general.seguimiento_paulistana");
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