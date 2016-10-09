<?php

/**
* Class Talerta_alumno_configuracion
*
*Class  Talerta_alumno_configuracion maneja todos los aspectos de alerta_alumno_configuracion
*
* @package  SistemaIGA
* @subpackage Alerta_alumno_configuracion
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Talerta_alumno_configuracion extends class_general{

    /**
    * codigo de alerta_alumno_configuracion
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_alerta de alerta_alumno_configuracion
    * @var cod_alerta int
    * @access public
    */
    public $cod_alerta;

    /**
    * cod_alumno de alerta_alumno_configuracion
    * @var cod_alumno int
    * @access public
    */
    public $cod_alumno;

    /**
    * key de alerta_alumno_configuracion
    * @var key enum
    * @access public
    */
    public $key;

    /**
    * valor de alerta_alumno_configuracion
    * @var valor varchar
    * @access public
    */
    public $valor;


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
    protected $nombreTabla = 'alerta_alumno_configuracion';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase alerta_alumno_configuracion
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
                $this->cod_alerta = $arrConstructor[0]['cod_alerta'];
                $this->cod_alumno = $arrConstructor[0]['cod_alumno'];
                $this->key = $arrConstructor[0]['key'];
                $this->valor = $arrConstructor[0]['valor'];
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
        $arrTemp['cod_alerta'] = $this->cod_alerta;
        $arrTemp['cod_alumno'] = $this->cod_alumno;
        $arrTemp['key'] = $this->key;
        $arrTemp['valor'] = $this->valor;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase alerta_alumno_configuracion o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarAlerta_alumno_configuracion(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto alerta_alumno_configuracion
     *
     * @return integer
     */
    public function getCodigoAlerta_alumno_configuracion(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de alerta_alumno_configuracion según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de alerta_alumno_configuracion y los valores son los valores a actualizar
     */
    public function setAlerta_alumno_configuracion(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_alerta"]))
            $retorno = "cod_alerta";
        else if (!isset($arrCamposValores["cod_alumno"]))
            $retorno = "cod_alumno";
        else if (!isset($arrCamposValores["key"]))
            $retorno = "key";
        else if (!isset($arrCamposValores["valor"]))
            $retorno = "valor";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setAlerta_alumno_configuracion");
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
    * retorna los campos presentes en la tabla alerta_alumno_configuracion en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposAlerta_alumno_configuracion(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "alerta_alumno_configuracion");
    }

    /**
    * Buscar registros en la tabla alerta_alumno_configuracion
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de alerta_alumno_configuracion o la cantdad de registros segun el parametro contar
    */
    static function listarAlerta_alumno_configuracion(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "alerta_alumno_configuracion", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>