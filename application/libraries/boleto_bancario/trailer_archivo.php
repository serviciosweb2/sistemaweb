<?php

class trailer_archivo{
    
    public $control_banco;
    public $control_lote;
    public $control_registro;
    public $CNAB;
    public $total_cantiadad_lotes;
    public $total_cantidad_registros;
    public $total_cantidad_cuentas;
    public $CNAB2;
    
    function __construct($trailer) {
        $trailer = " ".$trailer;
        $this->control_banco = self::_get_control_banco($trailer);
        $this->control_lote = self::_get_control_lote($trailer);
        $this->control_registro = self::_get_control_registro($trailer);
        $this->CNAB = self::_get_CNAB($trailer);
        $this->total_cantiadad_lotes = self::_get_total_cantiadad_lotes($trailer);
        $this->total_cantidad_registros = self::_get_total_cantidad_registros($trailer);
        $this->total_cantidad_cuentas = self::_get_total_cantidad_cuentas($trailer);
        $this->CNAB2 = self::_get_CNAB2($trailer);
    }
    
    static private function _get_control_banco($trailer){
        return trim(substr($trailer, 1, 3));
    }
    
    static private function _get_control_lote($trailer){
        return trim(substr($trailer, 4, 4));
    }
    
    static private function _get_control_registro($trailer){
        return trim(substr($trailer, 8, 1));
    }
    
    static private function _get_CNAB($trailer){
        return trim(substr($trailer, 9, 9));
    }
    
    static private function _get_total_cantiadad_lotes($trailer){
        return trim(substr($trailer, 18, 6));
    }
    
    static private function _get_total_cantidad_registros($trailer){
        return trim(substr($trailer, 24, 6));
    }
    
    static private function _get_total_cantidad_cuentas($trailer){
        return trim(substr($trailer, 30, 6));
    }
    
    static private function _get_CNAB2($trailer){
        return trim(substr($trailer, 36, 205));
    }
    
    
}