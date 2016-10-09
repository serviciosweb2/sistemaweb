<?php

/**
* Class Tsalones
*
*Class  Tsalones maneja todos los aspectos de salones
*
* @package  SistemaIGA
* @subpackage Salones
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tsalones extends class_general{

    /**
    * codigo de salones
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * salon de salones
    * @var salon varchar
    * @access public
    */
    public $salon;

    /**
    * cupo de salones
    * @var cupo int
    * @access public
    */
    public $cupo;

    /**
    * tipo de salones
    * @var tipo enum
    * @access public
    */
    public $tipo;

    /**
    * color de salones
    * @var color varchar
    * @access public
    */
    public $color;

    /**
    * estado de salones
    * @var estado tinyint (requerido)
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
    protected $nombreTabla = 'salones';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase salones
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
                $this->salon = $arrConstructor[0]['salon'];
                $this->cupo = $arrConstructor[0]['cupo'];
                $this->tipo = $arrConstructor[0]['tipo'];
                $this->color = $arrConstructor[0]['color'];
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
        $arrTemp['salon'] = $this->salon;
        $arrTemp['cupo'] = $this->cupo == '' ? '0' : $this->cupo;
        $arrTemp['tipo'] = $this->tipo;
        $arrTemp['color'] = $this->color == '' ? 'FFFFFF' : $this->color;
        $arrTemp['estado'] = $this->estado == '' ? null : $this->estado;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase salones o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarSalones(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto salones
     *
     * @return integer
     */
    public function getCodigoSalones(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de salones según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de salones y los valores son los valores a actualizar
     */
    public function setSalones(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["salon"]))
            $retorno = "salon";
        else if (!isset($arrCamposValores["cupo"]))
            $retorno = "cupo";
        else if (!isset($arrCamposValores["tipo"]))
            $retorno = "tipo";
        else if (!isset($arrCamposValores["color"]))
            $retorno = "color";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setSalones");
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
    * retorna los campos presentes en la tabla salones en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposSalones(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "salones");
    }

    /**
    * Buscar registros en la tabla salones
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de salones o la cantdad de registros segun el parametro contar
    */
    static function listarSalones(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "salones", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>