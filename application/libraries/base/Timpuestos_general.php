<?php

/**
* Class Timpuestos_general 
*
*Class  Timpuestos_general maneja todos los aspectos de impuestos_general
*
* @package  SistemaIGA
* @subpackage Impuestos_general
* @author   Foox
* @version  $Revision: 1.1 $
* @access   private
*/
class Timpuestos_general extends class_general{

    /**
    * codigo de impuestos_general
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * nombre de impuestos_general
    * @var nombre varchar
    * @access public
    */
    public $nombre;

    /**
    * pais de impuestos_general
    * @var pais int
    * @access public
    */
    public $pais;


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
    protected $nombreTabla = 'general.impuestos_general';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase impuestos_general
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
                $this->pais = $arrConstructor[0]['pais'];
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
        $arrTemp['pais'] = $this->tipo;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase impuestos o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarImpuestos_general(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto impuestos_general
     *
     * @return integer
     */
    public function getCodigoImpuestos_general(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de impuestos_general según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de impuestos_general y los valores son los valores a actualizar
     */
    public function setImpuestos_general(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["nombre"]))
            $retorno = "nombre";
        else if (!isset($arrCamposValores["pais"]))
            $retorno = "pais";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setImpuestos_general");
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
    * retorna los campos presentes en la tabla impuestos_general en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposImpuestos_general(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.impuestos_general");
    }

    /**
    * Buscar registros en la tabla impuestos_general
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de impuestos o la cantdad de registros segun el parametro contar
    */
    static function listarImpuestos_general(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.impuestos_general", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>