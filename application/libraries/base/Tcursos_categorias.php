<?php

/**
* Class Tcursos_categorias
*
*Class  Tcursos_categorias maneja todos los aspectos de cursos_categorias
*
* @package  SistemaIGA
* @subpackage Cursos_categorias
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tcursos_categorias extends class_general{

    /**
    * id de cursos_categorias
    * @var id int
    * @access protected
    */
    protected $id;

    /**
    * nombre de cursos_categorias
    * @var nombre varchar
    * @access public
    */
    public $nombre;

    /**
    * estado de cursos_categorias
    * @var estado enum
    * @access public
    */
    public $estado;

    /**
    * nombre_pt de cursos_categorias
    * @var nombre_pt varchar (requerido)
    * @access public
    */
    public $nombre_pt;

    /**
    * nombre_in de cursos_categorias
    * @var nombre_in varchar (requerido)
    * @access public
    */
    public $nombre_in;


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
    protected $nombreTabla = 'general.cursos_categorias';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase cursos_categorias
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
                $this->nombre = $arrConstructor[0]['nombre'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->nombre_pt = $arrConstructor[0]['nombre_pt'];
                $this->nombre_in = $arrConstructor[0]['nombre_in'];
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
        $arrTemp['nombre'] = $this->nombre;
        $arrTemp['estado'] = $this->estado == '' ? 'habilitado' : $this->estado;
        $arrTemp['nombre_pt'] = $this->nombre_pt == '' ? null : $this->nombre_pt;
        $arrTemp['nombre_in'] = $this->nombre_in == '' ? null : $this->nombre_in;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase cursos_categorias o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarCursos_categorias(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto cursos_categorias
     *
     * @return integer
     */
    public function getCodigoCursos_categorias(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de cursos_categorias según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de cursos_categorias y los valores son los valores a actualizar
     */
    public function setCursos_categorias(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["nombre"]))
            $retorno = "nombre";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setCursos_categorias");
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
    * retorna los campos presentes en la tabla cursos_categorias en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposCursos_categorias(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.cursos_categorias");
    }

    /**
    * Buscar registros en la tabla cursos_categorias
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de cursos_categorias o la cantdad de registros segun el parametro contar
    */
    static function listarCursos_categorias(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.cursos_categorias", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>