<?php

/**
* Class Tpos_contratos
*
*Class  Tpos_contratos maneja todos los aspectos de pos_contratos
*
* @package  SistemaIGA
* @subpackage Pos_contratos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tpos_contratos extends class_general{

    /**
    * codigo de pos_contratos
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_facturante de pos_contratos
    * @var cod_facturante int
    * @access public
    */
    public $cod_facturante;

    /**
    * cod_operador de pos_contratos
    * @var cod_operador int
    * @access public
    */
    public $cod_operador;

    /**
    * estado de pos_contratos
    * @var estado enum
    * @access public
    */
    public $estado;

    /**
    * fecha_contrato de pos_contratos
    * @var fecha_contrato date
    * @access public
    */
    public $fecha_contrato;


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
    protected $nombreTabla = 'tarjetas.pos_contratos';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase pos_contratos
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
                $this->cod_facturante = $arrConstructor[0]['cod_facturante'];
                $this->cod_operador = $arrConstructor[0]['cod_operador'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->fecha_contrato = $arrConstructor[0]['fecha_contrato'];
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
        $arrTemp['cod_facturante'] = $this->cod_facturante;
        $arrTemp['cod_operador'] = $this->cod_operador;
        $arrTemp['estado'] = $this->estado == '' ? 'habilitado' : $this->estado;
        $arrTemp['fecha_contrato'] = $this->fecha_contrato;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase pos_contratos o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarPos_contratos(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto pos_contratos
     *
     * @return integer
     */
    public function getCodigoPos_contratos(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de pos_contratos según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de pos_contratos y los valores son los valores a actualizar
     */
    public function setPos_contratos(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_facturante"]))
            $retorno = "cod_facturante";
        else if (!isset($arrCamposValores["cod_operador"]))
            $retorno = "cod_operador";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        else if (!isset($arrCamposValores["fecha_contrato"]))
            $retorno = "fecha_contrato";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setPos_contratos");
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
    * retorna los campos presentes en la tabla pos_contratos en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposPos_contratos(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "tarjetas.pos_contratos");
    }

    /**
    * Buscar registros en la tabla pos_contratos
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de pos_contratos o la cantdad de registros segun el parametro contar
    */
    static function listarPos_contratos(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "tarjetas.pos_contratos", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>