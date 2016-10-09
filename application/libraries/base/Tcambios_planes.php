<?php

/**
* Class Tcambios_planes
*
*Class  Tcambios_planes maneja todos los aspectos de cambios_planes
*
* @package  SistemaIGA
* @subpackage Cambios_planes
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tcambios_planes extends class_general{

    /**
    * codigo de cambios_planes
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * financiacion de cambios_planes
    * @var financiacion int
    * @access public
    */
    public $financiacion;

    /**
    * codmatricula de cambios_planes
    * @var codmatricula int
    * @access public
    */
    public $codmatricula;

    /**
    * fecha de cambios_planes
    * @var fecha date
    * @access public
    */
    public $fecha;

    /**
    * codigo_plan de cambios_planes
    * @var codigo_plan int
    * @access public
    */
    public $codigo_plan;


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
    protected $nombreTabla = 'cambios_planes';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase cambios_planes
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
                $this->financiacion = $arrConstructor[0]['financiacion'];
                $this->codmatricula = $arrConstructor[0]['codmatricula'];
                $this->fecha = $arrConstructor[0]['fecha'];
                $this->codigo_plan = $arrConstructor[0]['codigo_plan'];
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
        $arrTemp['financiacion'] = $this->financiacion;
        $arrTemp['codmatricula'] = $this->codmatricula;
        $arrTemp['fecha'] = $this->fecha;
        $arrTemp['codigo_plan'] = $this->codigo_plan;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase cambios_planes o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarCambios_planes(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto cambios_planes
     *
     * @return integer
     */
    public function getCodigoCambios_planes(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de cambios_planes según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de cambios_planes y los valores son los valores a actualizar
     */
    public function setCambios_planes(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["financiacion"]))
            $retorno = "financiacion";
        else if (!isset($arrCamposValores["codmatricula"]))
            $retorno = "codmatricula";
        else if (!isset($arrCamposValores["fecha"]))
            $retorno = "fecha";
        else if (!isset($arrCamposValores["codigo_plan"]))
            $retorno = "codigo_plan";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setCambios_planes");
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
    * retorna los campos presentes en la tabla cambios_planes en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposCambios_planes(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "cambios_planes");
    }

    /**
    * Buscar registros en la tabla cambios_planes
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de cambios_planes o la cantdad de registros segun el parametro contar
    */
    static function listarCambios_planes(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "cambios_planes", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>