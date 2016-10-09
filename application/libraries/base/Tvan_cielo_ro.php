<?php

/**
* Class Tvan_cielo_ro
*
*Class  Tvan_cielo_ro maneja todos los aspectos de van_cielo_ro
*
* @package  SistemaIGA
* @subpackage Van_cielo_ro
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tvan_cielo_ro extends class_general{

    /**
    * codigo de van_cielo_ro
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_header de van_cielo_ro
    * @var cod_header int
    * @access public
    */
    public $cod_header;

    /**
    * cod_trailer de van_cielo_ro
    * @var cod_trailer int
    * @access public
    */
    public $cod_trailer;

    /**
    * tipo_registro de van_cielo_ro
    * @var tipo_registro smallint
    * @access public
    */
    public $tipo_registro;

    /**
    * establecimiento de van_cielo_ro
    * @var establecimiento bigint
    * @access public
    */
    public $establecimiento;

    /**
    * numero_ro de van_cielo_ro
    * @var numero_ro int
    * @access public
    */
    public $numero_ro;

    /**
    * parcela de van_cielo_ro
    * @var parcela varchar
    * @access public
    */
    public $parcela;

    /**
    * filler de van_cielo_ro
    * @var filler varchar
    * @access public
    */
    public $filler;

    /**
    * plano de van_cielo_ro
    * @var plano varchar
    * @access public
    */
    public $plano;

    /**
    * tipo_transaccion de van_cielo_ro
    * @var tipo_transaccion int
    * @access public
    */
    public $tipo_transaccion;

    /**
    * fecha_presentacion de van_cielo_ro
    * @var fecha_presentacion date
    * @access public
    */
    public $fecha_presentacion;

    /**
    * fecha_prevista_pago de van_cielo_ro
    * @var fecha_prevista_pago date
    * @access public
    */
    public $fecha_prevista_pago;

    /**
    * fecha_envio_banco de van_cielo_ro
    * @var fecha_envio_banco date (requerido)
    * @access public
    */
    public $fecha_envio_banco;

    /**
    * signo_valor_bruto de van_cielo_ro
    * @var signo_valor_bruto varchar
    * @access public
    */
    public $signo_valor_bruto;

    /**
    * valor_bruto de van_cielo_ro
    * @var valor_bruto decimal
    * @access public
    */
    public $valor_bruto;

    /**
    * signo_comision de van_cielo_ro
    * @var signo_comision varchar
    * @access public
    */
    public $signo_comision;

    /**
    * valor_comision de van_cielo_ro
    * @var valor_comision decimal
    * @access public
    */
    public $valor_comision;

    /**
    * signo_valor_rechazado de van_cielo_ro
    * @var signo_valor_rechazado varchar
    * @access public
    */
    public $signo_valor_rechazado;

    /**
    * valor_rechazado de van_cielo_ro
    * @var valor_rechazado decimal
    * @access public
    */
    public $valor_rechazado;

    /**
    * signo_valor_liquido de van_cielo_ro
    * @var signo_valor_liquido varchar
    * @access public
    */
    public $signo_valor_liquido;

    /**
    * valor_liquido de van_cielo_ro
    * @var valor_liquido decimal
    * @access public
    */
    public $valor_liquido;

    /**
    * banco de van_cielo_ro
    * @var banco int
    * @access public
    */
    public $banco;

    /**
    * agencia de van_cielo_ro
    * @var agencia int
    * @access public
    */
    public $agencia;

    /**
    * cuenta_corriente de van_cielo_ro
    * @var cuenta_corriente varchar
    * @access public
    */
    public $cuenta_corriente;

    /**
    * estado_pago de van_cielo_ro
    * @var estado_pago int
    * @access public
    */
    public $estado_pago;

    /**
    * cantidad_cv_aceptados de van_cielo_ro
    * @var cantidad_cv_aceptados int
    * @access public
    */
    public $cantidad_cv_aceptados;

    /**
    * identificador_producto_descartar de van_cielo_ro
    * @var identificador_producto_descartar int
    * @access public
    */
    public $identificador_producto_descartar;

    /**
    * cantidad_cv_rechazados de van_cielo_ro
    * @var cantidad_cv_rechazados int
    * @access public
    */
    public $cantidad_cv_rechazados;

    /**
    * identificador_reventa de van_cielo_ro
    * @var identificador_reventa varchar
    * @access public
    */
    public $identificador_reventa;

    /**
    * fecha_captura_transaccion de van_cielo_ro
    * @var fecha_captura_transaccion date (requerido)
    * @access public
    */
    public $fecha_captura_transaccion;

    /**
    * origen_ajuste de van_cielo_ro
    * @var origen_ajuste varchar
    * @access public
    */
    public $origen_ajuste;

    /**
    * valor_complementar de van_cielo_ro
    * @var valor_complementar decimal
    * @access public
    */
    public $valor_complementar;

    /**
    * identificador_producto_financiero de van_cielo_ro
    * @var identificador_producto_financiero varchar
    * @access public
    */
    public $identificador_producto_financiero;

    /**
    * numero_operacion_financiera de van_cielo_ro
    * @var numero_operacion_financiera bigint
    * @access public
    */
    public $numero_operacion_financiera;

    /**
    * signo_valor_bruto_anticipado de van_cielo_ro
    * @var signo_valor_bruto_anticipado varchar
    * @access public
    */
    public $signo_valor_bruto_anticipado;

    /**
    * valor_bruto_anticipado de van_cielo_ro
    * @var valor_bruto_anticipado decimal
    * @access public
    */
    public $valor_bruto_anticipado;

    /**
    * codigo_bandera de van_cielo_ro
    * @var codigo_bandera int
    * @access public
    */
    public $codigo_bandera;

    /**
    * numero_unico_ro de van_cielo_ro
    * @var numero_unico_ro varchar
    * @access public
    */
    public $numero_unico_ro;

    /**
    * tasa_comision de van_cielo_ro
    * @var tasa_comision decimal
    * @access public
    */
    public $tasa_comision;

    /**
    * tarifa de van_cielo_ro
    * @var tarifa decimal
    * @access public
    */
    public $tarifa;

    /**
    * tasa_garantia de van_cielo_ro
    * @var tasa_garantia decimal
    * @access public
    */
    public $tasa_garantia;

    /**
    * medio_captura de van_cielo_ro
    * @var medio_captura varchar
    * @access public
    */
    public $medio_captura;

    /**
    * numero_logico_terminal de van_cielo_ro
    * @var numero_logico_terminal varchar
    * @access public
    */
    public $numero_logico_terminal;

    /**
    * identificador_producto de van_cielo_ro
    * @var identificador_producto varchar
    * @access public
    */
    public $identificador_producto;

    /**
    * uso_cielo de van_cielo_ro
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
    protected $nombreTabla = 'tarjetas.van_cielo_ro';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase van_cielo_ro
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
                $this->parcela = $arrConstructor[0]['parcela'];
                $this->filler = $arrConstructor[0]['filler'];
                $this->plano = $arrConstructor[0]['plano'];
                $this->tipo_transaccion = $arrConstructor[0]['tipo_transaccion'];
                $this->fecha_presentacion = $arrConstructor[0]['fecha_presentacion'];
                $this->fecha_prevista_pago = $arrConstructor[0]['fecha_prevista_pago'];
                $this->fecha_envio_banco = $arrConstructor[0]['fecha_envio_banco'];
                $this->signo_valor_bruto = $arrConstructor[0]['signo_valor_bruto'];
                $this->valor_bruto = $arrConstructor[0]['valor_bruto'];
                $this->signo_comision = $arrConstructor[0]['signo_comision'];
                $this->valor_comision = $arrConstructor[0]['valor_comision'];
                $this->signo_valor_rechazado = $arrConstructor[0]['signo_valor_rechazado'];
                $this->valor_rechazado = $arrConstructor[0]['valor_rechazado'];
                $this->signo_valor_liquido = $arrConstructor[0]['signo_valor_liquido'];
                $this->valor_liquido = $arrConstructor[0]['valor_liquido'];
                $this->banco = $arrConstructor[0]['banco'];
                $this->agencia = $arrConstructor[0]['agencia'];
                $this->cuenta_corriente = $arrConstructor[0]['cuenta_corriente'];
                $this->estado_pago = $arrConstructor[0]['estado_pago'];
                $this->cantidad_cv_aceptados = $arrConstructor[0]['cantidad_cv_aceptados'];
                $this->identificador_producto_descartar = $arrConstructor[0]['identificador_producto_descartar'];
                $this->cantidad_cv_rechazados = $arrConstructor[0]['cantidad_cv_rechazados'];
                $this->identificador_reventa = $arrConstructor[0]['identificador_reventa'];
                $this->fecha_captura_transaccion = $arrConstructor[0]['fecha_captura_transaccion'];
                $this->origen_ajuste = $arrConstructor[0]['origen_ajuste'];
                $this->valor_complementar = $arrConstructor[0]['valor_complementar'];
                $this->identificador_producto_financiero = $arrConstructor[0]['identificador_producto_financiero'];
                $this->numero_operacion_financiera = $arrConstructor[0]['numero_operacion_financiera'];
                $this->signo_valor_bruto_anticipado = $arrConstructor[0]['signo_valor_bruto_anticipado'];
                $this->valor_bruto_anticipado = $arrConstructor[0]['valor_bruto_anticipado'];
                $this->codigo_bandera = $arrConstructor[0]['codigo_bandera'];
                $this->numero_unico_ro = $arrConstructor[0]['numero_unico_ro'];
                $this->tasa_comision = $arrConstructor[0]['tasa_comision'];
                $this->tarifa = $arrConstructor[0]['tarifa'];
                $this->tasa_garantia = $arrConstructor[0]['tasa_garantia'];
                $this->medio_captura = $arrConstructor[0]['medio_captura'];
                $this->numero_logico_terminal = $arrConstructor[0]['numero_logico_terminal'];
                $this->identificador_producto = $arrConstructor[0]['identificador_producto'];
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
        $arrTemp['tipo_registro'] = $this->tipo_registro == '' ? '0' : $this->tipo_registro;
        $arrTemp['establecimiento'] = $this->establecimiento;
        $arrTemp['numero_ro'] = $this->numero_ro;
        $arrTemp['parcela'] = $this->parcela;
        $arrTemp['filler'] = $this->filler;
        $arrTemp['plano'] = $this->plano;
        $arrTemp['tipo_transaccion'] = $this->tipo_transaccion;
        $arrTemp['fecha_presentacion'] = $this->fecha_presentacion;
        $arrTemp['fecha_prevista_pago'] = $this->fecha_prevista_pago;
        $arrTemp['fecha_envio_banco'] = $this->fecha_envio_banco == '' ? null : $this->fecha_envio_banco;
        $arrTemp['signo_valor_bruto'] = $this->signo_valor_bruto;
        $arrTemp['valor_bruto'] = $this->valor_bruto;
        $arrTemp['signo_comision'] = $this->signo_comision;
        $arrTemp['valor_comision'] = $this->valor_comision;
        $arrTemp['signo_valor_rechazado'] = $this->signo_valor_rechazado;
        $arrTemp['valor_rechazado'] = $this->valor_rechazado;
        $arrTemp['signo_valor_liquido'] = $this->signo_valor_liquido;
        $arrTemp['valor_liquido'] = $this->valor_liquido;
        $arrTemp['banco'] = $this->banco;
        $arrTemp['agencia'] = $this->agencia;
        $arrTemp['cuenta_corriente'] = $this->cuenta_corriente;
        $arrTemp['estado_pago'] = $this->estado_pago;
        $arrTemp['cantidad_cv_aceptados'] = $this->cantidad_cv_aceptados;
        $arrTemp['identificador_producto_descartar'] = $this->identificador_producto_descartar;
        $arrTemp['cantidad_cv_rechazados'] = $this->cantidad_cv_rechazados;
        $arrTemp['identificador_reventa'] = $this->identificador_reventa;
        $arrTemp['fecha_captura_transaccion'] = $this->fecha_captura_transaccion == '' ? null : $this->fecha_captura_transaccion;
        $arrTemp['origen_ajuste'] = $this->origen_ajuste;
        $arrTemp['valor_complementar'] = $this->valor_complementar;
        $arrTemp['identificador_producto_financiero'] = $this->identificador_producto_financiero;
        $arrTemp['numero_operacion_financiera'] = $this->numero_operacion_financiera;
        $arrTemp['signo_valor_bruto_anticipado'] = $this->signo_valor_bruto_anticipado;
        $arrTemp['valor_bruto_anticipado'] = $this->valor_bruto_anticipado;
        $arrTemp['codigo_bandera'] = $this->codigo_bandera;
        $arrTemp['numero_unico_ro'] = $this->numero_unico_ro;
        $arrTemp['tasa_comision'] = $this->tasa_comision;
        $arrTemp['tarifa'] = $this->tarifa;
        $arrTemp['tasa_garantia'] = $this->tasa_garantia;
        $arrTemp['medio_captura'] = $this->medio_captura;
        $arrTemp['numero_logico_terminal'] = $this->numero_logico_terminal;
        $arrTemp['identificador_producto'] = $this->identificador_producto;
        $arrTemp['uso_cielo'] = $this->uso_cielo;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase van_cielo_ro o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarVan_cielo_ro(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto van_cielo_ro
     *
     * @return integer
     */
    public function getCodigoVan_cielo_ro(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de van_cielo_ro según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de van_cielo_ro y los valores son los valores a actualizar
     */
    public function setVan_cielo_ro(array $arrCamposValores){
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
        else if (!isset($arrCamposValores["parcela"]))
            $retorno = "parcela";
        else if (!isset($arrCamposValores["filler"]))
            $retorno = "filler";
        else if (!isset($arrCamposValores["plano"]))
            $retorno = "plano";
        else if (!isset($arrCamposValores["tipo_transaccion"]))
            $retorno = "tipo_transaccion";
        else if (!isset($arrCamposValores["fecha_presentacion"]))
            $retorno = "fecha_presentacion";
        else if (!isset($arrCamposValores["fecha_prevista_pago"]))
            $retorno = "fecha_prevista_pago";
        else if (!isset($arrCamposValores["signo_valor_bruto"]))
            $retorno = "signo_valor_bruto";
        else if (!isset($arrCamposValores["valor_bruto"]))
            $retorno = "valor_bruto";
        else if (!isset($arrCamposValores["signo_comision"]))
            $retorno = "signo_comision";
        else if (!isset($arrCamposValores["valor_comision"]))
            $retorno = "valor_comision";
        else if (!isset($arrCamposValores["signo_valor_rechazado"]))
            $retorno = "signo_valor_rechazado";
        else if (!isset($arrCamposValores["valor_rechazado"]))
            $retorno = "valor_rechazado";
        else if (!isset($arrCamposValores["signo_valor_liquido"]))
            $retorno = "signo_valor_liquido";
        else if (!isset($arrCamposValores["valor_liquido"]))
            $retorno = "valor_liquido";
        else if (!isset($arrCamposValores["banco"]))
            $retorno = "banco";
        else if (!isset($arrCamposValores["agencia"]))
            $retorno = "agencia";
        else if (!isset($arrCamposValores["cuenta_corriente"]))
            $retorno = "cuenta_corriente";
        else if (!isset($arrCamposValores["estado_pago"]))
            $retorno = "estado_pago";
        else if (!isset($arrCamposValores["cantidad_cv_aceptados"]))
            $retorno = "cantidad_cv_aceptados";
        else if (!isset($arrCamposValores["identificador_producto_descartar"]))
            $retorno = "identificador_producto_descartar";
        else if (!isset($arrCamposValores["cantidad_cv_rechazados"]))
            $retorno = "cantidad_cv_rechazados";
        else if (!isset($arrCamposValores["identificador_reventa"]))
            $retorno = "identificador_reventa";
        else if (!isset($arrCamposValores["origen_ajuste"]))
            $retorno = "origen_ajuste";
        else if (!isset($arrCamposValores["valor_complementar"]))
            $retorno = "valor_complementar";
        else if (!isset($arrCamposValores["identificador_producto_financiero"]))
            $retorno = "identificador_producto_financiero";
        else if (!isset($arrCamposValores["numero_operacion_financiera"]))
            $retorno = "numero_operacion_financiera";
        else if (!isset($arrCamposValores["signo_valor_bruto_anticipado"]))
            $retorno = "signo_valor_bruto_anticipado";
        else if (!isset($arrCamposValores["valor_bruto_anticipado"]))
            $retorno = "valor_bruto_anticipado";
        else if (!isset($arrCamposValores["codigo_bandera"]))
            $retorno = "codigo_bandera";
        else if (!isset($arrCamposValores["numero_unico_ro"]))
            $retorno = "numero_unico_ro";
        else if (!isset($arrCamposValores["tasa_comision"]))
            $retorno = "tasa_comision";
        else if (!isset($arrCamposValores["tarifa"]))
            $retorno = "tarifa";
        else if (!isset($arrCamposValores["tasa_garantia"]))
            $retorno = "tasa_garantia";
        else if (!isset($arrCamposValores["medio_captura"]))
            $retorno = "medio_captura";
        else if (!isset($arrCamposValores["numero_logico_terminal"]))
            $retorno = "numero_logico_terminal";
        else if (!isset($arrCamposValores["identificador_producto"]))
            $retorno = "identificador_producto";
        else if (!isset($arrCamposValores["uso_cielo"]))
            $retorno = "uso_cielo";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setVan_cielo_ro");
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
    * retorna los campos presentes en la tabla van_cielo_ro en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposVan_cielo_ro(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "tarjetas.van_cielo_ro");
    }

    /**
    * Buscar registros en la tabla van_cielo_ro
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de van_cielo_ro o la cantdad de registros segun el parametro contar
    */
    static function listarVan_cielo_ro(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "tarjetas.van_cielo_ro", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>