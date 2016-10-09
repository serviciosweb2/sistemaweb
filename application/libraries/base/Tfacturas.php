<?php

/**
* Class Tfacturas
*
*Class  Tfacturas maneja todos los aspectos de facturas
*
* @package  SistemaIGA
* @subpackage Facturas
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tfacturas extends class_general{

    /**
    * codigo de facturas
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * fecha de facturas
    * @var fecha date
    * @access public
    */
    public $fecha;

    /**
    * codrazsoc de facturas
    * @var codrazsoc int
    * @access public
    */
    public $codrazsoc;

    /**
    * total de facturas
    * @var total double
    * @access public
    */
    public $total;

    /**
    * estado de facturas
    * @var estado enum
    * @access public
    */
    public $estado;

    /**
    * fechareal de facturas
    * @var fechareal datetime
    * @access public
    */
    public $fechareal;

    /**
    * punto_venta de facturas
    * @var punto_venta int
    * @access public
    */
    public $punto_venta;

    /**
    * cod_usuario de facturas
    * @var cod_usuario int (requerido)
    * @access public
    */
    public $cod_usuario;


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
    protected $nombreTabla = 'facturas';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase facturas
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
                $this->fecha = $arrConstructor[0]['fecha'];
                $this->codrazsoc = $arrConstructor[0]['codrazsoc'];
                $this->total = $arrConstructor[0]['total'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->fechareal = $arrConstructor[0]['fechareal'];
                $this->punto_venta = $arrConstructor[0]['punto_venta'];
                $this->cod_usuario = $arrConstructor[0]['cod_usuario'];
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
        $arrTemp['fecha'] = $this->fecha;
        $arrTemp['codrazsoc'] = $this->codrazsoc;
        $arrTemp['total'] = $this->total;
        $arrTemp['estado'] = $this->estado == '' ? 'habilitada' : $this->estado;
        $arrTemp['fechareal'] = $this->fechareal;
        $arrTemp['punto_venta'] = $this->punto_venta;
        $arrTemp['cod_usuario'] = $this->cod_usuario == '' ? null : $this->cod_usuario;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase facturas o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarFacturas(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto facturas
     *
     * @return integer
     */
    public function getCodigoFacturas(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de facturas según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de facturas y los valores son los valores a actualizar
     */
    public function setFacturas(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["fecha"]))
            $retorno = "fecha";
        else if (!isset($arrCamposValores["codrazsoc"]))
            $retorno = "codrazsoc";
        else if (!isset($arrCamposValores["total"]))
            $retorno = "total";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        else if (!isset($arrCamposValores["fechareal"]))
            $retorno = "fechareal";
        else if (!isset($arrCamposValores["punto_venta"]))
            $retorno = "punto_venta";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setFacturas");
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
    * retorna los campos presentes en la tabla facturas en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposFacturas(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "facturas");
    }

    /**
    * Buscar registros en la tabla facturas
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de facturas o la cantdad de registros segun el parametro contar
    */
    static function         listarFacturas(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "facturas", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>