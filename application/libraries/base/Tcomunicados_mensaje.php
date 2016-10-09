<?php

/**
* Class Tcomunicados_mensaje
*
*Class  Tcomunicados_mensaje maneja todos los aspectos de comunicados_mensaje
*
* @package  SistemaIGA
* @subpackage Comunicados_mensaje
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tcomunicados_mensaje extends class_general{

    /**
    * codigo de comunicados_mensaje
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * mensaje de comunicados_mensaje
    * @var mensaje longtext
    * @access public
    */
    public $mensaje;

    /**
    * usuario_creador de comunicados_mensaje
    * @var usuario_creador int
    * @access public
    */
    public $usuario_creador;

    /**
    * fecha_hora de comunicados_mensaje
    * @var fecha_hora datetime
    * @access public
    */
    public $fecha_hora;

    /**
    * asunto de comunicados_mensaje
    * @var asunto varchar
    * @access public
    */
    public $asunto;


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
    protected $nombreTabla = 'comunicados_mensaje';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase comunicados_mensaje
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
                $this->mensaje = $arrConstructor[0]['mensaje'];
                $this->usuario_creador = $arrConstructor[0]['usuario_creador'];
                $this->fecha_hora = $arrConstructor[0]['fecha_hora'];
                $this->asunto = $arrConstructor[0]['asunto'];
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
        $arrTemp['mensaje'] = $this->mensaje;
        $arrTemp['usuario_creador'] = $this->usuario_creador;
        $arrTemp['fecha_hora'] = $this->fecha_hora;
        $arrTemp['asunto'] = $this->asunto;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase comunicados_mensaje o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarComunicados_mensaje(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto comunicados_mensaje
     *
     * @return integer
     */
    public function getCodigoComunicados_mensaje(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de comunicados_mensaje según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de comunicados_mensaje y los valores son los valores a actualizar
     */
    public function setComunicados_mensaje(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["mensaje"]))
            $retorno = "mensaje";
        else if (!isset($arrCamposValores["usuario_creador"]))
            $retorno = "usuario_creador";
        else if (!isset($arrCamposValores["fecha_hora"]))
            $retorno = "fecha_hora";
        else if (!isset($arrCamposValores["asunto"]))
            $retorno = "asunto";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setComunicados_mensaje");
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
    * retorna los campos presentes en la tabla comunicados_mensaje en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposComunicados_mensaje(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "comunicados_mensaje");
    }

    /**
    * Buscar registros en la tabla comunicados_mensaje
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de comunicados_mensaje o la cantdad de registros segun el parametro contar
    */
    static function listarComunicados_mensaje(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "comunicados_mensaje", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>