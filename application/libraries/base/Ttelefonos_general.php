<?php

/**
* Class Ttelefonos_general
*
*Class  Ttelefonos_general maneja todos los aspectos de telefonos_general
*
* @package  SistemaIGA
* @subpackage Telefonos_general
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Ttelefonos_general extends class_general{

    /**
    * codigo de telefonos_general
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_area de telefonos_general
    * @var cod_area int
    * @access public
    */
    public $cod_area;

    /**
    * numero de telefonos_general
    * @var numero int
    * @access public
    */
    public $numero;

    /**
    * tipo_telefono de telefonos_general
    * @var tipo_telefono int
    * @access public
    */
    public $tipo_telefono;

    /**
    * empresa de telefonos_general
    * @var empresa int (requerido)
    * @access public
    */
    public $empresa;


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
    protected $nombreTabla = 'general.telefonos_general';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase telefonos_general
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
                $this->cod_area = $arrConstructor[0]['cod_area'];
                $this->numero = $arrConstructor[0]['numero'];
                $this->tipo_telefono = $arrConstructor[0]['tipo_telefono'];
                $this->empresa = $arrConstructor[0]['empresa'];
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
        $arrTemp['cod_area'] = $this->cod_area;
        $arrTemp['numero'] = $this->numero;
        $arrTemp['tipo_telefono'] = $this->tipo_telefono;
        $arrTemp['empresa'] = $this->empresa == '' ? null : $this->empresa;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase telefonos_general o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarTelefonos_general(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto telefonos_general
     *
     * @return integer
     */
    public function getCodigoTelefonos_general(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de telefonos_general según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de telefonos_general y los valores son los valores a actualizar
     */
    public function setTelefonos_general(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_area"]))
            $retorno = "cod_area";
        else if (!isset($arrCamposValores["numero"]))
            $retorno = "numero";
        else if (!isset($arrCamposValores["tipo_telefono"]))
            $retorno = "tipo_telefono";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setTelefonos_general");
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
    * retorna los campos presentes en la tabla telefonos_general en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposTelefonos_general(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.telefonos_general");
    }

    /**
    * Buscar registros en la tabla telefonos_general
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de telefonos_general o la cantdad de registros segun el parametro contar
    */
    static function listarTelefonos_general(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.telefonos_general", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>