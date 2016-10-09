<?php

/**
* Class Tprofesores
*
*Class  Tprofesores maneja todos los aspectos de profesores
*
* @package  SistemaIGA
* @subpackage Profesores
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tprofesores extends class_general{

    /**
    * codigo de profesores
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * nombre de profesores
    * @var nombre varchar
    * @access public
    */
    public $nombre;

    /**
    * fechanac de profesores
    * @var fechanac date (requerido)
    * @access public
    */
    public $fechanac;

    /**
    * estcivil de profesores
    * @var estcivil enum (requerido)
    * @access public
    */
    public $estcivil;

    /**
    * observaciones de profesores
    * @var observaciones varchar (requerido)
    * @access public
    */
    public $observaciones;

    /**
    * fechaalta de profesores
    * @var fechaalta date (requerido)
    * @access public
    */
    public $fechaalta;

    /**
    * cod_localidad de profesores
    * @var cod_localidad int
    * @access public
    */
    public $cod_localidad;

    /**
    * codigopostal de profesores
    * @var codigopostal varchar
    * @access public
    */
    public $codigopostal;

    /**
    * mail de profesores
    * @var mail varchar (requerido)
    * @access public
    */
    public $mail;

    /**
    * tipodocumento de profesores
    * @var tipodocumento int
    * @access public
    */
    public $tipodocumento;

    /**
    * nrodocumento de profesores
    * @var nrodocumento varchar (requerido)
    * @access public
    */
    public $nrodocumento;

    /**
    * calle de profesores
    * @var calle varchar (requerido)
    * @access public
    */
    public $calle;

    /**
    * numero de profesores
    * @var numero int (requerido)
    * @access public
    */
    public $numero;

    /**
    * complemento de profesores
    * @var complemento varchar (requerido)
    * @access public
    */
    public $complemento;

    /**
    * apellido de profesores
    * @var apellido varchar
    * @access public
    */
    public $apellido;

    /**
    * id_usuario_creadpr de profesores
    * @var id_usuario_creadpr int (requerido)
    * @access public
    */
    public $id_usuario_creadpr;

    /**
    * sexo de profesores
    * @var sexo enum (requerido)
    * @access public
    */
    public $sexo;

    /**
    * barrio de profesores
    * @var barrio varchar (requerido)
    * @access public
    */
    public $barrio;

    /**
    * estado de profesores
    * @var estado enum
    * @access public
    */
    public $estado;


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
    protected $nombreTabla = 'profesores';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase profesores
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
                $this->fechanac = $arrConstructor[0]['fechanac'];
                $this->estcivil = $arrConstructor[0]['estcivil'];
                $this->observaciones = $arrConstructor[0]['observaciones'];
                $this->fechaalta = $arrConstructor[0]['fechaalta'];
                $this->cod_localidad = $arrConstructor[0]['cod_localidad'];
                $this->codigopostal = $arrConstructor[0]['codigopostal'];
                $this->mail = $arrConstructor[0]['mail'];
                $this->tipodocumento = $arrConstructor[0]['tipodocumento'];
                $this->nrodocumento = $arrConstructor[0]['nrodocumento'];
                $this->calle = $arrConstructor[0]['calle'];
                $this->numero = $arrConstructor[0]['numero'];
                $this->complemento = $arrConstructor[0]['complemento'];
                $this->apellido = $arrConstructor[0]['apellido'];
                $this->id_usuario_creadpr = $arrConstructor[0]['id_usuario_creadpr'];
                $this->sexo = $arrConstructor[0]['sexo'];
                $this->barrio = $arrConstructor[0]['barrio'];
                $this->estado = $arrConstructor[0]['estado'];
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
        $arrTemp['fechanac'] = $this->fechanac == '' ? null : $this->fechanac;
        $arrTemp['estcivil'] = $this->estcivil == '' ? null : $this->estcivil;
        $arrTemp['observaciones'] = $this->observaciones == '' ? null : $this->observaciones;
        $arrTemp['fechaalta'] = $this->fechaalta == '' ? null : $this->fechaalta;
        $arrTemp['cod_localidad'] = $this->cod_localidad;
        $arrTemp['codigopostal'] = $this->codigopostal;
        $arrTemp['mail'] = $this->mail == '' ? null : $this->mail;
        $arrTemp['tipodocumento'] = $this->tipodocumento;
        $arrTemp['nrodocumento'] = $this->nrodocumento == '' ? null : $this->nrodocumento;
        $arrTemp['calle'] = $this->calle == '' ? null : $this->calle;
        $arrTemp['numero'] = $this->numero == '' ? null : $this->numero;
        $arrTemp['complemento'] = $this->complemento == '' ? null : $this->complemento;
        $arrTemp['apellido'] = $this->apellido;
        $arrTemp['id_usuario_creadpr'] = $this->id_usuario_creadpr == '' ? null : $this->id_usuario_creadpr;
        $arrTemp['sexo'] = $this->sexo == '' ? null : $this->sexo;
        $arrTemp['barrio'] = $this->barrio == '' ? null : $this->barrio;
        $arrTemp['estado'] = $this->estado;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase profesores o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarProfesores(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto profesores
     *
     * @return integer
     */
    public function getCodigoProfesores(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de profesores según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de profesores y los valores son los valores a actualizar
     */
    public function setProfesores(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["nombre"]))
            $retorno = "nombre";
        else if (!isset($arrCamposValores["cod_localidad"]))
            $retorno = "cod_localidad";
        else if (!isset($arrCamposValores["codigopostal"]))
            $retorno = "codigopostal";
        else if (!isset($arrCamposValores["tipodocumento"]))
            $retorno = "tipodocumento";
        else if (!isset($arrCamposValores["apellido"]))
            $retorno = "apellido";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setProfesores");
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
    * retorna los campos presentes en la tabla profesores en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposProfesores(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "profesores");
    }

    /**
    * Buscar registros en la tabla profesores
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de profesores o la cantdad de registros segun el parametro contar
    */
    static function listarProfesores(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "profesores", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>