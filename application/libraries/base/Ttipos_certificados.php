<?php

/**
* Class Ttipos_certificados
*
*Class  Ttipos_certificados maneja todos los aspectos de tipos_certificados
*
* @package  SistemaIGA
* @subpackage Tipos_certificados
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Ttipos_certificados extends class_general{

    /**
    * codigo de tipos_certificados
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * nombre_es de tipos_certificados
    * @var nombre_es varchar
    * @access public
    */
    public $nombre_es;

    /**
    * nombre_pt de tipos_certificados
    * @var nombre_pt varchar
    * @access public
    */
    public $nombre_pt;

    /**
    * nombre_in de tipos_certificados
    * @var nombre_in varchar
    * @access public
    */
    public $nombre_in;

    /**
    * cod_producto de tipos_certificados
    * @var cod_producto int
    * @access public
    */
    public $cod_producto;

    /**
    * cod_producto_certificado_es de tipos_certificados
    * @var cod_producto_certificado_es int
    * @access public
    */
    public $cod_producto_certificado_es;

    /**
    * cod_producto_certificado_pt de tipos_certificados
    * @var cod_producto_certificado_pt int
    * @access public
    */
    public $cod_producto_certificado_pt;

    /**
    * modelo de tipos_certificados
    * @var modelo int
    * @access public
    */
    public $modelo;


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
    protected $nombreTabla = 'general.tipos_certificados';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase tipos_certificados
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
                $this->nombre_es = $arrConstructor[0]['nombre_es'];
                $this->nombre_pt = $arrConstructor[0]['nombre_pt'];
                $this->nombre_in = $arrConstructor[0]['nombre_in'];
                $this->cod_producto = $arrConstructor[0]['cod_producto'];
                $this->cod_producto_certificado_es = $arrConstructor[0]['cod_producto_certificado_es'];
                $this->cod_producto_certificado_pt = $arrConstructor[0]['cod_producto_certificado_pt'];
                $this->modelo = $arrConstructor[0]['modelo'];
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
        $arrTemp['nombre_es'] = $this->nombre_es;
        $arrTemp['nombre_pt'] = $this->nombre_pt;
        $arrTemp['nombre_in'] = $this->nombre_in;
        $arrTemp['cod_producto'] = $this->cod_producto;
        $arrTemp['cod_producto_certificado_es'] = $this->cod_producto_certificado_es;
        $arrTemp['cod_producto_certificado_pt'] = $this->cod_producto_certificado_pt;
        $arrTemp['modelo'] = $this->modelo;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase tipos_certificados o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarTipos_certificados(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto tipos_certificados
     *
     * @return integer
     */
    public function getCodigoTipos_certificados(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de tipos_certificados según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de tipos_certificados y los valores son los valores a actualizar
     */
    public function setTipos_certificados(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["nombre_es"]))
            $retorno = "nombre_es";
        else if (!isset($arrCamposValores["nombre_pt"]))
            $retorno = "nombre_pt";
        else if (!isset($arrCamposValores["nombre_in"]))
            $retorno = "nombre_in";
        else if (!isset($arrCamposValores["cod_producto"]))
            $retorno = "cod_producto";
        else if (!isset($arrCamposValores["cod_producto_certificado_es"]))
            $retorno = "cod_producto_certificado_es";
        else if (!isset($arrCamposValores["cod_producto_certificado_pt"]))
            $retorno = "cod_producto_certificado_pt";
        else if (!isset($arrCamposValores["modelo"]))
            $retorno = "modelo";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setTipos_certificados");
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
    * retorna los campos presentes en la tabla tipos_certificados en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposTipos_certificados(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.tipos_certificados");
    }

    /**
    * Buscar registros en la tabla tipos_certificados
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de tipos_certificados o la cantdad de registros segun el parametro contar
    */
    static function listarTipos_certificados(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.tipos_certificados", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>