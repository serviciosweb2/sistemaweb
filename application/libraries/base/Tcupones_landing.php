<?php

/**
* Class Tcupones_landing
*
*Class  Tcupones_landing maneja todos los aspectos de cupones_landing
*
* @package  SistemaIGA
* @subpackage Cupones_landing
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tcupones_landing extends class_general{

    /**
    * id de cupones_landing
    * @var id int
    * @access protected
    */
    protected $id;

    /**
    * id_filial de cupones_landing
    * @var id_filial int
    * @access public
    */
    public $id_filial;

    /**
    * id_curso de cupones_landing
    * @var id_curso int
    * @access public
    */
    public $id_curso;

    /**
    * nombre de cupones_landing
    * @var nombre varchar
    * @access public
    */
    public $nombre;

    /**
    * email de cupones_landing
    * @var email varchar
    * @access public
    */
    public $email;

    /**
    * telefono de cupones_landing
    * @var telefono varchar
    * @access public
    */
    public $telefono;

    /**
    * documento de cupones_landing
    * @var documento varchar
    * @access public
    */
    public $documento;

    /**
    * fecha de cupones_landing
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
    protected $nombreTabla = 'publicidad.cupones_landing';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase cupones_landing
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
                $this->id_curso = $arrConstructor[0]['id_curso'];
                $this->nombre = $arrConstructor[0]['nombre'];
                $this->email = $arrConstructor[0]['email'];
                $this->telefono = $arrConstructor[0]['telefono'];
                $this->documento = $arrConstructor[0]['documento'];
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
        $arrTemp['id_filial'] = $this->id_filial;
        $arrTemp['id_curso'] = $this->id_curso;
        $arrTemp['nombre'] = $this->nombre;
        $arrTemp['email'] = $this->email;
        $arrTemp['telefono'] = $this->telefono;
        $arrTemp['documento'] = $this->documento;
        $arrTemp['fecha'] = $this->fecha;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase cupones_landing o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarCupones_landing(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto cupones_landing
     *
     * @return integer
     */
    public function getCodigoCupones_landing(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de cupones_landing según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de cupones_landing y los valores son los valores a actualizar
     */
    public function setCupones_landing(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["id_filial"]))
            $retorno = "id_filial";
        else if (!isset($arrCamposValores["id_curso"]))
            $retorno = "id_curso";
        else if (!isset($arrCamposValores["nombre"]))
            $retorno = "nombre";
        else if (!isset($arrCamposValores["email"]))
            $retorno = "email";
        else if (!isset($arrCamposValores["telefono"]))
            $retorno = "telefono";
        else if (!isset($arrCamposValores["documento"]))
            $retorno = "documento";
        else if (!isset($arrCamposValores["fecha"]))
            $retorno = "fecha";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setCupones_landing");
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
    * retorna los campos presentes en la tabla cupones_landing en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposCupones_landing(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "publicidad.cupones_landing");
    }

    /**
    * Buscar registros en la tabla cupones_landing
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de cupones_landing o la cantdad de registros segun el parametro contar
    */
    static function listarCupones_landing(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "publicidad.cupones_landing", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>