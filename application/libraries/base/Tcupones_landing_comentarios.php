<?php

/**
* Class Tcupones_landing_comentarios
*
*Class  Tcupones_landing_comentarios maneja todos los aspectos de cupones_landing_comentarios
*
* @package  SistemaIGA
* @subpackage Cupones_landing_comentarios
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tcupones_landing_comentarios extends class_general{

    /**
    * id de cupones_landing_comentarios
    * @var id int
    * @access protected
    */
    protected $id;

    /**
    * id_cupon_landing de cupones_landing_comentarios
    * @var id_cupon_landing int
    * @access public
    */
    public $id_cupon_landing;

    /**
    * comentario de cupones_landing_comentarios
    * @var comentario text
    * @access public
    */
    public $comentario;

    /**
    * fecha de cupones_landing_comentarios
    * @var fecha datetime
    * @access public
    */
    public $fecha;

    /**
    * id_usuario de cupones_landing_comentarios
    * @var id_usuario int
    * @access public
    */
    public $id_usuario;


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
    protected $nombreTabla = 'publicidad.cupones_landing_comentarios';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase cupones_landing_comentarios
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
                $this->id_cupon_landing = $arrConstructor[0]['id_cupon_landing'];
                $this->comentario = $arrConstructor[0]['comentario'];
                $this->fecha = $arrConstructor[0]['fecha'];
                $this->id_usuario = $arrConstructor[0]['id_usuario'];
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
        $arrTemp['id_cupon_landing'] = $this->id_cupon_landing;
        $arrTemp['comentario'] = $this->comentario;
        $arrTemp['fecha'] = $this->fecha;
        $arrTemp['id_usuario'] = $this->id_usuario;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase cupones_landing_comentarios o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarCupones_landing_comentarios(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto cupones_landing_comentarios
     *
     * @return integer
     */
    public function getCodigoCupones_landing_comentarios(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de cupones_landing_comentarios según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de cupones_landing_comentarios y los valores son los valores a actualizar
     */
    public function setCupones_landing_comentarios(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["id_cupon_landing"]))
            $retorno = "id_cupon_landing";
        else if (!isset($arrCamposValores["comentario"]))
            $retorno = "comentario";
        else if (!isset($arrCamposValores["fecha"]))
            $retorno = "fecha";
        else if (!isset($arrCamposValores["id_usuario"]))
            $retorno = "id_usuario";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setCupones_landing_comentarios");
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
    * retorna los campos presentes en la tabla cupones_landing_comentarios en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposCupones_landing_comentarios(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "publicidad.cupones_landing_comentarios");
    }

    /**
    * Buscar registros en la tabla cupones_landing_comentarios
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de cupones_landing_comentarios o la cantdad de registros segun el parametro contar
    */
    static function listarCupones_landing_comentarios(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "publicidad.cupones_landing_comentarios", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>