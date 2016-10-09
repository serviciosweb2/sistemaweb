<?php

/**
* Class Tsecciones
*
*Class  Tsecciones maneja todos los aspectos de secciones
*
* @package  SistemaIGA
* @subpackage Secciones
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tsecciones extends class_general{

    /**
    * codigo de secciones
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * slug de secciones
    * @var slug varchar
    * @access public
    */
    public $slug;

    /**
    * id_seccion_padre de secciones
    * @var id_seccion_padre int
    * @access public
    */
    public $id_seccion_padre;

    /**
    * menu_tipo de secciones
    * @var menu_tipo varchar (requerido)
    * @access public
    */
    public $menu_tipo;

    /**
    * method de secciones
    * @var method varchar (requerido)
    * @access public
    */
    public $method;

    /**
    * categoria de secciones
    * @var categoria varchar (requerido)
    * @access public
    */
    public $categoria;

    /**
    * control de secciones
    * @var control varchar (requerido)
    * @access public
    */
    public $control;

    /**
    * prioridad de secciones
    * @var prioridad int
    * @access public
    */
    public $prioridad;


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
    protected $nombreTabla = 'general.secciones';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase secciones
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
                $this->slug = $arrConstructor[0]['slug'];
                $this->id_seccion_padre = $arrConstructor[0]['id_seccion_padre'];
                $this->menu_tipo = $arrConstructor[0]['menu_tipo'];
                $this->method = $arrConstructor[0]['method'];
                $this->categoria = $arrConstructor[0]['categoria'];
                $this->control = $arrConstructor[0]['control'];
                $this->prioridad = $arrConstructor[0]['prioridad'];
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
        $arrTemp['slug'] = $this->slug;
        $arrTemp['id_seccion_padre'] = $this->id_seccion_padre;
        $arrTemp['menu_tipo'] = $this->menu_tipo == '' ? null : $this->menu_tipo;
        $arrTemp['method'] = $this->method == '' ? null : $this->method;
        $arrTemp['categoria'] = $this->categoria == '' ? null : $this->categoria;
        $arrTemp['control'] = $this->control == '' ? null : $this->control;
        $arrTemp['prioridad'] = $this->prioridad == '' ? '0' : $this->prioridad;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase secciones o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarSecciones(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto secciones
     *
     * @return integer
     */
    public function getCodigoSecciones(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de secciones según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de secciones y los valores son los valores a actualizar
     */
    public function setSecciones(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["slug"]))
            $retorno = "slug";
        else if (!isset($arrCamposValores["id_seccion_padre"]))
            $retorno = "id_seccion_padre";
        else if (!isset($arrCamposValores["prioridad"]))
            $retorno = "prioridad";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setSecciones");
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
    * retorna los campos presentes en la tabla secciones en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposSecciones(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.secciones");
    }

    /**
    * Buscar registros en la tabla secciones
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de secciones o la cantdad de registros segun el parametro contar
    */
    static function listarSecciones(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.secciones", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>