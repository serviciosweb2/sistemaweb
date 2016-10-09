<?php

/**
* Class Tusuarios_rubros
*
*Class  Tusuarios_rubros maneja todos los aspectos de usuarios_rubros
*
* @package  SistemaIGA
* @subpackage Usuarios_rubros
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tusuarios_rubros extends class_general{

    /**
    * coduser de usuarios_rubros
    * @var coduser int
    * @access public
    */
    public $coduser;

    /**
    * codrubro de usuarios_rubros
    * @var codrubro int
    * @access public
    */
    public $codrubro;


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
    protected $nombreTabla = 'usuarios_rubros';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase usuarios_rubros
     *
     * @param CI_DB_mysqli_driver $connection
     * @param integer $codigo (opcional) el codigo del objeto a crear
     */
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null){
        $this->oConnection = $conexion;
        if ($codigo != null && $codigo != -1){
            $arrConstructor = $this->_constructor($codigo);
            if (count($arrConstructor) > 0){
                $this->coduser = $arrConstructor[0]['coduser'];
                $this->codrubro = $arrConstructor[0]['codrubro'];
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
        $arrTemp['coduser'] = $this->coduser;
        $arrTemp['codrubro'] = $this->codrubro;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase usuarios_rubros o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarUsuarios_rubros(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto usuarios_rubros
     *
     * @return integer
     */
    public function getCodigoUsuarios_rubros(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de usuarios_rubros según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de usuarios_rubros y los valores son los valores a actualizar
     */
    public function setUsuarios_rubros(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["coduser"]))
            $retorno = "coduser";
        else if (!isset($arrCamposValores["codrubro"]))
            $retorno = "codrubro";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setUsuarios_rubros");
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
    * retorna los campos presentes en la tabla usuarios_rubros en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposUsuarios_rubros(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "usuarios_rubros");
    }

    /**
    * Buscar registros en la tabla usuarios_rubros
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de usuarios_rubros o la cantdad de registros segun el parametro contar
    */
    static function listarUsuarios_rubros(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "usuarios_rubros", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>