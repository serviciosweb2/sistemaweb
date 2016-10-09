<?php

/**
* Class Ttareas_crons
*
*Class  Ttareas_crons maneja todos los aspectos de tareas_crons
*
* @package  SistemaIGA
* @subpackage Tareas_crons
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Ttareas_crons extends class_general{

    /**
    * codigo de tareas_crons
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * nombre de tareas_crons
    * @var nombre enum (requerido)
    * @access public
    */
    public $nombre;

    /**
    * parametros de tareas_crons
    * @var parametros varchar (requerido)
    * @access public
    */
    public $parametros;

    /**
    * fecha_hora de tareas_crons
    * @var fecha_hora datetime (requerido)
    * @access public
    */
    public $fecha_hora;

    /**
    * estado de tareas_crons
    * @var estado enum (requerido)
    * @access public
    */
    public $estado;

    /**
    * completado de tareas_crons
    * @var completado float (requerido)
    * @access public
    */
    public $completado;

    /**
    * cod_filial de tareas_crons
    * @var cod_filial int
    * @access public
    */
    public $cod_filial;


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
    protected $nombreTabla = 'general.tareas_crons';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase tareas_crons
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
                $this->parametros = $arrConstructor[0]['parametros'];
                $this->fecha_hora = $arrConstructor[0]['fecha_hora'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->completado = $arrConstructor[0]['completado'];
                $this->cod_filial = $arrConstructor[0]['cod_filial'];
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
        $arrTemp['nombre'] = $this->nombre == '' ? null : $this->nombre;
        $arrTemp['parametros'] = $this->parametros == '' ? null : $this->parametros;
        $arrTemp['fecha_hora'] = $this->fecha_hora == '' ? null : $this->fecha_hora;
        $arrTemp['estado'] = $this->estado == '' ? null : $this->estado;
        $arrTemp['completado'] = $this->completado == '' ? null : $this->completado;
        $arrTemp['cod_filial'] = $this->cod_filial;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase tareas_crons o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarTareas_crons(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto tareas_crons
     *
     * @return integer
     */
    public function getCodigoTareas_crons(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de tareas_crons según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de tareas_crons y los valores son los valores a actualizar
     */
    public function setTareas_crons(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_filial"]))
            $retorno = "cod_filial";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setTareas_crons");
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
    * retorna los campos presentes en la tabla tareas_crons en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposTareas_crons(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.tareas_crons");
    }

    /**
    * Buscar registros en la tabla tareas_crons
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de tareas_crons o la cantdad de registros segun el parametro contar
    */
    static function listarTareas_crons(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.tareas_crons", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>