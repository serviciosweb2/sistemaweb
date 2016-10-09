<?php

/**
* Class Tclases
*
*Class  Tclases maneja todos los aspectos de clases
*
* @package  SistemaIGA
* @subpackage Clases
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tclases extends class_general{

    /**
    * id de clases
    * @var id int
    * @access protected
    */
    protected $id;

    /**
    * id_filial de clases
    * @var id_filial int
    * @access public
    */
    public $id_filial;

    /**
    * id_plan_academico de clases
    * @var id_plan_academico int
    * @access public
    */
    public $id_plan_academico;

    /**
    * id_materia de clases
    * @var id_materia int
    * @access public
    */
    public $id_materia;

    /**
    * modalidad de clases
    * @var modalidad enum
    * @access public
    */
    public $modalidad;

    /**
    * nombre de clases
    * @var nombre varchar
    * @access public
    */
    public $nombre;

    /**
    * nro_clase de clases
    * @var nro_clase int
    * @access public
    */
    public $nro_clase;

    /**
    * estado de clases
    * @var estado enum
    * @access public
    */
    public $estado;

    /**
    * tipo_clase de clases
    * @var tipo_clase enum
    * @access public
    */
    public $tipo_clase;


    /**
    * primaryKey de la tabla
    * @var primaryKey var
    * @access protected
    */
    protected $primaryKey = "id";
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
    protected $nombreTabla = 'general.clases';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase clases
     *
     * @param CI_DB_mysqli_driver $connection
     * @param integer $codigo (opcional) el codigo del objeto a crear
     */
    function __construct(CI_DB_mysqli_driver $conexion, $id = null){
        $this->oConnection = $conexion;
        if ($id != null && $id != -1){
            $arrConstructor = $this->_constructor($id);
            if (count($arrConstructor) > 0){
                $this->id = $arrConstructor[0]['id'];
                $this->id_filial = $arrConstructor[0]['id_filial'];
                $this->id_plan_academico = $arrConstructor[0]['id_plan_academico'];
                $this->id_materia = $arrConstructor[0]['id_materia'];
                $this->modalidad = $arrConstructor[0]['modalidad'];
                $this->nombre = $arrConstructor[0]['nombre'];
                $this->nro_clase = $arrConstructor[0]['nro_clase'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->tipo_clase = $arrConstructor[0]['tipo_clase'];
            } else {
                $this->id = -1;
            }
        } else {
            $this->id = -1;
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
        $arrTemp['id_filial'] = $this->id_filial;
        $arrTemp['id_plan_academico'] = $this->id_plan_academico;
        $arrTemp['id_materia'] = $this->id_materia;
        $arrTemp['modalidad'] = $this->modalidad;
        $arrTemp['nombre'] = $this->nombre;
        $arrTemp['nro_clase'] = $this->nro_clase;
        $arrTemp['estado'] = $this->estado == '' ? 'habilitada' : $this->estado;
        $arrTemp['tipo_clase'] = $this->tipo_clase == '' ? 'clase' : $this->tipo_clase;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase clases o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarClases(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto clases
     *
     * @return integer
     */
    public function getCodigoClases(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de clases según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de clases y los valores son los valores a actualizar
     */
    public function setClases(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["id_filial"]))
            $retorno = "id_filial";
        else if (!isset($arrCamposValores["id_plan_academico"]))
            $retorno = "id_plan_academico";
        else if (!isset($arrCamposValores["id_materia"]))
            $retorno = "id_materia";
        else if (!isset($arrCamposValores["modalidad"]))
            $retorno = "modalidad";
        else if (!isset($arrCamposValores["nombre"]))
            $retorno = "nombre";
        else if (!isset($arrCamposValores["nro_clase"]))
            $retorno = "nro_clase";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        else if (!isset($arrCamposValores["tipo_clase"]))
            $retorno = "tipo_clase";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setClases");
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
    * retorna los campos presentes en la tabla clases en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposClases(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.clases");
    }

    /**
    * Buscar registros en la tabla clases
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de clases o la cantdad de registros segun el parametro contar
    */
    static function listarClases(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.clases", $condiciones, $limite, $orden, $grupo, $contar);
    }
}