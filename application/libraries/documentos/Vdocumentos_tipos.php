<?php

/**
* Class Vdocumentos_tipos
*
*Class  Vdocumentos_tipos maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vdocumentos_tipos extends Tdocumentos_tipos{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    public function getCondicionesSociales(){
        $this->oConnection->select("general.condiciones_sociales.*", false);
        $this->oConnection->from("general.condiciones_sociales_documentos_tipos");
        $this->oConnection->join("general.condiciones_sociales", "general.condiciones_sociales.codigo = general.condiciones_sociales_documentos_tipos.cod_condicion_social");
        $this->oConnection->where("general.condiciones_sociales_documentos_tipos.cod_documento_tipo", $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }
}