<?php

class Vmails_respuesta_consultas_historico extends class_general{

    private $cod_filial;
    private $cod_template;
    public $cantidad;

    private $exists = false;
    
    static private $dataBase = "mails_consultas";
    static private $tableName = "mails_respuesta_consultas_historico";
    private $oConnection;
    
    /* CONSTRUCTOR */
    
    function __construct(CI_DB_mysqli_driver &$conexion, $codFilial, $codTemplate) {
        $this->oConnection = $conexion;
        $this->cod_filial = $codFilial;
        $this->cod_template = $codTemplate;
        $arrHistorico = self::constructor($conexion, $codFilial, $codTemplate);
        if (count($arrHistorico) > 0){
            $this->exists = true;
            $this->cantidad = $arrHistorico[0]['cantidad'];
        } else {
            $this->exists = false;
            $this->cantidad = 0;
        }
    }
    
    /* PROTECTED FUNCTIONS */
    
    /**
     * Retorna un array del objeto
     * 
     * @return array
     */
    protected function _getArrayDeObjeto(){
        $arrResp = array();
        $arrResp['cod_filial'] = $this->cod_filial;
        $arrResp['cod_template'] = $this->cod_template;
        $arrResp['cantidad'] = $this->cantidad;
        return $arrResp;
     }
    
     /**
      * Inserta el objeto en la base de datos
      * 
      * @return boolean
      */
    protected function _insertar(){
        return $this->oConnection->insert(self::$dataBase.".".self::$tableName, $this->_getArrayDeObjeto());
    }
    
    /**
     * actualiza los registro del objeto en la base de datos
     * 
     * @return boolean
     */
    protected function _actualizar(){
        $arrCondiciones = array(
            "cod_template" => $this->cod_template,
            "cod_filial" => $this->cod_filial
        );
        return $this->oConnection->update(self::$dataBase.".".self::$tableName, $this->_getArrayDeObjeto(), $arrCondiciones);
    }
    
    /* PRIVATE FUNCTIONS */
    
    /**
     * consturctor de la clase
     * 
     * @param CI_DB_mysqli_driver $conexion
     * @param int $codFilial
     * @param int $codTemplate
     * @return array
     */
    static private function constructor(CI_DB_mysqli_driver $conexion, $codFilial, $codTemplate){
        $conexion->select("*");
        $conexion->from(self::$dataBase.".".self::$tableName);
        $conexion->where("cod_filial", $codFilial);
        $conexion->where("cod_template", $codTemplate);
        $query = $conexion->get();
        return $query->result_array();
    }
    
    /* PUBLIC FUNCTIONS */
    
    /**
     * Guarda el objeto en la base de datos (inserta o actualiza segun sea un registro nuevo o existente)
     * 
     * @return booelan
     */
    public function guardar(){
        if ($this->exists){
            return $this->_actualizar();
        } else {
            $this->exists = $this->_insertar();
            return $this->exists;
        }
    }
    
    public function sumarHistorico($cantidad = 1){
        $this->cantidad = $this->cantidad + $cantidad;
        return $this->guardar();
    }
    
    /* STATIC FUNCTIONS */
}

?>