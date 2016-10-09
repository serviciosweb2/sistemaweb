<?php

/**
* Class Tmatriculas_horarios_estados_historicos
*
*Class  Tmatriculas_horarios_estados_historicos maneja todos los aspectos de matriculas_horarios_estados_historicos
*
* @package  SistemaIGA
* @subpackage Matriculas_horarios_estados_historicos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tmatriculas_horarios_estados_historicos extends class_general{

    /**
    * codigo de matriculas_horarios_estados_historicos
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_matricula_horario de matriculas_horarios_estados_historicos
    * @var cod_matricula_horario int
    * @access public
    */
    public $cod_matricula_horario;

    /**
    * fecha_hora de matriculas_horarios_estados_historicos
    * @var fecha_hora datetime
    * @access public
    */
    public $fecha_hora;

    /**
    * motivo de matriculas_horarios_estados_historicos
    * @var motivo int (requerido)
    * @access public
    */
    public $motivo;

    /**
    * comentario de matriculas_horarios_estados_historicos
    * @var comentario varchar (requerido)
    * @access public
    */
    public $comentario;

    /**
    * usuario_creador de matriculas_horarios_estados_historicos
    * @var usuario_creador int
    * @access public
    */
    public $usuario_creador;

    /**
    * estado de matriculas_horarios_estados_historicos
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
    protected $nombreTabla = 'matriculas_horarios_estados_historicos';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase matriculas_horarios_estados_historicos
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
                $this->cod_matricula_horario = $arrConstructor[0]['cod_matricula_horario'];
                $this->fecha_hora = $arrConstructor[0]['fecha_hora'];
                $this->motivo = $arrConstructor[0]['motivo'];
                $this->comentario = $arrConstructor[0]['comentario'];
                $this->usuario_creador = $arrConstructor[0]['usuario_creador'];
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
        $arrTemp['cod_matricula_horario'] = $this->cod_matricula_horario;
        $arrTemp['fecha_hora'] = $this->fecha_hora;
        $arrTemp['motivo'] = $this->motivo == '' ? null : $this->motivo;
        $arrTemp['comentario'] = $this->comentario == '' ? null : $this->comentario;
        $arrTemp['usuario_creador'] = $this->usuario_creador;
        $arrTemp['estado'] = $this->estado;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase matriculas_horarios_estados_historicos o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarMatriculas_horarios_estados_historicos(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto matriculas_horarios_estados_historicos
     *
     * @return integer
     */
    public function getCodigoMatriculas_horarios_estados_historicos(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de matriculas_horarios_estados_historicos según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de matriculas_horarios_estados_historicos y los valores son los valores a actualizar
     */
    public function setMatriculas_horarios_estados_historicos(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_matricula_horario"]))
            $retorno = "cod_matricula_horario";
        else if (!isset($arrCamposValores["fecha_hora"]))
            $retorno = "fecha_hora";
        else if (!isset($arrCamposValores["usuario_creador"]))
            $retorno = "usuario_creador";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setMatriculas_horarios_estados_historicos");
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
    * retorna los campos presentes en la tabla matriculas_horarios_estados_historicos en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposMatriculas_horarios_estados_historicos(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "matriculas_horarios_estados_historicos");
    }

    /**
    * Buscar registros en la tabla matriculas_horarios_estados_historicos
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de matriculas_horarios_estados_historicos o la cantdad de registros segun el parametro contar
    */
    static function listarMatriculas_horarios_estados_historicos(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "matriculas_horarios_estados_historicos", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>