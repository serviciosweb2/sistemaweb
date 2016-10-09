<?php

/**
* Class Tctacte_otros
*
*Class  Tctacte_otros maneja todos los aspectos de ctacte_otros
*
* @package  SistemaIGA
* @subpackage Ctacte_otros
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tctacte_otros extends class_general{

    /**
    * codigo de ctacte_otros
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_concepto de ctacte_otros
    * @var cod_concepto int
    * @access public
    */
    public $cod_concepto;

    /**
    * cod_usuario de ctacte_otros
    * @var cod_usuario int (requerido)
    * @access public
    */
    public $cod_usuario;

    /**
    * fecha_hora de ctacte_otros
    * @var fecha_hora datetime (requerido)
    * @access public
    */
    public $fecha_hora;


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
    protected $nombreTabla = 'ctacte_otros';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase ctacte_otros
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
                $this->cod_concepto = $arrConstructor[0]['cod_concepto'];
                $this->cod_usuario = $arrConstructor[0]['cod_usuario'];
                $this->fecha_hora = $arrConstructor[0]['fecha_hora'];
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
        $arrTemp['cod_concepto'] = $this->cod_concepto;
        $arrTemp['cod_usuario'] = $this->cod_usuario == '' ? null : $this->cod_usuario;
        $arrTemp['fecha_hora'] = $this->fecha_hora == '' ? null : $this->fecha_hora;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase ctacte_otros o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarCtacte_otros(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto ctacte_otros
     *
     * @return integer
     */
    public function getCodigoCtacte_otros(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de ctacte_otros según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de ctacte_otros y los valores son los valores a actualizar
     */
    public function setCtacte_otros(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_concepto"]))
            $retorno = "cod_concepto";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setCtacte_otros");
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
    * retorna los campos presentes en la tabla ctacte_otros en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposCtacte_otros(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "ctacte_otros");
    }

    /**
    * Buscar registros en la tabla ctacte_otros
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de ctacte_otros o la cantdad de registros segun el parametro contar
    */
    static function listarCtacte_otros(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "ctacte_otros", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>