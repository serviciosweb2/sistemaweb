<?php

/**
* Class Tmatriculas_inscripciones
*
*Class  Tmatriculas_inscripciones maneja todos los aspectos de matriculas_inscripciones
*
* @package  SistemaIGA
* @subpackage Matriculas_inscripciones
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tmatriculas_inscripciones extends class_general{

    /**
    * codigo de matriculas_inscripciones
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_estado_academico de matriculas_inscripciones
    * @var cod_estado_academico int
    * @access public
    */
    public $cod_estado_academico;

    /**
    * cod_comision de matriculas_inscripciones
    * @var cod_comision int
    * @access public
    */
    public $cod_comision;

    /**
    * baja de matriculas_inscripciones
    * @var baja int
    * @access public
    */
    public $baja;

    /**
    * fecha_hora de matriculas_inscripciones
    * @var fecha_hora datetime (requerido)
    * @access public
    */
    public $fecha_hora;

    /**
    * cod_usuario_creador de matriculas_inscripciones
    * @var cod_usuario_creador int (requerido)
    * @access public
    */
    public $cod_usuario_creador;

    /**
    * motivo_baja de matriculas_inscripciones
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
    protected $nombreTabla = 'matriculas_inscripciones';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase matriculas_inscripciones
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
                $this->cod_comision = $arrConstructor[0]['cod_comision'];
                $this->baja = $arrConstructor[0]['baja'];
                $this->fecha_hora = $arrConstructor[0]['fecha_hora'];
                $this->cod_usuario_creador = $arrConstructor[0]['cod_usuario_creador'];
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
        $arrTemp['cod_comision'] = $this->cod_comision;
        $arrTemp['baja'] = $this->baja == '' ? '0' : $this->baja;
        $arrTemp['fecha_hora'] = $this->fecha_hora == '' ? null : $this->fecha_hora;
        $arrTemp['cod_usuario_creador'] = $this->cod_usuario_creador == '' ? null : $this->cod_usuario_creador;
        $arrTemp['motivo_baja'] = $this->motivo_baja == '' ? null : $this->motivo_baja;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase matriculas_inscripciones o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarMatriculas_inscripciones(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto matriculas_inscripciones
     *
     * @return integer
     */
    public function getCodigoMatriculas_inscripciones(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de matriculas_inscripciones según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de matriculas_inscripciones y los valores son los valores a actualizar
     */
    public function setMatriculas_inscripciones(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_estado_academico"]))
            $retorno = "cod_estado_academico";
        else if (!isset($arrCamposValores["cod_comision"]))
            $retorno = "cod_comision";
        else if (!isset($arrCamposValores["baja"]))
            $retorno = "baja";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setMatriculas_inscripciones");
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
    * retorna los campos presentes en la tabla matriculas_inscripciones en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposMatriculas_inscripciones(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "matriculas_inscripciones");
    }

    /**
    * Buscar registros en la tabla matriculas_inscripciones
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de matriculas_inscripciones o la cantdad de registros segun el parametro contar
    */
    static function listarMatriculas_inscripciones(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "matriculas_inscripciones", $condiciones, $limite, $orden, $grupo, $contar);
    }
}