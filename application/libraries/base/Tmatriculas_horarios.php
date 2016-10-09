<?php

/**
* Class Tmatriculas_horarios
*
*Class  Tmatriculas_horarios maneja todos los aspectos de matriculas_horarios
*
* @package  SistemaIGA
* @subpackage Matriculas_horarios
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tmatriculas_horarios extends class_general{

    /**
    * codigo de matriculas_horarios
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_estado_academico de matriculas_horarios
    * @var cod_estado_academico int
    * @access public
    */
    public $cod_estado_academico;

    /**
    * cod_horario de matriculas_horarios
    * @var cod_horario int
    * @access public
    */
    public $cod_horario;

    /**
    * baja de matriculas_horarios
    * @var baja smallint
    * @access public
    */
    public $baja;

    /**
    * fecha_hora de matriculas_horarios
    * @var fecha_hora datetime
    * @access public
    */
    public $fecha_hora;

    /**
    * usuario de matriculas_horarios
    * @var usuario int
    * @access public
    */
    public $usuario;

    /**
    * estado de matriculas_horarios
    * @var estado enum (requerido)
    * @access public
    */
    public $estado;

    /**
    * motivo_baja de matriculas_horarios
    * @var motivo_baja enum (requerido)
    * @access public
    */
    public $motivo_baja;


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
    protected $nombreTabla = 'matriculas_horarios';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase matriculas_horarios
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
                $this->cod_estado_academico = $arrConstructor[0]['cod_estado_academico'];
                $this->cod_horario = $arrConstructor[0]['cod_horario'];
                $this->baja = $arrConstructor[0]['baja'];
                $this->fecha_hora = $arrConstructor[0]['fecha_hora'];
                $this->usuario = $arrConstructor[0]['usuario'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->motivo_baja = $arrConstructor[0]['motivo_baja'];
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
        $arrTemp['cod_estado_academico'] = $this->cod_estado_academico;
        $arrTemp['cod_horario'] = $this->cod_horario;
        $arrTemp['baja'] = $this->baja == '' ? '0' : $this->baja;
        $arrTemp['fecha_hora'] = $this->fecha_hora;
        $arrTemp['usuario'] = $this->usuario;
        $arrTemp['estado'] = $this->estado == '' ? null : $this->estado;
        $arrTemp['motivo_baja'] = $this->motivo_baja == '' ? null : $this->motivo_baja;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase matriculas_horarios o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarMatriculas_horarios(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto matriculas_horarios
     *
     * @return integer
     */
    public function getCodigoMatriculas_horarios(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de matriculas_horarios según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de matriculas_horarios y los valores son los valores a actualizar
     */
    public function setMatriculas_horarios(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_estado_academico"]))
            $retorno = "cod_estado_academico";
        else if (!isset($arrCamposValores["cod_horario"]))
            $retorno = "cod_horario";
        else if (!isset($arrCamposValores["baja"]))
            $retorno = "baja";
        else if (!isset($arrCamposValores["fecha_hora"]))
            $retorno = "fecha_hora";
        else if (!isset($arrCamposValores["usuario"]))
            $retorno = "usuario";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setMatriculas_horarios");
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
    * retorna los campos presentes en la tabla matriculas_horarios en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposMatriculas_horarios(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "matriculas_horarios");
    }

    /**
    * Buscar registros en la tabla matriculas_horarios
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de matriculas_horarios o la cantdad de registros segun el parametro contar
    */
    static function listarMatriculas_horarios(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "matriculas_horarios", $condiciones, $limite, $orden, $grupo, $contar);
    }
}