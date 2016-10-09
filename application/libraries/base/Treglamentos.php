<?php

/**
* Class Treglamentos
*
*Class  Treglamentos maneja todos los aspectos de reglamentos
*
* @package  SistemaIGA
* @subpackage Reglamentos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Treglamentos extends class_general{

    /**
    * id de reglamentos
    * @var id int
    * @access protected
    */
    protected $id;

    /**
    * tipo de reglamentos
    * @var tipo enum
    * @access public
    */
    public $tipo;

    /**
    * reglamento_es de reglamentos
    * @var reglamento_es longtext
    * @access public
    */
    public $reglamento_es;

    /**
    * reglamento_in de reglamentos
    * @var reglamento_in longtext
    * @access public
    */
    public $reglamento_in;

    /**
    * reglamento_pt de reglamentos
    * @var reglamento_pt longtext
    * @access public
    */
    public $reglamento_pt;


    /**
    * primaryKey de la tabla
    * @var primaryKey var
    * @access protected
    */
    protected $primaryKey = "id";
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
    protected $nombreTabla = 'general.reglamentos';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase reglamentos
     *
     * @param CI_DB_mysqli_driver $connection
     * @param integer $codigo (opcional) el codigo del objeto a crear
     */
    function __construct(CI_DB_mysqli_driver $conexion, $id = null){
        $this->oConnection = $conexion;
        if ($id != null && $id != -1){
            $arrConstructor = $this->_constructor($id);
            if (count($arrConstructor) > 0){
                $this->id = $arrConstructor[0]['id'];
                $this->tipo = $arrConstructor[0]['tipo'];
                $this->reglamento_es = $arrConstructor[0]['reglamento_es'];
                $this->reglamento_in = $arrConstructor[0]['reglamento_in'];
                $this->reglamento_pt = $arrConstructor[0]['reglamento_pt'];
            } else {
                $this->id = -1;
            }
        } else {
            $this->id = -1;
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
        $arrTemp['tipo'] = $this->tipo == '' ? 'matriculas' : $this->tipo;
        $arrTemp['reglamento_es'] = $this->reglamento_es;
        $arrTemp['reglamento_in'] = $this->reglamento_in;
        $arrTemp['reglamento_pt'] = $this->reglamento_pt;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase reglamentos o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarReglamentos(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto reglamentos
     *
     * @return integer
     */
    public function getCodigoReglamentos(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de reglamentos según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de reglamentos y los valores son los valores a actualizar
     */
    public function setReglamentos(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["tipo"]))
            $retorno = "tipo";
        else if (!isset($arrCamposValores["reglamento_es"]))
            $retorno = "reglamento_es";
        else if (!isset($arrCamposValores["reglamento_in"]))
            $retorno = "reglamento_in";
        else if (!isset($arrCamposValores["reglamento_pt"]))
            $retorno = "reglamento_pt";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setReglamentos");
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
    * retorna los campos presentes en la tabla reglamentos en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposReglamentos(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.reglamentos");
    }

    /**
    * Buscar registros en la tabla reglamentos
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de reglamentos o la cantdad de registros segun el parametro contar
    */
    static function listarReglamentos(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.reglamentos", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>