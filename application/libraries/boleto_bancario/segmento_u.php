<?php

class segmento_u{
    public $control_banco;
    public $control_lote;
    public $control_registro;
    public $servicio_numero_registro;
    public $servicio_segmento;
    public $servicio_CNAB;
    public $servicio_codigo_movimiento;
    public $datos_titulo_acresimos;
    public $datos_titulo_valor_desconto_concedido;
    public $datos_titulo_valor_abatimiento;
    public $valor_titulo_IOF;
    public $valor_titulo_pago;
    public $valor_titulo_liquido;
    public $valor_otras_despesas;
    public $valor_otros_creditos;
    public $fecha_ocurrencia;
    public $fecha_credito;
    public $ocurrencia_sacado_codigo;
    public $ocurrencia_sacado_fecha;
    public $ocurrencia_sacado_valor;
    public $ocurrencia_sacado_complemento;
    public $codigo_banco_compensacion;
    public $nosso_numero_banco_correspondente;
    public $CNAB;
    
    function __construct($segmento) {
        $segmento = " ".$segmento;
        $this->control_banco = self::_get_control_banco($segmento);
        $this->control_lote = self::_get_control_lote($segmento);
        $this->control_registro = self::_get_control_registro($segmento);
        $this->servicio_numero_registro = self::_get_servicio_numero_registro($segmento);
        $this->servicio_segmento = self::_get_servicio_segmento($segmento);
        $this->servicio_CNAB = self::_get_servicio_CNAB($segmento);
        $this->servicio_codigo_movimiento = self::_get_servicio_codigo_movimiento($segmento);
        $this->datos_titulo_acresimos = self::_get_datos_titulo_acresimos($segmento);
        $this->datos_titulo_valor_desconto_concedido = self::_get_datos_titulo_valor_desconto_concedido($segmento);
        $this->datos_titulo_valor_abatimiento = self::_get_datos_titulo_valor_abatimiento($segmento);
        $this->valor_titulo_IOF = self::_get_valor_titulo_IOF($segmento);
        $this->valor_titulo_pago = self::_get_valor_titulo_pago($segmento);
        $this->valor_titulo_liquido = self::_get_valor_titulo_liquido($segmento);
        $this->valor_otras_despesas = self::_get_valor_otras_despesas($segmento);
        $this->valor_otros_creditos = self::_get_valor_otros_creditos($segmento);
        $this->fecha_ocurrencia = self::_get_fecha_ocurrencia($segmento);
        $this->fecha_credito = self::_get_fecha_credito($segmento);
        $this->ocurrencia_sacado_codigo = self::_get_ocurrencia_sacado_codigo($segmento);
        $this->ocurrencia_sacado_fecha = self::_get_ocurrencia_sacado_fecha($segmento);
        $this->ocurrencia_sacado_valor = self::_get_ocurrencia_sacado_valor($segmento);
        $this->ocurrencia_sacado_complemento = self::_get_ocurrencia_sacado_complemento($segmento);
        $this->codigo_banco_compensacion = self::_get_codigo_banco_compensacion($segmento);
        $this->nosso_numero_banco_correspondente = self::_get_nosso_numero_banco_correspondente($segmento);
        $this->CNAB = self::_get_CNAB($segmento);
    }
    
    static private function _get_control_banco($segmento){
        return trim(substr($segmento, 1, 3));
    }
    
    static private function _get_control_lote($segmento){
        return trim(substr($segmento, 4, 4));
    }
    
    static private function _get_control_registro($segmento){
        return trim(substr($segmento, 8, 1));
    }
    
    static private function _get_servicio_numero_registro($segmento){
        return trim(substr($segmento, 9, 5));
    }
    
    static private function _get_servicio_segmento($segmento){
        return trim(substr($segmento, 14, 1));
    }
    
    static private function _get_servicio_CNAB($segmento){
        return trim(substr($segmento, 15, 1));
    }
    
    static private function _get_servicio_codigo_movimiento($segmento){
        return trim(substr($segmento, 16, 2));
    }
    
    static private function _get_datos_titulo_acresimos($segmento){
        $resp = (integer) (substr($segmento, 18, 15));
        if ($resp == 0) $resp = "000";
        $resp = substr($resp, 0, strlen($resp) - 2).".".substr($resp, strlen($resp) - 2);
        return $resp;
    }
    
