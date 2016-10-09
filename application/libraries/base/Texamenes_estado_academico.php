<?php

/**
* Class Texamenes_estado_academico
*
*Class  Texamenes_estado_academico maneja todos los aspectos de examenes_estado_academico
*
* @package  SistemaIGA
* @subpackage Examenes_estado_academico
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Texamenes_estado_academico extends class_general{

    /**
    * codigo de examenes_estado_academico
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_examen de examenes_estado_academico
    * @var cod_examen int
    * @access public
    */
    public $cod_examen;

    /**
    * cod_estado_academico de examenes_estado_academico
    * @var cod_estado_academico int
    * @access public
    */
    public $cod_estado_academico;

    /**
    * fechadeinscripcion de examenes_estado_academico
    * @var fechadeinscripcion date
    * @access public
    */
    public $fechadeinscripcion;

    /**
    * inscripcion_web de examenes_estado_academico
    * @var inscripcion_web smallint (requerido)
    * @access public
    */
    public $inscripcion_web;

    /**
    * estado de examenes_estado_academico
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
    protected $nombreTabla = 'examenes_estado_academico';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase examenes_estado_academico
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
                $this->cod_examen = $arrConstructor[0]['cod_examen'];
                $this->cod_estado_academico = $arrConstructor[0]['cod_estado_academico'];
                $this->fechadeinscripcion = $arrConstructor[0]['fechadeinscripcion'];
                $this->inscripcion_web = $arrConstructor[0]['inscripcion_web'];
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
        $arrTemp['cod_examen'] = $this->cod_examen;
        $arrTemp['cod_estado_academico'] = $this->cod_estado_academico;
        $arrTemp['fechadeinscripcion'] = $this->fechadeinscripcion;
        $arrTemp['inscripcion_web'] = $this->inscripcion_web == '' ? null : $this->inscripcion_web;
        $arrTemp['estado'] = $this->estado == '' ? 'pendiente' : $this->estado;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase examenes_estado_academico o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarExamenes_estado_academico(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto examenes_estado_academico
     *
     * @return integer
     */
    public function getCodigoExamenes_estado_academico(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de examenes_estado_academico según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de examenes_estado_academico y los valores son los valores a actualizar
     */
    public function setExamenes_estado_academico(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_examen"]))
            $retorno = "cod_examen";
        else if (!isset($arrCamposValores["cod_estado_academico"]))
            $retorno = "cod_estado_academico";
        else if (!isset($arrCamposValores["fechadeinscripcion"]))
            $retorno = "fechadeinscripcion";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setExamenes_estado_academico");
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
    * retorna los campos presentes en la tabla examenes_estado_academico en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposExamenes_estado_academico(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "examenes_estado_academico");
    }

    /**
    * Buscar registros en la tabla examenes_estado_academico
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de examenes_estado_academico o la cantdad de registros segun el parametro contar
    */
    static function listarExamenes_estado_academico(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "examenes_estado_academico", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>