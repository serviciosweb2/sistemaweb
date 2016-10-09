<?php

/**
* Class Tcompras_renglones
*
*Class  Tcompras_renglones maneja todos los aspectos de compras_renglones
*
* @package  SistemaIGA
* @subpackage Compras_renglones
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tcompras_renglones extends class_general{

    /**
    * codigo de compras_renglones
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_compra de compras_renglones
    * @var cod_compra int
    * @access public
    */
    public $cod_compra;

    /**
    * cod_articulo de compras_renglones
    * @var cod_articulo int
    * @access public
    */
    public $cod_articulo;

    /**
    * cantidad de compras_renglones
    * @var cantidad double
    * @access public
    */
    public $cantidad;

    /**
    * precio_unitario de compras_renglones
    * @var precio_unitario double
    * @access public
    */
    public $precio_unitario;

    /**
    * precio_total de compras_renglones
    * @var precio_total double
    * @access public
    */
    public $precio_total;

    /**
    * baja de compras_renglones
    * @var baja smallint
    * @access public
    */
    public $baja;


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
    protected $nombreTabla = 'compras_renglones';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase compras_renglones
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
                $this->cod_compra = $arrConstructor[0]['cod_compra'];
                $this->cod_articulo = $arrConstructor[0]['cod_articulo'];
                $this->cantidad = $arrConstructor[0]['cantidad'];
                $this->precio_unitario = $arrConstructor[0]['precio_unitario'];
                $this->precio_total = $arrConstructor[0]['precio_total'];
                $this->baja = $arrConstructor[0]['baja'];
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
        $arrTemp['cod_compra'] = $this->cod_compra;
        $arrTemp['cod_articulo'] = $this->cod_articulo;
        $arrTemp['cantidad'] = $this->cantidad;
        $arrTemp['precio_unitario'] = $this->precio_unitario;
        $arrTemp['precio_total'] = $this->precio_total;
        $arrTemp['baja'] = $this->baja == '' ? '0' : $this->baja;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase compras_renglones o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarCompras_renglones(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto compras_renglones
     *
     * @return integer
     */
    public function getCodigoCompras_renglones(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de compras_renglones según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de compras_renglones y los valores son los valores a actualizar
     */
    public function setCompras_renglones(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_compra"]))
            $retorno = "cod_compra";
        else if (!isset($arrCamposValores["cod_articulo"]))
            $retorno = "cod_articulo";
        else if (!isset($arrCamposValores["cantidad"]))
            $retorno = "cantidad";
        else if (!isset($arrCamposValores["precio_unitario"]))
            $retorno = "precio_unitario";
        else if (!isset($arrCamposValores["precio_total"]))
            $retorno = "precio_total";
        else if (!isset($arrCamposValores["baja"]))
            $retorno = "baja";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setCompras_renglones");
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
    * retorna los campos presentes en la tabla compras_renglones en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposCompras_renglones(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "compras_renglones");
    }

    /**
    * Buscar registros en la tabla compras_renglones
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de compras_renglones o la cantdad de registros segun el parametro contar
    */
    static function listarCompras_renglones(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "compras_renglones", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>