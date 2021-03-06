<?php

/**
* Class Trubros_caja
*
*Class  Trubros_caja maneja todos los aspectos de rubros_caja
*
* @package  SistemaIGA
* @subpackage Rubros_caja
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Trubros_caja extends class_general{

    /**
    * codigo de rubros_caja
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * rubro de rubros_caja
    * @var rubro enum
    * @access public
    */
    public $rubro;

    /**
    * subrubro de rubros_caja
    * @var subrubro enum
    * @access public
    */
    public $subrubro;


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
    protected $nombreTabla = 'rubros_caja';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase rubros_caja
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
                $this->rubro = $arrConstructor[0]['rubro'];
                $this->subrubro = $arrConstructor[0]['subrubro'];
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
        $arrTemp['rubro'] = $this->rubro;
        $arrTemp['subrubro'] = $this->subrubro;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase rubros_caja o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarRubros_caja(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto rubros_caja
     *
     * @return integer
     */
    public function getCodigoRubros_caja(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de rubros_caja según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de rubros_caja y los valores son los valores a actualizar
     */
    public function setRubros_caja(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["rubro"]))
            $retorno = "rubro";
        else if (!isset($arrCamposValores["subrubro"]))
            $retorno = "subrubro";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setRubros_caja");
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
    * retorna los campos presentes en la tabla rubros_caja en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposRubros_caja(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "rubros_caja");
    }

    /**
    * Buscar registros en la tabla rubros_caja
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de rubros_caja o la cantdad de registros segun el parametro contar
    */
    static function listarRubros_caja(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "rubros_caja", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>