<?php

/**
* Class Tinscriptos
*
*Class  Tinscriptos maneja todos los aspectos de inscriptos
*
* @package  SistemaIGA
* @subpackage Inscriptos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tinscriptos extends class_general{

    /**
    * id de inscriptos
    * @var id int
    * @access protected
    */
    protected $id;

    /**
    * nombre de inscriptos
    * @var nombre varchar
    * @access public
    */
    public $nombre;

    /**
    * apellido de inscriptos
    * @var apellido varchar
    * @access public
    */
    public $apellido;

    /**
    * fecha_nacimiento de inscriptos
    * @var fecha_nacimiento date
    * @access public
    */
    public $fecha_nacimiento;

    /**
    * email de inscriptos
    * @var email varchar
    * @access public
    */
    public $email;

    /**
    * telefono de inscriptos
    * @var telefono varchar
    * @access public
    */
    public $telefono;

    /**
    * referencia de inscriptos
    * @var referencia int
    * @access public
    */
    public $referencia;

    /**
    * documento de inscriptos
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
    protected $nombreTabla = 'seminarios.inscriptos';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase inscriptos
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
                $this->apellido = $arrConstructor[0]['apellido'];
                $this->fecha_nacimiento = $arrConstructor[0]['fecha_nacimiento'];
                $this->email = $arrConstructor[0]['email'];
                $this->telefono = $arrConstructor[0]['telefono'];
                $this->referencia = $arrConstructor[0]['referencia'];
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
        $arrTemp['apellido'] = $this->apellido;
        $arrTemp['fecha_nacimiento'] = $this->fecha_nacimiento;
        $arrTemp['email'] = $this->email;
        $arrTemp['telefono'] = $this->telefono;
        $arrTemp['referencia'] = $this->referencia;
        $arrTemp['documento'] = $this->documento;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase inscriptos o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarInscriptos(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto inscriptos
     *
     * @return integer
     */
    public function getCodigoInscriptos(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de inscriptos según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de inscriptos y los valores son los valores a actualizar
     */
    public function setInscriptos(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["nombre"]))
            $retorno = "nombre";
        else if (!isset($arrCamposValores["apellido"]))
            $retorno = "apellido";
        else if (!isset($arrCamposValores["fecha_nacimiento"]))
            $retorno = "fecha_nacimiento";
        else if (!isset($arrCamposValores["email"]))
            $retorno = "email";
        else if (!isset($arrCamposValores["telefono"]))
            $retorno = "telefono";
        else if (!isset($arrCamposValores["referencia"]))
            $retorno = "referencia";
        else if (!isset($arrCamposValores["documento"]))
            $retorno = "documento";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setInscriptos");
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
    * retorna los campos presentes en la tabla inscriptos en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposInscriptos(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "seminarios.inscriptos");
    }

    /**
    * Buscar registros en la tabla inscriptos
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de inscriptos o la cantdad de registros segun el parametro contar
    */
    static function listarInscriptos(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "seminarios.inscriptos", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>