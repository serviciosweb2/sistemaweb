<?php

/**
* Class Tcertificados_requerimientos
*
*Class  Tcertificados_requerimientos maneja todos los aspectos de certificados_requerimientos
*
* @package  SistemaIGA
* @subpackage Certificados_requerimientos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tcertificados_requerimientos extends class_general{

    /**
    * codigo de certificados_requerimientos
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_plan_academico de certificados_requerimientos
    * @var cod_plan_academico int
    * @access public
    */
    public $cod_plan_academico;

    /**
    * cod_certificante de certificados_requerimientos
    * @var cod_certificante int
    * @access public
    */
    public $cod_certificante;

    /**
    * cod_tipo_periodo de certificados_requerimientos
    * @var cod_tipo_periodo int
    * @access public
    */
    public $cod_tipo_periodo;

    /**
    * cod_filial de certificados_requerimientos
    * @var cod_filial int
    * @access public
    */
    public $cod_filial;

    /**
    * key de certificados_requerimientos
    * @var key enum
    * @access public
    */
    public $key;

    /**
    * valor de certificados_requerimientos
    * @var valor varchar (requerido)
    * @access public
    */
    public $valor;

    /**
    * estado de certificados_requerimientos
    * @var estado enum
    * @access public
    */
    public $estado;


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
    protected $nombreTabla = 'general.certificados_requerimientos';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase certificados_requerimientos
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
                $this->cod_plan_academico = $arrConstructor[0]['cod_plan_academico'];
                $this->cod_certificante = $arrConstructor[0]['cod_certificante'];
                $this->cod_tipo_periodo = $arrConstructor[0]['cod_tipo_periodo'];
                $this->cod_filial = $arrConstructor[0]['cod_filial'];
                $this->key = $arrConstructor[0]['key'];
                $this->valor = $arrConstructor[0]['valor'];
                $this->estado = $arrConstructor[0]['estado'];
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
        $arrTemp['cod_plan_academico'] = $this->cod_plan_academico;
        $arrTemp['cod_certificante'] = $this->cod_certificante;
        $arrTemp['cod_tipo_periodo'] = $this->cod_tipo_periodo;
        $arrTemp['cod_filial'] = $this->cod_filial;
        $arrTemp['key'] = $this->key;
        $arrTemp['valor'] = $this->valor == '' ? null : $this->valor;
        $arrTemp['estado'] = $this->estado == '' ? 'habilitado' : $this->estado;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase certificados_requerimientos o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarCertificados_requerimientos(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto certificados_requerimientos
     *
     * @return integer
     */
    public function getCodigoCertificados_requerimientos(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de certificados_requerimientos según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de certificados_requerimientos y los valores son los valores a actualizar
     */
    public function setCertificados_requerimientos(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_plan_academico"]))
            $retorno = "cod_plan_academico";
        else if (!isset($arrCamposValores["cod_certificante"]))
            $retorno = "cod_certificante";
        else if (!isset($arrCamposValores["cod_tipo_periodo"]))
            $retorno = "cod_tipo_periodo";
        else if (!isset($arrCamposValores["cod_filial"]))
            $retorno = "cod_filial";
        else if (!isset($arrCamposValores["key"]))
            $retorno = "key";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setCertificados_requerimientos");
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
    * retorna los campos presentes en la tabla certificados_requerimientos en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposCertificados_requerimientos(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.certificados_requerimientos");
    }

    /**
    * Buscar registros en la tabla certificados_requerimientos
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de certificados_requerimientos o la cantdad de registros segun el parametro contar
    */
    static function listarCertificados_requerimientos(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.certificados_requerimientos", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>