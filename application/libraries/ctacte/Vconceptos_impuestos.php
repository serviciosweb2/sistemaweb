<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Vconceptos_impuestos extends Tconceptos_impuestos{
     function __construct(CI_DB_mysqli_driver $conexion,$cod_concepto, $cod_impuesto) {
        parent::__construct($conexion,$cod_concepto, $cod_impuesto);
    }
}


?>
