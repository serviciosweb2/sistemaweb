<?php

class Ttipos_razones_sociales{

    public $id;
    public $nombre;
    private $oConnection;
    private $nombreTabla = 'tipos_razones_sociales';

    /* CONSTRUCTOR */

    function __construct(CI_DB_mysqli_driver $connection, $id = null){
        $this->oConnection = $connection;
        if ($id != null){
            $arrConstructor = $this->_constructor($id);
            if (count($arrConstructor) > 0){
                $this->id = $arrConstructor[0]['id'];
                $this->nombre = $arrConstructor[0]['nombre'];
            } else {
                $this->id = -1;
            }
        } else {
            $this->id = -1;
        }
    }

    /* PRIVATE FUNCTIONS */

    private function _getArrayDeObjeto(){
        $arrTemp = array();
        $arrTemp['nombre'] = $this->nombre;
        return $arrTemp;
    }

    private function _insertar(){
        if ($this->oConnection->insertar($this->nombreTabla, $this->_getArrayDeObjeto())){
            $this->id = $this->oConnection->insert_id();
            return true;
        } else {
            return false;
        }
    }

    private function _actualizar(){
        return $this->oConnection->actualizar($this->nombreTabla, $this->_getArrayDeObjeto(), "id = $this->id");
    }

    private function _constructor($id){
        $query = $this->oConnection->select('*')
            ->from($this->nombreTabla)
            ->where(array(
                'id' => "$id"
            ))->get();
        $arrResp = $query->result_array();
        return $arrResp;
    }

    /* PUBLIC FUNCTIONS */

    public function guardar(){
        if ($this->id == '' || $this->id < 1){
            return $this->_insertar();
        } else {
            return $this->_actualizar();
        }
    }

    /* STATIC FUNCTIONS */

    static function campos(CI_DB_mysqli_driver $connection){
        $query = $connection->field_data('tipos_razones_sociales');
        $arrResp = array();
        foreach ($query as $filed){
            $arrResp[] = $filed->name;
        }
        return $arrResp;
    }

    static function listar(CI_DB_mysqli_driver $connection, $id = null){
        $condiciones = array();
        if ($id != null) $condiciones["id"] = "$id";
        $query = $connection->select('*')
            ->from('tipos_razones_sociales')
            ->where($condiciones)->get();
        $arrResp = $query->result_array();
        return $arrResp;
    }

}
