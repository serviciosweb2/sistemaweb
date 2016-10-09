<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Tipos tarjetas
 * 
 * Modelo dedicado a gestionar todo lo relacionado con las entidades bancarias
 * 
 * @package bancos
 * @author vane
 * @version 1.1.0
 */
class Model_tipos_tarjetas extends CI_Model {

    /**
     * id de pais para instanciar el modelo
     * @access public
     * @var $id int
     */
    var $idpais;

    public function __construct($arg) {
        parent::__construct();
        $this->idpais = $arg['pais'];
    }

    /**
     * retorna todas los tipos tarjetas de un pais
     * @access public
     * @return array Bancos
     */
    public function getTipos() {
        $this->load->database();
        $conexion = $this->db;
        
        return Vtipos_tarjetas::listarTipos_tarjetas($conexion);
    }


    public function getTiposTarjetasTerminal($codigo, $cod_filial) {
        $conexion = $this->load->database($cod_filial, true);
        $terminal = new Vpos_terminales($conexion, $codigo);
        $tarjetas = $terminal->getTiposTarjetas();

        return $tarjetas;
    }

    public function getTiposDebitoTerminal($codigo, $cod_filial) {
        $conexion = $this->load->database($cod_filial, true);
        $terminal = new Vpos_terminales($conexion, $codigo);
        $tarjetas = $terminal->getTiposDebito();
        return $tarjetas;
    }
}
