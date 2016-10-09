<?php

/**
* Class Testadoacademico
*
*Class  Testadoacademico maneja todos los aspectos de estadoacademico
*
* @package  SistemaIGA
* @subpackage Estadoacademico
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Testadoacademico extends class_general{

    /**
    * codigo de estadoacademico
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_matricula_periodo de estadoacademico
    * @var cod_matricula_periodo int
    * @access public
    */
    public $cod_matricula_periodo;

    /**
    * codmateria de estadoacademico
    * @var codmateria int
    * @access public
    */
    public $codmateria;

    /**
    * estado de estadoacademico
    * @var estado enum
    * @access public
    */
    public $estado;

    /**
    * fecha de estadoacademico
    * @var fecha date
    * @access public
    */
    public $fecha;

    /**
    * porcasistencia de estadoacademico
    * @var porcasistencia double (requerido)
    * @access public
    */
    public $porcasistencia;


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
    protected $nombreTabla = 'estadoacademico';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase estadoacademico
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
                $this->cod_matricula_periodo = $arrConstructor[0]['cod_matricula_periodo'];
                $this->codmateria = $arrConstructor[0]['codmateria'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->fecha = $arrConstructor[0]['fecha'];
                $this->porcasistencia = $arrConstructor[0]['porcasistencia'];
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
        $arrTemp['cod_matricula_periodo'] = $this->cod_matricula_periodo;
        $arrTemp['codmateria'] = $this->codmateria;
        $arrTemp['estado'] = $this->estado == '' ? 'cursando' : $this->estado;
        $arrTemp['fecha'] = $this->fecha;
        $arrTemp['porcasistencia'] = $this->porcasistencia == '' ? null : $this->porcasistencia;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase estadoacademico o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarEstadoacademico(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto estadoacademico
     *
     * @return integer
     */
    public function getCodigoEstadoacademico(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de estadoacademico según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de estadoacademico y los valores son los valores a actualizar
     */
    public function setEstadoacademico(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_matricula_periodo"]))
            $retorno = "cod_matricula_periodo";
        else if (!isset($arrCamposValores["codmateria"]))
            $retorno = "codmateria";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        else if (!isset($arrCamposValores["fecha"]))
            $retorno = "fecha";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setEstadoacademico");
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
    * retorna los campos presentes en la tabla estadoacademico en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposEstadoacademico(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "estadoacademico");
    }

    /**
    * Buscar registros en la tabla estadoacademico
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de estadoacademico o la cantdad de registros segun el parametro contar
    */
    static function listarEstadoacademico(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "estadoacademico", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>