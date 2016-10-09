<?php

/**
 * @author romario
 */
class Tgoogle_adwords_goal_completions extends class_general {
    protected $codigo;
    public $campana_codigo;
    public $envios;
    public $fecha;
    public $clics;


    /**
    * primaryKey de la tabla
    * @var primaryKey var
    * @access protected
    */
    protected $primaryKey = "codigo";
    /**
    * conexion utilizada por el objeto
    * @var oConnection CI_DB_mysqli_driver
    * @access protected
    */
    protected $oConnection;

    /**
    * nombre de la tabla donde se guardan los objetos
    * @var nombreTabla varchar
    * @access protected
    */
    protected $nombreTabla = 'general.google_adwords_goal_completions';
    
    public function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        $this->oConnection = $conexion;
        
        if ($codigo != null && $codigo != -1){
            $arrConstructor = $this->_constructor($id);
            if (count($arrConstructor) > 0){
                $this->codigo = $arrConstructor[0]['codigo'];
                $this->campana_codigo = $arrConstructor[0]['campana_codigo'];
                $this->envios = $arrConstructor[0]['envios'];
                $this->fecha = $arrConstructor[0]['fecha'];
                $this->clics = $arrConstructor[0]['clics'];
            }
            else {
                $this->codigo = -1;
            }
        }
        else {
            $this->codigo = -1;
        }
    }
    
    protected function _getArrayDeObjeto(){
        $arrTemp = array();
        $arrTemp['campana_codigo'] = $this->campana_codigo == '' ? null : $this->campana_codigo;
        $arrTemp['envios'] = $this->envios;
        $arrTemp['fecha'] = $this->fecha;
        $arrTemp['clics'] = $this->clics;
        return $arrTemp;
    }
    
    public function guardarGoogle_adwords_goal_completions(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto google_adwords_goal_completions
     *
     * @return integer
     */
    public function getCodigoGoogle_adwords_goal_completions(){
        return $this->_getCodigo();
    }
    
    public function setGoogle_adwords_goal_completions(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["campana_codigo"]))
            $retorno = "campana_codigo";
        else if (!isset($arrCamposValores["envios"]))
            $retorno = "envios";
        else if (!isset($arrCamposValores["fecha"]))
            $retorno = "fecha";
        else if (!isset($arrCamposValores["clics"]))
            $retorno = "clics";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setGoogle_adwords_goal_completions");
        } else {
            foreach ($this as $key => $value){
                if (isset($arrCamposValores[$key])){
                    $this->$key = $arrCamposValores[$key];
                }
            }
            return true;
        }
    }
    
    static function camposGoogle_adwords_goal_completions(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.google_adwords_goal_completions");
    }
    
    static function listarGoogle_adwords_goal_completions(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.google_adwords_goal_completions", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
