<?php

/**
* Class Tcaja
*
*Class  Tcaja maneja todos los aspectos de caja
*
* @package  SistemaIGA
* @subpackage Caja
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tcaja extends class_general{

    /**
    * codigo de caja
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * nombre de caja
    * @var nombre varchar
    * @access public
    */
    public $nombre;

    /**
    * estado de caja
    * @var estado enum
    * @access public
    */
    public $estado;

    /**
    * desactivada de caja
    * @var desactivada smallint
    * @access public
    */
    public $desactivada;

    /**
    * cod_moneda de caja
    * @var cod_moneda int
    * @access public
    */
    public $cod_moneda;


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
    protected $nombreTabla = 'caja';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase caja
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
                $this->estado = $arrConstructor[0]['estado'];
                $this->desactivada = $arrConstructor[0]['desactivada'];
                $this->cod_moneda = $arrConstructor[0]['cod_moneda'];
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
        $arrTemp['estado'] = $this->estado == '' ? 'abierta' : $this->estado;
        $arrTemp['desactivada'] = $this->desactivada == '' ? '0' : $this->desactivada;
        $arrTemp['cod_moneda'] = $this->cod_moneda;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase caja o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarCaja(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto caja
     *
     * @return integer
     */
    public function getCodigoCaja(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de caja según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de caja y los valores son los valores a actualizar
     */
    public function setCaja(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["nombre"]))
            $retorno = "nombre";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        else if (!isset($arrCamposValores["desactivada"]))
            $retorno = "desactivada";
        else if (!isset($arrCamposValores["cod_moneda"]))
            $retorno = "cod_moneda";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setCaja");
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
    * retorna los campos presentes en la tabla caja en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposCaja(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "caja");
    }

    /**
    * Buscar registros en la tabla caja
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de caja o la cantdad de registros segun el parametro contar
    */
    static function listarCaja(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "caja", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>