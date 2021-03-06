<?php

/**
* Class Tseminarios
*
*Class  Tseminarios maneja todos los aspectos de seminarios
*
* @package  SistemaIGA
* @subpackage Seminarios
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tseminarios extends class_general{

    /**
    * id de seminarios
    * @var id int
    * @access protected
    */
    protected $id;

    /**
    * fecha de seminarios
    * @var fecha date
    * @access public
    */
    public $fecha;

    /**
    * id_filial de seminarios
    * @var id_filial int
    * @access public
    */
    public $id_filial;

    /**
    * cupo de seminarios
    * @var cupo int
    * @access public
    */
    public $cupo;


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
    protected $nombreTabla = 'seminarios.seminarios';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase seminarios
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
                $this->fecha = $arrConstructor[0]['fecha'];
                $this->id_filial = $arrConstructor[0]['id_filial'];
                $this->cupo = $arrConstructor[0]['cupo'];
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
        $arrTemp['fecha'] = $this->fecha;
        $arrTemp['id_filial'] = $this->id_filial;
        $arrTemp['cupo'] = $this->cupo;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase seminarios o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarSeminarios(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto seminarios
     *
     * @return integer
     */
    public function getCodigoSeminarios(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de seminarios según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de seminarios y los valores son los valores a actualizar
     */
    public function setSeminarios(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["fecha"]))
            $retorno = "fecha";
        else if (!isset($arrCamposValores["id_filial"]))
            $retorno = "id_filial";
        else if (!isset($arrCamposValores["cupo"]))
            $retorno = "cupo";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setSeminarios");
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
    * retorna los campos presentes en la tabla seminarios en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposSeminarios(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "seminarios.seminarios");
    }

    /**
    * Buscar registros en la tabla seminarios
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de seminarios o la cantdad de registros segun el parametro contar
    */
    static function listarSeminarios(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "seminarios.seminarios", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>