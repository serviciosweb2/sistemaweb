<?php

/**
 * Class Vmedios_pago
 *
 * Class  Vmedios_pago maneja todos los aspectos de  los medio de pago
 *
 * @package  SistemaIGA
 * @subpackage
 * @author   Vane
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vmedios_pago extends Tmedios_pago {

//    var $MUESTRACOBRAR = 1;
//    var $NOMUESTRACOBRAR = 0;
    var $ENTRADASALIDA = 1;
    var $ENTRADA = 0;

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    public function getObjmedio($arrMedio = NULL, $codigo = null) {
        
        $cod_medio = $codigo != null ? $codigo : -1;
        switch ($this->codigo) {
            case '2': //BOLETO BANCARIO
                break;
            case '3'://TARJETA
                $mTarjeta = new Vmedio_tarjetas($this->oConnection, $cod_medio);
                $mTarjeta->cod_tipo = $arrMedio['cod_tipo'];
                $mTarjeta->cod_terminal = $arrMedio['cod_terminal'];
                $mTarjeta->cupon = $arrMedio['cupon'];
                $mTarjeta->cod_autorizacion = $arrMedio['cod_autorizacion'];
                return $mTarjeta;
                break;
            case '4'://CHEQUE
                $mCheque = new Vmedio_cheques($this->oConnection, $cod_medio);
                $mCheque->cod_banco_emisor = $arrMedio['cod_banco_emisor'];
                $mCheque->tipo_cheque = $arrMedio['tipo_cheque'];
                $mCheque->emisor = $arrMedio['emisor'];
                $mCheque->fecha_cobro = $arrMedio['fecha_cobro'];
                $mCheque->nro_cheque = $arrMedio['nro_cheque'];
                return $mCheque;
                break;
//            case '5'://NOTA CREDITO
//                $mNotaCredito = new Vmedio_notas_credito($this->oConnection, $cod_medio);
//                $mNotaCredito->motivo = $arrMedio['motivo'];
//                $mNotaCredito->estado = $mNotaCredito->estadopendiente;
//                $mNotaCredito->arrRenglones = $arrMedio['renglones'];
//                return $mNotaCredito;
//                break;
            case '6'://DEPOSITO BANCARIO
                $mDeposito = new Vmedio_depositos($this->oConnection, $cod_medio);
                $mDeposito->cod_banco = $arrMedio['cod_banco'];
                $mDeposito->fecha_hora = $arrMedio['fecha_hora'];
                $mDeposito->nro_transaccion = $arrMedio['nro_transaccion'];
                $mDeposito->cuenta_nombre = $arrMedio['cuenta_nombre'];
                return $mDeposito;
                break;
            case '7'://TRANSFERENCIA
                $mTransferencia = new Vmedio_transferencias($this->oConnection, $cod_medio);
                $mTransferencia->cod_banco = $arrMedio['cod_banco'];
                $mTransferencia->fecha_hora = $arrMedio['fecha_hora'];
                $mTransferencia->nro_transaccion = $arrMedio['nro_transaccion'];
                $mTransferencia->cuenta_nombre = $arrMedio['cuenta_nombre'];
                return $mTransferencia;
                break;
            case '8'://TARJETA
                $mTarjeta = new Vmedio_debito($this->oConnection, $cod_medio);
                $mTarjeta->cod_tipo = $arrMedio['cod_tipo'];
                $mTarjeta->cod_terminal = $arrMedio['cod_terminal'];
                $mTarjeta->cupon = $arrMedio['cupon'];
                $mTarjeta->cod_autorizacion = $arrMedio['cod_autorizacion'];
                return $mTarjeta;
                break;
            default:
                return;
                break;
        }
    }

    public function guardarMedio($codCobro = null, $objMedioPago) {
        $objMedioPago->cod_cobro = $codCobro;
        switch ($this->codigo) {
            case '2': //BOLETO BANCARIO

                break;
            case '3'://TARJETA
                $objMedioPago->guardarMedio_tarjetas();
                break;
            case '4'://CHEQUE
                $objMedioPago->guardarMedio_cheques();
                break;
//            case '5'://NOTA CREDITO
//                $objMedioPago->guardar();
                break;
            case '6'://DEPOSITO BANCARIO
                $objMedioPago->guardarMedio_depositos();
                break;
            case '7'://TRANSFERENCIA
                $objMedioPago->guardarMedio_transferencias();
                break;

            case '8'://DEBITO
                $objMedioPago->guardarMedio_debito();
                break;

            default:
                break;
        }
    }

    static function listarMedios_pago(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false, $codPais = null) {
        $conexion->join("general.medios_pago_paises", "general.medios_pago_paises.cod_medio_pago = general.medios_pago.codigo");
        if ($codPais != null)
            $conexion->where("general.medios_pago_paises.cod_pais = ", $codPais);
        $conexion->group_by("general.medios_pago.codigo");
        return parent::listarMedios_pago($conexion, $condiciones, $limite, $orden, $grupo, $contar);
    }

}
