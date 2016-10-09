<?php

/**
* Class Toffline_sincronizacion
*
*Class  Toffline_sincronizacion maneja todos los aspectos de offline_sincronizacion
*
* @package  SistemaIGA
* @subpackage Offline_sincronizacion
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Toffline_sincronizacion extends class_general{

    /**
    * id de offline_sincronizacion
    * @var id int
    * @access protected
    */
    protected $id;

    /**
    * nombre_tabla de offline_sincronizacion
    * @var nombre_tabla varchar (requerido)
    * @access public
    */
    public $nombre_tabla;

    /**
    * id_registro de offline_sincronizacion
    * @var id_registro varchar (requerido)
    * @access public
    */
    public $id_registro;


    /**
    * primaryKey de la tabla
    * @var primaryKey var
    * @access protected
    */
    protected $primaryKey = "id";
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
    protected $nombreTabla = 'offline_sincronizacion';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase offline_sincronizacion
     *
     * @param CI_DB_mysqli_driver $connection
     * @param integer $codigo (opcional) el codigo del objeto a crear
     */
    function __construct(CI_DB_mysqli_driver $conexion, $id = null){
        $this->oConnection = $conexion;
        if ($id != null && $id != -1){
            $arrConstructor = $this->_constructor($id);
            if (count($arrConstructor) > 0){
                $this->id = $arrConstructor[0]['id'];
                $this->nombre_tabla = $arrConstructor[0]['nombre_tabla'];
                $this->id_registro = $arrConstructor[0]['id_registro'];
            } else {
                $this->id = -1;
            }
        } else {
            $this->id = -1;
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
        $arrTemp['nombre_tabla'] = $this->nombre_tabla == '' ? null : $this->nombre_tabla;
        $arrTemp['id_registro'] = $this->id_registro == '' ? null : $this->id_registro;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase offline_sincronizacion o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarOffline_sincronizacion(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto offline_sincronizacion
     *
     * @return integer
     */
    public function getCodigoOffline_sincronizacion(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de offline_sincronizacion según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de offline_sincronizacion y los valores son los valores a actualizar
     */
    public function setOffline_sincronizacion(array $arrCamposValores){
        $retorno = "";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setOffline_sincronizacion");
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
    * retorna los campos presentes en la tabla offline_sincronizacion en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposOffline_sincronizacion(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "offline_sincronizacion");
    }

    /**
    * Buscar registros en la tabla offline_sincronizacion
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de offline_sincronizacion o la cantdad de registros segun el parametro contar
    */
    static function listarOffline_sincronizacion(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "offline_sincronizacion", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>