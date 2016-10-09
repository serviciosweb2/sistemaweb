<?php

/**
* Class Thorarios
*
*Class  Thorarios maneja todos los aspectos de horarios
*
* @package  SistemaIGA
* @subpackage Horarios
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Thorarios extends class_general{

    /**
    * codigo de horarios
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * dia de horarios
    * @var dia date
    * @access public
    */
    public $dia;

    /**
    * horadesde de horarios
    * @var horadesde time
    * @access public
    */
    public $horadesde;

    /**
    * horahasta de horarios
    * @var horahasta time
    * @access public
    */
    public $horahasta;

    /**
    * cod_comision de horarios
    * @var cod_comision int
    * @access public
    */
    public $cod_comision;

    /**
    * cod_salon de horarios
    * @var cod_salon int
    * @access public
    */
    public $cod_salon;

    /**
    * cod_materia de horarios
    * @var cod_materia int
    * @access public
    */
    public $cod_materia;

    /**
    * baja de horarios
    * @var baja smallint
    * @access public
    */
    public $baja;

    /**
    * padre de horarios
    * @var padre int
    * @access public
    */
    public $padre;

    /**
    * asistencia de horarios
    * @var asistencia int
    * @access public
    */
    public $asistencia;


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
    protected $nombreTabla = 'horarios';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase horarios
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
                $this->dia = $arrConstructor[0]['dia'];
                $this->horadesde = $arrConstructor[0]['horadesde'];
                $this->horahasta = $arrConstructor[0]['horahasta'];
                $this->cod_comision = $arrConstructor[0]['cod_comision'];
                $this->cod_salon = $arrConstructor[0]['cod_salon'];
                $this->cod_materia = $arrConstructor[0]['cod_materia'];
                $this->baja = $arrConstructor[0]['baja'];
                $this->padre = $arrConstructor[0]['padre'];
                $this->asistencia = $arrConstructor[0]['asistencia'];
            } else {
                $this->codigo = -1;
                $this->asistencia = '0';
            }
        } else {
            $this->codigo = -1;
            $this->asistencia = '0';
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
        $arrTemp['dia'] = $this->dia;
        $arrTemp['horadesde'] = $this->horadesde;
        $arrTemp['horahasta'] = $this->horahasta;
        $arrTemp['cod_comision'] = $this->cod_comision;
        $arrTemp['cod_salon'] = $this->cod_salon;
        $arrTemp['cod_materia'] = $this->cod_materia;
        $arrTemp['baja'] = $this->baja == '' ? '0' : $this->baja;
        $arrTemp['padre'] = $this->padre == '' ? '0' : $this->padre;
        $arrTemp['asistencia'] = $this->asistencia;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase horarios o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarHorarios(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto horarios
     *
     * @return integer
     */
    public function getCodigoHorarios(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de horarios según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de horarios y los valores son los valores a actualizar
     */
    public function setHorarios(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["dia"]))
            $retorno = "dia";
        else if (!isset($arrCamposValores["horadesde"]))
            $retorno = "horadesde";
        else if (!isset($arrCamposValores["horahasta"]))
            $retorno = "horahasta";
        else if (!isset($arrCamposValores["cod_comision"]))
            $retorno = "cod_comision";
        else if (!isset($arrCamposValores["cod_salon"]))
            $retorno = "cod_salon";
        else if (!isset($arrCamposValores["cod_materia"]))
            $retorno = "cod_materia";
        else if (!isset($arrCamposValores["baja"]))
            $retorno = "baja";
        else if (!isset($arrCamposValores["padre"]))
            $retorno = "padre";
        else if (!isset($arrCamposValores["asistencia"]))
            $retorno = "asistencia";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setHorarios");
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
    * retorna los campos presentes en la tabla horarios en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposHorarios(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "horarios");
    }

    /**
    * Buscar registros en la tabla horarios
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de horarios o la cantdad de registros segun el parametro contar
    */
    static function listarHorarios(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "horarios", $condiciones, $limite, $orden, $grupo, $contar);
    }
}