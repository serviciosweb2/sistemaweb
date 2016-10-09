<?php

/**
* Class Vclases
*
*Class  Vclases maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vclases extends Tclases{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    public function getVideos(){
        return Vvideos::listar($this->oConnection, array("material_didactico.videos.id" => $this->id));
    }
    
    static function listar(CI_DB_mysqli_driver $conexion, $cod_clase = null){
        $conexion->select("general.clases.*", false);
        $conexion->select("general.filiales.nombre AS filial_nombre", false);
        $conexion->select("general.planes_academicos.nombre AS plan_academico_nombre", false);
        $conexion->select("general.cursos.nombre_es AS curso_nombre_es", false);
        $conexion->select("general.cursos.nombre_pt AS curso_nombre_pt", false);
        $conexion->select("general.cursos.nombre_in AS curso_nombre_in", false);
        $conexion->select("general.materias.nombre_es AS materia_nombre_es", false);
        $conexion->select("general.materias.nombre_pt AS materia_nombre_pt", false);
        $conexion->select("general.materias.nombre_in AS materia_nombre_in", false);
        $conexion->from("general.clases");
        $conexion->join("general.planes_academicos", "general.planes_academicos.codigo = general.clases.id_plan_academico");
        $conexion->join("general.cursos", "general.cursos.codigo = general.planes_academicos.cod_curso");
        $conexion->join("general.materias", "general.materias.codigo = general.clases.id_materia");
        $conexion->join("general.filiales", "general.filiales.codigo = clases.id_filial");
        if ($cod_clase){
            $tipo = is_array($cod_clase) ? "where_in" : "where";
            $conexion->$tipo("general.clases.id", $cod_clase);
        }
        $query = $conexion->get();
        return $query->result_array();        
    }
    
    public function inhabilitar_materiales(array $codMateriales){
        $resp = true;
        foreach ($codMateriales as $material){
            $this->oConnection->where("id", $material);
            $this->oConnection->where("id_clase", $this->id);
            $resp = $resp && $this->oConnection->update("material_didactico.materiales_didacticos", array("estado" => "inhabilitada"));
        }
        return $resp;
    }
    static public function inhabilitarClases(CI_DB_mysqli_driver $conexion, $codFilial, $codPlanAcademico, $modalidad, $materia){
        $conexion->where("id_filial", $codFilial);
        $conexion->where("id_plan_academico", $codPlanAcademico);
        $conexion->where("id_materia", $materia);
        $conexion->where("modalidad", $modalidad);
        return $conexion->update("general.clases", array("estado" => "inhabilitada"));
    }
}