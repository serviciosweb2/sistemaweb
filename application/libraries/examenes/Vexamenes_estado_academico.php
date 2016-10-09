<?php

/**
 * Class Vexamenes_estado_academico
 *
 * Class  Vexamenes_estado_academico maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vexamenes_estado_academico extends Texamenes_estado_academico {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    public function setCambioEstado($estado, $cod_materia, $cod_estado_academico) {
        $this->oConnection->where('estadoacademico.codigo', $cod_estado_academico);
        $this->oConnection->where('estadoacademico.codmateria', $cod_materia);
        $this->oConnection->update('estadoacademico', array('estado' => $estado));
    }

    public function cambiarEstadoExamen($cod_examen, $cod_estado_academico, $estado) {

        $this->oConnection->where('examenes_estado_academico.cod_examen', $cod_examen);
        $this->oConnection->where('examenes_estado_academico.cod_estado_academico', $cod_estado_academico);
        $this->oConnection->where('examenes_estado_academico.estado <>','baja');
        $this->oConnection->update('examenes_estado_academico', array('estado' => $estado));
    }

    public function setBajaInscripcionExamen($cod_estado_academico, $estado) {
        $this->oConnection->where('examenes_estado_academico.codigo', $this->codigo);
        $this->oConnection->where('examenes_estado_academico.cod_estado_academico', $cod_estado_academico);
        $this->oConnection->update('examenes_estado_academico', array('estado' => $estado));
    }

    public function getNotas() {
        $this->oConnection->select('examenes_estado_academico.cod_examen, notas_resultados.tipo_resultado, notas_resultados.nota');
        $this->oConnection->from('examenes_estado_academico');
        $this->oConnection->join('notas_resultados', 'notas_resultados.cod_inscripcion = examenes_estado_academico.codigo');
        $this->oConnection->where('examenes_estado_academico.codigo', $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

}
