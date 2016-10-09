<?php

/**
* Class Thorarios_filiales
*
*Class  Thorarios_filiales maneja todos los aspectos de horarios_filiales
*
* @package  SistemaIGA
* @subpackage Horarios_filiales
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Thorarios_filiales extends class_general{

    /**
    * codigo de horarios_filiales
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_filiales de horarios_filiales
    * @var cod_filiales int
    * @access public
    */
    public $cod_filiales;

    /**
    * dia de horarios_filiales
    * @var dia int
    * @access public
    */
    public $dia;

    /**
    * hora_desde de horarios_filiales
    * @var hora_desde time
    * @access public
    */
    public $hora_desde;

    /**
    * hora_hasta de horarios_filiales
    * @var hora_hasta time
    * @access public
    */
    public $hora_hasta;

    /**
    * fecha_creacion de horarios_filiales
    * @var fecha_creacion datetime
    * @access public
    */
    public $fecha_creacion;

    /**
    * cod_usuario de horarios_filiales
    * @var cod_usuario int
    * @access public
    */
    public $cod_usuario;

    /**
    * baja de horarios_filiales
    * @var baja smallint
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
    protected $nombreTabla = 'general.horarios_filiales';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase horarios_filiales
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
                $this->cod_filiales = $arrConstructor[0]['cod_filiales'];
                $this->dia = $arrConstructor[0]['dia'];
                $this->hora_desde = $arrConstructor[0]['hora_desde'];
                $this->hora_hasta = $arrConstructor[0]['hora_hasta'];
                $this->fecha_creacion = $arrConstructor[0]['fecha_creacion'];
                $this->cod_usuario = $arrConstructor[0]['cod_usuario'];
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
        $arrTemp['cod_filiales'] = $this->cod_filiales;
        $arrTemp['dia'] = $this->dia;
        $arrTemp['hora_desde'] = $this->hora_desde;
        $arrTemp['hora_hasta'] = $this->hora_hasta;
        $arrTemp['fecha_creacion'] = $this->fecha_creacion;
        $arrTemp['cod_usuario'] = $this->cod_usuario;
        $arrTemp['baja'] = $this->baja == '' ? '0' : $this->baja;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase horarios_filiales o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarHorarios_filiales(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto horarios_filiales
     *
     * @return integer
     */
    public function getCodigoHorarios_filiales(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de horarios_filiales según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de horarios_filiales y los valores son los valores a actualizar
     */
    public function setHorarios_filiales(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_filiales"]))
            $retorno = "cod_filiales";
        else if (!isset($arrCamposValores["dia"]))
            $retorno = "dia";
        else if (!isset($arrCamposValores["hora_desde"]))
            $retorno = "hora_desde";
        else if (!isset($arrCamposValores["hora_hasta"]))
            $retorno = "hora_hasta";
        else if (!isset($arrCamposValores["fecha_creacion"]))
            $retorno = "fecha_creacion";
        else if (!isset($arrCamposValores["cod_usuario"]))
            $retorno = "cod_usuario";
        else if (!isset($arrCamposValores["baja"]))
            $retorno = "baja";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setHorarios_filiales");
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
    * retorna los campos presentes en la tabla horarios_filiales en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposHorarios_filiales(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.horarios_filiales");
    }

    /**
    * Buscar registros en la tabla horarios_filiales
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de horarios_filiales o la cantdad de registros segun el parametro contar
    */
    static function listarHorarios_filiales(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.horarios_filiales", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>