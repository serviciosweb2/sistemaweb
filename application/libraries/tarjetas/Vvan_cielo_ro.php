<?php

/**
* Class Vvan_cielo_ro
*
*Class  Vvan_cielo_ro maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vvan_cielo_ro extends Tvan_cielo_ro{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    /* PRIVATE FUNCTIONS */
    
    static public function _get_tipo_registro($string){
        return (integer) (substr($string, 1, 1));
    }

    static public function _get_establecimiento($string){
        return trim(substr($string, 2, 10));
    }

    static public function _get_numero_ro($string){
        return trim(substr($string, 12, 7));
    }

    static public function _get_parcela($string){
        return trim(substr($string, 19, 2));
    }

    static public function _get_filler($string){
        return substr($string, 21, 1);
    }

    static public function _get_plano($string){
        return trim(substr($string, 22, 2));
    }

    static public function _get_tipo_transaccion($string){
        return (integer) (substr($string, 24, 2));
    }

    static public function _get_fecha_presentacion($string){
        $resp = substr($string, 26, 6);
        return "20".substr($resp, 0, 2)."-".substr($resp, 2, 2)."-".substr($resp, 4, 2);
    }

    static public function _get_fecha_prevista_pago($string){
        $resp = substr($string, 32, 6);
        return "20".substr($resp, 0, 2)."-".substr($resp, 2, 2)."-".substr($resp, 4, 2);
    }

    static public function _get_fecha_envio_banco($string){
        $resp = substr($string, 38, 6);
        if ($resp == '000000' || trim($resp) == '')
            return null;
        else
            return "20".substr($resp, 0, 2)."-".substr($resp, 2, 2)."-".substr($resp, 4, 2);
    }

    static public function _get_signo_valor_bruto($string){
        return substr($string, 44, 1);
    }

    static public function _get_valor_bruto($string){
        $resp = (float) (substr($string, 45, 13));
        return (float) (substr($resp, 0, strlen($resp) - 2).".".substr($resp, strlen($resp) - 2));
    }

    static public function _get_signo_comision($string){
        return substr($string, 58, 1);
    }

    static public function _get_valor_comision($string){
        $resp = (float) (substr($string, 59, 13));
        return (float) (substr($resp, 0, strlen($resp) - 2).".".substr($resp, strlen($resp) - 2));
    }

    static public function _get_signo_valor_rechazado($string){
        return substr($string, 72, 1);
    }

    static public function _get_valor_rechazado($string){
        $resp = (float) (substr($string, 73, 13));
        return (float) (substr($resp, 0, strlen($resp) - 2).".".substr($resp, strlen($resp) - 2));
    }

    static public function _get_signo_valor_liquido($string){
        return substr($string, 86, 1);
    }

    static public function _get_valor_liquido($string){
        $resp = (float) (substr($string, 87, 13));
        return (float) (substr($resp, 0, strlen($resp) - 2).".".substr($resp, strlen($resp) - 2));
    }

    static public function _get_banco($string){
        return (integer) (substr($string, 100, 4));
    }

    static public function _get_agencia($string){
        return (integer) (substr($string, 104, 5));
    }

    static public function _get_cuenta_corriente($string){
        return trim(substr($string, 109, 14));
    }

    static public function _get_estado_pago($string){
        return (integer) (substr($string, 123, 2));
    }

    static public function _get_cantidad_cv_aceptados($string){
        return (integer) (substr($string, 125, 6));
    }

    static public function _get_identificador_producto_descartar($string){
        return (integer) (substr($string, 131, 2));
    }

    static public function _get_cantidad_cv_rechazados($string){
        return (integer) (substr($string, 133, 6));
    }

    static public function _get_identificador_reventa($string){
        return substr($string, 139, 1);
    }

    static public function _get_fecha_captura_transaccion($string){
        $resp = substr($string, 140, 6);
        if ($resp == '000000' || trim($resp) == '')
            return null;
        else
            return "20".substr($resp, 0, 2)."-".substr($resp, 2, 2)."-".substr($resp, 4, 2);
    }

    static public function _get_origen_ajuste($string){
        return substr($string, 146, 2);
    }

    static public function _get_valor_complementar($string){
        $resp = (float) (substr($string, 148, 13));
        return (float) (substr($resp, 0, strlen($resp) - 2).".".substr($resp, strlen($resp) - 2));
    }

    static public function _get_identificador_producto_financiero($string){
        return substr($string, 161, 1);
    }

    static public function _get_numero_operacion_financiera($string){
        return (float) (substr($string, 162, 9));
    }

    static public function _get_signo_valor_bruto_anticipado($string){
        return substr($string, 171, 1);
    }

    static public function _get_valor_bruto_anticipado($string){
        $resp = (float) (substr($string, 172, 13));
        return (float) (substr($resp, 0, strlen($resp) - 2).".".substr($resp, strlen($resp) - 2));
    }

    static public function _get_codigo_bandera($string){
        return (integer) (substr($string, 185, 3));
    }

    static public function _get_numero_unico_ro($string){
        return (integer) (substr($string, 188, 22));
    }

    static public function _get_tasa_comision($string){
        $resp = (float) (substr($string, 210, 4));
        return (float) (substr($resp, 0, strlen($resp) - 2).".".substr($resp, strlen($resp) - 2));
    }

    static public function _get_tarifa($string){
        $resp = (float) (substr($string, 214, 5));
        return (float) (substr($resp, 0, strlen($resp) - 2).".".substr($resp, strlen($resp) - 2));
    }

    static public function _get_tasa_garantia($string){
        $resp = (float) (substr($string, 219, 4));
        return (float) (substr($resp, 0, strlen($resp) - 2).".".substr($resp, strlen($resp) - 2));
    }

    static public function _get_medio_captura($string){
        return trim(substr($string, 223, 2));
    }

    static public function _get_numero_logico_terminal($string){
        return trim(substr($string, 225, 8));
    }

    static public function _get_identificador_producto($string){
        return substr($string, 233, 3);
    }

    static public function _get_uso_cielo($string){
        return trim(substr($string, 236, 15));
    }

    
    /* PUBLIC FUNCTIONS */
    
    public function loadFromString($string){
        $string = " ".trim($string);
        $this->tipo_registro = self::_get_tipo_registro($string);
        $this->establecimiento = self::_get_establecimiento($string);
        $this->numero_ro = self::_get_numero_ro($string);
        $this->parcela = self::_get_parcela($string);
        $this->filler = self::_get_filler($string);
        $this->plano = self::_get_plano($string);
        $this->tipo_transaccion = self::_get_tipo_transaccion($string);
        $this->fecha_presentacion = self::_get_fecha_presentacion($string);
        $this->fecha_prevista_pago = self::_get_fecha_prevista_pago($string);
        $this->fecha_envio_banco = self::_get_fecha_envio_banco($string);
        $this->signo_valor_bruto = self::_get_signo_valor_bruto($string);
        $this->valor_bruto = self::_get_valor_bruto($string);
        $this->signo_comision = self::_get_signo_comision($string);
        $this->valor_comision = self::_get_valor_comision($string);
        $this->signo_valor_rechazado = self::_get_signo_valor_rechazado($string);
        $this->valor_rechazado = self::_get_valor_rechazado($string);
        $this->signo_valor_liquido = self::_get_signo_valor_liquido($string);
        $this->valor_liquido = self::_get_valor_liquido($string);
        $this->banco = self::_get_banco($string);
        $this->agencia = self::_get_agencia($string);
        $this->cuenta_corriente = self::_get_cuenta_corriente($string);
        $this->estado_pago = self::_get_estado_pago($string);
        $this->cantidad_cv_aceptados = self::_get_cantidad_cv_aceptados($string);
        $this->identificador_producto_descartar = self::_get_identificador_producto_descartar($string);
        $this->cantidad_cv_rechazados = self::_get_cantidad_cv_rechazados($string);
        $this->identificador_reventa = self::_get_identificador_reventa($string);
        $this->fecha_captura_transaccion = self::_get_fecha_captura_transaccion($string);
        $this->origen_ajuste = self::_get_origen_ajuste($string);
        $this->valor_complementar = self::_get_valor_complementar($string);
        $this->identificador_producto_financiero = self::_get_identificador_producto_financiero($string);
        $this->numero_operacion_financiera = self::_get_numero_operacion_financiera($string);
        $this->signo_valor_bruto_anticipado = self::_get_signo_valor_bruto_anticipado($string);
        $this->valor_bruto_anticipado = self::_get_valor_bruto_anticipado($string);
        $this->codigo_bandera = self::_get_codigo_bandera($string);
        $this->numero_unico_ro = self::_get_numero_unico_ro($string);
        $this->tasa_comision = self::_get_tasa_comision($string);
        $this->tarifa = self::_get_tarifa($string);
        $this->tasa_garantia = self::_get_tasa_garantia($string);
        $this->medio_captura = self::_get_medio_captura($string);
        $this->numero_logico_terminal = self::_get_numero_logico_terminal($string);
        $this->identificador_producto = self::_get_identificador_producto($string);
        $this->uso_cielo = self::_get_uso_cielo($string);
        return $this->validar();

    }
    
    public function validar(){
        $arrFechaPresentacion = explode("-", $this->fecha_presentacion);
        $arrFechaPrevistaPago = explode("-", $this->fecha_prevista_pago);
//        $arrFechaEnvioBanco = explode("-", $this->fecha_envio_banco);
//        $arrFechaCaptura = explode ("-", $this->fecha_captura_transaccion);
        $valida = $this->tipo_registro == 1;
        $valida = $valida && count($arrFechaPresentacion) == 3 && checkdate($arrFechaPresentacion[1], $arrFechaPresentacion[2], $arrFechaPresentacion[0]);
        $valida = $valida && ($arrFechaPresentacion[0] == date("Y") - 1 || $arrFechaPresentacion[0] == date("Y") || $arrFechaPresentacion[0] == date("Y") + 1);
        $valida = $valida && count($arrFechaPrevistaPago) == 3 && checkdate($arrFechaPrevistaPago[1], $arrFechaPrevistaPago[2], $arrFechaPrevistaPago[0]);
        $valida = $valida && ($arrFechaPrevistaPago[0] == date("Y") - 1 || $arrFechaPrevistaPago[0] == date("Y") || $arrFechaPrevistaPago[0] == date("Y") + 1);
//        $valida = $valida && count($arrFechaEnvioBanco) == 3 && checkdate($arrFechaEnvioBanco[1], $arrFechaEnvioBanco[2], $arrFechaEnvioBanco[0]);
//        $valida = $valida && ($arrFechaEnvioBanco[0] == date("Y") - 1 || $arrFechaEnvioBanco[0] == date("Y") || $arrFechaEnvioBanco[0] == date("Y") + 1);
//        $valida = $valida && count($arrFechaCaptura) == 3 && checkdate($arrFechaCaptura[1], $arrFechaCaptura[2], $arrFechaCaptura[0]);
        $valida = $valida && ($this->identificador_reventa == "R" || $this->identificador_reventa == "A" || $this->identificador_reventa == ' ');
        $valida = $valida && ($this->identificador_producto_financiero == " " || $this->identificador_producto_financiero == "A" || $this->identificador_producto_financiero == "C");
        $valida = $valida && ($this->signo_comision == "+" || $this->signo_comision == "-");
        $valida = $valida && ($this->signo_valor_bruto == "+" ||$this->signo_valor_bruto == "-");
        $valida = $valida && ($this->signo_valor_bruto_anticipado == "+" || $this->signo_valor_bruto_anticipado == "-");
        $valida = $valida && ($this->signo_valor_liquido == "+" || $this->signo_valor_liquido == "-");
        $valida = $valida && ($this->signo_valor_rechazado == "+" || $this->signo_valor_rechazado == "-");
        $valida = $valida && Vvan_cielo::getProductos($this->identificador_producto) <> null;
        return $valida;
    }
    
    /* STATIC FUNCTIONS */
    
}

?>