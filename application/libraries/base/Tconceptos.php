<?php

/**
* Class Tconceptos
*
*Class  Tconceptos maneja todos los aspectos de conceptos
*
* @package  SistemaIGA
* @subpackage Conceptos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tconceptos extends class_general{

    /**
    * codigo de conceptos
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * key de conceptos
    * @var key varchar (requerido)
    * @access public
    */
    public $key;

    /**
    * valor de conceptos
    * @var valor varchar (requerido)
    * @access public
    */
    public $valor;

    /**
    * codigo_padre de conceptos
    * @var codigo_padre int (requerido)
    * @access public
    */
    public $codigo_padre;


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
    protected $nombreTabla = 'conceptos';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase conceptos
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
                $this->valor = $arrConstructor[0]['valor'];
                $this->codigo_padre = $arrConstructor[0]['codigo_padre'];
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
        $arrTemp['key'] = $this->key == '' ? null : $this->key;
        $arrTemp['valor'] = $this->valor == '' ? null : $this->valor;
        $arrTemp['codigo_padre'] = $this->codigo_padre == '' ? null : $this->codigo_padre;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase conceptos o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarConceptos(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto conceptos
     *
     * @return integer
     */
    public function getCodigoConceptos(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de conceptos según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de conceptos y los valores son los valores a actualizar
     */
    public function setConceptos(array $arrCamposValores){
        $retorno = "";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setConceptos");
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
    * retorna los campos presentes en la tabla conceptos en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposConceptos(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "conceptos");
    }

    /**
    * Buscar registros en la tabla conceptos
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de conceptos o la cantdad de registros segun el parametro contar
    */
    static function listarConceptos(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "conceptos", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>