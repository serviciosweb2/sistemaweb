<?php

/**
* Class Ttelefonos
*
*Class  Ttelefonos maneja todos los aspectos de telefonos
*
* @package  SistemaIGA
* @subpackage Telefonos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Ttelefonos extends class_general{

    /**
    * codigo de telefonos
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_area de telefonos
    * @var cod_area varchar
    * @access public
    */
    public $cod_area;

    /**
    * numero de telefonos
    * @var numero varchar
    * @access public
    */
    public $numero;

    /**
    * tipo_telefono de telefonos
    * @var tipo_telefono enum
    * @access public
    */
    public $tipo_telefono;

    /**
    * empresa de telefonos
    * @var empresa int (requerido)
    * @access public
    */
    public $empresa;

    /**
    * baja de telefonos
    * @var baja tinyint
    * @access public
    */
    public $baja;

    /**
    * numero_old de telefonos
    * @var numero_old varchar (requerido)
    * @access public
    */
    public $numero_old;

    /**
    * cod_area_old de telefonos
    * @var cod_area_old varchar (requerido)
    * @access public
    */
    public $cod_area_old;

    /**
     * pais de codigo de area
     * @var pais varchar (no requerido)
     * @access public
     */
    public $pais;


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
    protected $nombreTabla = 'telefonos';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase telefonos
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
                $this->cod_area = $arrConstructor[0]['cod_area'];
                $this->numero = $arrConstructor[0]['numero'];
                $this->tipo_telefono = $arrConstructor[0]['tipo_telefono'];
                $this->empresa = $arrConstructor[0]['empresa'];
                $this->baja = $arrConstructor[0]['baja'];
                $this->numero_old = $arrConstructor[0]['numero_old'];
                $this->cod_area_old = $arrConstructor[0]['cod_area_old'];
                $this->pais = $arrConstructor[0]['pais'];
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
        $arrTemp['cod_area'] = $this->cod_area;
        $arrTemp['numero'] = $this->numero;
        $arrTemp['tipo_telefono'] = $this->tipo_telefono;
        $arrTemp['empresa'] = $this->empresa == '' ? null : $this->empresa;
        $arrTemp['baja'] = $this->baja == '' ? '0' : $this->baja;
        $arrTemp['numero_old'] = $this->numero_old == '' ? null : $this->numero_old;
        $arrTemp['cod_area_old'] = $this->cod_area_old == '' ? null : $this->cod_area_old;
        $arrTemp['pais'] = $this->pais == '' ? null : $this->pais;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase telefonos o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarTelefonos(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto telefonos
     *
     * @return integer
     */
    public function getCodigoTelefonos(){
        return $this->_getCodigo();
    }


    /**
     * actualiza los campos de telefonos según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de telefonos y los valores son los valores a actualizar
     */
    public function setTelefonos(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_area"]))
            $retorno = "cod_area";
        else if (!isset($arrCamposValores["numero"]))
            $retorno = "numero";
        else if (!isset($arrCamposValores["tipo_telefono"]))
            $retorno = "tipo_telefono";
        else if (!isset($arrCamposValores["baja"]))
            $retorno = "baja";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setTelefonos");
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
    * retorna los campos presentes en la tabla telefonos en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposTelefonos(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "telefonos");
    }

    /**
    * Buscar registros en la tabla telefonos
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de telefonos o la cantdad de registros segun el parametro contar
    */
    static function listarTelefonos(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "telefonos", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>