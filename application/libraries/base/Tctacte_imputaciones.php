<?php

/**
* Class Tctacte_imputaciones
*
*Class  Tctacte_imputaciones maneja todos los aspectos de ctacte_imputaciones
*
* @package  SistemaIGA
* @subpackage Ctacte_imputaciones
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tctacte_imputaciones extends class_general{

    /**
    * codigo de ctacte_imputaciones
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_ctacte de ctacte_imputaciones
    * @var cod_ctacte int
    * @access public
    */
    public $cod_ctacte;

    /**
    * valor de ctacte_imputaciones
    * @var valor double
    * @access public
    */
    public $valor;

    /**
    * cod_cobro de ctacte_imputaciones
    * @var cod_cobro int
    * @access public
    */
    public $cod_cobro;

    /**
    * fecha de ctacte_imputaciones
    * @var fecha datetime
    * @access public
    */
    public $fecha;

    /**
    * cod_usuario de ctacte_imputaciones
    * @var cod_usuario int
    * @access public
    */
    public $cod_usuario;

    /**
    * estado de ctacte_imputaciones
    * @var estado enum
    * @access public
    */
    public $estado;

    /**
    * tipo de ctacte_imputaciones
    * @var tipo enum
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
    protected $nombreTabla = 'ctacte_imputaciones';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase ctacte_imputaciones
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
                $this->valor = $arrConstructor[0]['valor'];
                $this->cod_cobro = $arrConstructor[0]['cod_cobro'];
                $this->fecha = $arrConstructor[0]['fecha'];
                $this->cod_usuario = $arrConstructor[0]['cod_usuario'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->tipo = $arrConstructor[0]['tipo'];
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
        $arrTemp['valor'] = $this->valor == '' ? '0.00' : $this->valor;
        $arrTemp['cod_cobro'] = $this->cod_cobro;
        $arrTemp['fecha'] = $this->fecha == '' ? '0000-00-00 00:00:00' : $this->fecha;
        $arrTemp['cod_usuario'] = $this->cod_usuario;
        $arrTemp['estado'] = $this->estado == '' ? 'pendiente' : $this->estado;
        $arrTemp['tipo'] = $this->tipo == '' ? 'COBRO' : $this->tipo;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase ctacte_imputaciones o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarCtacte_imputaciones(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto ctacte_imputaciones
     *
     * @return integer
     */
    public function getCodigoCtacte_imputaciones(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de ctacte_imputaciones según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de ctacte_imputaciones y los valores son los valores a actualizar
     */
    public function setCtacte_imputaciones(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_ctacte"]))
            $retorno = "cod_ctacte";
        else if (!isset($arrCamposValores["valor"]))
            $retorno = "valor";
        else if (!isset($arrCamposValores["cod_cobro"]))
            $retorno = "cod_cobro";
        else if (!isset($arrCamposValores["fecha"]))
            $retorno = "fecha";
        else if (!isset($arrCamposValores["cod_usuario"]))
            $retorno = "cod_usuario";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        else if (!isset($arrCamposValores["tipo"]))
            $retorno = "tipo";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setCtacte_imputaciones");
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
    * retorna los campos presentes en la tabla ctacte_imputaciones en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposCtacte_imputaciones(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "ctacte_imputaciones");
    }

    /**
    * Buscar registros en la tabla ctacte_imputaciones
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de ctacte_imputaciones o la cantdad de registros segun el parametro contar
    */
    static function listarCtacte_imputaciones(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "ctacte_imputaciones", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>