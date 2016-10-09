<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Model_certificados_plan_filial
 * 
 * ...
 * 
 * @package Model_certificados_plan_filial
 * @author vane
 * @version 1.0.0
 */
class Model_certificados_plan_filial extends CI_Model {

    var $codigo_filial = 0;

    public function __construct($arg) {
        parent::__construct();
        $this->codigo_filial = $arg["codigo_filial"];
    }

    public function getCertificadosCertificar($codalumno) {
        $conexion = $this->load->database($this->codigo_filial, TRUE);

        $objalumno = new Valumnos($conexion, $codalumno);
        $planes = $objalumno->getCertificadosCertificar($this->codigo_filial);

        for ($i = 0; $i < count($planes); $i++) {
            $plan = new Vplanes_academicos($conexion, $planes[$i]['cod_plan_academico']);
            $planes[$i]['descripcion'] = $planes[$i]['nombre'] . ': ' . lang($planes[$i]['titulo']);

            $planperiodos = $plan->getPeriodos();
            if (count($planperiodos) > 1) {
                $periodos = new Vtipos_periodos($conexion);
                $nombrePeriodo = lang($periodos->getNombre($conexion, $planes[$i]['cod_tipo_periodo']));
                $planes[$i]['descripcion'].= ' (' . $nombrePeriodo . ')';
            }

            //si tiene mas de un plan el curso mostrar plan
            $curso = new Vcursos($conexion, $plan->cod_curso);
            $condplanes = array('cod_curso' => $curso->getCodigo());
            $planescurso = Vplanes_academicos::listarPlanes_academicos($conexion, $condplanes);
            if (count($planescurso) > 1) {
                $planes[$i]['descripcion'].=' / ' . $plan->nombre;
            }
        }
        return $planes;
    }

}
