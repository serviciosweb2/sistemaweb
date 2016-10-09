<?php

/**
 * Description of Vmedio_tarjeta
 *
 * @author Vane
 */
class Vmedio_debito extends Tmedio_debito {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

}
