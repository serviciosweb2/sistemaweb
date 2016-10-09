<?php

/**
* Class Ttipos_facturas
*
*Class  Ttipos_facturas maneja todos los aspectos de tipos_facturas
*
* @package  SistemaIGA
* @subpackage Tipos_facturas
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Ttipos_facturas extends class_general{

    /**
    * codigo de tipos_facturas
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * factura de tipos_facturas
    * @var factura varchar
    * @access public
    */
    public $factura;

    /**
    * discrimina_iva de tipos_facturas
    * @var discrimina_iva smallint
    * @access public
    */
    public $discrimina_iva;

    /**
    * discrimina_otroimpuesto de tipos_facturas
    * @var discrimina_otroimpuesto smallint
    * @access public
    */
    public $discrimina_otroimpuesto;

    /**
    * codigocontrol de tipos_facturas
    * @var codigocontrol int (requerido)
    * @access public
    */
    public $codigocontrol;

    /**
    * habilitado de tipos_facturas
    * @var habilitado smallint
    * @access public
    */
    public $habilitado;

    /**
    * cod_afip de tipos_facturas
    * @var cod_afip int 
    * @access public
    */
    public $cod_afip;
    
     /**
    * comprobante de tipos_facturas
    * @var comprobante enum
    * @access public
    */
    public $comprobante;
    
     /**
    * tipo de tipos_facturas
    * @var tipo trol enum
    * @access public
    */
    public $tipo;
    
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
    protected $nombreTabla = 'general.tipos_facturas';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase tipos_facturas
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
                $this->factura = $arrConstructor[0]['factura'];
                $this->discrimina_iva = $arrConstructor[0]['discrimina_iva'];
                $this->discrimina_otroimpuesto = $arrConstructor[0]['discrimina_otroimpuesto'];
                $this->codigocontrol = $arrConstructor[0]['codigocontrol'];
                $this->habilitado = $arrConstructor[0]['habilitado'];
                $this->cod_afip = $arrConstructor[0]['cod_afip'];
                $this->tipo = $arrConstructor[0]['tipo'];
                $this->comprobante = $arrConstructor[0]['comprobante'];
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
        $arrTemp['factura'] = $this->factura;
        $arrTemp['discrimina_iva'] = $this->discrimina_iva;
        $arrTemp['discrimina_otroimpuesto'] = $this->discrimina_otroimpuesto;
        $arrTemp['codigocontrol'] = $this->codigocontrol == '' ? null : $this->codigocontrol;
        $arrTemp['habilitado'] = $this->habilitado == '' ? '1' : $this->habilitado;
        $arrTemp['cod_afip'] = $this->cod_afip == '' ? null : $this->cod_afip;
        $arrTemp['tipo'] = $this->tipo == '' ? null : $this->tipo;
        $arrTemp['comprobante'] = $this->comprobante;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase tipos_facturas o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarTipos_facturas(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto tipos_facturas
     *
     * @return integer
     */
    public function getCodigoTipos_facturas(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de tipos_facturas según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de tipos_facturas y los valores son los valores a actualizar
     */
    public function setTipos_facturas(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["factura"]))
            $retorno = "factura";
        else if (!isset($arrCamposValores["discrimina_iva"]))
            $retorno = "discrimina_iva";
        else if (!isset($arrCamposValores["discrimina_otroimpuesto"]))
            $retorno = "discrimina_otroimpuesto";
        else if (!isset($arrCamposValores["habilitado"]))
            $retorno = "habilitado";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setTipos_facturas");
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
    * retorna los campos presentes en la tabla tipos_facturas en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposTipos_facturas(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.tipos_facturas");
    }

    /**
    * Buscar registros en la tabla tipos_facturas
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de tipos_facturas o la cantdad de registros segun el parametro contar
    */
    static function listarTipos_facturas(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.tipos_facturas", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>