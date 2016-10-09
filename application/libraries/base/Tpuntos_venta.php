<?php

/**
* Class Tpuntos_venta
*
*Class  Tpuntos_venta maneja todos los aspectos de puntos_venta
*
* @package  SistemaIGA
* @subpackage Puntos_venta
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tpuntos_venta extends class_general{

    /**
    * codigo de puntos_venta
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_facturante de puntos_venta
    * @var cod_facturante int
    * @access public
    */
    public $cod_facturante;

    /**
    * medio de puntos_venta
    * @var medio enum
    * @access public
    */
    public $medio;

    /**
    * tipo_factura de puntos_venta
    * @var tipo_factura int
    * @access public
    */
    public $tipo_factura;
   
    /**
    * nro de puntos_venta
    * @var nro int
    * @access public
    */
    public $nro;

    /**
    * prefijo de puntos_venta
    * @var prefijo varchar
    * @access public
    */
    public $prefijo;

    /**
    * estado de puntos_venta
    * @var estado enum
    * @access public
    */
    public $estado;

    /**
    * webservice de puntos_venta
    * @var webservice smallint (requerido)
    * @access public
    */
    public $webservice;


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
    protected $nombreTabla = 'general.puntos_venta';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase puntos_venta
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
                $this->cod_facturante = $arrConstructor[0]['cod_facturante'];
                $this->medio = $arrConstructor[0]['medio'];
                $this->tipo_factura = $arrConstructor[0]['tipo_factura'];
                $this->nro = $arrConstructor[0]['nro'];
                $this->prefijo = $arrConstructor[0]['prefijo'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->webservice = $arrConstructor[0]['webservice'];
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
        $arrTemp['cod_facturante'] = $this->cod_facturante;
        $arrTemp['medio'] = $this->medio;
        $arrTemp['tipo_factura'] = $this->tipo_factura;
        $arrTemp['nro'] = $this->nro;
        $arrTemp['prefijo'] = $this->prefijo;
        $arrTemp['estado'] = $this->estado == '' ? 'habilitado' : $this->estado;
        $arrTemp['webservice'] = $this->webservice == '' ? null : $this->webservice;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase puntos_venta o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarPuntos_venta(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto puntos_venta
     *
     * @return integer
     */
    public function getCodigoPuntos_venta(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de puntos_venta según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de puntos_venta y los valores son los valores a actualizar
     */
    public function setPuntos_venta(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_facturante"]))
            $retorno = "cod_facturante";
        else if (!isset($arrCamposValores["medio"]))
            $retorno = "medio";
        else if (!isset($arrCamposValores["tipo_factura"]))
            $retorno = "tipo_factura";
        else if (!isset($arrCamposValores["nro"]))
            $retorno = "nro";
        else if (!isset($arrCamposValores["prefijo"]))
            $retorno = "prefijo";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setPuntos_venta");
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
    * retorna los campos presentes en la tabla puntos_venta en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposPuntos_venta(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.puntos_venta");
    }

    /**
    * Buscar registros en la tabla puntos_venta
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de puntos_venta o la cantdad de registros segun el parametro contar
    */
    static function listarPuntos_venta(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.puntos_venta", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>