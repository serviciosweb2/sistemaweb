<?php

/**
 * Class Vcertificados_estado_historico
 *
 * Class  Vcertificados_estado_historico maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vcertificados_estado_historico extends Tcertificados_estado_historico {

    private static $motivos = array(
        array("id" => 1, "motivo" => 'no_cumple_requerimientos', "visible" => false),
        array("id" => 2, "motivo" => 'cumple_requerimientos', "visible" => false),
        array("id" => 3, "motivo" => 'reimpresion_certificado', "visible" => false),
        array("id" => 4, "motivo" => 'revision_certificado', "visible" => false),
        array("id" => 5, "motivo" => 'inhabilita_matricula', "visible" => false),
        array("id" => 6, "motivo" => 'rehabilita_matricula', "visible" => false),
        array("id" => 7, "motivo" => 'inhabilitado_por_usuario', "visible" => false),
        array("id" => 8, "motivo" => 'migracion', "visible" => false),
        array("id" => 9, "motivo" => "reinsercion", "visible" => false));

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    static function getmotivos($id = null) {
        $retorno = array();
        if ($id !== null) {
            foreach (self :: $motivos as $rowmotivo) {
                if ($rowmotivo['id'] == $id) {
                    return $rowmotivo;
                }
            }
            return array();
        } else {
            return self :: $motivos;
        }
    }

    function guardar($codmatriculaperiodo, $codcertificante, $estado, $codusuario = null, $fechahora = null, $motivo = null, $comentario = null) {
        $this->cod_matricula_periodo = $codmatriculaperiodo;
        $this->cod_certificante = $codcertificante;
        $this->motivo = $motivo;
        $this->cod_usuario = $codusuario;
        $this->estado = $estado;
        $this->fecha_hora = $fechahora != null ? $fechahora : date('Y-m-d H:m:i');
        $this->comentario = $comentario;
        return $this->guardarCertificados_estado_historico();
    }

}

?>