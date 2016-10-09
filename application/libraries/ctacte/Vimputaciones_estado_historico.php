<?php

class Vimputaciones_estado_historico extends Timputaciones_estado_historico {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

}

