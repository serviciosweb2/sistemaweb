<?php

/**
* Class Tprestador_toolsnfe
*
*Class  Tprestador_toolsnfe maneja todos los aspectos de prestador_toolsnfe
*
* @package  SistemaIGA
* @subpackage Prestador_toolsnfe
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tprestador_toolsnfe extends class_general{

    /**
    * codigo de prestador_toolsnfe
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * nombre_configuracion de prestador_toolsnfe
    * @var nombre_configuracion varchar
    * @access public
    */
    public $nombre_configuracion;

    /**
    * cod_punto_venta de prestador_toolsnfe
    * @var cod_punto_venta int
    * @access public
    */
    public $cod_punto_venta;

    /**
    * ie de prestador_toolsnfe
    * @var ie varchar
    * @access public
    */
    public $ie;

    /**
    * cnae de prestador_toolsnfe
    * @var cnae varchar
    * @access public
    */
    public $cnae;

    /**
    * ncm de prestador_toolsnfe
    * @var ncm varchar
    * @access public
    */
    public $ncm;

    /**
    * inscripcion_municipal de prestador_toolsnfe
    * @var inscripcion_municipal varchar
    * @access public
    */
    public $inscripcion_municipal;

    /**
    * codigo_numerico de prestador_toolsnfe
    * @var codigo_numerico varchar
    * @access public
    */
    public $codigo_numerico;

    /**
    * forma_pago de prestador_toolsnfe
    * @var forma_pago int
    * @access public
    */
    public $forma_pago;

    /**
    * situacion_tributaria de prestador_toolsnfe
    * @var situacion_tributaria int
    * @access public
    */
    public $situacion_tributaria;

    /**
    * regimen_tributario de prestador_toolsnfe
    * @var regimen_tributario int
    * @access public
    */
    public $regimen_tributario;

    /**
    * cfop de prestador_toolsnfe
    * @var cfop varchar
    * @access public
    */
    public $cfop;

    /**
    * icms de prestador_toolsnfe
    * @var icms decimal
    * @access public
    */
    public $icms;

    /**
    * motivo de prestador_toolsnfe
    * @var motivo int
    * @access public
    */
    public $motivo;

    /**
    * transporte de prestador_toolsnfe
    * @var transporte int
    * @access public
    */
    public $transporte;

    /**
    * descripcion de prestador_toolsnfe
    * @var descripcion varchar
    * @access public
    */
    public $descripcion;

    /**
    * codigo_producto de prestador_toolsnfe
    * @var codigo_producto varchar
    * @access public
    */
    public $codigo_producto;

    /**
    * nombre_producto de prestador_toolsnfe
    * @var nombre_producto varchar
    * @access public
    */
    public $nombre_producto;

    /**
    * porcentaje_facturar de prestador_toolsnfe
    * @var porcentaje_facturar double
    * @access public
    */
    public $porcentaje_facturar;

    /**
    * ultimo_lote de prestador_toolsnfe
    * @var ultimo_lote int
    * @access public
    */
    public $ultimo_lote;

    /**
    * estado de prestador_toolsnfe
    * @var estado enum (requerido)
    * @access public
    */
    public $estado;

    /**
    * cfop_juridico de prestador_toolsnfe
    * @var cfop_juridico varchar (requerido)
    * @access public
    */
    public $cfop_juridico;

    /**
    * cfop_fisico de prestador_toolsnfe
    * @var cfop_fisico varchar (requerido)
    * @access public
    */
    public $cfop_fisico;


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
    protected $nombreTabla = 'general.prestador_toolsnfe';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase prestador_toolsnfe
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
                $this->ie = $arrConstructor[0]['ie'];
                $this->cnae = $arrConstructor[0]['cnae'];
                $this->ncm = $arrConstructor[0]['ncm'];
                $this->inscripcion_municipal = $arrConstructor[0]['inscripcion_municipal'];
                $this->codigo_numerico = $arrConstructor[0]['codigo_numerico'];
                $this->forma_pago = $arrConstructor[0]['forma_pago'];
                $this->situacion_tributaria = $arrConstructor[0]['situacion_tributaria'];
                $this->regimen_tributario = $arrConstructor[0]['regimen_tributario'];
                $this->cfop = $arrConstructor[0]['cfop'];
                $this->icms = $arrConstructor[0]['icms'];
                $this->motivo = $arrConstructor[0]['motivo'];
                $this->transporte = $arrConstructor[0]['transporte'];
                $this->descripcion = $arrConstructor[0]['descripcion'];
                $this->codigo_producto = $arrConstructor[0]['codigo_producto'];
                $this->nombre_producto = $arrConstructor[0]['nombre_producto'];
                $this->porcentaje_facturar = $arrConstructor[0]['porcentaje_facturar'];
                $this->ultimo_lote = $arrConstructor[0]['ultimo_lote'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->cfop_juridico = $arrConstructor[0]['cfop_juridico'];
                $this->cfop_fisico = $arrConstructor[0]['cfop_fisico'];
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
        $arrTemp['ie'] = $this->ie;
        $arrTemp['cnae'] = $this->cnae;
        $arrTemp['ncm'] = $this->ncm;
        $arrTemp['inscripcion_municipal'] = $this->inscripcion_municipal;
        $arrTemp['codigo_numerico'] = $this->codigo_numerico;
        $arrTemp['forma_pago'] = $this->forma_pago;
        $arrTemp['situacion_tributaria'] = $this->situacion_tributaria;
        $arrTemp['regimen_tributario'] = $this->regimen_tributario;
        $arrTemp['cfop'] = $this->cfop;
        $arrTemp['icms'] = $this->icms;
        $arrTemp['motivo'] = $this->motivo;
        $arrTemp['transporte'] = $this->transporte;
        $arrTemp['descripcion'] = $this->descripcion;
        $arrTemp['codigo_producto'] = $this->codigo_producto;
        $arrTemp['nombre_producto'] = $this->nombre_producto;
        $arrTemp['porcentaje_facturar'] = $this->porcentaje_facturar;
        $arrTemp['ultimo_lote'] = $this->ultimo_lote == '' ? '0' : $this->ultimo_lote;
        $arrTemp['estado'] = $this->estado == '' ? null : $this->estado;
        $arrTemp['cfop_juridico'] = $this->cfop_juridico == '' ? null : $this->cfop_juridico;
        $arrTemp['cfop_fisico'] = $this->cfop_fisico == '' ? null : $this->cfop_fisico;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase prestador_toolsnfe o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarPrestador_toolsnfe(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto prestador_toolsnfe
     *
     * @return integer
     */
    public function getCodigoPrestador_toolsnfe(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de prestador_toolsnfe según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de prestador_toolsnfe y los valores son los valores a actualizar
     */
    public function setPrestador_toolsnfe(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["nombre_configuracion"]))
            $retorno = "nombre_configuracion";
        else if (!isset($arrCamposValores["cod_punto_venta"]))
            $retorno = "cod_punto_venta";
        else if (!isset($arrCamposValores["ie"]))
            $retorno = "ie";
        else if (!isset($arrCamposValores["cnae"]))
            $retorno = "cnae";
        else if (!isset($arrCamposValores["ncm"]))
            $retorno = "ncm";
        else if (!isset($arrCamposValores["inscripcion_municipal"]))
            $retorno = "inscripcion_municipal";
        else if (!isset($arrCamposValores["codigo_numerico"]))
            $retorno = "codigo_numerico";
        else if (!isset($arrCamposValores["forma_pago"]))
            $retorno = "forma_pago";
        else if (!isset($arrCamposValores["situacion_tributaria"]))
            $retorno = "situacion_tributaria";
        else if (!isset($arrCamposValores["regimen_tributario"]))
            $retorno = "regimen_tributario";
        else if (!isset($arrCamposValores["cfop"]))
            $retorno = "cfop";
        else if (!isset($arrCamposValores["icms"]))
            $retorno = "icms";
        else if (!isset($arrCamposValores["motivo"]))
            $retorno = "motivo";
        else if (!isset($arrCamposValores["transporte"]))
            $retorno = "transporte";
        else if (!isset($arrCamposValores["descripcion"]))
            $retorno = "descripcion";
        else if (!isset($arrCamposValores["codigo_producto"]))
            $retorno = "codigo_producto";
        else if (!isset($arrCamposValores["nombre_producto"]))
            $retorno = "nombre_producto";
        else if (!isset($arrCamposValores["porcentaje_facturar"]))
            $retorno = "porcentaje_facturar";
        else if (!isset($arrCamposValores["ultimo_lote"]))
            $retorno = "ultimo_lote";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setPrestador_toolsnfe");
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
    * retorna los campos presentes en la tabla prestador_toolsnfe en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposPrestador_toolsnfe(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.prestador_toolsnfe");
    }

    /**
    * Buscar registros en la tabla prestador_toolsnfe
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de prestador_toolsnfe o la cantdad de registros segun el parametro contar
    */
    static function listarPrestador_toolsnfe(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.prestador_toolsnfe", $condiciones, $limite, $orden, $grupo, $contar);
    }
}