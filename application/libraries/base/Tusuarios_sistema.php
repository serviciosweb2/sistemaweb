<?php

/**
* Class Tusuarios_sistema
*
*Class  Tusuarios_sistema maneja todos los aspectos de usuarios_sistema
*
* @package  SistemaIGA
* @subpackage Usuarios_sistema
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tusuarios_sistema extends class_general{

    /**
    * codigo de usuarios_sistema
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * nombre de usuarios_sistema
    * @var nombre varchar
    * @access public
    */
    public $nombre;

    /**
    * apellido de usuarios_sistema
    * @var apellido varchar
    * @access public
    */
    public $apellido;

    /**
    * calle de usuarios_sistema
    * @var calle varchar
    * @access public
    */
    public $calle;

    /**
    * numero de usuarios_sistema
    * @var numero varchar
    * @access public
    */
    public $numero;

    /**
    * complemento de usuarios_sistema
    * @var complemento varchar (requerido)
    * @access public
    */
    public $complemento;

    /**
    * fecha_creacion de usuarios_sistema
    * @var fecha_creacion datetime
    * @access public
    */
    public $fecha_creacion;

    /**
    * email de usuarios_sistema
    * @var email varchar
    * @access public
    */
    public $email;

    /**
    * administra de usuarios_sistema
    * @var administra smallint
    * @access public
    */
    public $administra;

    /**
    * cod_filial de usuarios_sistema
    * @var cod_filial int
    * @access public
    */
    public $cod_filial;

    /**
    * baja de usuarios_sistema
    * @var baja smallint
    * @access public
    */
    public $baja;

    /**
    * idioma de usuarios_sistema
    * @var idioma varchar
    * @access public
    */
    public $idioma;

    /**
    * pass de usuarios_sistema
    * @var pass varchar
    * @access public
    */
    public $pass;


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
    protected $nombreTabla = 'general.usuarios_sistema';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase usuarios_sistema
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
                $this->nombre = $arrConstructor[0]['nombre'];
                $this->apellido = $arrConstructor[0]['apellido'];
                $this->calle = $arrConstructor[0]['calle'];
                $this->numero = $arrConstructor[0]['numero'];
                $this->complemento = $arrConstructor[0]['complemento'];
                $this->fecha_creacion = $arrConstructor[0]['fecha_creacion'];
                $this->email = $arrConstructor[0]['email'];
                $this->administra = $arrConstructor[0]['administra'];
                $this->cod_filial = $arrConstructor[0]['cod_filial'];
                $this->baja = $arrConstructor[0]['baja'];
                $this->idioma = $arrConstructor[0]['idioma'];
                $this->pass = $arrConstructor[0]['pass'];
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
        $arrTemp['nombre'] = $this->nombre;
        $arrTemp['apellido'] = $this->apellido;
        $arrTemp['calle'] = $this->calle;
        $arrTemp['numero'] = $this->numero;
        $arrTemp['complemento'] = $this->complemento == '' ? null : $this->complemento;
        $arrTemp['fecha_creacion'] = $this->fecha_creacion;
        $arrTemp['email'] = $this->email;
        $arrTemp['administra'] = $this->administra == '' ? '0' : $this->administra;
        $arrTemp['cod_filial'] = $this->cod_filial;
        $arrTemp['baja'] = $this->baja == '' ? '0' : $this->baja;
        $arrTemp['idioma'] = $this->idioma == '' ? 'es' : $this->idioma;
        $arrTemp['pass'] = $this->pass;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase usuarios_sistema o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarUsuarios_sistema(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto usuarios_sistema
     *
     * @return integer
     */
    public function getCodigoUsuarios_sistema(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de usuarios_sistema según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de usuarios_sistema y los valores son los valores a actualizar
     */
    public function setUsuarios_sistema(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["nombre"]))
            $retorno = "nombre";
        else if (!isset($arrCamposValores["apellido"]))
            $retorno = "apellido";
        else if (!isset($arrCamposValores["calle"]))
            $retorno = "calle";
        else if (!isset($arrCamposValores["numero"]))
            $retorno = "numero";
        else if (!isset($arrCamposValores["fecha_creacion"]))
            $retorno = "fecha_creacion";
        else if (!isset($arrCamposValores["email"]))
            $retorno = "email";
        else if (!isset($arrCamposValores["administra"]))
            $retorno = "administra";
        else if (!isset($arrCamposValores["cod_filial"]))
            $retorno = "cod_filial";
        else if (!isset($arrCamposValores["baja"]))
            $retorno = "baja";
        else if (!isset($arrCamposValores["idioma"]))
            $retorno = "idioma";
        else if (!isset($arrCamposValores["pass"]))
            $retorno = "pass";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setUsuarios_sistema");
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
    * retorna los campos presentes en la tabla usuarios_sistema en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposUsuarios_sistema(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.usuarios_sistema");
    }

    /**
    * Buscar registros en la tabla usuarios_sistema
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de usuarios_sistema o la cantdad de registros segun el parametro contar
    */
    static function listarUsuarios_sistema(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.usuarios_sistema", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>