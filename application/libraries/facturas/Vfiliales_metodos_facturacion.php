<?php

/**
 * Class Vfiliales_metodos_facturacion
 *
 * Class  Vfiliales_metodos_facturacion maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vfiliales_metodos_facturacion extends Tfiliales_metodos_facturacion {

    static private $metodoNoFactura = "no_factura";
    static private $proveedorAbrasf = "abrasf";
    static private $proveedorDSF = "dsf";
    static private $proveedorGinfes = "ginfes";
    static private $proveedorPaulistana = "paulistana";
    static private $proveedorProducto = "toolsnfe";
    static private $proveedorNoFactura = "no_factura";

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    static public function get_metodos_facturacion_producto(CI_DB_mysqli_driver $conexion, array $filiales = null) {
        if ($filiales != null) {
            $union = array();
            foreach ($filiales as $filial) {
                $conexion->select("general.filiales_metodos_facturacion.facturacion_productos");
                $conexion->from("general.filiales_metodos_facturacion");
                $conexion->where("general.filiales_metodos_facturacion.cod_filial", $filial);
                $sqProveedor1 = $conexion->return_query();
                $conexion->resetear();
                $conexion->select($filial, false);
                $conexion->select("($sqProveedor1) AS proveedor", false);
                $union[] = $conexion->return_query();
                $conexion->resetear();
            }
            $query = "SELECT DISTINCT proveedor FROM (" . implode(" UNION ", $union) . ") AS proveedor";
            $query = $conexion->query($query); // codeigniter no soporta consultas de union en active record (ver manual)
        } else {
            $conexion->select("DISTINCT general.filiales_metodos_facturacion.facturacion_productos AS proveedor", false);
            $conexion->from("general.filiales_metodos_facturacion");
            $query = $conexion->get();
        }
        return $query->result_array();
    }

    static public function get_metodos_facturacion_servicio(CI_DB_mysqli_driver $conexion, array $filiales = null) {
        if ($filiales != null) {
            $union = array();
            foreach ($filiales as $filial) {
                $conexion->select("general.filiales_metodos_facturacion.facturacion_servicios");
                $conexion->from("general.filiales_metodos_facturacion");
                $conexion->where("general.filiales_metodos_facturacion.cod_filial", $filial);
                $sqProveedor1 = $conexion->return_query();
                $conexion->resetear();
                $conexion->select($filial, false);
                $conexion->select("($sqProveedor1) AS proveedor", false);
                $union[] = $conexion->return_query();
                $conexion->resetear();
            }
            $query = "SELECT distinct proveedor FROM(" . implode(" UNION ", $union) . ") AS proveedor";
            $query = $conexion->query($query); // codeigniter no soporta consultas de union en active record (ver manual)
        } else {
            $conexion->select("DISTINCT general.filiales_metodos_facturacion.facturacion_servicios AS proveedor", false);
            $conexion->from("general.filiales_metodos_facturacion");
            $query = $conexion->get();
        }
        return $query->result_array();
    }

    static public function getMetodoNoFactura() {
        return self::$metodoNoFactura;
    }

    static public function getProveedorAbrasf() {
        return self::$proveedorAbrasf;
    }

    static public function getProveedorDSF() {
        return self::$proveedorDSF;
    }

    static public function getProveedorGinfes() {
        return self::$proveedorGinfes;
    }

    static public function getProveedorProducto() {
        return self::$proveedorProducto;
    }

    static public function getProveedorPaulistana() {
        return self::$proveedorPaulistana;
    }

    static public function getProveedorNoFactura() {
        return self::$proveedorNoFactura;
    }

}

?>