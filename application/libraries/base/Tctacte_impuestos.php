<?php

/**
* Class Tctacte_impuestos
*
*Class  Tctacte_impuestos maneja todos los aspectos de ctacte_impuestos
*
* @package  SistemaIGA
* @subpackage Ctacte_impuestos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tctacte_impuestos extends class_general{

    /**
    * cod_ctacte de ctacte_impuestos
    * @var cod_ctacte int
    * @access public
    */
    public $cod_ctacte;

    /**
    * cod_impuesto de ctacte_impuestos
    * @var cod_impuesto int
    * @access public
    */
    public $cod_impuesto;

    /**
    * valor de ctacte_impuestos
    * @var valor int
    * @access public
    */
    public $valor;


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
    protected $nombreTabla = 'ctacte_impuestos';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase ctacte_impuestos
     *
     * @param CI_DB_mysqli_driver $connection
     * @param integer $codigo (opcional) el codigo del objeto a crear
     */
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null){
        $this->oConnection = $conexion;
        if ($codigo != null && $codigo != -1){
            $arrConstructor = $this->_constructor($codigo);
            if (count($arrConstructor) > 0){
                $this->cod_ctacte = $arrConstructor[0]['cod_ctacte'];
                $this->cod_impuesto = $arrConstructor[0]['cod_impuesto'];
                $this->valor = $arrConstructor[0]['valor'];
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
        $arrTemp['cod_impuesto'] = $this->cod_impuesto;
        $arrTemp['valor'] = $this->valor;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase ctacte_impuestos o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarCtacte_impuestos(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto ctacte_impuestos
     *
     * @return integer
     */
    public function getCodigoCtacte_impuestos(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de ctacte_impuestos según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de ctacte_impuestos y los valores son los valores a actualizar
     */
    public function setCtacte_impuestos(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_ctacte"]))
            $retorno = "cod_ctacte";
        else if (!isset($arrCamposValores["cod_impuesto"]))
            $retorno = "cod_impuesto";
        else if (!isset($arrCamposValores["valor"]))
            $retorno = "valor";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setCtacte_impuestos");
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
    * retorna los campos presentes en la tabla ctacte_impuestos en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposCtacte_impuestos(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "ctacte_impuestos");
    }

    /**
    * Buscar registros en la tabla ctacte_impuestos
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de ctacte_impuestos o la cantdad de registros segun el parametro contar
    */
    static function listarCtacte_impuestos(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "ctacte_impuestos", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>