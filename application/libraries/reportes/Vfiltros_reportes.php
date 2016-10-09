<?php

/**
* Class Vfiltros_reportes
*
*Class  Vfiltros_reportes maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vfiltros_reportes extends Tfiltros_reportes{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    public function setDefault($codigoUsuario){
        $this->oConnection->where("codigo_usuario", $codigoUsuario);
        $values = array("default" => 0);
        $resp = $this->oConnection->update("filtros_reportes", $values);
        $this->oConnection->where("codigo", $this->codigo);
        $values = array("default" => 1);
        return $resp && $this->oConnection->update("filtros_reportes", $values);
    }
    
    public function remove(){
        return $this->oConnection->delete("filtros_reportes", array("codigo" => $this->codigo));
    }
}

?>