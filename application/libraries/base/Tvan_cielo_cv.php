<?php

/**
* Class Tvan_cielo_cv
*
*Class  Tvan_cielo_cv maneja todos los aspectos de van_cielo_cv
*
* @package  SistemaIGA
* @subpackage Van_cielo_cv
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tvan_cielo_cv extends class_general{

    /**
    * codigo de van_cielo_cv
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_header de van_cielo_cv
    * @var cod_header int
    * @access public
    */
    public $cod_header;

    /**
    * cod_trailer de van_cielo_cv
    * @var cod_trailer int
    * @access public
    */
    public $cod_trailer;

    /**
    * tipo_registro de van_cielo_cv
    * @var tipo_registro smallint
    * @access public
    */
    public $tipo_registro;

    /**
    * establecimiento de van_cielo_cv
    * @var establecimiento bigint
    * @access public
    */
    public $establecimiento;

    /**
    * numero_ro de van_cielo_cv
    * @var numero_ro int
    * @access public
    */
    public $numero_ro;

    /**
    * numero_tarjeta de van_cielo_cv
    * @var numero_tarjeta varchar
    * @access public
    */
    public $numero_tarjeta;

    /**
    * fecha_venta de van_cielo_cv
    * @var fecha_venta date
    * @access public
    */
    public $fecha_venta;

    /**
    * signo_valor_compra de van_cielo_cv
    * @var signo_valor_compra varchar
    * @access public
    */
    public $signo_valor_compra;

    /**
    * valor_compra de van_cielo_cv
    * @var valor_compra decimal
    * @access public
    */
    public $valor_compra;

    /**
    * parcela de van_cielo_cv
    * @var parcela int
    * @access public
    */
    public $parcela;

    /**
    * total_parcelas de van_cielo_cv
    * @var total_parcelas int
    * @access public
    */
    public $total_parcelas;

    /**
    * motivo_rechazo de van_cielo_cv
    * @var motivo_rechazo varchar
    * @access public
    */
    public $motivo_rechazo;

    /**
    * codigo_autorizacion de van_cielo_cv
    * @var codigo_autorizacion varchar
    * @access public
    */
    public $codigo_autorizacion;

    /**
    * tid de van_cielo_cv
    * @var tid varchar
    * @access public
    */
    public $tid;

    /**
    * nsu_doc de van_cielo_cv
    * @var nsu_doc varchar
    * @access public
    */
    public $nsu_doc;

    /**
    * valor_complementar de van_cielo_cv
    * @var valor_complementar decimal
    * @access public
    */
    public $valor_complementar;

    /**
    * digitos_tarjeta de van_cielo_cv
    * @var digitos_tarjeta int
    * @access public
    */
    public $digitos_tarjeta;

    /**
    * valor_total_venta de van_cielo_cv
    * @var valor_total_venta decimal
    * @access public
    */
    public $valor_total_venta;

    /**
    * valor_proxima_parcela de van_cielo_cv
    * @var valor_proxima_parcela decimal
    * @access public
    */
    public $valor_proxima_parcela;

    /**
    * numero_nota_fiscal de van_cielo_cv
    * @var numero_nota_fiscal int
    * @access public
    */
    public $numero_nota_fiscal;

    /**
    * indicador_tarjeta_exterior de van_cielo_cv
    * @var indicador_tarjeta_exterior int
    * @access public
    */
    public $indicador_tarjeta_exterior;

    /**
    * numero_logico_terminal de van_cielo_cv
    * @var numero_logico_terminal varchar
    * @access public
    */
    public $numero_logico_terminal;

    /**
    * indicador_tasa_embarque de van_cielo_cv
    * @var indicador_tasa_embarque varchar
    * @access public
    */
    public $indicador_tasa_embarque;

    /**
    * referencia_codigo_pedido de van_cielo_cv
    * @var referencia_codigo_pedido varchar
    * @access public
    */
    public $referencia_codigo_pedido;

    /**
    * hora_transaccion de van_cielo_cv
    * @var hora_transaccion time
    * @access public
    */
    public $hora_transaccion;

    /**
    * numero_unico_transaccion de van_cielo_cv
    * @var numero_unico_transaccion varchar
    * @access public
    */
    public $numero_unico_transaccion;

    /**
    * indicador_cielo_premia de van_cielo_cv
    * @var indicador_cielo_premia varchar
    * @access public
    */
    public $indicador_cielo_premia;

    /**
    * uso_cielo de van_cielo_cv
    * @var uso_cielo varchar
    * @access public
    */
    public $uso_cielo;


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
    protected $nombreTabla = 'tarjetas.van_cielo_cv';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase van_cielo_cv
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
                $this->cod_header = $arrConstructor[0]['cod_header'];
                $this->cod_trailer = $arrConstructor[0]['cod_trailer'];
                $this->tipo_registro = $arrConstructor[0]['tipo_registro'];
                $this->establecimiento = $arrConstructor[0]['establecimiento'];
                $this->numero_ro = $arrConstructor[0]['numero_ro'];
                $this->numero_tarjeta = $arrConstructor[0]['numero_tarjeta'];
                $this->fecha_venta = $arrConstructor[0]['fecha_venta'];
                $this->signo_valor_compra = $arrConstructor[0]['signo_valor_compra'];
                $this->valor_compra = $arrConstructor[0]['valor_compra'];
                $this->parcela = $arrConstructor[0]['parcela'];
                $this->total_parcelas = $arrConstructor[0]['total_parcelas'];
                $this->motivo_rechazo = $arrConstructor[0]['motivo_rechazo'];
                $this->codigo_autorizacion = $arrConstructor[0]['codigo_autorizacion'];
                $this->tid = $arrConstructor[0]['tid'];
                $this->nsu_doc = $arrConstructor[0]['nsu_doc'];
                $this->valor_complementar = $arrConstructor[0]['valor_complementar'];
                $this->digitos_tarjeta = $arrConstructor[0]['digitos_tarjeta'];
                $this->valor_total_venta = $arrConstructor[0]['valor_total_venta'];
                $this->valor_proxima_parcela = $arrConstructor[0]['valor_proxima_parcela'];
                $this->numero_nota_fiscal = $arrConstructor[0]['numero_nota_fiscal'];
                $this->indicador_tarjeta_exterior = $arrConstructor[0]['indicador_tarjeta_exterior'];
                $this->numero_logico_terminal = $arrConstructor[0]['numero_logico_terminal'];
                $this->indicador_tasa_embarque = $arrConstructor[0]['indicador_tasa_embarque'];
                $this->referencia_codigo_pedido = $arrConstructor[0]['referencia_codigo_pedido'];
                $this->hora_transaccion = $arrConstructor[0]['hora_transaccion'];
                $this->numero_unico_transaccion = $arrConstructor[0]['numero_unico_transaccion'];
                $this->indicador_cielo_premia = $arrConstructor[0]['indicador_cielo_premia'];
                $this->uso_cielo = $arrConstructor[0]['uso_cielo'];
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
        $arrTemp['cod_header'] = $this->cod_header;
        $arrTemp['cod_trailer'] = $this->cod_trailer;
        $arrTemp['tipo_registro'] = $this->tipo_registro;
        $arrTemp['establecimiento'] = $this->establecimiento;
        $arrTemp['numero_ro'] = $this->numero_ro;
        $arrTemp['numero_tarjeta'] = $this->numero_tarjeta;
        $arrTemp['fecha_venta'] = $this->fecha_venta;
        $arrTemp['signo_valor_compra'] = $this->signo_valor_compra;
        $arrTemp['valor_compra'] = $this->valor_compra;
        $arrTemp['parcela'] = $this->parcela;
        $arrTemp['total_parcelas'] = $this->total_parcelas;
        $arrTemp['motivo_rechazo'] = $this->motivo_rechazo;
        $arrTemp['codigo_autorizacion'] = $this->codigo_autorizacion;
        $arrTemp['tid'] = $this->tid;
        $arrTemp['nsu_doc'] = $this->nsu_doc;
        $arrTemp['valor_complementar'] = $this->valor_complementar;
        $arrTemp['digitos_tarjeta'] = $this->digitos_tarjeta;
        $arrTemp['valor_total_venta'] = $this->valor_total_venta;
        $arrTemp['valor_proxima_parcela'] = $this->valor_proxima_parcela;
        $arrTemp['numero_nota_fiscal'] = $this->numero_nota_fiscal;
        $arrTemp['indicador_tarjeta_exterior'] = $this->indicador_tarjeta_exterior;
        $arrTemp['numero_logico_terminal'] = $this->numero_logico_terminal;
        $arrTemp['indicador_tasa_embarque'] = $this->indicador_tasa_embarque;
        $arrTemp['referencia_codigo_pedido'] = $this->referencia_codigo_pedido;
        $arrTemp['hora_transaccion'] = $this->hora_transaccion;
        $arrTemp['numero_unico_transaccion'] = $this->numero_unico_transaccion;
        $arrTemp['indicador_cielo_premia'] = $this->indicador_cielo_premia;
        $arrTemp['uso_cielo'] = $this->uso_cielo;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase van_cielo_cv o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarVan_cielo_cv(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto van_cielo_cv
     *
     * @return integer
     */
    public function getCodigoVan_cielo_cv(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de van_cielo_cv según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de van_cielo_cv y los valores son los valores a actualizar
     */
    public function setVan_cielo_cv(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_header"]))
            $retorno = "cod_header";
        else if (!isset($arrCamposValores["cod_trailer"]))
            $retorno = "cod_trailer";
        else if (!isset($arrCamposValores["tipo_registro"]))
            $retorno = "tipo_registro";
        else if (!isset($arrCamposValores["establecimiento"]))
            $retorno = "establecimiento";
        else if (!isset($arrCamposValores["numero_ro"]))
            $retorno = "numero_ro";
        else if (!isset($arrCamposValores["numero_tarjeta"]))
            $retorno = "numero_tarjeta";
        else if (!isset($arrCamposValores["fecha_venta"]))
            $retorno = "fecha_venta";
        else if (!isset($arrCamposValores["signo_valor_compra"]))
            $retorno = "signo_valor_compra";
        else if (!isset($arrCamposValores["valor_compra"]))
            $retorno = "valor_compra";
        else if (!isset($arrCamposValores["parcela"]))
            $retorno = "parcela";
        else if (!isset($arrCamposValores["total_parcelas"]))
            $retorno = "total_parcelas";
        else if (!isset($arrCamposValores["motivo_rechazo"]))
            $retorno = "motivo_rechazo";
        else if (!isset($arrCamposValores["codigo_autorizacion"]))
            $retorno = "codigo_autorizacion";
        else if (!isset($arrCamposValores["tid"]))
            $retorno = "tid";
        else if (!isset($arrCamposValores["nsu_doc"]))
            $retorno = "nsu_doc";
        else if (!isset($arrCamposValores["valor_complementar"]))
            $retorno = "valor_complementar";
        else if (!isset($arrCamposValores["digitos_tarjeta"]))
            $retorno = "digitos_tarjeta";
        else if (!isset($arrCamposValores["valor_total_venta"]))
            $retorno = "valor_total_venta";
        else if (!isset($arrCamposValores["valor_proxima_parcela"]))
            $retorno = "valor_proxima_parcela";
        else if (!isset($arrCamposValores["numero_nota_fiscal"]))
            $retorno = "numero_nota_fiscal";
        else if (!isset($arrCamposValores["indicador_tarjeta_exterior"]))
            $retorno = "indicador_tarjeta_exterior";
        else if (!isset($arrCamposValores["numero_logico_terminal"]))
            $retorno = "numero_logico_terminal";
        else if (!isset($arrCamposValores["indicador_tasa_embarque"]))
            $retorno = "indicador_tasa_embarque";
        else if (!isset($arrCamposValores["referencia_codigo_pedido"]))
            $retorno = "referencia_codigo_pedido";
        else if (!isset($arrCamposValores["hora_transaccion"]))
            $retorno = "hora_transaccion";
        else if (!isset($arrCamposValores["numero_unico_transaccion"]))
            $retorno = "numero_unico_transaccion";
        else if (!isset($arrCamposValores["indicador_cielo_premia"]))
            $retorno = "indicador_cielo_premia";
        else if (!isset($arrCamposValores["uso_cielo"]))
            $retorno = "uso_cielo";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setVan_cielo_cv");
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
    * retorna los campos presentes en la tabla van_cielo_cv en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposVan_cielo_cv(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "tarjetas.van_cielo_cv");
    }

    /**
    * Buscar registros en la tabla van_cielo_cv
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de van_cielo_cv o la cantdad de registros segun el parametro contar
    */
    static function listarVan_cielo_cv(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "tarjetas.van_cielo_cv", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>