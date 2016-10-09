<?php

/**
* Class Vtitulos
*
*Class  Vtitulos maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vtitulos extends Ttitulos{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    static public function getTitulos(CI_DB_mysqli_driver $conexion, $codFilial = null, $codTipoPeriodo = null, $codPlanAcademico = null, $modalidad = null){
        $conexion->select("general.titulos.*", false);
        $conexion->from("general.titulos");
        $conexion->join("general.planes_academicos_filiales", "planes_academicos_filiales.cod_titulo = general.titulos.codigo");
        if ($codFilial != null){
            $conexion->where("general.planes_academicos_filiales.cod_filial", $codFilial);
        }
        if ($codTipoPeriodo != null){
            $conexion->where("general.planes_academicos_filiales.cod_tipo_periodo", $codTipoPeriodo);
        }
        if ($codPlanAcademico != null){
            $conexion->where("general.planes_academicos_filiales.cod_plan_academico", $codPlanAcademico);
        }
        if ($modalidad != null){
            $conexion->where("general.planes_academicos_filiales.modalidad", $modalidad);
        }
        $conexion->order_by("general.titulos.nombre", "ASC");
        $conexion->group_by("general.titulos.codigo");
        $query = $conexion->get();
        return $query->result_array();
    }
    
}