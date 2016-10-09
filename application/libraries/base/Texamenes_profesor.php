<?php

/**
* Class Texamenes_profesor
*
*Class  Texamenes_profesor maneja todos los aspectos de examenes_profesor
*
* @package  SistemaIGA
* @subpackage Examenes_profesor
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Texamenes_profesor extends class_general{

    /**
    * codigo de examenes_profesor
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * codprofesor de examenes_profesor
    * @var codprofesor int
    * @access public
    */
    public $codprofesor;

    /**
    * codexamen de examenes_profesor
    * @var codexamen int
    * @access public
    */
    public $codexamen;

    /**
    * baja de examenes_profesor
    * @var baja int
    * @access public
    */
    public $baja;


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
    protected $nombreTabla = 'examenes_profesor';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase examenes_profesor
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
                $this->codprofesor = $arrConstructor[0]['codprofesor'];
                $this->codexamen = $arrConstructor[0]['codexamen'];
                $this->baja = $arrConstructor[0]['baja'];
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
        $arrTemp['codprofesor'] = $this->codprofesor;
        $arrTemp['codexamen'] = $this->codexamen;
        $arrTemp['baja'] = $this->baja == '' ? '0' : $this->baja;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase examenes_profesor o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarExamenes_profesor(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto examenes_profesor
     *
     * @return integer
     */
    public function getCodigoExamenes_profesor(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de examenes_profesor según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de examenes_profesor y los valores son los valores a actualizar
     */
    public function setExamenes_profesor(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["codprofesor"]))
            $retorno = "codprofesor";
        else if (!isset($arrCamposValores["codexamen"]))
            $retorno = "codexamen";
        else if (!isset($arrCamposValores["baja"]))
            $retorno = "baja";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setExamenes_profesor");
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
    * retorna los campos presentes en la tabla examenes_profesor en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposExamenes_profesor(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "examenes_profesor");
    }

    /**
    * Buscar registros en la tabla examenes_profesor
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de examenes_profesor o la cantdad de registros segun el parametro contar
    */
    static function listarExamenes_profesor(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "examenes_profesor", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>