<?php

/**
* Class Tcomunicados
*
*Class  Tcomunicados maneja todos los aspectos de comunicados
*
* @package  SistemaIGA
* @subpackage Comunicados
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tcomunicados extends class_general{

    /**
    * id de comunicados
    * @var id int
    * @access protected
    */
    protected $id;

    /**
    * titulo de comunicados
    * @var titulo varchar
    * @access public
    */
    public $titulo;

    /**
    * mensaje de comunicados
    * @var mensaje text
    * @access public
    */
    public $mensaje;

    /**
    * fecha_creacion de comunicados
    * @var fecha_creacion datetime
    * @access public
    */
    public $fecha_creacion;

    /**
    * usuario de comunicados
    * @var usuario varchar
    * @access public
    */
    public $usuario;

    /**
    * estado de comunicados
    * @var estado enum
    * @access public
    */
    public $estado;


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
    protected $nombreTabla = 'general.comunicados';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase comunicados
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
                $this->titulo = $arrConstructor[0]['titulo'];
                $this->mensaje = $arrConstructor[0]['mensaje'];
                $this->fecha_creacion = $arrConstructor[0]['fecha_creacion'];
                $this->usuario = $arrConstructor[0]['usuario'];
                $this->estado = $arrConstructor[0]['estado'];
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
        $arrTemp['titulo'] = $this->titulo;
        $arrTemp['mensaje'] = $this->mensaje;
        $arrTemp['fecha_creacion'] = $this->fecha_creacion;
        $arrTemp['usuario'] = $this->usuario;
        $arrTemp['estado'] = $this->estado == '' ? 'habilitada' : $this->estado;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase comunicados o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarComunicados(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto comunicados
     *
     * @return integer
     */
    public function getCodigoComunicados(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de comunicados según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de comunicados y los valores son los valores a actualizar
     */
    public function setComunicados(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["titulo"]))
            $retorno = "titulo";
        else if (!isset($arrCamposValores["mensaje"]))
            $retorno = "mensaje";
        else if (!isset($arrCamposValores["fecha_creacion"]))
            $retorno = "fecha_creacion";
        else if (!isset($arrCamposValores["usuario"]))
            $retorno = "usuario";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setComunicados");
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
    * retorna los campos presentes en la tabla comunicados en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposComunicados(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.comunicados");
    }

    /**
    * Buscar registros en la tabla comunicados
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de comunicados o la cantdad de registros segun el parametro contar
    */
    static function listarComunicados(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.comunicados", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>