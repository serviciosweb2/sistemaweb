<?php

/**
* Class Ttiposfacturas
*
*Class  Ttiposfacturas maneja todos los aspectos de tiposfacturas
*
* @package  SistemaIGA
* @subpackage Tiposfacturas
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Ttiposfacturas extends class_general{

    /**
    * codigo de tiposfacturas
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * factura de tiposfacturas
    * @var factura varchar
    * @access public
    */
    public $factura;

    /**
    * copias de tiposfacturas
    * @var copias int (requerido)
    * @access public
    */
    public $copias;

    /**
    * discrimina_iva de tiposfacturas
    * @var discrimina_iva smallint
    * @access public
    */
    public $discrimina_iva;

    /**
    * discrimina_otroimpuesto de tiposfacturas
    * @var discrimina_otroimpuesto smallint
    * @access public
    */
    public $discrimina_otroimpuesto;

    /**
    * codigocontrol de tiposfacturas
    * @var codigocontrol int (requerido)
    * @access public
    */
    public $codigocontrol;

    /**
    * codtalonariousado de tiposfacturas
    * @var codtalonariousado int (requerido)
    * @access public
    */
    public $codtalonariousado;

    /**
    * fecha_hora_cc de tiposfacturas
    * @var fecha_hora_cc datetime (requerido)
    * @access public
    */
    public $fecha_hora_cc;


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
    protected $nombreTabla = 'tiposfacturas';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase tiposfacturas
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
                $this->factura = $arrConstructor[0]['factura'];
                $this->copias = $arrConstructor[0]['copias'];
                $this->discrimina_iva = $arrConstructor[0]['discrimina_iva'];
                $this->discrimina_otroimpuesto = $arrConstructor[0]['discrimina_otroimpuesto'];
                $this->codigocontrol = $arrConstructor[0]['codigocontrol'];
                $this->codtalonariousado = $arrConstructor[0]['codtalonariousado'];
                $this->fecha_hora_cc = $arrConstructor[0]['fecha_hora_cc'];
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
        $arrTemp['factura'] = $this->factura;
        $arrTemp['copias'] = $this->copias == '' ? null : $this->copias;
        $arrTemp['discrimina_iva'] = $this->discrimina_iva;
        $arrTemp['discrimina_otroimpuesto'] = $this->discrimina_otroimpuesto;
        $arrTemp['codigocontrol'] = $this->codigocontrol == '' ? null : $this->codigocontrol;
        $arrTemp['codtalonariousado'] = $this->codtalonariousado == '' ? null : $this->codtalonariousado;
        $arrTemp['fecha_hora_cc'] = $this->fecha_hora_cc == '' ? null : $this->fecha_hora_cc;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase tiposfacturas o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarTiposfacturas(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto tiposfacturas
     *
     * @return integer
     */
    public function getCodigoTiposfacturas(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de tiposfacturas según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de tiposfacturas y los valores son los valores a actualizar
     */
    public function setTiposfacturas(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["factura"]))
            $retorno = "factura";
        else if (!isset($arrCamposValores["discrimina_iva"]))
            $retorno = "discrimina_iva";
        else if (!isset($arrCamposValores["discrimina_otroimpuesto"]))
            $retorno = "discrimina_otroimpuesto";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setTiposfacturas");
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
    * retorna los campos presentes en la tabla tiposfacturas en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposTiposfacturas(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "tiposfacturas");
    }

    /**
    * Buscar registros en la tabla tiposfacturas
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de tiposfacturas o la cantdad de registros segun el parametro contar
    */
    static function listarTiposfacturas(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "tiposfacturas", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>