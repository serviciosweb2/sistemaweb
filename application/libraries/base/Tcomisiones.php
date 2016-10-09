<?php

/**
* Class Tcomisiones
*
*Class  Tcomisiones maneja todos los aspectos de comisiones
*
* @package  SistemaIGA
* @subpackage Comisiones
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tcomisiones extends class_general{

    /**
    * codigo de comisiones
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * nombre de comisiones
    * @var nombre varchar
    * @access public
    */
    public $nombre;

    /**
    * cod_tipo_periodo de comisiones
    * @var cod_tipo_periodo int
    * @access public
    */
    public $cod_tipo_periodo;

    /**
    * ciclo de comisiones
    * @var ciclo int
    * @access public
    */
    public $ciclo;

    /**
    * fecha_creacion de comisiones
    * @var fecha_creacion datetime
    * @access public
    */
    public $fecha_creacion;

    /**
    * usuario_creador de comisiones
    * @var usuario_creador int
    * @access public
    */
    public $usuario_creador;

    /**
    * descripcion de comisiones
    * @var descripcion varchar
    * @access public
    */
    public $descripcion;

    /**
    * cod_plan_academico de comisiones
    * @var cod_plan_academico int
    * @access public
    */
    public $cod_plan_academico;

    /**
    * modalidad de comisiones
    * @var modalidad enum
    * @access public
    */
    public $modalidad;

    /**
    * dias_prorroga de comisiones
    * @var dias_prorroga int (requerido)
    * @access public
    */
    public $dias_prorroga;

    /**
    * estado de comisiones
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
    protected $nombreTabla = 'comisiones';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase comisiones
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
                $this->cod_tipo_periodo = $arrConstructor[0]['cod_tipo_periodo'];
                $this->ciclo = $arrConstructor[0]['ciclo'];
                $this->fecha_creacion = $arrConstructor[0]['fecha_creacion'];
                $this->usuario_creador = $arrConstructor[0]['usuario_creador'];
                $this->descripcion = $arrConstructor[0]['descripcion'];
                $this->cod_plan_academico = $arrConstructor[0]['cod_plan_academico'];
                $this->modalidad = $arrConstructor[0]['modalidad'];
                $this->dias_prorroga = $arrConstructor[0]['dias_prorroga'];
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
        $arrTemp['cod_tipo_periodo'] = $this->cod_tipo_periodo;
        $arrTemp['ciclo'] = $this->ciclo;
        $arrTemp['fecha_creacion'] = $this->fecha_creacion;
        $arrTemp['usuario_creador'] = $this->usuario_creador;
        $arrTemp['descripcion'] = $this->descripcion;
        $arrTemp['cod_plan_academico'] = $this->cod_plan_academico;
        $arrTemp['modalidad'] = $this->modalidad == '' ? 'normal' : $this->modalidad;
        $arrTemp['dias_prorroga'] = $this->dias_prorroga == '' ? null : $this->dias_prorroga;
        $arrTemp['estado'] = $this->estado == '' ? 'habilitado' : $this->estado;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase comisiones o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarComisiones(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto comisiones
     *
     * @return integer
     */
    public function getCodigoComisiones(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de comisiones según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de comisiones y los valores son los valores a actualizar
     */
    public function setComisiones(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["nombre"]))
            $retorno = "nombre";
        else if (!isset($arrCamposValores["cod_tipo_periodo"]))
            $retorno = "cod_tipo_periodo";
        else if (!isset($arrCamposValores["ciclo"]))
            $retorno = "ciclo";
        else if (!isset($arrCamposValores["fecha_creacion"]))
            $retorno = "fecha_creacion";
        else if (!isset($arrCamposValores["usuario_creador"]))
            $retorno = "usuario_creador";
        else if (!isset($arrCamposValores["descripcion"]))
            $retorno = "descripcion";
        else if (!isset($arrCamposValores["cod_plan_academico"]))
            $retorno = "cod_plan_academico";
        else if (!isset($arrCamposValores["modalidad"]))
            $retorno = "modalidad";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setComisiones");
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
    * retorna los campos presentes en la tabla comisiones en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposComisiones(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "comisiones");
    }

    /**
    * Buscar registros en la tabla comisiones
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de comisiones o la cantdad de registros segun el parametro contar
    */
    static function listarComisiones(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "comisiones", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>