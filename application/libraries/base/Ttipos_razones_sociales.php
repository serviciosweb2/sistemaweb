<?php

/**
* Class Ttipos_razones_sociales
*
*Class  Ttipos_razones_sociales maneja todos los aspectos de tipos_razones_sociales
*
* @package  SistemaIGA
* @subpackage Tipos_razones_sociales
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Ttipos_razones_sociales extends class_general{

    /**
    * id de tipos_razones_sociales
    * @var id int
    * @access protected
    */
    protected $id;

    /**
    * nombre de tipos_razones_sociales
    * @var nombre varchar
    * @access public
    */
    public $nombre;


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
    protected $nombreTabla = 'general.tipos_razones_sociales';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase tipos_razones_sociales
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
                $this->nombre = $arrConstructor[0]['nombre'];
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
        $arrTemp['nombre'] = $this->nombre;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase tipos_razones_sociales o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarTipos_razones_sociales(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto tipos_razones_sociales
     *
     * @return integer
     */
    public function getCodigoTipos_razones_sociales(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de tipos_razones_sociales según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de tipos_razones_sociales y los valores son los valores a actualizar
     */
    public function setTipos_razones_sociales(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["nombre"]))
            $retorno = "nombre";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setTipos_razones_sociales");
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
    * retorna los campos presentes en la tabla tipos_razones_sociales en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposTipos_razones_sociales(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.tipos_razones_sociales");
    }

    /**
    * Buscar registros en la tabla tipos_razones_sociales
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de tipos_razones_sociales o la cantdad de registros segun el parametro contar
    */
    static function listarTipos_razones_sociales(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.tipos_razones_sociales", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>