<?php

/**
* Class Vvideos
*
*Class  Vvideos maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vvideos extends Tvideos{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    public function addPropiedad(array $arrKeyValue){
        $resp = true;
        foreach ($arrKeyValue as $key => $value){
            $this->oConnection->where("id_video", $this->id);
            $this->oConnection->where("propiedad", $key);
            $resp = $resp && $this->oConnection->delete("material_didactico.videos_propiedades");
            $arrInsert = array(
                "id_video" => $this->id,
                "propiedad" => $key,
                "valor" => $value
            );
            $resp = $resp && $this->oConnection->insert("material_didactico.videos_propiedades", $arrInsert);
        }
        return $resp;
    }
    
    public function getPropiedades(array $condiciones = null)
    {
        $conexion = $this->oConnection;
        $conexion->select('material_didactico.videos_propiedades.propiedad, material_didactico.videos_propiedades.valor');
        $conexion->from('material_didactico.videos_propiedades');
        $conexion->where('material_didactico.videos_propiedades.id_video', $this->getCodigo());
        if($condiciones != null)
        {
            $conexion->where($condiciones);
        }
        $query = $conexion->get();
        return $query->result_array();
    }
    
    static public function listar(CI_DB_mysqli_driver $conexion, array $condiciones = null){
        $conexion->select("material_didactico.videos_propiedades.valor");
        $conexion->from("material_didactico.videos_propiedades");
        $conexion->where("material_didactico.videos_propiedades.id_video = videos.id");
        $conexion->where("material_didactico.videos_propiedades.propiedad", "evento_id");
        $sqEvento = $conexion->return_query();
        $conexion->resetear();
        $conexion->select("videos.*");
        $conexion->select("material_didactico.materiales_didacticos.id AS id_material_didactico");
        $conexion->select("material_didactico.materiales_didacticos.estado AS estado_material_didactico");
        $conexion->select("($sqEvento) AS evento_id", false);
        $conexion->from("material_didactico.materiales_didacticos");
        $conexion->join("material_didactico.videos", "material_didactico.videos.id = material_didactico.materiales_didacticos.id_material");
        $conexion->where("material_didactico.materiales_didacticos.tipo", "video");
        $conexion->where("material_didactico.videos.estado", "habilitada");
//        $conexion->where("material_didactico.materiales_didacticos.estado", "habilitada");
        
        if($condiciones != null)
        {
            $conexion->where($condiciones);
        }
        $conexion->group_by("material_didactico.videos.id");
        $query = $conexion->get();
        //die($conexion->last_query());
        return $query->result_array();        
    }
    
    static function listarVideos(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "material_didactico.videos", $condiciones, $limite, $orden, $grupo, $contar);
    }
}