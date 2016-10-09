<?php

/**
* Class Tconceptos_factura_renglones
*
*Class  Tconceptos_factura_renglones maneja todos los aspectos de conceptos_factura_renglones
*
* @package  SistemaIGA
* @subpackage Conceptos_factura_renglones
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tconceptos_factura_renglones extends class_general{

    /**
    * codigo de conceptos_factura_renglones
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * concepto de conceptos_factura_renglones
    * @var concepto varchar
    * @access public
    */
    public $concepto;


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
    protected $nombreTabla = 'general.conceptos_factura_renglones';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase conceptos_factura_renglones
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
                $this->concepto = $arrConstructor[0]['concepto'];
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
        $arrTemp['concepto'] = $this->concepto;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase conceptos_factura_renglones o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarConceptos_factura_renglones(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto conceptos_factura_renglones
     *
     * @return integer
     */
    public function getCodigoConceptos_factura_renglones(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de conceptos_factura_renglones según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de conceptos_factura_renglones y los valores son los valores a actualizar
     */
    public function setConceptos_factura_renglones(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["concepto"]))
            $retorno = "concepto";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setConceptos_factura_renglones");
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
    * retorna los campos presentes en la tabla conceptos_factura_renglones en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposConceptos_factura_renglones(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.conceptos_factura_renglones");
    }

    /**
    * Buscar registros en la tabla conceptos_factura_renglones
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de conceptos_factura_renglones o la cantdad de registros segun el parametro contar
    */
    static function listarConceptos_factura_renglones(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.conceptos_factura_renglones", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>