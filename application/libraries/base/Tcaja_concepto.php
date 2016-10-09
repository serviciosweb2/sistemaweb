<?php

/**
* Class Tcaja_concepto
*
*Class  Tcaja_concepto maneja todos los aspectos de caja_concepto
*
* @package  SistemaIGA
* @subpackage Caja_concepto
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tcaja_concepto extends class_general{

    /**
    * codigo de caja_concepto
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * concepto de caja_concepto
    * @var concepto varchar (requerido)
    * @access public
    */
    public $concepto;


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
    protected $nombreTabla = 'general.caja_concepto';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase caja_concepto
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
                $this->concepto = $arrConstructor[0]['concepto'];
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
        $arrTemp['concepto'] = $this->concepto == '' ? null : $this->concepto;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase caja_concepto o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarCaja_concepto(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto caja_concepto
     *
     * @return integer
     */
    public function getCodigoCaja_concepto(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de caja_concepto según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de caja_concepto y los valores son los valores a actualizar
     */
    public function setCaja_concepto(array $arrCamposValores){
        $retorno = "";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setCaja_concepto");
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
    * retorna los campos presentes en la tabla caja_concepto en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposCaja_concepto(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.caja_concepto");
    }

    /**
    * Buscar registros en la tabla caja_concepto
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de caja_concepto o la cantdad de registros segun el parametro contar
    */
    static function listarCaja_concepto(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.caja_concepto", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>