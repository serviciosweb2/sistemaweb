<?php

/**
* Class Tseguimiento_ginfes
*
*Class  Tseguimiento_ginfes maneja todos los aspectos de seguimiento_ginfes
*
* @package  SistemaIGA
* @subpackage Seguimiento_ginfes
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tseguimiento_ginfes extends class_general{

    /**
    * id de seguimiento_ginfes
    * @var id int
    * @access protected
    */
    protected $id;

    /**
    * cod_factura de seguimiento_ginfes
    * @var cod_factura int
    * @access public
    */
    public $cod_factura;

    /**
    * cod_filial de seguimiento_ginfes
    * @var cod_filial int
    * @access public
    */
    public $cod_filial;

    /**
    * estado de seguimiento_ginfes
    * @var estado enum
    * @access public
    */
    public $estado;

    /**
    * mensaje de seguimiento_ginfes
    * @var mensaje text (requerido)
    * @access public
    */
    public $mensaje;

    /**
    * numero_lote de seguimiento_ginfes
    * @var numero_lote varchar (requerido)
    * @access public
    */
    public $numero_lote;

    /**
    * protocolo de seguimiento_ginfes
    * @var protocolo varchar (requerido)
    * @access public
    */
    public $protocolo;

    /**
    * fecha_envio de seguimiento_ginfes
    * @var fecha_envio datetime (requerido)
    * @access public
    */
    public $fecha_envio;

    /**
    * codigo_verificacion de seguimiento_ginfes
    * @var codigo_verificacion varchar (requerido)
    * @access public
    */
    public $codigo_verificacion;

    /**
    * numero_nfse de seguimiento_ginfes
    * @var numero_nfse varchar (requerido)
    * @access public
    */
    public $numero_nfse;


    /**
    * primaryKey de la tabla
    * @var primaryKey var
    * @access protected
    */
    protected $primaryKey = "id";
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
    protected $nombreTabla = 'general.seguimiento_ginfes';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase seguimiento_ginfes
     *
     * @param CI_DB_mysqli_driver $connection
     * @param integer $codigo (opcional) el codigo del objeto a crear
     */
    function __construct(CI_DB_mysqli_driver $conexion, $id = null){
        $this->oConnection = $conexion;
        if ($id != null && $id != -1){
            $arrConstructor = $this->_constructor($id);
            if (count($arrConstructor) > 0){
                $this->id = $arrConstructor[0]['id'];
                $this->cod_factura = $arrConstructor[0]['cod_factura'];
                $this->cod_filial = $arrConstructor[0]['cod_filial'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->mensaje = $arrConstructor[0]['mensaje'];
                $this->numero_lote = $arrConstructor[0]['numero_lote'];
                $this->protocolo = $arrConstructor[0]['protocolo'];
                $this->fecha_envio = $arrConstructor[0]['fecha_envio'];
                $this->codigo_verificacion = $arrConstructor[0]['codigo_verificacion'];
                $this->numero_nfse = $arrConstructor[0]['numero_nfse'];
            } else {
                $this->id = -1;
            }
        } else {
            $this->id = -1;
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
        $arrTemp['cod_factura'] = $this->cod_factura;
        $arrTemp['cod_filial'] = $this->cod_filial;
        $arrTemp['estado'] = $this->estado;
        $arrTemp['mensaje'] = $this->mensaje == '' ? null : $this->mensaje;
        $arrTemp['numero_lote'] = $this->numero_lote == '' ? null : $this->numero_lote;
        $arrTemp['protocolo'] = $this->protocolo == '' ? null : $this->protocolo;
        $arrTemp['fecha_envio'] = $this->fecha_envio == '' ? null : $this->fecha_envio;
        $arrTemp['codigo_verificacion'] = $this->codigo_verificacion == '' ? null : $this->codigo_verificacion;
        $arrTemp['numero_nfse'] = $this->numero_nfse == '' ? null : $this->numero_nfse;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase seguimiento_ginfes o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarSeguimiento_ginfes(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto seguimiento_ginfes
     *
     * @return integer
     */
    public function getCodigoSeguimiento_ginfes(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de seguimiento_ginfes según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de seguimiento_ginfes y los valores son los valores a actualizar
     */
    public function setSeguimiento_ginfes(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_factura"]))
            $retorno = "cod_factura";
        else if (!isset($arrCamposValores["cod_filial"]))
            $retorno = "cod_filial";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setSeguimiento_ginfes");
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
    * retorna los campos presentes en la tabla seguimiento_ginfes en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposSeguimiento_ginfes(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.seguimiento_ginfes");
    }

    /**
    * Buscar registros en la tabla seguimiento_ginfes
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de seguimiento_ginfes o la cantdad de registros segun el parametro contar
    */
    static function listarSeguimiento_ginfes(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.seguimiento_ginfes", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>