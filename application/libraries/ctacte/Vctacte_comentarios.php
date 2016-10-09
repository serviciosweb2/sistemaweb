<?php

/**
 * Class Vctacte_comentarios
 *
 * Class  Vctacte_comentarios administra los comentarios realizados a la cuenta corriente
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Vane
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vctacte_comentarios extends Tctacte_comentarios {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

}
