<?php

/**
* Class Varticulos_categorias
*
*Class  Varticulos_categorias maneja todos los aspectos de categorias
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Vane
* @version  $Revision: 1.1 $
* @access   private
*/
class Varticulos_categorias extends Tarticulos_categorias{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    static function getCategorias(CI_DB_mysqli_driver $conexion) {
        $conexion->select('articulos_categorias.*, (select nombre from articulos_categorias as t2 where articulos_categorias.cod_padre = t2.codigo) as nombrepadre');
        $conexion->from('articulos_categorias');
        $query = $conexion->get();
        $result = $query->result_array();
        return $result;
    }
}