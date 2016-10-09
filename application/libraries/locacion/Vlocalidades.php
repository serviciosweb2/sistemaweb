<?php

/**
* Class Vlocalidades
*
*Class  Vlocalidades maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vlocalidades extends Tlocalidades{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }
    
    public function getProvinciaLocalidad(){
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
    
    public function get_codigo_municipio(){
        $this->oConnection->select("valor");
        $this->oConnection->from("general.localidades_propiedades");
        $this->oConnection->where("cod_localidad", $this->id);
        $this->oConnection->where("propiedad", "codigo_municipio");
        $query = $this->oConnection->get();
        $resp = $query->result_array();
        if (count($resp) > 0){
            return $resp[0]['valor'];
        } else {
            return null;
        }
    }
    
    public function get_codigo_siafi(){
        $this->oConnection->select("valor");
        $this->oConnection->from("general.localidades_propiedades");
        $this->oConnection->where("cod_localidad", $this->codigo);
        $this->oConnection->where("valor", "codigo_siafi");
        $query = $this->oConnection->get();
        $resp = $query->result_array();
        if (count($resp) > 0){
            return $resp[0]['valor'];
        } else {
            return null;
        }
    }

}

