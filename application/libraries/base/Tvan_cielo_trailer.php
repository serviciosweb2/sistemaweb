<?php

/**
* Class Tvan_cielo_trailer
*
*Class  Tvan_cielo_trailer maneja todos los aspectos de van_cielo_trailer
*
* @package  SistemaIGA
* @subpackage Van_cielo_trailer
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tvan_cielo_trailer extends class_general{

    /**
    * codigo de van_cielo_trailer
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * tipo_registro de van_cielo_trailer
    * @var tipo_registro smallint
    * @access public
    */
    public $tipo_registro;

    /**
    * total_registros de van_cielo_trailer
    * @var total_registros bigint
    * @access public
    */
    public $total_registros;

    /**
    * uso_cielo de van_cielo_trailer
    * @var uso_cielo varchar
    * @access public
    */
    public $uso_cielo;


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
    protected $nombreTabla = 'tarjetas.van_cielo_trailer';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase van_cielo_trailer
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
                $this->tipo_registro = $arrConstructor[0]['tipo_registro'];
                $this->total_registros = $arrConstructor[0]['total_registros'];
                $this->uso_cielo = $arrConstructor[0]['uso_cielo'];
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
        $arrTemp['tipo_registro'] = $this->tipo_registro;
        $arrTemp['total_registros'] = $this->total_registros;
        $arrTemp['uso_cielo'] = $this->uso_cielo;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase van_cielo_trailer o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarVan_cielo_trailer(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto van_cielo_trailer
     *
     * @return integer
     */
    public function getCodigoVan_cielo_trailer(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de van_cielo_trailer según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de van_cielo_trailer y los valores son los valores a actualizar
     */
    public function setVan_cielo_trailer(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["tipo_registro"]))
            $retorno = "tipo_registro";
        else if (!isset($arrCamposValores["total_registros"]))
            $retorno = "total_registros";
        else if (!isset($arrCamposValores["uso_cielo"]))
            $retorno = "uso_cielo";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setVan_cielo_trailer");
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
    * retorna los campos presentes en la tabla van_cielo_trailer en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposVan_cielo_trailer(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "tarjetas.van_cielo_trailer");
    }

    /**
    * Buscar registros en la tabla van_cielo_trailer
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de van_cielo_trailer o la cantdad de registros segun el parametro contar
    */
    static function listarVan_cielo_trailer(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "tarjetas.van_cielo_trailer", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>