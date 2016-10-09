<?php

/**
 * Model_planes_cursos_periodos
 * 
 * Planes de pago de cursos.
 * 
 * @package model_planes_pagos
 * @author ivan <ivan.sys@gmail.com>
 * @version 1.0.0
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_planes_cursos_periodos extends CI_Model {

    var $codigo_filial = 0;

    public function __construct($arg) {
        parent::__construct();

        $this->codigo_filial = $arg["codigo_filial"];
    }

    public function getPlanesCursosPeriodos($codPlan) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $condiciones = array('cod_plan_pago' => $codPlan);
        $cursosPlan = Vplanes_cursos_periodos::listarPlanes_cursos_periodos($conexion, $condiciones);
        return $cursosPlan;
    }

}
