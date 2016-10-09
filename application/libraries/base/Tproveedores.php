<?php

/**
* Class Tproveedores
*
*Class  Tproveedores maneja todos los aspectos de proveedores
*
* @package  SistemaIGA
* @subpackage Proveedores
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tproveedores extends class_general{

    /**
    * codigo de proveedores
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * web de proveedores
    * @var web varchar (requerido)
    * @access public
    */
    public $web;

    /**
    * descripcion de proveedores
    * @var descripcion varchar (requerido)
    * @access public
    */
    public $descripcion;

    /**
    * baja de proveedores
    * @var baja smallint
    * @access public
    */
    public $baja;

    /**
    * cod_postal de proveedores
    * @var cod_postal varchar (requerido)
    * @access public
    */
    public $cod_postal;

    /**
    * fecha_alta de proveedores
    * @var fecha_alta date (requerido)
    * @access public
    */
    public $fecha_alta;

    /**
    * cod_usuario_creador de proveedores
    * @var cod_usuario_creador int
    * @access public
    */
    public $cod_usuario_creador;

    /**
    * cod_razon_social de proveedores
    * @var cod_razon_social int
    * @access public
    */
    public $cod_razon_social;


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
    protected $nombreTabla = 'proveedores';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase proveedores
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
                $this->web = $arrConstructor[0]['web'];
                $this->descripcion = $arrConstructor[0]['descripcion'];
                $this->baja = $arrConstructor[0]['baja'];
                $this->cod_postal = $arrConstructor[0]['cod_postal'];
                $this->fecha_alta = $arrConstructor[0]['fecha_alta'];
                $this->cod_usuario_creador = $arrConstructor[0]['cod_usuario_creador'];
                $this->cod_razon_social = $arrConstructor[0]['cod_razon_social'];
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
        $arrTemp['web'] = $this->web == '' ? null : $this->web;
        $arrTemp['descripcion'] = $this->descripcion == '' ? null : $this->descripcion;
        $arrTemp['baja'] = $this->baja == '' ? '0' : $this->baja;
        $arrTemp['cod_postal'] = $this->cod_postal == '' ? null : $this->cod_postal;
        $arrTemp['fecha_alta'] = $this->fecha_alta == '' ? null : $this->fecha_alta;
        $arrTemp['cod_usuario_creador'] = $this->cod_usuario_creador;
        $arrTemp['cod_razon_social'] = $this->cod_razon_social;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase proveedores o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarProveedores(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto proveedores
     *
     * @return integer
     */
    public function getCodigoProveedores(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de proveedores según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de proveedores y los valores son los valores a actualizar
     */
    public function setProveedores(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["baja"]))
            $retorno = "baja";
        else if (!isset($arrCamposValores["cod_usuario_creador"]))
            $retorno = "cod_usuario_creador";
        else if (!isset($arrCamposValores["cod_razon_social"]))
            $retorno = "cod_razon_social";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setProveedores");
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
    * retorna los campos presentes en la tabla proveedores en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposProveedores(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "proveedores");
    }

    /**
    * Buscar registros en la tabla proveedores
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de proveedores o la cantdad de registros segun el parametro contar
    */
    static function listarProveedores(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "proveedores", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>