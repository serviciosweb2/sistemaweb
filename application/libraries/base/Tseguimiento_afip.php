<?php

/**
 * Class Tseguimiento_afip
 *
 * Class  Tseguimiento_afip maneja todos los aspectos de seguimiento_afip
 *
 * @package  SistemaIGA
 * @subpackage Seguimiento_afip
 * @author   Foox
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Tseguimiento_afip extends class_general {

    /**
     * codigo de seguimiento_afip
     * @var codigo int
     * @access protected
     */
    protected $codigo;

    /**
     * cod_filial de seguimiento_afip
     * @var cod_filial int
     * @access public
     */
    public $cod_filial;

    /**
     * cod_factura de seguimiento_afip
     * @var cod_factura int
     * @access public
     */
    public $cod_factura;

    /**
     * cod_sesion de seguimiento_afip
     * @var cod_sesion int
     * @access public
     */
    public $cod_sesion;

    /**
     * fecha_hora de seguimiento_afip
     * @var fecha_hora datetime
     * @access public
     */
    public $fecha_hora;

    /**
     * estado de seguimiento_afip
     * @var estado enum
     * @access public
     */
    public $estado;

    /**
     * errores de seguimiento_afip
     * @var erroes varchar 
     * @access public
     */
    public $errores;

    /**
     * cae de seguimiento_afip
     * @var cae varchar
     * @access public
     */
    public $cae;

    /**
     * vencimiento_cae de seguimiento_afip
     * @var vencimiento_cae datetime
     * @access public
     */
    public $vencimiento_cae;


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
    protected $nombreTabla = 'general.seguimiento_afip';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase seguimiento_afip
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
                $this->cod_filial = $arrConstructor[0]['cod_filial'];
                $this->cod_factura = $arrConstructor[0]['cod_factura'];
                $this->cod_sesion = $arrConstructor[0]['cod_sesion'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->fecha_hora = $arrConstructor[0]['fecha_hora'];
                $this->errores = $arrConstructor[0]['errores'];
                $this->cae = $arrConstructor[0]['cae'];
                $this->vencimiento_cae = $arrConstructor[0]['vencimiento_cae'];
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
        $arrTemp['cod_filial'] = $this->cod_filial;
        $arrTemp['cod_factura'] = $this->cod_factura == '' ? null : $this->cod_factura;
        $arrTemp['cod_sesion'] = $this->cod_sesion == '' ? null : $this->cod_sesion;
        $arrTemp['estado'] = $this->estado == '' ? null : $this->estado;
        $arrTemp['fecha_hora'] = $this->fecha_hora == '' ? null : $this->fecha_hora;
        $arrTemp['errores'] = $this->errores == '' ? null : $this->errores;
        $arrTemp['cae'] = $this->cae== '' ? null : $this->cae;
        $arrTemp['vencimiento_cae'] = $this->vencimiento_cae == '' ? null : $this->vencimiento_cae;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */

    /**
     * Guarda un objeto nuevo de la clase seguimiento_afip o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarSeguimiento_afip() {
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto seguimiento_afip
     *
     * @return integer
     */
    public function getCodigoSeguimiento_afip() {
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de seguimiento_afip según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de seguimiento_afip y los valores son los valores a actualizar
     */
    public function setSeguimiento_afip(array $arrCamposValores) {
        $retorno = "";
        if ($retorno <> "") {
            die("falta el parametro " . $retorno . " en setSeguimiento_afip");
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
     * retorna los campos presentes en la tabla seguimiento_afip en formato array
     * 
     * @param CI_DB_mysqli_driver $connection   La conexion actual
     * @return array
     */
    static function camposSeguimiento_afip(CI_DB_mysqli_driver $conexion) {
        return parent::_campos($conexion, "general.seguimiento_afip");
    }

    /**
     * Buscar registros en la tabla seguimiento_afip
     *
     * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
     * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
     * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
     * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
     * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
     * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
     * @return mixed    Retorna la lista de seguimiento_dsf o la cantdad de registros segun el parametro contar
     */
    static function listarSeguimiento_afip(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false) {
        return parent::_listar($conexion, "general.seguimiento_afip", $condiciones, $limite, $orden, $grupo, $contar);
    }

}

?>