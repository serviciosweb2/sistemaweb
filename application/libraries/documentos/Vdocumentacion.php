<?php

/**
* Class Vdocumentacion
*
*Class  Vdocumentacion maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vdocumentacion extends Tdocumentacion{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    public static function getDocumentacionPlan($conexion, $filial, $plan){
        $conexion->select("id_documentacion");
        $conexion->from("general.documentacion_planes");
        $conexion->where("id_filial",$filial);
        $conexion->where("id_plan",$plan);
        $query = $conexion->get();
        return $query->result_array();
    }
}
