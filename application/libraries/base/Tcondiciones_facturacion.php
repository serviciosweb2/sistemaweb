<?php

/**
* Class Tcondiciones_facturacion
*
*Class  Tcondiciones_facturacion maneja todos los aspectos de condiciones_facturacion
*
* @package  SistemaIGA
* @subpackage Condiciones_facturacion
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tcondiciones_facturacion extends class_general{

    /**
    * codigo de condiciones_facturacion
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_cond_facturante de condiciones_facturacion
    * @var cod_cond_facturante int
    * @access public
    */
    public $cod_cond_facturante;

    /**
    * cod_cond_facturado de condiciones_facturacion
    * @var cod_cond_facturado int
    * @access public
    */
    public $cod_cond_facturado;

    /**
    * cod_tipo_factura de condiciones_facturacion
    * @var cod_tipo_factura int
    * @access public
    */
    public $cod_tipo_factura;


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
    protected $nombreTabla = 'general.condiciones_facturacion';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase condiciones_facturacion
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
                $this->cod_cond_facturante = $arrConstructor[0]['cod_cond_facturante'];
                $this->cod_cond_facturado = $arrConstructor[0]['cod_cond_facturado'];
                $this->cod_tipo_factura = $arrConstructor[0]['cod_tipo_factura'];
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
        $arrTemp['cod_cond_facturante'] = $this->cod_cond_facturante;
        $arrTemp['cod_cond_facturado'] = $this->cod_cond_facturado;
        $arrTemp['cod_tipo_factura'] = $this->cod_tipo_factura;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase condiciones_facturacion o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarCondiciones_facturacion(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto condiciones_facturacion
     *
     * @return integer
     */
    public function getCodigoCondiciones_facturacion(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de condiciones_facturacion según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de condiciones_facturacion y los valores son los valores a actualizar
     */
    public function setCondiciones_facturacion(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_cond_facturante"]))
            $retorno = "cod_cond_facturante";
        else if (!isset($arrCamposValores["cod_cond_facturado"]))
            $retorno = "cod_cond_facturado";
        else if (!isset($arrCamposValores["cod_tipo_factura"]))
            $retorno = "cod_tipo_factura";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setCondiciones_facturacion");
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
    * retorna los campos presentes en la tabla condiciones_facturacion en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposCondiciones_facturacion(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.condiciones_facturacion");
    }

    /**
    * Buscar registros en la tabla condiciones_facturacion
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de condiciones_facturacion o la cantdad de registros segun el parametro contar
    */
    static function listarCondiciones_facturacion(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.condiciones_facturacion", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>