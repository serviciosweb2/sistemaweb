<?php

/**
* Class Ttareas_usuario
*
*Class  Ttareas_usuario maneja todos los aspectos de tareas_usuario
*
* @package  SistemaIGA
* @subpackage Tareas_usuario
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Ttareas_usuario extends class_general{

    /**
    * codigo de tareas_usuario
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * nombre de tareas_usuario
    * @var nombre varchar
    * @access public
    */
    public $nombre;

    /**
    * estado de tareas_usuario
    * @var estado enum
    * @access public
    */
    public $estado;

    /**
    * cod_usuario de tareas_usuario
    * @var cod_usuario int
    * @access public
    */
    public $cod_usuario;

    /**
    * fecha_hora de tareas_usuario
    * @var fecha_hora datetime
    * @access public
    */
    public $fecha_hora;


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
    protected $nombreTabla = 'tareas_usuario';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase tareas_usuario
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
                $this->nombre = $arrConstructor[0]['nombre'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->cod_usuario = $arrConstructor[0]['cod_usuario'];
                $this->fecha_hora = $arrConstructor[0]['fecha_hora'];
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
        $arrTemp['nombre'] = $this->nombre;
        $arrTemp['estado'] = $this->estado == '' ? 'noconcretadas' : $this->estado;
        $arrTemp['cod_usuario'] = $this->cod_usuario;
        $arrTemp['fecha_hora'] = $this->fecha_hora;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase tareas_usuario o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarTareas_usuario(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto tareas_usuario
     *
     * @return integer
     */
    public function getCodigoTareas_usuario(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de tareas_usuario según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de tareas_usuario y los valores son los valores a actualizar
     */
    public function setTareas_usuario(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["nombre"]))
            $retorno = "nombre";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        else if (!isset($arrCamposValores["cod_usuario"]))
            $retorno = "cod_usuario";
        else if (!isset($arrCamposValores["fecha_hora"]))
            $retorno = "fecha_hora";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setTareas_usuario");
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
    * retorna los campos presentes en la tabla tareas_usuario en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposTareas_usuario(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "tareas_usuario");
    }

    /**
    * Buscar registros en la tabla tareas_usuario
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de tareas_usuario o la cantdad de registros segun el parametro contar
    */
    static function listarTareas_usuario(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "tareas_usuario", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>