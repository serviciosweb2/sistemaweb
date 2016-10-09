<?php

/**
* Class Tboletos_bancarios
*
*Class  Tboletos_bancarios maneja todos los aspectos de boletos_bancarios
*
* @package  SistemaIGA
* @subpackage Boletos_bancarios
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tboletos_bancarios extends class_general{

    /**
    * codigo de boletos_bancarios
    * @var codigo int
    * @access protected
    */
    public $codigo;

    /**
    * cod_remesa de boletos_bancarios
    * @var cod_remesa int
    * @access public
    */
    public $cod_remesa;

    /**
    * cod_filial de boletos_bancarios
    * @var cod_filial int
    * @access public
    */
    public $cod_filial;

    /**
    * fecha_vencimiento de boletos_bancarios
    * @var fecha_vencimiento date (requerido)
    * @access public
    */
    public $fecha_vencimiento;

    /**
    * numero_secuencial de boletos_bancarios
    * @var numero_secuencial int
    * @access public
    */
    public $numero_secuencial;

    /**
    * numero_documento de boletos_bancarios
    * @var numero_documento int
    * @access public
    */
    public $numero_documento;

    /**
    * valor_boleto de boletos_bancarios
    * @var valor_boleto double
    * @access public
    */
    public $valor_boleto;

    /**
    * interes_mora de boletos_bancarios
    * @var interes_mora decimal (requerido)
    * @access public
    */
    public $interes_mora;

    /**
    * valor_descuento de boletos_bancarios
    * @var valor_descuento double (requerido)
    * @access public
    */
    public $valor_descuento;

    /**
    * sacado_cpf_cnpj de boletos_bancarios
    * @var sacado_cpf_cnpj varchar
    * @access public
    */
    public $sacado_cpf_cnpj;

    /**
    * sacado_nombre de boletos_bancarios
    * @var sacado_nombre varchar
    * @access public
    */
    public $sacado_nombre;

    /**
    * sacado_direccion de boletos_bancarios
    * @var sacado_direccion varchar
    * @access public
    */
    public $sacado_direccion;

    /**
    * sacado_cod_postal de boletos_bancarios
    * @var sacado_cod_postal varchar
    * @access public
    */
    public $sacado_cod_postal;

    /**
    * sacado_ciudad de boletos_bancarios
    * @var sacado_ciudad varchar
    * @access public
    */
    public $sacado_ciudad;

    /**
    * sacado_codigo_estado de boletos_bancarios
    * @var sacado_codigo_estado varchar
    * @access public
    */
    public $sacado_codigo_estado;

    /**
    * fecha_multa de boletos_bancarios
    * @var fecha_multa date (requerido)
    * @access public
    */
    public $fecha_multa;

    /**
    * valor_multa de boletos_bancarios
    * @var valor_multa double (requerido)
    * @access public
    */
    public $valor_multa;

    /**
    * porcentaje_multa de boletos_bancarios
    * @var porcentaje_multa decimal (requerido)
    * @access public
    */
    public $porcentaje_multa;

    /**
    * fecha_mora de boletos_bancarios
    * @var fecha_mora date (requerido)
    * @access public
    */
    public $fecha_mora;

    /**
    * porcentaje_mora de boletos_bancarios
    * @var porcentaje_mora decimal (requerido)
    * @access public
    */
    public $porcentaje_mora;

    /**
    * instrucciones_cantidad_dias de boletos_bancarios
    * @var instrucciones_cantidad_dias int (requerido)
    * @access public
    */
    public $instrucciones_cantidad_dias;

    /**
    * estado de boletos_bancarios
    * @var estado enum
    * @access public
    */
    public $estado;

    /**
    * numero_seguimiento de boletos_bancarios
    * @var numero_seguimiento varchar
    * @access public
    */
    public $numero_seguimiento;

    /**
    * nosso_numero de boletos_bancarios
    * @var nosso_numero varchar
    * @access public
    */
    public $nosso_numero;

    /**
    * convenio de boletos_bancarios
    * @var convenio varchar
    * @access public
    */
    public $convenio;

    /**
    * demostrativo1 de boletos_bancarios
    * @var demostrativo1 varchar (requerido)
    * @access public
    */
    public $demostrativo1;

    /**
    * demostrativo2 de boletos_bancarios
    * @var demostrativo2 varchar (requerido)
    * @access public
    */
    public $demostrativo2;

    /**
    * demostrativo3 de boletos_bancarios
    * @var demostrativo3 varchar (requerido)
    * @access public
    */
    public $demostrativo3;

    /**
    * instrucciones1 de boletos_bancarios
    * @var instrucciones1 varchar (requerido)
    * @access public
    */
    public $instrucciones1;

    /**
    * instrucciones2 de boletos_bancarios
    * @var instrucciones2 varchar (requerido)
    * @access public
    */
    public $instrucciones2;

    /**
    * instrucciones3 de boletos_bancarios
    * @var instrucciones3 varchar (requerido)
    * @access public
    */
    public $instrucciones3;

    /**
    * instrucciones4 de boletos_bancarios
    * @var instrucciones4 varchar (requerido)
    * @access public
    */
    public $instrucciones4;


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
    protected $nombreTabla = 'bancos.boletos_bancarios';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase boletos_bancarios
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
                $this->cod_remesa = $arrConstructor[0]['cod_remesa'];
                $this->cod_filial = $arrConstructor[0]['cod_filial'];
                $this->fecha_vencimiento = $arrConstructor[0]['fecha_vencimiento'];
                $this->numero_secuencial = $arrConstructor[0]['numero_secuencial'];
                $this->numero_documento = $arrConstructor[0]['numero_documento'];
                $this->valor_boleto = $arrConstructor[0]['valor_boleto'];
                $this->interes_mora = $arrConstructor[0]['interes_mora'];
                $this->valor_descuento = $arrConstructor[0]['valor_descuento'];
                $this->sacado_cpf_cnpj = $arrConstructor[0]['sacado_cpf_cnpj'];
                $this->sacado_nombre = $arrConstructor[0]['sacado_nombre'];
                $this->sacado_direccion = $arrConstructor[0]['sacado_direccion'];
                $this->sacado_cod_postal = $arrConstructor[0]['sacado_cod_postal'];
                $this->sacado_ciudad = $arrConstructor[0]['sacado_ciudad'];
                $this->sacado_codigo_estado = $arrConstructor[0]['sacado_codigo_estado'];
                $this->fecha_multa = $arrConstructor[0]['fecha_multa'];
                $this->valor_multa = $arrConstructor[0]['valor_multa'];
                $this->porcentaje_multa = $arrConstructor[0]['porcentaje_multa'];
                $this->fecha_mora = $arrConstructor[0]['fecha_mora'];
                $this->porcentaje_mora = $arrConstructor[0]['porcentaje_mora'];
                $this->instrucciones_cantidad_dias = $arrConstructor[0]['instrucciones_cantidad_dias'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->numero_seguimiento = $arrConstructor[0]['numero_seguimiento'];
                $this->nosso_numero = $arrConstructor[0]['nosso_numero'];
                $this->convenio = $arrConstructor[0]['convenio'];
                $this->demostrativo1 = $arrConstructor[0]['demostrativo1'];
                $this->demostrativo2 = $arrConstructor[0]['demostrativo2'];
                $this->demostrativo3 = $arrConstructor[0]['demostrativo3'];
                $this->instrucciones1 = $arrConstructor[0]['instrucciones1'];
                $this->instrucciones2 = $arrConstructor[0]['instrucciones2'];
                $this->instrucciones3 = $arrConstructor[0]['instrucciones3'];
                $this->instrucciones4 = $arrConstructor[0]['instrucciones4'];
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
        $arrTemp['cod_remesa'] = $this->cod_remesa;
        $arrTemp['cod_filial'] = $this->cod_filial;
        $arrTemp['fecha_vencimiento'] = $this->fecha_vencimiento == '' ? null : $this->fecha_vencimiento;
        $arrTemp['numero_secuencial'] = $this->numero_secuencial;
        $arrTemp['numero_documento'] = $this->numero_documento;
        $arrTemp['valor_boleto'] = $this->valor_boleto;
        $arrTemp['interes_mora'] = $this->interes_mora == '' ? null : $this->interes_mora;
        $arrTemp['valor_descuento'] = $this->valor_descuento == '' ? null : $this->valor_descuento;
        $arrTemp['sacado_cpf_cnpj'] = $this->sacado_cpf_cnpj;
        $arrTemp['sacado_nombre'] = $this->sacado_nombre;
        $arrTemp['sacado_direccion'] = $this->sacado_direccion;
        $arrTemp['sacado_cod_postal'] = $this->sacado_cod_postal;
        $arrTemp['sacado_ciudad'] = $this->sacado_ciudad;
        $arrTemp['sacado_codigo_estado'] = $this->sacado_codigo_estado;
        $arrTemp['fecha_multa'] = $this->fecha_multa == '' ? null : $this->fecha_multa;
        $arrTemp['valor_multa'] = $this->valor_multa == '' ? null : $this->valor_multa;
        $arrTemp['porcentaje_multa'] = $this->porcentaje_multa == '' ? null : $this->porcentaje_multa;
        $arrTemp['fecha_mora'] = $this->fecha_mora == '' ? null : $this->fecha_mora;
        $arrTemp['porcentaje_mora'] = $this->porcentaje_mora == '' ? null : $this->porcentaje_mora;
        $arrTemp['instrucciones_cantidad_dias'] = $this->instrucciones_cantidad_dias == '' ? null : $this->instrucciones_cantidad_dias;
        $arrTemp['estado'] = $this->estado;
        $arrTemp['numero_seguimiento'] = $this->numero_seguimiento;
        $arrTemp['nosso_numero'] = $this->nosso_numero;
        $arrTemp['convenio'] = $this->convenio;
        $arrTemp['demostrativo1'] = $this->demostrativo1 == '' ? null : $this->demostrativo1;
        $arrTemp['demostrativo2'] = $this->demostrativo2 == '' ? null : $this->demostrativo2;
        $arrTemp['demostrativo3'] = $this->demostrativo3 == '' ? null : $this->demostrativo3;
        $arrTemp['instrucciones1'] = $this->instrucciones1 == '' ? null : $this->instrucciones1;
        $arrTemp['instrucciones2'] = $this->instrucciones2 == '' ? null : $this->instrucciones2;
        $arrTemp['instrucciones3'] = $this->instrucciones3 == '' ? null : $this->instrucciones3;
        $arrTemp['instrucciones4'] = $this->instrucciones4 == '' ? null : $this->instrucciones4;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase boletos_bancarios o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarBoletos_bancarios(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto boletos_bancarios
     *
     * @return integer
     */
    public function getCodigoBoletos_bancarios(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de boletos_bancarios según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de boletos_bancarios y los valores son los valores a actualizar
     */
    public function setBoletos_bancarios(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_remesa"]))
            $retorno = "cod_remesa";
        else if (!isset($arrCamposValores["cod_filial"]))
            $retorno = "cod_filial";
        else if (!isset($arrCamposValores["numero_secuencial"]))
            $retorno = "numero_secuencial";
        else if (!isset($arrCamposValores["numero_documento"]))
            $retorno = "numero_documento";
        else if (!isset($arrCamposValores["valor_boleto"]))
            $retorno = "valor_boleto";
        else if (!isset($arrCamposValores["sacado_cpf_cnpj"]))
            $retorno = "sacado_cpf_cnpj";
        else if (!isset($arrCamposValores["sacado_nombre"]))
            $retorno = "sacado_nombre";
        else if (!isset($arrCamposValores["sacado_direccion"]))
            $retorno = "sacado_direccion";
        else if (!isset($arrCamposValores["sacado_cod_postal"]))
            $retorno = "sacado_cod_postal";
        else if (!isset($arrCamposValores["sacado_ciudad"]))
            $retorno = "sacado_ciudad";
        else if (!isset($arrCamposValores["sacado_codigo_estado"]))
            $retorno = "sacado_codigo_estado";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        else if (!isset($arrCamposValores["numero_seguimiento"]))
            $retorno = "numero_seguimiento";
        else if (!isset($arrCamposValores["nosso_numero"]))
            $retorno = "nosso_numero";
        else if (!isset($arrCamposValores["convenio"]))
            $retorno = "convenio";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setBoletos_bancarios");
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
    * retorna los campos presentes en la tabla boletos_bancarios en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposBoletos_bancarios(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "bancos.boletos_bancarios");
    }

    /**
    * Buscar registros en la tabla boletos_bancarios
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de boletos_bancarios o la cantdad de registros segun el parametro contar
    */
    static function listarBoletos_bancarios(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "bancos.boletos_bancarios", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>