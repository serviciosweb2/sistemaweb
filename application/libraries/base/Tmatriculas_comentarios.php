<?php

/**
* Class Tmatriculas_comentarios
*
*Class  Tmatriculas_comentarios maneja todos los aspectos de matriculas_comentarios
*
* @package  SistemaIGA
* @subpackage Matriculas_comentarios
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tmatriculas_comentarios extends class_general{

    /**
    * codigo de matriculas_comentarios
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_alumno de matriculas_comentarios
    * @var cod_alumno int
    * @access public
    */
    public $cod_alumno;

    /**
    * cod_plan_academico de matriculas_comentarios
    * @var cod_plan_academico int
    * @access public
    */
    public $cod_plan_academico;

    /**
    * fecha_hora de matriculas_comentarios
    * @var fecha_hora datetime
    * @access public
    */
    public $fecha_hora;

    /**
    * comentario de matriculas_comentarios
    * @var comentario varchar
    * @access public
    */
    public $comentario;

    /**
    * usuario_creador de matriculas_comentarios
    * @var usuario_creador int
    * @access public
    */
    public $usuario_creador;

    /**
    * baja de matriculas_comentarios
    * @var baja int
    * @access public
    */
    public $baja;

    /**
    * cod_matricula de matriculas_comentarios
    * @var cod_matricula int (requerido)
    * @access public
    */
    public $cod_matricula;


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
    protected $nombreTabla = 'matriculas_comentarios';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase matriculas_comentarios
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
                $this->cod_alumno = $arrConstructor[0]['cod_alumno'];
                $this->cod_plan_academico = $arrConstructor[0]['cod_plan_academico'];
                $this->fecha_hora = $arrConstructor[0]['fecha_hora'];
                $this->comentario = $arrConstructor[0]['comentario'];
                $this->usuario_creador = $arrConstructor[0]['usuario_creador'];
                $this->baja = $arrConstructor[0]['baja'];
                $this->cod_matricula = $arrConstructor[0]['cod_matricula'];
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
        $arrTemp['cod_alumno'] = $this->cod_alumno;
        $arrTemp['cod_plan_academico'] = $this->cod_plan_academico;
        $arrTemp['fecha_hora'] = $this->fecha_hora;
        $arrTemp['comentario'] = $this->comentario;
        $arrTemp['usuario_creador'] = $this->usuario_creador;
        $arrTemp['baja'] = $this->baja;
        $arrTemp['cod_matricula'] = $this->cod_matricula == '' ? null : $this->cod_matricula;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase matriculas_comentarios o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarMatriculas_comentarios(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto matriculas_comentarios
     *
     * @return integer
     */
    public function getCodigoMatriculas_comentarios(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de matriculas_comentarios según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de matriculas_comentarios y los valores son los valores a actualizar
     */
    public function setMatriculas_comentarios(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_alumno"]))
            $retorno = "cod_alumno";
        else if (!isset($arrCamposValores["cod_plan_academico"]))
            $retorno = "cod_plan_academico";
        else if (!isset($arrCamposValores["fecha_hora"]))
            $retorno = "fecha_hora";
        else if (!isset($arrCamposValores["comentario"]))
            $retorno = "comentario";
        else if (!isset($arrCamposValores["usuario_creador"]))
            $retorno = "usuario_creador";
        else if (!isset($arrCamposValores["baja"]))
            $retorno = "baja";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setMatriculas_comentarios");
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
    * retorna los campos presentes en la tabla matriculas_comentarios en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposMatriculas_comentarios(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "matriculas_comentarios");
    }

    /**
    * Buscar registros en la tabla matriculas_comentarios
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de matriculas_comentarios o la cantdad de registros segun el parametro contar
    */
    static function listarMatriculas_comentarios(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "matriculas_comentarios", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>