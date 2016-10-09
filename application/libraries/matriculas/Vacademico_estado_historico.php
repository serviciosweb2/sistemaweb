<?php

/**
 * Class Vacademico_estado_historico
 *
 * Class  Vacademico_estado_historico maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage academico_estado_historico
 * @author   Vane
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vacademico_estado_historico extends Tacademico_estado_historico {

    private static $motivos = array(
        array("id" => 1, "motivo" => 'Inscripción a Materia.'),
        array("id" => 2, "motivo" => 'Regularidad vencida.'),
        array("id" => 3, "motivo" => 'pasaje de periodo'));

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    function getmotivos($index = false) {
        return $index !== false ? self :: $motivos[$index] : self ::$motivos;
    }

    public function guardar($estadoacademico, $codusuario, $estado, $codmotivo = null, $comentario = null) {
        $this->cod_estado_academico = $estadoacademico;
        $this->cod_usuario = $codusuario;
        $this->comentario = $comentario;
        $this->estado = $estado;
        $this->fecha_hora = date("Y-m-d H:i:s");
        $this->motivo = $codmotivo;
        $this->guardarAcademico_estado_historico();
    }

}
