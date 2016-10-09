<?php
/**
 * Created by PhpStorm.
 * User: Rodrigo Gliksberg
 * Date: 19/04/2016
 * Time: 15:33
 */


class Tprestador_paulistana extends class_general{

    /**
     * codigo de prestador_paulistana
     * @var codigo int
     * @access protected
     */
    protected $codigo;

    /**
     * nombre_configuracion de prestador_paulistana
     * @var nombre_configuracion varchar
     * @access public
     */
    public $nombre_configuracion;

    /**
     * cod_punto_venta de prestador_paulistana
     * @var cod_punto_venta int
     * @access public
     */
    public $cod_punto_venta;

    /**
     * inscripcion_municipal de prestador_paulistana
     * @var inscripcion_municipal varchar
     * @access public
     */
    public $inscripcion_municipal;

    /**
     * numero_serie de prestador_paulistana
     * @var numero_serie int
     * @access public
     */
    public $numero_serie;

    /**
     * tipo_nota de prestador_paulistana
     * @var tipo_nota enum
     * @access public
     */
    public $tipo_nota;

    /**
     * optante_simples_nacional de prestador_paulistana
     * @var optante_simples_nacional enum
     * @access public
     */
    public $optante_simples_nacional;

    /**
     * incentivador_cultural de prestador_paulistana
     * @var incentivador_cultural enum
     * @access public
     */
    public $incentivador_cultural;

    /**
     * regimen_especial_tibutario de prestador_paulistana
     * @var regimen_especial_tibutario enum
     * @access public
     */
    public $regimen_especial_tibutario;

    /**
     * valor_pis de prestador_paulistana
     * @var valor_pis decimal
     * @access public
     */
    public $valor_pis;

    /**
     * valor_cofins de prestador_paulistana
     * @var valor_cofins decimal
     * @access public
     */
    public $valor_cofins;

    /**
     * valor_inss de prestador_paulistana
     * @var valor_inss decimal
     * @access public
     */
    public $valor_inss;

    /**
     * valor_ir de prestador_paulistana
     * @var valor_ir decimal
     * @access public
     */
    public $valor_ir;

    /**
     * valor_csll de prestador_paulistana
     * @var valor_csll decimal
     * @access public
     */
    public $valor_csll;

    /**
     * alicuota de prestador_paulistana
     * @var alicuota decimal
     * @access public
     */
    public $alicuota;

    /**
     * item_lista_servicio de prestador_paulistana
     * @var item_lista_servicio varchar
     * @access public
     */
    public $item_lista_servicio;

    /**
     * codigo_tributacion_municipio de prestador_paulistana
     * @var codigo_tributacion_municipio varchar
     * @access public
     */
    public $codigo_tributacion_municipio;

