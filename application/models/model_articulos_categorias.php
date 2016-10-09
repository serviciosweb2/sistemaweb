<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Model_articulos_categorias extends CI_Model {

    var $codigo_filial = 0;

    public function __construct($arg) {
        parent::__construct();
        $this->codigo_filial = $arg["codigo_filial"];
    }

    public function getCategorias() {
        $conexion = $this->load->database($this->codigo_filial, true);
        return Varticulos_categorias::getCategorias($conexion);
    }

    public function getArticulos($codcategoria) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $condiciones = array('estado' => 'habilitado',
            'cod_categoria' => $codcategoria);
        $articulos = Varticulos::listarArticulos($conexion, $condiciones);
        return $articulos;
    }

}
