<?php

/**
* Class Tloginalumnos
*
*Class  Tloginalumnos maneja todos los aspectos de loginalumnos
*
* @package  SistemaIGA
* @subpackage Loginalumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tloginalumnos extends class_general{

    /**
    * codigo de loginalumnos
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * codunico de loginalumnos
    * @var codunico varchar
    * @access public
    */
    public $codunico;

    /**
    * usuario de loginalumnos
    * @var usuario varchar
    * @access public
    */
    public $usuario;

    /**
    * pass de loginalumnos
    * @var pass text
    * @access public
    */
    public $pass;

    /**
    * pass_cruda de loginalumnos
    * @var pass_cruda varchar
    * @access public
    */
    public $pass_cruda;

    /**
    * filial de loginalumnos
    * @var filial int
    * @access public
    */
    public $filial;

    /**
    * mail de loginalumnos
    * @var mail int
    * @access public
    */
    public $mail;

    /**
    * email de loginalumnos
    * @var email varchar
    * @access public
    */
    public $email;

    /**
    * fecha de loginalumnos
    * @var fecha date
    * @access public
    */
    public $fecha;

    /**
    * ultimolog de loginalumnos
    * @var ultimolog date (requerido)
    * @access public
    */
    public $ultimolog;

    /**
    * actualizar_datos de loginalumnos
    * @var actualizar_datos tinyint (requerido)
    * @access public
    */
    public $actualizar_datos;

    /**
    * nombre de loginalumnos
    * @var nombre varchar (requerido)
    * @access public
    */
    public $nombre;

    /**
    * apellido de loginalumnos
    * @var apellido varchar (requerido)
    * @access public
    */
    public $apellido;


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
    protected $nombreTabla = 'alumnos.loginalumnos';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase loginalumnos
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
                $this->codunico = $arrConstructor[0]['codunico'];
                $this->usuario = $arrConstructor[0]['usuario'];
                $this->pass = $arrConstructor[0]['pass'];
                $this->pass_cruda = $arrConstructor[0]['pass_cruda'];
                $this->filial = $arrConstructor[0]['filial'];
                $this->mail = $arrConstructor[0]['mail'];
                $this->email = $arrConstructor[0]['email'];
                $this->fecha = $arrConstructor[0]['fecha'];
                $this->ultimolog = $arrConstructor[0]['ultimolog'];
                $this->actualizar_datos = $arrConstructor[0]['actualizar_datos'];
                $this->nombre = $arrConstructor[0]['nombre'];
                $this->apellido = $arrConstructor[0]['apellido'];
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
        $arrTemp['codunico'] = $this->codunico;
        $arrTemp['usuario'] = $this->usuario;
        $arrTemp['pass'] = $this->pass;
        $arrTemp['pass_cruda'] = $this->pass_cruda;
        $arrTemp['filial'] = $this->filial;
        $arrTemp['mail'] = $this->mail == '' ? '0' : $this->mail;
        $arrTemp['email'] = $this->email;
        $arrTemp['fecha'] = $this->fecha;
        $arrTemp['ultimolog'] = $this->ultimolog == '' ? null : $this->ultimolog;
        $arrTemp['actualizar_datos'] = $this->actualizar_datos == '' ? null : $this->actualizar_datos;
        $arrTemp['nombre'] = $this->nombre == '' ? null : $this->nombre;
        $arrTemp['apellido'] = $this->apellido == '' ? null : $this->apellido;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase loginalumnos o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarLoginalumnos(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto loginalumnos
     *
     * @return integer
     */
    public function getCodigoLoginalumnos(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de loginalumnos según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de loginalumnos y los valores son los valores a actualizar
     */
    public function setLoginalumnos(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["codunico"]))
            $retorno = "codunico";
        else if (!isset($arrCamposValores["usuario"]))
            $retorno = "usuario";
        else if (!isset($arrCamposValores["pass"]))
            $retorno = "pass";
        else if (!isset($arrCamposValores["pass_cruda"]))
            $retorno = "pass_cruda";
        else if (!isset($arrCamposValores["filial"]))
            $retorno = "filial";
        else if (!isset($arrCamposValores["mail"]))
            $retorno = "mail";
        else if (!isset($arrCamposValores["email"]))
            $retorno = "email";
        else if (!isset($arrCamposValores["fecha"]))
            $retorno = "fecha";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setLoginalumnos");
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
    * retorna los campos presentes en la tabla loginalumnos en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposLoginalumnos(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "alumnos.loginalumnos");
    }

    /**
    * Buscar registros en la tabla loginalumnos
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de loginalumnos o la cantdad de registros segun el parametro contar
    */
    static function listarLoginalumnos(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "alumnos.loginalumnos", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>