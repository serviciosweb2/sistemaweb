<?php

/**
* Class Tdocumentacion
*
*Class  Tdocumentacion maneja todos los aspectos de documentacion
*
* @package  SistemaIGA
* @subpackage Documentacion
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tdocumentacion extends class_general{

    /**
    * codigo de documentacion
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * documentacion de documentacion
    * @var documentacion varchar
    * @access public
    */
    public $documentacion;


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
    protected $nombreTabla = 'general.documentacion';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase documentacion
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
                $this->documentacion = $arrConstructor[0]['documentacion'];
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
        $arrTemp['documentacion'] = $this->documentacion;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase documentacion o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarDocumentacion(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto documentacion
     *
     * @return integer
     */
    public function getCodigoDocumentacion(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de documentacion según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de documentacion y los valores son los valores a actualizar
     */
    public function setDocumentacion(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["documentacion"]))
            $retorno = "documentacion";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setDocumentacion");
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
    * retorna los campos presentes en la tabla documentacion en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposDocumentacion(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.documentacion");
    }

    /**
    * Buscar registros en la tabla documentacion
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de documentacion o la cantdad de registros segun el parametro contar
    */
    static function listarDocumentacion(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.documentacion", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>