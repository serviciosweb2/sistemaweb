<?php

/**
* Class Tbancos
*
*Class  Tbancos maneja todos los aspectos de bancos
*
* @package  SistemaIGA
* @subpackage Bancos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tbancos extends class_general{

    /**
    * codigo de bancos
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * nombre de bancos
    * @var nombre varchar
    * @access public
    */
    public $nombre;

    /**
    * banco_asociado de bancos
    * @var banco_asociado varchar
    * @access public
    */
    public $banco_asociado;

    /**
    * codigo_interno de bancos
    * @var codigo_interno int
    * @access public
    */
    public $codigo_interno;


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
    protected $nombreTabla = 'bancos.bancos';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase bancos
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
                $this->banco_asociado = $arrConstructor[0]['banco_asociado'];
                $this->codigo_interno = $arrConstructor[0]['codigo_interno'];
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
        $arrTemp['banco_asociado'] = $this->banco_asociado;
        $arrTemp['codigo_interno'] = $this->codigo_interno;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase bancos o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarBancos(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto bancos
     *
     * @return integer
     */
    public function getCodigoBancos(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de bancos según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de bancos y los valores son los valores a actualizar
     */
    public function setBancos(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["nombre"]))
            $retorno = "nombre";
        else if (!isset($arrCamposValores["banco_asociado"]))
            $retorno = "banco_asociado";
        else if (!isset($arrCamposValores["codigo_interno"]))
            $retorno = "codigo_interno";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setBancos");
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
    * retorna los campos presentes en la tabla bancos en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposBancos(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "bancos.bancos");
    }

    /**
    * Buscar registros en la tabla bancos
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de bancos o la cantdad de registros segun el parametro contar
    */
    static function listarBancos(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "bancos.bancos", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>