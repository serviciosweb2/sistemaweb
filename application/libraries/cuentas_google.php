<?php

class cuentas_google{
    
    private $id_filial;
    public $user;
    public $pass;
    public $baja;
    
    private $cuenta_exists = false;
    
    static private $tableName = "filiales_cuentas_google";
    
    /* FUNCTION CONSTRUCT */
    
    function __construct(CI_DB_mysqli_driver $conexion, $id_filial) {
        $this->id_filial = $id_filial;
        $cuentaGoogle = $this->construct($conexion, $id_filial);
        if (isset($cuentaGoogle[0]['id_filial']) && $cuentaGoogle[0]['id_filial'] == $id_filial){
            $this->user = $cuentaGoogle[0]['user'];
            $this->pass = $cuentaGoogle[0]['pass'];
            $this->baja = $cuentaGoogle[0]['baja'];
            $this->cuenta_exists = true;
        } else {
            $this->cuenta_exists = false;
        }
    }
    
    /* PRIVATE FUNCTIONS */
    
    private function getObjectToArray(){
        $arrResp = array();
        $arrResp['id_filial'] = $this->id_filial;
        $arrResp['user'] = $this->user;
        $arrResp['pass'] = $this->pass;
        $arrResp['baja'] = $this->baja == '' ? 0 : $this->baja;
        return $arrResp;        
    }    
    
    private function insert(CI_DB_mysqli_driver $conexion){
        $this->cuenta_exists = $conexion->insert("general.".self::$tableName, $this->getObjectToArray());
        return $this->cuenta_exists;
    }
    
    private function update(CI_DB_mysqli_driver $conexion){
        $conexion->where("id_filial", $this->id_filial);
        return $conexion->update("general.".self::$tableName, $this->getObjectToArray());
    }
    
    private function construct(CI_DB_mysqli_driver $conexion){
        $conexion->select("*");
        $conexion->from("general.".self::$tableName);
        $conexion->where("id_filial", $this->id_filial);
        $query = $conexion->get();
        return $query->result_array();
    }
    
    /**
     * determina si una cuenta esta disponible (existe en la DB y baja = 0)
     * 
     * @return boolean
     */
    public function isEnabled(){
        return $this->cuenta_exists && is_numeric($this->baja) && $this->baja == 0;
    }
    
    /**
     * deshabilita una cuenta google
     * 
     * @return boolean
     */
    public function disableAccount(CI_DB_mysqli_driver $conexion){
        $conexion->where("id_filial", $this->id_filial);
        return $conexion->update("general.".self::$tableName, array("baja" => 1));
    }
    
    /* determina si una cuenta existe en la base de datos 
     *
     * @return boolean
     */
    public function accountExists(){
        return $this->cuenta_exists;
    }
    
    /* PUBLIC FUNCTIONS */
    
    /**
     * Guarda o actualiza una cuenta Google definida por la filial
     * 
     * @param CI_DB_mysqli_driver $conexion      Objeto de conexion a la base de datos
     * @return boolean
     */
    public function guardar(CI_DB_mysqli_driver $conexion){
        if ($this->cuenta_exists)
            return $this->update($conexion);
        else
            return $this->insert($conexion);
    }
    
    /**
     * retorna el codigo de filial de la cuenta google del objeto
     * 
     * @return integer
     */
    public function getIdFilial(){
        return $this->id_filial;
    }
    
    /* STATIC FUCNTIONS */
    
    /**
     * Lista las cunetas google de las filiales
     * 
     * @param CI_DB_mysqli_driver $conexion
     * @return array
     */
    static public function listar(CI_DB_mysqli_driver $conexion){
        $conexion->select("*");
        $conexion->from("general.".self::$tableName);
        $query = $conexion->get();
        return $query->result_array();
    }
    
}

