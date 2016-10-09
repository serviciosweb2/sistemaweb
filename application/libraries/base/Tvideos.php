<?php

/**
* Class Tvideos
*
*Class  Tvideos maneja todos los aspectos de videos
*
* @package  SistemaIGA
* @subpackage Videos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tvideos extends class_general{

    /**
    * id de videos
    * @var id int
    * @access protected
    */
    protected $id;

    /**
    * duracion de videos
    * @var duracion int
    * @access public
    */
    public $duracion;

    /**
    * fecha_publicacion de videos
    * @var fecha_publicacion datetime
    * @access public
    */
    public $fecha_publicacion;

    /**
    * fecha_creacion de videos
    * @var fecha_creacion datetime
    * @access public
    */
    public $fecha_creacion;

    /**
    * id_usuario de videos
    * @var id_usuario int
    * @access public
    */
    public $id_usuario;

    /**
    * estado de videos
    * @var estado enum
    * @access public
    */
    public $estado;

    /**
    * titulo de videos
    * @var titulo varchar
    * @access public
    */
    public $titulo;


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
    protected $nombreTabla = 'material_didactico.videos';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase videos
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
                $this->duracion = $arrConstructor[0]['duracion'];
                $this->fecha_publicacion = $arrConstructor[0]['fecha_publicacion'];
                $this->fecha_creacion = $arrConstructor[0]['fecha_creacion'];
                $this->id_usuario = $arrConstructor[0]['id_usuario'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->titulo = $arrConstructor[0]['titulo'];
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
        $arrTemp['duracion'] = $this->duracion;
        $arrTemp['fecha_publicacion'] = $this->fecha_publicacion;
        $arrTemp['fecha_creacion'] = $this->fecha_creacion;
        $arrTemp['id_usuario'] = $this->id_usuario;
        $arrTemp['estado'] = $this->estado == '' ? 'habilitada' : $this->estado;
        $arrTemp['titulo'] = $this->titulo;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase videos o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarVideos(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto videos
     *
     * @return integer
     */
    public function getCodigoVideos(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de videos según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de videos y los valores son los valores a actualizar
     */
    public function setVideos(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["duracion"]))
            $retorno = "duracion";
        else if (!isset($arrCamposValores["fecha_publicacion"]))
            $retorno = "fecha_publicacion";
        else if (!isset($arrCamposValores["fecha_creacion"]))
            $retorno = "fecha_creacion";
        else if (!isset($arrCamposValores["id_usuario"]))
            $retorno = "id_usuario";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        else if (!isset($arrCamposValores["titulo"]))
            $retorno = "titulo";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setVideos");
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
    * retorna los campos presentes en la tabla videos en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposVideos(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "material_didactico.videos");
    }

    /**
    * Buscar registros en la tabla videos
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de videos o la cantdad de registros segun el parametro contar
    */
    static function listarVideos(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "material_didactico.videos", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>