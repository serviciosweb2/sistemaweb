<?php

/**
* Class Tmatriculas_periodos
*
*Class  Tmatriculas_periodos maneja todos los aspectos de matriculas_periodos
*
* @package  SistemaIGA
* @subpackage Matriculas_periodos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tmatriculas_periodos extends class_general{

    /**
    * codigo de matriculas_periodos
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_matricula de matriculas_periodos
    * @var cod_matricula int
    * @access public
    */
    public $cod_matricula;

    /**
    * cod_tipo_periodo de matriculas_periodos
    * @var cod_tipo_periodo int
    * @access public
    */
    public $cod_tipo_periodo;

    /**
    * cod_usuario_creador de matriculas_periodos
    * @var cod_usuario_creador int
    * @access public
    */
    public $cod_usuario_creador;

    /**
    * fecha_emision de matriculas_periodos
    * @var fecha_emision datetime
    * @access public
    */
    public $fecha_emision;

    /**
    * estado de matriculas_periodos
    * @var estado enum
    * @access public
    */
    public $estado;

    /**
    * modalidad de matriculas_periodos
    * @var modalidad enum
    * @access public
    */
    public $modalidad;


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
    protected $nombreTabla = 'matriculas_periodos';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase matriculas_periodos
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
                $this->cod_matricula = $arrConstructor[0]['cod_matricula'];
                $this->cod_tipo_periodo = $arrConstructor[0]['cod_tipo_periodo'];
                $this->cod_usuario_creador = $arrConstructor[0]['cod_usuario_creador'];
                $this->fecha_emision = $arrConstructor[0]['fecha_emision'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->modalidad = $arrConstructor[0]['modalidad'];
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
        $arrTemp['cod_matricula'] = $this->cod_matricula;
        $arrTemp['cod_tipo_periodo'] = $this->cod_tipo_periodo;
        $arrTemp['cod_usuario_creador'] = $this->cod_usuario_creador;
        $arrTemp['fecha_emision'] = $this->fecha_emision;
        $arrTemp['estado'] = $this->estado == '' ? 'habilitada' : $this->estado;
        $arrTemp['modalidad'] = $this->modalidad == '' ? 'normal' : $this->modalidad;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase matriculas_periodos o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarMatriculas_periodos(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto matriculas_periodos
     *
     * @return integer
     */
    public function getCodigoMatriculas_periodos(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de matriculas_periodos según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de matriculas_periodos y los valores son los valores a actualizar
     */
    public function setMatriculas_periodos(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_matricula"]))
            $retorno = "cod_matricula";
        else if (!isset($arrCamposValores["cod_tipo_periodo"]))
            $retorno = "cod_tipo_periodo";
        else if (!isset($arrCamposValores["cod_usuario_creador"]))
            $retorno = "cod_usuario_creador";
        else if (!isset($arrCamposValores["fecha_emision"]))
            $retorno = "fecha_emision";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        else if (!isset($arrCamposValores["modalidad"]))
            $retorno = "modalidad";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setMatriculas_periodos");
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
    * retorna los campos presentes en la tabla matriculas_periodos en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposMatriculas_periodos(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "matriculas_periodos");
    }

    /**
    * Buscar registros en la tabla matriculas_periodos
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de matriculas_periodos o la cantdad de registros segun el parametro contar
    */
    static function listarMatriculas_periodos(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "matriculas_periodos", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>