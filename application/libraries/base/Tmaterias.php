<?php

/**
* Class Tmaterias
*
*Class  Tmaterias maneja todos los aspectos de materias
*
* @package  SistemaIGA
* @subpackage Materias
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tmaterias extends class_general{

    /**
    * codigo de materias
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * nombre_es de materias
    * @var nombre_es varchar
    * @access public
    */
    public $nombre_es;

    /**
    * nombre_pt de materias
    * @var nombre_pt varchar
    * @access public
    */
    public $nombre_pt;

    /**
    * nombre_in de materias
    * @var nombre_in varchar
    * @access public
    */
    public $nombre_in;

    /**
    * descripcion_es de materias
    * @var descripcion_es varchar
    * @access public
    */
    public $descripcion_es;

    /**
    * descripcion_pt de materias
    * @var descripcion_pt varchar
    * @access public
    */
    public $descripcion_pt;

    /**
    * descripcion_in de materias
    * @var descripcion_in varchar
    * @access public
    */
    public $descripcion_in;

    /**
    * cod_tipo_materia de materias
    * @var cod_tipo_materia set
    * @access public
    */
    public $cod_tipo_materia;


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
    protected $nombreTabla = 'general.materias';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase materias
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
                $this->nombre_es = $arrConstructor[0]['nombre_es'];
                $this->nombre_pt = $arrConstructor[0]['nombre_pt'];
                $this->nombre_in = $arrConstructor[0]['nombre_in'];
                $this->descripcion_es = $arrConstructor[0]['descripcion_es'];
                $this->descripcion_pt = $arrConstructor[0]['descripcion_pt'];
                $this->descripcion_in = $arrConstructor[0]['descripcion_in'];
                $this->cod_tipo_materia = $arrConstructor[0]['cod_tipo_materia'];
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
        $arrTemp['nombre_es'] = $this->nombre_es;
        $arrTemp['nombre_pt'] = $this->nombre_pt;
        $arrTemp['nombre_in'] = $this->nombre_in;
        $arrTemp['descripcion_es'] = $this->descripcion_es;
        $arrTemp['descripcion_pt'] = $this->descripcion_pt;
        $arrTemp['descripcion_in'] = $this->descripcion_in;
        $arrTemp['cod_tipo_materia'] = $this->cod_tipo_materia == '' ? 'teorico' : $this->cod_tipo_materia;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase materias o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarMaterias(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto materias
     *
     * @return integer
     */
    public function getCodigoMaterias(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de materias según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de materias y los valores son los valores a actualizar
     */
    public function setMaterias(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["nombre_es"]))
            $retorno = "nombre_es";
        else if (!isset($arrCamposValores["nombre_pt"]))
            $retorno = "nombre_pt";
        else if (!isset($arrCamposValores["nombre_in"]))
            $retorno = "nombre_in";
        else if (!isset($arrCamposValores["descripcion_es"]))
            $retorno = "descripcion_es";
        else if (!isset($arrCamposValores["descripcion_pt"]))
            $retorno = "descripcion_pt";
        else if (!isset($arrCamposValores["descripcion_in"]))
            $retorno = "descripcion_in";
        else if (!isset($arrCamposValores["cod_tipo_materia"]))
            $retorno = "cod_tipo_materia";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setMaterias");
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
    * retorna los campos presentes en la tabla materias en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposMaterias(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.materias");
    }

    /**
    * Buscar registros en la tabla materias
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de materias o la cantdad de registros segun el parametro contar
    */
    static function listarMaterias(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.materias", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>