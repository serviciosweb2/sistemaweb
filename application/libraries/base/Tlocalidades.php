<?php

/**
* Class Tlocalidades
*
*Class  Tlocalidades maneja todos los aspectos de localidades
*
* @package  SistemaIGA
* @subpackage Localidades
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tlocalidades extends class_general{

    /**
    * id de localidades
    * @var id int
    * @access protected
    */
    protected $id;

    /**
    * departamento_id de localidades
    * @var departamento_id int
    * @access public
    */
    public $departamento_id;

    /**
    * nombre de localidades
    * @var nombre varchar
    * @access public
    */
    public $nombre;

    /**
    * provincia_id de localidades
    * @var provincia_id int
    * @access public
    */
    public $provincia_id;


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
    protected $nombreTabla = 'general.localidades';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase localidades
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
                $this->departamento_id = $arrConstructor[0]['departamento_id'];
                $this->nombre = $arrConstructor[0]['nombre'];
                $this->provincia_id = $arrConstructor[0]['provincia_id'];
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
        $arrTemp['departamento_id'] = $this->departamento_id;
        $arrTemp['nombre'] = $this->nombre;
        $arrTemp['provincia_id'] = $this->provincia_id;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase localidades o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarLocalidades(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto localidades
     *
     * @return integer
     */
    public function getCodigoLocalidades(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de localidades según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de localidades y los valores son los valores a actualizar
     */
    public function setLocalidades(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["departamento_id"]))
            $retorno = "departamento_id";
        else if (!isset($arrCamposValores["nombre"]))
            $retorno = "nombre";
        else if (!isset($arrCamposValores["provincia_id"]))
            $retorno = "provincia_id";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setLocalidades");
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
    * retorna los campos presentes en la tabla localidades en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposLocalidades(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.localidades");
    }

    /**
    * Buscar registros en la tabla localidades
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de localidades o la cantdad de registros segun el parametro contar
    */
    static function listarLocalidades(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.localidades", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>