<?php

/**
 * Class Vferiados_estado_historico
 *
 * Class  Vferiados_estado_historico maneja...
 *
 * @package  SistemaIGA
 * @subpackage feriados_estado_historico
 * @author   Vane
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vferiados_estado_historico extends Tferiados_estado_historico {

    private static $motivos = array(
        array("id" => 1, "motivo" => ''),
        array("id" => 2, "motivo" => ''));

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    function getmotivos($index = false) {
        return $index !== false ? self :: $motivos[$index] : self ::$motivos;
    }


}