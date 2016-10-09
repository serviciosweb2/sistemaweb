<?php

class header_archivo{
    
    public $control_banco;
    public $control_lote;
    public $control_registro;
    public $CNAB;
    public $empresa_inscripcion_tipo;
    public $empresa_inscripcion_numero;
    public $empresa_convenio;
    public $empresa_ctacte_agencia_codigo;
    public $empresa_ctacte_agencia_dv;
    public $empresa_ctacte_conta_numero;
    public $empresa_ctacte_contra_dv;
    public $empresa_ctacte_dv;
    public $empresa_nombre;
    public $nombre_banco;
    public $CNAB_FEBRABAN;
    public $archivo_codigo;
    public $archivo_fecha_generacion;
    public $archivo_hora_generacion;
    public $archivo_secuencia;
    public $archivo_layaut;
    public $archivo_densidad;
    public $reservado_banco;
    public $reservado_empresa;
    public $reservado_CNAB;
    
    function __construct($header) {
        $header = " ".$header;        
        $this->control_banco = self::_get_control_banco($header);
        $this->control_lote = self::_get_control_lote($header);
        $this->control_registro = self::_get_control_registro($header);
        $this->CNAB = self::_get_CNAB($header);
        $this->empresa_inscripcion_tipo = self::_get_empresa_inscripcion_tipo($header);
        $this->empresa_inscripcion_numero = self::_get_empresa_inscripcion_numero($header);
        $this->empresa_convenio = self::_get_empresa_convenio($header);
        $this->empresa_ctacte_agencia_codigo = self::_get_empresa_ctacte_agencia_codigo($header);
        $this->empresa_ctacte_agencia_dv = self::_get_empresa_ctacte_agencia_dv($header);
        $this->empresa_ctacte_conta_numero = self::_get_empresa_ctacte_conta_numero($header);
        $this->empresa_ctacte_contra_dv = self::_get_empresa_ctacte_contra_dv($header);
        $this->empresa_ctacte_dv = self::_get_empresa_ctacte_dv($header);
        $this->empresa_nombre = self::_get_empresa_nombre($header);
        $this->nombre_banco = self::_get_nombre_banco($header);
        $this->CNAB_FEBRABAN = self::_get_CNAB_FEBRABAN($header);
        $this->archivo_codigo = self::_get_archivo_codigo($header);
        $this->archivo_fecha_generacion = self::_get_archivo_fecha_generacion($header);
        $this->archivo_hora_generacion = self::_get_archivo_hora_generacion($header);
        $this->archivo_secuencia = self::_get_archivo_secuencia($header);
        $this->archivo_layaut = self::_get_archivo_layaut($header);
        $this->archivo_densidad = self::_get_archivo_densidad($header);
        $this->reservado_banco = self::_get_reservado_banco($header);
        $this->reservado_empresa = self::_get_reservado_empresa($header);
        $this->reservado_CNAB = self::_get_reservado_CNAB($header);
    }
    
    static private function _get_control_banco($header){
        return trim(substr($header, 1, 3));
    }
    
    static private function _get_control_lote($header){
        return trim(substr($header, 4, 4));
    }
    
    static private function _get_control_registro($header){
        return trim(substr($header, 8, 1));
    }
    
    static private function _get_CNAB($header){
        return trim(substr($header, 9, 9));
    }
    
    static private function _get_empresa_inscripcion_tipo($header){
        return trim(substr($header, 18, 1));
    }
    
    static private function _get_empresa_inscripcion_numero($header){
        return trim(substr($header, 19, 14));
    }
    
    static private function _get_empresa_convenio($header){
        return (int) trim(substr($header, 33, 9));
    }
    
    static private function _get_empresa_ctacte_agencia_codigo($header){
        return trim(substr($header, 53, 5));
    }
    
    static private function _get_empresa_ctacte_agencia_dv($header){
        return trim(substr($header, 58, 1));
    }
    
    static private function _get_empresa_ctacte_conta_numero($header){
        return trim(substr($header, 59, 12));
    }
    
    static private function _get_empresa_ctacte_contra_dv($header){
        return trim(substr($header, 71, 1));
    }
    
    static private function _get_empresa_ctacte_dv($header){
        return trim(substr($header, 72, 1));
    }
    
    static private function _get_empresa_nombre($header){
        return trim(substr($header, 73, 30));
    }
    
    static private function _get_nombre_banco($header){
        return trim(substr($header, 103, 30));
    }
    
    static private function _get_CNAB_FEBRABAN($header){
        return trim(substr($header, 133, 10));
    }
    
    static private function _get_archivo_codigo($header){
        return trim(substr($header, 143, 1));
    }
    
    static private function _get_archivo_fecha_generacion($header){
        $resp = trim(substr($header, 144, 8));
        if ($resp == "00000000") $resp = "";
        if (strlen($resp) == 8){
            $resp = substr($resp, 4)."-".substr($resp, 2, 2)."-".substr($resp, 0, 2);
        }
        return $resp;
    }
    
    static private function _get_archivo_hora_generacion($header){
        $resp = trim(substr($header, 152, 6));
        if (strlen($resp) == 6){
            $resp = substr($resp, 0, 2).":".substr($resp, 2, 2).":".substr($resp, 4);
        }
        return $resp;
    }
    
    static private function _get_archivo_secuencia($header){
        return (int)trim(substr($header, 158, 6));
    }
    
    static private function _get_archivo_layaut($header){
        return trim(substr($header, 164, 3));
    }
    
    static private function _get_archivo_densidad($header){
        return trim(substr($header, 167, 5));
    }
    
    static private function _get_reservado_banco($header){
        return trim(substr($header, 172, 20));
    }
    
    static private function _get_reservado_empresa($header){
        return trim(substr($header, 192, 20));
    }
    
    static private function _get_reservado_CNAB($header){
        return trim(substr($header, 212, 29));
    }
    
    
}