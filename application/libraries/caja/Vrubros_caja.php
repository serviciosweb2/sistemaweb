<?php

/**
* Class Vrubros_caja
*
*Class  Vrubros_caja maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vrubros_caja extends Trubros_caja{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }
    
    static public function getRubros(CI_DB_mysqli_driver $conexion)
    {
        $conexion->select("rubros_caja.rubro, rubros_caja.codigo", false);
        $conexion->from("rubros_caja");
        $conexion->group_by("rubros_caja.rubro");
        $query = $conexion->get();
        return $query->result_array();
    }
    
    static public function getSubRubros(CI_DB_mysqli_driver $conexion, $rubro = false)
    {
        $conexion->select("rubros_caja.*", false);
        $conexion->from("rubros_caja");
        $conexion->where("rubros_caja.baja = 0");
        
        if($rubro)
        {
            $conexion->where("rubros_caja.rubro = '".$rubro."'");
        }
        $query = $conexion->get();
        return $query->result_array();
    }
           
    
}

?>