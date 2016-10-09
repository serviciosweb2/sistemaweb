<?php

/**
* Class Tmedio_cheques
*
*Class  Tmedio_cheques maneja todos los aspectos de medio_cheques
*
* @package  SistemaIGA
* @subpackage Medio_cheques
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tmedio_cheques extends class_general{

    /**
    * codigo de medio_cheques
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * tipo_cheque de medio_cheques
    * @var tipo_cheque int
    * @access public
    */
    public $tipo_cheque;

    /**
    * fecha_cobro de medio_cheques
    * @var fecha_cobro date
    * @access public
    */
    public $fecha_cobro;

    /**
    * nro_cheque de medio_cheques
    * @var nro_cheque double
    * @access public
    */
    public $nro_cheque;

    /**
    * emisor de medio_cheques
    * @var emisor varchar
    * @access public
    */
    public $emisor;

    /**
    * cod_banco_emisor de medio_cheques
    * @var cod_banco_emisor int (requerido)
    * @access public
    */
    public $cod_banco_emisor;

    /**
    * cod_cobro de medio_cheques
    * @var cod_cobro int
    * @access public
    */
    public $cod_cobro;


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
    protected $nombreTabla = 'medio_cheques';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase medio_cheques
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
                $this->tipo_cheque = $arrConstructor[0]['tipo_cheque'];
                $this->fecha_cobro = $arrConstructor[0]['fecha_cobro'];
                $this->nro_cheque = $arrConstructor[0]['nro_cheque'];
                $this->emisor = $arrConstructor[0]['emisor'];
                $this->cod_banco_emisor = $arrConstructor[0]['cod_banco_emisor'];
                $this->cod_cobro = $arrConstructor[0]['cod_cobro'];
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
        $arrTemp['tipo_cheque'] = $this->tipo_cheque;
        $arrTemp['fecha_cobro'] = $this->fecha_cobro;
        $arrTemp['nro_cheque'] = $this->nro_cheque;
        $arrTemp['emisor'] = $this->emisor;
        $arrTemp['cod_banco_emisor'] = $this->cod_banco_emisor == '' ? null : $this->cod_banco_emisor;
        $arrTemp['cod_cobro'] = $this->cod_cobro;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase medio_cheques o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarMedio_cheques(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto medio_cheques
     *
     * @return integer
     */
    public function getCodigoMedio_cheques(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de medio_cheques según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de medio_cheques y los valores son los valores a actualizar
     */
    public function setMedio_cheques(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["tipo_cheque"]))
            $retorno = "tipo_cheque";
        else if (!isset($arrCamposValores["fecha_cobro"]))
            $retorno = "fecha_cobro";
        else if (!isset($arrCamposValores["nro_cheque"]))
            $retorno = "nro_cheque";
        else if (!isset($arrCamposValores["emisor"]))
            $retorno = "emisor";
        else if (!isset($arrCamposValores["cod_cobro"]))
            $retorno = "cod_cobro";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setMedio_cheques");
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
    * retorna los campos presentes en la tabla medio_cheques en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposMedio_cheques(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "medio_cheques");
    }

    /**
    * Buscar registros en la tabla medio_cheques
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de medio_cheques o la cantdad de registros segun el parametro contar
    */
    static function listarMedio_cheques(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "medio_cheques", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>