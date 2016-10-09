<?php

/**
 * Class Vmaterias
 *
 * Class  Vmaterias maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vmaterias extends Tmaterias {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    function getHorarios($disponibles = 1) {
        $this->oConnection->select("*");
        $this->oConnection->from("horarios");
        $this->oConnection->where("cod_materia", $this->codigo);
        if ($disponibles == 1) {
            $this->oConnection->where("baja", 0);
        }
        $this->oConnection->group_by(array("cod_materia", "cod_comision"));
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getMateriasCurso($cod_curso) {
        $this->oConnection->select('general.materias.codigo, general.materias.nombre_es, general.materias.nombre_in, general.materias.nombre_pt');
        $this->oConnection->from('general.cursos');
        $this->oConnection->join('general.materias_curso', 'general.materias_curso.cod_curso = general.cursos.codigo');
        $this->oConnection->join('general.materias', 'general.materias.codigo = general.materias_curso.cod_materia');
        $this->oConnection->where('general.cursos.codigo', $cod_curso);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    static function getMateriasconHorarios(CI_DB_mysqli_driver $conexion) {
        $conexion->select('general.materias.*', FALSE);
        $conexion->from('general.materias');
        $conexion->join('horarios', 'general.materias.codigo = horarios.cod_materia');
        $conexion->where('horarios.baja', 0);
        $conexion->order_by('general.materias.nombre_'.get_idioma(), 'asc');
        $conexion->group_by('general.materias.codigo');
        $query = $conexion->get();

        return$query->result_array();
    }
    
    public function insertSincronizacion() {
        $arrTemp = array();
        $arrTemp = $this->_getArrayDeObjeto();
        $primary = $this->primaryKey;
        $arrTemp[$primary] = $this->$primary;

        if ($this->oConnection->insert($this->nombreTabla, $arrTemp)) {
            return true;
        } else {
            return false;
        }
    }

    public function updateSincronizacion() {
        $arrTemp = array();
        $arrTemp = $this->_getArrayDeObjeto();
        $primary = $this->primaryKey;
        $primaryVal = $this->$primary;
        return $this->oConnection->update($this->nombreTabla, $arrTemp, "$primary = $primaryVal");
    }

    public function getGrupo($cod_plan_academico){
        $conexion = $this->oConnection;
        $conexion->select('grupo');
        $conexion->from('general.materias_plan_academico');
        $conexion->where('cod_plan = '.$cod_plan_academico .'AND cod_materia ='. $this->codigo);
        $query = $conexion->get();
        return $query->result_array();
    }





}

