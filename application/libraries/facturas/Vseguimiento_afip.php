<?php

/**
 * Class Vseguimiento_afip
 *
 * Class  Vseguimiento_afip maneja todos los aspectos de seguimiento_afip
 *
 * @package  SistemaIGA
 * @author  Foox
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vseguimiento_afip extends Tseguimiento_afip {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }
}
