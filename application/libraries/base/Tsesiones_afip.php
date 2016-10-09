<?php

/**
 * Class Tsesiones_afip
 *
 * Class  Tsesiones_afip maneja todos los aspectos de sesiones_afip
 *
 * @package  SistemaIGA
 * @subpackage sesiones_afip
 * @author   Foox
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Tsesiones_afip extends class_general {

    /**
     * id de sesiones_afip
     * @var codigo int
     * @access protected
     */
    protected $codigo;

    /**
     * cod_facturante de sesiones_afip
     * @var sesiones_afip int
     * @access public
     */
    public $cod_facturante;

    /**
     * generationTime de sesiones_afip
     * @var generationTime timestamp
     * @access public
     */
    public $generationTime;

    /**
     * expirationTime de sesiones_afip
     * @var generationTime timestamp
     * @access public
     */
    public $expirationTime;

    /**
     * token de sesiones_afip
     * @var token varchar
     * @access public
     */
    public $token;

    /**
     * sign de sesiones_afip
     * @var sign varchar
     * @access public
     */
    public $sign;

    /**
     * uniqueId de sesiones_afip
     * @var uniqueID int 
     * @access public
     */
    public $uniqueId;

   
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
     * @var sesiones_afip varchar
     * @access protected
     */
    protected $nombreTabla = 'general.sesiones_afip';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase sesiones_afip
     *
     * @param CI_DB_mysqli_driver $connection
     * @param integer $codigo (opcional) el codigo del objeto a crear
     */
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        $this->oConnection = $conexion;
        if ($codigo != null && $codigo != -1) {
            $arrConstructor = $this->_constructor($codigo);
            if (count($arrConstructor) > 0) {
                $this->codigo = $arrConstructor[0]['codigo'];
                $this->cod_facturante = $arrConstructor[0]['cod_facturante'];
                $this->generationTime = $arrConstructor[0]['generationTime'];
                $this->expirationTime = $arrConstructor[0]['expirationTime'];
                $this->token = $arrConstructor[0]['token'];
                $this->sign = $arrConstructor[0]['sign'];
                $this->uniqueId = $arrConstructor[0]['uniqueId'];
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
    protected function _getArrayDeObjeto() {
        $arrTemp = array();
        $arrTemp['cod_facturante'] = $this->cod_facturante;
        $arrTemp['generationTime'] = $this->generationTime;
        $arrTemp['expirationTime'] = $this->expirationTime;
        $arrTemp['token'] = $this->token;
        $arrTemp['sign'] = $this->sign;
        $arrTemp['uniqueId'] = $this->uniqueId;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */

    /**
     * Guarda un objeto nuevo de la clase sesiones_afip o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarSesiones_afip() {
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto seguimiento_afip
     *
     * @return integer
     */
    public function getCodigoSesiones_afip() {
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de sesiones_afip según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de sesiones_afip y los valores son los valores a actualizar
     */
    public function setSesiones_afip(array $arrCamposValores) {
        $retorno = "";
        if (!isset($arrCamposValores["cod_facturante"]))
            $retorno = "cod_facturante";
        else if (!isset($arrCamposValores["generationTime"]))
            $retorno = "generationTime";
        else if (!isset($arrCamposValores["expirationTime"]))
            $retorno = "expirationTime";
        else if (!isset($arrCamposValores["token"]))
            $retorno = "token";
        else if (!isset($arrCamposValores["sign"]))
            $retorno = "sign";
        else if (!isset($arrCamposValores["uniqueId"]))
            $retorno = "uniqueId";
        if ($retorno <> "") {
            die("falta el parametro " . $retorno . " en setSesiones_afip");
        } else {
            foreach ($this as $key => $value) {
                if (isset($arrCamposValores[$key])) {
                    $this->$key = $arrCamposValores[$key];
                }
            }
            return true;
        }
    }

    /* STATIC FUNCTIONS */

    /**
     * retorna los campos presentes en la tabla sesiones_afip en formato array
     * 
     * @param CI_DB_mysqli_driver $connection   La conexion actual
     * @return array
     */
    static function camposSesiones_afip(CI_DB_mysqli_driver $conexion) {
        return parent::_campos($conexion, "general.sesiones_afip");
    }

    /**
     * Buscar registros en la tabla sesiones_afip
     *
     * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
     * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
     * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
     * @param array $orden    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
     * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
     * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
     * @return mixed    Retorna la lista de seguimiento_dsf o la cantdad de registros segun el parametro contar
     */
    static function listarSesiones_afip(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false) {
        return parent::_listar($conexion, "general.sesiones_afip", $condiciones, $limite, $orden, $grupo, $contar);
    }

}

?>