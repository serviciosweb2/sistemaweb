<?php

/**
* Class Tmoras_estados_historicos
*
*Class  Tmoras_estados_historicos maneja todos los aspectos de moras_estados_historicos
*
* @package  SistemaIGA
* @subpackage Moras_estados_historicos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tmoras_estados_historicos extends class_general{

    /**
    * codigo de moras_estados_historicos
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_mora de moras_estados_historicos
    * @var cod_mora int
    * @access public
    */
    public $cod_mora;

    /**
    * dia_desde de moras_estados_historicos
    * @var dia_desde int
    * @access public
    */
    public $dia_desde;

    /**
    * dia_hasta de moras_estados_historicos
    * @var dia_hasta int
    * @access public
    */
    public $dia_hasta;

    /**
    * mora de moras_estados_historicos
    * @var mora float
    * @access public
    */
    public $mora;

    /**
    * es_porcentaje de moras_estados_historicos
    * @var es_porcentaje smallint
    * @access public
    */
    public $es_porcentaje;

    /**
    * baja de moras_estados_historicos
    * @var baja smallint
    * @access public
    */
    public $baja;

    /**
    * diariamente de moras_estados_historicos
    * @var diariamente tinyint
    * @access public
    */
    public $diariamente;

    /**
    * tipo de moras_estados_historicos
    * @var tipo enum
    * @access public
    */
    public $tipo;

    /**
    * fecha_hora de moras_estados_historicos
    * @var fecha_hora datetime
    * @access public
    */
    public $fecha_hora;

    /**
    * usuario_creador de moras_estados_historicos
    * @var usuario_creador int
    * @access public
    */
    public $usuario_creador;


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
    protected $nombreTabla = 'moras_estados_historicos';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase moras_estados_historicos
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
                $this->cod_mora = $arrConstructor[0]['cod_mora'];
                $this->dia_desde = $arrConstructor[0]['dia_desde'];
                $this->dia_hasta = $arrConstructor[0]['dia_hasta'];
                $this->mora = $arrConstructor[0]['mora'];
                $this->es_porcentaje = $arrConstructor[0]['es_porcentaje'];
                $this->baja = $arrConstructor[0]['baja'];
                $this->diariamente = $arrConstructor[0]['diariamente'];
                $this->tipo = $arrConstructor[0]['tipo'];
                $this->fecha_hora = $arrConstructor[0]['fecha_hora'];
                $this->usuario_creador = $arrConstructor[0]['usuario_creador'];
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
        $arrTemp['cod_mora'] = $this->cod_mora;
        $arrTemp['dia_desde'] = $this->dia_desde;
        $arrTemp['dia_hasta'] = $this->dia_hasta;
        $arrTemp['mora'] = $this->mora;
        $arrTemp['es_porcentaje'] = $this->es_porcentaje;
        $arrTemp['baja'] = $this->baja;
        $arrTemp['diariamente'] = $this->diariamente;
        $arrTemp['tipo'] = $this->tipo;
        $arrTemp['fecha_hora'] = $this->fecha_hora;
        $arrTemp['usuario_creador'] = $this->usuario_creador;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase moras_estados_historicos o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarMoras_estados_historicos(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto moras_estados_historicos
     *
     * @return integer
     */
    public function getCodigoMoras_estados_historicos(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de moras_estados_historicos según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de moras_estados_historicos y los valores son los valores a actualizar
     */
    public function setMoras_estados_historicos(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_mora"]))
            $retorno = "cod_mora";
        else if (!isset($arrCamposValores["dia_desde"]))
            $retorno = "dia_desde";
        else if (!isset($arrCamposValores["dia_hasta"]))
            $retorno = "dia_hasta";
        else if (!isset($arrCamposValores["mora"]))
            $retorno = "mora";
        else if (!isset($arrCamposValores["es_porcentaje"]))
            $retorno = "es_porcentaje";
        else if (!isset($arrCamposValores["baja"]))
            $retorno = "baja";
        else if (!isset($arrCamposValores["diariamente"]))
            $retorno = "diariamente";
        else if (!isset($arrCamposValores["tipo"]))
            $retorno = "tipo";
        else if (!isset($arrCamposValores["fecha_hora"]))
            $retorno = "fecha_hora";
        else if (!isset($arrCamposValores["usuario_creador"]))
            $retorno = "usuario_creador";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setMoras_estados_historicos");
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
    * retorna los campos presentes en la tabla moras_estados_historicos en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposMoras_estados_historicos(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "moras_estados_historicos");
    }

    /**
    * Buscar registros en la tabla moras_estados_historicos
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de moras_estados_historicos o la cantdad de registros segun el parametro contar
    */
    static function listarMoras_estados_historicos(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "moras_estados_historicos", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>