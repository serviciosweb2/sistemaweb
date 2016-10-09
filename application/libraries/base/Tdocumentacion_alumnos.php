<?php

/**
* Class Tdocumentacion_alumnos
*
*Class  Tdocumentacion_alumnos maneja todos los aspectos de documentacion_alumnos
*
* @package  SistemaIGA
* @subpackage Documentacion_alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tdocumentacion_alumnos extends class_general{

    /**
    * codalu de documentacion_alumnos
    * @var codalu int
    * @access protected
    */
    protected $codalu;

    /**
    * coddocumentacion de documentacion_alumnos
    * @var coddocumentacion int
    * @access public
    */
    public $coddocumentacion;


    /**
    * primaryKey de la tabla
    * @var primaryKey var
    * @access protected
    */
    protected $primaryKey = "codalu";
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
    protected $nombreTabla = 'documentacion_alumnos';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase documentacion_alumnos
     *
     * @param CI_DB_mysqli_driver $connection
     * @param integer $codigo (opcional) el codigo del objeto a crear
     */
    function __construct(CI_DB_mysqli_driver $conexion, $codalu = null){
        $this->oConnection = $conexion;
        if ($codalu != null && $codalu != -1){
            $arrConstructor = $this->_constructor($codalu);
            if (count($arrConstructor) > 0){
                $this->codalu = $arrConstructor[0]['codalu'];
                $this->coddocumentacion = $arrConstructor[0]['coddocumentacion'];
            } else {
                $this->codalu = -1;
            }
        } else {
            $this->codalu = -1;
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
        $arrTemp['coddocumentacion'] = $this->coddocumentacion;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase documentacion_alumnos o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarDocumentacion_alumnos(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto documentacion_alumnos
     *
     * @return integer
     */
    public function getCodigoDocumentacion_alumnos(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de documentacion_alumnos según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de documentacion_alumnos y los valores son los valores a actualizar
     */
    public function setDocumentacion_alumnos(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["coddocumentacion"]))
            $retorno = "coddocumentacion";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setDocumentacion_alumnos");
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
    * retorna los campos presentes en la tabla documentacion_alumnos en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposDocumentacion_alumnos(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "documentacion_alumnos");
    }

    /**
    * Buscar registros en la tabla documentacion_alumnos
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de documentacion_alumnos o la cantdad de registros segun el parametro contar
    */
    static function listarDocumentacion_alumnos(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "documentacion_alumnos", $condiciones, $limite, $orden, $grupo, $contar);
    }
    
    static function listarDocumentacion(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.documentacion", $condiciones, $limite, $orden, $grupo, $contar);
    }
    
}
?>
