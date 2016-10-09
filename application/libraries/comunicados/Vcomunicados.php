<?php

/**
* Class Vcomunicados
*
*Class  Vcomunicados maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vcomunicados extends Tcomunicados{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    static public function listar(CI_DB_mysqli_driver $conexion, array $seach = null, $limitInf = 0, $limitCant = null, 
            array $orderBy = null, $contar = false, $fechaDesde = null, $fechaHasta = null, $estado = null, $codFilial = null){
        $conexion->select("general.comunicados.*", false);
        if ($codFilial != null){
            $conexion->select("general.comunicados_imagenes.url",false);    
        }
        $conexion->select("DATE_FORMAT(general.comunicados.fecha_creacion, '%d/%m/%Y') AS fecha", false);
        $conexion->from("general.comunicados");
        if ($codFilial != null){
            $conexion->join("general.comunicados_filiales", "general.comunicados_filiales.id_comunicado = general.comunicados.id AND general.comunicados_filiales.id_filial = $codFilial");
        }
        if ($codFilial != null){
            $conexion->join("general.comunicados_imagenes", "general.comunicados_imagenes.id_comunicado = general.comunicados.id", 'LEFT');
        }
        if ($fechaDesde != null){
            $conexion->where("DATE(general.comunicados.fecha_creacion) >=", $fechaDesde);
        }
        if ($fechaHasta != null){
            $conexion->where("DATE(general.comunicados.fecha_creacion) <=", $fechaHasta);
        }
        if ($estado != null){
            $conexion->where("general.comunicados.estado", $estado);
        }
        if ($limitCant != null){
            $conexion->limit($limitCant, $limitInf);
        }
        if ($orderBy != null && count($orderBy) > 0){
            $asc = isset($orderBy[1]) ? $orderBy[1] : 'ASC';
            $conexion->order_by($orderBy[0], $asc);
        }
        if ($seach != null && count($seach) > 0){
            $arrTemp = array();
            foreach ($seach as $field => $value){
                $arrTemp[] = "$field LIKE '%$value%'";
            }
            $having = "(" . implode(" OR ", $arrTemp) . ")";
            $conexion->having($having);
        }
        $query = $conexion->get();
        if ($contar) {
            $arrResp = $query->num_rows();
        } else {
            $arrResp = $query->result_array();
        }
        return $arrResp;
    }
    
    public function setFiliales(array $arrFiliales){
        $this->oConnection->where("id_comunicado", $this->id);
        $resp = $this->oConnection->delete("general.comunicados_filiales");
        foreach ($arrFiliales as $filial){
            $resp = $resp && $this->oConnection->insert("general.comunicados_filiales", 
                    array("id_comunicado" => $this->id, "id_filial" => $filial));
        }
        return $resp;
    }
    
    public function getFiliales(){
        $this->oConnection->select("id_filial");
        $this->oConnection->from("general.comunicados_filiales");
        $this->oConnection->where("id_comunicado", $this->id);
        $query = $this->oConnection->get();
        return $query->result_array();
    }
    
    public function getImagenes(){
        $this->oConnection->select("url");
        $this->oConnection->from("general.comunicados_imagenes");
        $this->oConnection->where("id_comunicado", $this->id);
        $query = $this->oConnection->get();
        return $query->result_array();
    }
    
    public function setImagenes(array $imagenes){
        $resp = $this->clear_imagenes();
        foreach ($imagenes as $imagen){
            $resp = $resp && $this->oConnection->insert("general.comunicados_imagenes", array("id_comunicado" => $this->id, "url" => $imagen));
        }
        return $resp;
    }
    
    public function clear_imagenes(){
        $this->oConnection->where("id_comunicado", $this->id);
        return $this->oConnection->delete("general.comunicados_imagenes");
    }
    
}