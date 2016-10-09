<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Paises
 * 
 * Modelo dedicado a gestionar todo lo relacionado con las entidades bancarias
 * 
 * @package bancos
 * @author vane
 * @version 1.1.0
 */
class Model_entidades_bancarias extends CI_Model {

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
     * retorna todas los bancos de un pais
     * @access public
     * @return array Bancos
     */
    public function getBancos() {
        $this->load->database();
        $conexion = $this->db;
        $condiciones = array("cod_pais" => $this->idpais);
        return Ventidades_bancarias::listarEntidades_bancarias($conexion, $condiciones);

    }

}