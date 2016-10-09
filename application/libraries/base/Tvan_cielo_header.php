<?php

/**
* Class Tvan_cielo_header
*
*Class  Tvan_cielo_header maneja todos los aspectos de van_cielo_header
*
* @package  SistemaIGA
* @subpackage Van_cielo_header
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tvan_cielo_header extends class_general{

    /**
    * codigo de van_cielo_header
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * tipo_registro de van_cielo_header
    * @var tipo_registro smallint
    * @access public
    */
    public $tipo_registro;

    /**
    * establecimiento_matriz de van_cielo_header
    * @var establecimiento_matriz bigint
    * @access public
    */
    public $establecimiento_matriz;

    /**
    * fecha_procesamiento de van_cielo_header
    * @var fecha_procesamiento date
    * @access public
    */
    public $fecha_procesamiento;

    /**
    * periodo_inicial de van_cielo_header
    * @var periodo_inicial date
    * @access public
    */
    public $periodo_inicial;

    /**
    * periodo_final de van_cielo_header
    * @var periodo_final date
    * @access public
    */
    public $periodo_final;

    /**
    * secuencia de van_cielo_header
    * @var secuencia int
    * @access public
    */
    public $secuencia;

    /**
    * empresa de van_cielo_header
    * @var empresa varchar
    * @access public
    */
    public $empresa;

    /**
    * opcion_extracto de van_cielo_header
    * @var opcion_extracto int
    * @access public
    */
    public $opcion_extracto;

    /**
    * van de van_cielo_header
    * @var van varchar
    * @access public
    */
    public $van;

    /**
    * caja_postal de van_cielo_header
    * @var caja_postal varchar
    * @access public
    */
    public $caja_postal;

    /**
    * version_layout de van_cielo_header
    * @var version_layout varchar
    * @access public
    */
    public $version_layout;

    /**
    * uso_cielo de van_cielo_header
    * @var uso_cielo varchar
    * @access public
    */
    public $uso_cielo;

    /**
    * nombre_archivo de van_cielo_header
    * @var nombre_archivo varchar
    * @access public
    */
    public $nombre_archivo;


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
    protected $nombreTabla = 'tarjetas.van_cielo_header';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase van_cielo_header
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
                $this->tipo_registro = $arrConstructor[0]['tipo_registro'];
                $this->establecimiento_matriz = $arrConstructor[0]['establecimiento_matriz'];
                $this->fecha_procesamiento = $arrConstructor[0]['fecha_procesamiento'];
                $this->periodo_inicial = $arrConstructor[0]['periodo_inicial'];
                $this->periodo_final = $arrConstructor[0]['periodo_final'];
                $this->secuencia = $arrConstructor[0]['secuencia'];
                $this->empresa = $arrConstructor[0]['empresa'];
                $this->opcion_extracto = $arrConstructor[0]['opcion_extracto'];
                $this->van = $arrConstructor[0]['van'];
                $this->caja_postal = $arrConstructor[0]['caja_postal'];
                $this->version_layout = $arrConstructor[0]['version_layout'];
                $this->uso_cielo = $arrConstructor[0]['uso_cielo'];
                $this->nombre_archivo = $arrConstructor[0]['nombre_archivo'];
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
        $arrTemp['tipo_registro'] = $this->tipo_registro == '' ? '0' : $this->tipo_registro;
        $arrTemp['establecimiento_matriz'] = $this->establecimiento_matriz;
        $arrTemp['fecha_procesamiento'] = $this->fecha_procesamiento;
        $arrTemp['periodo_inicial'] = $this->periodo_inicial;
        $arrTemp['periodo_final'] = $this->periodo_final;
        $arrTemp['secuencia'] = $this->secuencia;
        $arrTemp['empresa'] = $this->empresa;
        $arrTemp['opcion_extracto'] = $this->opcion_extracto;
        $arrTemp['van'] = $this->van;
        $arrTemp['caja_postal'] = $this->caja_postal;
        $arrTemp['version_layout'] = $this->version_layout;
        $arrTemp['uso_cielo'] = $this->uso_cielo;
        $arrTemp['nombre_archivo'] = $this->nombre_archivo;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase van_cielo_header o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarVan_cielo_header(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto van_cielo_header
     *
     * @return integer
     */
    public function getCodigoVan_cielo_header(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de van_cielo_header según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de van_cielo_header y los valores son los valores a actualizar
     */
    public function setVan_cielo_header(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["tipo_registro"]))
            $retorno = "tipo_registro";
        else if (!isset($arrCamposValores["establecimiento_matriz"]))
            $retorno = "establecimiento_matriz";
        else if (!isset($arrCamposValores["fecha_procesamiento"]))
            $retorno = "fecha_procesamiento";
        else if (!isset($arrCamposValores["periodo_inicial"]))
            $retorno = "periodo_inicial";
        else if (!isset($arrCamposValores["periodo_final"]))
            $retorno = "periodo_final";
        else if (!isset($arrCamposValores["secuencia"]))
            $retorno = "secuencia";
        else if (!isset($arrCamposValores["empresa"]))
            $retorno = "empresa";
        else if (!isset($arrCamposValores["opcion_extracto"]))
            $retorno = "opcion_extracto";
        else if (!isset($arrCamposValores["van"]))
            $retorno = "van";
        else if (!isset($arrCamposValores["caja_postal"]))
            $retorno = "caja_postal";
        else if (!isset($arrCamposValores["version_layout"]))
            $retorno = "version_layout";
        else if (!isset($arrCamposValores["uso_cielo"]))
            $retorno = "uso_cielo";
        else if (!isset($arrCamposValores["nombre_archivo"]))
            $retorno = "nombre_archivo";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setVan_cielo_header");
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
    * retorna los campos presentes en la tabla van_cielo_header en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposVan_cielo_header(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "tarjetas.van_cielo_header");
    }

    /**
    * Buscar registros en la tabla van_cielo_header
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de van_cielo_header o la cantdad de registros segun el parametro contar
    */
    static function listarVan_cielo_header(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "tarjetas.van_cielo_header", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>