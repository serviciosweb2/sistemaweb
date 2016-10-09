<?php

/**
 * Class Ttalonarios
 *
 * Class  Ttalonarios maneja todos los aspectos de talonarios
 *
 * @package  SistemaIGA
 * @subpackage Talonarios
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Ttalonarios extends class_general {

    /**
     * fechahora de talonarios
     * @var fechahora datetime (requerido)
     * @access public
     */
    public $fechahora;

    /**
     * codtipofactura de talonarios
     * @var codtipofactura int
     * @access public
     */
    public $codtipofactura;

    /**
     * ultimonumero de talonarios
     * @var ultimonumero int
     * @access public
     */
    public $ultimonumero;

    /**
     * comentarios de talonarios
     * @var comentarios varchar (requerido)
     * @access public
     */
    public $comentarios;

    /**
     * usuario_creador de talonarios
     * @var usuario_creador int (requerido)
     * @access public
     */
    public $usuario_creador;

    /**
     * activo de talonarios
     * @var activo int
     * @access public
     */
    public $activo;

    /**
     * cod_facturante de talonarios
     * @var cod_facturante int
     * @access public
     */
    public $cod_facturante;

    /**
     * punto_venta de talonarios
     * @var punto_venta int
     * @access public
     */
    public $punto_venta;

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
    protected $nombreTabla = 'general.talonarios';
    
    protected $exists = false;
    
    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase talonarios
     *
     * @param CI_DB_mysqli_driver $connection
     * @param integer $codigo (opcional) el codigo del objeto a crear
     */
    function __construct(CI_DB_mysqli_driver $conexion, $cod_tipo_factura, $cod_facturante, $punto_venta){
        $this->oConnection = $conexion;
        $this->cod_facturante = $cod_facturante;
        $this->punto_venta = $punto_venta;
        $this->codtipofactura = $cod_tipo_factura;
        $arrConstructor = $this->_constructor($cod_tipo_factura, $cod_facturante, $punto_venta);
        if (count($arrConstructor) > 0) {
            $this->exists = true;
            $this->fechahora = $arrConstructor[0]['fechahora'];
            $this->codtipofactura = $arrConstructor[0]['codtipofactura'];
            $this->ultimonumero = $arrConstructor[0]['ultimonumero'];
            $this->comentarios = $arrConstructor[0]['comentarios'];
            $this->usuario_creador = $arrConstructor[0]['usuario_creador'];
            $this->activo = $arrConstructor[0]['activo'];
            $this->cod_facturante = $arrConstructor[0]['cod_facturante'];
            $this->punto_venta = $arrConstructor[0]['punto_venta'];
        } else {
            $this->exists = false;
        }        
    }

     protected function _constructor($cod_tipo_factura, $cod_facturante, $punto_venta){
        $query = $this->oConnection->select('*')
                        ->from($this->nombreTabla)
                        ->where(array(
                            "codtipofactura" => $cod_tipo_factura,
                            "cod_facturante" => $cod_facturante,
                            "punto_venta" => $punto_venta,
                        ))->get();
        $arrConstructor = $query->result_array();   
        return $arrConstructor;
    }

    /* PORTECTED FUNCTIONS */

    /**
     * Devuelve el objeto con todas sus propiedades y valores en formato array
     * 
     * @return array
     */
    protected function _getArrayDeObjeto(){
        $arrTemp = array();
        $arrTemp['codtipofactura'] = $this->codtipofactura;
        $arrTemp['cod_facturante'] = $this->cod_facturante;
        $arrTemp['punto_venta'] = $this->punto_venta;
        $arrTemp['fechahora'] = $this->fechahora == '' ? null : $this->fechahora;
        $arrTemp['ultimonumero'] = $this->ultimonumero == '' ? '0' : $this->ultimonumero;
        $arrTemp['comentarios'] = $this->comentarios == '' ? null : $this->comentarios;
        $arrTemp['usuario_creador'] = $this->usuario_creador == '' ? null : $this->usuario_creador;
        $arrTemp['activo'] = $this->activo == '' ? '0' : $this->activo;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */

    /**
     * Guarda un objeto nuevo de la clase talonarios o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarTalonarios() {        
        if ($this->exists){
            $condiciones = array("codtipofactura" => $this->codtipofactura, 
                "cod_facturante" => $this->cod_facturante, 
                "punto_venta" => $this->punto_venta);
            return $this->oConnection->update($this->nombreTabla, $this->_getArrayDeObjeto(), $condiciones);
        } else {
            $this->exists = $this->oConnection->insert($this->nombreTabla, $this->_getArrayDeObjeto());
            return $this->exists;
        }
    }

    /**
     * Retorna el codigo del objeto talonarios
     *
     * @return integer
     */
    public function getCodigoTalonarios() {
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de talonarios según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de talonarios y los valores son los valores a actualizar
     */
    public function setTalonarios(array $arrCamposValores) {
        $retorno = "";
        if (!isset($arrCamposValores["ultimonumero"]))
            $retorno = "ultimonumero";
        else if (!isset($arrCamposValores["activo"]))
            $retorno = "activo";
        if ($retorno <> "") {
            die("falta el parametro " . $retorno . " en setTalonarios");
        } else {
            foreach ($this as $key => $value) {
                if (isset($arrCamposValores[$key])) {
                    $this->$key = $arrCamposValores[$key];
                }
            }
            return true;
        }
    }

    /* STATIC FUNCTIONS */

    /**
     * retorna los campos presentes en la tabla talonarios en formato array
     * 
     * @param CI_DB_mysqli_driver $connection   La conexion actual
     * @return array
     */
    static function camposTalonarios(CI_DB_mysqli_driver $conexion) {
        return parent::_campos($conexion, "talonarios");
    }

    /**
     * Buscar registros en la tabla talonarios
     *
     * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
     * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
     * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
     * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
     * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
     * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
     * @return mixed    Retorna la lista de talonarios o la cantdad de registros segun el parametro contar
     */
    static function listarTalonarios(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false) {
        return parent::_listar($conexion, "talonarios", $condiciones, $limite, $orden, $grupo, $contar);
    }

}

?>