<?php

/**
 * Created by PhpStorm.
 * User: damian
 * Date: 24/08/16
 * Time: 16:16
 */
class Vgrupos extends Tgrupos {
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

}