<?php

/**
 * Class Vctacte_otros
 *
 * Class  Vctacte_otros maneja ...
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Vane
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vctacte_otros extends Tctacte_otros {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    public function guardar($cod_concepto, $cod_usuario = null, $fecha_hora = null) {
        $this->cod_concepto = $cod_concepto;
        $this->fecha_hora = $fecha_hora == null ? date('Y-m-d H:m:i') : $fecha_hora;
        $this->cod_usuario = $cod_usuario;
        $this->guardarCtacte_otros();
    }

}
