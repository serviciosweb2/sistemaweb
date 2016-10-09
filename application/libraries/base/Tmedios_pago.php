<?php

/**
* Class Tmedios_pago
*
*Class  Tmedios_pago maneja todos los aspectos de medios_pago
*
* @package  SistemaIGA
* @subpackage Medios_pago
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tmedios_pago extends class_general{

    /**
    * codigo de medios_pago
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * medio de medios_pago
    * @var medio varchar
    * @access public
    */
    public $medio;


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
    protected $nombreTabla = 'general.medios_pago';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase medios_pago
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
                $this->medio = $arrConstructor[0]['medio'];
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
        $arrTemp['medio'] = $this->medio;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase medios_pago o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarMedios_pago(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto medios_pago
     *
     * @return integer
     */
    public function getCodigoMedios_pago(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de medios_pago según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de medios_pago y los valores son los valores a actualizar
     */
    public function setMedios_pago(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["medio"]))
            $retorno = "medio";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setMedios_pago");
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
    * retorna los campos presentes en la tabla medios_pago en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposMedios_pago(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.medios_pago");
    }

    /**
    * Buscar registros en la tabla medios_pago
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de medios_pago o la cantdad de registros segun el parametro contar
    */
    static function listarMedios_pago(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.medios_pago", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>