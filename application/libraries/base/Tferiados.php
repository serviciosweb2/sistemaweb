<?php

/**
* Class Tferiados
*
*Class  Tferiados maneja todos los aspectos de feriados
*
* @package  SistemaIGA
* @subpackage Feriados
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tferiados extends class_general{

    /**
    * codigo de feriados
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * nombre de feriados
    * @var nombre varchar (requerido)
    * @access public
    */
    public $nombre;

    /**
    * dia de feriados
    * @var dia int
    * @access public
    */
    public $dia;

    /**
    * mes de feriados
    * @var mes int
    * @access public
    */
    public $mes;

    /**
    * repite de feriados
    * @var repite smallint
    * @access public
    */
    public $repite;

    /**
    * anio de feriados
    * @var anio int (requerido)
    * @access public
    */
    public $anio;

    /**
    * hora_desde de feriados
    * @var hora_desde time (requerido)
    * @access public
    */
    public $hora_desde;

    /**
    * hora_hasta de feriados
    * @var hora_hasta time (requerido)
    * @access public
    */
    public $hora_hasta;

    /**
    * baja de feriados
    * @var baja int
    * @access public
    */
    public $baja;

    /**
    * fecha_hora de feriados
    * @var fecha_hora datetime
    * @access public
    */
    public $fecha_hora;

    /**
    * cod_usuario de feriados
    * @var cod_usuario int
    * @access public
    */
    public $cod_usuario;


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
    protected $nombreTabla = 'feriados';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase feriados
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
                $this->dia = $arrConstructor[0]['dia'];
                $this->mes = $arrConstructor[0]['mes'];
                $this->repite = $arrConstructor[0]['repite'];
                $this->anio = $arrConstructor[0]['anio'];
                $this->hora_desde = $arrConstructor[0]['hora_desde'];
                $this->hora_hasta = $arrConstructor[0]['hora_hasta'];
                $this->baja = $arrConstructor[0]['baja'];
                $this->fecha_hora = $arrConstructor[0]['fecha_hora'];
                $this->cod_usuario = $arrConstructor[0]['cod_usuario'];
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
        $arrTemp['nombre'] = $this->nombre == '' ? null : $this->nombre;
        $arrTemp['dia'] = $this->dia;
        $arrTemp['mes'] = $this->mes;
        $arrTemp['repite'] = $this->repite == '' ? '0' : $this->repite;
        $arrTemp['anio'] = $this->anio == '' ? null : $this->anio;
        $arrTemp['hora_desde'] = $this->hora_desde == '' ? null : $this->hora_desde;
        $arrTemp['hora_hasta'] = $this->hora_hasta == '' ? null : $this->hora_hasta;
        $arrTemp['baja'] = $this->baja == '' ? '0' : $this->baja;
        $arrTemp['fecha_hora'] = $this->fecha_hora;
        $arrTemp['cod_usuario'] = $this->cod_usuario;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase feriados o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarFeriados(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto feriados
     *
     * @return integer
     */
    public function getCodigoFeriados(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de feriados según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de feriados y los valores son los valores a actualizar
     */
    public function setFeriados(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["dia"]))
            $retorno = "dia";
        else if (!isset($arrCamposValores["mes"]))
            $retorno = "mes";
        else if (!isset($arrCamposValores["repite"]))
            $retorno = "repite";
        else if (!isset($arrCamposValores["baja"]))
            $retorno = "baja";
        else if (!isset($arrCamposValores["fecha_hora"]))
            $retorno = "fecha_hora";
        else if (!isset($arrCamposValores["cod_usuario"]))
            $retorno = "cod_usuario";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setFeriados");
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
    * retorna los campos presentes en la tabla feriados en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposFeriados(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "feriados");
    }

    /**
    * Buscar registros en la tabla feriados
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de feriados o la cantdad de registros segun el parametro contar
    */
    static function listarFeriados(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "feriados", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>