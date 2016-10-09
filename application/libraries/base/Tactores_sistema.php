<?php

/**
* Class Tactores_sistema
*
*Class  Tactores_sistema maneja todos los aspectos de actores_sistema
*
* @package  SistemaIGA
* @subpackage Actores_sistema
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tactores_sistema extends class_general{

    /**
    * codigo de actores_sistema
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * actor de actores_sistema
    * @var actor varchar
    * @access public
    */
    public $actor;


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
    protected $nombreTabla = 'general.actores_sistema';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase actores_sistema
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
                $this->actor = $arrConstructor[0]['actor'];
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
        $arrTemp['actor'] = $this->actor;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase actores_sistema o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarActores_sistema(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto actores_sistema
     *
     * @return integer
     */
    public function getCodigoActores_sistema(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de actores_sistema según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de actores_sistema y los valores son los valores a actualizar
     */
    public function setActores_sistema(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["actor"]))
            $retorno = "actor";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setActores_sistema");
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
    * retorna los campos presentes en la tabla actores_sistema en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposActores_sistema(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.actores_sistema");
    }

    /**
    * Buscar registros en la tabla actores_sistema
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de actores_sistema o la cantdad de registros segun el parametro contar
    */
    static function listarActores_sistema(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.actores_sistema", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>