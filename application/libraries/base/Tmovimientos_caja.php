<?php

/**
* Class Tmovimientos_caja
*
*Class  Tmovimientos_caja maneja todos los aspectos de movimientos_caja
*
* @package  SistemaIGA
* @subpackage Movimientos_caja
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tmovimientos_caja extends class_general{

    /**
    * codigo de movimientos_caja
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * fecha_hora_real de movimientos_caja
    * @var fecha_hora_real datetime
    * @access public
    */
    public $fecha_hora_real;

    /**
    * cod_medio de movimientos_caja
    * @var cod_medio int
    * @access public
    */
    public $cod_medio;

    /**
    * debe de movimientos_caja
    * @var debe double
    * @access public
    */
    public $debe;

    /**
    * haber de movimientos_caja
    * @var haber double
    * @access public
    */
    public $haber;

    /**
    * observacion de movimientos_caja
    * @var observacion varchar (requerido)
    * @access public
    */
    public $observacion;

    /**
    * cod_user de movimientos_caja
    * @var cod_user int
    * @access public
    */
    public $cod_user;

    /**
    * cod_caja de movimientos_caja
    * @var cod_caja int
    * @access public
    */
    public $cod_caja;

    /**
    * cod_concepto de movimientos_caja
    * @var cod_concepto enum
    * @access public
    */
    public $cod_concepto;

    /**
    * concepto de movimientos_caja
    * @var concepto int (requerido)
    * @access public
    */
    public $concepto;

    /**
    * fecha_hora de movimientos_caja
    * @var fecha_hora datetime
    * @access public
    */
    public $fecha_hora;

    /**
    * saldo de movimientos_caja
    * @var saldo double
    * @access public
    */
    public $saldo;

    /**
    * codigo_apertura de movimientos_caja
    * @var codigo_apertura int
    * @access public
    */
    public $codigo_apertura;


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
    protected $nombreTabla = 'movimientos_caja';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase movimientos_caja
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
                $this->fecha_hora_real = $arrConstructor[0]['fecha_hora_real'];
                $this->cod_medio = $arrConstructor[0]['cod_medio'];
                $this->debe = $arrConstructor[0]['debe'];
                $this->haber = $arrConstructor[0]['haber'];
                $this->observacion = $arrConstructor[0]['observacion'];
                $this->cod_user = $arrConstructor[0]['cod_user'];
                $this->cod_caja = $arrConstructor[0]['cod_caja'];
                $this->cod_concepto = $arrConstructor[0]['cod_concepto'];
                $this->concepto = $arrConstructor[0]['concepto'];
                $this->fecha_hora = $arrConstructor[0]['fecha_hora'];
                $this->saldo = $arrConstructor[0]['saldo'];
                $this->codigo_apertura = $arrConstructor[0]['codigo_apertura'];
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
        $arrTemp['fecha_hora_real'] = $this->fecha_hora_real;
        $arrTemp['cod_medio'] = $this->cod_medio;
        $arrTemp['debe'] = $this->debe == '' ? '0.00' : $this->debe;
        $arrTemp['haber'] = $this->haber == '' ? '0.00' : $this->haber;
        $arrTemp['observacion'] = $this->observacion == '' ? null : $this->observacion;
        $arrTemp['cod_user'] = $this->cod_user == '' ? '1' : $this->cod_user;
        $arrTemp['cod_caja'] = $this->cod_caja == '' ? '1' : $this->cod_caja;
        $arrTemp['cod_concepto'] = $this->cod_concepto;
        $arrTemp['concepto'] = $this->concepto == '' ? null : $this->concepto;
        $arrTemp['fecha_hora'] = $this->fecha_hora;
        $arrTemp['saldo'] = $this->saldo == '' ? '0.00' : $this->saldo;
        $arrTemp['codigo_apertura'] = $this->codigo_apertura;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase movimientos_caja o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarMovimientos_caja(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto movimientos_caja
     *
     * @return integer
     */
    public function getCodigoMovimientos_caja(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de movimientos_caja según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de movimientos_caja y los valores son los valores a actualizar
     */
    public function setMovimientos_caja(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["fecha_hora_real"]))
            $retorno = "fecha_hora_real";
        else if (!isset($arrCamposValores["cod_medio"]))
            $retorno = "cod_medio";
        else if (!isset($arrCamposValores["debe"]))
            $retorno = "debe";
        else if (!isset($arrCamposValores["haber"]))
            $retorno = "haber";
        else if (!isset($arrCamposValores["cod_user"]))
            $retorno = "cod_user";
        else if (!isset($arrCamposValores["cod_caja"]))
            $retorno = "cod_caja";
        else if (!isset($arrCamposValores["cod_concepto"]))
            $retorno = "cod_concepto";
        else if (!isset($arrCamposValores["fecha_hora"]))
            $retorno = "fecha_hora";
        else if (!isset($arrCamposValores["saldo"]))
            $retorno = "saldo";
        else if (!isset($arrCamposValores["codigo_apertura"]))
            $retorno = "codigo_apertura";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setMovimientos_caja");
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
    * retorna los campos presentes en la tabla movimientos_caja en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposMovimientos_caja(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "movimientos_caja");
    }

    /**
    * Buscar registros en la tabla movimientos_caja
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de movimientos_caja o la cantdad de registros segun el parametro contar
    */
    static function listarMovimientos_caja(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "movimientos_caja", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>