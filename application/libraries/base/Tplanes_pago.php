<?php

/**
* Class Tplanes_pago
*
*Class  Tplanes_pago maneja todos los aspectos de planes_pago
*
* @package  SistemaIGA
* @subpackage Planes_pago
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tplanes_pago extends class_general{

    /**
    * codigo de planes_pago
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * nombre de planes_pago
    * @var nombre varchar
    * @access public
    */
    public $nombre;

    /**
    * fechaalta de planes_pago
    * @var fechaalta date (requerido)
    * @access public
    */
    public $fechaalta;

    /**
    * fechainicio de planes_pago
    * @var fechainicio date (requerido)
    * @access public
    */
    public $fechainicio;

    /**
    * fechavigencia de planes_pago
    * @var fechavigencia date (requerido)
    * @access public
    */
    public $fechavigencia;

    /**
    * descon de planes_pago
    * @var descon tinyint (requerido)
    * @access public
    */
    public $descon;

    /**
    * periodo de planes_pago
    * @var periodo int (requerido)
    * @access public
    */
    public $periodo;

    /**
    * baja de planes_pago
    * @var baja int (requerido)
    * @access public
    */
    public $baja;

    /**
    * cod_usuario de planes_pago
    * @var cod_usuario int (requerido)
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
    protected $nombreTabla = 'planes_pago';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase planes_pago
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
                $this->fechaalta = $arrConstructor[0]['fechaalta'];
                $this->fechainicio = $arrConstructor[0]['fechainicio'];
                $this->fechavigencia = $arrConstructor[0]['fechavigencia'];
                $this->descon = $arrConstructor[0]['descon'];
                $this->periodo = $arrConstructor[0]['periodo'];
                $this->baja = $arrConstructor[0]['baja'];
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
        $arrTemp['nombre'] = $this->nombre;
        $arrTemp['fechaalta'] = $this->fechaalta == '' ? null : $this->fechaalta;
        $arrTemp['fechainicio'] = $this->fechainicio == '' ? null : $this->fechainicio;
        $arrTemp['fechavigencia'] = $this->fechavigencia == '' ? null : $this->fechavigencia;
        $arrTemp['descon'] = $this->descon == '' ? null : $this->descon;
        $arrTemp['periodo'] = $this->periodo == '' ? null : $this->periodo;
        $arrTemp['baja'] = $this->baja == '' ? null : $this->baja;
        $arrTemp['cod_usuario'] = $this->cod_usuario == '' ? null : $this->cod_usuario;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase planes_pago o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarPlanes_pago(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto planes_pago
     *
     * @return integer
     */
    public function getCodigoPlanes_pago(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de planes_pago según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de planes_pago y los valores son los valores a actualizar
     */
    public function setPlanes_pago(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["nombre"]))
            $retorno = "nombre";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setPlanes_pago");
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
    * retorna los campos presentes en la tabla planes_pago en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposPlanes_pago(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "planes_pago");
    }

    /**
    * Buscar registros en la tabla planes_pago
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de planes_pago o la cantdad de registros segun el parametro contar
    */
    static function listarPlanes_pago(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "planes_pago", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>