    /**
     * porcentaje_facturar de prestador_paulistana
     * @var porcentaje_facturar decimal
     * @access public
     */
    public $porcentaje_facturar;


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
    protected $nombreTabla = 'general.prestador_paulistana';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase prestador_paulistana
     *
     * @param CI_DB_mysqli_driver $connection
     * @param integer $codigo (opcional) el codigo del objeto a crear
     */
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null){
        $this->oConnection = $conexion;
       // var_dump($codigo);
        $codigo = 1;
        if ($codigo != null && $codigo != -1){
            $arrConstructor = $this->_constructor($codigo);
            if (count($arrConstructor) > 0){
                $this->codigo = $arrConstructor[0]['codigo'];
                $this->nombre_configuracion = $arrConstructor[0]['nombre_configuracion'];
                $this->cod_punto_venta = $arrConstructor[0]['cod_punto_venta'];
                $this->inscripcion_municipal = $arrConstructor[0]['inscripcion_municipal'];
                $this->numero_serie = $arrConstructor[0]['numero_serie'];
                $this->tipo_nota = $arrConstructor[0]['tipo_nota'];
                $this->optante_simples_nacional = $arrConstructor[0]['optante_simples_nacional'];
                $this->incentivador_cultural = $arrConstructor[0]['incentivador_cultural'];
                $this->regimen_especial_tibutario = $arrConstructor[0]['regimen_especial_tibutario'];
                $this->valor_pis = $arrConstructor[0]['valor_pis'];
                $this->valor_cofins = $arrConstructor[0]['valor_cofins'];
                $this->valor_inss = $arrConstructor[0]['valor_inss'];
                $this->valor_ir = $arrConstructor[0]['valor_ir'];
                $this->valor_csll = $arrConstructor[0]['valor_csll'];
                $this->alicuota = $arrConstructor[0]['alicuota'];
                $this->item_lista_servicio = $arrConstructor[0]['item_lista_servicio'];
                $this->codigo_tributacion_municipio = $arrConstructor[0]['codigo_tributacion_municipio'];
                $this->porcentaje_facturar = $arrConstructor[0]['porcentaje_facturar'];
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
        $arrTemp['nombre_configuracion'] = $this->nombre_configuracion;
        $arrTemp['cod_punto_venta'] = $this->cod_punto_venta;
        $arrTemp['inscripcion_municipal'] = $this->inscripcion_municipal;
        $arrTemp['numero_serie'] = $this->numero_serie;
        $arrTemp['tipo_nota'] = $this->tipo_nota;
        $arrTemp['optante_simples_nacional'] = $this->optante_simples_nacional;
        $arrTemp['incentivador_cultural'] = $this->incentivador_cultural;
        $arrTemp['regimen_especial_tibutario'] = $this->regimen_especial_tibutario;
        $arrTemp['valor_pis'] = $this->valor_pis;
        $arrTemp['valor_cofins'] = $this->valor_cofins;
        $arrTemp['valor_inss'] = $this->valor_inss;
        $arrTemp['valor_ir'] = $this->valor_ir;
        $arrTemp['valor_csll'] = $this->valor_csll;
        $arrTemp['alicuota'] = $this->alicuota;
        $arrTemp['item_lista_servicio'] = $this->item_lista_servicio;
        $arrTemp['codigo_tributacion_municipio'] = $this->codigo_tributacion_municipio;
        $arrTemp['porcentaje_facturar'] = $this->porcentaje_facturar;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase prestador_paulistana o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarPrestador_paulistana(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto prestador_paulistana
     *
     * @return integer
     */
    public function getCodigoPrestador_paulistana(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de prestador_paulistana según los datos enviados en el array de parametro
     *
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de prestador_paulistana y los valores son los valores a actualizar
     */
    public function setPrestador_paulistana(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["nombre_configuracion"]))
            $retorno = "nombre_configuracion";
        else if (!isset($arrCamposValores["cod_punto_venta"]))
            $retorno = "cod_punto_venta";
        else if (!isset($arrCamposValores["inscripcion_municipal"]))
            $retorno = "inscripcion_municipal";
        else if (!isset($arrCamposValores["numero_serie"]))
            $retorno = "numero_serie";
        else if (!isset($arrCamposValores["tipo_nota"]))
            $retorno = "tipo_nota";
        else if (!isset($arrCamposValores["optante_simples_nacional"]))
            $retorno = "optante_simples_nacional";
        else if (!isset($arrCamposValores["incentivador_cultural"]))
            $retorno = "incentivador_cultural";
        else if (!isset($arrCamposValores["regimen_especial_tibutario"]))
            $retorno = "regimen_especial_tibutario";
        else if (!isset($arrCamposValores["valor_pis"]))
            $retorno = "valor_pis";
        else if (!isset($arrCamposValores["valor_cofins"]))
            $retorno = "valor_cofins";
        else if (!isset($arrCamposValores["valor_inss"]))
            $retorno = "valor_inss";
        else if (!isset($arrCamposValores["valor_ir"]))
            $retorno = "valor_ir";
        else if (!isset($arrCamposValores["valor_csll"]))
            $retorno = "valor_csll";
        else if (!isset($arrCamposValores["alicuota"]))
            $retorno = "alicuota";
        else if (!isset($arrCamposValores["item_lista_servicio"]))
            $retorno = "item_lista_servicio";
        else if (!isset($arrCamposValores["codigo_tributacion_municipio"]))
            $retorno = "codigo_tributacion_municipio";
        else if (!isset($arrCamposValores["porcentaje_facturar"]))
            $retorno = "porcentaje_facturar";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setPrestador_paulistana");
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
     * retorna los campos presentes en la tabla prestador_paulistana en formato array
     *
     * @param CI_DB_mysqli_driver $connection   La conexion actual
     * @return array
     */
    static function camposPrestador_paulistana(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.prestador_paulistana");
    }

    /**
     * Buscar registros en la tabla prestador_paulistana
     *
     * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
     * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
     * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
     * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
     * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
     * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
     * @return mixed    Retorna la lista de prestador_paulistana o la cantdad de registros segun el parametro contar
     */
    static function listarPrestador_paulistana(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.prestador_paulistana", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>