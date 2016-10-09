<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Model_comprobantes extends CI_Model{
    var $codigo_filial = 0;
    
    public function __construct($arg) {
        parent::__construct();
        $this->codigo_filial = $arg["codigo_filial"];
    }
    
    public function getComprobates($id_pais){
        $conexion = $this->load->database($this->codigo_filial,true);
        $condiciones = array(
            'id_pais'=>$id_pais
        );
        $comprobantes = Vcomprobantes::listarComprobantes($conexion, $condiciones);
        return $comprobantes;
    }
}

