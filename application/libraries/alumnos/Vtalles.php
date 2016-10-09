<?php

/**
* Class Vtalles
*
*Class  Vtalles maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vtalles extends Ttalles{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }
    
    static function getTallesPais(CI_DB_mysqli_driver $conexion, $pais){
        $conexion->select('td.id_talle');
        $conexion->from('general.talles_descripcion td');
        $conexion->where('td.propiedad','pais');
        $conexion->where('td.valor',$pais);
        $subquery = $conexion->return_query();
        $conexion->resetear();
        
        $conexion->select('general.talles.talle');
        $conexion->from('general.talles');
        $conexion->where('general.talles.codigo = talles_descripcion.id_talle');
        $conexion->order_by('general.talles.talle','desc');
        $subquery2 = $conexion->return_query();
        $conexion->resetear();
        
        $conexion->select('general.talles_descripcion.*',false);
        $conexion->select("($subquery2) as talle",false);
        $conexion->from('general.talles_descripcion');
        $conexion->join('general.talles','general.talles.codigo = general.talles_descripcion.id_talle');
        $conexion->where("general.talles_descripcion.id_talle IN ($subquery)");
        $conexion->order_by('general.talles.talle','desc');
//        $conexion->order_by('general.talles_descripcion.propiedad','desc');
        $query = $conexion->get();
//        echo $conexion->last_query();
        return $query->result_array();
    }

}

