<?php

/**
 * Model_materias
 * 
 * Description...
 * 
 * @package model_materias
 * @author ivan <ivan.sys@gmail.com>
 * @version 1.0.0
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_materias extends CI_Model {

    var $codigo = 0;
    var $codigo_filial = 0;

    public function __construct($arg) {
        parent::__construct();
        $this->codigo = isset($arg["codigo"]) ? $arg["codigo"] : 0;
        $this->codigo_filial = $arg["codigo_filial"];
    }

    public function getMaterias() {
        $conexion = $this->load->database($this->codigo_filial, true);
        return Vmaterias::listarMaterias($conexion);
    }

    public function getMateriasCurso($cod_curso) {
        $conexion = $this->load->database($this->codigo_filial, TRUE);
        $materias = new Vmaterias($conexion);
        $materiasCurso = $materias->getMateriasCurso($cod_curso);
        return $materiasCurso;
    }

    public function getMateriasconHorarios() {
        $conexion = $this->load->database($this->codigo_filial, true);

        $materias = Vmaterias::getMateriasconHorarios($conexion);
        foreach ($materias as $key => $materia) {
            $materias[$key]['nombre'] = $materia['nombre_' . get_idioma()];
        }
        return $materias;
    }
    
    public function getMateriaExamen($cod_materia){
        $conexion = $this->load->database($this->codigo_filial,true);
        $myMateria = new Vmaterias($conexion,$cod_materia);
        return $myMateria;
    }

}

/* End of file model_materias.php */
/* Location: ./application/models/model_materias.php */