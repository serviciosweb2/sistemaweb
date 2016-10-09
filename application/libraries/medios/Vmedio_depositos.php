<?php

/**
 * Description of Vmedio_depositos
 *
 * @author Vane
 */
class Vmedio_depositos extends Tmedio_depositos {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

}
