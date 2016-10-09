<?php

/**
* Class Tfacturas_estado_historicos
*
*Class  Tfacturas_estado_historicos maneja todos los aspectos de facturas_estado_historicos
*
* @package  SistemaIGA
* @subpackage Facturas_estado_historicos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tfacturas_estado_historicos extends class_general{

    /**
    * codigo de facturas_estado_historicos
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_factura de facturas_estado_historicos
    * @var cod_factura int
    * @access public
    */
    public $cod_factura;

    /**
    * estado de facturas_estado_historicos
    * @var estado enum
    * @access public
    */
    public $estado;

    /**
    * fecha_hora de facturas_estado_historicos
    * @var fecha_hora datetime
    * @access public
    */
    public $fecha_hora;

    /**
    * motivo de facturas_estado_historicos
    * @var motivo int (requerido)
    * @access public
    */
    public $motivo;

    /**
    * comentario de facturas_estado_historicos
    * @var comentario varchar (requerido)
    * @access public
    */
    public $comentario;

    /**
    * cod_usuario de facturas_estado_historicos
    * @var cod_usuario int
    * @access public
    */
    public $cod_usuario;


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
    protected $nombreTabla = 'facturas_estado_historicos';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase facturas_estado_historicos
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
                $this->cod_factura = $arrConstructor[0]['cod_factura'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->fecha_hora = $arrConstructor[0]['fecha_hora'];
                $this->motivo = $arrConstructor[0]['motivo'];
                $this->comentario = $arrConstructor[0]['comentario'];
                $this->cod_usuario = $arrConstructor[0]['cod_usuario'];
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
        $arrTemp['cod_factura'] = $this->cod_factura;
        $arrTemp['estado'] = $this->estado;
        $arrTemp['fecha_hora'] = $this->fecha_hora;
        $arrTemp['motivo'] = $this->motivo == '' ? null : $this->motivo;
        $arrTemp['comentario'] = $this->comentario == '' ? null : $this->comentario;
        $arrTemp['cod_usuario'] = $this->cod_usuario;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase facturas_estado_historicos o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarFacturas_estado_historicos(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto facturas_estado_historicos
     *
     * @return integer
     */
    public function getCodigoFacturas_estado_historicos(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de facturas_estado_historicos según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de facturas_estado_historicos y los valores son los valores a actualizar
     */
    public function setFacturas_estado_historicos(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_factura"]))
            $retorno = "cod_factura";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        else if (!isset($arrCamposValores["fecha_hora"]))
            $retorno = "fecha_hora";
        else if (!isset($arrCamposValores["cod_usuario"]))
            $retorno = "cod_usuario";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setFacturas_estado_historicos");
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
    * retorna los campos presentes en la tabla facturas_estado_historicos en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposFacturas_estado_historicos(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "facturas_estado_historicos");
    }

    /**
    * Buscar registros en la tabla facturas_estado_historicos
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de facturas_estado_historicos o la cantdad de registros segun el parametro contar
    */
    static function listarFacturas_estado_historicos(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "facturas_estado_historicos", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>