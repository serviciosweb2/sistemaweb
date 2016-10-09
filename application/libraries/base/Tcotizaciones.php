<?php

/**
* Class Tcotizaciones
*
*Class  Tcotizaciones maneja todos los aspectos de cotizaciones
*
* @package  SistemaIGA
* @subpackage Cotizaciones
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tcotizaciones extends class_general{

    /**
    * id de cotizaciones
    * @var id int
    * @access protected
    */
    protected $id;

    /**
    * moneda de cotizaciones
    * @var moneda varchar
    * @access public
    */
    public $moneda;

    /**
    * simbolo de cotizaciones
    * @var simbolo varchar
    * @access public
    */
    public $simbolo;

    /**
    * descripcion de cotizaciones
    * @var descripcion varchar
    * @access public
    */
    public $descripcion;

    /**
    * cotizacion de cotizaciones
    * @var cotizacion decimal
    * @access public
    */
    public $cotizacion;

    /**
    * fecha de cotizaciones
    * @var fecha date
    * @access public
    */
    public $fecha;

    /**
    * hora de cotizaciones
    * @var hora time
    * @access public
    */
    public $hora;

    /**
    * cod_afip de cotizaciones
    * @var cod_afip varchar
    * @access public
    */
    public $cod_afip;

    /**
    * primaryKey de la tabla
    * @var primaryKey var
    * @access protected
    */
    protected $primaryKey = "id";
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
    protected $nombreTabla = 'general.cotizaciones';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase cotizaciones
     *
     * @param CI_DB_mysqli_driver $connection
     * @param integer $codigo (opcional) el codigo del objeto a crear
     */
    function __construct(CI_DB_mysqli_driver $conexion, $id = null){
        $this->oConnection = $conexion;
        if ($id != null && $id != -1){
            $arrConstructor = $this->_constructor($id);
            if (count($arrConstructor) > 0){
                $this->id = $arrConstructor[0]['id'];
                $this->moneda = $arrConstructor[0]['moneda'];
                $this->simbolo = $arrConstructor[0]['simbolo'];
                $this->descripcion = $arrConstructor[0]['descripcion'];
                $this->cotizacion = $arrConstructor[0]['cotizacion'];
                $this->fecha = $arrConstructor[0]['fecha'];
                $this->hora = $arrConstructor[0]['hora'];
                $this->cod_afip = $arrConstructor[0]['cod_afip'];
            } else {
                $this->id = -1;
            }
        } else {
            $this->id = -1;
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
        $arrTemp['moneda'] = $this->moneda;
        $arrTemp['simbolo'] = $this->simbolo;
        $arrTemp['descripcion'] = $this->descripcion;
        $arrTemp['cotizacion'] = $this->cotizacion;
        $arrTemp['fecha'] = $this->fecha;
        $arrTemp['hora'] = $this->hora;
        $arrTemp['cod_afip'] = $this->cod_afip;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase cotizaciones o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarCotizaciones(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto cotizaciones
     *
     * @return integer
     */
    public function getCodigoCotizaciones(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de cotizaciones según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de cotizaciones y los valores son los valores a actualizar
     */
    public function setCotizaciones(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["moneda"]))
            $retorno = "moneda";
        else if (!isset($arrCamposValores["simbolo"]))
            $retorno = "simbolo";
        else if (!isset($arrCamposValores["descripcion"]))
            $retorno = "descripcion";
        else if (!isset($arrCamposValores["cotizacion"]))
            $retorno = "cotizacion";
        else if (!isset($arrCamposValores["fecha"]))
            $retorno = "fecha";
        else if (!isset($arrCamposValores["hora"]))
            $retorno = "hora";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setCotizaciones");
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
    * retorna los campos presentes en la tabla cotizaciones en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposCotizaciones(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.cotizaciones");
    }

    /**
    * Buscar registros en la tabla cotizaciones
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de cotizaciones o la cantdad de registros segun el parametro contar
    */
    static function listarCotizaciones(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.cotizaciones", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>