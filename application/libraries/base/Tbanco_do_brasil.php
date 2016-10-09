<?php

/**
* Class Tbanco_do_brasil
*
*Class  Tbanco_do_brasil maneja todos los aspectos de banco_do_brasil
*
* @package  SistemaIGA
* @subpackage Banco_do_brasil
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tbanco_do_brasil extends class_general{

    /**
    * codigo de banco_do_brasil
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * agencia de banco_do_brasil
    * @var agencia int
    * @access public
    */
    public $agencia;

    /**
    * conta de banco_do_brasil
    * @var conta int
    * @access public
    */
    public $conta;

    /**
    * contrato de banco_do_brasil
    * @var contrato int
    * @access public
    */
    public $contrato;

    /**
    * formatacao_convenio de banco_do_brasil
    * @var formatacao_convenio int
    * @access public
    */
    public $formatacao_convenio;

    /**
    * formatacao_nosso_numero de banco_do_brasil
    * @var formatacao_nosso_numero int
    * @access public
    */
    public $formatacao_nosso_numero;

    /**
    * identificacao de banco_do_brasil
    * @var identificacao varchar
    * @access public
    */
    public $identificacao;

    /**
    * digito_agencia de banco_do_brasil
    * @var digito_agencia smallint
    * @access public
    */
    public $digito_agencia;

    /**
    * digito_cuenta de banco_do_brasil
    * @var digito_cuenta smallint
    * @access public
    */
    public $digito_cuenta;

    /**
    * estado de banco_do_brasil
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
    protected $nombreTabla = 'bancos.banco_do_brasil';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase banco_do_brasil
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
                $this->agencia = $arrConstructor[0]['agencia'];
                $this->conta = $arrConstructor[0]['conta'];
                $this->contrato = $arrConstructor[0]['contrato'];
                $this->formatacao_convenio = $arrConstructor[0]['formatacao_convenio'];
                $this->formatacao_nosso_numero = $arrConstructor[0]['formatacao_nosso_numero'];
                $this->identificacao = $arrConstructor[0]['identificacao'];
                $this->digito_agencia = $arrConstructor[0]['digito_agencia'];
                $this->digito_cuenta = $arrConstructor[0]['digito_cuenta'];
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
        $arrTemp['agencia'] = $this->agencia;
        $arrTemp['conta'] = $this->conta;
        $arrTemp['contrato'] = $this->contrato;
        $arrTemp['formatacao_convenio'] = $this->formatacao_convenio;
        $arrTemp['formatacao_nosso_numero'] = $this->formatacao_nosso_numero;
        $arrTemp['identificacao'] = $this->identificacao;
        $arrTemp['digito_agencia'] = $this->digito_agencia;
        $arrTemp['digito_cuenta'] = $this->digito_cuenta;
        $arrTemp['estado'] = $this->estado;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase banco_do_brasil o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarBanco_do_brasil(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto banco_do_brasil
     *
     * @return integer
     */
    public function getCodigoBanco_do_brasil(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de banco_do_brasil según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de banco_do_brasil y los valores son los valores a actualizar
     */
    public function setBanco_do_brasil(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["agencia"]))
            $retorno = "agencia";
        else if (!isset($arrCamposValores["conta"]))
            $retorno = "conta";
        else if (!isset($arrCamposValores["contrato"]))
            $retorno = "contrato";
        else if (!isset($arrCamposValores["formatacao_convenio"]))
            $retorno = "formatacao_convenio";
        else if (!isset($arrCamposValores["formatacao_nosso_numero"]))
            $retorno = "formatacao_nosso_numero";
        else if (!isset($arrCamposValores["identificacao"]))
            $retorno = "identificacao";
        else if (!isset($arrCamposValores["digito_agencia"]))
            $retorno = "digito_agencia";
        else if (!isset($arrCamposValores["digito_cuenta"]))
            $retorno = "digito_cuenta";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setBanco_do_brasil");
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
    * retorna los campos presentes en la tabla banco_do_brasil en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposBanco_do_brasil(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "bancos.banco_do_brasil");
    }

    /**
    * Buscar registros en la tabla banco_do_brasil
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de banco_do_brasil o la cantdad de registros segun el parametro contar
    */
    static function listarBanco_do_brasil(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "bancos.banco_do_brasil", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>