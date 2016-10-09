<?php

/**
* Class Tcupones_canje
*
*Class  Tcupones_canje maneja todos los aspectos de cupones_canje
*
* @package  SistemaIGA
* @subpackage Cupones_canje
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tcupones_canje extends class_general{

    /**
    * codigo_cupon de cupones_canje
    * @var codigo_cupon int
    * @access public
    */
    public $codigo_cupon;

    /**
    * cod_matricula de cupones_canje
    * @var cod_matricula int
    * @access public
    */
    public $cod_matricula;

    /**
    * cod_filial de cupones_canje
    * @var cod_filial int
    * @access public
    */
    public $cod_filial;

    /**
    * codigo de cupones_canje
    * @var codigo int
    * @access protected
    */
    protected $codigo;


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
    protected $nombreTabla = 'general.cupones_canje';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase cupones_canje
     *
     * @param CI_DB_mysqli_driver $connection
     * @param integer $codigo (opcional) el codigo del objeto a crear
     */
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null){
        $this->oConnection = $conexion;
        if ($codigo != null && $codigo != -1){
            $arrConstructor = $this->_constructor($codigo);
            if (count($arrConstructor) > 0){
                $this->codigo_cupon = $arrConstructor[0]['codigo_cupon'];
                $this->cod_matricula = $arrConstructor[0]['cod_matricula'];
                $this->cod_filial = $arrConstructor[0]['cod_filial'];
                $this->codigo = $arrConstructor[0]['codigo'];
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
        $arrTemp['codigo_cupon'] = $this->codigo_cupon;
        $arrTemp['cod_matricula'] = $this->cod_matricula;
        $arrTemp['cod_filial'] = $this->cod_filial;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase cupones_canje o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarCupones_canje(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto cupones_canje
     *
     * @return integer
     */
    public function getCodigoCupones_canje(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de cupones_canje según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de cupones_canje y los valores son los valores a actualizar
     */
    public function setCupones_canje(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["codigo_cupon"]))
            $retorno = "codigo_cupon";
        else if (!isset($arrCamposValores["cod_matricula"]))
            $retorno = "cod_matricula";
        else if (!isset($arrCamposValores["cod_filial"]))
            $retorno = "cod_filial";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setCupones_canje");
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
    * retorna los campos presentes en la tabla cupones_canje en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposCupones_canje(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.cupones_canje");
    }

    /**
    * Buscar registros en la tabla cupones_canje
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de cupones_canje o la cantdad de registros segun el parametro contar
    */
    static function listarCupones_canje(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.cupones_canje", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>