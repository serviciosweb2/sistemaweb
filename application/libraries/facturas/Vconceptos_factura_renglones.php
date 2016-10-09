<?php

/**
* Class Vconceptos_factura_renglones
*
*Class  Vconceptos_factura_renglones maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vconceptos_factura_renglones extends Tconceptos_factura_renglones{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

}

