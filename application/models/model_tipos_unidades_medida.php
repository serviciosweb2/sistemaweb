<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Model_tipos_unidades_medida extends CI_Model {

    var $codigo_filial = 0;

    public function __construct($arg) {
        parent::__construct();
        $this->codigo_filial = $arg["codigo_filial"];
    }

    public function getUnidades() {
        $conexion = $this->load->database($this->codigo_filial, true);

        $unidades = Vtipos_unidades_medida::listarTipo_unidades_medida($conexion);
        
        return $unidades;
    }

}
