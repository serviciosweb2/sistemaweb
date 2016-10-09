<?php

/**
* Class Tfiliales_metodos_facturacion
*
*Class  Tfiliales_metodos_facturacion maneja todos los aspectos de filiales_metodos_facturacion
*
* @package  SistemaIGA
* @subpackage Filiales_metodos_facturacion
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tfiliales_metodos_facturacion extends class_general{

    /**
    * cod_filial de filiales_metodos_facturacion
    * @var cod_filial int
    * @access protected
    */
    protected $cod_filial;

    /**
    * facturacion_productos de filiales_metodos_facturacion
    * @var facturacion_productos enum
    * @access public
    */
    public $facturacion_productos;

    /**
    * facturacion_servicios de filiales_metodos_facturacion
    * @var facturacion_servicios enum
    * @access public
    */
    public $facturacion_servicios;


    /**
    * primaryKey de la tabla
    * @var primaryKey var
    * @access protected
    */
    protected $primaryKey = "cod_filial";
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
    protected $nombreTabla = 'general.filiales_metodos_facturacion';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase filiales_metodos_facturacion
     *
     * @param CI_DB_mysqli_driver $connection
     * @param integer $codigo (opcional) el codigo del objeto a crear
     */
    function __construct(CI_DB_mysqli_driver $conexion, $cod_filial = null){
        $this->oConnection = $conexion;
        if ($cod_filial != null && $cod_filial != -1){
            $arrConstructor = $this->_constructor($cod_filial);
            if (count($arrConstructor) > 0){
                $this->cod_filial = $arrConstructor[0]['cod_filial'];
                $this->facturacion_productos = $arrConstructor[0]['facturacion_productos'];
                $this->facturacion_servicios = $arrConstructor[0]['facturacion_servicios'];
            } else {
                $this->cod_filial = -1;
            }
        } else {
            $this->cod_filial = -1;
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
        $arrTemp['facturacion_productos'] = $this->facturacion_productos == '' ? 'no_factura' : $this->facturacion_productos;
        $arrTemp['facturacion_servicios'] = $this->facturacion_servicios == '' ? 'no_factura' : $this->facturacion_servicios;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase filiales_metodos_facturacion o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarFiliales_metodos_facturacion(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto filiales_metodos_facturacion
     *
     * @return integer
     */
    public function getCodigoFiliales_metodos_facturacion(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de filiales_metodos_facturacion según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de filiales_metodos_facturacion y los valores son los valores a actualizar
     */
    public function setFiliales_metodos_facturacion(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["facturacion_productos"]))
            $retorno = "facturacion_productos";
        else if (!isset($arrCamposValores["facturacion_servicios"]))
            $retorno = "facturacion_servicios";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setFiliales_metodos_facturacion");
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
    * retorna los campos presentes en la tabla filiales_metodos_facturacion en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposFiliales_metodos_facturacion(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.filiales_metodos_facturacion");
    }

    /**
    * Buscar registros en la tabla filiales_metodos_facturacion
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de filiales_metodos_facturacion o la cantdad de registros segun el parametro contar
    */
    static function listarFiliales_metodos_facturacion(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.filiales_metodos_facturacion", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>