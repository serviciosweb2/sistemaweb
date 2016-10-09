<?php

/**
* Class Tresponsables_telefonos
*
*Class  Tresponsables_telefonos maneja todos los aspectos de responsables_telefonos
*
* @package  SistemaIGA
* @subpackage Responsables_telefonos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tresponsables_telefonos extends class_general{

    /**
    * id_responsable de responsables_telefonos
    * @var id_responsable int
    * @access public
    */
    public $id_responsable;

    /**
    * id_telefono de responsables_telefonos
    * @var id_telefono int
    * @access public
    */
    public $id_telefono;


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
    protected $nombreTabla = 'responsables_telefonos';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase responsables_telefonos
     *
     * @param CI_DB_mysqli_driver $connection
     * @param integer $codigo (opcional) el codigo del objeto a crear
     */
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null){
        $this->oConnection = $conexion;
        if ($codigo != null && $codigo != -1){
            $arrConstructor = $this->_constructor($codigo);
            if (count($arrConstructor) > 0){
                $this->id_responsable = $arrConstructor[0]['id_responsable'];
                $this->id_telefono = $arrConstructor[0]['id_telefono'];
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
        $arrTemp['id_responsable'] = $this->id_responsable;
        $arrTemp['id_telefono'] = $this->id_telefono;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase responsables_telefonos o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarResponsables_telefonos(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto responsables_telefonos
     *
     * @return integer
     */
    public function getCodigoResponsables_telefonos(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de responsables_telefonos según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de responsables_telefonos y los valores son los valores a actualizar
     */
    public function setResponsables_telefonos(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["id_responsable"]))
            $retorno = "id_responsable";
        else if (!isset($arrCamposValores["id_telefono"]))
            $retorno = "id_telefono";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setResponsables_telefonos");
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
    * retorna los campos presentes en la tabla responsables_telefonos en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposResponsables_telefonos(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "responsables_telefonos");
    }

    /**
    * Buscar registros en la tabla responsables_telefonos
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de responsables_telefonos o la cantdad de registros segun el parametro contar
    */
    static function listarResponsables_telefonos(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "responsables_telefonos", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>