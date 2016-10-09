<?php

/**
* Class Tcursos_subcategorias
*
*Class  Tcursos_subcategorias maneja todos los aspectos de cursos_subcategorias
*
* @package  SistemaIGA
* @subpackage Cursos_subcategorias
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tcursos_subcategorias extends class_general{

    /**
    * id de cursos_subcategorias
    * @var id int
    * @access protected
    */
    protected $id;

    /**
    * nombre de cursos_subcategorias
    * @var nombre varchar
    * @access public
    */
    public $nombre;

    /**
    * estado de cursos_subcategorias
    * @var estado enum
    * @access public
    */
    public $estado;

    /**
    * id_categoria de cursos_subcategorias
    * @var id_categoria int
    * @access public
    */
    public $id_categoria;

    /**
    * nombre_pt de cursos_subcategorias
    * @var nombre_pt varchar (requerido)
    * @access public
    */
    public $nombre_pt;

    /**
    * nombre_in de cursos_subcategorias
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
    protected $nombreTabla = 'general.cursos_subcategorias';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase cursos_subcategorias
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
                $this->id_categoria = $arrConstructor[0]['id_categoria'];
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
        $arrTemp['estado'] = $this->estado == '' ? 'habilitada' : $this->estado;
        $arrTemp['id_categoria'] = $this->id_categoria;
        $arrTemp['nombre_pt'] = $this->nombre_pt == '' ? null : $this->nombre_pt;
        $arrTemp['nombre_in'] = $this->nombre_in == '' ? null : $this->nombre_in;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase cursos_subcategorias o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarCursos_subcategorias(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto cursos_subcategorias
     *
     * @return integer
     */
    public function getCodigoCursos_subcategorias(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de cursos_subcategorias según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de cursos_subcategorias y los valores son los valores a actualizar
     */
    public function setCursos_subcategorias(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["nombre"]))
            $retorno = "nombre";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        else if (!isset($arrCamposValores["id_categoria"]))
            $retorno = "id_categoria";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setCursos_subcategorias");
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
    * retorna los campos presentes en la tabla cursos_subcategorias en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposCursos_subcategorias(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.cursos_subcategorias");
    }

    /**
    * Buscar registros en la tabla cursos_subcategorias
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de cursos_subcategorias o la cantdad de registros segun el parametro contar
    */
    static function listarCursos_subcategorias(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.cursos_subcategorias", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>