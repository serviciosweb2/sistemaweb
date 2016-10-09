<?php

/**
* Class Tpos_terminales
*
*Class  Tpos_terminales maneja todos los aspectos de pos_terminales
*
* @package  SistemaIGA
* @subpackage Pos_terminales
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tpos_terminales extends class_general{

    /**
    * codigo de pos_terminales
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_punto_venta de pos_terminales
    * @var cod_punto_venta int
    * @access public
    */
    public $cod_punto_venta;

    /**
    * cod_interno de pos_terminales
    * @var cod_interno varchar (requerido)
    * @access public
    */
    public $cod_interno;

    /**
    * estado de pos_terminales
    * @var estado enum
    * @access public
    */
    public $estado;

    /**
    * tipo_captura de pos_terminales
    * @var tipo_captura enum
    * @access public
    */
    public $tipo_captura;


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
    protected $nombreTabla = 'pos_terminales';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase pos_terminales
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
                $this->cod_punto_venta = $arrConstructor[0]['cod_punto_venta'];
                $this->cod_interno = $arrConstructor[0]['cod_interno'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->tipo_captura = $arrConstructor[0]['tipo_captura'];
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
        $arrTemp['cod_punto_venta'] = $this->cod_punto_venta;
        $arrTemp['cod_interno'] = $this->cod_interno == '' ? null : $this->cod_interno;
        $arrTemp['estado'] = $this->estado == '' ? 'habilitado' : $this->estado;
        $arrTemp['tipo_captura'] = $this->tipo_captura;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase pos_terminales o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarPos_terminales(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto pos_terminales
     *
     * @return integer
     */
    public function getCodigoPos_terminales(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de pos_terminales según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de pos_terminales y los valores son los valores a actualizar
     */
    public function setPos_terminales(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_punto_venta"]))
            $retorno = "cod_punto_venta";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        else if (!isset($arrCamposValores["tipo_captura"]))
            $retorno = "tipo_captura";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setPos_terminales");
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
    * retorna los campos presentes en la tabla pos_terminales en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposPos_terminales(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "pos_terminales");
    }

    /**
    * Buscar registros en la tabla pos_terminales
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de pos_terminales o la cantdad de registros segun el parametro contar
    */
    static function listarPos_terminales(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "pos_terminales", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>