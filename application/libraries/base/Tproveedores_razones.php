<?php

/**
* Class Tproveedores_razones
*
*Class  Tproveedores_razones maneja todos los aspectos de proveedores_razones
*
* @package  SistemaIGA
* @subpackage Proveedores_razones
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tproveedores_razones extends class_general{

    /**
    * cod_proveedor de proveedores_razones
    * @var cod_proveedor int
    * @access protected
    */
    protected $cod_proveedor;

    /**
    * cod_razon de proveedores_razones
    * @var cod_razon int (requerido)
    * @access public
    */
    public $cod_razon;


    /**
    * primaryKey de la tabla
    * @var primaryKey var
    * @access protected
    */
    protected $primaryKey = "cod_proveedor";
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
    protected $nombreTabla = 'proveedores_razones';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase proveedores_razones
     *
     * @param CI_DB_mysqli_driver $connection
     * @param integer $codigo (opcional) el codigo del objeto a crear
     */
    function __construct(CI_DB_mysqli_driver $conexion, $cod_proveedor = null){
        $this->oConnection = $conexion;
        if ($cod_proveedor != null && $cod_proveedor != -1){
            $arrConstructor = $this->_constructor($cod_proveedor);
            if (count($arrConstructor) > 0){
                $this->cod_proveedor = $arrConstructor[0]['cod_proveedor'];
                $this->cod_razon = $arrConstructor[0]['cod_razon'];
            } else {
                $this->cod_proveedor = -1;
            }
        } else {
            $this->cod_proveedor = -1;
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
        $arrTemp['cod_razon'] = $this->cod_razon == '' ? null : $this->cod_razon;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase proveedores_razones o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarProveedores_razones(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto proveedores_razones
     *
     * @return integer
     */
    public function getCodigoProveedores_razones(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de proveedores_razones según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de proveedores_razones y los valores son los valores a actualizar
     */
    public function setProveedores_razones(array $arrCamposValores){
        $retorno = "";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setProveedores_razones");
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
    * retorna los campos presentes en la tabla proveedores_razones en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposProveedores_razones(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "proveedores_razones");
    }

    /**
    * Buscar registros en la tabla proveedores_razones
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de proveedores_razones o la cantdad de registros segun el parametro contar
    */
    static function listarProveedores_razones(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "proveedores_razones", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>