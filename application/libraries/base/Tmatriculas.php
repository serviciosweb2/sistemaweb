<?php

/**
* Class Tmatriculas
*
*Class  Tmatriculas maneja todos los aspectos de matriculas
*
* @package  SistemaIGA
* @subpackage Matriculas
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tmatriculas extends class_general{

    /**
    * codigo de matriculas
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_alumno de matriculas
    * @var cod_alumno int
    * @access public
    */
    public $cod_alumno;

    /**
    * fecha_emision de matriculas
    * @var fecha_emision datetime
    * @access public
    */
    public $fecha_emision;

    /**
    * observaciones de matriculas
    * @var observaciones varchar (requerido)
    * @access public
    */
    public $observaciones;

    /**
    * estado de matriculas
    * @var estado enum
    * @access public
    */
    public $estado;

    /**
    * cod_plan_pago de matriculas
    * @var cod_plan_pago int
    * @access public
    */
    public $cod_plan_pago;

    /**
    * cod_plan_academico de matriculas
    * @var cod_plan_academico int
    * @access public
    */
    public $cod_plan_academico;

    /**
    * usuario_creador de matriculas
    * @var usuario_creador int
    * @access public
    */
    public $usuario_creador;


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
    protected $nombreTabla = 'matriculas';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase matriculas
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
                $this->fecha_emision = $arrConstructor[0]['fecha_emision'];
                $this->observaciones = $arrConstructor[0]['observaciones'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->cod_plan_pago = $arrConstructor[0]['cod_plan_pago'];
                $this->cod_plan_academico = $arrConstructor[0]['cod_plan_academico'];
                $this->usuario_creador = $arrConstructor[0]['usuario_creador'];
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
        $arrTemp['fecha_emision'] = $this->fecha_emision;
        $arrTemp['observaciones'] = $this->observaciones == '' ? null : $this->observaciones;
        $arrTemp['estado'] = $this->estado == '' ? 'habilitada' : $this->estado;
        $arrTemp['cod_plan_pago'] = $this->cod_plan_pago;
        $arrTemp['cod_plan_academico'] = $this->cod_plan_academico;
        $arrTemp['usuario_creador'] = $this->usuario_creador;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase matriculas o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarMatriculas(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto matriculas
     *
     * @return integer
     */
    public function getCodigoMatriculas(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de matriculas según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de matriculas y los valores son los valores a actualizar
     */
    public function setMatriculas(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_alumno"]))
            $retorno = "cod_alumno";
        else if (!isset($arrCamposValores["fecha_emision"]))
            $retorno = "fecha_emision";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        else if (!isset($arrCamposValores["cod_plan_pago"]))
            $retorno = "cod_plan_pago";
        else if (!isset($arrCamposValores["cod_plan_academico"]))
            $retorno = "cod_plan_academico";
        else if (!isset($arrCamposValores["usuario_creador"]))
            $retorno = "usuario_creador";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setMatriculas");
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
    * retorna los campos presentes en la tabla matriculas en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposMatriculas(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "matriculas");
    }

    /**
    * Buscar registros en la tabla matriculas
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de matriculas o la cantdad de registros segun el parametro contar
    */
    static function listarMatriculas(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "matriculas", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>