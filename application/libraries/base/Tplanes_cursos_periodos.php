<?php

/**
* Class Tplanes_cursos_periodos
*
*Class  Tplanes_cursos_periodos maneja todos los aspectos de planes_cursos_periodos
*
* @package  SistemaIGA
* @subpackage Planes_cursos_periodos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tplanes_cursos_periodos extends class_general{

    /**
    * codigo de planes_cursos_periodos
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_plan_pago de planes_cursos_periodos
    * @var cod_plan_pago int
    * @access public
    */
    public $cod_plan_pago;

    /**
    * cod_tipo_periodo de planes_cursos_periodos
    * @var cod_tipo_periodo int
    * @access public
    */
    public $cod_tipo_periodo;

    /**
    * cod_curso de planes_cursos_periodos
    * @var cod_curso int
    * @access public
    */
    public $cod_curso;


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
    protected $nombreTabla = 'planes_cursos_periodos';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase planes_cursos_periodos
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
                $this->cod_plan_pago = $arrConstructor[0]['cod_plan_pago'];
                $this->cod_tipo_periodo = $arrConstructor[0]['cod_tipo_periodo'];
                $this->cod_curso = $arrConstructor[0]['cod_curso'];
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
        $arrTemp['cod_plan_pago'] = $this->cod_plan_pago;
        $arrTemp['cod_tipo_periodo'] = $this->cod_tipo_periodo;
        $arrTemp['cod_curso'] = $this->cod_curso;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase planes_cursos_periodos o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarPlanes_cursos_periodos(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto planes_cursos_periodos
     *
     * @return integer
     */
    public function getCodigoPlanes_cursos_periodos(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de planes_cursos_periodos según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de planes_cursos_periodos y los valores son los valores a actualizar
     */
    public function setPlanes_cursos_periodos(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_plan_pago"]))
            $retorno = "cod_plan_pago";
        else if (!isset($arrCamposValores["cod_tipo_periodo"]))
            $retorno = "cod_tipo_periodo";
        else if (!isset($arrCamposValores["cod_curso"]))
            $retorno = "cod_curso";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setPlanes_cursos_periodos");
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
    * retorna los campos presentes en la tabla planes_cursos_periodos en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposPlanes_cursos_periodos(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "planes_cursos_periodos");
    }

    /**
    * Buscar registros en la tabla planes_cursos_periodos
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de planes_cursos_periodos o la cantdad de registros segun el parametro contar
    */
    static function listarPlanes_cursos_periodos(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "planes_cursos_periodos", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>