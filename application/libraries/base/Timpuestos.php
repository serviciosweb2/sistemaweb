<?php

/**
* Class Timpuestos
*
*Class  Timpuestos maneja todos los aspectos de impuestos
*
* @package  SistemaIGA
* @subpackage Impuestos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Timpuestos extends class_general{

    /**
    * codigo de impuestos
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * nombre de impuestos
    * @var nombre varchar
    * @access public
    */
    public $nombre;

    /**
    * valor de impuestos
    * @var valor decimal
    * @access public
    */
    public $valor;

    /**
    * tipo de impuestos
    * @var tipo enum
    * @access public
    */
    public $tipo;

    /**
    * baja de impuestos
    * @var baja smallint
    * @access public
    */
    public $baja;

    /**
     * relación a general.impuestos_general
     * @var cod_impuesto int
     * @access public
     */
    public $cod_impuesto;


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
    protected $nombreTabla = 'impuestos';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase impuestos
     *
     * @param CI_DB_mysqli_driver $connection
     * @param integer $codigo (opcional) el codigo del objeto a crear
     */
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null){
        $this->oConnection = $conexion;
        if ($codigo != null && $codigo != -1) {
            $arrConstructor = $this->_constructor($codigo);
            if (count($arrConstructor) > 0) {
                $this->codigo       = $arrConstructor[0]['codigo'];
                $this->nombre       = $arrConstructor[0]['nombre'];
                $this->valor        = $arrConstructor[0]['valor'];
                $this->tipo         = $arrConstructor[0]['tipo'];
                $this->baja         = $arrConstructor[0]['baja'];
                $this->cod_impuesto = $arrConstructor[0]['cod_impuesto'];
            }else {
                $this->codigo = -1;
            }
        }else {
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
        $arrTemp['nombre'] = $this->nombre;
        $arrTemp['valor'] = $this->valor;
        $arrTemp['tipo'] = $this->tipo;
        $arrTemp['baja'] = $this->baja == '' ? '0' : $this->baja;
        $arrTemp['cod_impuesto'] = empty($this->cod_impuesto) ? null : $this->cod_impuesto;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase impuestos o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarImpuestos(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto impuestos
     *
     * @return integer
     */
    public function getCodigoImpuestos(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de impuestos según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de impuestos y los valores son los valores a actualizar
     */
    public function setImpuestos(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["nombre"]))
            $retorno = "nombre";
        else if (!isset($arrCamposValores["valor"]))
            $retorno = "valor";
        else if (!isset($arrCamposValores["tipo"]))
            $retorno = "tipo";
        else if (!isset($arrCamposValores["baja"]))
            $retorno = "baja";
        else if (!isset($arrCamposValores['cod_impuesto']))
            $retorno = 'cod_impuesto';
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setImpuestos");
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
    * retorna los campos presentes en la tabla impuestos en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposImpuestos(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "impuestos");
    }

    /**
    * Buscar registros en la tabla impuestos
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de impuestos o la cantdad de registros segun el parametro contar
    */
    static function listarImpuestos(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "impuestos", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>