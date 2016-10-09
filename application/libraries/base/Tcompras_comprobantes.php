<?php

/**
* Class Tcompras_comprobantes
*
*Class  Tcompras_comprobantes maneja todos los aspectos de compras_comprobantes
*
* @package  SistemaIGA
* @subpackage Compras_comprobantes
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tcompras_comprobantes extends class_general{

    /**
    * codigo de compras_comprobantes
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_compra de compras_comprobantes
    * @var cod_compra int
    * @access public
    */
    public $cod_compra;

    /**
    * cod_comprobante de compras_comprobantes
    * @var cod_comprobante int
    * @access public
    */
    public $cod_comprobante;

    /**
    * nro_comprobante de compras_comprobantes
    * @var nro_comprobante varchar
    * @access public
    */
    public $nro_comprobante;

    /**
    * fecha_comprobante de compras_comprobantes
    * @var fecha_comprobante date
    * @access public
    */
    public $fecha_comprobante;

    /**
    * total de compras_comprobantes
    * @var total double
    * @access public
    */
    public $total;

    /**
    * estado de compras_comprobantes
    * @var estado enum
    * @access public
    */
    public $estado;

    /**
    * fecha de compras_comprobantes
    * @var fecha datetime
    * @access public
    */
    public $fecha;

    /**
    * cod_usuario de compras_comprobantes
    * @var cod_usuario int (requerido)
    * @access public
    */
    public $cod_usuario;


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
    protected $nombreTabla = 'compras_comprobantes';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase compras_comprobantes
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
                $this->cod_comprobante = $arrConstructor[0]['cod_comprobante'];
                $this->nro_comprobante = $arrConstructor[0]['nro_comprobante'];
                $this->fecha_comprobante = $arrConstructor[0]['fecha_comprobante'];
                $this->total = $arrConstructor[0]['total'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->fecha = $arrConstructor[0]['fecha'];
                $this->cod_usuario = $arrConstructor[0]['cod_usuario'];
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
        $arrTemp['cod_comprobante'] = $this->cod_comprobante;
        $arrTemp['nro_comprobante'] = $this->nro_comprobante;
        $arrTemp['fecha_comprobante'] = $this->fecha_comprobante;
        $arrTemp['total'] = $this->total;
        $arrTemp['estado'] = $this->estado == '' ? 'habilitado' : $this->estado;
        $arrTemp['fecha'] = $this->fecha;
        $arrTemp['cod_usuario'] = $this->cod_usuario == '' ? null : $this->cod_usuario;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase compras_comprobantes o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarCompras_comprobantes(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto compras_comprobantes
     *
     * @return integer
     */
    public function getCodigoCompras_comprobantes(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de compras_comprobantes según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de compras_comprobantes y los valores son los valores a actualizar
     */
    public function setCompras_comprobantes(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_compra"]))
            $retorno = "cod_compra";
        else if (!isset($arrCamposValores["cod_comprobante"]))
            $retorno = "cod_comprobante";
        else if (!isset($arrCamposValores["nro_comprobante"]))
            $retorno = "nro_comprobante";
        else if (!isset($arrCamposValores["fecha_comprobante"]))
            $retorno = "fecha_comprobante";
        else if (!isset($arrCamposValores["total"]))
            $retorno = "total";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        else if (!isset($arrCamposValores["fecha"]))
            $retorno = "fecha";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setCompras_comprobantes");
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
    * retorna los campos presentes en la tabla compras_comprobantes en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposCompras_comprobantes(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "compras_comprobantes");
    }

    /**
    * Buscar registros en la tabla compras_comprobantes
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de compras_comprobantes o la cantdad de registros segun el parametro contar
    */
    static function listarCompras_comprobantes(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "compras_comprobantes", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>