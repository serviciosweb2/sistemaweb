<?php

/**
* Class Tusuarios_filiales_historico
*
*Class  Tusuarios_filiales_historico maneja todos los aspectos de usuarios_filiales_historico
*
* @package  SistemaIGA
* @subpackage Usuarios_filiales_historico
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tusuarios_filiales_historico extends class_general{

    /**
    * id de usuarios_filiales_historico
    * @var id int
    * @access protected
    */
    protected $id;

    /**
    * id_usuario de usuarios_filiales_historico
    * @var id_usuario int
    * @access public
    */
    public $id_usuario;

    /**
    * accion de usuarios_filiales_historico
    * @var accion enum
    * @access public
    */
    public $accion;

    /**
    * id_filial de usuarios_filiales_historico
    * @var id_filial int
    * @access public
    */
    public $id_filial;

    /**
    * id_usuario_iga de usuarios_filiales_historico
    * @var id_usuario_iga int
    * @access public
    */
    public $id_usuario_iga;

    /**
    * nombre_usuario_iga de usuarios_filiales_historico
    * @var nombre_usuario_iga varchar
    * @access public
    */
    public $nombre_usuario_iga;

    /**
    * fecha de usuarios_filiales_historico
    * @var fecha datetime
    * @access public
    */
    public $fecha;


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
    protected $nombreTabla = 'general.usuarios_filiales_historico';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase usuarios_filiales_historico
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
                $this->id_usuario = $arrConstructor[0]['id_usuario'];
                $this->accion = $arrConstructor[0]['accion'];
                $this->id_filial = $arrConstructor[0]['id_filial'];
                $this->id_usuario_iga = $arrConstructor[0]['id_usuario_iga'];
                $this->nombre_usuario_iga = $arrConstructor[0]['nombre_usuario_iga'];
                $this->fecha = $arrConstructor[0]['fecha'];
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
        $arrTemp['id_usuario'] = $this->id_usuario;
        $arrTemp['accion'] = $this->accion;
        $arrTemp['id_filial'] = $this->id_filial;
        $arrTemp['id_usuario_iga'] = $this->id_usuario_iga;
        $arrTemp['nombre_usuario_iga'] = $this->nombre_usuario_iga;
        $arrTemp['fecha'] = $this->fecha;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase usuarios_filiales_historico o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarUsuarios_filiales_historico(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto usuarios_filiales_historico
     *
     * @return integer
     */
    public function getCodigoUsuarios_filiales_historico(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de usuarios_filiales_historico según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de usuarios_filiales_historico y los valores son los valores a actualizar
     */
    public function setUsuarios_filiales_historico(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["id_usuario"]))
            $retorno = "id_usuario";
        else if (!isset($arrCamposValores["accion"]))
            $retorno = "accion";
        else if (!isset($arrCamposValores["id_filial"]))
            $retorno = "id_filial";
        else if (!isset($arrCamposValores["id_usuario_iga"]))
            $retorno = "id_usuario_iga";
        else if (!isset($arrCamposValores["nombre_usuario_iga"]))
            $retorno = "nombre_usuario_iga";
        else if (!isset($arrCamposValores["fecha"]))
            $retorno = "fecha";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setUsuarios_filiales_historico");
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
    * retorna los campos presentes en la tabla usuarios_filiales_historico en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposUsuarios_filiales_historico(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.usuarios_filiales_historico");
    }

    /**
    * Buscar registros en la tabla usuarios_filiales_historico
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de usuarios_filiales_historico o la cantdad de registros segun el parametro contar
    */
    static function listarUsuarios_filiales_historico(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.usuarios_filiales_historico", $condiciones, $limite, $orden, $grupo, $contar);
    }
}