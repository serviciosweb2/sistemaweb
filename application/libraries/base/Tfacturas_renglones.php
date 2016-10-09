<?php

/**
* Class Tfacturas_renglones
*
*Class  Tfacturas_renglones maneja todos los aspectos de facturas_renglones
*
* @package  SistemaIGA
* @subpackage Facturas_renglones
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tfacturas_renglones extends class_general{

    /**
    * codigo de facturas_renglones
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_ctacte de facturas_renglones
    * @var cod_ctacte int
    * @access public
    */
    public $cod_ctacte;

    /**
    * cod_factura de facturas_renglones
    * @var cod_factura int
    * @access public
    */
    public $cod_factura;

    /**
    * importe de facturas_renglones
    * @var importe double
    * @access public
    */
    public $importe;

    /**
    * anulada de facturas_renglones
    * @var anulada smallint
    * @access public
    */
    public $anulada;


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
    protected $nombreTabla = 'facturas_renglones';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase facturas_renglones
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
                $this->cod_ctacte = $arrConstructor[0]['cod_ctacte'];
                $this->cod_factura = $arrConstructor[0]['cod_factura'];
                $this->importe = $arrConstructor[0]['importe'];
                $this->anulada = $arrConstructor[0]['anulada'];
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
        $arrTemp['cod_ctacte'] = $this->cod_ctacte;
        $arrTemp['cod_factura'] = $this->cod_factura;
        $arrTemp['importe'] = $this->importe;
        $arrTemp['anulada'] = $this->anulada == '' ? '0' : $this->anulada;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase facturas_renglones o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarFacturas_renglones(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto facturas_renglones
     *
     * @return integer
     */
    public function getCodigoFacturas_renglones(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de facturas_renglones según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de facturas_renglones y los valores son los valores a actualizar
     */
    public function setFacturas_renglones(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_ctacte"]))
            $retorno = "cod_ctacte";
        else if (!isset($arrCamposValores["cod_factura"]))
            $retorno = "cod_factura";
        else if (!isset($arrCamposValores["importe"]))
            $retorno = "importe";
        else if (!isset($arrCamposValores["anulada"]))
            $retorno = "anulada";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setFacturas_renglones");
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
    * retorna los campos presentes en la tabla facturas_renglones en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposFacturas_renglones(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "facturas_renglones");
    }

    /**
    * Buscar registros en la tabla facturas_renglones
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de facturas_renglones o la cantdad de registros segun el parametro contar
    */
    static function listarFacturas_renglones(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "facturas_renglones", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>