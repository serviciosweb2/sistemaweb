<?php

/**
* Class Tcaja_historico
*
*Class  Tcaja_historico maneja todos los aspectos de caja_historico
*
* @package  SistemaIGA
* @subpackage Caja_historico
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tcaja_historico extends class_general{

    /**
    * codigo de caja_historico
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_usuario de caja_historico
    * @var cod_usuario int
    * @access public
    */
    public $cod_usuario;

    /**
    * fecha_hora de caja_historico
    * @var fecha_hora datetime
    * @access public
    */
    public $fecha_hora;

    /**
    * cod_caja de caja_historico
    * @var cod_caja int
    * @access public
    */
    public $cod_caja;

    /**
    * accion de caja_historico
    * @var accion int
    * @access public
    */
    public $accion;


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
    protected $nombreTabla = 'caja_historico';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase caja_historico
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
                $this->cod_usuario = $arrConstructor[0]['cod_usuario'];
                $this->fecha_hora = $arrConstructor[0]['fecha_hora'];
                $this->cod_caja = $arrConstructor[0]['cod_caja'];
                $this->accion = $arrConstructor[0]['accion'];
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
        $arrTemp['cod_usuario'] = $this->cod_usuario;
        $arrTemp['fecha_hora'] = $this->fecha_hora;
        $arrTemp['cod_caja'] = $this->cod_caja;
        $arrTemp['accion'] = $this->accion;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase caja_historico o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarCaja_historico(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto caja_historico
     *
     * @return integer
     */
    public function getCodigoCaja_historico(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de caja_historico según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de caja_historico y los valores son los valores a actualizar
     */
    public function setCaja_historico(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_usuario"]))
            $retorno = "cod_usuario";
        else if (!isset($arrCamposValores["fecha_hora"]))
            $retorno = "fecha_hora";
        else if (!isset($arrCamposValores["cod_caja"]))
            $retorno = "cod_caja";
        else if (!isset($arrCamposValores["accion"]))
            $retorno = "accion";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setCaja_historico");
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
    * retorna los campos presentes en la tabla caja_historico en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposCaja_historico(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "caja_historico");
    }

    /**
    * Buscar registros en la tabla caja_historico
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de caja_historico o la cantdad de registros segun el parametro contar
    */
    static function listarCaja_historico(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "caja_historico", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>