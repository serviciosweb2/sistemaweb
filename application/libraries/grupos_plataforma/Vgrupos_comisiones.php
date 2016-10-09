<?php

/**
 * Created by PhpStorm.
 * User: damian
 * Date: 24/08/16
 * Time: 16:17
 */
class Vgrupos_comisiones extends Tgrupos_comisiones {
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

}