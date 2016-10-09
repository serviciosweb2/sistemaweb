<?php

/**
* Class Tpos_puntos_venta
*
*Class  Tpos_puntos_venta maneja todos los aspectos de pos_puntos_venta
*
* @package  SistemaIGA
* @subpackage Pos_puntos_venta
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tpos_puntos_venta extends class_general{

    /**
    * codigo de pos_puntos_venta
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_contrato de pos_puntos_venta
    * @var cod_contrato int
    * @access public
    */
    public $cod_contrato;

    /**
    * cod_facturante de pos_puntos_venta
    * @var cod_facturante int
    * @access public
    */
    public $cod_facturante;

    /**
    * estado de pos_puntos_venta
    * @var estado enum
    * @access public
    */
    public $estado;


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
    protected $nombreTabla = 'tarjetas.pos_puntos_venta';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase pos_puntos_venta
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
                $this->cod_contrato = $arrConstructor[0]['cod_contrato'];
                $this->cod_facturante = $arrConstructor[0]['cod_facturante'];
                $this->estado = $arrConstructor[0]['estado'];
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
        $arrTemp['cod_contrato'] = $this->cod_contrato;
        $arrTemp['cod_facturante'] = $this->cod_facturante;
        $arrTemp['estado'] = $this->estado == '' ? 'habilitado' : $this->estado;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase pos_puntos_venta o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarPos_puntos_venta(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto pos_puntos_venta
     *
     * @return integer
     */
    public function getCodigoPos_puntos_venta(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de pos_puntos_venta según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de pos_puntos_venta y los valores son los valores a actualizar
     */
    public function setPos_puntos_venta(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_contrato"]))
            $retorno = "cod_contrato";
        else if (!isset($arrCamposValores["cod_facturante"]))
            $retorno = "cod_facturante";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setPos_puntos_venta");
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
    * retorna los campos presentes en la tabla pos_puntos_venta en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposPos_puntos_venta(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "tarjetas.pos_puntos_venta");
    }

    /**
    * Buscar registros en la tabla pos_puntos_venta
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de pos_puntos_venta o la cantdad de registros segun el parametro contar
    */
    static function listarPos_puntos_venta(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "tarjetas.pos_puntos_venta", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>