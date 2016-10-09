<?php

/**
* Class Tmedio_tarjetas
*
*Class  Tmedio_tarjetas maneja todos los aspectos de medio_tarjetas
*
* @package  SistemaIGA
* @subpackage Medio_tarjetas
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tmedio_tarjetas extends class_general{

    /**
    * codigo de medio_tarjetas
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_tipo de medio_tarjetas
    * @var cod_tipo int
    * @access public
    */
    public $cod_tipo;

    /**
    * cod_bco_emisor de medio_tarjetas
    * @var cod_bco_emisor int (requerido)
    * @access public
    */
    public $cod_bco_emisor;

    /**
    * cupon de medio_tarjetas
    * @var cupon varchar
    * @access public
    */
    public $cupon;

    /**
    * cod_cobro de medio_tarjetas
    * @var cod_cobro int (requerido)
    * @access public
    */
    public $cod_cobro;

    /**
    * cod_terminal de medio_tarjetas
    * @var cod_terminal int
    * @access public
    */
    public $cod_terminal;

    /**
    * cod_autorizacion de medio_tarjetas
    * @var cod_autorizacion varchar
    * @access public
    */
    public $cod_autorizacion;

    /**
    * cuotas de medio_tarjetas
    * @var cuotas int (requerido)
    * @access public
    */
    public $cuotas;


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
    protected $nombreTabla = 'medio_tarjetas';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase medio_tarjetas
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
                $this->cod_tipo = $arrConstructor[0]['cod_tipo'];
                $this->cod_bco_emisor = $arrConstructor[0]['cod_bco_emisor'];
                $this->cupon = $arrConstructor[0]['cupon'];
                $this->cod_cobro = $arrConstructor[0]['cod_cobro'];
                $this->cod_terminal = $arrConstructor[0]['cod_terminal'];
                $this->cod_autorizacion = $arrConstructor[0]['cod_autorizacion'];
                $this->cuotas = $arrConstructor[0]['cuotas'];
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
        $arrTemp['cod_tipo'] = $this->cod_tipo;
        $arrTemp['cod_bco_emisor'] = $this->cod_bco_emisor == '' ? null : $this->cod_bco_emisor;
        $arrTemp['cupon'] = $this->cupon;
        $arrTemp['cod_cobro'] = $this->cod_cobro == '' ? null : $this->cod_cobro;
        $arrTemp['cod_terminal'] = $this->cod_terminal;
        $arrTemp['cod_autorizacion'] = $this->cod_autorizacion;
        $arrTemp['cuotas'] = $this->cuotas == '' ? null : $this->cuotas;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase medio_tarjetas o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarMedio_tarjetas(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto medio_tarjetas
     *
     * @return integer
     */
    public function getCodigoMedio_tarjetas(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de medio_tarjetas según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de medio_tarjetas y los valores son los valores a actualizar
     */
    public function setMedio_tarjetas(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_tipo"]))
            $retorno = "cod_tipo";
        else if (!isset($arrCamposValores["cupon"]))
            $retorno = "cupon";
        else if (!isset($arrCamposValores["cod_terminal"]))
            $retorno = "cod_terminal";
        else if (!isset($arrCamposValores["cod_autorizacion"]))
            $retorno = "cod_autorizacion";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setMedio_tarjetas");
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
    * retorna los campos presentes en la tabla medio_tarjetas en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposMedio_tarjetas(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "medio_tarjetas");
    }

    /**
    * Buscar registros en la tabla medio_tarjetas
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de medio_tarjetas o la cantdad de registros segun el parametro contar
    */
    static function listarMedio_tarjetas(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "medio_tarjetas", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>