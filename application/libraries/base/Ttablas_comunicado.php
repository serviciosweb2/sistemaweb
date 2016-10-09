<?php

/**
* Class Ttablas_comunicado
*
*Class  Ttablas_comunicado maneja todos los aspectos de tablas_comunicado
*
* @package  SistemaIGA
* @subpackage Tablas_comunicado
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Ttablas_comunicado extends class_general{

    /**
    * id_comunicado de tablas_comunicado
    * @var id_comunicado int
    * @access protected
    */
    protected $id_comunicado;

    /**
    * texto de tablas_comunicado
    * @var texto varchar
    * @access public
    */
    public $texto;

    /**
    * fecha_hora de tablas_comunicado
    * @var fecha_hora date
    * @access public
    */
    public $fecha_hora;


    /**
    * primaryKey de la tabla
    * @var primaryKey var
    * @access protected
    */
    protected $primaryKey = "id_comunicado";
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
    protected $nombreTabla = 'general.tablas_comunicado';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase tablas_comunicado
     *
     * @param CI_DB_mysqli_driver $connection
     * @param integer $codigo (opcional) el codigo del objeto a crear
     */
    function __construct(CI_DB_mysqli_driver $conexion, $id_comunicado = null){
        $this->oConnection = $conexion;
        if ($id_comunicado != null && $id_comunicado != -1){
            $arrConstructor = $this->_constructor($id_comunicado);
            if (count($arrConstructor) > 0){
                $this->id_comunicado = $arrConstructor[0]['id_comunicado'];
                $this->texto = $arrConstructor[0]['texto'];
                $this->fecha_hora = $arrConstructor[0]['fecha_hora'];
            } else {
                $this->id_comunicado = -1;
            }
        } else {
            $this->id_comunicado = -1;
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
        $arrTemp['texto'] = $this->texto;
        $arrTemp['fecha_hora'] = $this->fecha_hora;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase tablas_comunicado o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarTablas_comunicado(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto tablas_comunicado
     *
     * @return integer
     */
    public function getCodigoTablas_comunicado(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de tablas_comunicado según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de tablas_comunicado y los valores son los valores a actualizar
     */
    public function setTablas_comunicado(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["texto"]))
            $retorno = "texto";
        else if (!isset($arrCamposValores["fecha_hora"]))
            $retorno = "fecha_hora";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setTablas_comunicado");
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
    * retorna los campos presentes en la tabla tablas_comunicado en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposTablas_comunicado(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.tablas_comunicado");
    }

    /**
    * Buscar registros en la tabla tablas_comunicado
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de tablas_comunicado o la cantdad de registros segun el parametro contar
    */
    static function listarTablas_comunicado(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.tablas_comunicado", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>