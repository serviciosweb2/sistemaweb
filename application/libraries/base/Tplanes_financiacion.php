<?php

/**
 * Class Tplanes_financiacion
 *
 * Class  Tplanes_financiacion maneja todos los aspectos de planes_financiacion
 *
 * @package  SistemaIGA
 * @subpackage Planes_financiacion
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Tplanes_financiacion extends class_general {

    /**
     * codigo_plan de planes_financiacion
     * @var codigo_plan int
     * @access public
     */
    public $codigo_plan;

    /**
     * nrocuota de planes_financiacion
     * @var nro_cuota int
     * @access public
     */
    public $nro_cuota;

    /**
     * valorcuota de planes_financiacion
     * @var valor double
     * @access public
     */
    public $valor;

    /**
     * cod_concepto de planes_financiacion
     * @var codigo_concepto int (requerido)
     * @access public
     */
    public $codigo_concepto;

    /**
     * orden de planes_financiacion
     * @var orden int 
     * @access public
     */
    public $orden;

    /**
     * cod_concepto de planes_financiacion
     * @var codigo_financiacion int (requerido)
     * @access public
     */
    public $codigo_financiacion;

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
    protected $nombreTabla = 'planes_financiacion';

    /**
     * Indica si el objeto se encuentra guardado en la base de datos o no (utilizado en guardar para hacer el correspondiente insert o update)
     * 
     * @var exists bool
     */
    private $exists = false;

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase planes_financiacion
     *
     * @param CI_DB_mysqli_driver $connection
     * @param integer $codigo (opcional) el codigo del objeto a crear
     */
    function __construct(CI_DB_mysqli_driver $conexion, $codigo_plan = null, $codigo_concepto = null, $codigo_financiacion = null, $nro_cuota = null) {
        $this->oConnection = $conexion;
        $arrConstructor = $this->_constructor($codigo_plan, $codigo_concepto, $codigo_financiacion, $nro_cuota);
        if (count($arrConstructor) > 0) {
            $this->codigo_plan = $arrConstructor[0]['codigo_plan'];
            $this->nro_cuota = $arrConstructor[0]['nro_cuota'];
            $this->valor = $arrConstructor[0]['valor'];
            $this->orden = $arrConstructor[0]['orden'];
            $this->codigo_concepto = $arrConstructor[0]['codigo_concepto'];
            $this->codigo_financiacion = $arrConstructor[0]['codigo_financiacion'];
            $this->exists = true;
        } else {
            $this->codigo_plan = $codigo_plan;
            $this->nro_cuota = $nro_cuota;
            $this->codigo_financiacion = $codigo_financiacion;
            $this->codigo_concepto = $codigo_concepto;
            $this->exists = false;
        }
    }

    /* PORTECTED FUNCTIONS */

    /**
     * Metodo _construct overwrite de class general para TPlanes_cuotas
     * 
     * @param integer $codigo_plan
     * @param integer $nrocuota
     * @param integer $cuota
     * 
     * @return type
     */
    protected function _constructor($codigo_plan = null, $codigo_concepto = null, $codigo_financiacion = null, $nro_cuota = null) {
        $this->oConnection->select('*');
        $this->oConnection->from($this->nombreTabla);
        $this->oConnection->where(array('codigo_plan' => "$codigo_plan"));
        $this->oConnection->where(array('nro_cuota' => "$nro_cuota"));
        $this->oConnection->where(array('codigo_financiacion' => "$codigo_financiacion"));
        $this->oConnection->where(array('codigo_concepto' => "$codigo_concepto"));
        $query = $this->oConnection->get();
        $arrResp = $query->result_array();

        return $arrResp;
    }

    /**
     * Devuelve el objeto con todas sus propiedades y valores en formato array
     * 
     * @return array
     */
    protected function _getArrayDeObjeto() {
        $arrTemp = array();
        $arrTemp['codigo_plan'] = $this->codigo_plan == '' ? null : $this->codigo_plan;
        $arrTemp['nro_cuota'] = $this->nro_cuota;
        $arrTemp['valor'] = $this->valor;
        $arrTemp['orden'] = $this->orden;
        $arrTemp['codigo_financiacion'] = $this->codigo_financiacion == '' ? null : $this->codigo_financiacion;
        $arrTemp['codigo_concepto'] = $this->codigo_concepto == '' ? null : $this->codigo_concepto;
        return $arrTemp;
    }

    /**
     * Metodo overwrite de class general para TPlanes_financiacion
     * Guarda el nuevo objeto en la base de datos y le asigna el codigo obtenido
     * 
     * @return boolean
     */
    protected function _insertar() {
        if ($this->oConnection->insert($this->nombreTabla, $this->_getArrayDeObjeto())) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Metodo overwrite de class general para TPlanes_financiacion
     * Actualiza los valores de las propiedades del objeto en la base de datos
     * 
     * @return boolean
     */
    protected function _actualizar() {
        return $this->oConnection->update($this->nombreTabla, $this->_getArrayDeObjeto(), "codigo_financiacion = {$this->codigo_financiacion} AND nro_cuota = {$this->nro_cuota} AND codigo_plan = {$this->codigo_plan} AND codigo_concepto = {$this->codigo_concepto}");
    }

    /* PUBLIC FUNCTIONS */

    /**
     * Guarda un objeto nuevo de la clase planes_financiacion o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarPlanes_financiacion() {
        if ($this->exists) {
            return $this->_actualizar();
        } else {
            return $this->_insertar();
        }
    }

    /**
     * actualiza los campos de planes_financiacion según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de planes_financiacion y los valores son los valores a actualizar
     */
    public function setPlanes_financiacion(array $arrCamposValores) {
        $retorno = "";
        if (!isset($arrCamposValores["codigo_plan"]))
            $retorno = "codigo_plan";
        else if (!isset($arrCamposValores["nro_cuota"]))
            $retorno = "nro_cuota";
        else if (!isset($arrCamposValores["valor"]))
            $retorno = "valor";
        else if (!isset($arrCamposValores["orden"]))
            $retorno = "orden";
        else if (!isset($arrCamposValores["codigo_financiacion"]))
            $retorno = "codigo_financiacion";
        else if (!isset($arrCamposValores["codigo_concepto"]))
            $retorno = "codigo_concepto";
        if ($retorno <> "") {
            die("falta el parametro " . $retorno . " en setPlanes_financiacion");
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
     * retorna los campos presentes en la tabla planes_codigo_financiacion en formato array
     * 
     * @param CI_DB_mysqli_driver $connection   La conexion actual
     * @return array
     */
    static function camposPlanes_financiacion(CI_DB_mysqli_driver $conexion) {
        return parent::_campos($conexion, "planes_financiacion");
    }

    /**
     * Buscar registros en la tabla planes_financiacion
     *
     * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
     * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
     * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
     * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
     * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
     * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
     * @return mixed    Retorna la lista de planes_financiacion o la cantdad de registros segun el parametro contar
     */
    static function listarPlanes_financiacion(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false) {
        return parent::_listar($conexion, "planes_financiacion", $condiciones, $limite, $orden, $grupo, $contar);
    }

}

?>