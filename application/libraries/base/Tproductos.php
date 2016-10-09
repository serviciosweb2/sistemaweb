<?php

/**
* Class Tproductos
*
*Class  Tproductos maneja todos los aspectos de productos
*
* @package  SistemaIGA
* @subpackage Productos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tproductos extends class_general{

    /**
    * codigo de productos
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * nombre de productos
    * @var nombre varchar
    * @access public
    */
    public $nombre;

    /**
    * relacion de productos
    * @var relacion int
    * @access public
    */
    public $relacion;

    /**
    * curso de productos
    * @var curso int
    * @access public
    */
    public $curso;


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
    protected $nombreTabla = 'productos';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase productos
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
                $this->nombre = $arrConstructor[0]['nombre'];
                $this->relacion = $arrConstructor[0]['relacion'];
                $this->curso = $arrConstructor[0]['curso'];
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
        $arrTemp['nombre'] = $this->nombre;
        $arrTemp['relacion'] = $this->relacion;
        $arrTemp['curso'] = $this->curso;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase productos o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarProductos(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto productos
     *
     * @return integer
     */
    public function getCodigoProductos(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de productos según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de productos y los valores son los valores a actualizar
     */
    public function setProductos(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["nombre"]))
            $retorno = "nombre";
        else if (!isset($arrCamposValores["relacion"]))
            $retorno = "relacion";
        else if (!isset($arrCamposValores["curso"]))
            $retorno = "curso";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setProductos");
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
    * retorna los campos presentes en la tabla productos en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposProductos(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "productos");
    }

    /**
    * Buscar registros en la tabla productos
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de productos o la cantdad de registros segun el parametro contar
    */
    static function listarProductos(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "productos", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>