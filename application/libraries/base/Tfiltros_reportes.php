<?php

/**
* Class Tfiltros_reportes
*
*Class  Tfiltros_reportes maneja todos los aspectos de filtros_reportes
*
* @package  SistemaIGA
* @subpackage Filtros_reportes
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tfiltros_reportes extends class_general{

    /**
    * codigo de filtros_reportes
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * nombre de filtros_reportes
    * @var nombre varchar
    * @access public
    */
    public $nombre;

    /**
    * codigo_usuario de filtros_reportes
    * @var codigo_usuario int
    * @access public
    */
    public $codigo_usuario;

    /**
    * valores de filtros_reportes
    * @var valores text
    * @access public
    */
    public $valores;

    /**
    * reporte de filtros_reportes
    * @var reporte enum
    * @access public
    */
    public $reporte;

    /**
    * compartido de filtros_reportes
    * @var compartido smallint
    * @access public
    */
    public $compartido;

    /**
    * default de filtros_reportes
    * @var default smallint
    * @access public
    */
    public $default;

    /**
    * solo_lectura de filtros_reportes
    * @var solo_lectura smallint
    * @access public
    */
    public $solo_lectura;


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
    protected $nombreTabla = 'filtros_reportes';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase filtros_reportes
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
                $this->codigo_usuario = $arrConstructor[0]['codigo_usuario'];
                $this->valores = $arrConstructor[0]['valores'];
                $this->reporte = $arrConstructor[0]['reporte'];
                $this->compartido = $arrConstructor[0]['compartido'];
                $this->default = $arrConstructor[0]['default'];
                $this->solo_lectura = $arrConstructor[0]['solo_lectura'];
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
        $arrTemp['codigo_usuario'] = $this->codigo_usuario;
        $arrTemp['valores'] = $this->valores;
        $arrTemp['reporte'] = $this->reporte;
        $arrTemp['compartido'] = $this->compartido == '' ? '0' : $this->compartido;
        $arrTemp['default'] = $this->default == '' ? '0' : $this->default;
        $arrTemp['solo_lectura'] = $this->solo_lectura == '' ? '0' : $this->solo_lectura;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase filtros_reportes o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarFiltros_reportes(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto filtros_reportes
     *
     * @return integer
     */
    public function getCodigoFiltros_reportes(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de filtros_reportes según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de filtros_reportes y los valores son los valores a actualizar
     */
    public function setFiltros_reportes(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["nombre"]))
            $retorno = "nombre";
        else if (!isset($arrCamposValores["codigo_usuario"]))
            $retorno = "codigo_usuario";
        else if (!isset($arrCamposValores["valores"]))
            $retorno = "valores";
        else if (!isset($arrCamposValores["reporte"]))
            $retorno = "reporte";
        else if (!isset($arrCamposValores["compartido"]))
            $retorno = "compartido";
        else if (!isset($arrCamposValores["default"]))
            $retorno = "default";
        else if (!isset($arrCamposValores["solo_lectura"]))
            $retorno = "solo_lectura";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setFiltros_reportes");
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
    * retorna los campos presentes en la tabla filtros_reportes en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposFiltros_reportes(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "filtros_reportes");
    }

    /**
    * Buscar registros en la tabla filtros_reportes
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de filtros_reportes o la cantdad de registros segun el parametro contar
    */
    static function listarFiltros_reportes(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "filtros_reportes", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>