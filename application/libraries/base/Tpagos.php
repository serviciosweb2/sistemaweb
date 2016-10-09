<?php

/**
* Class Tpagos
*
*Class  Tpagos maneja todos los aspectos de pagos
*
* @package  SistemaIGA
* @subpackage Pagos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tpagos extends class_general{

    /**
    * codigo de pagos
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * fecha_pago de pagos
    * @var fecha_pago date
    * @access public
    */
    public $fecha_pago;

    /**
    * importe de pagos
    * @var importe double
    * @access public
    */
    public $importe;

    /**
    * estado de pagos
    * @var estado enum
    * @access public
    */
    public $estado;

    /**
    * fecha de pagos
    * @var fecha datetime
    * @access public
    */
    public $fecha;

    /**
    * cod_usuario de pagos
    * @var cod_usuario int (requerido)
    * @access public
    */
    public $cod_usuario;

    /**
    * medio_pago de pagos
    * @var medio_pago int
    * @access public
    */
    public $medio_pago;

    /**
    * concepto de pagos
    * @var concepto enum
    * @access public
    */
    public $concepto;

    /**
    * cod_concepto de pagos
    * @var cod_concepto int
    * @access public
    */
    public $cod_concepto;

    /**
    * cod_caja de pagos
    * @var cod_caja int
    * @access public
    */
    public $cod_caja;


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
    protected $nombreTabla = 'pagos';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase pagos
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
                $this->fecha_pago = $arrConstructor[0]['fecha_pago'];
                $this->importe = $arrConstructor[0]['importe'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->fecha = $arrConstructor[0]['fecha'];
                $this->cod_usuario = $arrConstructor[0]['cod_usuario'];
                $this->medio_pago = $arrConstructor[0]['medio_pago'];
                $this->concepto = $arrConstructor[0]['concepto'];
                $this->cod_concepto = $arrConstructor[0]['cod_concepto'];
                $this->cod_caja = $arrConstructor[0]['cod_caja'];
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
        $arrTemp['fecha_pago'] = $this->fecha_pago;
        $arrTemp['importe'] = $this->importe;
        $arrTemp['estado'] = $this->estado == '' ? 'confirmado' : $this->estado;
        $arrTemp['fecha'] = $this->fecha;
        $arrTemp['cod_usuario'] = $this->cod_usuario == '' ? null : $this->cod_usuario;
        $arrTemp['medio_pago'] = $this->medio_pago;
        $arrTemp['concepto'] = $this->concepto;
        $arrTemp['cod_concepto'] = $this->cod_concepto;
        $arrTemp['cod_caja'] = $this->cod_caja;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase pagos o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarPagos(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto pagos
     *
     * @return integer
     */
    public function getCodigoPagos(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de pagos según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de pagos y los valores son los valores a actualizar
     */
    public function setPagos(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["fecha_pago"]))
            $retorno = "fecha_pago";
        else if (!isset($arrCamposValores["importe"]))
            $retorno = "importe";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        else if (!isset($arrCamposValores["fecha"]))
            $retorno = "fecha";
        else if (!isset($arrCamposValores["medio_pago"]))
            $retorno = "medio_pago";
        else if (!isset($arrCamposValores["concepto"]))
            $retorno = "concepto";
        else if (!isset($arrCamposValores["cod_concepto"]))
            $retorno = "cod_concepto";
        else if (!isset($arrCamposValores["cod_caja"]))
            $retorno = "cod_caja";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setPagos");
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
    * retorna los campos presentes en la tabla pagos en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposPagos(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "pagos");
    }

    /**
    * Buscar registros en la tabla pagos
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de pagos o la cantdad de registros segun el parametro contar
    */
    static function listarPagos(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "pagos", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>