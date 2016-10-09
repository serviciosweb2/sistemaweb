<?php

/**
 * Class Vpagos
 *
 * Class  Vpagos maneja todos los aspectos de pagos 
 *
 * @package  SistemaIGA
 * @subpackage pagos
 * @author   VAne
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vpagos extends Tpagos {

    static private $conceptoproveedor = 'PROVEEDOR';
    var $estadopendiente = 'pendiente';
    var $estadoconfirmado = 'confirmado';
    var $estadoanulado = 'anulado';

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    function guardar($cod_caja, $importe, $usuario, $medio, $concepto, $codconcepto, $estado = null, $fechapago = null) {
        $this->importe = $importe;
        $this->cod_usuario = $usuario;
        $this->medio_pago = $medio;
        $this->estado = $estado == null ? $this->estadopendiente : $estado;
        $this->fecha = date('Y-m-d H:i:s');
        $this->fecha_pago = $fechapago == null ? date('Y-m-d') : $fechapago;
        $this->concepto = $concepto;
        $this->cod_concepto = $codconcepto;
        $this->cod_caja = $cod_caja;
        $this->guardarPagos();
    }

    function confirmar($codcaja, $codusuario) {//guardar movimiento de caja
        $this->estado = $this->estadoconfirmado;
        $this->guardarPagos();

        $medio = new Vmedios_pago($this->oConnection, $this->medio_pago);

//        if ($medio->cobrar == $medio->MUESTRACOBRAR) {//nota de credito no se va a usar aca, lo dejo igual
            $movCaja = new Vmovimientos_caja($this->oConnection);
            $movCaja->guardar(date("Y-m-d H:i:s"), $this->medio_pago, $this->importe, 0, null, $codusuario, $codcaja, Vmovimientos_caja::getConceptoPagos(), $this->getCodigo(), date("Y-m-d H:i:s"));
            
            $objcaja = new Vcaja($this->oConnection, $codcaja);
            $medios = $objcaja->getMediosPago($medio->getCodigo());

            if ($medios[0]['entrada_salida'] == '1') {
                $movCaja2 = new Vmovimientos_caja($this->oConnection);
                $movCaja2->guardar(date("Y-m-d H:i:s"), $this->medio_pago, 0, $this->importe, null, $codusuario, $codcaja, Vmovimientos_caja::getConceptoPagos(), $this->getCodigo(), date("Y-m-d H:i:s"));
            }
//        }
    }

    public function anular($codcaja, $codusuario) {
        $this->estado = $this->estadoanulado;
        $this->guardarPagos();

//        $medio = new Vmedios_pago($this->oConnection, $this->medio_pago);
//        if ($medio->cobrar == $medio->MUESTRACOBRAR) {//nota de credito no se va a usar aca, lo dejo igual
//            $movCaja = new Vmovimientos_caja($this->oConnection);
//            $movCaja->guardar(date("Y-m-d H:i:s"), $this->medio_pago, $this->importe, 0, null, $codusuario, $codcaja, Vmovimientos_caja::getConceptoPagos(), $this->getCodigo(), date("Y-m-d H:i:s"));
//
//            if ($medio->ent_sal == $medio->ENTRADASALIDA) {
//                $movCaja2 = new Vmovimientos_caja($this->oConnection);
//                $movCaja2->guardar(date("Y-m-d H:i:s"), $this->medio_pago, 0, $this->importe, null, $codusuario, $codcaja, Vmovimientos_caja::getConceptoPagos(), $this->getCodigo(), date("Y-m-d H:i:s"));
//            }
//        }
            $objcaja = new Vcaja($this->oConnection, $this->cod_caja);
            $medios = $objcaja->getMediosPago($this->medio_pago);
            if(count($medios) > 0){
                 $condiciones2 = array('cod_concepto' => 'PAGOS', 'concepto' => $this->codigo);
                $movimientos = Vmovimientos_caja::listarMovimientos_caja($this->oConnection, $condiciones2);
                $debe = 0;
                $haber = 0;
                foreach ($movimientos as $mov) {

                    $debe = $debe + $mov['debe'];
                    $haber = $haber + $mov['haber'];
                }
                if ($debe != $haber) {
                    $movCaja = new Vmovimientos_caja($this->oConnection);

                    if ($debe > $haber) {
                        $movCaja->guardar(date("Y-m-d H:i:s"), $this->medio_pago, 0, $debe - $haber, null, $codusuario, $this->cod_caja, Vmovimientos_caja::getConceptoPagos(), $this->codigo, date("Y-m-d H:i:s"));
                    } else {
                        $movCaja->guardar(date("Y-m-d H:i:s"), $this->medio_pago, $haber - $debe, 0, null, $codusuario, $this->cod_caja, Vmovimientos_caja::getConceptoPagos(), $this->codigo, date("Y-m-d H:i:s"));
                    }
                }
            }
    }

    static function getConceptoproveedor() {
        return self::$conceptoproveedor;
    }

    function getMovimientoCajaPago() {
        $this->oConnection->select('*');
        $this->oConnection->from('movimientos_caja');
        $this->oConnection->where('cod_concepto', Vmovimientos_caja::getConceptoPagos());
        $this->oConnection->where('concepto', $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

}
