<?php

/**
* Class Tmails_templates
*
*Class  Tmails_templates maneja todos los aspectos de mails_templates
*
* @package  SistemaIGA
* @subpackage Mails_templates
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tmails_templates extends class_general{

    /**
    * cod_template de mails_templates
    * @var cod_template int
    * @access protected
    */
    protected $cod_template;

    /**
    * cod_curso de mails_templates
    * @var cod_curso int (requerido)
    * @access public
    */
    public $cod_curso;

    /**
    * activo de mails_templates
    * @var activo smallint
    * @access public
    */
    public $activo;

    /**
    * orden_prioridad de mails_templates
    * @var orden_prioridad int
    * @access public
    */
    public $orden_prioridad;


    /**
    * primaryKey de la tabla
    * @var primaryKey var
    * @access protected
    */
    protected $primaryKey = "cod_template";
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
    protected $nombreTabla = 'mails_consultas.mails_templates';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase mails_templates
     *
     * @param CI_DB_mysqli_driver $connection
     * @param integer $codigo (opcional) el codigo del objeto a crear
     */
    function __construct(CI_DB_mysqli_driver $conexion, $cod_template = null){
        $this->oConnection = $conexion;
        if ($cod_template != null && $cod_template != -1){
            $arrConstructor = $this->_constructor($cod_template);
            if (count($arrConstructor) > 0){
                $this->cod_template = $arrConstructor[0]['cod_template'];
                $this->cod_curso = $arrConstructor[0]['cod_curso'];
                $this->activo = $arrConstructor[0]['activo'];
                $this->orden_prioridad = $arrConstructor[0]['orden_prioridad'];
            } else {
                $this->cod_template = -1;
            }
        } else {
            $this->cod_template = -1;
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
        $arrTemp['cod_curso'] = $this->cod_curso == '' ? null : $this->cod_curso;
        $arrTemp['activo'] = $this->activo == '' ? '1' : $this->activo;
        $arrTemp['orden_prioridad'] = $this->orden_prioridad;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase mails_templates o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarMails_templates(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto mails_templates
     *
     * @return integer
     */
    public function getCodigoMails_templates(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de mails_templates según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de mails_templates y los valores son los valores a actualizar
     */
    public function setMails_templates(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["activo"]))
            $retorno = "activo";
        else if (!isset($arrCamposValores["orden_prioridad"]))
            $retorno = "orden_prioridad";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setMails_templates");
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
    * retorna los campos presentes en la tabla mails_templates en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposMails_templates(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "mails_consultas.mails_templates");
    }

    /**
    * Buscar registros en la tabla mails_templates
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de mails_templates o la cantdad de registros segun el parametro contar
    */
    static function listarMails_templates(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "mails_consultas.mails_templates", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>