<?php

class segmento_t{

    public $control_banco;
    public $control_lote;
    public $control_registro;
    public $servicio_numeroRegistro;
    public $servicio_segmento;
    public $servicio_CNAB;
    public $servicio_codigo_movimiento;
    public $cc_agencia_codigo;
    public $cc_agencia_dv;
    public $cc_cuenta_nro;
    public $cc_cuenta_dv;
    public $cc_DV;
    public $nosso_numero;
    public $convenio;
    public $numero_seguimiento;
    public $carteira;
    public $numero_documento;
    public $vencimiento;
    public $valor_titulo;
    public $banco_cobrador;
    public $agencia_cobrador;
    public $DV;
    public $titulo_empresa;
    public $codigo_moneda;
    public $sacado_inscripcion_tipo;
    public $sacado_inscripcion_numero;
    public $sacado_nombre;
    public $numero_contrato;
    public $valor_tarifa;
    public $motivo_ocurrencia;
    public $CNAB;
    
    function __construct($segmento) {
        $segmento = " ".$segmento;
        $this->control_banco = self::_get_control_banco($segmento);
        $this->control_lote = self::_get_control_lote($segmento);
        $this->control_registro = self::_get_control_registro($segmento);
        $this->servicio_numeroRegistro = self::_get_servicio_numeroRegistro($segmento);
        $this->servicio_segmento = self::_get_servicio_segmento($segmento);
        $this->servicio_CNAB = self::_get_servicio_CNAB($segmento);
        $this->servicio_codigo_movimiento = self::_get_servicio_codigo_movimiento($segmento);
        $this->cc_agencia_codigo = self::_get_cc_agencia_codigo($segmento);
        $this->cc_agencia_dv = self::_get_cc_agencia_dv($segmento);
        $this->cc_cuenta_nro = self::_get_cc_cuenta_nro($segmento);
        $this->cc_cuenta_dv = self::_get_cc_cuenta_dv($segmento);
        $this->cc_DV = self::_get_cc_DV($segmento);
        $this->nosso_numero = self::_get_nosso_numero($segmento);
        $this->carteira = self::_get_carteira($segmento);
        $this->numero_documento = self::_get_numero_documento($segmento);
        $this->vencimiento = self::_get_vencimiento($segmento);
        $this->valor_titulo = self::_get_valor_titulo($segmento);
        $this->banco_cobrador = self::_get_banco_cobrador($segmento);
        $this->agencia_cobrador = self::_get_agencia_cobrador($segmento);
        $this->DV = self::_get_DV($segmento);
        $this->titulo_empresa = self::_get_titulo_empresa($segmento);
        $this->codigo_moneda = self::_get_codigo_moneda($segmento);
        $this->sacado_inscripcion_tipo = self::_get_sacado_inscripcion_tipo($segmento);
        $this->sacado_inscripcion_numero = self::_get_sacado_inscripcion_numero($segmento);
        $this->sacado_nombre = self::_get_sacado_nombre($segmento);
        $this->numero_contrato = self::_get_numero_contrato($segmento);
        $this->valor_tarifa = self::_get_valor_tarifa($segmento);
        $this->motivo_ocurrencia = self::_get_motivo_ocurrencia($segmento);
        $this->CNAB = self::_get_CNAB($segmento);
        $this->convenio = self::_get_convenio($segmento);
        $this->numero_seguimiento = self::_get_numero_seguiminto($segmento);
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
    
    static private function _get_servicio_numeroRegistro($segmento){
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
    
    static private function _get_cc_agencia_codigo($segmento){
        return trim(substr($segmento, 18, 5));
    }
    
    static private function _get_cc_agencia_dv($segmento){
        return trim(substr($segmento, 23, 1));
    }
    static private function _get_cc_cuenta_nro($segmento){
        return trim(substr($segmento, 24, 12));
    }
    
    static private function _get_cc_cuenta_dv($segmento){
        return trim(substr($segmento, 36, 1));
    }
    
    static private function _get_cc_DV($segmento){
        return trim(substr($segmento, 37, 1));
    }
    
    static private function _get_nosso_numero($segmento){
        return trim(substr($segmento, 38, 20));
    }
    
    static private function _get_carteira($segmento){
        return trim(substr($segmento, 58, 1));
    }
    
    static private function _get_numero_documento($segmento){
        return trim(substr($segmento, 59, 15));
    }
    
    static private function _get_vencimiento($segmento){
        $resp = trim(substr($segmento, 74, 8));
        if (strlen($resp) == 8) $resp = substr ($resp, 4)."-".substr ($resp, 2, 2)."-".substr ($resp, 0, 2);
        return $resp;
    }
    
    static private function _get_valor_titulo($segmento){
        $resp = (integer) (substr($segmento, 82, 15));
        if ($resp == 0) $resp = "000";
        $resp = substr($resp, 0, strlen($resp) - 2).".".substr($resp, strlen($resp) - 2);
        return $resp;
    }
    
    static private function _get_banco_cobrador($segmento){
        return trim(substr($segmento, 97, 3));
    }
    
    static private function _get_agencia_cobrador($segmento){
        return trim(substr($segmento, 100, 5));
    }
    
    static private function _get_DV($segmento){
        return trim(substr($segmento, 105, 1));
    }
    
    static private function _get_titulo_empresa($segmento){
        return trim(substr($segmento, 106, 25));
    }
    
    static private function _get_codigo_moneda($segmento){
        return trim(substr($segmento, 131, 2));
    }
    
    static private function _get_sacado_inscripcion_tipo($segmento){
        return trim(substr($segmento, 133, 1));
    }
    
    static private function _get_sacado_inscripcion_numero($segmento){
        return trim(substr($segmento, 134, 15));
    }
    
    static private function _get_sacado_nombre($segmento){
        return trim(substr($segmento, 149, 40));
    }
    
    static private function _get_numero_contrato($segmento){
        return trim(substr($segmento, 189, 10));
    }
    
    static private function _get_valor_tarifa($segmento){
        $resp = (integer) (substr($segmento, 199, 13));
        if ($resp == 0) $resp = "000";
        $resp = substr($resp, 0, strlen($resp) - 2).".".substr($resp, strlen($resp) - 2);
        return $resp;
    }
    
    static private function _get_motivo_ocurrencia($segmento){
        return trim(substr($segmento, 214, 10));
    }
    
    static private function _get_CNAB($segmento){
        return trim(substr($segmento, 224, 17));
    }
    
    static private function _get_convenio($segmento){
        $nossoNumero = self::_get_nosso_numero($segmento);
        return trim(substr($nossoNumero, 0, 7));
    }
    
    static private function _get_numero_seguiminto($segmento){
        $nossoNumero = self::_get_nosso_numero($segmento);
        return substr($nossoNumero, 7, 10);        
    }    
}