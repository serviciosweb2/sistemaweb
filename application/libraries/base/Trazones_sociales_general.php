<?php

/**
* Class Trazones_sociales_general
*
*Class  Trazones_sociales_general maneja todos los aspectos de razones_sociales_general
*
* @package  SistemaIGA
* @subpackage Razones_sociales_general
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Trazones_sociales_general extends class_general{

    /**
    * codigo de razones_sociales_general
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * razon_social de razones_sociales_general
    * @var razon_social varchar
    * @access public
    */
    public $razon_social;

    /**
    * tipo_documento de razones_sociales_general
    * @var tipo_documento int
    * @access public
    */
    public $tipo_documento;

    /**
    * documento de razones_sociales_general
    * @var documento varchar
    * @access public
    */
    public $documento;

    /**
    * condicion de razones_sociales_general
    * @var condicion int
    * @access public
    */
    public $condicion;

    /**
    * cod_localidad de razones_sociales_general
    * @var cod_localidad int
    * @access public
    */
    public $cod_localidad;

    /**
    * direccion_calle de razones_sociales_general
    * @var direccion_calle varchar
    * @access public
    */
    public $direccion_calle;

    /**
    * direccion_numero de razones_sociales_general
    * @var direccion_numero varchar
    * @access public
    */
    public $direccion_numero;

    /**
    * direccion_complemento de razones_sociales_general
    * @var direccion_complemento varchar (requerido)
    * @access public
    */
    public $direccion_complemento;

    /**
    * telefono_cod_area de razones_sociales_general
    * @var telefono_cod_area varchar
    * @access public
    */
    public $telefono_cod_area;

    /**
    * telefono_numero de razones_sociales_general
    * @var telefono_numero varchar
    * @access public
    */
    public $telefono_numero;

    /**
    * baja de razones_sociales_general
    * @var baja smallint
    * @access public
    */
    public $baja;

    /**
    * codigo_postal de razones_sociales_general
    * @var codigo_postal varchar (requerido)
    * @access public
    */
    public $codigo_postal;

    /**
    * email de razones_sociales_general
    * @var email varchar (requerido)
    * @access public
    */
    public $email;

    /**
    * barrio de razones_sociales_general
    * @var barrio varchar (requerido)
    * @access public
    */
    public $barrio;

     /**
    * ingresos_brutos de razones_sociales_general
    * @var ingresos_brutos varchar (requerido)
    * @access public
    */
    public $ingresos_brutos;

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
    protected $nombreTabla = 'general.razones_sociales_general';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase razones_sociales_general
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
                $this->razon_social = $arrConstructor[0]['razon_social'];
                $this->tipo_documento = $arrConstructor[0]['tipo_documento'];
                $this->documento = $arrConstructor[0]['documento'];
                $this->condicion = $arrConstructor[0]['condicion'];
                $this->cod_localidad = $arrConstructor[0]['cod_localidad'];
                $this->direccion_calle = $arrConstructor[0]['direccion_calle'];
                $this->direccion_numero = $arrConstructor[0]['direccion_numero'];
                $this->direccion_complemento = $arrConstructor[0]['direccion_complemento'];
                $this->telefono_cod_area = $arrConstructor[0]['telefono_cod_area'];
                $this->telefono_numero = $arrConstructor[0]['telefono_numero'];
                $this->baja = $arrConstructor[0]['baja'];
                $this->codigo_postal = $arrConstructor[0]['codigo_postal'];
                $this->email = $arrConstructor[0]['email'];
                $this->barrio = $arrConstructor[0]['barrio'];
                $this->ingresos_brutos = $arrConstructor[0]['ingresos_brutos'];
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
        $arrTemp['razon_social'] = $this->razon_social;
        $arrTemp['tipo_documento'] = $this->tipo_documento;
        $arrTemp['documento'] = $this->documento;
        $arrTemp['condicion'] = $this->condicion;
        $arrTemp['cod_localidad'] = $this->cod_localidad;
        $arrTemp['direccion_calle'] = $this->direccion_calle;
        $arrTemp['direccion_numero'] = $this->direccion_numero;
        $arrTemp['direccion_complemento'] = $this->direccion_complemento == '' ? null : $this->direccion_complemento;
        $arrTemp['telefono_cod_area'] = $this->telefono_cod_area;
        $arrTemp['telefono_numero'] = $this->telefono_numero;
        $arrTemp['baja'] = $this->baja == '' ? '0' : $this->baja;
        $arrTemp['codigo_postal'] = $this->codigo_postal == '' ? null : $this->codigo_postal;
        $arrTemp['email'] = $this->email == '' ? null : $this->email;
        $arrTemp['barrio'] = $this->barrio == '' ? null : $this->barrio;
        $arrTemp['ingresos_brutos'] = $this->ingresos_brutos == '' ? null : $this->ingresos_brutos;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase razones_sociales_general o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarRazones_sociales_general(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto razones_sociales_general
     *
     * @return integer
     */
    public function getCodigoRazones_sociales_general(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de razones_sociales_general según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de razones_sociales_general y los valores son los valores a actualizar
     */
    public function setRazones_sociales_general(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["razon_social"]))
            $retorno = "razon_social";
        else if (!isset($arrCamposValores["tipo_documento"]))
            $retorno = "tipo_documento";
        else if (!isset($arrCamposValores["documento"]))
            $retorno = "documento";
        else if (!isset($arrCamposValores["condicion"]))
            $retorno = "condicion";
        else if (!isset($arrCamposValores["cod_localidad"]))
            $retorno = "cod_localidad";
        else if (!isset($arrCamposValores["direccion_calle"]))
            $retorno = "direccion_calle";
        else if (!isset($arrCamposValores["direccion_numero"]))
            $retorno = "direccion_numero";
        else if (!isset($arrCamposValores["telefono_cod_area"]))
            $retorno = "telefono_cod_area";
        else if (!isset($arrCamposValores["telefono_numero"]))
            $retorno = "telefono_numero";
        else if (!isset($arrCamposValores["baja"]))
            $retorno = "baja";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setRazones_sociales_general");
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
    * retorna los campos presentes en la tabla razones_sociales_general en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposRazones_sociales_general(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.razones_sociales_general");
    }

    /**
    * Buscar registros en la tabla razones_sociales_general
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de razones_sociales_general o la cantdad de registros segun el parametro contar
    */
    static function listarRazones_sociales_general(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.razones_sociales_general", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>