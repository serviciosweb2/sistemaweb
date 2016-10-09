<?php

/**
* Class Vsecciones
*
*Class  Vsecciones maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vsecciones extends Tsecciones{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }
    
    
    static function getCategorias($conexion){
        $conexion->select('general.secciones.categoria');
        $conexion->from('general.secciones');
        $conexion->where("general.secciones.categoria <> ''");
        $conexion->group_by('general.secciones.categoria');
        $query = $conexion->get();
        return $query->result_array();
    }
    
    static function getPadres(CI_DB_mysqli_driver $conexion, $wherein){
        $conexion->select('general.secciones.codigo, general.secciones.id_seccion_padre, general.secciones.id_atajo');
        $conexion->from('general.secciones');
        $conexion->where_in('general.secciones.codigo',$wherein);
        $query = $conexion->get();
        return $query->result_array();
    }
    
    static function getGrupo(CI_DB_mysqli_driver $conexion, $grupo = '')
    {
        $conexion->select('general.secciones.slug');
        $conexion->from('general.secciones');
        $conexion->where('general.secciones.grupo',$grupo);
        $query = $conexion->get();
        return $query->result_array();
    }
  
}

