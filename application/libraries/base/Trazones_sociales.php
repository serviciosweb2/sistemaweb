<?php

/**
* Class Trazones_sociales
*
*Class  Trazones_sociales maneja todos los aspectos de razones_sociales
*
* @package  SistemaIGA
* @subpackage Razones_sociales
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Trazones_sociales extends class_general{

    /**
    * codigo de razones_sociales
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * razon_social de razones_sociales
    * @var razon_social varchar
    * @access public
    */
    public $razon_social;

    /**
    * documento de razones_sociales
    * @var documento varchar
    * @access public
    */
    public $documento;

    /**
    * condicion de razones_sociales
    * @var condicion int
    * @access public
    */
    public $condicion;

    /**
    * baja de razones_sociales
    * @var baja int
    * @access public
    */
    public $baja;

    /**
    * tipo_documentos de razones_sociales
    * @var tipo_documentos int
    * @access public
    */
    public $tipo_documentos;

    /**
    * direccion_calle de razones_sociales
    * @var direccion_calle varchar
    * @access public
    */
    public $direccion_calle;

    /**
    * direccion_numero de razones_sociales
    * @var direccion_numero int
    * @access public
    */
    public $direccion_numero;

    /**
    * direccion_complemento de razones_sociales
    * @var direccion_complemento varchar (requerido)
    * @access public
    */
    public $direccion_complemento;

    /**
    * cod_localidad de razones_sociales
    * @var cod_localidad int
    * @access public
    */
    public $cod_localidad;

    /**
    * email de razones_sociales
    * @var email varchar (requerido)
    * @access public
    */
    public $email;

    /**
    * codigo_postal de razones_sociales
    * @var codigo_postal varchar (requerido)
    * @access public
    */
    public $codigo_postal;

    /**
    * fecha_alta de razones_sociales
    * @var fecha_alta datetime
    * @access public
    */
    public $fecha_alta;

    /**
    * inicio_actividades de razones_sociales
    * @var inicio_actividades date
    * @access public
    */
    public $inicio_actividades;

    /**
    * barrio de razones_sociales
    * @var barrio varchar (requerido)
    * @access public
    */
    public $barrio;

    /**
    * usuario_creador de razones_sociales
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
    protected $nombreTabla = 'razones_sociales';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase razones_sociales
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
                $this->documento = $arrConstructor[0]['documento'];
                $this->condicion = $arrConstructor[0]['condicion'];
                $this->baja = $arrConstructor[0]['baja'];
                $this->tipo_documentos = $arrConstructor[0]['tipo_documentos'];
                $this->direccion_calle = $arrConstructor[0]['direccion_calle'];
                $this->direccion_numero = $arrConstructor[0]['direccion_numero'];
                $this->direccion_complemento = $arrConstructor[0]['direccion_complemento'];
                $this->cod_localidad = $arrConstructor[0]['cod_localidad'];
                $this->email = $arrConstructor[0]['email'];
                $this->codigo_postal = $arrConstructor[0]['codigo_postal'];
                $this->fecha_alta = $arrConstructor[0]['fecha_alta'];
                $this->inicio_actividades = $arrConstructor[0]['inicio_actividades'];
                $this->barrio = $arrConstructor[0]['barrio'];
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
        $arrTemp['razon_social'] = $this->razon_social;
        $arrTemp['documento'] = $this->documento;
        $arrTemp['condicion'] = $this->condicion;
        $arrTemp['baja'] = $this->baja == '' ? '0' : $this->baja;
        $arrTemp['tipo_documentos'] = $this->tipo_documentos;
        $arrTemp['direccion_calle'] = $this->direccion_calle;
        $arrTemp['direccion_numero'] = $this->direccion_numero;
        $arrTemp['direccion_complemento'] = $this->direccion_complemento == '' ? null : $this->direccion_complemento;
        $arrTemp['cod_localidad'] = $this->cod_localidad;
        $arrTemp['email'] = $this->email == '' ? null : $this->email;
        $arrTemp['codigo_postal'] = $this->codigo_postal == '' ? null : $this->codigo_postal;
        $arrTemp['fecha_alta'] = $this->fecha_alta == '' ? '0000-00-00 00:00:00' : $this->fecha_alta;
        $arrTemp['inicio_actividades'] = $this->inicio_actividades;
        $arrTemp['barrio'] = $this->barrio == '' ? null : $this->barrio;
        $arrTemp['usuario_creador'] = $this->usuario_creador;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase razones_sociales o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarRazones_sociales(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto razones_sociales
     *
     * @return integer
     */
    public function getCodigoRazones_sociales(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de razones_sociales según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de razones_sociales y los valores son los valores a actualizar
     */
    public function setRazones_sociales(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["razon_social"]))
            $retorno = "razon_social";
        else if (!isset($arrCamposValores["documento"]))
            $retorno = "documento";
        else if (!isset($arrCamposValores["condicion"]))
            $retorno = "condicion";
        else if (!isset($arrCamposValores["baja"]))
            $retorno = "baja";
        else if (!isset($arrCamposValores["tipo_documentos"]))
            $retorno = "tipo_documentos";
        else if (!isset($arrCamposValores["direccion_calle"]))
            $retorno = "direccion_calle";
        else if (!isset($arrCamposValores["direccion_numero"]))
            $retorno = "direccion_numero";
        else if (!isset($arrCamposValores["cod_localidad"]))
            $retorno = "cod_localidad";
        else if (!isset($arrCamposValores["fecha_alta"]))
            $retorno = "fecha_alta";
        else if (!isset($arrCamposValores["inicio_actividades"]))
            $retorno = "inicio_actividades";
        else if (!isset($arrCamposValores["usuario_creador"]))
            $retorno = "usuario_creador";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setRazones_sociales");
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
    * retorna los campos presentes en la tabla razones_sociales en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposRazones_sociales(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "razones_sociales");
    }

    /**
    * Buscar registros en la tabla razones_sociales
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de razones_sociales o la cantdad de registros segun el parametro contar
    */
    static function listarRazones_sociales(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "razones_sociales", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>