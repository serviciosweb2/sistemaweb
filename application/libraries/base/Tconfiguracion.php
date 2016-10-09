<?php

/**
* Class Tconfiguracion
*
*Class  Tconfiguracion maneja todos los aspectos de configuracion
*
* @package  SistemaIGA
* @subpackage Configuracion
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tconfiguracion extends class_general{

    /**
    * codigo de configuracion
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * key de configuracion
    * @var key varchar
    * @access public
    */
    public $key;

    /**
    * value de configuracion
    * @var value varchar (requerido)
    * @access public
    */
    public $value;

    /**
    * fecha_hora de configuracion
    * @var fecha_hora datetime (requerido)
    * @access public
    */
    public $fecha_hora;


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
    protected $nombreTabla = 'configuracion';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase configuracion
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
                $this->key = $arrConstructor[0]['key'];
                $this->value = $arrConstructor[0]['value'];
                $this->fecha_hora = $arrConstructor[0]['fecha_hora'];
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
        $arrTemp['key'] = $this->key;
        $arrTemp['value'] = $this->value == '' ? null : $this->value;
        $arrTemp['fecha_hora'] = $this->fecha_hora == '' ? null : $this->fecha_hora;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase configuracion o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarConfiguracion(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto configuracion
     *
     * @return integer
     */
    public function getCodigoConfiguracion(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de configuracion según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de configuracion y los valores son los valores a actualizar
     */
    public function setConfiguracion(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["key"]))
            $retorno = "key";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setConfiguracion");
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
    * retorna los campos presentes en la tabla configuracion en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposConfiguracion(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "configuracion");
    }

    /**
    * Buscar registros en la tabla configuracion
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de configuracion o la cantdad de registros segun el parametro contar
    */
    static function listarConfiguracion(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "configuracion", $condiciones, $limite, $orden, $grupo, $contar);
    }
}