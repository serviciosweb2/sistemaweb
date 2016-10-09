<?php

/**
* Class Tproductos_alumnos
*
*Class  Tproductos_alumnos maneja todos los aspectos de productos_alumnos
*
* @package  SistemaIGA
* @subpackage Productos_alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tproductos_alumnos extends class_general{

    /**
    * codigo de productos_alumnos
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * codmatri de productos_alumnos
    * @var codmatri int
    * @access public
    */
    public $codmatri;

    /**
    * fecha de productos_alumnos
    * @var fecha date
    * @access public
    */
    public $fecha;

    /**
    * producto de productos_alumnos
    * @var producto int
    * @access public
    */
    public $producto;

    /**
    * observaciones de productos_alumnos
    * @var observaciones varchar (requerido)
    * @access public
    */
    public $observaciones;


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
    protected $nombreTabla = 'productos_alumnos';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase productos_alumnos
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
                $this->codmatri = $arrConstructor[0]['codmatri'];
                $this->fecha = $arrConstructor[0]['fecha'];
                $this->producto = $arrConstructor[0]['producto'];
                $this->observaciones = $arrConstructor[0]['observaciones'];
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
        $arrTemp['codmatri'] = $this->codmatri;
        $arrTemp['fecha'] = $this->fecha;
        $arrTemp['producto'] = $this->producto;
        $arrTemp['observaciones'] = $this->observaciones == '' ? null : $this->observaciones;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase productos_alumnos o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarProductos_alumnos(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto productos_alumnos
     *
     * @return integer
     */
    public function getCodigoProductos_alumnos(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de productos_alumnos según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de productos_alumnos y los valores son los valores a actualizar
     */
    public function setProductos_alumnos(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["codmatri"]))
            $retorno = "codmatri";
        else if (!isset($arrCamposValores["fecha"]))
            $retorno = "fecha";
        else if (!isset($arrCamposValores["producto"]))
            $retorno = "producto";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setProductos_alumnos");
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
    * retorna los campos presentes en la tabla productos_alumnos en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposProductos_alumnos(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "productos_alumnos");
    }

    /**
    * Buscar registros en la tabla productos_alumnos
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de productos_alumnos o la cantdad de registros segun el parametro contar
    */
    static function listarProductos_alumnos(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "productos_alumnos", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>