<?php

/**
* Class Vvan_cielo_cv
*
*Class  Vvan_cielo_cv maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vvan_cielo_cv extends Tvan_cielo_cv{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    /* PRIVATE FUNCTIONS */
    
    static private function _get_tipo_registro($string){
        return substr($string, 1, 1);
    }

    static private function _get_establecimiento($string){
        return (float) (substr($string, 2, 10));
    }

    static private function _get_numero_ro($string){
        return (float) (substr($string, 12, 7));
    }

    static private function _get_numero_tarjeta($string){
        return trim(substr($string, 19, 19));
    }

    static private function _get_fecha_venta($string){
        $resp = substr($string, 38, 8);
        return substr($resp, 0, 4)."-".substr($resp, 4, 2)."-".substr($resp, 6, 2);
    }

    static private function _get_signo_valor_compra($string){
        return substr($string, 46, 1);
    }

    static private function _get_valor_compra($string){
        $resp = (float) (substr($string, 47, 13));
        return (float) (substr($resp, 0, strlen($resp) - 2).".".substr($resp, strlen($resp) - 2));
    }

    static private function _get_parcela($string){
        return (integer) (substr($string, 60, 2));
    }

    static private function _get_total_parcelas($string){
        return (integer) (substr($string, 62, 2));
    }

    static private function _get_motivo_rechazo($string){
        return trim(substr($string, 64, 3));
    }

    static private function _get_codigo_autorizacion($string){
        return trim(substr($string, 67, 6));
    }

    static private function _get_tid($string){
        return trim(substr($string, 73, 20));
    }

    static private function _get_nsu_doc($string){
        return trim(substr($string, 93, 6));
    }

    static private function _get_valor_complementar($string){
        $resp = (float) (substr($string, 99, 13));
        return (float) (substr($resp, 0, strlen($resp) - 2).".".substr($resp, strlen($resp) - 2));
    }

    static private function _get_digitos_tarjeta($string){
        return (integer) (substr($string, 112, 2));
    }

    static private function _get_valor_total_venta($string){
        $resp = (float) (substr($string, 114, 13));
        return (float) (substr($resp, 0, strlen($resp) - 2).".".substr($resp, strlen($resp) -2));
    }

    static private function _get_valor_proxima_parcela($string){
        $resp = (float) (substr($string, 127, 13));
        return (float) (substr($resp, 0, strlen($resp) - 2).".".substr($resp, strlen($resp) - 2));
    }

    static private function _get_numero_nota_fiscal($string){
        return (float) (substr($string, 140, 9));
    }

    static private function _get_indicador_tarjeta_exterior($string){
        return (integer) (substr($string, 149, 4));
    }

    static private function _get_numero_logico_terminal($string){
        return trim(substr($string, 153, 8));
    }

    static private function _get_indicador_tasa_embarque($string){
        return trim(substr($string, 161, 2));
    }

    static private function _get_referencia_codigo_pedido($string){
        return trim(substr($string, 163, 20));
    }

    static private function _get_hora_transaccion($string){
        $resp = substr($string, 183, 6);
        $resp = substr($resp, 0, 2).":".substr($resp, 2, 2).":".substr($resp, 4, 2);
        return $resp;
    }

    static private function _get_numero_unico_transaccion($string){
        return trim(substr($string, 189, 29));
    }

    static private function _get_indicador_cielo_premia($string){
        return trim(substr($string, 218, 1));
    }

    static private function _get_uso_cielo($string){
        return trim(substr($string, 219, 32));
    }

    
    /* PUBLIC FUNCTIONS */
    
    public function loadFromString($string){
        $string = " ".trim($string);
        $this->tipo_registro = self::_get_tipo_registro($string);
        $this->establecimiento = self::_get_establecimiento($string);
        $this->numero_ro = self::_get_numero_ro($string);
        $this->numero_tarjeta = self::_get_numero_tarjeta($string);
        $this->fecha_venta = self::_get_fecha_venta($string);
        $this->signo_valor_compra = self::_get_signo_valor_compra($string);
        $this->valor_compra = self::_get_valor_compra($string);
        $this->parcela = self::_get_parcela($string);
        $this->total_parcelas = self::_get_total_parcelas($string);
        $this->motivo_rechazo = self::_get_motivo_rechazo($string);
        $this->codigo_autorizacion = self::_get_codigo_autorizacion($string);
        $this->tid = self::_get_tid($string);
        $this->nsu_doc = self::_get_nsu_doc($string);
        $this->valor_complementar = self::_get_valor_complementar($string);
        $this->digitos_tarjeta = self::_get_digitos_tarjeta($string);
        $this->valor_total_venta = self::_get_valor_total_venta($string);
        $this->valor_proxima_parcela = self::_get_valor_proxima_parcela($string);
        $this->numero_nota_fiscal = self::_get_numero_nota_fiscal($string);
        $this->indicador_tarjeta_exterior = self::_get_indicador_tarjeta_exterior($string);
        $this->numero_logico_terminal = self::_get_numero_logico_terminal($string);
        $this->indicador_tasa_embarque = self::_get_indicador_tasa_embarque($string);
        $this->referencia_codigo_pedido = self::_get_referencia_codigo_pedido($string);
        $this->hora_transaccion = self::_get_hora_transaccion($string);
        $this->numero_unico_transaccion = self::_get_numero_unico_transaccion($string);
        $this->indicador_cielo_premia = self::_get_indicador_cielo_premia($string);
        $this->uso_cielo = self::_get_uso_cielo($string);
        return $this->validar();
    }
    
    public function validar(){
        $arrFechaVenta = explode("-", $this->fecha_venta);
        $valida = $this->tipo_registro == "2";
        $valida = $valida && count($arrFechaVenta) == 3 && checkdate($arrFechaVenta[1], $arrFechaVenta[2], $arrFechaVenta[0]);
        $valida = $valida && ($arrFechaVenta[0] == date("Y") - 1 || $arrFechaVenta[0] == date("Y") || $arrFechaVenta[0] == date("Y") + 1);
        $valida = $valida && ($this->signo_valor_compra == "+" || $this->signo_valor_compra == "-");
        return $valida;
    }
    
    /* STATIC FUNCTIONS */
    
}

?>