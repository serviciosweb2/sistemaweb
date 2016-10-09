<?php

/**
 * Created by PhpStorm.
 * User: nailson
 * Date: 09/08/16
 * Time: 17:08
 */
class VmorasCursosCortos extends TmorasCursosCortos
{
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }
}