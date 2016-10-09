<?php

/**
* Class Tcomprobantes
*
*Class  Tcomprobantes maneja todos los aspectos de comprobantes
*
* @package  SistemaIGA
* @subpackage Comprobantes
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tcomprobantes extends class_general{

    /**
    * id de comprobantes
    * @var id int
    * @access protected
    */
    protected $id;

    /**
    * nombre de comprobantes
    * @var nombre varchar
    * @access public
    */
    public $nombre;

    /**
    * id_pais de comprobantes
    * @var id_pais int
    * @access public
    */
    public $id_pais;


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
    protected $nombreTabla = 'general.comprobantes';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase comprobantes
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
                $this->id_pais = $arrConstructor[0]['id_pais'];
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
        $arrTemp['id_pais'] = $this->id_pais;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase comprobantes o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarComprobantes(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto comprobantes
     *
     * @return integer
     */
    public function getCodigoComprobantes(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de comprobantes según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de comprobantes y los valores son los valores a actualizar
     */
    public function setComprobantes(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["nombre"]))
            $retorno = "nombre";
        else if (!isset($arrCamposValores["id_pais"]))
            $retorno = "id_pais";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setComprobantes");
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
    * retorna los campos presentes en la tabla comprobantes en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposComprobantes(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.comprobantes");
    }

    /**
    * Buscar registros en la tabla comprobantes
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de comprobantes o la cantdad de registros segun el parametro contar
    */
    static function listarComprobantes(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.comprobantes", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>