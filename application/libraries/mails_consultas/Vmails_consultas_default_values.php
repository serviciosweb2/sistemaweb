<?php

class Vmails_consultas_default_values extends class_general{
    
    private $cod_template;
    private $cod_filial;
    private $numero_campo;
    private $tipo_campo;
    public $valor_campo;
    private $exists = false;
    
    static private $dataBase = "mails_consultas";
    static private $tableName = "mails_templates_default_values";
    private $oConnection;
    
    /* COSNTRUCTOR */
    
    function __construct(CI_DB_mysqli_driver $conexion, $codTemplate, $codFilial, $nroCampo, $tipoCampo) {
        $this->oConnection = $conexion;
        $this->cod_template = $codTemplate;
        $this->cod_filial = $codFilial;
        $this->numero_campo = $nroCampo;
        $this->tipo_campo = $tipoCampo;
        $arrValues = self::constructor($conexion, $codTemplate, $codFilial, $nroCampo, $tipoCampo);        
        if (count($arrValues) > 0){
            $this->exists = true;
            $this->valor_campo = $arrValues[0]['valor_campo'];
        } else {
            $this->exists = false;
        }
    }
    
    /* PROTECTED FUNCTIONS */
    
    /**
     * retorna el objeto en formato array
     * 
     * @return array
     */
    protected function _getArrayDeObjeto(){
        $arrResp = array();
        $arrResp['cod_template'] = $this->cod_template;
        $arrResp['cod_filial'] = $this->cod_filial;
        $arrResp['numero_campo'] = $this->numero_campo;
        $arrResp['tipo_campo'] = $this->tipo_campo;
        $arrResp['valor_campo'] = $this->valor_campo;
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
     * actualiza el objeto en la base de datos
     * 
     * @return boolean
     */
    protected function _actualizar(){
        $arrCondiciones = array(
            "cod_template" => $this->cod_template,
            "cod_filial" => $this->cod_filial,
            "numero_campo" => $this->numero_campo,
            "tipo_campo" => $this->tipo_campo
        );
        return $this->oConnection->update(self::$dataBase.".".self::$tableName, $this->_getArrayDeObjeto(), $arrCondiciones);
    }
    
    /* PRIVATE FUNCTIONS */
    
    /**
     * constructor de la clase
     * 
     * @param CI_DB_mysqli_driver $conexion
     * @param int $codTemplate
     * @param int $codFilial
     * @param int $nroCampo
     * @param string $tipoCampo
     * @return array
     */
    static private function constructor(CI_DB_mysqli_driver $conexion, $codTemplate, $codFilial, $nroCampo, $tipoCampo){
        $conexion->select("*");
        $conexion->from(self::$dataBase.".".self::$tableName);
        $conexion->where("cod_template", $codTemplate);
        $conexion->where("cod_filial", $codFilial);
        $conexion->where("numero_campo", $nroCampo);
        $conexion->where("tipo_campo", $tipoCampo);
        $query = $conexion->get();
        return $query->result_array();
    }
    
    /* PUBLIC FUNCTIONS */
    
    /**
     * Guarda el objeto en la base de datos (insert o update segun sea un registro nuevo o ya existente)
     * 
     * @return boolean
     */
    public function guardar(){
        if ($this->exists){
            return $this->_actualizar();
        } else {
            $this->exists = $this->_insertar();
            return $this->exists;              
        }
    }
    
    /* STATIC FUNCTIONS */
    
    /**
     * Lista los registros de la tabla mails_consultas_default_values segun los filtros indicados
     * 
     * @param CI_DB_mysqli_driver $connection
     * @param array $condiciones
     * @param array $limite
     * @param array $orden
     * @param array $grupo
     * @param boolean $contar
     * @return array
     */
    static function listar(CI_DB_mysqli_driver $connection, array $condiciones = null, array $limite = null, 
            array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($connection, self::$dataBase.".".self::$tableName, $condiciones, $limite, $orden, $grupo, $contar);
    }
    
}