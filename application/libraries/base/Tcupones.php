<?php

/**
* Class Tcupones
*
*Class  Tcupones maneja todos los aspectos de cupones
*
* @package  SistemaIGA
* @subpackage Cupones
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tcupones extends class_general{

    /**
    * id de cupones
    * @var id int
    * @access protected
    */
    protected $id;

    /**
    * nombre de cupones
    * @var nombre varchar
    * @access public
    */
    public $nombre;

    /**
    * email de cupones
    * @var email varchar
    * @access public
    */
    public $email;

    /**
    * telefono de cupones
    * @var telefono varchar
    * @access public
    */
    public $telefono;

    /**
    * id_filial de cupones
    * @var id_filial int
    * @access public
    */
    public $id_filial;

    /**
    * estado de cupones
    * @var estado enum
    * @access public
    */
    public $estado;

    /**
    * codigo de cupones
    * @var codigo varchar (requerido)
    * @access public
    */
    public $codigo;

    /**
    * fecha de cupones
    * @var fecha datetime
    * @access public
    */
    public $fecha;

    /**
    * medio de cupones
    * @var medio enum
    * @access public
    */
    public $medio;

    /**
    * id_landing de cupones
    * @var id_landing int
    * @access public
    */
    public $id_landing;

    /**
    * documento de cupones
    * @var documento varchar
    * @access public
    */
    public $documento;


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
    protected $nombreTabla = 'general.cupones';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase cupones
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
                $this->email = $arrConstructor[0]['email'];
                $this->telefono = $arrConstructor[0]['telefono'];
                $this->id_filial = $arrConstructor[0]['id_filial'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->codigo = $arrConstructor[0]['codigo'];
                $this->fecha = $arrConstructor[0]['fecha'];
                $this->medio = $arrConstructor[0]['medio'];
                $this->id_landing = $arrConstructor[0]['id_landing'];
                $this->documento = $arrConstructor[0]['documento'];
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
        $arrTemp['email'] = $this->email;
        $arrTemp['telefono'] = $this->telefono;
        $arrTemp['id_filial'] = $this->id_filial;
        $arrTemp['estado'] = $this->estado == '' ? 'pendiente' : $this->estado;
        $arrTemp['codigo'] = $this->codigo == '' ? null : $this->codigo;
        $arrTemp['fecha'] = $this->fecha;
        $arrTemp['medio'] = $this->medio;
        $arrTemp['id_landing'] = $this->id_landing;
        $arrTemp['documento'] = $this->documento;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase cupones o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarCupones(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto cupones
     *
     * @return integer
     */
    public function getCodigoCupones(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de cupones según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de cupones y los valores son los valores a actualizar
     */
    public function setCupones(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["nombre"]))
            $retorno = "nombre";
        else if (!isset($arrCamposValores["email"]))
            $retorno = "email";
        else if (!isset($arrCamposValores["telefono"]))
            $retorno = "telefono";
        else if (!isset($arrCamposValores["id_filial"]))
            $retorno = "id_filial";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        else if (!isset($arrCamposValores["fecha"]))
            $retorno = "fecha";
        else if (!isset($arrCamposValores["medio"]))
            $retorno = "medio";
        else if (!isset($arrCamposValores["id_landing"]))
            $retorno = "id_landing";
        else if (!isset($arrCamposValores["documento"]))
            $retorno = "documento";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setCupones");
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
    * retorna los campos presentes en la tabla cupones en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposCupones(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.cupones");
    }

    /**
    * Buscar registros en la tabla cupones
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de cupones o la cantdad de registros segun el parametro contar
    */
    static function listarCupones(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.cupones", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>