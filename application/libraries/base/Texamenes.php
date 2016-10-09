<?php

/**
* Class Texamenes
*
*Class  Texamenes maneja todos los aspectos de examenes
*
* @package  SistemaIGA
* @subpackage Examenes
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Texamenes extends class_general{

    /**
    * codigo de examenes
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * tipoexamen de examenes
    * @var tipoexamen enum
    * @access public
    */
    public $tipoexamen;

    /**
    * hora de examenes
    * @var hora time
    * @access public
    */
    public $hora;

    /**
    * horafin de examenes
    * @var horafin time
    * @access public
    */
    public $horafin;

    /**
    * fecha de examenes
    * @var fecha date
    * @access public
    */
    public $fecha;

    /**
    * materia de examenes
    * @var materia int
    * @access public
    */
    public $materia;

    /**
    * observaciones de examenes
    * @var observaciones varchar (requerido)
    * @access public
    */
    public $observaciones;

    /**
    * inscripcionweb de examenes
    * @var inscripcionweb smallint
    * @access public
    */
    public $inscripcionweb;

    /**
    * cupo de examenes
    * @var cupo int
    * @access public
    */
    public $cupo;

    /**
    * baja de examenes
    * @var baja smallint
    * @access public
    */
    public $baja;

    /**
    * cod_comision de examenes
    * @var cod_comision int (requerido)
    * @access public
    */
    public $cod_comision;

    /**
    * ver_campus de examenes
    * @var ver_campus smallint (requerido)
    * @access public
    */
    public $ver_campus;

    /**
    * codigo_examen_padre de examenes
    * @var codigo_examen_padre int (requerido)
    * @access public
    */
    public $codigo_examen_padre;


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
    protected $nombreTabla = 'examenes';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase examenes
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
                $this->tipoexamen = $arrConstructor[0]['tipoexamen'];
                $this->hora = $arrConstructor[0]['hora'];
                $this->horafin = $arrConstructor[0]['horafin'];
                $this->fecha = $arrConstructor[0]['fecha'];
                $this->materia = $arrConstructor[0]['materia'];
                $this->observaciones = $arrConstructor[0]['observaciones'];
                $this->inscripcionweb = $arrConstructor[0]['inscripcionweb'];
                $this->cupo = $arrConstructor[0]['cupo'];
                $this->baja = $arrConstructor[0]['baja'];
                $this->cod_comision = $arrConstructor[0]['cod_comision'];
                $this->ver_campus = $arrConstructor[0]['ver_campus'];
                $this->codigo_examen_padre = $arrConstructor[0]['codigo_examen_padre'];
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
        $arrTemp['tipoexamen'] = $this->tipoexamen;
        $arrTemp['hora'] = $this->hora;
        $arrTemp['horafin'] = $this->horafin;
        $arrTemp['fecha'] = $this->fecha;
        $arrTemp['materia'] = $this->materia;
        $arrTemp['observaciones'] = $this->observaciones == '' ? null : $this->observaciones;
        $arrTemp['inscripcionweb'] = $this->inscripcionweb == '' ? '0' : $this->inscripcionweb;
        $arrTemp['cupo'] = $this->cupo;
        $arrTemp['baja'] = $this->baja == '' ? '0' : $this->baja;
        $arrTemp['cod_comision'] = $this->cod_comision == '' ? null : $this->cod_comision;
        $arrTemp['ver_campus'] = $this->ver_campus == '' ? null : $this->ver_campus;
        $arrTemp['codigo_examen_padre'] = $this->codigo_examen_padre == '' ? null : $this->codigo_examen_padre;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase examenes o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarExamenes(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto examenes
     *
     * @return integer
     */
    public function getCodigoExamenes(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de examenes según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de examenes y los valores son los valores a actualizar
     */
    public function setExamenes(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["tipoexamen"]))
            $retorno = "tipoexamen";
        else if (!isset($arrCamposValores["hora"]))
            $retorno = "hora";
        else if (!isset($arrCamposValores["horafin"]))
            $retorno = "horafin";
        else if (!isset($arrCamposValores["fecha"]))
            $retorno = "fecha";
        else if (!isset($arrCamposValores["materia"]))
            $retorno = "materia";
        else if (!isset($arrCamposValores["inscripcionweb"]))
            $retorno = "inscripcionweb";
        else if (!isset($arrCamposValores["cupo"]))
            $retorno = "cupo";
        else if (!isset($arrCamposValores["baja"]))
            $retorno = "baja";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setExamenes");
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
    * retorna los campos presentes en la tabla examenes en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposExamenes(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "examenes");
    }

    /**
    * Buscar registros en la tabla examenes
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de examenes o la cantdad de registros segun el parametro contar
    */
    static function listarExamenes(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "examenes", $condiciones, $limite, $orden, $grupo, $contar);
    }
}