<?php

/**
* Class Vpaises
*
*Class  Vpaises maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vpaises extends Tpaises{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    public function getCiudades(){
        $this->oConnection->select("general.localidades.*", false);
        $this->oConnection->select("provincias.nombre as nombre_provincia");
        $this->oConnection->from("general.provincias");
        $this->oConnection->join("general.localidades", "general.localidades.provincia_id = general.provincias.id");
        $this->oConnection->where("general.provincias.pais", $this->id);
        $query = $this->oConnection->get();
        return $query->result_array();
    }
    
    public function insertSincronizacion() {
        $arrTemp = array();
        $arrTemp = $this->_getArrayDeObjeto();
        $primary = $this->primaryKey;
        $arrTemp[$primary] = $this->$primary;

        if ($this->oConnection->insert($this->nombreTabla, $arrTemp)) {
            return true;
        } else {
            return false;
        }
    }

    public function updateSincronizacion() {
        $arrTemp = array();
        $arrTemp = $this->_getArrayDeObjeto();
        $primary = $this->primaryKey;
        $primaryVal = $this->$primary;
        return $this->oConnection->update($this->nombreTabla, $arrTemp, "$primary = $primaryVal");
    }
    
    public function getAniosMayoriaEdad(){
        $respuesta = 18;
        switch ($this->id) {            // ir cambiando las edades segun corresponda
            case 1:                     // Argentina
                $respuesta = 18;
                break;            
            case 2:                     // Brasil
                $respuesta = 16;
                break;
            case 3:                     // Uruguay
                $respuesta = 18;
                break;
            case 4:                     // Paraguay
                $respuesta = 18;
                break;
            case 5:                     // Venezuela
                $respuesta = 18;
                break;
            case 6:                     // Bolivia
                $respuesta = 18;
                break;
            case 7:                     // Chile
                $respuesta = 18;
                break;
            case 8:                     // Colombia
                $respuesta = 18;
                break;
            case 9:                     // Panamá
                $respuesta = 18;
                break;
            case 10:                     // Estados Unidos
                $respuesta = 18;
                break;
            
            default:
                $respuesta = 18;
                break;
        }
        return $respuesta;
    }
    
    static public function getDocumentoDefaultPais($codPais){
        switch ($codPais) {
            case 1:
                $documento = 1;
                break;
            case 2:
                $documento = 21;
                break;
            case 3:
                $documento = 7;
                break;            
            case 4:
                $documento = 8;
                break;
            case 5:
                $documento = 9;
                break;
            case 6:
                $documento = 10;
                break;
            case 7:
                $documento = 17;
                break;
            case 8:
                $documento = 18;
                break;            
            case 9:
                $documento = 11;
                break;
            case 10:
                $documento = 20;
                break;
            default:
                $documento = 12;
                break;
        }
        return $documento;
    }
    
}

