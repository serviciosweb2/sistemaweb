<?php

/**
* Class Tplanes_certificacion
*
*Class  Tplanes_certificacion maneja todos los aspectos de planes_certificacion
*
* @package  SistemaIGA
* @subpackage Planes_certificacion
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tplanes_certificacion extends class_general{

    /**
    * codigo de planes_certificacion
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cuotas de planes_certificacion
    * @var cuotas int (requerido)
    * @access public
    */
    public $cuotas;

    /**
    * valor de planes_certificacion
    * @var valor double (requerido)
    * @access public
    */
    public $valor;

    /**
    * codcurso de planes_certificacion
    * @var codcurso int (requerido)
    * @access public
    */
    public $codcurso;

    /**
    * anio de planes_certificacion
    * @var anio int (requerido)
    * @access public
    */
    public $anio;

    /**
    * tipo_certifica de planes_certificacion
    * @var tipo_certifica int (requerido)
    * @access public
    */
    public $tipo_certifica;


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
    protected $nombreTabla = 'general.planes_certificacion';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase planes_certificacion
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
                $this->cuotas = $arrConstructor[0]['cuotas'];
                $this->valor = $arrConstructor[0]['valor'];
                $this->codcurso = $arrConstructor[0]['codcurso'];
                $this->anio = $arrConstructor[0]['anio'];
                $this->tipo_certifica = $arrConstructor[0]['tipo_certifica'];
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
        $arrTemp['cuotas'] = $this->cuotas == '' ? null : $this->cuotas;
        $arrTemp['valor'] = $this->valor == '' ? null : $this->valor;
        $arrTemp['codcurso'] = $this->codcurso == '' ? null : $this->codcurso;
        $arrTemp['anio'] = $this->anio == '' ? null : $this->anio;
        $arrTemp['tipo_certifica'] = $this->tipo_certifica == '' ? null : $this->tipo_certifica;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase planes_certificacion o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarPlanes_certificacion(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto planes_certificacion
     *
     * @return integer
     */
    public function getCodigoPlanes_certificacion(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de planes_certificacion según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de planes_certificacion y los valores son los valores a actualizar
     */
    public function setPlanes_certificacion(array $arrCamposValores){
        $retorno = "";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setPlanes_certificacion");
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
    * retorna los campos presentes en la tabla planes_certificacion en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposPlanes_certificacion(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.planes_certificacion");
    }

    /**
    * Buscar registros en la tabla planes_certificacion
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de planes_certificacion o la cantdad de registros segun el parametro contar
    */
    static function listarPlanes_certificacion(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.planes_certificacion", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>