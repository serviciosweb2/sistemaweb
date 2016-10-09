<?php

/**
* Class Tmovimientos_particulares
*
*Class  Tmovimientos_particulares maneja todos los aspectos de movimientos_particulares
*
* @package  SistemaIGA
* @subpackage Movimientos_particulares
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tmovimientos_particulares extends class_general{

    /**
    * codigo de movimientos_particulares
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_rubro de movimientos_particulares
    * @var cod_rubro int
    * @access public
    */
    public $cod_rubro;


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
    protected $nombreTabla = 'movimientos_particulares';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase movimientos_particulares
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
                $this->cod_rubro = $arrConstructor[0]['cod_rubro'];
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
        $arrTemp['cod_rubro'] = $this->cod_rubro;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase movimientos_particulares o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarMovimientos_particulares(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto movimientos_particulares
     *
     * @return integer
     */
    public function getCodigoMovimientos_particulares(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de movimientos_particulares según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de movimientos_particulares y los valores son los valores a actualizar
     */
    public function setMovimientos_particulares(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_rubro"]))
            $retorno = "cod_rubro";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setMovimientos_particulares");
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
    * retorna los campos presentes en la tabla movimientos_particulares en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposMovimientos_particulares(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "movimientos_particulares");
    }

    /**
    * Buscar registros en la tabla movimientos_particulares
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de movimientos_particulares o la cantdad de registros segun el parametro contar
    */
    static function listarMovimientos_particulares(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "movimientos_particulares", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>