<?php

class trailer_lote{
    
    public $control_banco;
    public $control_lote;
    public $control_registro;
    public $CNAB;
    public $cantidad_registros;
    public $total_cobranzas_simples_cantidad_titulos_cobranzas;
    public $total_cobranzas_simples_valor_titulos_carteiras;    
    public $total_cobranza_vinculada_cantidad_titulos_cobranzas;
    public $total_cobranza_vinculada_valor_titulos_carteiras;    
    public $total_cobranza_caucionada_cantidad_cobranzas;
    public $total_cobranza_caucionada_cantidad_carteiras;
    public $total_cobranza_descontada_cantidad_titulos_cobranzas;
    public $total_cobranza_descontada_valor_titulios_carteira;
    public $numero_aviso;
    public $CNAB2;
    
    function __construct($trailerLote) {
        $trailerLote = " ".$trailerLote;
        $this->control_banco = self::_get_control_banco($trailerLote);
        $this->control_lote = self::_get_control_lote($trailerLote);
        $this->control_registro = self::_get_control_registro($trailerLote);
        $this->CNAB = self::_get_CNAB($trailerLote);
        $this->cantidad_registros = self::_get_cantidad_registros($trailerLote);
        $this->total_cobranzas_simples_cantidad_titulos_cobranzas = self::_get_total_cobranzas_simples_cantidad_titulos_cobranzas($trailerLote);
        $this->total_cobranzas_simples_valor_titulos_carteiras = self::_get_total_cobranzas_simples_valor_titulos_carteiras($trailerLote);
        $this->total_cobranza_vinculada_cantidad_titulos_cobranzas = self::_get_total_cobranza_vinculada_cantidad_titulos_cobranzas($trailerLote);
        $this->total_cobranza_vinculada_valor_titulos_carteiras = self::_get_total_cobranza_vinculada_valor_titulos_carteiras($trailerLote);    
        $this->total_cobranza_caucionada_cantidad_cobranzas = self::_get_total_cobranza_caucionada_cantidad_cobranzas($trailerLote);
        $this->total_cobranza_caucionada_cantidad_carteiras = self::_get_total_cobranza_caucionada_cantidad_carteiras($trailerLote);
        $this->total_cobranza_descontada_cantidad_titulos_cobranzas = self::_get_total_cobranza_descontada_cantidad_titulos_cobranzas($trailerLote);
        $this->total_cobranza_descontada_valor_titulios_carteira = self::_get_total_cobranza_descontada_valor_titulios_carteira($trailerLote);
        $this->numero_aviso = self::_get_numero_aviso($trailerLote);
        $this->CNAB2 = self::_get_CNAB2($trailerLote);
    }
   
    static private function _get_control_banco($trailerLote){
        return trim(substr($trailerLote, 1, 3));
    }
    
    static private function _get_control_lote($trailerLote){
        return trim(substr($trailerLote, 4, 4));
    }
    
    static private function _get_control_registro($trailerLote){
        return trim(substr($trailerLote, 8, 1));
    }
    
    static private function _get_CNAB($trailerLote){
        return trim(substr($trailerLote, 9, 9));
    }
    
    static private function _get_cantidad_registros($trailerLote){
        return trim(substr($trailerLote, 18, 6));
    }
    
    static private function _get_total_cobranzas_simples_cantidad_titulos_cobranzas($trailerLote){
        return trim(substr($trailerLote, 24, 6));
    }
    
    static private function _get_total_cobranzas_simples_valor_titulos_carteiras($trailerLote){
        $resp = (integer) (substr($trailerLote, 30, 17)); // 2 decimales
        if ($resp == 0) $resp = "000";
        $resp = substr($resp, 0, strlen($resp) - 2).".".substr($resp, strlen($resp) - 2);
        return $resp;
    }
    
    static private function _get_total_cobranza_vinculada_cantidad_titulos_cobranzas($trailerLote){
        return trim(substr($trailerLote, 47, 6));
    }
    
    static private function _get_total_cobranza_vinculada_valor_titulos_carteiras($trailerLote){
        $resp = (integer) (substr($trailerLote, 53, 17)); // 2 decimales
        if ($resp == 0) $resp = "000";
        $resp = substr($resp, 0, strlen($resp) - 2).".".substr($resp, strlen($resp) - 2);
        return $resp;
    }
    
    static private function _get_total_cobranza_caucionada_cantidad_cobranzas($trailerLote){
        return trim(substr($trailerLote, 70, 6));
    }
    
    static private function _get_total_cobranza_caucionada_cantidad_carteiras($trailerLote){
        $resp = (integer) (substr($trailerLote, 76, 17)); // 2 decimales
        if ($resp == 0) $resp = "000";
        $resp = substr($resp, 0, strlen($resp) - 2).".".substr($resp, strlen($resp) - 2);
        return $resp;
    }
    
    static private function _get_total_cobranza_descontada_cantidad_titulos_cobranzas($trailerLote){
        return trim(substr($trailerLote, 93, 6));
    }
    
    static private function _get_total_cobranza_descontada_valor_titulios_carteira($trailerLote){
        return trim(substr($trailerLote, 99, 17));
    }
    
    static private function _get_numero_aviso($trailerLote){
        return trim(substr($trailerLote, 116, 8));
    }
    
    static private function _get_CNAB2($trailerLote){
        return trim(substr($trailerLote, 124, 117));
    }
    
}