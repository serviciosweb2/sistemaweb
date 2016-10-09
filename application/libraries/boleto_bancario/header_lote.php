<?php

class header_lote{
    
    public $control_banco;
    public $control_lote;
    public $control_registro;
    public $servicio_operacion;
    public $servicio_servicio;
    public $servicio_CNAB;
    public $servicio_layout_lote;
    public $CNAB;
    public $empresa_inscripcion_tipo;
    public $empresa_inscripcion_numero;
    public $empresa_convenio;
    public $empresa_ctacte_agencia_codigo;
    public $empresa_ctacte_agencia_dv;
    public $empresa_ctacte_conta_numero;
    public $empresa_ctacte_conta_dv;
    public $empresa_ctacte_dv;
    public $empresa_nombre;
    public $informacion1;
    public $informacion2;
    public $control_cobranza_numero_retorno;
    public $control_cobranza_fecha_retorno;
    public $fecha_credito;
    public $CNAB2;
    
    function __construct($headerLote) {
        $headerLote = " ".$headerLote;
        $this->control_banco = self::_get_control_banco($headerLote);
        $this->control_lote = self::_get_control_lote($headerLote);
        $this->control_registro = self::_get_control_registro($headerLote);
        $this->servicio_operacion = self::_get_servicio_operacion($headerLote);
        $this->servicio_servicio = self::_get_servicio_servicio($headerLote);
        $this->servicio_CNAB = self::_get_servicio_CNAB($headerLote);
        $this->servicio_layout_lote = self::_get_servicio_layout_lote($headerLote);
        $this->CNAB = self::_get_CNAB($headerLote);
        $this->empresa_inscripcion_tipo = self::_get_empresa_inscripcion_tipo($headerLote);
        $this->empresa_inscripcion_numero = self::_get_empresa_inscripcion_numero($headerLote);
        $this->empresa_convenio = self::_get_empresa_convenio($headerLote);
        $this->empresa_ctacte_agencia_codigo = self::_get_empresa_ctacte_agencia_codigo($headerLote);
        $this->empresa_ctacte_agencia_dv = self::_get_empresa_ctacte_agencia_dv($headerLote);
        $this->empresa_ctacte_conta_numero = self::_get_empresa_ctacte_conta_numero($headerLote);
        $this->empresa_ctacte_conta_dv = self::_get_empresa_ctacte_conta_dv($headerLote);
        $this->empresa_ctacte_dv = self::_get_empresa_ctacte_dv($headerLote);
        $this->empresa_nombre = self::_get_empresa_nombre($headerLote);
        $this->informacion1 = self::_get_informacion1($headerLote);
        $this->informacion2 = self::_get_informacion2($headerLote);
        $this->control_cobranza_numero_retorno = self::_get_control_cobranza_numero_retorno($headerLote);
        $this->control_cobranza_fecha_retorno = self::_get_control_cobranza_fecha_retorno($headerLote);
        $this->fecha_credito = self::_get_fecha_credito($headerLote);
        $this->CNAB2 = self::_get_CNAB2($headerLote);
    }
    
    static private function _get_control_banco($headerLote){
        return trim(substr($headerLote, 1, 3));
    }
    
    static private function _get_control_lote($headerLote){
        return trim(substr($headerLote, 4, 4));
    }
    
    static private function _get_control_registro($headerLote){
        return trim(substr($headerLote, 8, 1));
    }
    
    static private function _get_servicio_operacion($headerLote){
        return trim(substr($headerLote, 9, 1));
    }
    
    static private function _get_servicio_servicio($headerLote){
        return trim(substr($headerLote, 10, 2));
    }
    
    static private function _get_servicio_CNAB($headerLote){
        return trim(substr($headerLote, 12, 2));
    }
    
    static private function _get_servicio_layout_lote($headerLote){
        return trim(substr($headerLote, 14, 3));
    }
    
    static private function _get_CNAB($headerLote){
        return trim(substr($headerLote, 17, 1));
    }
    
    static private function _get_empresa_inscripcion_tipo($headerLote){
        return trim(substr($headerLote, 18, 1));
    }
    
    static private function _get_empresa_inscripcion_numero($headerLote){
        return trim(substr($headerLote, 19, 15));
    }
    
    static private function _get_empresa_convenio($headerLote){
        return trim(substr($headerLote, 34, 20));
    }
    
    static private function _get_empresa_ctacte_agencia_codigo($headerLote){
        return trim(substr($headerLote, 54, 5));
    }
    
    static private function _get_empresa_ctacte_agencia_dv($headerLote){
        return trim(substr($headerLote, 59, 1));
    }
    
    static private function _get_empresa_ctacte_conta_numero($headerLote){
        return trim(substr($headerLote, 60, 12));
    }
    
    static private function _get_empresa_ctacte_conta_dv($headerLote){
        return trim(substr($headerLote, 72, 1));
    }
    
    static private function _get_empresa_ctacte_dv($headerLote){
        return trim(substr($headerLote, 73, 1));
    }
    
    static private function _get_empresa_nombre($headerLote){
        return trim(substr($headerLote, 74, 30));
    }
    
    static private function _get_informacion1($headerLote){
        return trim(substr($headerLote, 104, 40));
    }
    
    static private function _get_informacion2($headerLote){
        return trim(substr($headerLote, 144, 40));
    }
    
    static private function _get_control_cobranza_numero_retorno($headerLote){
        return trim(substr($headerLote, 184, 8));
    }
    
    static private function _get_control_cobranza_fecha_retorno($headerLote){
        $resp =  trim(substr($headerLote, 192, 8));
        if ($resp == "00000000") $resp = "";
        if (strlen($resp) == 8){
            $resp = substr($resp, 4)."-".substr($resp, 2, 2)."-".substr($resp, 0, 2);
        }
        return $resp;
    }
    
    static private function _get_fecha_credito($headerLote){
        $resp = trim(substr($headerLote, 200, 8));
        if ($resp == "00000000") $resp = "";
        if (strlen($resp) == 8){
            $resp = substr($resp, 4)."-".substr($resp, 2, 2)."-".substr($resp, 0, 2);
        }
        return $resp;
    }
    
    static private function _get_CNAB2($headerLote){
        return trim(substr($headerLote, 208, 33));
    }
    
}