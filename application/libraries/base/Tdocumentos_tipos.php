<?php

/**
* Class Tdocumentos_tipos
*
*Class  Tdocumentos_tipos maneja todos los aspectos de documentos_tipos
*
* @package  SistemaIGA
* @subpackage Documentos_tipos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tdocumentos_tipos extends class_general{

    /**
    * codigo de documentos_tipos
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * nombre de documentos_tipos
    * @var nombre varchar
    * @access public
    */
    public $nombre;

    /**
    * pais de documentos_tipos
    * @var pais int
    * @access public
    */
    public $pais;

    /**
    * expresion_regular de documentos_tipos
    * @var expresion_regular varchar (requerido)
    * @access public
    */
    public $expresion_regular;

    /**
    * personafisica de documentos_tipos
    * @var personafisica int (requerido)
    * @access public
    */
    public $personafisica;

    /**
    * cod_afip de documentos_tipos
    * @var personafisica int 
    * @access public
    */
    public $cod_afip;
    
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
    protected $nombreTabla = 'general.documentos_tipos';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase documentos_tipos
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
                $this->pais = $arrConstructor[0]['pais'];
                $this->expresion_regular = $arrConstructor[0]['expresion_regular'];
                $this->personafisica = $arrConstructor[0]['personafisica'];
                $this->cod_afip = $arrConstructor[0]['cod_afip'];
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
        $arrTemp['pais'] = $this->pais;
        $arrTemp['expresion_regular'] = $this->expresion_regular == '' ? null : $this->expresion_regular;
        $arrTemp['personafisica'] = $this->personafisica == '' ? null : $this->personafisica;
        $arrTemp['cod_afip'] = $this->cod_afip == '' ? null : $this->cod_afip;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase documentos_tipos o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarDocumentos_tipos(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto documentos_tipos
     *
     * @return integer
     */
    public function getCodigoDocumentos_tipos(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de documentos_tipos según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de documentos_tipos y los valores son los valores a actualizar
     */
    public function setDocumentos_tipos(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["nombre"]))
            $retorno = "nombre";
        else if (!isset($arrCamposValores["pais"]))
            $retorno = "pais";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setDocumentos_tipos");
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
    * retorna los campos presentes en la tabla documentos_tipos en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposDocumentos_tipos(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.documentos_tipos");
    }

    /**
    * Buscar registros en la tabla documentos_tipos
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de documentos_tipos o la cantdad de registros segun el parametro contar
    */
    static function listarDocumentos_tipos(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.documentos_tipos", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>