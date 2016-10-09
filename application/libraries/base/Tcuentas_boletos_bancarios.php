<?php

/**
* Class Tcuentas_boletos_bancarios
*
*Class  Tcuentas_boletos_bancarios maneja todos los aspectos de cuentas_boletos_bancarios
*
* @package  SistemaIGA
* @subpackage Cuentas_boletos_bancarios
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tcuentas_boletos_bancarios extends class_general{

    /**
    * codigo de cuentas_boletos_bancarios
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_banco de cuentas_boletos_bancarios
    * @var cod_banco int
    * @access public
    */
    public $cod_banco;

    /**
    * numero_secuencia de cuentas_boletos_bancarios
    * @var numero_secuencia int
    * @access public
    */
    public $numero_secuencia;

    /**
    * cantidad_copias de cuentas_boletos_bancarios
    * @var cantidad_copias smallint
    * @access public
    */
    public $cantidad_copias;

    /**
    * convenio de cuentas_boletos_bancarios
    * @var convenio varchar
    * @access public
    */
    public $convenio;

    /**
    * carteira de cuentas_boletos_bancarios
    * @var carteira int
    * @access public
    */
    public $carteira;

    /**
    * variacao_carteira de cuentas_boletos_bancarios
    * @var variacao_carteira varchar
    * @access public
    */
    public $variacao_carteira;


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
    protected $nombreTabla = 'bancos.cuentas_boletos_bancarios';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase cuentas_boletos_bancarios
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
                $this->numero_secuencia = $arrConstructor[0]['numero_secuencia'];
                $this->cantidad_copias = $arrConstructor[0]['cantidad_copias'];
                $this->convenio = $arrConstructor[0]['convenio'];
                $this->carteira = $arrConstructor[0]['carteira'];
                $this->variacao_carteira = $arrConstructor[0]['variacao_carteira'];
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
        $arrTemp['numero_secuencia'] = $this->numero_secuencia;
        $arrTemp['cantidad_copias'] = $this->cantidad_copias == '' ? '1' : $this->cantidad_copias;
        $arrTemp['convenio'] = $this->convenio;
        $arrTemp['carteira'] = $this->carteira;
        $arrTemp['variacao_carteira'] = $this->variacao_carteira;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase cuentas_boletos_bancarios o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarCuentas_boletos_bancarios(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto cuentas_boletos_bancarios
     *
     * @return integer
     */
    public function getCodigoCuentas_boletos_bancarios(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de cuentas_boletos_bancarios según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de cuentas_boletos_bancarios y los valores son los valores a actualizar
     */
    public function setCuentas_boletos_bancarios(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_banco"]))
            $retorno = "cod_banco";
        else if (!isset($arrCamposValores["numero_secuencia"]))
            $retorno = "numero_secuencia";
        else if (!isset($arrCamposValores["cantidad_copias"]))
            $retorno = "cantidad_copias";
        else if (!isset($arrCamposValores["convenio"]))
            $retorno = "convenio";
        else if (!isset($arrCamposValores["carteira"]))
            $retorno = "carteira";
        else if (!isset($arrCamposValores["variacao_carteira"]))
            $retorno = "variacao_carteira";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setCuentas_boletos_bancarios");
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
    * retorna los campos presentes en la tabla cuentas_boletos_bancarios en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposCuentas_boletos_bancarios(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "bancos.cuentas_boletos_bancarios");
    }

    /**
    * Buscar registros en la tabla cuentas_boletos_bancarios
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de cuentas_boletos_bancarios o la cantdad de registros segun el parametro contar
    */
    static function listarCuentas_boletos_bancarios(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "bancos.cuentas_boletos_bancarios", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>