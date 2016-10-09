<?php

/**
* Class Tci_sessions
*
*Class  Tci_sessions maneja todos los aspectos de ci_sessions
*
* @package  SistemaIGA
* @subpackage Ci_sessions
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tci_sessions extends class_general{

    /**
    * session_id de ci_sessions
    * @var session_id varchar
    * @access protected
    */
    protected $session_id;

    /**
    * ip_address de ci_sessions
    * @var ip_address varchar
    * @access public
    */
    public $ip_address;

    /**
    * user_agent de ci_sessions
    * @var user_agent varchar
    * @access public
    */
    public $user_agent;

    /**
    * last_activity de ci_sessions
    * @var last_activity int
    * @access public
    */
    public $last_activity;

    /**
    * user_data de ci_sessions
    * @var user_data text
    * @access public
    */
    public $user_data;


    /**
    * primaryKey de la tabla
    * @var primaryKey var
    * @access protected
    */
    protected $primaryKey = "session_id";
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
    protected $nombreTabla = 'general.ci_sessions';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase ci_sessions
     *
     * @param CI_DB_mysqli_driver $connection
     * @param integer $codigo (opcional) el codigo del objeto a crear
     */
    function __construct(CI_DB_mysqli_driver $conexion, $session_id = null){
        $this->oConnection = $conexion;
        if ($session_id != null && $session_id != -1){
            $arrConstructor = $this->_constructor($session_id);
            if (count($arrConstructor) > 0){
                $this->session_id = $arrConstructor[0]['session_id'];
                $this->ip_address = $arrConstructor[0]['ip_address'];
                $this->user_agent = $arrConstructor[0]['user_agent'];
                $this->last_activity = $arrConstructor[0]['last_activity'];
                $this->user_data = $arrConstructor[0]['user_data'];
            } else {
                $this->session_id = -1;
            }
        } else {
            $this->session_id = -1;
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
        $arrTemp['ip_address'] = $this->ip_address == '' ? '0' : $this->ip_address;
        $arrTemp['user_agent'] = $this->user_agent;
        $arrTemp['last_activity'] = $this->last_activity == '' ? '0' : $this->last_activity;
        $arrTemp['user_data'] = $this->user_data;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase ci_sessions o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarCi_sessions(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto ci_sessions
     *
     * @return integer
     */
    public function getCodigoCi_sessions(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de ci_sessions según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de ci_sessions y los valores son los valores a actualizar
     */
    public function setCi_sessions(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["ip_address"]))
            $retorno = "ip_address";
        else if (!isset($arrCamposValores["user_agent"]))
            $retorno = "user_agent";
        else if (!isset($arrCamposValores["last_activity"]))
            $retorno = "last_activity";
        else if (!isset($arrCamposValores["user_data"]))
            $retorno = "user_data";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setCi_sessions");
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
    * retorna los campos presentes en la tabla ci_sessions en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposCi_sessions(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.ci_sessions");
    }

    /**
    * Buscar registros en la tabla ci_sessions
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de ci_sessions o la cantdad de registros segun el parametro contar
    */
    static function listarCi_sessions(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.ci_sessions", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>