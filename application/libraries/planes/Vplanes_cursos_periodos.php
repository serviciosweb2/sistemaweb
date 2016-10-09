<?php

/**
 * Class Vplanes_cursos_periodos
 *
 * Class  Vplanes_cursos_periodos  maneja todos los aspectos de los planes asignados a los peridos de los cursos
 *
 * @package  SistemaIGA
 * @subpackage Vplanes_cursos_periodos
 * @author   vane
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vplanes_cursos_periodos extends Tplanes_cursos_periodos {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    function guardar($codplan, $codcurso, $codperiodo) {
        $this->cod_plan_pago = $codplan;
        $this->cod_curso = $codcurso;
        $this->cod_tipo_periodo = $codperiodo;
        return $this->guardarPlanes_cursos_periodos();
    }

}