    static private function _get_datos_titulo_valor_desconto_concedido($segmento){
        $resp = (integer) (substr($segmento, 33, 15));
        if ($resp == 0) $resp = "000";
        $resp = substr($resp, 0, strlen($resp) - 2).".".substr($resp, strlen($resp) - 2);
        return $resp;
    }
    
    static private function _get_datos_titulo_valor_abatimiento($segmento){
        $resp = (integer) (substr($segmento, 48, 15));
        if ($resp == 0) $resp = "000";
        $resp = substr($resp, 0, strlen($resp) - 2).".".substr($resp, strlen($resp) - 2);
        return $resp;
    }
    
    static private function _get_valor_titulo_IOF($segmento){
        $resp = (integer) (substr($segmento, 63, 15));
        if ($resp == 0) $resp = "000";
        $resp = substr($resp, 0, strlen($resp) - 2).".".substr($resp, strlen($resp) - 2);
        return $resp;
    }
    
    static private function _get_valor_titulo_pago($segmento){
        $resp = (integer) (substr($segmento, 78, 15));
        if ($resp == 0) $resp = "000";
        $resp = substr($resp, 0, strlen($resp) - 2).".".substr($resp, strlen($resp) - 2);
        return $resp;
    }
    
    static private function _get_valor_titulo_liquido($segmento){
        $resp = (integer) substr($segmento, 93, 15);
        if ($resp == 0) $resp = "000";
        $resp = substr($resp, 0, strlen($resp) - 2).".".substr($resp, strlen($resp) - 2);
        return $resp;
    }
    
    static private function _get_valor_otras_despesas($segmento){
        $resp = (integer) (substr($segmento, 108, 15));
        if ($resp == 0) $resp = "000";
        $resp = substr($resp, 0, strlen($resp) - 2).".".substr($resp, strlen($resp) - 2);
        return $resp;
    }
    
    static private function _get_valor_otros_creditos($segmento){
        $resp = (integer) (substr($segmento, 123, 15));
        if ($resp == 0) $resp = "000";
        $resp = substr($resp, 0, strlen($resp) - 2).".".substr($resp, strlen($resp) - 2);
        return $resp;
        
    }
    
    static private function _get_fecha_ocurrencia($segmento){
        $resp = trim(substr($segmento, 138, 8));
        if (strlen($resp) == 8){
            $resp = substr($resp, 4)."-".substr($resp, 2, 2)."-".substr($resp, 0, 2);
        }
        return $resp;
    }
    
    static private function _get_fecha_credito($segmento){
        $resp = trim(substr($segmento, 146, 8));
        if (strlen($resp) == 8){
            $resp = $resp = substr($resp, 4)."-".substr($resp, 2, 2)."-".substr($resp, 0, 2);
        }
        return $resp;
    }
    
    static private function _get_ocurrencia_sacado_codigo($segmento){
        return trim(substr($segmento, 154, 4));
    }
    
    static private function _get_ocurrencia_sacado_fecha($segmento){
        $resp = trim(substr($segmento, 158, 8));
        if ($resp == "00000000") $resp = "";
         if (strlen($resp) == 8){
            $resp = $resp = substr($resp, 4)."-".substr($resp, 2, 2)."-".substr($resp, 0, 2);
        }
        return $resp;
    }
    
    static private function _get_ocurrencia_sacado_valor($segmento){
        $resp = (integer) (substr($segmento, 166, 15));
        if ($resp == 0) $resp = "000";
        $resp = substr($resp, 0, strlen($resp) - 2).".".substr($resp, strlen($resp) - 2);
        return $resp;
    }
    
    static private function _get_ocurrencia_sacado_complemento($segmento){
        return trim(substr($segmento, 181, 30));
    }
    
    static private function _get_codigo_banco_compensacion($segmento){
        return trim(substr($segmento, 211, 3));
    }
    
    static private function _get_nosso_numero_banco_correspondente($segmento){
        return trim(substr($segmento, 214, 20));
    }
    
    static private function _get_CNAB($segmento){
        return trim(substr($segmento, 234, 7));
    }    
}