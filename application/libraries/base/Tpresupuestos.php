<?php

/**
* Class Tpresupuestos
*
*Class  Tpresupuestos maneja todos los aspectos de presupuestos
*
* @package  SistemaIGA
* @subpackage Presupuestos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tpresupuestos extends class_general{

    /**
    * codigo de presupuestos
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * codcomision de presupuestos
    * @var codcomision int (requerido)
    * @access public
    */
    public $codcomision;

    /**
    * fecha de presupuestos
    * @var fecha datetime (requerido)
    * @access public
    */
    public $fecha;

    /**
    * observaciones de presupuestos
    * @var observaciones varchar (requerido)
    * @access public
    */
    public $observaciones;

    /**
    * cod_plan de presupuestos
    * @var cod_plan int
    * @access public
    */
    public $cod_plan;

    /**
    * fecha_vigencia de presupuestos
    * @var fecha_vigencia date (requerido)
    * @access public
    */
    public $fecha_vigencia;


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
    protected $nombreTabla = 'presupuestos';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase presupuestos
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
                $this->codcomision = $arrConstructor[0]['codcomision'];
                $this->fecha = $arrConstructor[0]['fecha'];
                $this->observaciones = $arrConstructor[0]['observaciones'];
                $this->cod_plan = $arrConstructor[0]['cod_plan'];
                $this->fecha_vigencia = $arrConstructor[0]['fecha_vigencia'];
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
        $arrTemp['codcomision'] = $this->codcomision == '' ? null : $this->codcomision;
        $arrTemp['fecha'] = $this->fecha == '' ? null : $this->fecha;
        $arrTemp['observaciones'] = $this->observaciones == '' ? null : $this->observaciones;
        $arrTemp['cod_plan'] = $this->cod_plan;
        $arrTemp['fecha_vigencia'] = $this->fecha_vigencia == '' ? null : $this->fecha_vigencia;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase presupuestos o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarPresupuestos(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto presupuestos
     *
     * @return integer
     */
    public function getCodigoPresupuestos(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de presupuestos según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de presupuestos y los valores son los valores a actualizar
     */
    public function setPresupuestos(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_plan"]))
            $retorno = "cod_plan";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setPresupuestos");
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
    * retorna los campos presentes en la tabla presupuestos en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposPresupuestos(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "presupuestos");
    }

    /**
    * Buscar registros en la tabla presupuestos
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de presupuestos o la cantdad de registros segun el parametro contar
    */
    static function listarPresupuestos(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "presupuestos", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>