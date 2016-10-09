<?php

/**
* Class Tasistencias
*
*Class  Tasistencias maneja todos los aspectos de asistencias
*
* @package  SistemaIGA
* @subpackage Asistencias
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tasistencias extends class_general{

    /**
    * codigo de asistencias
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * codmatricula de asistencias
    * @var codmatricula int
    * @access public
    */
    public $codmatricula;

    /**
    * justificado de asistencias
    * @var justificado smallint (requerido)
    * @access public
    */
    public $justificado;

    /**
    * ausente de asistencias
    * @var ausente smallint
    * @access public
    */
    public $ausente;

    /**
    * cod_horario de asistencias
    * @var cod_horario int
    * @access public
    */
    public $cod_horario;


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
    protected $nombreTabla = 'asistencias';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase asistencias
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
                $this->codmatricula = $arrConstructor[0]['codmatricula'];
                $this->justificado = $arrConstructor[0]['justificado'];
                $this->ausente = $arrConstructor[0]['ausente'];
                $this->cod_horario = $arrConstructor[0]['cod_horario'];
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
        $arrTemp['codmatricula'] = $this->codmatricula;
        $arrTemp['justificado'] = $this->justificado == '' ? null : $this->justificado;
        $arrTemp['ausente'] = $this->ausente == '' ? '0' : $this->ausente;
        $arrTemp['cod_horario'] = $this->cod_horario;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase asistencias o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarAsistencias(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto asistencias
     *
     * @return integer
     */
    public function getCodigoAsistencias(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de asistencias según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de asistencias y los valores son los valores a actualizar
     */
    public function setAsistencias(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["codmatricula"]))
            $retorno = "codmatricula";
        else if (!isset($arrCamposValores["ausente"]))
            $retorno = "ausente";
        else if (!isset($arrCamposValores["cod_horario"]))
            $retorno = "cod_horario";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setAsistencias");
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
    * retorna los campos presentes en la tabla asistencias en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposAsistencias(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "asistencias");
    }

    /**
    * Buscar registros en la tabla asistencias
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de asistencias o la cantdad de registros segun el parametro contar
    */
    static function listarAsistencias(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "asistencias", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>