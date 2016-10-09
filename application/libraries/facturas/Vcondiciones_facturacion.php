<?php

/**
 * Class Vcondiciones_facturacion
 *
 * Class  Vcondiciones_facturacion maneja todos los aspectos de condiciones_facturacion
 *
 * @package  SistemaIGA
 * @subpackage condiciones_facturacion
 * @author   Vane
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vcondiciones_facturacion extends Tcondiciones_facturacion {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

}