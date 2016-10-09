<?php

/**
* Class Vcupones_landing
*
*Class  Vcupones_landing maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vcupones_landing extends Tcupones_landing{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    static public function listar(CI_DB_mysqli_driver $conexion, $idFilial = null, $fechaDesde = null, $fechaHasta = null, $codCurso = null, 
            array $order = null, $search = null, $limitInf = 0, $limitCant = null, $contar = false, array $camposSearch = null){
        if ($camposSearch == null){
            $camposSearch = array("nombre", "email", "telefono", "documento", "nombre_es", "nombre_pt", "nombre_in", "fecha_cupon", "fecha");
        }
        $conexion->select("publicidad.cupones_landing.*", false);
        $conexion->select("DATE_FORMAT(publicidad.cupones_landing.fecha, '%d/%m/%Y') AS fecha_cupon", false);
        $conexion->select("general.cursos.nombre_es");
        $conexion->select("general.cursos.nombre_pt");
        $conexion->select("general.cursos.nombre_in");        
        $conexion->from("publicidad.cupones_landing");
        $conexion->join("general.cursos", "general.cursos.codigo = publicidad.cupones_landing.id_curso");
        if ($idFilial != null){
            $conexion->where("publicidad.cupones_landing.id_filial", $idFilial);
        }
        if ($fechaDesde != null){
            $conexion->where("DATE(publicidad.cupones_landing.fecha) >=", $fechaDesde);
        }
        if ($fechaHasta != null){
            $conexion->where("DATE(publicidad.cupones_landing.fecha) <=", $fechaHasta);
        }
        if ($codCurso != null){
            $conexion->where("publicidad.cupones_landing.id_curso", $codCurso);
        }
        if ($search != null){
            $arrHaving = array();
            foreach ($camposSearch as $campo){
                $arrHaving[] = "$campo LIKE '%$search%'";
            }
            $conexion->having("(".implode(" OR ", $arrHaving).")");
        }
        
        if (!$contar){
            if ($order != null){
                $campoOrder = $order[0];
                $orderMethod = isset($order[1]) ? $order[1] : "ASC";
                $conexion->order_by($campoOrder, $orderMethod);
            }
            if ($limitCant !== null){
                $conexion->limit($limitCant, $limitInf);
            }
            $query = $conexion->get();
            return $query->result_array();
        } else {
            $query = $conexion->get();
            return $query->num_rows();
        }        
    }    
}