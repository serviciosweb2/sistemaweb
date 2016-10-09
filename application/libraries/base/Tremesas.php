<?php

/**
* Class Tremesas
*
*Class  Tremesas maneja todos los aspectos de remesas
*
* @package  SistemaIGA
* @subpackage Remesas
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tremesas extends class_general{

    /**
    * codigo de remesas
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_banco de remesas
    * @var cod_banco int
    * @access public
    */
    public $cod_banco;

    /**
    * cod_configuracion de remesas
    * @var cod_configuracion varchar
    * @access public
    */
    public $cod_configuracion;

    /**
    * cod_facturante de remesas
    * @var cod_facturante int
    * @access public
    */
    public $cod_facturante;

    /**
    * nombre_banco de remesas
    * @var nombre_banco varchar
    * @access public
    */
    public $nombre_banco;

    /**
    * cedente_convenio de remesas
    * @var cedente_convenio varchar
    * @access public
    */
    public $cedente_convenio;

    /**
    * cedente_cpf_cnpj de remesas
    * @var cedente_cpf_cnpj varchar
    * @access public
    */
    public $cedente_cpf_cnpj;

    /**
    * agencia de remesas
    * @var agencia varchar
    * @access public
    */
    public $agencia;

    /**
    * digito_agencia de remesas
    * @var digito_agencia int
    * @access public
    */
    public $digito_agencia;

    /**
    * razon_social de remesas
    * @var razon_social varchar
    * @access public
    */
    public $razon_social;

    /**
    * fecha_documento de remesas
    * @var fecha_documento date
    * @access public
    */
    public $fecha_documento;

    /**
    * cartera de remesas
    * @var cartera varchar
    * @access public
    */
    public $cartera;

    /**
    * variacion_cartera de remesas
    * @var variacion_cartera varchar
    * @access public
    */
    public $variacion_cartera;

    /**
    * especie_documento de remesas
    * @var especie_documento varchar
    * @access public
    */
    public $especie_documento;

    /**
    * numero_cuenta de remesas
    * @var numero_cuenta varchar
    * @access public
    */
    public $numero_cuenta;

    /**
    * digito_cuenta de remesas
    * @var digito_cuenta int
    * @access public
    */
    public $digito_cuenta;

    /**
    * direccion de remesas
    * @var direccion varchar
    * @access public
    */
    public $direccion;

    /**
    * localidad de remesas
    * @var localidad varchar
    * @access public
    */
    public $localidad;

    /**
    * codigo_estado de remesas
    * @var codigo_estado varchar
    * @access public
    */
    public $codigo_estado;

    /**
    * remesa enviada o no
    * @var enviada int
    * @access public
    */
    public $enviada;

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
    protected $nombreTabla = 'bancos.remesas';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase remesas
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
                $this->cod_banco = $arrConstructor[0]['cod_banco'];
                $this->cod_configuracion = $arrConstructor[0]['cod_configuracion'];
                $this->cod_facturante = $arrConstructor[0]['cod_facturante'];
                $this->nombre_banco = $arrConstructor[0]['nombre_banco'];
                $this->cedente_convenio = $arrConstructor[0]['cedente_convenio'];
                $this->cedente_cpf_cnpj = $arrConstructor[0]['cedente_cpf_cnpj'];
                $this->agencia = $arrConstructor[0]['agencia'];
                $this->digito_agencia = $arrConstructor[0]['digito_agencia'];
                $this->razon_social = $arrConstructor[0]['razon_social'];
                $this->fecha_documento = $arrConstructor[0]['fecha_documento'];
                $this->cartera = $arrConstructor[0]['cartera'];
                $this->variacion_cartera = $arrConstructor[0]['variacion_cartera'];
                $this->especie_documento = $arrConstructor[0]['especie_documento'];
                $this->numero_cuenta = $arrConstructor[0]['numero_cuenta'];
                $this->digito_cuenta = $arrConstructor[0]['digito_cuenta'];
                $this->direccion = $arrConstructor[0]['direccion'];
                $this->localidad = $arrConstructor[0]['localidad'];
                $this->codigo_estado = $arrConstructor[0]['codigo_estado'];
                $this->enviada = $arrConstructor[0]['enviada'];
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
        $arrTemp['cod_banco'] = $this->cod_banco;
        $arrTemp['cod_configuracion'] = $this->cod_configuracion;
        $arrTemp['cod_facturante'] = $this->cod_facturante;
        $arrTemp['nombre_banco'] = $this->nombre_banco;
        $arrTemp['cedente_convenio'] = $this->cedente_convenio;
        $arrTemp['cedente_cpf_cnpj'] = $this->cedente_cpf_cnpj;
        $arrTemp['agencia'] = $this->agencia;
        $arrTemp['digito_agencia'] = $this->digito_agencia;
        $arrTemp['razon_social'] = $this->razon_social;
        $arrTemp['fecha_documento'] = $this->fecha_documento;
        $arrTemp['cartera'] = $this->cartera;
        $arrTemp['variacion_cartera'] = $this->variacion_cartera;
        $arrTemp['especie_documento'] = $this->especie_documento;
        $arrTemp['numero_cuenta'] = $this->numero_cuenta;
        $arrTemp['digito_cuenta'] = $this->digito_cuenta;
        $arrTemp['direccion'] = $this->direccion;
        $arrTemp['localidad'] = $this->localidad;
        $arrTemp['codigo_estado'] = $this->codigo_estado;
        $arrTemp['enviada'] = $this->enviada;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase remesas o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarRemesas(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto remesas
     *
     * @return integer
     */
    public function getCodigoRemesas(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de remesas según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de remesas y los valores son los valores a actualizar
     */
    public function setRemesas(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_banco"]))
            $retorno = "cod_banco";
        else if (!isset($arrCamposValores["cod_configuracion"]))
            $retorno = "cod_configuracion";
        else if (!isset($arrCamposValores["cod_facturante"]))
            $retorno = "cod_facturante";
        else if (!isset($arrCamposValores["nombre_banco"]))
            $retorno = "nombre_banco";
        else if (!isset($arrCamposValores["cedente_convenio"]))
            $retorno = "cedente_convenio";
        else if (!isset($arrCamposValores["cedente_cpf_cnpj"]))
            $retorno = "cedente_cpf_cnpj";
        else if (!isset($arrCamposValores["agencia"]))
            $retorno = "agencia";
        else if (!isset($arrCamposValores["digito_agencia"]))
            $retorno = "digito_agencia";
        else if (!isset($arrCamposValores["razon_social"]))
            $retorno = "razon_social";
        else if (!isset($arrCamposValores["fecha_documento"]))
            $retorno = "fecha_documento";
        else if (!isset($arrCamposValores["cartera"]))
            $retorno = "cartera";
        else if (!isset($arrCamposValores["variacion_cartera"]))
            $retorno = "variacion_cartera";
        else if (!isset($arrCamposValores["especie_documento"]))
            $retorno = "especie_documento";
        else if (!isset($arrCamposValores["numero_cuenta"]))
            $retorno = "numero_cuenta";
        else if (!isset($arrCamposValores["digito_cuenta"]))
            $retorno = "digito_cuenta";
        else if (!isset($arrCamposValores["direccion"]))
            $retorno = "direccion";
        else if (!isset($arrCamposValores["localidad"]))
            $retorno = "localidad";
        else if (!isset($arrCamposValores["codigo_estado"]))
            $retorno = "codigo_estado";
        else if (!isset($arrCamposValores["enviada"]))
            $retorno = "enviada";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setRemesas");
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
    * retorna los campos presentes en la tabla remesas en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposRemesas(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "bancos.remesas");
    }

    /**
    * Buscar registros en la tabla remesas
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de remesas o la cantdad de registros segun el parametro contar
    */
    static function listarRemesas(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "bancos.remesas", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>
