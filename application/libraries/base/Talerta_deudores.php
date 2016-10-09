<?php

/**
* Class Talerta_deudores
*
*Class  Talerta_deudores maneja todos los aspectos de alerta_deudores
*
* @package  SistemaIGA
* @subpackage Alerta_deudores
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Talerta_deudores extends class_general{

    /**
    * codigo de alerta_deudores
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * id_ctacte de alerta_deudores
    * @var id_ctacte int
    * @access public
    */
    public $id_ctacte;

    /**
    * fecha de alerta_deudores
    * @var fecha datetime
    * @access public
    */
    public $fecha;


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
    protected $nombreTabla = 'alerta_deudores';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase alerta_deudores
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
                $this->id_ctacte = $arrConstructor[0]['id_ctacte'];
                $this->fecha = $arrConstructor[0]['fecha'];
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
        $arrTemp['id_ctacte'] = $this->id_ctacte;
        $arrTemp['fecha'] = $this->fecha;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase alerta_deudores o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarAlerta_deudores(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto alerta_deudores
     *
     * @return integer
     */
    public function getCodigoAlerta_deudores(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de alerta_deudores según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de alerta_deudores y los valores son los valores a actualizar
     */
    public function setAlerta_deudores(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["id_ctacte"]))
            $retorno = "id_ctacte";
        else if (!isset($arrCamposValores["fecha"]))
            $retorno = "fecha";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setAlerta_deudores");
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
    * retorna los campos presentes en la tabla alerta_deudores en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposAlerta_deudores(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "alerta_deudores");
    }

    /**
    * Buscar registros en la tabla alerta_deudores
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de alerta_deudores o la cantdad de registros segun el parametro contar
    */
    static function listarAlerta_deudores(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "alerta_deudores", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>