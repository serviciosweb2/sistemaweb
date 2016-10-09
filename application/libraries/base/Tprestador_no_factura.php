<?php

/**
* Class Tprestador_no_factura
*
*Class  Tprestador_no_factura maneja todos los aspectos de prestador_no_factura
*
* @package  SistemaIGA
* @subpackage Prestador_no_factura
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tprestador_no_factura extends class_general{

    /**
    * codigo de prestador_no_factura
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * nombre_configuracion de prestador_no_factura
    * @var nombre_configuracion varchar
    * @access public
    */
    public $nombre_configuracion;

    /**
    * cod_punto_venta de prestador_no_factura
    * @var cod_punto_venta int
    * @access public
    */
    public $cod_punto_venta;

    /**
    * codigo_servicio de prestador_no_factura
    * @var codigo_servicio varchar
    * @access public
    */
    public $codigo_servicio;

    /**
    * nombre_servicio de prestador_no_factura
    * @var nombre_servicio varchar
    * @access public
    */
    public $nombre_servicio;

    /**
    * valor_pis de prestador_no_factura
    * @var valor_pis decimal
    * @access public
    */
    public $valor_pis;

    /**
    * valor_cofins de prestador_no_factura
    * @var valor_cofins decimal
    * @access public
    */
    public $valor_cofins;

    /**
    * valor_inss de prestador_no_factura
    * @var valor_inss decimal
    * @access public
    */
    public $valor_inss;

    /**
    * valor_ir de prestador_no_factura
    * @var valor_ir decimal
    * @access public
    */
    public $valor_ir;

    /**
    * valor_csll de prestador_no_factura
    * @var valor_csll decimal
    * @access public
    */
    public $valor_csll;

    /**
    * alicuota de prestador_no_factura
    * @var alicuota decimal
    * @access public
    */
    public $alicuota;

    /**
    * inscripcion_municipal de prestador_no_factura
    * @var inscripcion_municipal varchar
    * @access public
    */
    public $inscripcion_municipal;

    /**
    * codigo_actividad de prestador_no_factura
    * @var codigo_actividad varchar
    * @access public
    */
    public $codigo_actividad;

    /**
    * tipo_nota de prestador_no_factura
    * @var tipo_nota enum
    * @access public
    */
    public $tipo_nota;

    /**
    * alicuota_pis de prestador_no_factura
    * @var alicuota_pis decimal
    * @access public
    */
    public $alicuota_pis;

    /**
    * alicuota_cofins de prestador_no_factura
    * @var alicuota_cofins decimal
    * @access public
    */
    public $alicuota_cofins;

    /**
    * alicuota_inss de prestador_no_factura
    * @var alicuota_inss decimal
    * @access public
    */
    public $alicuota_inss;

    /**
    * alicuota_ir de prestador_no_factura
    * @var alicuota_ir decimal
    * @access public
    */
    public $alicuota_ir;

    /**
    * alicuota_csll de prestador_no_factura
    * @var alicuota_csll decimal
    * @access public
    */
    public $alicuota_csll;

    /**
    * cnae de prestador_no_factura
    * @var cnae varchar
    * @access public
    */
    public $cnae;

    /**
    * porcentaje_facturar de prestador_no_factura
    * @var porcentaje_facturar decimal
    * @access public
    */
    public $porcentaje_facturar;

    /**
    * regimen_especial_tributario de prestador_no_factura
    * @var regimen_especial_tributario int
    * @access public
    */
    public $regimen_especial_tributario;


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
    protected $nombreTabla = 'general.prestador_no_factura';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase prestador_no_factura
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
                $this->nombre_configuracion = $arrConstructor[0]['nombre_configuracion'];
                $this->cod_punto_venta = $arrConstructor[0]['cod_punto_venta'];
                $this->codigo_servicio = $arrConstructor[0]['codigo_servicio'];
                $this->nombre_servicio = $arrConstructor[0]['nombre_servicio'];
                $this->valor_pis = $arrConstructor[0]['valor_pis'];
                $this->valor_cofins = $arrConstructor[0]['valor_cofins'];
                $this->valor_inss = $arrConstructor[0]['valor_inss'];
                $this->valor_ir = $arrConstructor[0]['valor_ir'];
                $this->valor_csll = $arrConstructor[0]['valor_csll'];
                $this->alicuota = $arrConstructor[0]['alicuota'];
                $this->inscripcion_municipal = $arrConstructor[0]['inscripcion_municipal'];
                $this->codigo_actividad = $arrConstructor[0]['codigo_actividad'];
                $this->tipo_nota = $arrConstructor[0]['tipo_nota'];
                $this->alicuota_pis = $arrConstructor[0]['alicuota_pis'];
                $this->alicuota_cofins = $arrConstructor[0]['alicuota_cofins'];
                $this->alicuota_inss = $arrConstructor[0]['alicuota_inss'];
                $this->alicuota_ir = $arrConstructor[0]['alicuota_ir'];
                $this->alicuota_csll = $arrConstructor[0]['alicuota_csll'];
                $this->cnae = $arrConstructor[0]['cnae'];
                $this->porcentaje_facturar = $arrConstructor[0]['porcentaje_facturar'];
                $this->regimen_especial_tributario = $arrConstructor[0]['regimen_especial_tributario'];
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
        $arrTemp['codigo_servicio'] = $this->codigo_servicio;
        $arrTemp['nombre_servicio'] = $this->nombre_servicio;
        $arrTemp['valor_pis'] = $this->valor_pis;
        $arrTemp['valor_cofins'] = $this->valor_cofins;
        $arrTemp['valor_inss'] = $this->valor_inss;
        $arrTemp['valor_ir'] = $this->valor_ir;
        $arrTemp['valor_csll'] = $this->valor_csll;
        $arrTemp['alicuota'] = $this->alicuota;
        $arrTemp['inscripcion_municipal'] = $this->inscripcion_municipal;
        $arrTemp['codigo_actividad'] = $this->codigo_actividad;
        $arrTemp['tipo_nota'] = $this->tipo_nota;
        $arrTemp['alicuota_pis'] = $this->alicuota_pis;
        $arrTemp['alicuota_cofins'] = $this->alicuota_cofins;
        $arrTemp['alicuota_inss'] = $this->alicuota_inss;
        $arrTemp['alicuota_ir'] = $this->alicuota_ir;
        $arrTemp['alicuota_csll'] = $this->alicuota_csll;
        $arrTemp['cnae'] = $this->cnae;
        $arrTemp['porcentaje_facturar'] = $this->porcentaje_facturar;
        $arrTemp['regimen_especial_tributario'] = $this->regimen_especial_tributario;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase prestador_no_factura o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarPrestador_no_factura(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto prestador_no_factura
     *
     * @return integer
     */
    public function getCodigoPrestador_no_factura(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de prestador_no_factura según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de prestador_no_factura y los valores son los valores a actualizar
     */
    public function setPrestador_no_factura(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["nombre_configuracion"]))
            $retorno = "nombre_configuracion";
        else if (!isset($arrCamposValores["cod_punto_venta"]))
            $retorno = "cod_punto_venta";
        else if (!isset($arrCamposValores["codigo_servicio"]))
            $retorno = "codigo_servicio";
        else if (!isset($arrCamposValores["nombre_servicio"]))
            $retorno = "nombre_servicio";
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
        else if (!isset($arrCamposValores["inscripcion_municipal"]))
            $retorno = "inscripcion_municipal";
        else if (!isset($arrCamposValores["codigo_actividad"]))
            $retorno = "codigo_actividad";
        else if (!isset($arrCamposValores["tipo_nota"]))
            $retorno = "tipo_nota";
        else if (!isset($arrCamposValores["alicuota_pis"]))
            $retorno = "alicuota_pis";
        else if (!isset($arrCamposValores["alicuota_cofins"]))
            $retorno = "alicuota_cofins";
        else if (!isset($arrCamposValores["alicuota_inss"]))
            $retorno = "alicuota_inss";
        else if (!isset($arrCamposValores["alicuota_ir"]))
            $retorno = "alicuota_ir";
        else if (!isset($arrCamposValores["alicuota_csll"]))
            $retorno = "alicuota_csll";
        else if (!isset($arrCamposValores["cnae"]))
            $retorno = "cnae";
        else if (!isset($arrCamposValores["porcentaje_facturar"]))
            $retorno = "porcentaje_facturar";
        else if (!isset($arrCamposValores["regimen_especial_tributario"]))
            $retorno = "regimen_especial_tributario";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setPrestador_no_factura");
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
    * retorna los campos presentes en la tabla prestador_no_factura en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposPrestador_no_factura(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.prestador_no_factura");
    }

    /**
    * Buscar registros en la tabla prestador_no_factura
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de prestador_no_factura o la cantdad de registros segun el parametro contar
    */
    static function listarPrestador_no_factura(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.prestador_no_factura", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>