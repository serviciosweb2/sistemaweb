<?php

/**
* Class Talumnos
*
*Class  Talumnos maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Talumnos extends class_general{

    /**
    * codigo de alumnos
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * nombre de alumnos
    * @var nombre varchar
    * @access public
    */
    public $nombre;

    /**
    * apellido de alumnos
    * @var apellido varchar
    * @access public
    */
    public $apellido;

    /**
    * fechanaci de alumnos
    * @var fechanaci date
    * @access public
    */
    public $fechanaci;

    /**
    * sexo de alumnos
    * @var sexo enum (requerido)
    * @access public
    */
    public $sexo;

    /**
    * estado_civil de alumnos
    * @var estado_civil enum (requerido)
    * @access public
    */
    public $estado_civil;

    /**
    * profesion de alumnos
    * @var profesion varchar (requerido)
    * @access public
    */
    public $profesion;

    /**
    * tipo de alumnos
    * @var tipo int
    * @access public
    */
    public $tipo;

    /**
    * fechaalta de alumnos
    * @var fechaalta datetime
    * @access public
    */
    public $fechaalta;

    /**
    * trabajo de alumnos
    * @var trabajo varchar (requerido)
    * @access public
    */
    public $trabajo;

    /**
    * estudios de alumnos
    * @var estudios varchar (requerido)
    * @access public
    */
    public $estudios;

    /**
    * observaciones de alumnos
    * @var observaciones varchar (requerido)
    * @access public
    */
    public $observaciones;

    /**
    * id_usuario_creador de alumnos
    * @var id_usuario_creador int (requerido)
    * @access public
    */
    public $id_usuario_creador;

    /**
    * calle de alumnos
    * @var calle varchar
    * @access public
    */
    public $calle;

    /**
    * calle_numero de alumnos
    * @var calle_numero int
    * @access public
    */
    public $calle_numero;

    /**
    * calle_complemento de alumnos
    * @var calle_complemento varchar (requerido)
    * @access public
    */
    public $calle_complemento;

    /**
    * id_actor de alumnos
    * @var id_actor int (requerido)
    * @access public
    */
    public $id_actor;

    /**
    * id_talle de alumnos
    * @var id_talle int (requerido)
    * @access public
    */
    public $id_talle;

    /**
    * documento de alumnos
    * @var documento varchar
    * @access public
    */
    public $documento;

    /**
    * id_localidad de alumnos
    * @var id_localidad int
    * @access public
    */
    public $id_localidad;

    /**
    * codpost de alumnos
    * @var codpost varchar (requerido)
    * @access public
    */
    public $codpost;

    /**
    * email de alumnos
    * @var email varchar (requerido)
    * @access public
    */
    public $email;

    /**
    * comonosconocio de alumnos
    * @var comonosconocio int (requerido)
    * @access public
    */
    public $comonosconocio;

    /**
    * id_lugar_nacimiento de alumnos
    * @var id_lugar_nacimiento int (requerido)
    * @access public
    */
    public $id_lugar_nacimiento;

    /**
    * barrio de alumnos
    * @var barrio varchar (requerido)
    * @access public
    */
    public $barrio;

    /**
    * baja de alumnos
    * @var baja enum
    * @access public
    */
    public $baja;


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
    protected $nombreTabla = 'alumnos';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase alumnos
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
                $this->fechanaci = $arrConstructor[0]['fechanaci'];
                $this->sexo = $arrConstructor[0]['sexo'];
                $this->estado_civil = $arrConstructor[0]['estado_civil'];
                $this->profesion = $arrConstructor[0]['profesion'];
                $this->tipo = $arrConstructor[0]['tipo'];
                $this->fechaalta = $arrConstructor[0]['fechaalta'];
                $this->trabajo = $arrConstructor[0]['trabajo'];
                $this->estudios = $arrConstructor[0]['estudios'];
                $this->observaciones = $arrConstructor[0]['observaciones'];
                $this->id_usuario_creador = $arrConstructor[0]['id_usuario_creador'];
                $this->calle = $arrConstructor[0]['calle'];
                $this->calle_numero = $arrConstructor[0]['calle_numero'];
                $this->calle_complemento = $arrConstructor[0]['calle_complemento'];
                $this->id_actor = $arrConstructor[0]['id_actor'];
                $this->id_talle = $arrConstructor[0]['id_talle'];
                $this->documento = $arrConstructor[0]['documento'];
                $this->id_localidad = $arrConstructor[0]['id_localidad'];
                $this->codpost = $arrConstructor[0]['codpost'];
                $this->email = $arrConstructor[0]['email'];
                $this->comonosconocio = $arrConstructor[0]['comonosconocio'];
                $this->id_lugar_nacimiento = $arrConstructor[0]['id_lugar_nacimiento'];
                $this->barrio = $arrConstructor[0]['barrio'];
                $this->baja = $arrConstructor[0]['baja'];
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
        $arrTemp['fechanaci'] = $this->fechanaci;
        $arrTemp['sexo'] = $this->sexo == '' ? null : $this->sexo;
        $arrTemp['estado_civil'] = $this->estado_civil == '' ? null : $this->estado_civil;
        $arrTemp['profesion'] = $this->profesion == '' ? null : $this->profesion;
        $arrTemp['tipo'] = $this->tipo;
        $arrTemp['fechaalta'] = $this->fechaalta;
        $arrTemp['trabajo'] = $this->trabajo == '' ? null : $this->trabajo;
        $arrTemp['estudios'] = $this->estudios == '' ? null : $this->estudios;
        $arrTemp['observaciones'] = $this->observaciones == '' ? null : $this->observaciones;
        $arrTemp['id_usuario_creador'] = $this->id_usuario_creador == '' ? null : $this->id_usuario_creador;
        $arrTemp['calle'] = $this->calle;
        $arrTemp['calle_numero'] = $this->calle_numero;
        $arrTemp['calle_complemento'] = $this->calle_complemento == '' ? null : $this->calle_complemento;
        $arrTemp['id_actor'] = $this->id_actor == '' ? null : $this->id_actor;
        $arrTemp['id_talle'] = $this->id_talle == '' ? null : $this->id_talle;
        $arrTemp['documento'] = $this->documento;
        $arrTemp['id_localidad'] = $this->id_localidad;
        $arrTemp['codpost'] = $this->codpost == '' ? null : $this->codpost;
        $arrTemp['email'] = $this->email == '' ? null : $this->email;
        $arrTemp['comonosconocio'] = $this->comonosconocio == '' ? null : $this->comonosconocio;
        $arrTemp['id_lugar_nacimiento'] = $this->id_lugar_nacimiento == '' ? null : $this->id_lugar_nacimiento;
        $arrTemp['barrio'] = $this->barrio == '' ? null : $this->barrio;
        $arrTemp['baja'] = $this->baja == '' ? 'habilitada' : $this->baja;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase alumnos o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarAlumnos(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto alumnos
     *
     * @return integer
     */
    public function getCodigoAlumnos(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de alumnos según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de alumnos y los valores son los valores a actualizar
     */
    public function setAlumnos(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["nombre"]))
            $retorno = "nombre";
        else if (!isset($arrCamposValores["apellido"]))
            $retorno = "apellido";
        else if (!isset($arrCamposValores["fechanaci"]))
            $retorno = "fechanaci";
        else if (!isset($arrCamposValores["tipo"]))
            $retorno = "tipo";
        else if (!isset($arrCamposValores["fechaalta"]))
            $retorno = "fechaalta";
        else if (!isset($arrCamposValores["calle"]))
            $retorno = "calle";
        else if (!isset($arrCamposValores["calle_numero"]))
            $retorno = "calle_numero";
        else if (!isset($arrCamposValores["documento"]))
            $retorno = "documento";
        else if (!isset($arrCamposValores["id_localidad"]))
            $retorno = "id_localidad";
        else if (!isset($arrCamposValores["baja"]))
            $retorno = "baja";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setAlumnos");
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
    * retorna los campos presentes en la tabla alumnos en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposAlumnos(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "alumnos");
    }

    /**
    * Buscar registros en la tabla alumnos
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de alumnos o la cantdad de registros segun el parametro contar
    */
    static function listarAlumnos(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "alumnos", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>