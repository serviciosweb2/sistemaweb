<?php

/**
 * Description of Tpublicidad_campanas
 *
 * @author romario
 */
class Tpublicidad_campanas {
    protected $codigo;
    public $nombre;
    public $filial_codigo;
    public $origen;


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
    protected $nombreTabla = 'general.publicidad_campanas';
    
    public function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        $this->oConnection = $conexion;
        
        if ($codigo != null && $codigo != -1){
            $arrConstructor = $this->_constructor($id);
            if (count($arrConstructor) > 0){
                $this->codigo = $arrConstructor[0]['codigo'];
                $this->nombre = $arrConstructor[0]['nombre'];
                $this->filial_codigo = $arrConstructor[0]['filial_codigo'];
                $this->origen = $arrConstructor[0]['origen'];
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
        $arrTemp['nombre'] = $this->filial_codigo;
        $arrTemp['filial_codigo'] = $this->filial_codigo;
        $arrTemp['origen'] = $this->origen;
        return $arrTemp;
    }
    
    public function guardarPublicidad_campanas(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto publicidad_campanas
     *
     * @return integer
     */
    public function getCodigoPublicidad_campanas(){
        return $this->_getCodigo();
    }
    
    public function setPublicidad_campanas(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["nombre"]))
            $retorno = "nombre";
        else if (!isset($arrCamposValores["filial_codigo"]))
            $retorno = "filial_codigo";
        else if (!isset($arrCamposValores["origen"]))
            $retorno = "origen";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setPublicidad_campanas");
        } else {
            foreach ($this as $key => $value){
                if (isset($arrCamposValores[$key])){
                    $this->$key = $arrCamposValores[$key];
                }
            }
            return true;
        }
    }
    
    static function camposPublicidad_campanas(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.publicidad_campanas");
    }
    
    static function listarPublicidad_campanas(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.publicidad_campanas", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
