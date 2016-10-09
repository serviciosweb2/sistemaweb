<?php

/**
 * Description of Tfacebook_ads_datos
 *
 * @author romario
 */
class Tfacebook_ads_datos extends class_general {
    protected $codigo;
    public $campana_codigo;
    public $alcance;
    public $fecha;
    public $resultados;
    public $tipo_resultado;


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
    protected $nombreTabla = 'general.facebook_ads_datos';
    
    public function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        $this->oConnection = $conexion;
        
        if ($codigo != null && $codigo != -1){
            $arrConstructor = $this->_constructor($id);
            if (count($arrConstructor) > 0){
                $this->codigo = $arrConstructor[0]['codigo'];
                $this->campana_codigo = $arrConstructor[0]['campana_codigo'];
                $this->alcance = $arrConstructor[0]['alcance'];
                $this->fecha = $arrConstructor[0]['fecha'];
                $this->resultados = $arrConstructor[0]['resultados'];
                $this->tipo_resultado = $arrConstructor[0]['tipo_resultado'];
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
        $arrTemp['alcance'] = $this->alcance;
        $arrTemp['fecha'] = $this->fecha;
        $arrTemp['resultados'] = $this->resultados;
        $arrTemp['tipo_resultado'] = $this->tipo_resultado;
        return $arrTemp;
    }
    
    public function guardarFacebook_ads_datos(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto facebook_ads_datos
     *
     * @return integer
     */
    public function getCodigoFacebook_ads_datos(){
        return $this->_getCodigo();
    }
    
    public function setFacebook_ads_datos(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["campana_codigo"]))
            $retorno = "campana_codigo";
        else if (!isset($arrCamposValores["alcance"]))
            $retorno = "alcance";
        else if (!isset($arrCamposValores["fecha"]))
            $retorno = "fecha";
        else if (!isset($arrCamposValores["resultados"]))
            $retorno = "resultados";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setFacebook_ads_datos");
        } else {
            foreach ($this as $key => $value){
                if (isset($arrCamposValores[$key])){
                    $this->$key = $arrCamposValores[$key];
                }
            }
            return true;
        }
    }
    
    static function camposFacebook_ads_datos(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.facebook_ads_datos");
    }
    
    static function listarFacebook_ads_datos(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.facebook_ads_datos", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
