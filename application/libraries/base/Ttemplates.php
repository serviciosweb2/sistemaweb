<?php

/**
* Class Ttemplates
*
*Class  Ttemplates maneja todos los aspectos de templates
*
* @package  SistemaIGA
* @subpackage Templates
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Ttemplates extends class_general{

    /**
    * codigo de templates
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * nombre de templates
    * @var nombre varchar
    * @access public
    */
    public $nombre;

    /**
    * html de templates
    * @var html longtext
    * @access public
    */
    public $html;


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
    protected $nombreTabla = 'general.templates';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase templates
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
                $this->nombre = $arrConstructor[0]['nombre'];
                if(get_idioma() != null && get_idioma() != 'es' && get_idioma() != '')
                {
                    $this->html = $arrConstructor[0]['html_'.get_idioma()];
                }
                else
                {
                    $this->html = $arrConstructor[0]['html'];
                }

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
        $arrTemp['nombre'] = $this->nombre;
        $arrTemp['html'] = $this->html;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase templates o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarTemplates(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto templates
     *
     * @return integer
     */
    public function getCodigoTemplates(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de templates según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de templates y los valores son los valores a actualizar
     */
    public function setTemplates(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["nombre"]))
            $retorno = "nombre";
        else if (!isset($arrCamposValores["html"]))
            $retorno = "html";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setTemplates");
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
    * retorna los campos presentes en la tabla templates en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposTemplates(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.templates");
    }

    /**
    * Buscar registros en la tabla templates
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de templates o la cantdad de registros segun el parametro contar
    */
    static function listarTemplates(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.templates", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>