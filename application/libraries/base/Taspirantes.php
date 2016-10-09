<?php

/**
* Class Taspirantes
*
*Class  Taspirantes maneja todos los aspectos de aspirantes
*
* @package  SistemaIGA
* @subpackage Aspirantes
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Taspirantes extends class_general{

    /**
    * codigo de aspirantes
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * nombre de aspirantes
    * @var nombre varchar
    * @access public
    */
    public $nombre;

    /**
    * fechanaci de aspirantes
    * @var fechanaci date (requerido)
    * @access public
    */
    public $fechanaci;

    /**
    * tipo de aspirantes
    * @var tipo int
    * @access public
    */
    public $tipo;

    /**
    * fechaalta de aspirantes
    * @var fechaalta datetime
    * @access public
    */
    public $fechaalta;

    /**
    * observaciones de aspirantes
    * @var observaciones varchar (requerido)
    * @access public
    */
    public $observaciones;

    /**
    * documento de aspirantes
    * @var documento varchar
    * @access public
    */
    public $documento;

    /**
    * cod_localidad de aspirantes
    * @var cod_localidad int (requerido)
    * @access public
    */
    public $cod_localidad;

    /**
    * codpost de aspirantes
    * @var codpost varchar (requerido)
    * @access public
    */
    public $codpost;

    /**
    * email de aspirantes
    * @var email varchar
    * @access public
    */
    public $email;

    /**
    * comonosconocio de aspirantes
    * @var comonosconocio int
    * @access public
    */
    public $comonosconocio;

    /**
    * apellido de aspirantes
    * @var apellido varchar
    * @access public
    */
    public $apellido;

    /**
    * calle de aspirantes
    * @var calle varchar
    * @access public
    */
    public $calle;

    /**
    * calle_numero de aspirantes
    * @var calle_numero varchar
    * @access public
    */
    public $calle_numero;

    /**
    * calle_complemento de aspirantes
    * @var calle_complemento varchar (requerido)
    * @access public
    */
    public $calle_complemento;

    /**
    * tipo_contacto de aspirantes
    * @var tipo_contacto enum
    * @access public
    */
    public $tipo_contacto;

    /**
    * barrio de aspirantes
    * @var barrio varchar (requerido)
    * @access public
    */
    public $barrio;

    /**
    * usuario_creador de aspirantes
    * @var usuario_creador int
    * @access public
    */
    public $usuario_creador;

    /**
    * email_enviado de aspirantes
    * @var email_enviado smallint
    * @access public
    */
    public $email_enviado;


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
    protected $nombreTabla = 'aspirantes';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase aspirantes
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
                $this->fechanaci = $arrConstructor[0]['fechanaci'];
                $this->tipo = $arrConstructor[0]['tipo'];
                $this->fechaalta = $arrConstructor[0]['fechaalta'];
                $this->observaciones = $arrConstructor[0]['observaciones'];
                $this->documento = $arrConstructor[0]['documento'];
                $this->cod_localidad = $arrConstructor[0]['cod_localidad'];
                $this->codpost = $arrConstructor[0]['codpost'];
                $this->email = $arrConstructor[0]['email'];
                $this->comonosconocio = $arrConstructor[0]['comonosconocio'];
                $this->apellido = $arrConstructor[0]['apellido'];
                $this->calle = $arrConstructor[0]['calle'];
                $this->calle_numero = $arrConstructor[0]['calle_numero'];
                $this->calle_complemento = $arrConstructor[0]['calle_complemento'];
                $this->tipo_contacto = $arrConstructor[0]['tipo_contacto'];
                $this->barrio = $arrConstructor[0]['barrio'];
                $this->usuario_creador = $arrConstructor[0]['usuario_creador'];
                $this->email_enviado = $arrConstructor[0]['email_enviado'];
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
        $arrTemp['fechanaci'] = $this->fechanaci == '' ? null : $this->fechanaci;
        $arrTemp['tipo'] = $this->tipo;
        $arrTemp['fechaalta'] = $this->fechaalta;
        $arrTemp['observaciones'] = $this->observaciones == '' ? null : $this->observaciones;
        $arrTemp['documento'] = $this->documento;
        $arrTemp['cod_localidad'] = $this->cod_localidad == '' ? null : $this->cod_localidad;
        $arrTemp['codpost'] = $this->codpost == '' ? null : $this->codpost;
        $arrTemp['email'] = $this->email;
        $arrTemp['comonosconocio'] = $this->comonosconocio;
        $arrTemp['apellido'] = $this->apellido;
        $arrTemp['calle'] = $this->calle;
        $arrTemp['calle_numero'] = $this->calle_numero;
        $arrTemp['calle_complemento'] = $this->calle_complemento == '' ? null : $this->calle_complemento;
        $arrTemp['tipo_contacto'] = $this->tipo_contacto == '' ? 'PRESENCIAL' : $this->tipo_contacto;
        $arrTemp['barrio'] = $this->barrio == '' ? null : $this->barrio;
        $arrTemp['usuario_creador'] = $this->usuario_creador;
        $arrTemp['email_enviado'] = $this->email_enviado == '' ? '0' : $this->email_enviado;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase aspirantes o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarAspirantes(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto aspirantes
     *
     * @return integer
     */
    public function getCodigoAspirantes(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de aspirantes según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de aspirantes y los valores son los valores a actualizar
     */
    public function setAspirantes(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["nombre"]))
            $retorno = "nombre";
        else if (!isset($arrCamposValores["tipo"]))
            $retorno = "tipo";
        else if (!isset($arrCamposValores["fechaalta"]))
            $retorno = "fechaalta";
        else if (!isset($arrCamposValores["documento"]))
            $retorno = "documento";
        else if (!isset($arrCamposValores["email"]))
            $retorno = "email";
        else if (!isset($arrCamposValores["comonosconocio"]))
            $retorno = "comonosconocio";
        else if (!isset($arrCamposValores["apellido"]))
            $retorno = "apellido";
        else if (!isset($arrCamposValores["calle"]))
            $retorno = "calle";
        else if (!isset($arrCamposValores["calle_numero"]))
            $retorno = "calle_numero";
        else if (!isset($arrCamposValores["tipo_contacto"]))
            $retorno = "tipo_contacto";
        else if (!isset($arrCamposValores["usuario_creador"]))
            $retorno = "usuario_creador";
        else if (!isset($arrCamposValores["email_enviado"]))
            $retorno = "email_enviado";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setAspirantes");
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
    * retorna los campos presentes en la tabla aspirantes en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposAspirantes(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "aspirantes");
    }

    /**
    * Buscar registros en la tabla aspirantes
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de aspirantes o la cantdad de registros segun el parametro contar
    */
    static function listarAspirantes(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "aspirantes", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>
