<?php

/**
* Class Tfilial_razones
*
*Class  Tfilial_razones maneja todos los aspectos de filial_razones
*
* @package  SistemaIGA
* @subpackage Filial_razones
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tfilial_razones extends class_general{

    /**
    * cod_razon_social de filial_razones
    * @var cod_razon_social int (requerido)
    * @access public
    */
    public $cod_razon_social;

    /**
    * codigo de filial_razones
    * @var codigo int
    * @access protected
    */
    protected $codigo;


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
    protected $nombreTabla = 'filial_razones';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase filial_razones
     *
     * @param CI_DB_mysqli_driver $connection
     * @param integer $codigo (opcional) el codigo del objeto a crear
     */
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null){
        $this->oConnection = $conexion;
        if ($codigo != null && $codigo != -1){
            $arrConstructor = $this->_constructor($codigo);
            if (count($arrConstructor) > 0){
                $this->cod_razon_social = $arrConstructor[0]['cod_razon_social'];
                $this->codigo = $arrConstructor[0]['codigo'];
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
        $arrTemp['cod_razon_social'] = $this->cod_razon_social == '' ? null : $this->cod_razon_social;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase filial_razones o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarFilial_razones(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto filial_razones
     *
     * @return integer
     */
    public function getCodigoFilial_razones(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de filial_razones según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de filial_razones y los valores son los valores a actualizar
     */
    public function setFilial_razones(array $arrCamposValores){
        $retorno = "";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setFilial_razones");
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
    * retorna los campos presentes en la tabla filial_razones en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposFilial_razones(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "filial_razones");
    }

    /**
    * Buscar registros en la tabla filial_razones
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de filial_razones o la cantdad de registros segun el parametro contar
    */
    static function listarFilial_razones(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "filial_razones", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>