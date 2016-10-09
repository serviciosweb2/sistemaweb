<?php

/**
 * Description of Vmedio_transferencias
 *
 * @author Vane
 */
class Vmedio_transferencias extends Tmedio_transferencias {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

}
