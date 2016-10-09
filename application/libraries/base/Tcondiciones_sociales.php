<?php

/**
* Class Tcondiciones_sociales
*
*Class  Tcondiciones_sociales maneja todos los aspectos de condiciones_sociales
*
* @package  SistemaIGA
* @subpackage Condiciones_sociales
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tcondiciones_sociales extends class_general{

    /**
    * codigo de condiciones_sociales
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * condicion de condiciones_sociales
    * @var condicion varchar
    * @access public
    */
    public $condicion;

    /**
    * cuit de condiciones_sociales
    * @var cuit smallint
    * @access public
    */
    public $cuit;

    /**
    * razon_social de condiciones_sociales
    * @var razon_social smallint
    * @access public
    */
    public $razon_social;

    /**
    * cod_pais de condiciones_sociales
    * @var cod_pais int
    * @access public
    */
    public $cod_pais;

    /**
    * default de condiciones_sociales
    * @var default int (requerido)
    * @access public
    */
    public $default;

    /**
    * cod_afip de condiciones_sociales
    * @var default int 
    * @access public
    */
    public $cod_afip;

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
    protected $nombreTabla = 'general.condiciones_sociales';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase condiciones_sociales
     *
     * @param CI_DB_mysqli_driver $connection
     * @param integer $codigo (opcional) el codigo del objeto a crear
     */
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null){
        $this->oConnection = $conexion;
        if ($codigo != null && $codigo != -1){
            $arrConstructor = $this->_constructor($codigo);
            if (count($arrConstructor) > 0){
                $this->codigo = $arrConstructor[0]['codigo'];
                $this->condicion = $arrConstructor[0]['condicion'];
                $this->cuit = $arrConstructor[0]['cuit'];
                $this->razon_social = $arrConstructor[0]['razon_social'];
                $this->cod_pais = $arrConstructor[0]['cod_pais'];
                $this->default = $arrConstructor[0]['default'];
                $this->cod_afip = $arrConstructor[0]['cod_afip'];
            } else {
                $this->codigo = -1;
            }
        } else {
            $this->codigo = -1;
        }
    }

    /* PORTECTED FUNCTIONS */

    /**
    * Devuelve el objeto con todas sus propiedades y valores en formato array
    * 
    * @return array
    */
    protected function _getArrayDeObjeto(){
        $arrTemp = array();
        $arrTemp['condicion'] = $this->condicion;
        $arrTemp['cuit'] = $this->cuit == '' ? '0' : $this->cuit;
        $arrTemp['razon_social'] = $this->razon_social == '' ? '0' : $this->razon_social;
        $arrTemp['cod_pais'] = $this->cod_pais;
        $arrTemp['default'] = $this->default == '' ? null : $this->default;
        $arrTemp['cod_afip'] = $this->cod_afip == '' ? null : $this->cod_afip;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase condiciones_sociales o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarCondiciones_sociales(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto condiciones_sociales
     *
     * @return integer
     */
    public function getCodigoCondiciones_sociales(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de condiciones_sociales según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de condiciones_sociales y los valores son los valores a actualizar
     */
    public function setCondiciones_sociales(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["condicion"]))
            $retorno = "condicion";
        else if (!isset($arrCamposValores["cuit"]))
            $retorno = "cuit";
        else if (!isset($arrCamposValores["razon_social"]))
            $retorno = "razon_social";
        else if (!isset($arrCamposValores["cod_pais"]))
            $retorno = "cod_pais";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setCondiciones_sociales");
        } else {
            foreach ($this as $key => $value){
                if (isset($arrCamposValores[$key])){
                    $this->$key = $arrCamposValores[$key];
                }
            }
            return true;
        }
    }

    /* STATIC FUNCTIONS */

    /**
    * retorna los campos presentes en la tabla condiciones_sociales en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposCondiciones_sociales(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.condiciones_sociales");
    }

    /**
    * Buscar registros en la tabla condiciones_sociales
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de condiciones_sociales o la cantdad de registros segun el parametro contar
    */
    static function listarCondiciones_sociales(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.condiciones_sociales", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>