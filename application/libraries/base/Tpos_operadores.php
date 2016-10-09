<?php

/**
* Class Tpos_operadores
*
*Class  Tpos_operadores maneja todos los aspectos de pos_operadores
*
* @package  SistemaIGA
* @subpackage Pos_operadores
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tpos_operadores extends class_general{

    /**
    * codigo de pos_operadores
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * nombre de pos_operadores
    * @var nombre varchar
    * @access public
    */
    public $nombre;

    /**
    * cod_pais de pos_operadores
    * @var cod_pais int
    * @access public
    */
    public $cod_pais;

    /**
    * estado de pos_operadores
    * @var estado enum
    * @access public
    */
    public $estado;


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
    protected $nombreTabla = 'tarjetas.pos_operadores';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase pos_operadores
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
                $this->cod_pais = $arrConstructor[0]['cod_pais'];
                $this->estado = $arrConstructor[0]['estado'];
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
        $arrTemp['cod_pais'] = $this->cod_pais;
        $arrTemp['estado'] = $this->estado == '' ? 'habilitado' : $this->estado;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase pos_operadores o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarPos_operadores(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto pos_operadores
     *
     * @return integer
     */
    public function getCodigoPos_operadores(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de pos_operadores según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de pos_operadores y los valores son los valores a actualizar
     */
    public function setPos_operadores(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["nombre"]))
            $retorno = "nombre";
        else if (!isset($arrCamposValores["cod_pais"]))
            $retorno = "cod_pais";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setPos_operadores");
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
    * retorna los campos presentes en la tabla pos_operadores en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposPos_operadores(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "tarjetas.pos_operadores");
    }

    /**
    * Buscar registros en la tabla pos_operadores
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de pos_operadores o la cantdad de registros segun el parametro contar
    */
    static function listarPos_operadores(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "tarjetas.pos_operadores", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>