<?php

/**
 * Class Vcompras
 *
 * Class  Vcompras maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vcompras extends Tcompras {

    var $estadoconfirmada = 'confirmada';

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    public function getProveedor() {
        return new Vproveedores($this->oConnection, $this->cod_proveedor);
    }

    static function listarComprasDataTable(CI_DB_mysqli_driver $conexion, $arrCondiciones = null, $arrLimit = null, $arrSort = null, $contar = false) {
        $conexion->select("sum(compras_renglones.precio_total)", false);
        $conexion->from('compras_renglones');
        $conexion->where('compras.codigo = compras_renglones.cod_compra');
        $conexion->where('compras_renglones.baja', 0);
        $subquery = $conexion->return_query();
        $conexion->resetear();

        $conexion->select('compras.codigo, razones_sociales.razon_social as nombre, compras.fecha,(' . $subquery . ') as totalCompra, compras.estado');
        $conexion->select("CONCAT(general.usuarios_sistema.nombre,', ',general.usuarios_sistema.apellido) AS usuario_creador",false);
        $conexion->from('compras');
        $conexion->join('proveedores', 'proveedores.codigo = compras.cod_proveedor');
        $conexion->join('razones_sociales', 'razones_sociales.codigo = proveedores.cod_razon_social');
        $conexion->join('general.usuarios_sistema','general.usuarios_sistema.codigo = compras.cod_usuario_creador');
        if (count($arrCondiciones) > 0) {
            foreach ($arrCondiciones as $key => $value) {
                $conexion->or_like($key, $value);
            }
        }
        if ($arrLimit != null) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }
        if ($arrSort != null) {

            $conexion->order_by($arrSort["0"], $arrSort["1"]);
        }

        if ($contar) {
            return $conexion->count_all_results();
        } else {

            $query = $conexion->get();
            //echo $conexion->last_query();
            return $query->result_array();
        }
    }

    public function getCompraRenglones() {
        $this->oConnection->select('*');
        $this->oConnection->from('compras_renglones');
        $this->oConnection->where('compras_renglones.cod_compra', $this->codigo);
        $this->oConnection->where('compras_renglones.baja', 0);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getComprobantes($estado = null) {
        $this->oConnection->select('compras_comprobantes.*, general.comprobantes.nombre, compras_tipos_factura.*,(select factura from general.tipos_facturas where general.tipos_facturas.codigo = compras_tipos_factura.cod_tipo_factura) as tipo');
        $this->oConnection->from('compras_comprobantes');
        $this->oConnection->join('general.comprobantes', 'general.comprobantes.id = compras_comprobantes.cod_comprobante');
        $this->oConnection->join('compras_tipos_factura', 'compras_comprobantes.codigo = compras_tipos_factura.cod_compra_comprobante', 'left');
        $this->oConnection->where('compras_comprobantes.cod_compra', $this->codigo);
        if ($estado != null) {
            $this->oConnection->where('compras_comprobantes.estado', $estado);
        }



        $query = $this->oConnection->get();
        //echo $this->oConnection->last_query();
//        //echo '<pre>'; 
//print_r($query->result_array());
//echo '</pre>';
        return $query->result_array();
    }

    public function getPagos($estado = null) {
        $this->oConnection->select('pagos.*, general.medios_pago.medio, movimientos_caja.cod_caja');
        $this->oConnection->from('compras_imputaciones');
        $this->oConnection->join('pagos', 'compras_imputaciones.cod_pago = pagos.codigo');
        $this->oConnection->join('general.medios_pago', 'general.medios_pago.codigo = pagos.medio_pago');
        $this->oConnection->join('movimientos_caja','movimientos_caja.concepto = pagos.codigo AND movimientos_caja.cod_concepto = "PAGOS"');
        $this->oConnection->where('compras_imputaciones.cod_compra', $this->codigo);
        if ($estado != null) {
            $this->oConnection->where('pagos.estado', $estado);
        }
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function guardar($codproveedor, $codusuario, $fecha = null, $estado = null) {
        $this->cod_proveedor = $codproveedor;
        $this->cod_usuario_creador = $codusuario;
        $this->estado = $estado == null ? $this->estadoconfirmada : $estado;
        $this->fecha_real = date('Y-m-d H:i:s');
        $this->fecha = $fecha == null ? date('Y-m-d') : $fecha;
        $this->guardarCompras();
    }

    public function getTotalRenglones() {
        $this->oConnection->select('sum(compras_renglones.precio_total) as total');
        $this->oConnection->from('compras_renglones');
        $this->oConnection->where('compras_renglones.cod_compra', $this->codigo);
        $query = $this->oConnection->get();
        $resultado = $query->result_array();
        return $resultado[0]['total'];
    }

    public function imputarPago($codpago) {
        $datos = array('cod_compra' => $this->codigo,
            'cod_pago' => $codpago,
            'baja' => 0);
        $this->oConnection->insert('compras_imputaciones', $datos);
    }

    public function desimputarPago($codpago) {
        $datos = array('cod_compra' => $this->codigo,
            'cod_pago' => $codpago);
        $this->oConnection->where($datos);
        $this->oConnection->update('compras_imputaciones', array("baja" => 1));
    }

    public function anular() {
        $this->estado = 'anulada';
        $respuesta = $this->guardarCompras();
        return $respuesta;
    }

    public function confirmar() {
        $this->estado = 'confirmada';
        $respuesta = $this->guardarCompras();
        return $respuesta;
    }

}
